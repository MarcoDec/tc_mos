<?php

namespace App\Controller\File;

use App\Entity\AbstractAttachment;
use App\Filesystem\FileManager;
use App\Service\ParameterManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class FileUploadController {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileManager $fileManager,
        private ParameterManager $parameterManager) {
    }

   public AbstractAttachment $entity;
    /**
     * @throws Exception
     */
    public function __invoke(Request $request):AbstractAttachment {
        $this->getEntity($request);
        $this->performCustomEntityAction();
        $this->getFileAndPersist($request);
        return $this->entity;
    }
    public function getEntity(Request $request) {
       $entity = $request->attributes->get('data');
       $class = get_class($entity);
       if (!($entity instanceof AbstractAttachment)) {
          throw new RuntimeException("l'entité $class n'hérite pas de App\\Entity\\AbstractAttachment");
       } else {
          $this->entity = $entity;
       }
    }
    public function getFileAndPersist(Request $request, bool $withError = true) {
       $host = $request->getSchemeAndHttpHost();
       $file = $request->files->get('file');
       if ($withError && $file->getError()===1) {
          throw new FileException($file->getErrorMessage());
       }
       $this->entity->setFile($file);
       if ($this->entity->hasParameter) {
          $expirationDirectoriesParameter = $this->entity->getExpirationDirectoriesParameter();
          $parameterClass = $this->entity->getParameterClass();
          $expirationDirectories = $this->parameterManager->getParameter($parameterClass, $expirationDirectoriesParameter);
          foreach ($expirationDirectories->getTypedValue() as $directory) {
             if (str_contains($this->entity->getCategory(), $directory)) {
                $durationParameter = $this->entity->getExpirationDurationParameter();
                $duration = (int) ($this->parameterManager->getParameter($parameterClass, $durationParameter)->getValue());
                $durationUnit = $this->entity->getExpirationDateStr();
                $this->entity->setExpirationDate(new DateTime("now + $duration $durationUnit"));
             } else {
                $this->entity->setExpirationDate(null);
             }
          }
       }
       $this->entityManager->persist($this->entity);
       $this->entityManager->flush(); // pour récupération id utilisé par default dans getBaseFolder
       $saveFolder = $this->entity->getBaseFolder().'/'.$this->entity->getCategory();
       $this->fileManager->persistFile($saveFolder, $file);
       $this->entity->setUrl($host.'/uploads'.$saveFolder.'/'.str_replace(' ', '_', $file->getClientOriginalName()));
       $this->entityManager->flush(); // pour persist du chemin vers le fichier
    }
    public function performCustomEntityAction() {}
}
