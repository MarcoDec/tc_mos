<?php

namespace App\Serializer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class FileDenormalizer implements DenormalizerInterface {
    /**
     * @param File $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     * @return File
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): File
    {
        return $data;
    }

    /**
     * @param $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     * @return bool
     */
    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool {
        return $data instanceof File;
    }
}
