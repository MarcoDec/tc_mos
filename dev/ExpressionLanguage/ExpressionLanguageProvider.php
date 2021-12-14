<?php

namespace App\ExpressionLanguage;

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
            )
        ];
    }
}
