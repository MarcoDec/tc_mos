<?php

namespace App\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DatabaseLoadCommand extends AbstractCommand {
    protected static $defaultDescription = 'Charge le schéma de la base de données puis les fixtures.';
    protected static $defaultName = 'gpao:database:load';

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $run = function (string $cmd) use ($output): void {
            $this
                ->getApplication()
                ->find($cmd)
                ->run(new ArrayInput(['command' => $cmd]), $output);
        };

        $run(SchemaUpdateCommand::getDefaultName());
        $run(FixturesCommand::getDefaultName());
        return self::SUCCESS;
    }
}
