<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApiAuthenticator extends AbstractLoginFormAuthenticator {
    public function __construct(
        private readonly TranslatorInterface $trans,
        private readonly UrlGeneratorInterface $url
    ) {
    }

    public function authenticate(Request $request): Passport {
        /** @var array{password?: string, username?: string} $content */
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (empty($content)) {
            throw new BadCredentialsException();
        }
        $password = $content['password'] ?? null;
        $username = $content['username'] ?? null;
        if (empty($password) || empty($username)) {
            throw new BadCredentialsException();
        }
        return new Passport(new UserBadge($username), new PasswordCredentials($password));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response {
        return new JsonResponse(
            data: $this->trans->trans($exception->getMessageKey(), $exception->getMessageData(), 'security'),
            status: JsonResponse::HTTP_UNAUTHORIZED
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        return new JsonResponse(status: JsonResponse::HTTP_NO_CONTENT);
    }

    protected function getLoginUrl(Request $request): string {
        return $this->url->generate('_api_/login_post');
    }
}
