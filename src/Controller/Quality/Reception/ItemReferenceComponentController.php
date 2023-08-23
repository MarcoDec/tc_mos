<?php

namespace App\Controller\Quality\Reception;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Quality\Reception\ItemReferenceComponentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ItemReferenceComponentController
{
    public function __construct(private readonly EntityManagerInterface $em, private ItemReferenceComponentRepository $repository, private LoggerInterface $logger) {
    }

   /**
    * @param Request $request
    * @return array
    * @throws \ReflectionException
    */
    public function __invoke(Request $request): array{
        $tab = [
            'item' => $request->get('item'),
            'kind' => $request->get('kind'),
            'name' => $request->get('name'),
            'sampleQuantity' => $request->get('sampleQuantity'),
            'minCode' => $request->get('minCode'),
            'minValue' => $request->get('minValue'),
            'maxValeur' => $request->get('maxValue'),
            'maxCode' => $request->get('maxCode'),
            'currentPage' => $request->get('page'),
        ]; 
        
        $itemId = $request->get('api');
        $currentPage = $request->get('page');

        $sourceItem = $this->repository->findByReferenceComponentId($itemId, $tab);
        $entityStr = 'componentItem';    
        return $sourceItem;
    }
}