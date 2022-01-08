<?php

namespace App\Attribute\Couchdb\Abstract;

use JetBrains\PhpStorm\ArrayShape;
use ReflectionAttribute;

abstract class OneToOne {
    /**
     * The fetching strategy to use for the association.
     */
    public string $fetch = Fetch::LAZY;

    public string $mappedBy;
    public string $targetEntity;

    public function __construct(
        string $mappedBy,
        string $targetEntity,
        string $fetch = Fetch::LAZY
    ) {
        $this->mappedBy = $mappedBy;
        $this->targetEntity = $targetEntity;
        $this->fetch = $fetch;
    }

    /**
     * @param ReflectionAttribute $property
     *
     * @return array<string,mixed>
     * @phpstan-ignore-next-line
     */
    #[ArrayShape(['targetEntity' => 'mixed', 'mappedBy' => 'mixed', 'fetch' => 'mixed', 'type' => 'string'])]
    public static function getPropertyData(ReflectionAttribute $property): array {
        $instance = $property->newInstance();
        /** @phpstan-ignore-next-line */
        return ['targetEntity' => $instance->targetEntity, 'mappedBy' => $instance->mappedBy, 'fetch' => $instance->fetch, 'type' => self::class];
    }
}
