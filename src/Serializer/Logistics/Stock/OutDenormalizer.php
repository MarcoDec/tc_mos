<?php

namespace App\Serializer\Logistics\Stock;

use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Logistics\Stock\Stock;
use App\Service\MeasureHydrator;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class OutDenormalizer implements DenormalizerAwareInterface, DenormalizerInterface {
    use DenormalizerAwareTrait;

    public function __construct(private readonly MeasureHydrator $hydrator) {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []) {
        /** @var Stock<MeasuredInterface> $stock */
        $stock = $context['object_to_populate'];
        $this->hydrator->hydrateIn($stock);
        /** @var Measure $out */
        $out = $this->denormalizer->denormalize($data, Measure::class, $format);
        return $stock->substract($this->hydrator->hydrate($out));
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool {
        return is_array($data) && isset($data['code'], $data['value']) && $type === Stock::class;
    }
}
