<?php

namespace App\Security;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\TokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class TokenAuthenticator extends AbstractAuthenticator {
    public function __construct(private readonly TokenRepository $tokenRepo) {
    }

    private static function getBearer(Request $request): ?string {
        if ($request->cookies->has('token')) {
            return $request->cookies->get('token');
        }
        $authorization = $request->headers->get('Authorization');
        if (empty($authorization)) {
            return null;
        }
        return str_starts_with($authorization, 'Bearer ') ? removeStart($authorization, 'Bearer ') : null;
    }

    public function authenticate(Request $request): Passport {
        if (empty($bearer = self::getBearer($request))) {
            throw new TokenNotFoundException();
        }
        $token = $this->tokenRepo->findOneBy(['token' => $bearer]);
        if (empty($token)) {
            throw new TokenNotFoundException();
        }
        if ($token->isExpired()) {
            throw new CredentialsExpiredException();
        }
        return new SelfValidatingPassport(new UserBadge($token->getUserIdentifier()));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        return new JsonResponse(
            data: empty($exception->getMessage()) ? $exception->getMessageKey() : $exception->getMessage(),
            status: Response::HTTP_UNAUTHORIZED
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        /** @var Employee $user */
        $user = $token->getUser();
        $this->tokenRepo->renew($user);
        return null;
    }

    public function supports(Request $request): ?bool {
        return str_starts_with($request->getRequestUri(), '/api');
    }
}
