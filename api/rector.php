<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\CodingStyle\Rector\ClassMethod\OrderAttributesRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector;
use Rector\CodingStyle\Rector\Property\InlineSimplePropertyAnnotationRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Set\ValueObject\SetList;
use Rector\Strict\Rector\BooleanNot\BooleanInBooleanNotRuleFixerRector;
use Rector\Strict\Rector\If_\BooleanInIfConditionRuleFixerRector;
use Rector\Strict\Rector\Ternary\BooleanInTernaryOperatorRuleFixerRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return static function (RectorConfig $rector): void {
    $rector->paths([__DIR__.'/bin', __DIR__.'/config', __DIR__.'/dev', __DIR__.'/migrations', __DIR__.'/src']);
    $rector->phpVersion(PhpVersion::PHP_81);
    $rector->phpstanConfig(__DIR__.'/phpstan.neon.dist');
    $rector->sets([
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::PSR_4,
        SetList::PHP_52,
        SetList::PHP_53,
        SetList::PHP_54,
        SetList::PHP_55,
        SetList::PHP_56,
        SetList::PHP_70,
        SetList::PHP_71,
        SetList::PHP_72,
        SetList::PHP_73,
        SetList::PHP_74,
        SetList::PHP_80,
        SetList::PHP_81,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        SetList::TYPE_DECLARATION_STRICT
    ]);
    $rector->ruleWithConfiguration(
        rectorClass: ConsistentPregDelimiterRector::class,
        configuration: [ConsistentPregDelimiterRector::DELIMITER => '/']
    );
    $rector->ruleWithConfiguration(
        rectorClass: InlineSimplePropertyAnnotationRector::class,
        configuration: ['@var', '@phpstan-var', '@psalm-var']
    );
    $rector->ruleWithConfiguration(
        rectorClass: OrderAttributesRector::class,
        configuration: [OrderAttributesRector::ALPHABETICALLY]
    );
    $rector->ruleWithConfiguration(
        rectorClass: BooleanInBooleanNotRuleFixerRector::class,
        configuration: [BooleanInBooleanNotRuleFixerRector::TREAT_AS_NON_EMPTY => true]
    );
    $rector->ruleWithConfiguration(
        rectorClass: BooleanInIfConditionRuleFixerRector::class,
        configuration: [BooleanInIfConditionRuleFixerRector::TREAT_AS_NON_EMPTY => true]
    );
    $rector->ruleWithConfiguration(
        rectorClass: BooleanInTernaryOperatorRuleFixerRector::class,
        configuration: [BooleanInTernaryOperatorRuleFixerRector::TREAT_AS_NON_EMPTY => true]
    );
    $rector->ruleWithConfiguration(
        rectorClass: AddVoidReturnTypeWhereNoReturnRector::class,
        configuration: [AddVoidReturnTypeWhereNoReturnRector::USE_PHPDOC => false]
    );
    $rector->skip([
        // SetList::CODING_STYLE
        EncapsedStringsToSprintfRector::class,
        NewlineAfterStatementRector::class,
        NewlineBeforeNewAssignSetRector::class,
        SymplifyQuoteEscapeRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class,
        // SetList::PRIVATIZATION
        FinalizeClassesWithoutChildrenRector::class
    ]);
};
