<?php

declare(strict_types=1);

namespace App\PhpStan\ReturnTypes;

use App\PhpStan\Support\CollectionHelper;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Type;
use Tightenco\Collect\Support\Collection;

final class CollectionMakeDynamicStaticMethodReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension {
    public function __construct(private CollectionHelper $collectionHelper) {
    }

    public function getClass(): string {
        return Collection::class;
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type {
        if (count($methodCall->getArgs()) < 1) {
            return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        }

        $valueType = $scope->getType($methodCall->getArgs()[0]->value);

        return $this->collectionHelper->determineGenericCollectionTypeFromType($valueType);
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool {
        return $methodReflection->getName() === 'make';
    }
}
