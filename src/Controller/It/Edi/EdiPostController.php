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


class EdiPostController extends AbstractController
{
   public function __construct(
       private readonly EntityManagerInterface $em,
       private readonly EmployeeRepository     $repository,
       private readOnly FileManager $fm,
       private readonly LoggerInterface        $logger,
       //private UserPasswordHasherInterface $passwordHasher
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
       $dateStr = date('YmdHisv');
       $filename = $ediType."_".$inputRef ."_".$dateStr.".json";
       $pathfile = $path.$filename;
       try {
           file_put_contents($pathfile, $inputJson);
       } catch (Exception $e) {
           throw new Exception('Impossible de générer et sauvegarder le fichier de l\'EDI '.$id );
       }
       //endregion
       //region connexion et envoi du fichier JSON sur SFTP
       dump('EDI: connexion SFTP en cours');
       if (!$connection = ssh2_connect('van.tx2.fr', 2022)) {
           dump('EDI: CANNOT CONNEXT TO SERVER');
       } else {
           dump('EDI: CONNECTED TO THE SERVER');
           if (!ssh2_auth_password($connection, 'TCONCEPT', 'Fg@B7?Q?`kq\DESzr,')) {
               dump('EDI: AUTHENTICATION FAILED...');
           } else {
               dump('EDI: Jusque là tout va bien...');
               $sftp=ssh2_sftp($connection);
               $connection_string = 'ssh2.sftp://' . intval($sftp);
               dump('EDI: connection_string',$connection_string);
               $handle = opendir($connection_string."/");
               dump( "Directory handle: $handle");
               dump("Entries:");
               while (false != ($entry = readdir($handle))){
                   dump("$entry");
               }
               //$r = new \RecursiveIteratorIterator($i);
//               dump('EDI: Récupération Dossiers distants');
//               foreach ($r as $f) {
//                   dump($f->getPathname());
//               }
               $connection = null; unset($connection);
               dump('EDI: Déconnection SFTP OK');
           }
       }
       //endregion
      //$this->logger->debug('api EDI $request->getContent()', [$ediType, $inputJson, $object, $username, $employeeAccount]);
      return $newEdi;
   }
}