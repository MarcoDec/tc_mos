<?php

namespace App\Controller\Purchase\Order;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use ProductItemRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ItemProductControler
{
    public function __construct(private readonly EntityManagerInterface $em, private ProductItemRepository $repository, private LoggerInterface $logger) {
    }

   /**
    * @param Request $request
    * @return array
    * @throws \ReflectionException
    */
    public function __invoke(Request $request): array{
        $tab = [
            'item' => $request->get('item'),
            'confQuantityCode' => $request->get('confirmedQuantityCode'),
            'confQuantityValue' => $request->get('confirmedQuantityValue'),
            'reqQuantityCode' => $request->get('requestedQuantityCode'),
            'reqQuantityValue' => $request->get('requestedQuantityValue'),
            'confDate' => $request->get('confirmedDate'),
            'reqDate' => $request->get('requestedDate'),
            'currentPage' => $request->get('currentPage'),
            'note' => $request->get('note'),
            'retard' => $request->get('retard'),
            'ref' => $request->get('ref'),
            'prixCode' => $request->get('prixCode'),
            'prixValue' => $request->get('prixValue')
        ];
        dump($tab);
        
        $itemId = $request->get('api');
        $currentPage = $request->get('page');

        $sourceItem = $this->repository->findBySupplierId($itemId, $tab);
        // dump($sourceItem);
        $entityStr = 'componentItem';
        return $sourceItem;
    }
}