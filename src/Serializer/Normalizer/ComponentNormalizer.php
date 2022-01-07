<?php

namespace App\Serializer\Normalizer;

use App\Entity\Purchase\Component\Component;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ComponentNormalizer implements CacheableSupportsMethodInterface, NormalizerInterface {
    public function __construct(private ObjectNormalizer $normalizer, private RequestStack $requestStack) {
    }

    public function hasCacheableSupportsMethod(): bool {
        return true;
    }

    /**
     * @return mixed[]
     */
    public function normalize($object, $format = null, array $context = []): array {
        $data = $this->normalizer->normalize($object, $format, $context);

        $request = $this->requestStack->getCurrentRequest();
        $process = null;

        if (null !== $request) {
            $process = $request->attributes->get('process');

            if ($process && is_array($data)) {
                $returnedData = [];

                switch ($process) {
                    case 'admin':
                        $returnedData = [
                            'family' => $data['family'] ?? null,
                            'name' => $data['name'] ?? null,
                            'index' => $data['index'] ?? null,
                            'ref' => $data['ref'] ?? null
                        ];
                        break;
                    case 'quality':
                        $returnedData = [
                            'ppmRate' => $data['ppmRate'] ?? null,
                        ];
                        break;
                    case 'purchase':
                        $returnedData = [
                            'manufacturer' => $data['manufacturer'] ?? null,
                        ];
                        break;
                    case 'logistics':
                        $returnedData = [
                            'customsCode' => $data['customsCode'] ?? null,
                            'weight' => $data['weight'] ?? null,
                            'unit' => $data['unit'] ?? null,
                            'forecastVolume' => $data['forecastVolume'] ?? null,
                            'minStock' => $data['minStock'] ?? null,
                            'managedStock' => $data['managedStock'] ?? null,
                        ];
                        break;
                    case 'price':
                        $returnedData = [
                            'copperWeight' => $data['copperWeight'] ?? null,
                        ];
                        break;
                    default:
                        break;
                }

                return $returnedData;
            }
        }

        return is_array($data) ? $data : [];
    }

    public function supportsNormalization($data, $format = null): bool {
        return $data instanceof Component;
    }
}
