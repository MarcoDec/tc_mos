<?php

namespace App\Fixtures;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class EntityConfig {
    /** @var mixed[] */
    private array $data = [];

    private ?string $deleted;

    /** @var Collection<int, mixed> */
    private Collection $entities;

    /** @var array<int, int> */
    private array $ids = [];

    /** @var array<string, PropertyConfig> */
    private array $properties;

    /**
     * @param ClassMetadata<object> $metadata
     * @param array{deleted?: string, properties: array{country?: bool, customscode?: bool, force_value?: string, new?: bool, new_name: string, new_ref?: class-string, old_ref?: string}[]} $config
     */
    public function __construct(
        private Configurations $configurations,
        private ExpressionLanguage $exprLang,
        private ClassMetadata $metadata,
        array $config
    ) {
        $this->deleted = $config['deleted'] ?? null;
        /** @var mixed[] entities */
        $entities = [];
        $this->entities = collect($entities);
        $this->properties = collect($config['properties'])
            ->map(static function (array $config): PropertyConfig {
                /** @var array{country?: bool, customscode?: bool, force_value?: string, new?: bool, new_name: string, new_ref?: class-string, old_ref?: string} $config */
                return new PropertyConfig($config);
            })
            ->all();
    }

    #[Pure]
    public function count(): int {
        return $this->entities->count();
    }

    /**
     * @return mixed
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
     * @return mixed[]
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
     * @param mixed[] $data
     */
    public function setData(array $data, int $count): void {
        $this->data = $data;

        foreach ($data as $entity) {
            if (!empty($this->deleted) && $entity[$this->deleted] !== '0') {
                continue;
            }

            $transformed = ['id' => $id = ++$count];
            $this->ids[(int) ($entity['id'])] = $id;
            foreach ($this->properties as $property => $config) {
                $value = !$config->isNew() ? $entity[$property] : null;
                if (is_string($value)) {
                    $value = trim($value);
                }
                if ($config->isCountry()) {
                    $value = $this->configurations->getCountry($value);
                }
                if ($config->isCustomscode()) {
                    $value = $this->configurations->getCustomscode($value);
                }
                if (!empty($forceValue = $config->getForceValue())) {
                    $value = $this->exprLang->evaluate($forceValue, $entity);
                }
                if (!empty($ref = $config->getOldRef())) {
                    $value = $this->configurations->getId($ref, $value);
                }
                $transformed[$config->getNewName()] = $value;
            }
            $this->entities->push(collect($transformed));
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
                $sqlEntity[] = $entity[$column] === null || $entity[$column] === '' ? 'NULL' : $connection->quote($entity[$column]);
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
        return $this->entities->max(static fn (Collection $entity): int => $entity->keys()->count());
    }
}
