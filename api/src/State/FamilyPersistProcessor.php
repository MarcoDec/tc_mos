<?php

declare(strict_types=1);

namespace App\State;

use App\Entity\Entity;
use App\Entity\Purchase\Component\Family;
use App\Filesystem\FileManager;
use Doctrine\ORM\EntityManagerInterface;

class FamilyPersistProcessor extends PersistProcessor {
    public function __construct(EntityManagerInterface $em, private readonly FileManager $fm) {
        parent::__construct($em);
    }

    /** @param Family $data */
    protected function postPersist(Entity $data): void {
        $this->fm->uploadIcon($data);
    }
}
