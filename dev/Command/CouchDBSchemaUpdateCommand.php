<?php

namespace App\Command;

use App\Entity\Couchdb\Document;
use App\Service\CouchDBManager;
use Exception;
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
            $entityDocList = $this->DBManager->getCouchdbDocuments();
            $nbEntityDoc = count($entityDocList);
            echo "{$nbEntityDoc} Document(s) définis dans le modèle\n";
            $nbCouchdbDoc = count($docsList);
            echo "{$nbCouchdbDoc} Document(s) actuellement en base CouchDB\n";
            //region Identification des Documents à supprimer en base
            $docsToDelete = collect($docsList)->filter(static function ($dbDoc) use ($entityDocList) {
                foreach ($entityDocList as $key => $entity) {
                    if ($dbDoc == $entity) {
                        return false;
                    }
                }
                return true;
            })->toArray();
            foreach ($docsToDelete as $dbDoc) {
                echo 'Suppression '.$dbDoc."\n";
                $rev = $this->DBManager->documentGetRev($dbDoc);
                $this->DBManager->documentDelete($dbDoc, $rev);
            }
            $nbDeleted = count($docsToDelete);
            echo "====> {$nbDeleted} Document(s) supprimé(s)\n";
            //endregion

            //region Identification des Documents à créer en base
            $docToCreate = collect($entityDocList)->filter(static function ($entityDoc) use ($docsList) {
                $test = true;
                foreach ($docsList as $key => $doc) {
                    if ($doc == $entityDoc) {
                        $test = false;
                    }
                }
                return $test;
            })->toArray();
            /** @var Document $dbDoc */
            foreach ($docToCreate as $dbDoc) {
                echo 'Création '.$dbDoc->getId()."\n";
                $this->DBManager->documentCreate([
                    '_id' => $dbDoc
                ]); //Document vide par défault
            }
            $nbAdded = count($docToCreate);
            echo "====> {$nbAdded} Document(s) ajouté(s)\n";
            //endregion
        } catch (Exception $e) {
            echo 'Plantage => '.$e->getMessage()."\n";
        }
        return 0;
    }
}
