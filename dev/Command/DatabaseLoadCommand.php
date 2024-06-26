<?php

namespace App\Command;

use App\Command\Project\Product\ExpirationDateCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DatabaseLoadCommand extends AbstractCommand {
    protected static $defaultDescription = 'Charge le schéma de la base de données puis les fixtures.';
    protected static $defaultName = 'gpao:database:load';

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $run = function (string $cmd, array $options = [], bool $verbose = false) use ($output): void {
            $output->writeln("<fg=green>$cmd</>");
            $options['command'] = $cmd;
            $commandOutput = $verbose ? new \Symfony\Component\Console\Output\ConsoleOutput($output->getVerbosity()) : $output;
            $this
                ->getApplication()
                ->find($cmd)
                ->run(new ArrayInput($options), $commandOutput);
        };

        $run('doctrine:database:drop', ['--force' => true]);
        $run('doctrine:database:create');
        $run('doctrine:migrations:migrate', ['--verbose' => true], true);
        //$run(CronCommand::getDefaultName(), ['--'.CronCommand::OPTION_SCAN => true]);
        $run(CurrencyRateCommand::getDefaultName());
        $run(ExpirationDateCommand::getDefaultName());
        return self::SUCCESS;
    }
}
