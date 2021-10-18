<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SecurityController extends AbstractController {
    #[Route(path: '/api/login', name: 'login', methods: 'POST')]
    public function login(NormalizerInterface $normalizer): JsonResponse {
        return $this->json($normalizer->normalize($this->getUser(), 'jsonld'));
    }
}
