<?php

namespace App\Command;

use App\Service\CouchDBManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CouchDBCreateCommand extends AbstractCommand {
    public function __construct(private CouchDBManager $DBManager) {
        parent::__construct('gpao:couchdb:create');
    }

    protected function configure(): void {
        $this->setDescription('Créer la base de données Couch.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
       try {
          $this->DBManager->createDatabase();
       } catch (\Exception $e) {
          echo "Plantage => ".$e->getMessage()."\n";
       }
        return 0;
    }
}
