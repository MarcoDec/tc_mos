<?php

namespace App\OpenApi;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class OpenApiNormalizer implements NormalizerInterface {
    public function __construct(private readonly NormalizerInterface $decorated) {
    }

    /**
     * @param mixed[] $array
     *
     * @return mixed[]
     */
    private static function map(array $array): array {
        $filtered = [];
        foreach ($array as $key => $data) {
            if (is_array($data)) {
                if (!empty($data = self::map($data))) {
                    $filtered[$key] = $data;
                }
            } else {
                $filtered[$key] = $data;
            }
        }
        ksort($filtered);
        return $filtered;
    }

    /**
     * @return mixed[]
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array {
        /** @var mixed[] $normalized */
        $normalized = $this->decorated->normalize($object, $format, $context);
        return self::map($normalized);
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool {
        return $this->decorated->supportsNormalization($data, $format);
    }
}
