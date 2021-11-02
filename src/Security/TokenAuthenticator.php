<?php

namespace App\Security;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\TokenRepository;
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
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class TokenAuthenticator extends AbstractAuthenticator {
    public function __construct(
        private TokenRepository $apiTokenRepository,
        private EmployeeRepository $employeeRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private PasswordHasherFactoryInterface $hasherFactory
    ) {
    }

    public function authenticate(Request $request): PassportInterface {
        $this->logger->info('ApiTokenAuthenticator::authenticate');
        if ($this->hasAuthorization($request)) {
            $this->logger->info('ApiTokenAuthenticator::authenticate hasAuthorization');
            /** @var string $authorizationHeader */
            $authorizationHeader = $request->headers->get('Authorization');
            $credentialsData = [
                'token' => substr($authorizationHeader, 7),
                'requestedUri' => $request->getRequestUri()
            ];
            $userBadge = new UserBadge($credentialsData['token'], function ($userToken) {
                $apiToken = $this->apiTokenRepository->findOneBy(['token' => $userToken]);
                if ($apiToken) {
                    $emp = $apiToken->getEmployee();
                    if ($emp != null) {
                        /** @var int $employeeId */
                        $employeeId = $emp->getId();
                        return $this->entityManager->getRepository(Employee::class)->find($employeeId);
                    }
                }
            });
            $credentials = new CustomCredentials(function ($credentialsDate, $employee) {
                $apiToken = $this->apiTokenRepository->findOneBy(['token' => $credentialsDate['token']]);
                if (!$apiToken) {
                    throw new CustomUserMessageAuthenticationException('Invalid Api Token');
                }
                if ($credentialsDate['requestedUri'] === '/api/logout') {
                    if ($apiToken->isExpired()) {
                        throw new CustomUserMessageAuthenticationException('Token already disabled');
                    }
                    //On désactive le token
                    $apiToken->expire();
                    $this->entityManager->flush();
                    throw new CustomUserMessageAuthenticationException('Token disabled');
                }
                if ($apiToken->isExpired()) {
                    throw new CustomUserMessageAuthenticationException('Token has expired');
                }
                //on renouvelle le token
                $apiToken->renew();
                $this->entityManager->flush();

                $tokenEmployee = $apiToken->getEmployee();
                if ($tokenEmployee != null) {
                    return (bool) ($tokenEmployee->getId() === $employee->getId());
                }
                return false;
            }, $credentialsData);
            $employee = $userBadge->getUser();
            if ($employee != null) {
                $credentials->executeCustomChecker($employee);
                if (!$credentials->isResolved()) {
                    throw new CustomUserMessageAuthenticationException('Credentials check failed');
                }
            }
            return new Passport($userBadge, $credentials);
        }
        $this->logger->info('ApiTokenAuthenticator::authenticate has NOT Authorization');
        if ($this->hasUsernameAndPassord($request)) {
            $cred = json_decode($request->getContent());
            $credentialsData = [
                'username' => $cred->username,
                'password' => $cred->password,
                'requestedUri' => $request->getRequestUri()
            ];

            $userBadge = new UserBadge($credentialsData['username'], fn ($credentialsData) => $this->entityManager->getRepository(Employee::class)->findOneBy(['username' => $credentialsData]));

            $credentials = new CustomCredentials(function ($credentialsData, Employee $employee) {
                $password = $employee->getPassword();
                if ($password != null) {
                    return $this->hasherFactory->getPasswordHasher($employee)->verify($password, $credentialsData['password']);
                }
                return false;
            }, $credentialsData);

            /** @var Employee|null $employee */
            $employee = $userBadge->getUser();

            if ($employee != null) {
                $credentials->executeCustomChecker($employee);
                if (!$credentials->isResolved()) {
                    throw new CustomUserMessageAuthenticationException('Credentials check failed');
                }
            }
            return new Passport($userBadge, $credentials);
        }
        throw new CustomUserMessageAuthenticationException('Authentification hors API');
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }

    public function createAuthenticatedToken(PassportInterface $passport, string $firewallName): TokenInterface {
        return parent::createAuthenticatedToken($passport, $firewallName); // TODO: Change the autogenerated stub
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        return null;
        //return new JsonResponse(["error"=>"AuthenticationFailure"]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        return null;
    }

    public function supports(Request $request): ?bool {
        $this->logger->info('ApiTokenAuthenticator::requested URI', [$request->getRequestUri()]);
        if ($this->hasAuthorization($request)) {
            $this->logger->info('ApiTokenAuthenticator:: Entête Authorization non vide trouvée !', [$request->headers->get('Authorization')]);
            /** @var string $token */
            $token = $request->headers->get('Authorization') === null ? '' : $request->headers->get('Authorization');
            return str_starts_with($token, 'Bearer ')
             && str_starts_with($request->getRequestUri(), '/api/');
        }
        $this->logger->info('ApiTokenAuthenticator:: Pas d\'entête Authorization trouvée !', [$request->headers->get('Authorization')]);
        return $this->hasUsernameAndPassord($request);
    }

    private function hasAuthorization(Request $request): bool {
        return $request->headers->has('Authorization') && $request->headers->get('Authorization') != '';
    }

    private function hasUsernameAndPassord(Request $request): bool {
        $cred = json_decode($request->getContent());
        return $cred->username != null && $cred->password != null;
    }
}
