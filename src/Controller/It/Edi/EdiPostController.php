<?php

namespace App\Controller\It\Edi;

use App\Entity\It\Edi;
use App\Filesystem\FileManager;
use App\Repository\Hr\Employee\EmployeeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\It\SFTPConnection;


class EdiPostController extends AbstractController
{
   public function __construct(
       private readonly EntityManagerInterface $em,
       private readonly EmployeeRepository     $repository,
       private readOnly FileManager $fm,
       private readonly LoggerInterface        $logger
   ) {
   }

   /**
    * @param Request $request
    * @return Edi
    */
   public function __invoke(Request $request): Edi {
       //region récupération des données des la requête
      $ediType = $request->get('ediType');
      $content = $request->getContent();
      $object = json_decode($content);
      $inputJson = $object->inputJson;
      $inputRef = $object->inputRef;
      $ediMode = $object->ediMode;
      //endregion
       //region récupération des données utilisateur
       $username = $this->getUser()->getUserIdentifier();
       $employeeAccount = $this->repository->findOneBy([
           'username' => $username
       ]);
       $this->logger->info("EDI: Requête de création", [$ediType, $inputRef, $ediMode, $username, $employeeAccount, $inputJson]);
       //endregion
       //region initialisation et persit EDI
       $newEdi = new Edi();
       $newEdi
           ->setInputJson($inputJson)
           ->setEdiType($ediType)
           ->setInputRef($inputRef)
           ->setLoginAccount($employeeAccount)
           ->setEdiMode($ediMode)
           ->setDate(new DateTime('now'))
       ;
       $this->em->persist($newEdi);
       $this->em->flush();
       $id = $newEdi->getId();
       //endregion
       //region génération du fichier json
       $basePath = $this->fm->getUploadsDir();
       if (!is_dir($basePath.'/Edi')) mkdir($basePath.'/Edi', 0755);
       $path = $basePath.'/Edi/'.$id.'/';
        if (!is_dir($path)) mkdir($path, 0755);
       $filename = $inputRef.".json";
       $pathfile = $path.$filename;
       $newEdi->setFilePath('/Edi/'.$id.'/'.$filename);
       try {
           file_put_contents($pathfile, $inputJson);
       } catch (Exception $e) {
           throw new Exception('Impossible de générer et sauvegarder le fichier de l\'EDI '.$id );
       }
       //endregion
       //region connexion et envoi du fichier JSON sur SFTP
        $directory = $ediMode === 'TEST'?'/test/':'/prod';
        $directory = $ediType === 'ORDERS'?$directory.'orders/':$directory;
        $filePathNameFinal = $directory.$inputRef.'.json';
        $filePathNameTMP = $filePathNameFinal.'.tmp';
       try {
            $sftp = new SFTPConnection('van.tx2.fr', 2022);
            $sftp->login('TCONCEPT', 'Fg@B7?Q?`kq\DESzr,');
            $sftp->uploadFile($pathfile, $filePathNameTMP);
            $sftp->renameFile($filePathNameTMP,$filePathNameFinal);
       } catch (Exception $e) {
            $this->logger->error($e->getMessage(), [$e]);
       }
       $this->em->flush(); // Pour sauvegarde chemin fichier JSON envoyé
       //endregion
      return $newEdi;
   }
}