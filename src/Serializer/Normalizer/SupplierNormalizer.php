<?php

namespace App\Serializer\Normalizer;

use App\Entity\Purchase\Supplier\Supplier;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SupplierNormalizer implements CacheableSupportsMethodInterface, NormalizerInterface {
    public function __construct(private ObjectNormalizer $normalizer, private RequestStack $requestStack) {
    }

    public function hasCacheableSupportsMethod(): bool {
        return true;
    }

    /**
     * @param Supplier $object
     *
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
                            'notes' => $data['notes'] ?? null,
                            'managedProduction' => $data['managedProduction'] ?? null,
                        ];
                        break;
                    case 'quality':
                        $returnedData = [
                            'managedQuality' => $data['managedQuality'] ?? null,
                            'confidenceCriteria' => $data['confidenceCriteria'] ?? null,
                            'ppmRate' => $data['ppmRate'] ?? null,
                        ];
                        break;
                    case 'purchase-logistics':
                        $returnedData = [
                            'orderMin' => $data['orderMin'] ?? null,
                            'incoterms' => $data['incoterms'] ?? null,
                            'copper' => $data['copper'] ?? null,
                            'confidenceCriteria' => $data['confidenceCriteria'] ?? null,
                            'ar' => $data['ar'] ?? null,
                        ];
                        break;
                    case 'accounting':
                        $returnedData = [
                            'invoiceMin' => $data['invoiceMin'] ?? null,
                            'vat' => $data['vat'] ?? null,
                            'currency' => $data['currency'] ?? null,
                            'vatMessage' => $data['vatMessage'] ?? null,
                            'accountingAccount' => $data['accountingAccount'] ?? null,
                            'forceVat' => $data['forceVat'] ?? null,
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
        return $data instanceof Supplier;
    }
}
