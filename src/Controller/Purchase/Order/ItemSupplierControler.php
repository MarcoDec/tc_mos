<?php

namespace App\Controller\Purchase\Order;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Purchase\Order\ComponentItemRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ItemSupplierControler
{
    public function __construct(private readonly EntityManagerInterface $em, private ComponentItemRepository $repository, private LoggerInterface $logger) {
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
            'currentPage' => $request->get('page'),
            'note' => $request->get('note'),
            'retard' => $request->get('retard'),
            'ref' => $request->get('ref'),
            'prixCode' => $request->get('prixCode'),
            'prixValue' => $request->get('prixValue'),
            'notePo' => $request->get('infoPublic'),
            'embState' => $request->get('statutFournisseur'),
            'supplementFret' => $request->get('supplementFret'),
            'refOrder' => $request->get('refOrder')
        ]; 
        dump($tab);
        
        $itemId = $request->get('api');
        $currentPage = $request->get('page');

        $sourceItem = $this->repository->findBySupplierId($itemId, $tab);
        dump($sourceItem);
        $entityStr = 'componentItem';

        return $sourceItem;
    }
}