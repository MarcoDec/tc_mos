<?php

namespace App\Filesystem;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\Operation\DashPathSegmentNameGenerator;
use App\Entity\Interfaces\FileEntity;
use App\Entity\Project\Product\Family;
use Symfony\Component\Filesystem\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FileManager {
    public function __construct(
        private ResourceMetadataFactoryInterface $apiMetadatas,
        private DashPathSegmentNameGenerator $dashGenerator,
        private string $dir,
        private Filesystem $fs
    ) {
    }

    public function uploadFamilyIcon(Family $family): void {
        if (
            empty($file = $family->getFile())
            || !($file instanceof UploadedFile)
            || empty($shortName = $this->getMetadata($family)->getShortName())
        ) {
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

    private function getMetadata(FileEntity $entity): ResourceMetadata {
        return $this->apiMetadatas->create(get_class($entity));
    }

    private function scandir(string $dir): Directory {
        return new Directory("$this->dir/$dir");
    }
}
