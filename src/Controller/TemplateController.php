<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\TemplateController as SymfonyTemplateController;
use Symfony\Component\HttpFoundation\Response;
use voku\helper\HtmlMin;

final class TemplateController extends SymfonyTemplateController {
    public function __invoke(string $template, ?int $maxAge = null, ?int $sharedAge = null, ?bool $private = null, array $context = [], int $statusCode = 200): Response {
        return ($response = parent::__invoke($template, $maxAge, $sharedAge, $private, $context, $statusCode))
            ->setContent((new HtmlMin())->minify($response->getContent()).'</body></html>');
    }
}
