<?php

namespace App\Fixtures;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Tightenco\Collect\Support\Collection;

final class EntityConfig {
    /** @var Collection<mixed> */
    private Collection $entities;

    /** @var array<string, PropertyConfig> */
    private array $properties;

    /**
     * @param ClassMetadata<object> $metadata
     * @param mixed[]               $properties
     */
    public function __construct(private ClassMetadata $metadata, array $properties) {
        $this->entities = collect();
        $this->properties = collect($properties)
            ->map(static fn (array $config, string $property): PropertyConfig => new PropertyConfig($property, $config))
            ->all();
    }

    /**
     * @param mixed[] $data
     */
    public function setData(array $data): void {
        foreach ($data as $entity) {
            if ($entity['statut'] !== '0') {
                continue;
            }

            $transformed = ['id' => $entity['id']];
            foreach ($this->properties as $property => $config) {
                $transformed[$config->getNewName()] = $entity[$property];
            }
            $this->entities->push(collect($transformed));
        }
    }

    public function toSQL(Connection $connection): string {
        $columns = $this->getColumns();
        $sql = "INSERT INTO {$this->metadata->getTableName()} ({$columns->implode(', ')})";
        $sqlEntities = collect();
        foreach ($this->entities as $entity) {
            $sqlEntity = collect();
            foreach ($columns as $column) {
                $sqlEntity[] = $connection->quote($entity[$column]);
            }
            $sqlEntities->push("({$sqlEntity->implode(', ')})");
        }
        return sprintf(
            "INSERT INTO `{$this->metadata->getTableName()}` (%s) VALUES {$sqlEntities->implode(', ')}",
            $columns->map(static fn (string $column): string => "`$column`")->implode(', ')
        );
    }

    /**
     * @return Collection<string>
     */
    private function getColumns(): Collection {
        $max = $this->getMaxColumns();
        return collect(
            $this->entities
                ->first(static fn (Collection $entity): bool => $entity->keys()->count() === $max)
                ->keys()
        )->sort();
    }

    private function getMaxColumns(): int {
        return $this->entities->max(static fn (Collection $entity): int => $entity->keys()->count());
    }
}
