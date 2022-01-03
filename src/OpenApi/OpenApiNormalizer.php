<?php

namespace App\OpenApi;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tightenco\Collect\Support\Collection;

final class OpenApiNormalizer implements NormalizerInterface {
    private const METHODS = ['delete', 'get', 'patch', 'post'];

    public function __construct(private NormalizerInterface $decorated) {
    }

    /**
     * @param mixed[] $paths
     *
     * @return mixed[]
     */
    private static function sortPaths(array $paths): array {
        $sorted = collect(['hidden' => collect()]);
        foreach ($paths as $path => $item) {
            $tag = 'hidden';
            foreach (self::METHODS as $method) {
                if (!isset($item[$method]) || empty($operation = $item[$method])) {
                    continue;
                }

                if (isset($operation['parameters']) && !empty($operation['parameters'])) {
                    $operation['parameters'] = collect($operation['parameters'])->sortBy('name')->values()->all();
                }

                $item[$method] = $operation;
                $tag = $operation['tags'][0];
            }
            if (!$sorted->offsetExists($tag)) {
                $sorted->put($tag, collect());
            }
            if (!empty($sortedItem = $sorted->get($tag))) {
                $sortedItem->put($path, $item);
            }
        }
        return $sorted
            ->map(static fn (Collection $paths): array => $paths->sortKeys()->all())
            ->sortKeys()
            ->collapse()
            ->all();
    }

    /**
     * @return mixed[]
     */
    public function normalize($object, ?string $format = null, array $context = []): array {
        /** @var mixed[] $normalized */
        $normalized = $this->decorated->normalize($object, $format, $context);
        $normalized['components']['schemas'] = collect($normalized['components']['schemas'])->sortKeys()->all();
        $normalized['paths'] = self::sortPaths($normalized['paths']);
        return $normalized;
    }

    public function supportsNormalization($data, ?string $format = null): bool {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
