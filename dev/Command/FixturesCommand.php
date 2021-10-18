<?php

namespace App\Command;

use App\Fixtures\EntityConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

final class FixturesCommand extends AbstractCommand {
    public const GPAO_FIXTURES_COMMAND = 'gpao:fixtures:load';

    /** @var EntityConfig[] */
    private array $config = [];

    public function __construct(private string $configDir, private EntityManagerInterface $em) {
        parent::__construct(self::GPAO_FIXTURES_COMMAND);
    }

    protected function configure(): void {
        $this->setDescription('Transfert les données de l\'ancien système vers le nouveau.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->loadConfig();
        return 0;
    }

    private function loadConfig(): void {
        if (empty($configDir = scandir($this->configDir))) {
            throw new RuntimeException("Invalid or empty dir $this->configDir");
        }

        foreach ($configDir as $file) {
            if (str_ends_with($file, '.yaml')) {
                if (!is_array($yaml = Yaml::parseFile("$this->configDir/$file"))) {
                    throw new RuntimeException("Invalid $file.");
                }

                foreach ($yaml as $entity => $config) {
                    $this->config[removeEnd($file, '.yaml')] = new EntityConfig(
                        $this->em->getClassMetadata($entity),
                        $config
                    );
                }
            }
        }
    }
}
