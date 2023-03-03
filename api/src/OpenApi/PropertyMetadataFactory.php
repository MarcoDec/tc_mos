<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Util\CachedTrait;
use Psr\Cache\CacheItemPoolInterface;
use ReflectionClass;

class PropertyMetadataFactory implements PropertyMetadataFactoryInterface {
    use CachedTrait;

    /** @var string */
    final public const CACHE_KEY_PREFIX = 'property_metadata_';

    public function __construct(
        private readonly PropertyMetadataFactoryInterface $wrapped,
        CacheItemPoolInterface $cacheItemPool
    ) {
        $this->cacheItemPool = $cacheItemPool;
    }

    /**
     * @param class-string $resourceClass
     * @param mixed[]      $options
     */
    public function create(string $resourceClass, string $property, array $options = []): ApiProperty {
        $cacheKey = self::CACHE_KEY_PREFIX.md5(serialize([$resourceClass, $property, $options]));
        /* @phpstan-ignore-next-line */
        return $this->getCached($cacheKey, function () use ($resourceClass, $property, $options): ApiProperty {
            $metadata = $this->wrapped->create($resourceClass, $property, $options);
            $refl = new ReflectionClass($resourceClass);
            $reflections = [];
            if ($refl->hasProperty($property)) {
                $reflections[] = $refl->getProperty($property);
            }
            $ucfProperty = ucfirst($property);
            if ($refl->hasMethod($get = "get$ucfProperty")) {
                $reflections[] = $refl->getMethod($get);
            }
            if ($refl->hasMethod($is = "is$ucfProperty")) {
                $reflections[] = $refl->getMethod($is);
            }
            if ($refl->hasMethod($set = "set$ucfProperty")) {
                $reflections[] = $refl->getMethod($set);
            }
            foreach ($reflections as $reflection) {
                foreach ($reflection->getAttributes(ApiProperty::class) as $attribute) {
                    /** @var ApiProperty $instance */
                    $instance = $attribute->newInstance();
                    if ($instance->isRequired() === true) {
                        $metadata = $metadata->withRequired(true);
                    }
                }
            }
            return $metadata;
        });
    }
}
