<?php

declare(strict_types=1);

namespace App\Command;

use App\Filesystem\FileManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'gpao:clean:uploads', description: 'Vide le dossier des uploads')]
class CleanUploadsCommand extends Command {
    public function __construct(private readonly FileManager $fm, ?string $name = null) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->fm->clean();
        return self::SUCCESS;
    }
}
