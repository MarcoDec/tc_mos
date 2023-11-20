<?php

namespace App\Controller\Logistics\Stock;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Logistics\Stock\StockComponentSumQuantity;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ItemComponentStockQuantiteSumController

{
    public function __construct(private readonly EntityManagerInterface $em, private StockComponentSumQuantity $repository, private LoggerInterface $logger) {
    }

   /**
    * @param Request $request
    * @return array
    * @throws \ReflectionException
    */
    public function __invoke(Request $request): array{
        $tab = [
            'item' => $request->get('item'),
            'name' => $request->get('name'),
            'jail' => $request->get('jail'),
            'quantiteValue' => $request->get('quantiteValue'),
            'quantiteCode' => $request->get('quantiteCode'),
            'currentPage' => $request->get('page'),
        ]; 
        
        $itemId = $request->get('api');
        $currentPage = $request->get('page');
        $sourceItem = $this->repository->findByStockComponentId($itemId, $tab);
        $entityStr = 'componentItem';    
        return $sourceItem;
    }
}