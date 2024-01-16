<?php

namespace App\Filesystem;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\Operation\DashPathSegmentNameGenerator;
use App\Entity\Family;
use App\Entity\Interfaces\FileEntity;
use Symfony\Component\Filesystem\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FileManager {
    private readonly string $uploadsDir;

    public function __construct(
        private readonly ResourceMetadataFactoryInterface $apiMetadatas,
        private readonly DashPathSegmentNameGenerator $dashGenerator,
        private readonly string $dir,
        private readonly Filesystem $fs
    ) {
        $this->uploadsDir = "$this->dir/uploads";
    }

    public function loadFamilyIcon(Family $family): void {
        if (empty($dashedName = $this->getDashedName($family))) {
            return;
        }

        $dir = $this->scandir($dashedName);
        if (empty($icon = $dir->firstStartsWith("{$family->getId()}."))) {
            return;
        }

        $family->setFile(new File($icon));
    }

    public function normalizePath(?string $path): ?string {
        return !empty($path) ? removeStart($path, $this->dir) : $path;
    }

    public function uploadFamilyIcon(Family $family): void {
        if (
            empty($file = $family->getFile())
            || !($file instanceof UploadedFile)
            || empty($dashedName = $this->getDashedName($family))
        ) {
            return;
        }

        $dir = $this->scandir($dashedName);
        if (!empty($first = $dir->firstStartsWith("{$family->getId()}."))) {
            $this->fs->remove($first);
        }
        $extension = $file->getExtension() ?: $file->guessExtension();
        if (empty($extension)) {
            throw new InvalidArgumentException("Cannot guess extension of {$file->getClientOriginalName()}.");
        }
        $family->setFile($file->move($dir, "{$family->getId()}.{$extension}"));
    }

    private function getDashedName(FileEntity $entity): ?string {
        return ($shortName = $this->getShortName($entity))
            ? $this->dashGenerator->getSegmentName($shortName)
            : null;
    }

    private function getMetadata(FileEntity $entity): ResourceMetadata {
        return $this->apiMetadatas->create(removeStart($entity::class, 'Proxies\\__CG__\\'));
    }

    private function getShortName(FileEntity $entity): ?string {
        return $this->getMetadata($entity)->getShortName();
    }

    private function scandir(string $dir): Directory {
        return new Directory("$this->uploadsDir/$dir");
    }
}
