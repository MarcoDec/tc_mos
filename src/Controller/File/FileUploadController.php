<?php

namespace App\Controller\File;

use App\Entity\AbstractAttachment;
use App\Filesystem\FileManager;
use App\Service\ParameterManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class FileUploadController
{
   public function __construct(private EntityManagerInterface $entityManager, private FileManager $fileManager, private ParameterManager $parameterManager)
{
}

   /**
    * @throws Exception
    */
   public function __invoke(Request $request) {
      $host = $request->getSchemeAndHttpHost();
      /** @var UploadedFile $file */
      $file = $request->files->get('file');
      /** @var AbstractAttachment $entity */
      $entity = $request->attributes->get('data');
      $class = get_class($entity);
      if (!($entity instanceof AbstractAttachment)) throw new Exception("l'entité $class n'hérite pas de App\Entity\AbstractAttachment");
      $entity->setFile($file);

      $expirationDirectoriesParameter = $entity->getExpirationDirectoriesParameter();
      $parameterClass = $entity->getParameterClass();
      $expirationDirectories = $this->parameterManager->getParameter($parameterClass,$expirationDirectoriesParameter);
      foreach ($expirationDirectories->getTypedValue() as $directory) {
         if (str_contains($entity->getCategory(),$directory)) {
            $nbMonthParameter = $entity->getExpirationDurationParameter();
            $nbMonth = intval($this->parameterManager->getParameter($parameterClass,$nbMonthParameter)->getValue());
            $entity->setExpirationDate(new \DateTime("now + $nbMonth month"));
         } else {
            $entity->setExpirationDate(null);
         }
      }
      $this->entityManager->persist($entity);
      $this->entityManager->flush(); // pour récupération id utilisé par default dans getBaseFolder
      $saveFolder = $entity->getBaseFolder()."/".$entity->getCategory();
      $this->fileManager->persistFile($saveFolder,$file);
      $entity->setUrl($host."/uploads".$saveFolder."/".$file->getClientOriginalName());
      $this->entityManager->flush(); // pour persist du chemin vers le fichier
      return $entity;
   }
}