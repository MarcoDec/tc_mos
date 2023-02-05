<?php

declare(strict_types=1);

namespace App\Filesystem;

use App\Entity\Project\Product\Family as ProductFamily;
use App\Entity\Purchase\Component\Family as ComponentFamily;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager {
    public function __construct(private readonly string $dir, private readonly Filesystem $fs) {
    }

    public function clean(): void {
        $files = (new Finder())->in($this->dir)->files();
        foreach ($files as $file) {
            $this->fs->remove($file->getPathname());
        }
    }

    public function uploadIcon(ComponentFamily|ProductFamily $family): void {
        $file = $family->getFile();
        if ($file instanceof UploadedFile === false) {
            return;
        }
        $dir = new Directory("$this->dir/{$family->getDir()}");
        if (empty($existing = $dir->startsWith($family->generateIconName())) === false) {
            $this->fs->remove($existing);
        }
        $family->setFile($file->move((string) $dir, $family->generateFullIconName()));
    }
}