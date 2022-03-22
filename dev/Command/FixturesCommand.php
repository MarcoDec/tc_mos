<?php

namespace App\Command;

use App\Fixtures\Configurations;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @method static string getDefaultName()
 *
 * @phpstan-import-type CodeJson from Configurations
 * @phpstan-import-type Entity from Configurations
 */
final class FixturesCommand extends AbstractCommand {
    private const DOCTRINE_FIXTURES_COMMAND = 'doctrine:fixtures:load';

    protected static $defaultDescription = 'Transfert les données de l\'ancien système vers le nouveau.';
    protected static $defaultName = 'gpao:fixtures:load';
    private readonly Configurations $configurations;

    public function __construct(
        private readonly string $configDir,
        EntityManagerInterface $em,
        private readonly string $jsonDir,
        private readonly string $jsonPrefix
    ) {
        parent::__construct();
        $this->configurations = new Configurations($em);
    }

    #[Pure]
    public function getOldName(string $file): string {
        return removeEnd(removeStart($file, $this->jsonPrefix), '.json');
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

        /** @var CodeJson[] $countries */
        $countries = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $this->configurations->setCountries($countries);
    }

    private function loadCustomscode(): void {
        if (empty($json = file_get_contents("$this->jsonDir/{$this->jsonPrefix}customcode.json"))) {
            throw new RuntimeException("Invalid $json.");
        }

        /** @var CodeJson[] $customscode */
        $customscode = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $this->configurations->setCustomscode($customscode);
    }

    private function loadJSON(): void {
        $this->loadCountries();
        $this->loadCustomscode();

        if (empty($jsonDir = scandir($this->jsonDir))) {
            throw new RuntimeException("Invalid or empty dir $this->jsonDir.");
        }

        $jsonDir = collect($jsonDir);
        $excludes = ["{$this->jsonPrefix}country.json", "{$this->jsonPrefix}customcode.json"];
        /** @var Collection<int, string> $processed */
        $processed = new Collection();
        while ($jsonDir->isNotEmpty()) {
            foreach ($jsonDir as $file) {
                $this->loadJSONFile($excludes, $file, $processed);
            }

            $jsonDir = $jsonDir->filter(static fn (string $file): bool => $processed->doesntContain($file));
        }
    }

    /**
     * @param string[]                $excludes
     * @param Collection<int, string> $processed
     */
    private function loadJSONFile(array $excludes, string $file, Collection $processed): void {
        $name = $this->getOldName($file);

        if (!in_array($file, $excludes) && str_ends_with($file, '.json')) {
            $dependencies = $this->configurations->getDependencies($name);
            $oldProcessed = $processed->map([$this, 'getOldName']);
            foreach ($dependencies as $dependency) {
                if (!$oldProcessed->contains($dependency) && $dependency !== $name) {
                    return;
                }
            }

            if (empty($json = file_get_contents("$this->jsonDir/$file"))) {
                throw new RuntimeException("Invalid $json.");
            }

            /** @var Entity[] $entities */
            $entities = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $this->configurations->setData(name: $name, data: $entities);
        }

        $processed->push($file);
    }
}
