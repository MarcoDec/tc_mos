<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'gpao:database:load', description: 'Charge le schéma de la base de données puis les fixtures')]
class DatabaseLoadCommand extends Command {
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $run = function (string $cmd, array $options = []) use ($output): void {
            $output->writeln("<fg=green>$cmd</>");
            $options['command'] = $cmd;
            $this->getApplication()->find($cmd)->run(new ArrayInput($options), $output);
        };
        $run('doctrine:database:drop', ['--force' => true]);
        $run('doctrine:database:create');
        $run('doctrine:migrations:migrate', ['--all-or-nothing' => true]);
        $run(TreeVerifyCommand::getDefaultName());
        return self::SUCCESS;
    }
}
