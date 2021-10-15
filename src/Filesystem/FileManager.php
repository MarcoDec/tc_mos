<?php

namespace App\Filesystem;

use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\Operation\DashPathSegmentNameGenerator;
use App\Entity\Project\Product\Family;
use Symfony\Component\Filesystem\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FileManager {
    public function __construct(
        private DashPathSegmentNameGenerator $dashGenerator,
        private string $dir,
        private Filesystem $fs
    ) {
    }

    public function uploadFamilyIcon(Family $family, ResourceMetadata $metadata): void {
        if (empty($file = $family->getFile()) || !($file instanceof UploadedFile) || empty($shortName = $metadata->getShortName())) {
            return;
        }

        $dir = $this->scandir($this->dashGenerator->getSegmentName($shortName));
        if (!empty($first = $dir->firstStartsWith("{$family->getId()}."))) {
            $this->fs->remove($first);
        }
        $extension = $file->getExtension() ?: $file->guessExtension();
        if (empty($extension)) {
            throw new InvalidArgumentException("Cannot guess extension of {$file->getClientOriginalName()}.");
        }
        $file->move($dir, "{$family->getId()}.{$extension}");
    }

    private function scandir(string $dir): Directory {
        return new Directory("$this->dir/$dir");
    }
}
