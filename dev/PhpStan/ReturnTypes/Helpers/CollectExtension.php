<?php

declare(strict_types=1);

namespace App\PhpStan\ReturnTypes\Helpers;

use App\PhpStan\Support\CollectionHelper;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\Type;

final class CollectExtension implements DynamicFunctionReturnTypeExtension {
    public function __construct(private CollectionHelper $collectionHelper) {
    }

    public function getTypeFromFunctionCall(FunctionReflection $functionReflection, FuncCall $functionCall, Scope $scope): Type {
        if (count($functionCall->getArgs()) < 1) {
            return ParametersAcceptorSelector::selectSingle($functionReflection->getVariants())->getReturnType();
        }

        $valueType = $scope->getType($functionCall->getArgs()[0]->value);

        return $this->collectionHelper->determineGenericCollectionTypeFromType($valueType);
    }

    public function isFunctionSupported(FunctionReflection $functionReflection): bool {
        return $functionReflection->getName() === 'collect';
    }
}
