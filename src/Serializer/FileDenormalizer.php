<?php

namespace App\Serializer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class FileDenormalizer implements DenormalizerInterface {
    /**
     * @param File    $data
     * @param mixed[] $context
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): File {
        return $data;
    }

    /**
     * @param mixed $data
     */
    public function supportsDenormalization($data, string $type, ?string $format = null): bool {
        return $data instanceof File;
    }
}
