<?php

namespace App\DataProvider\Logistics\Stock;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Logistics\Stock\Stock;
use Doctrine\ORM\EntityManagerInterface;

final class StockDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface {
    public function __construct(
        private readonly IriConverterInterface $iriConverter,
        private readonly EntityManagerInterface $em
    ) {
    }

    /**
     * @param array{filters?: array<string, mixed>} $context
     *
     * @return (ComponentStock|ProductStock)[]
     */
    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = []) {
        if (empty($context)) {
            return [];
        }
        if (isset($context['filters']['warehouse']) && is_string($context['filters']['warehouse'])) {
            $context['filters']['warehouse'] = $this->iriConverter->getItemFromIri($context['filters']['warehouse']);
        }
        return $this->em->getRepository(Stock::class)->findFetched($context['filters']);
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Stock::class && $operationName === 'get';
    }
}
