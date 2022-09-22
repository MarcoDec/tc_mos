<?php

namespace App\Security;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class EmployeeAuthenticator extends AbstractLoginFormAuthenticator {
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly TokenRepository $tokenRepo,
        private readonly TranslatorInterface $translator,
        protected readonly UrlGeneratorInterface $urlGenerator,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function authenticate(Request $request): Passport {
        $this->em->beginTransaction();
        /** @var array{password?: string, username?: string}|null $content */
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
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
        $this->em->rollback();
        return new JsonResponse(
            data: $this->translator->trans(
                id: empty($exception->getMessage()) ? $exception->getMessageKey() : $exception->getMessage(),
                domain: 'security'
            ),
            status: Response::HTTP_UNAUTHORIZED
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        /** @var Employee $user */
        $user = $token->getUser();
        $this->tokenRepo->connect($user);
        $this->em->commit();
        return new JsonResponse($this->normalizer->normalize($user, null, [
            'groups' => ['read:state', 'read:user'],
            'jsonld_has_context' => false
        ]));
    }
}
