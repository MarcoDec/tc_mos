<?php

namespace App\Doctrine\Serializer;

use App\Doctrine\Entity\Embeddable\Measure;
use App\Doctrine\Service\MeasureHydrator;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class MeasureDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface {
    use DenormalizerAwareTrait;

    public function __construct(private MeasureHydrator $hydrator) {
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): Measure {
        $context[__CLASS__] = true;
        /** @var Measure $measure */
        $measure = $this->denormalizer->denormalize($data, $type, $format, $context);
        return $this->hydrator->hydrate($measure);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool {
        return !isset($context[__CLASS__]) && $type === Measure::class;
    }
}
