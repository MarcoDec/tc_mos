<?php

namespace App\Attribute\Couchdb\Abstract;

use JetBrains\PhpStorm\ArrayShape;
use ReflectionAttribute;

abstract class ManyToMany {
    /**
     * The fetching strategy to use for the association.
     */
    public string $fetch = Fetch::LAZY;

    public bool $owned = true;
    public string $targetEntity;

    public function __construct(
        string $targetEntity,
        bool $owned = true,
        string $fetch = Fetch::LAZY
    ) {
        $this->targetEntity = $targetEntity;
        $this->owned = $owned;
        $this->fetch = $fetch;
    }

    /**
     * @param ReflectionAttribute $reflectionAttribute
     *
     * @return array<string,mixed>
     * @phpstan-ignore-next-line
     */
    #[ArrayShape(['targetEntity' => 'mixed', 'owned' => 'mixed', 'fetch' => 'mixed', 'type' => 'string'])]
    public static function getPropertyData(ReflectionAttribute $reflectionAttribute): array {
        $instance = $reflectionAttribute->newInstance();
        /** @phpstan-ignore-next-line  */
        return ['targetEntity' => $instance->targetEntity, 'owned' => $instance->owned, 'fetch' => $instance->fetch, 'type' => self::class];
    }
}
