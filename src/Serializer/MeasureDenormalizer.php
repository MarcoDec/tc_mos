<?php

namespace App\Serializer;

use App\Entity\Embeddable\Measure;
use App\Service\MeasureHydrator;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class MeasureDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface {
    use DenormalizerAwareTrait;

    public function __construct(private readonly MeasureHydrator $hydrator) {
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): Measure {
        $context[self::class] = true;
        /** @var Measure $measure */
        $measure = $this->denormalizer->denormalize($data, $type, $format, $context);
        $measure = $this->hydrator->hydrateUnit($measure);
        return $this->hydrator->hydrateCurrency($measure);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool {
        return !isset($context[self::class]) && $type === Measure::class;
    }
}
