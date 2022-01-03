<?php

declare(strict_types=1);

namespace App\PhpStan\Support;

use Iterator;
use IteratorAggregate;
use PHPStan\Type\GeneralizePrecision;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeUtils;
use PHPStan\Type\TypeWithClassName;
use Tightenco\Collect\Support\Collection;
use Traversable;

final class CollectionHelper {
    public function determineGenericCollectionTypeFromType(Type $type): GenericObjectType {
        $keyType = TypeCombinator::union(new IntegerType(), new StringType());

        if ($type instanceof TypeWithClassName) {
            if (
                (new ObjectType(Traversable::class))->isSuperTypeOf($type)->yes()
                || (new ObjectType(IteratorAggregate::class))->isSuperTypeOf($type)->yes()
                || (new ObjectType(Iterator::class))->isSuperTypeOf($type)->yes()
            ) {
                return $this->getTypeFromIterator($type);
            }
        }

        if (!$type->isArray()->yes()) {
            return new GenericObjectType(Collection::class, [$type->toArray()->getIterableKeyType(), $type->toArray()->getIterableValueType()]);
        }

        if ($type->isIterableAtLeastOnce()->no()) {
            return new GenericObjectType(Collection::class, [$keyType, new MixedType()]);
        }

        return new GenericObjectType(Collection::class, [
            TypeUtils::generalizeType($type->getIterableKeyType(), GeneralizePrecision::lessSpecific()),
            TypeUtils::generalizeType($type->getIterableValueType(), GeneralizePrecision::lessSpecific()),
        ]);
    }

    private function getTypeFromIterator(TypeWithClassName $valueType): GenericObjectType {
        $keyType = TypeCombinator::union(new IntegerType(), new StringType());

        $classReflection = $valueType->getClassReflection();

        if ($classReflection === null) {
            return new GenericObjectType(Collection::class, [$keyType, new MixedType()]);
        }

        $templateTypes = array_values($classReflection->getActiveTemplateTypeMap()->getTypes());

        if (count($templateTypes) === 1) {
            return new GenericObjectType(Collection::class, [$keyType, $templateTypes[0]]);
        }

        return new GenericObjectType(Collection::class, $templateTypes);
    }
}
