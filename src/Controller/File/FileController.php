<?php

namespace App\Controller\File;

use App\Entity\AbstractAttachment;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class FileController
{
   public function __construct(private EntityManagerInterface $entityManager)
{
}
   public function __invoke( Request $request) {
      $file = $request->files->get('file');
      $entity = $request->attributes->get('data');
      $class = get_class($entity);
      if (!($entity instanceof AbstractAttachment)) throw new Exception("l'entité $class n'hérite pas de App\Entity\AbstractAttachment");
      $entity->setFile($file);
      $entity->setExpirationDate(new \DateTime('now + 1 day'));
      $entity->setUrl($entity->getFile()->getPathname());
      $this->entityManager->persist($entity);
      $this->entityManager->flush();
      dd($entity);
      return $entity;
   }
}