<?php

namespace App\Command;

use mysql_xdevapi\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\CouchDB\CouchDBClient;

final class CouchDBCreateCommand extends AbstractCommand {
    public function __construct() {
        parent::__construct('gpao:couchdb:create');
    }

    protected function configure(): void {
        $this->setDescription('Créer la base de données Couch `tconcept`.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
       try {
          $client = CouchDBClient::create([
             'url'=>'http://couchdb:couchdb@couchdb-server-0:5984',
             'dbname'=>'tconcept'
          ]);
          $client->createDatabase($client->getDatabase());
       } catch (\Exception $e) {
          echo "Plantage => ".$e->getMessage()."\n";
       }
        return 0;
    }
}
