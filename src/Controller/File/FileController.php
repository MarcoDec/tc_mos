<?php

namespace App\Controller\File;

use App\Entity\AbstractAttachment;
use App\Filesystem\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class FileController
{
   public function __construct(private EntityManagerInterface $entityManager, private FileManager $fileManager)
{
}

   public function __invoke( Request $request) {
      $host = $request->getSchemeAndHttpHost();
      /** @var UploadedFile $file */
      $file = $request->files->get('file');
      /** @var AbstractAttachment $entity */
      $entity = $request->attributes->get('data');
      $class = get_class($entity);
      if (!($entity instanceof AbstractAttachment)) throw new Exception("l'entité $class n'hérite pas de App\Entity\AbstractAttachment");
      $entity->setFile($file);
      $entity->setExpirationDate(new \DateTime('now + 1 day')); //TODO: Ajouter date en fonction paramètre de la classe
      $this->entityManager->persist($entity);
      $this->entityManager->flush(); // pour récupération id utilisé par default dans getBaseFolder
      $saveFolder = $entity->getBaseFolder().$entity->getTargetFolder();
      $this->fileManager->persistFile($saveFolder,$file);
      $entity->setUrl($host."/uploads".$saveFolder."/".$file->getClientOriginalName());
      $this->entityManager->flush(); // pour persist du chemin vers le fichier
      return $entity;
   }
}