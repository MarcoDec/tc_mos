<?php

namespace App\OpenApi;

use Illuminate\Support\Collection;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
        $hidden = new Collection();
        /** @var Collection<string, Collection<string, mixed>> $sorted */
        $sorted = new Collection(['hidden' => $hidden]);
        foreach ($paths as $path => $item) {
            $tag = 'hidden';
            foreach (self::METHODS as $method) {
                if (!isset($item[$method]) || empty($operation = $item[$method])) {
                    continue;
                }

                if (isset($operation['parameters']) && !empty($operation['parameters'])) {
                    /** @var mixed[] $parameters */
                    $parameters = $operation['parameters'];
                    $operation['parameters'] = collect($parameters)->sortBy('name')->values()->all();
                }

                $item[$method] = $operation;
                $tag = $operation['tags'][0];
            }
            if (!$sorted->offsetExists($tag)) {
                /** @var Collection<string, mixed> $empty */
                $empty = new Collection();
                $sorted->put($tag, $empty);
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
        /** @var array{components: array{schemas: array<string, mixed>}, paths: mixed[]} $normalized */
        $normalized = $this->decorated->normalize($object, $format, $context);
        $normalized['components']['schemas'] = collect($normalized['components']['schemas'])->sortKeys()->all();
        $normalized['paths'] = self::sortPaths($normalized['paths']);
        return $normalized;
    }

    public function supportsNormalization($data, ?string $format = null): bool {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
