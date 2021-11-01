<?php

namespace App\Controller\Security;

use App\Entity\Api\ApiToken;
use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\ApiTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SecurityController extends AbstractController {
    public function __construct(private ApiTokenRepository $apiTokenRepository) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(path: '/api/login', name: 'login', methods: 'POST')]
    public function login(NormalizerInterface $normalizer): JsonResponse {
        //On désactive tous les autres Token actifs
        $apiTokens = $this->apiTokenRepository->findBy(['employee' => $this->getUser()]);
        collect($apiTokens)->map(static function (ApiToken $apiToken): void {
            if (!$apiToken->isExpired()) {
                $apiToken->expire();
            }
        });
        //On crée un nouveau token valide 1h
        /** @var Employee $employee */
        $employee = $this->getUser();
        $newApiToken = new ApiToken($employee);
        $this->getDoctrine()->getManager()->persist($newApiToken);
        $this->getDoctrine()->getManager()->flush();
        $response = array_merge(
            $normalizer->normalize($this->getUser(), null, ['jsonld_has_context' => false]),
            ['token' => $newApiToken->getToken()]
        );
        return new JsonResponse($response);
    }
}
