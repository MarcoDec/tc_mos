<?php

namespace App\Security;

use App\Entity\Api\Token;
use App\Entity\Hr\Employee\Employee;
use App\Repository\Hr\Employee\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\LogicException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

final class TokenAuthenticator extends AbstractAuthenticator {
    public function __construct(
        private EntityManagerInterface $em,
        private PasswordHasherFactoryInterface $hasherFactory,
        private LoggerInterface $logger
    ) {
    }

    public function authenticate(Request $request): Passport {
        return $this->apiAuthenticate(new ApiRequest($request));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        return new JsonResponse($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        return null;
    }

    public function supports(Request $request): bool {
        return $this->apiSupports(new ApiRequest($request));
    }

    private function apiAuthenticate(ApiRequest $request): Passport {
        $this->logger->info(__METHOD__);
        if (!empty($passport = $this->apiTokenAuthenticate($request))) {
            return $passport;
        }
        if (!empty($passport = $this->apiCredentialsAuthenticate($request))) {
            return $passport;
        }
        throw new CustomUserMessageAuthenticationException('Authentification hors API');
    }

    private function apiCredentialsAuthenticate(ApiRequest $request): ?Passport {
        $this->logger->info(__METHOD__);
        if ($request->hasCredentials()) {
            $credentials = $request->getCredentials();
            return $this->createPassport(
                userBadge: new UserBadge($credentials['username'], function (string $username): Employee {
                    if (!empty($employee = $this->getEmployeeRepo()->findOneBy(['username' => $username]))) {
                        return $employee;
                    }
                    throw new UserNotFoundException();
                }),
                credentials: new CustomCredentials(function (array $credentials, Employee $user): bool {
                    return !empty($user->getPassword())
                        && $this->hasherFactory->getPasswordHasher($user)->verify($user->getPassword(), $credentials['password']);
                }, $credentials)
            );
        }
        return null;
    }

    private function apiSupports(ApiRequest $request): bool {
        $this->logger->info(__METHOD__.': URI', [$request->getRequestUri()]);
        if ($request->hasAuthorization()) {
            $this->logger->info(__METHOD__.': En-tête Authorization trouvée !', [$request]);
            return $request->isBearerAuthorization() && $request->isApiUri();
        }
        $this->logger->info(__METHOD__.': En-tête Authorization absente !');
        return $request->hasCredentials();
    }

    private function apiTokenAuthenticate(ApiRequest $request): ?Passport {
        $this->logger->info(__METHOD__);
        if ($request->hasAuthorization()) {
            $this->logger->info(__METHOD__.': hasAuthorization');
            $credentials = $request->getToken();
            return $this->createPassport(
                userBadge: new UserBadge($credentials['token'], function (string $token): Employee {
                    if (!empty($employee = $this->getEmployeeRepo()->findByToken($token))) {
                        return $employee;
                    }
                    throw new UserNotFoundException();
                }),
                credentials: new CustomCredentials(function (array $credentials, Employee $user): bool {
                    $token = $this->em->getRepository(Token::class)->findOneBy(['token' => $credentials['token']]);
                    if (empty($token)) {
                        throw new CustomUserMessageAuthenticationException('Invalid Api Token');
                    }
                    if ($credentials['requestedUri'] === '/api/logout') {
                        if ($token->isExpired()) {
                            throw new CustomUserMessageAuthenticationException('Token already disabled');
                        }
                        $token->expire();
                        $this->em->flush();
                        throw new CustomUserMessageAuthenticationException('Token disabled');
                    }
                    if ($token->isExpired()) {
                        throw new CustomUserMessageAuthenticationException('Token has expired');
                    }
                    $token->renew();
                    $this->em->flush();
                    return !empty($tokenUser = $token->getEmployee()) && $tokenUser->getId() === $user->getId();
                }, $credentials)
            );
        }
        return null;
    }

    private function createPassport(UserBadge $userBadge, CustomCredentials $credentials): Passport {
        $credentials->executeCustomChecker($userBadge->getUser());
        if (!$credentials->isResolved()) {
            throw new CustomUserMessageAuthenticationException('Credentials check failed');
        }
        return new Passport($userBadge, $credentials);
    }

    private function getEmployeeRepo(): EmployeeRepository {
        if (($repo = $this->em->getRepository(Employee::class)) instanceof EmployeeRepository) {
            return $repo;
        }
        throw new LogicException(sprintf('Class "%s" must use "%s" but "%s" found.', Employee::class, EmployeeRepository::class, ($type = gettype($repo)) === 'object' ? get_class($repo) : $type));
    }
}
