<?php

namespace App\Command;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SchemaUpdateCommand extends AbstractCommand {
    private const DOCTRINE_COMMAND = 'doctrine:schema:update';

    public function __construct() {
        parent::__construct('gpao:schema:update');
    }

    protected function configure(): void {
        $this->setDescription('Modifie le schéma de la base de données en fonction des entités');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $tag = 'Chargement du modèle';
        $this->startTime($tag);
        $application = $this->getApplication();
        if (empty($application)) {
            throw new RuntimeException('Application not found.');
        }
        $application->find(self::DOCTRINE_COMMAND)->run(
            new ArrayInput(['command' => self::DOCTRINE_COMMAND, '--force' => true]),
            $output
        );
        $this->endTime($output, $tag);
        return 0;
    }
}
