<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Serializer\OpenApiNormalizer as BaseOpenApiNormalizer;
use App\Collection;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @phpstan-type Operation array<string, array{tags: non-empty-array<string>}>
 * @phpstan-type Normalized array{paths: array<string, Operation>}
 */
class OpenApiNormalizer implements CacheableSupportsMethodInterface, NormalizerInterface {
    public function __construct(private readonly BaseOpenApiNormalizer $wrapped) {
    }

    /** @param Operation $operations */
    private static function getTag(array $operations): string {
        return $operations[array_key_first($operations)]['tags'][0];
    }

    public function hasCacheableSupportsMethod(): bool {
        return $this->wrapped->hasCacheableSupportsMethod();
    }

    /** @return Normalized */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array {
        /** @var Normalized $normalized */
        $normalized = $this->wrapped->normalize($object, $format, $context);
        $normalized['paths'] = (new Collection($normalized['paths']))
            ->mapKeys(static fn (string $path, array $operations): string => self::getTag($operations).$path)
            ->sortKeys()
            ->mapKeys(static fn (string $path, array $operations): string => mb_substr($path, mb_strlen(self::getTag($operations))))
            ->toArray();
        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool {
        return $this->wrapped->supportsNormalization($data, $format);
    }
}
