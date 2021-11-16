<?php

namespace App\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method static string getDefaultName()
 */
final class SchemaUpdateCommand extends AbstractCommand {
    private const DOCTRINE_COMMAND = 'doctrine:schema:update';

    protected static $defaultDescription = 'Modifie le schéma de la base de données en fonction des entités';
    protected static $defaultName = 'gpao:schema:update';

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $tag = 'Chargement du modèle';
        $this->startTime($tag);
        $this->getApplication()->find(self::DOCTRINE_COMMAND)->run(
            new ArrayInput(['command' => self::DOCTRINE_COMMAND, '--force' => true]),
            $output
        );
        $this->endTime($output, $tag);
        return self::SUCCESS;
    }
}
