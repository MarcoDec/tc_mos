<?php

namespace App\Serializer\Logistics\Stock;

use App\Entity\Embeddable\Measure;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class OutDenormalizer implements DenormalizerAwareInterface, DenormalizerInterface {
    use DenormalizerAwareTrait;

    /**
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     * @return Stock
     * @throws ExceptionInterface
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Stock{
        /** @var Stock<Component|Product> $stock */
        $stock = $context['object_to_populate'];
        /** @var Measure $out */
        $out = $this->denormalizer->denormalize($data, Measure::class, $format);
        return $stock->substract($out);
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     * @return bool
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool {
        return is_array($data) && isset($data['code'], $data['value']) && $type === Stock::class;
    }
}
