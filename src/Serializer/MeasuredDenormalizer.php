<?php

namespace App\Serializer;

use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Service\MeasureHydrator;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

final class MeasuredDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface {
    use DenormalizerAwareTrait;

    public function __construct(private readonly MeasureHydrator $hydrator) {
    }

    /**
     * @throws ExceptionInterface
     * @throws \ReflectionException
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): MeasuredInterface {
        $context[self::class] = true;
        /** @var MeasuredInterface $entity */
        $entity = $this->denormalizer->denormalize($data, $type, $format, $context);
        $reflectionClass = new \ReflectionClass($entity);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
//            dump(['property' => $property->getName()]);
            $property->setAccessible(true);
            $value = $property->getValue($entity);
            if ($value instanceof Measure) {
                $propertyName = $property->getName();
//                dump("Value is Measure ($propertyName)");
                if (isset($data[$propertyName])) {
//                    dump("Data has $propertyName");
                    $value->setCode($data[$propertyName]['code']);
                    $value->setValue($data[$propertyName]['value']);
                }
            }
        }
//        dump(['entity before hydrateIn' => $entity]);
        return $this->hydrator->hydrateIn($entity);
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool {
        //On vérifie que la classe $type est une instance de MeasuredInterface
        $isInstanceOfMeasuredInterface = (new $type) instanceof MeasuredInterface;
        if (!isset($context[self::class]) && $isInstanceOfMeasuredInterface) return true;
        return false;
    }
}
