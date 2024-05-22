<?php

namespace App\Serializer;

use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Project\Product\Product;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class MeasuredInterfaceDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface {
    use DenormalizerAwareTrait;

    public function __construct(private readonly MeasureDenormalizer $measureDenormalizer) {
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): MeasuredInterface {
        $context[self::class] = true;
        dump([$data, $type, $format, $context]);
        /** @var MeasuredInterface $measuredInterface */
        $measuredInterface = $this->denormalizer->denormalize($data, $type, $format, $context);
        dump($measuredInterface);
        return new Product();
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool {
        return $type === MeasuredInterface::class;
    }
}