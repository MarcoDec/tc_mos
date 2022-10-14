<?php

namespace App\Security;

use App\Entity\Api\Token;
use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class TokenAuthenticator extends AbstractAuthenticator {
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    private static function getBearer(Request $request): ?string {
        $authorization = $request->headers->get('Authorization');
        if (empty($authorization)) {
            return null;
        }
        return str_starts_with($authorization, 'Bearer ') ? removeStart($authorization, 'Bearer ') : null;
    }

    public function authenticate(Request $request): Passport {
        $this->em->beginTransaction();
        if (empty($bearer = self::getBearer($request))) {
            throw new TokenNotFoundException();
        }
        $token = $this->getRepo()->findOneBy(['token' => $bearer]);
        if (empty($token)) {
            throw new TokenNotFoundException();
        }
        if ($token->isExpired()) {
            throw new CredentialsExpiredException();
        }
        return new SelfValidatingPassport(new UserBadge(
            userIdentifier: $token->getUserIdentifier(),
            userLoader: static fn (): ?Employee => $token->getEmployee()
        ));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        $this->em->rollback();
        return new JsonResponse(
            data: empty($exception->getMessage()) ? $exception->getMessageKey() : $exception->getMessage(),
            status: Response::HTTP_UNAUTHORIZED
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        /** @var Employee $user */
        $user = $token->getUser();
        $this->getRepo()->renew($user);
        $this->em->commit();
        $this->em->clear();
        return null;
    }

    public function supports(Request $request): ?bool {
        $uri = $request->getRequestUri();
        return $uri !== $this->urlGenerator->generate('login')
            && $uri !== $this->urlGenerator->generate('mobile.login')
            && str_starts_with($request->getRequestUri(), '/api/');
    }

    private function getRepo(): TokenRepository {
        return $this->em->getRepository(Token::class);
    }
}
