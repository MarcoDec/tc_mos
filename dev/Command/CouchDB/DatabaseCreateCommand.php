<?php

namespace App\Command\CouchDB;

use App\Command\AbstractCommand;
use App\CouchDB\DocumentManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DatabaseCreateCommand extends AbstractCommand {
    protected static $defaultDescription = 'Créer la base de données NoSQL.';
    protected static $defaultName = 'gpao:couchdb:database:create';

    public function __construct(private DocumentManager $dm) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->dm->createDatabase();
        return self::SUCCESS;
    }
}
