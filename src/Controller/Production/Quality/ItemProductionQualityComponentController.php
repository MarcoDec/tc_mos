<?php

namespace App\Controller\Production\Quality;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Production\Quality\ItemProductionQualityComponentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemProductionQualityComponentController
{
    public function __construct(private readonly EntityManagerInterface $em, private ItemProductionQualityComponentRepository $repository, private LoggerInterface $logger) {
    }

   /**
    * @param Request $request
    * @return array
    * @throws \ReflectionException
    */
    public function __invoke(Request $request): array{
        $tab = [
            'id' => $request->get('id'),
            'creeLe' => $request->get('creeLe'),
            'detectePar' => $request->get('detectePar'),
            'ref' => $request->get('ref'),
            'description' => $request->get('description'),
            'responsable' => $request->get('responsable'),
            'localisation' => $request->get('localisation'),
            'societe' => $request->get('societe'),
            'progressionCode' => $request->get('progressionCode'),
            'progressionValue' => $request->get('progressionValue'),
            'statut' => $request->get('statut'),
            'currentPage' => $request->get('page')
        ];
        
        $itemId = $request->get('api');
        $currentPage = $request->get('page');

        $sourceItem = $this->repository->findByQualityComponentId($itemId, $tab);
        $entityStr = 'componentItem';    
        return $sourceItem;
    }
}