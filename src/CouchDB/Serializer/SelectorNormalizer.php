<?php

namespace App\CouchDB\Serializer;

use App\CouchDB\Repository\Finder\Selector;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

final class SelectorNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface {
    use NormalizerAwareTrait;

    private const CALLED = 'SELECTOR_NORMALIZER_CALLED_FOR';

    public function normalize(mixed $object, ?string $format = null, array $context = []) {
        if (!isset($context[self::CALLED])) {
            $context[self::CALLED] = [];
        }
        $context[self::CALLED][] = $object;
        $normalized = $this->normalizer->normalize($object, $format, $context);
        if (!is_array($normalized)) {
            throw new LogicException(sprintf('Unexpected value. Require array, get %s.', gettype($normalized)));
        }
        return $normalized['selector'];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool {
        if ($format !== 'json' || !($data instanceof Selector)) {
            return false;
        }

        if (!isset($context[self::CALLED]) || empty($context[self::CALLED])) {
            return true;
        }

        return !in_array($data, $context[self::CALLED]);
    }
}
