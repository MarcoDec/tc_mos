<?php

namespace App\Controller\Manufacturing\Component;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Manufacturing\Component\ManufacturingComponentItemRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ItemManufacturingComponentController
{
    public function __construct(private readonly EntityManagerInterface $em, private ManufacturingComponentItemRepository $repository, private LoggerInterface $logger) {
    }

   /**
    * @param Request $request
    * @return array
    * @throws \ReflectionException
    */
    public function __invoke(Request $request): array{
        $tab = [
            'item' => $request->get('item'),
            'ref' => $request->get('productOF'),
            'name' => $request->get('name'),
            'date' => $request->get('date'),
            'forecastVolumeValue' => $request->get('forecastVolumeValue'),
            'forecastVolumeCode' => $request->get('forecastVolumeCode'),
            'currentPage' => $request->get('page'),
        ]; 
        
        $itemId = $request->get('api');
        $currentPage = $request->get('page');

        $sourceItem = $this->repository->findByManufacturingComponentId($itemId, $tab);
        $entityStr = 'componentItem';    
        return $sourceItem;
    }
}