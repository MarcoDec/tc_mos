<?php

namespace App\Controller\Logistics\Stock;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Logistics\Warehouse;
use App\Repository\Logistics\Stock\StockRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class StockController {
    /**
     * @param StockRepository<ComponentStock|ProductStock> $repo
     */
    public function __construct(
        private readonly IriConverterInterface $converter,
        private readonly int $itemPerPage,
        private readonly StockRepository $repo
    ) {
    }

    #[Route(
        path: '/api/stocks/grouped',
        name: 'api_stocks_grouped_collection',
        defaults: [
            '_api_collection_operation_name' => 'grouped',
            '_api_resource_class' => Stock::class
        ],
        methods: 'GET'
    )]
    public function grouped(Request $request): JsonResponse {
        $warehouseIri = $request->query->get('warehouse');
        if (empty($warehouseIri)) {
            throw new BadRequestHttpException('Missing warehouse filter.');
        }
        /** @var Warehouse $warehouse */
        $warehouse = $this->converter->getItemFromIri($warehouseIri);
        $location = $request->query->get('location');
        $count = $this->repo->countGrouped($warehouse, $location);
        $page = (int) ($request->query->get('page', '1'));
        $last = ceil($count / $this->itemPerPage);
        $hydraView = [
            '@id' => "/api/stocks/grouped?page=$page",
            'hydra:first' => '/api/stocks/grouped?page=1',
            'hydra:last' => "/api/stocks/grouped?page=$last"
        ];
        if ($page < $last) {
            $hydraView['hydra:next'] = '/api/stocks/grouped?page='.($page + 1);
        }
        if ($page > 1) {
            $hydraView['hydra:previous'] = '/api/stocks/grouped?page='.($page - 1);
        }
        return new JsonResponse([
            'hydra:member' => $this->repo->findGrouped(
                warehouse: $warehouse,
                limit: $this->itemPerPage,
                offset: ($page - 1) * $this->itemPerPage,
                location: $location
            ),
            'hydra:totalItems' => $count,
            'hydra:view' => $hydraView
        ]);
    }
}
