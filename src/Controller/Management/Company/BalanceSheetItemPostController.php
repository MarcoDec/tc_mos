<?php

namespace App\Controller\Management\Company;

use App\Entity\Management\Society\Company\BalanceSheetItem;
use App\Filesystem\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;

class BalanceSheetItemPostController
{
    private BalanceSheetItem $entity;
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileManager            $fileManager) {
    }
    public function __invoke(Request $request):BalanceSheetItem {
        $this->getEntity($request);
        $this->getFileAndPersist($request);
        return $this->entity;
    }
    public function getEntity(Request $request):void {
        $entity = $request->attributes->get('data');
        $class = get_class($entity);
        if (!($entity instanceof BalanceSheetItem)) {
            throw new RuntimeException("l'entité $class n'hérite pas de App\\Entity\\Management\\Society\\Company\\BalanceSheetItem");
        } else {
            $this->entity = $entity;
        }
    }
    public function getFileAndPersist(Request $request, bool $withError = true):void {
        $host = $request->getSchemeAndHttpHost();
        $file = $request->files->get('file');
        if ($withError && $file->getError()===1) {
            throw new FileException($file->getErrorMessage());
        }
        $this->entity->setFile($file);
        $this->entityManager->persist($this->entity);
        $this->entityManager->flush(); // pour récupération id utilisé par default dans getBaseFolder
        $saveFolder = $this->entity->getBaseFolder();
        if ($this->entity->getSubCategory() !== 'null') {
            $saveFolder .= '/' . $this->entity->getSubCategory();
        } else $this->entity->setSubCategory('');
        $this->fileManager->persistFile($saveFolder, $file);
        $this->entity->setUrl($host.'/uploads'.$saveFolder.'/'.str_replace(' ', '_', $file->getClientOriginalName()));
        $this->entityManager->flush(); // pour persist du chemin vers le fichier
    }
}