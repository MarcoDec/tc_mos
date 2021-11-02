<?php

namespace App\Controller\Security;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\TokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SecurityController extends AbstractController {
    #[Route(path: '/api/login', name: 'login', methods: 'POST')]
    public function login(NormalizerInterface $normalizer, TokenRepository $tokenRepo): JsonResponse {
        if (($user = $this->getUser()) instanceof Employee) {
            $tokenRepo->connect($user);
        }
        return new JsonResponse($normalizer->normalize($user, null, ['jsonld_has_context' => false]));
    }
}
