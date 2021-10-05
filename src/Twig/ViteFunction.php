<?php

namespace App\Twig;

use Twig\TwigFunction;

final class ViteFunction {
    private TwigFunction $function;

    public function __construct(string $name, callable $callable) {
        $this->function = new TwigFunction($name, $callable, ['is_safe' => ['html'], 'needs_environment' => true]);
    }

    public function get(): TwigFunction {
        return $this->function;
    }
}
