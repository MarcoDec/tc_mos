<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Family;
use App\Entity\Interfaces\FileEntity;
use App\Filesystem\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class FileDataPersister implements DataPersisterInterface {
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileManager $fm,
        private readonly RequestStack $requests
    ) {
    }

    /**
     * @param FileEntity $data
     */
    public function persist($data): FileEntity {
        $this->em->persist($data);
        $this->em->flush();
        if ($data instanceof Family) {
            $this->fm->uploadFamilyIcon($data);
        }
        return $data;
    }

    public function remove($data): void {
    }

    public function supports($data): bool {
        $request = $this->requests->getCurrentRequest();
        return !empty($request)
            && $request->isMethod(Request::METHOD_POST)
            && $data instanceof FileEntity;
    }
}
