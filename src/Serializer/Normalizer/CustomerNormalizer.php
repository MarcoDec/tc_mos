<?php

namespace App\Serializer\Normalizer;

use App\Entity\Management\Society\Company;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CustomerNormalizer implements CacheableSupportsMethodInterface, NormalizerInterface {
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
                            'name' => $data['name'] ?? null,
                        ];
                        break;
                    case 'main':
                        $returnedData = [
                            'address' => $data['address'] ?? null,
                        ];
                        break;
                    case 'selling':
                        $returnedData = [
                            'engineHourRate' => $data['engineHourRate'] ?? null,
                            'handlingHourRate' => $data['handlingHourRate'] ?? null,
                            'managementFees' => $data['managementFees'] ?? null,
                            'generalMargin' => $data['generalMargin'] ?? null,
                        ];
                        break;
                    case 'logistics':
                        $returnedData = [
                            'deliveryTime' => $data['deliveryTime'] ?? null,
                            'deliveryTimeOpenDays' => $data['deliveryTimeOpenDays'] ?? null,
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
        return $data instanceof Company;
    }
}
