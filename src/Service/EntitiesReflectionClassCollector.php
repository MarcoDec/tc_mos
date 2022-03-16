<?php

namespace App\Service;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Collection;
use ReflectionClass;

final class EntitiesReflectionClassCollector {
    public function __construct(private readonly string $namespace) {
    }

    /**
     * @return array<class-string, ReflectionClass<object>>
     */
    public function getClasses(): array {
        /** @var Collection<class-string, ReflectionClass<object>> $classes */
        $classes = collect(ClassFinder::getClassesInNamespace($this->namespace, ClassFinder::RECURSIVE_MODE))
            ->mapWithKeys(static fn (string $class): array => [$class => new ReflectionClass($class)]);
        return $classes->all();
    }
}
