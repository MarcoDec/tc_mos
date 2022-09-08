<?php

namespace App\DataProvider\Logistics\Stock;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Repository\Logistics\Stock\StockRepository;

final class TransferDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
    /**
     * @param StockRepository<Stock<Component|Product>> $repo
     */
    public function __construct(private readonly StockRepository $repo) {
    }

    /**
     * @param int     $id
     * @param mixed[] $context
     *
     * @return null|Stock<Component|Product>
     */
    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = []): ?Stock {
        return $this->repo->findTransfer($id);
    }

    /**
     * @param mixed[] $context
     */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool {
        return $resourceClass === Stock::class && $operationName === 'transfer';
    }
}
