<?php

namespace App\ExpressionLanguage;

use App\Doctrine\DBAL\Types\Project\Product\CurrentPlaceType;
use App\Fixtures\Configurations;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

final class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface {
    public function __construct(private Configurations $configurations) {
    }

    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions(): array {
        return [
            new ExpressionFunction(
                name: 'component_family_code',
                compiler: static fn (string $name): string => sprintf('strtoupper(substr(%1$s, 0, 3))', $name),
                evaluator: static fn (array $args, string $name): string => strtoupper(substr($name, 0, 3))
            ),
            new ExpressionFunction(
                name: 'component_subfamily_code',
                compiler: static fn (int $id): string => sprintf('strtoupper(substr($this->configurations->findData(\'component_family\', %1$d)[\'family_name\'], 0, 3))', $id),
                evaluator: fn (array $args, int $id): string => strtoupper(substr($this->configurations->findData('component_family', $id)['code'], 0, 3))
            ),
            new ExpressionFunction(
                name: 'engine_group',
                compiler: static fn (int $id): string => sprintf('%1$d == 1 ? \'workstation\' : \'tool\'', $id),
                evaluator: static fn (array $args, int $id): string => $id == 1 ? 'workstation' : 'tool'
            ),
            ExpressionFunction::fromPhp('floatval'),
            new ExpressionFunction(
                name: 'product_parent',
                compiler: static fn (int $id): string => sprintf(<<<'FUNCTION'
$this->configurations->findEntities('product')->first(static fn (Collection $entity): bool => $entity['id_product_child'] == %s)
FUNCTION, $id),
                evaluator: fn (array $args, int $id): int => collect($this->configurations->findEntities('product'))
                    ->first(static fn (array $entity): bool => $entity['id_product_child'] == $id, ['id' => 0])['id']
            ),
            new ExpressionFunction(
                name: 'product_workflow',
                compiler: static fn (int $id): string => sprintf(<<<'FUNCTION'
match (%s) {
    2 => CurrentPlaceType::TYPE_TO_VALIDATE,
    3 => CurrentPlaceType::TYPE_AGREED,
    4 => CurrentPlaceType::TYPE_UNDER_EXEMPTION,
    5 => CurrentPlaceType::TYPE_BLOCKED,
    6 => CurrentPlaceType::TYPE_DISABLED,
    default => CurrentPlaceType::TYPE_DRAFT
}
FUNCTION, $id),
                evaluator: static fn (array $args, int $id): string => match ($id) {
                    2 => CurrentPlaceType::TYPE_TO_VALIDATE,
                    3 => CurrentPlaceType::TYPE_AGREED,
                    4 => CurrentPlaceType::TYPE_UNDER_EXEMPTION,
                    5 => CurrentPlaceType::TYPE_BLOCKED,
                    6 => CurrentPlaceType::TYPE_DISABLED,
                    default => CurrentPlaceType::TYPE_DRAFT
                }
            )
        ];
    }
}
