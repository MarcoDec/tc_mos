<?php

namespace App\Fixtures;

use App\Entity\Management\Unit;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use UnexpectedValueException;

/**
 * @phpstan-import-type PropertyConfigArray from PropertyConfig
 *
 * @phpstan-type ConvertedEntity Collection<string, bool|int|float|string>
 * @phpstan-type Entity array<string, bool|int|float|string>
 */
final class EntityConfig {
    /** @var Entity[] */
    private array $data = [];

    private ?string $deleted;

    /** @var Collection<int, ConvertedEntity> */
    private Collection $entities;

    /** @var array<int, int> */
    private array $ids = [];

    /** @var array<string, PropertyConfig> */
    private readonly array $properties;

    /**
     * @param ClassMetadata<object>                                                   $metadata
     * @param array{deleted?: string, properties: array<string, PropertyConfigArray>} $config
     */
    public function __construct(
        private readonly Configurations $configurations,
        private readonly ExpressionLanguage $exprLang,
        private readonly ClassMetadata $metadata,
        array $config
    ) {
        $this->deleted = $config['deleted'] ?? null;
        /** @var ConvertedEntity[] entities */
        $entities = [];
        $this->entities = collect($entities);
        /** @var Collection<string, PropertyConfigArray> $properties */
        $properties = collect($config['properties']);
        $this->properties = $properties
            ->map(static fn (array $config): PropertyConfig => new PropertyConfig($config))
            ->all();
    }

    #[Pure]
    public function count(): int {
        return $this->entities->count();
    }

    /**
     * @return ConvertedEntity|null
     */
    public function findData(int $id) {
        return $this->entities->first(fn (Collection $entity): bool => $entity['id'] === $this->ids[$id]);
    }

    /**
     * @return class-string
     */
    #[Pure]
    public function getClassName(): string {
        return $this->metadata->getName();
    }

    /**
     * @return Entity[]
     */
    public function getData(): array {
        return $this->data;
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array {
        return collect($this->properties)->map->getOldRef()->filter()->unique()->values()->all();
    }

    public function getId(int $id): ?int {
        return $this->ids[$id] ?? null;
    }

    /**
     * @param Entity[] $data
     */
    public function setData(array $data, int $count): void {
        $this->data = [];
        foreach ($data as $entity) {
            if (!empty($this->deleted) && $entity[$this->deleted] !== '0') {
                continue;
            }

            $id = ++$count;
            $this->ids[(int) ($entity['id'])] = $id;
            $entity['id'] = $id;
            $this->data[] = $entity;
        }

        foreach ($this->data as $entity) {
            /** @var ConvertedEntity $transformed */
            $transformed = collect(['id' => $entity['id']]);
            foreach ($this->properties as $property => $config) {
                /** @var bool|float|int|string $value */
                $value = !$config->isNew() ? $entity[$property] : null;
                if (is_string($value)) {
                    $value = trim($value);
                }
                if ($config->isCountry() && is_numeric($value)) {
                    $value = $this->configurations->getCountry((int) $value);
                }
                if ($config->isCustomscode() && is_numeric($value)) {
                    $value = $this->configurations->getCustomscode((int) $value);
                }
                if (!empty($forceValue = $config->getForceValue())) {
                    $value = $this->exprLang->evaluate($forceValue, $entity);
                }
                if (!empty($ref = $config->getOldRef()) && is_numeric($value)) {
                    $value = $this->configurations->getId($ref, (int) $value);
                }
                $transformed[$config->getNewName()] = $value;
            }
            $this->entities->push($transformed);
        }

        if ($this->metadata->getName() === Unit::class) {
            /** @phpstan-ignore-next-line */
            $this->entities = $this->entities
                ->mapToGroups(static fn (Collection $entity): array => [$entity['parent_id'] === null ? 'null' : 'parent' => $entity])
                ->sortKeys()
                ->flatten(1);
        }
    }

    public function toSQL(Connection $connection): string {
        $columns = $this->getColumns();
        /** @var Collection<int, string> $sqlEntities */
        $sqlEntities = new Collection();
        foreach ($this->entities as $entity) {
            /** @var Collection<int, string> $sqlEntity */
            $sqlEntity = new Collection();
            foreach ($columns as $column) {
                $sql = $entity[$column] === null || $entity[$column] === '' ? 'NULL' : $connection->quote($entity[$column]);
                if (is_string($sql) && str_contains($sql, '%')) {
                    $sql = str_replace('%', '%%', $sql);
                }
                $sqlEntity[] = $sql;
            }
            $sqlEntities->push("({$sqlEntity->implode(', ')})");
        }
        return sprintf(
            "INSERT INTO `{$this->metadata->getTableName()}` (%s) VALUES {$sqlEntities->implode(', ')}",
            $columns->map(static fn (string $column): string => "`$column`")->implode(', ')
        );
    }

    /**
     * @return Collection<int, string>
     */
    private function getColumns(): Collection {
        $max = $this->getMaxColumns();
        /** @var Collection<string, mixed>|null $columns */
        $columns = $this->entities->first(static fn (Collection $entity): bool => $entity->keys()->count() === $max);
        return (new Collection($columns))->keys()->sort();
    }

    private function getMaxColumns(): int {
        /** @var ConvertedEntity|int $max */
        $max = $this->entities->max(static fn (Collection $entity): int => $entity->keys()->count());
        if (is_int($max)) {
            return $max;
        }
        throw new UnexpectedValueException(sprintf('Expected argument of type "int", "%s" given', get_debug_type($max)));
    }
}
