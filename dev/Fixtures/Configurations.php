<?php

namespace App\Fixtures;

use App\ExpressionLanguage\ExpressionLanguageProvider;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class Configurations {
    /** @var array<string, EntityConfig> */
    private array $configurations = [];

    /** @var array<int, string> */
    private array $countries = [];

    /** @var array<int, string> */
    private array $customscode = [];

    private ExpressionLanguage $exprLang;

    public function __construct(private EntityManagerInterface $em) {
        $this->exprLang = new ExpressionLanguage();
        $this->exprLang->registerProvider(new ExpressionLanguageProvider($this));
    }

    /**
     * @param class-string                                                                                                                             $entity
     * @param array{deleted?: string, properties: array{force_value?: string, new?: bool, new_name: string, new_ref?: class-string, old_ref?: string}} $config
     */
    public function addConfig(string $name, string $entity, array $config): void {
        $this->configurations[$name] = new EntityConfig(
            configurations: $this,
            exprLang: $this->exprLang,
            metadata: $this->em->getClassMetadata($entity),
            config: $config
        );
    }

    /**
     * @param class-string $className
     */
    public function count(string $className): int {
        $count = 0;
        foreach ($this->configurations as $config) {
            if ($config->getClassName() === $className) {
                $count += $config->count();
            }
        }
        return $count;
    }

    public function findData(string $name, int $id): mixed {
        return $this->configurations[$name]->findData($id);
    }

    /**
     * @return mixed[]
     */
    #[Pure]
    public function findEntities(string $name): array {
        return $this->configurations[$name]->getData();
    }

    public function getCountry(int $id): ?string {
        return $this->countries[$id] ?? null;
    }

    public function getCustomscode(int $id): ?string {
        return $this->customscode[$id] ?? null;
    }

    /**
     * @return string[]
     */
    public function getDependencies(string $name): array {
        return $this->configurations[$name]->getDependencies();
    }

    public function getId(string $name, int $id): ?int {
        return $this->configurations[$name]->getId($id);
    }

    public function persist(): void {
        /** @var Collection<int, string> $processed */
        $processed = new Collection();
        $configurations = collect($this->configurations);
        while ($configurations->isNotEmpty()) {
            foreach ($configurations as $current => $config) {
                $this->persistEntities($config, $current, $processed);
            }

            $configurations = $configurations->filter(static fn (EntityConfig $config, string $name): bool => $processed->doesntContain($name));
        }
    }

    /**
     * @param array{code: string, id: string, statut: string}[] $countries
     */
    public function setCountries(array $countries): void {
        $this->countries = collect($countries)
            ->mapWithKeys(static function (array $country): array {
                /** @var array{code: string, id: string, statut: string} $country */
                return empty($country['statut']) || $country['statut'] === '0' ? [(int) $country['id'] => $country['code']] : [];
            })
            ->all();
    }

    /**
     * @param array{code: string, id: string, statut: string}[] $customscode
     */
    public function setCustomscode(array $customscode): void {
        $this->customscode = collect($customscode)
            ->mapWithKeys(static function (array $code): array {
                /** @var array{code: string, id: string, statut: string} $code */
                return empty($code['statut']) || $code['statut'] === '0' ? [(int) $code['id'] => $code['code']] : [];
            })
            ->all();
    }

    /**
     * @param mixed[] $data
     */
    public function setData(string $name, array $data): void {
        $this->configurations[$name]->setData(
            data: $data,
            count: $this->count($this->configurations[$name]->getClassName())
        );
    }

    /**
     * @param Collection<int, string> $processed
     */
    private function persistEntities(EntityConfig $config, string $current, Collection $processed): void {
        $dependencies = $config->getDependencies();
        foreach ($dependencies as $dependency) {
            if (!$processed->contains($dependency) && $dependency !== $current) {
                return;
            }
        }
        $this->em->getConnection()->executeStatement($config->toSQL($this->em->getConnection()));
        $processed->push($current);
    }
}
