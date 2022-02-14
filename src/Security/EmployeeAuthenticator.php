<?php

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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class EmployeeAuthenticator extends AbstractLoginFormAuthenticator {
    public function __construct(private NormalizerInterface $normalizer, private UrlGeneratorInterface $urlGenerator) {
    }

    public function authenticate(Request $request): Passport {
        /** @var array{password?: string, username?: string}|null $content */
        $content = json_decode($request->getContent(), true);
        if (empty($content)) {
            throw new BadCredentialsException();
        }
        $username = $content['username'] ?? null;
        $password = $content['password'] ?? null;
        if (empty($password) || empty($username)) {
            throw new BadCredentialsException();
        }
        return new Passport(new UserBadge($username), new PasswordCredentials($password));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response {
        return new JsonResponse(
            data: empty($exception->getMessage()) ? $exception->getMessageKey() : $exception->getMessage(),
            status: Response::HTTP_UNAUTHORIZED
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        return new JsonResponse($this->normalizer->normalize($token->getUser(), null, ['jsonld_has_context' => false]));
    }

    protected function getLoginUrl(Request $request): string {
        return $this->urlGenerator->generate('login');
    }
}
