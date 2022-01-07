<?php

namespace App\Serializer\Normalizer;

use App\Entity\Selling\Customer\Customer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CompanyNormalmizer implements CacheableSupportsMethodInterface, NormalizerInterface {
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
                            'notes' => $data['notes'] ?? null,
                        ];
                        break;
                    case 'quality':
                        $returnedData = [
                            'ppmRate' => $data['ppmRate'] ?? null,
                            'qualityPortal' => $data['qualityPortal'] ?? null
                        ];
                        break;
                    case 'logistics':
                        $returnedData = [
                            'nbDelivery' => $data['nbDelivery'] ?? null,
                            'incoterms' => $data['incoterms'] ?? null,
                            'conveyanceDuration' => $data['conveyanceDuration'] ?? null,
                            'outstandingMax' => $data['outstandingMax'] ?? null,
                            'orderMin' => $data['orderMin'] ?? null,
                            'logisticsPortal' => $data['logisticsPortal'] ?? null,
                        ];
                        break;
                    case 'logistics':
                        $returnedData = [
                            'accountingAccount' => $data['accountingAccount'] ?? null,
                            'paymentTerms' => $data['paymentTerms'] ?? null,
                            'forceVat' => $data['forceVat'] ?? null,
                            'vat' => $data['vat'] ?? null,
                            'vatMessage' => $data['vatMessage'] ?? null,
                            'nbInvoices' => $data['nbInvoices'] ?? null,
                            'invoiceMin' => $data['invoiceMin'] ?? null,
                            'accountingPortal' => $data['accountingPortal'] ?? null,
                            'invoiceByEmail' => $data['invoiceByEmail'] ?? null,
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
        return $data instanceof Customer;
    }
}
