<?php

namespace App\Command;

use App\Service\CouchDBManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CouchDBSchemaUpdateCommand extends AbstractCommand {
    public function __construct(private CouchDBManager $DBManager) {
        parent::__construct('gpao:couchdb:schema:update');
    }

    protected function configure(): void {
        $this->setDescription('Mets à jour la structure des documents de la base de données Couch `tconcept`.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
       //Tentative de connexion à la base de donnée couchdb et récupération des documents
       try {
          $docsList = $this->DBManager->getDocList();
          echo "docsList actuellement en base\n";
          echo print_r($docsList,true)."\n";
          $entityDocList = $this->DBManager->getCouchdbDocument();
          echo "entityDocList actuellement définis\n";
          echo print_r($entityDocList,true)."\n";

          //region Identification des Documents à supprimer en base
          $docsToDelete = collect($docsList)->filter(function ($dbDoc) use ($entityDocList) {
             foreach ($entityDocList as $key => $entity) {
                if ($dbDoc == $entity) return false;
             }
             return true;
          })->toArray();
          foreach ($docsToDelete as $dbDoc) {
             echo "\nSuppression ".$dbDoc."\n";
            $rev = $this->DBManager->getDocumentRev($dbDoc);
            $this->DBManager->deleteDocument($dbDoc, $rev);
          }
          echo "\nFin Suppression \n";
          //endregion

          //region Identification des Documents à créer en base
          $docToCreate= collect($entityDocList)->filter(function ($entityDoc) use ($docsList) {
             $test=true;
             foreach ($docsList as $key => $doc) {
                if ($doc == $entityDoc) $test= false;
             }
             return $test;
          })->toArray();
          foreach ($docToCreate as $dbDoc) {
             echo "\nCréation ".$dbDoc."\n";
             list($dbDoc,$rev) = $this->DBManager->postDocument(array('foo' => 'bar')); //Document vide par défault
          }
          echo "\nFin Suppression \n";
          //endregion
       } catch (\Exception $e) {
          echo "Plantage => ".$e->getMessage()."\n";
       }
        return 0;
    }
}
