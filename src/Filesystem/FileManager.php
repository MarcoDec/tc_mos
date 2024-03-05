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
use Symfony\Component\HttpFoundation\RequestStack;

final class FileManager {
    private readonly string $uploadsDir;

    public function __construct(
        private readonly ResourceMetadataFactoryInterface $apiMetadatas,
        private readonly DashPathSegmentNameGenerator $dashGenerator,
        private readonly string $dir,
        private readonly Filesystem $fs,
        private readonly RequestStack $requestStack
    ) {
        $this->uploadsDir = "$this->dir/uploads";
    }

    public function getUploadsDir(): string {
        return $this->uploadsDir;
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

    public function persistFile(string $uploadSubFolder, UploadedFile $file): void {
        $basePath = $this->getUploadsDir();
        $targetFolder = $uploadSubFolder;
        /* @phpstan-ignore-next-line */
        $completeTargetPath = $basePath.$targetFolder.'/'.str_replace(' ', '_', $file->getClientOriginalName());
        //Check if Folder Exist
        $this->checkFolderAndCreateIfNeeded($basePath.$targetFolder);
        move_uploaded_file($file->getPathname(), $completeTargetPath);
    }

    public function uploadFamilyIcon(Family $family): void {
        $host = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
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
        $familySubFolder = $family instanceof \App\Entity\Project\Product\Family ? 'product-families' : 'component-families';
        $family->setFilePath($host.'/uploads/'.$familySubFolder.'/'.$family->getId().'.'.$extension);
    }
    public function uploadFileEntityImage(FileEntity $entity): void {
        $host = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
//        dump($_FILES);
        if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
            // Créer une instance de UploadedFile
            $file = new UploadedFile(
                $_FILES['file']['tmp_name'],
                $_FILES['file']['name'],
                $_FILES['file']['type'],
                $_FILES['file']['error']
            );
        } else {
            $file = $entity->getFile();
        }
        $dashedName = $this->getSubFolderNameFromClassName($entity::class);
//        dump(['dashName' => $dashedName]);
        if (
            empty($file)
            || !($file instanceof UploadedFile)
            || empty($dashedName)
        ) {
//            dump('uploadFileEntityImage::empty');
            return;
        }
        $dir = $this->scandir($dashedName);
        if (!empty($first = $dir->firstStartsWith("{$entity->getId()}."))) {
            $this->fs->remove($first);
        }
        $extension = $file->getExtension() ?: $file->guessExtension();
        if (empty($extension)) {
            throw new InvalidArgumentException("Cannot guess extension of {$file->getClientOriginalName()}.");
        }
        $entity->setFile($file->move($dir, "{$entity->getId()}.{$extension}"));
        $entitySubFolder = $this->getSubFolderNameFromClassName($entity::class);
        $entity->setFilePath($host.'/uploads/'.$entitySubFolder.'/'.$entity->getId().'.'.$extension);
    }
    private function getSubFolderNameFromClassName(string $className): string {
        // Étape 1: Supprimer le namespace de base
//        dump(['className' => $className]);
        $chaineSansNamespace = str_replace('App\Entity\\', '', $className);
//        dump(['chaineSansNamespace' => $chaineSansNamespace]);
        // Étape 2: Séparer les mots sur les majuscules
        $parties = preg_split('/(?=[A-Z])/', $chaineSansNamespace, -1, PREG_SPLIT_NO_EMPTY);
        $parties = array_map(function ($partie) {
            return str_replace('\\', '', $partie);
        }, $parties);
//        dump(['parties' => $parties]);
        // Étape 3: Convertir en minuscules
        $partiesMinuscules = array_map('strtolower', $parties);
//        dump(['partiesMinuscules' => $partiesMinuscules]);
        // Étape 4: Concaténer avec des traits d'union
        $resultat = implode('-', $partiesMinuscules);
//        dump(['resultat' => $resultat]);
        return $resultat;
    }

    private function checkFolderAndCreateIfNeeded(string $folder): void {
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
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
