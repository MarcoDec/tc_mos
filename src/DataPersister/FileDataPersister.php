<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Family;
use App\Entity\Interfaces\FileEntity;
use App\Filesystem\FileManager;
use Doctrine\ORM\EntityManagerInterface;

final class FileDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private readonly EntityManagerInterface $em, private readonly FileManager $fm) {
    }

    /**
     * @param FileEntity $data
     * @param mixed[]    $context
     */
    public function persist($data, array $context = []): FileEntity {
        $this->em->persist($data);
        $this->em->flush();
        if ($data instanceof Family) {
            $this->fm->uploadFamilyIcon($data);
        }
        return $data;
    }

    /**
     * @param FileEntity $data
     * @param mixed[]    $context
     */
    public function remove($data, array $context = []): void {
        $this->em->remove($data);
        $this->em->flush();
    }

    /**
     * @param mixed   $data
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return $data instanceof FileEntity;
    }
}
