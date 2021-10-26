<?php

namespace App\Security;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\ApiTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
   public function __construct(
      private ApiTokenRepository $apiTokenRepository,
      private EntityManagerInterface $entityManager
   ) {}

    public function supports(Request $request): ?bool
    {
       return $request->headers->has('Authorization')
          && str_starts_with($request->headers->get('Authorization'), 'Bearer ')
          && str_starts_with($request->getRequestUri(), '/api/')
          ;
    }

    public function authenticate(Request $request): PassportInterface
    {
       $authorizationHeader = $request->headers->get('Authorization');
       $credentialsData =[
          'token'=>substr($authorizationHeader,7),
          'requestedUri' => $request->getRequestUri()
       ];

       $userBadge = new UserBadge($credentialsData['token'],function($userToken){
          $apiToken = $this->apiTokenRepository->findOneBy(['token'=>$userToken]);
          return $apiToken?->getEmployee();
       });

       $credentials = new CustomCredentials(function ($credentialsDate, $employee) {
          $apiToken = $this->apiTokenRepository->findOneBy(['token'=>$credentialsDate['token']]);
          if (!$apiToken) {
             throw new CustomUserMessageAuthenticationException('Invalid Api Token');
          }
          if ($credentialsDate['requestedUri']==="/api/logout") {
             if ($apiToken->isExpired()) {
                throw new CustomUserMessageAuthenticationException("Token already disabled");
             }
             //On désactive le token
             $apiToken->expire();
             $this->entityManager->flush();
             throw new CustomUserMessageAuthenticationException("Token disabled");
          }
          if ($apiToken->isExpired()) {
             throw new CustomUserMessageAuthenticationException("Token has expired");
          } else {
             //on renouvelle le token
             $apiToken->renewExpiresAt();
             $this->entityManager->flush();
          }
          $tokenEmployee = $apiToken->getEmployee();
          if ($tokenEmployee->getId() === $employee->getId()) return true; else return false;
       }, $credentialsData);

       $apiToken = $this->apiTokenRepository->findOneBy(['token'=>$credentialsData['token']]);
       $employee = $apiToken?->getEmployee();
       if ($employee!=null) {
          $credentials->executeCustomChecker($employee);
          if (!$credentials->isResolved()) {
             throw new CustomUserMessageAuthenticationException('Credentials check failed');
          }
       }
       return new Passport($userBadge, $credentials);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        dd("Implement onAuthenticationSuccess() method.");
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
       return new JsonResponse(["error"=>"AuthenticationFailure"]);
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
}
