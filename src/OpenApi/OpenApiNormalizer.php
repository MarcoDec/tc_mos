<?php

namespace App\OpenApi;

use App\Collection;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @phpstan-type Paths array<string, array<'delete'|'get'|'patch'|'post', array{parameters?: mixed[], tags: string[]}>>
 */
final class OpenApiNormalizer implements NormalizerInterface {
    private const METHODS = ['delete', 'get', 'patch', 'post'];

    public function __construct(private readonly NormalizerInterface $decorated) {
    }

    /**
     * @param Paths $paths
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
                /** @phpstan-ignore-next-line */
                if (!isset($item[$method]) || empty($operation = $item[$method])) {
                    continue;
                }

                if (isset($operation['parameters']) && !empty($operation['parameters'])) {
                    /** @var mixed[] $parameters */
                    $parameters = $operation['parameters'];
                    $operation['parameters'] = Collection::collect($parameters)->sortBy('name')->all();
                }

                $item[$method] = $operation;
                $tag = $operation['tags'][0];
            }
            if (!$sorted->has($tag)) {
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
            ->flatten(1, true)
            ->all();
    }

    /**
     * @return mixed[]
     */
    public function normalize($object, ?string $format = null, array $context = []): array {
        /** @var array{components: array{schemas: array<string, mixed>}, paths: Paths} $normalized */
        $normalized = $this->decorated->normalize($object, $format, $context);
        $normalized['components']['schemas'] = Collection::collect($normalized['components']['schemas'])->sortKeys()->all();
        $normalized['paths'] = self::sortPaths($normalized['paths']);
        return $normalized;
    }

    public function supportsNormalization($data, ?string $format = null): bool {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
