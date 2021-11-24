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

          echo "Identification des documents à supprimer\n";
          //Identification des Documents à supprimer en base
          //$docsToDelete =
          //Identification des Documents à créer en base

       } catch (\Exception $e) {
          echo "Plantage => ".$e->getMessage()."\n";
       }
        return 0;
    }
}
