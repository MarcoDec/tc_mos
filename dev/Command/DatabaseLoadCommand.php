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
        $run = function (string $cmd) use ($output): void {
            $this
                ->getApplication()
                ->find($cmd)
                ->run(new ArrayInput(['command' => $cmd]), $output);
        };

        $run(SchemaUpdateCommand::GPAO_SCHEMA_COMMAND);
        $run(FixturesCommand::GPAO_FIXTURES_COMMAND);
        return 0;
    }
}
