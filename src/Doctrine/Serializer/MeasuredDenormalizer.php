<?php

namespace App\Doctrine\Serializer;

use App\Doctrine\Entity\Interfaces\MeasuredInterface;
use App\Doctrine\Service\MeasureHydrator;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class MeasuredDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface {
    use DenormalizerAwareTrait;

    public function __construct(private MeasureHydrator $hydrator) {
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): MeasuredInterface {
        $context[__CLASS__] = true;
        /** @var MeasuredInterface $entity */
        $entity = $this->denormalizer->denormalize($data, $type, $format, $context);
        return $this->hydrator->hydrateIn($entity);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool {
        return !isset($context[__CLASS__]) && $type === MeasuredInterface::class;
    }
}
