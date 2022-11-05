<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
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
            $post = $item->getPost();
            if (empty($post) === false) {
                $paths->addPath(
                    path: $path,
                    pathItem: $item->withPost(
                        $post->withResponses(
                            (new Collection($post->getResponses()))
                                ->filter(static fn (Response $response): bool => $response->getDescription() !== 'none')
                                ->toArray()
                        )
                    )
                );
            }
        }
        return $openapi->withPaths($paths);
    }
}
