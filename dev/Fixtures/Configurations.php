<?php

namespace App\Fixtures;

use Doctrine\ORM\EntityManagerInterface;
use Tightenco\Collect\Support\Collection;

final class Configurations {
    /** @var Collection<EntityConfig> */
    private Collection $configurations;

    public function __construct(private EntityManagerInterface $em) {
        $this->configurations = collect();
    }

    /**
     * @param class-string                                 $entity
     * @param array{deleted?: string, properties: mixed[]} $config
     */
    public function addConfig(string $name, string $entity, array $config): void {
        $this->configurations->put($name, new EntityConfig(
            metadata: $this->em->getClassMetadata($entity),
            config: $config
        ));
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

    public function persist(): void {
        foreach ($this->configurations as $config) {
            $this->em->getConnection()->executeStatement($config->toSQL($this->em->getConnection()));
        }
    }

    /**
     * @param mixed[] $data
     */
    public function setData(string $name, array $data): void {
        $this->configurations->get($name)->setData(
            data: $data,
            count: $this->count($this->configurations->get($name)->getClassName())
        );
    }
}
