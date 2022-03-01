<?php

namespace App\Doctrine\Serializer;

use App\Doctrine\Entity\Interfaces\FileEntity;
use App\Filesystem\FileManager;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

final class FileEntityNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface {
    use NormalizerAwareTrait;

    private const CALLED = 'FILE_ENTITY_NORMALIZER_CALLED_FOR';

    public function __construct(private FileManager $fm) {
    }

    /**
     * @param FileEntity $object
     *
     * @return mixed[]
     */
    public function normalize($object, ?string $format = null, array $context = []): array {
        if (!isset($context[self::CALLED])) {
            $context[self::CALLED] = [];
        }
        $class = get_class($object);
        if (!isset($context[self::CALLED][$class])) {
            $context[self::CALLED][$class] = [];
        }
        $context[self::CALLED][$class][] = $object->getId();

        $normalized = $this->normalizer->normalize($object, $format, $context);
        if (!is_array($normalized)) {
            throw new LogicException(sprintf('Unexpected value. Require array, get %s.', gettype($normalized)));
        }
        $normalized['filepath'] = $this->fm->normalizePath($normalized['filepath']);
        return $normalized;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool {
        if ($format !== 'jsonld' || !($data instanceof FileEntity)) {
            return false;
        }

        if (!isset($context[self::CALLED]) || empty($context[self::CALLED])) {
            return true;
        }

        $class = get_class($data);
        if (!isset($context[self::CALLED][$class]) || empty($context[self::CALLED][$class])) {
            return true;
        }

        return !in_array($data->getId(), $context[self::CALLED][$class]);
    }
}
