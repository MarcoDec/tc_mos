<?php

namespace App\EventListener\Project\Product;

use App\Entity\Project\Product\Family;
use App\Filesystem\FileManager;

final class FamilyListener {
    public function __construct(private FileManager $fm) {
    }

    public function postLoad(Family $family): void {
        $this->fm->loadFamilyIcon($family);
    }
}
