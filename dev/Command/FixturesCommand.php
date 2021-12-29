<?php

namespace App\Command;

use App\Fixtures\Configurations;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @method static string getDefaultName()
 */
final class FixturesCommand extends AbstractCommand {
    private const DOCTRINE_FIXTURES_COMMAND = 'doctrine:fixtures:load';

    protected static $defaultDescription = 'Transfert les données de l\'ancien système vers le nouveau.';
    protected static $defaultName = 'gpao:fixtures:load';
    private Configurations $configurations;

    public function __construct(
        private string $configDir,
        EntityManagerInterface $em,
        private string $jsonDir,
        private string $jsonPrefix
    ) {
        parent::__construct();
        $this->configurations = new Configurations($em);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $loadConfigTag = 'Chargement des configurations YAML';
        $this->startTime($loadConfigTag);
        $this->loadConfig();
        $this->endTime($output, $loadConfigTag);

        $loadJSONTag = 'Chargement des données JSON';
        $this->startTime($loadJSONTag);
        $this->loadJSON();
        $this->endTime($output, $loadJSONTag);

        $persistTag = 'Enregistrement des données en base';
        $this->startTime($persistTag);
        $this->configurations->persist();
        $this->endTime($output, $persistTag);

        $loadDoctrine = 'Chargement des fixtures';
        $this->startTime($loadDoctrine);
        $this
            ->getApplication()
            ->find(self::DOCTRINE_FIXTURES_COMMAND)
            ->run(new ArrayInput(['command' => self::DOCTRINE_FIXTURES_COMMAND, '--append' => true]), $output);
        $this->endTime($output, $loadDoctrine);

        $loadCurrencyRate = 'Chargement des taux de change des devises';
        $this->startTime($loadCurrencyRate);
        $this
            ->getApplication()
            ->find(CurrencyRateCommand::getDefaultName())
            ->run(new ArrayInput(['command' => CurrencyRateCommand::getDefaultName()]), $output);
        $this->endTime($output, $loadCurrencyRate);

        return self::SUCCESS;
    }

    private function loadConfig(): void {
        if (empty($configDir = scandir($this->configDir))) {
            throw new RuntimeException("Invalid or empty dir $this->configDir.");
        }

        foreach ($configDir as $file) {
            if (str_ends_with($file, '.yaml')) {
                if (!is_array($yaml = Yaml::parseFile("$this->configDir/$file"))) {
                    throw new RuntimeException("Invalid $file.");
                }

                foreach ($yaml as $entity => $config) {
                    /** @var class-string $entity */
                    $this->configurations->addConfig(
                        name: removeEnd($file, '.yaml'),
                        entity: $entity,
                        config: $config
                    );
                }
            }
        }
    }

    private function loadCountries(): void {
        if (empty($json = file_get_contents("$this->jsonDir/{$this->jsonPrefix}country.json"))) {
            throw new RuntimeException("Invalid $json.");
        }

        $this->configurations->setCountries(json_decode($json, true));
    }

    private function loadJSON(): void {
        $this->loadCountries();

        if (empty($jsonDir = scandir($this->jsonDir))) {
            throw new RuntimeException("Invalid or empty dir $this->jsonDir.");
        }

        $excludes = ["{$this->jsonPrefix}country.json"];
        foreach ($jsonDir as $file) {
            if (!in_array($file, $excludes) && str_ends_with($file, '.json')) {
                if (empty($json = file_get_contents("$this->jsonDir/$file"))) {
                    throw new RuntimeException("Invalid $json.");
                }

                $this->configurations->setData(
                    name: removeEnd(removeStart($file, $this->jsonPrefix), '.json'),
                    data: json_decode($json, true)
                );
            }
        }
    }
}
