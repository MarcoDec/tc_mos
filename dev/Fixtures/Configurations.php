<?php

namespace App\Fixtures;

use App\ExpressionLanguage\ExpressionLanguageProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class Configurations {
    /** @var array<string, EntityConfig> */
    private array $configurations = [];

    /** @var array<int, string> */
    private array $countries = [];

    private ExpressionLanguage $exprLang;

    public function __construct(private EntityManagerInterface $em) {
        $this->exprLang = new ExpressionLanguage();
        $this->exprLang->registerProvider(new ExpressionLanguageProvider($this));
    }

    /**
     * @param class-string                                                                                                                                             $entity
     * @param array{deleted?: string, properties: array{country?: bool, force_value?: string, new?: bool, new_name: string, new_ref?: class-string, old_ref?: string}} $config
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

    /**
     * @return mixed
     */
    public function findData(string $name, int $id) {
        return $this->configurations[$name]->findData($id);
    }

    public function getCountry(int $id): ?string {
        return $this->countries[$id] ?? null;
    }

    public function getId(string $name, int $id): ?int {
        return $this->configurations[$name]->getId($id);
    }

    public function persist(): void {
        foreach ($this->configurations as $config) {
            $this->em->getConnection()->executeStatement($config->toSQL($this->em->getConnection()));
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
     * @param mixed[] $data
     */
    public function setData(string $name, array $data): void {
        $this->configurations[$name]->setData(
            data: $data,
            count: $this->count($this->configurations[$name]->getClassName())
        );
    }
}
