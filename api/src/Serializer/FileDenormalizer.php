<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class FileDenormalizer implements DenormalizerInterface {
    /** @param UploadedFile $data */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): UploadedFile {
        return $data;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool {
        return $data instanceof UploadedFile;
    }
}
