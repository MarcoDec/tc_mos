<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Response;
use ApiPlatform\OpenApi\OpenApi;
use App\Collection;

class OpenApiFactory implements OpenApiFactoryInterface {
    public function __construct(private readonly OpenApiFactoryInterface $wrapped) {
    }

    /** @param array{base_url?: string} $context */
    public function __invoke(array $context = []): OpenApi {
        $openapi = $this->wrapped->__invoke($context);
        $paths = clone $openapi->getPaths();
        foreach ($openapi->getPaths()->getPaths() as $path => $item) {
            /** @var PathItem $item */
            $get = $item->getGet();
            if (empty($get) === false) {
                $item = $item->withGet($get->withParameters(
                    (new Collection($get->getParameters()))
                        ->map(static fn (Parameter $parameter): Parameter => $parameter->withAllowEmptyValue(false))
                        ->toArray()
                ));
            }
            $post = $item->getPost();
            if (empty($post) === false) {
                $item = $item->withPost($post->withResponses(
                    (new Collection($post->getResponses()))
                        ->filter(static fn (Response $response): bool => $response->getDescription() !== 'none')
                        ->toArray()
                ));
            }
            $paths->addPath($path, $item);
        }
        return $openapi->withPaths($paths);
    }
}
