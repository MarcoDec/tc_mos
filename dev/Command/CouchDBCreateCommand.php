<?php

namespace App\Command;

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
       $client = CouchDBClient::create([
          'url'=>'http://couchdb:couchdb@tconcept-gpao_couchdb-server-0_1:5984',
          'dbname'=>'tconcept'
       ]);

       $client->createDatabase($client->getDatabase());
        return 0;
    }
}
