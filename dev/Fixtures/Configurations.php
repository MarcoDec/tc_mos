<?php

namespace App\Fixtures;

use Doctrine\ORM\EntityManagerInterface;

final class Configurations {
    /** @var array<string, EntityConfig> */
    private array $configurations = [];

    public function __construct(private EntityManagerInterface $em) {
    }

    /**
     * @param class-string                                                                                           $entity
     * @param array{deleted?: string, properties: array{new_name: string, new_ref?: class-string, old_ref?: string}} $config
     */
    public function addConfig(string $name, string $entity, array $config): void {
        $this->configurations[$name] = new EntityConfig(
            configurations: $this,
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

    public function getId(string $name, string $id): ?int {
        return $this->configurations[$name]->getId($id);
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
        $this->configurations[$name]->setData(
            data: $data,
            count: $this->count($this->configurations[$name]->getClassName())
        );
    }
}
