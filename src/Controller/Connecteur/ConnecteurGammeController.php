<?php

namespace App\Controller\Connecteur;

use App\DTO\ConnecteurGammeRequest;
use App\Repository\Mos\ConnecteurRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConnecteurGammeController {

    public function __construct(private ConnecteurRepository $connecteurRepository)
    { }


    public function __invoke(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse {

        $content = $request->getContent();
        $req = $serializer->deserialize($content, ConnecteurGammeRequest::class, 'json');

        $errors = $validator->validate($req);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $ids = $req->getIds();

        $result = [];
        foreach ($ids as $id) {
            $count = $this->connecteurRepository->count(['idProduct' => $id]);
            $result[] = ['id' => $id, 'exist' => $count>0];
        }

        return new JsonResponse($result, 200);
    }

}