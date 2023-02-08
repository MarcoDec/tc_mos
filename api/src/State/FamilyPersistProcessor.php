<?php

declare(strict_types=1);

namespace App\State;

use App\Entity\Entity;
use App\Entity\Project\Product\Family as ProductFamily;
use App\Entity\Purchase\Component\Family as ComponentFamily;
use App\Filesystem\FileManager;
use Doctrine\ORM\EntityManagerInterface;

class FamilyPersistProcessor extends PersistProcessor {
    public function __construct(EntityManagerInterface $em, private readonly FileManager $fm) {
        parent::__construct($em);
    }

    /** @param ComponentFamily|ProductFamily $data */
    protected function postPersist(Entity $data): void {
        $this->fm->uploadIcon($data);
    }
}
