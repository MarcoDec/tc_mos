<?php

namespace App\Serializer;

use App\Entity\Interfaces\MeasuredInterface;
use App\Service\MeasureHydrator;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class MeasuredDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface {
    use DenormalizerAwareTrait;

    public function __construct(private readonly MeasureHydrator $hydrator) {
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): MeasuredInterface {
        $context[self::class] = true;
        /** @var MeasuredInterface $entity */
        $entity = $this->denormalizer->denormalize($data, $type, $format, $context);
        return $this->hydrator->hydrateIn($entity);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool {
        return !isset($context[self::class]) && $type === MeasuredInterface::class;
    }
}
