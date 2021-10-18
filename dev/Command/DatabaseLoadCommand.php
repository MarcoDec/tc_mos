<?php

namespace App\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DatabaseLoadCommand extends AbstractCommand {
    public function __construct() {
        parent::__construct('gpao:database:load');
    }

    protected function configure(): void {
        $this->setDescription('Charge le schéma de la base de données puis les fixtures.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this
            ->getApplication()
            ->find(SchemaUpdateCommand::GPAO_SCHEMA_COMMAND)
            ->run(new ArrayInput(['command' => SchemaUpdateCommand::GPAO_SCHEMA_COMMAND]), $output);
        return 0;
    }
}
