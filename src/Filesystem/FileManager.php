<?php

namespace App\Filesystem;

use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\Operation\DashPathSegmentNameGenerator;
use App\Entity\Project\Product\Family;
use Symfony\Component\Filesystem\Filesystem;

final class FileManager {
    public function __construct(
        private DashPathSegmentNameGenerator $dashGenerator,
        private string $dir,
        private Filesystem $fs
    ) {
    }

    public function uploadFamilyIcon(Family $family, ResourceMetadata $metadata): void {
        if (empty($file = $family->getFile()) || empty($shortName = $metadata->getShortName())) {
            return;
        }

        $dir = $this->scandir($this->dashGenerator->getSegmentName($shortName));
        if (!empty($first = $dir->firstStartsWith("{$family->getId()}."))) {
            $this->fs->remove($first);
        }
        $file->move("$this->dir/$dir", "{$family->getId()}.{$file->getExtension()}");
    }

    private function scandir(string $dir): Directory {
        return new Directory("$this->dir/$dir");
    }
}
