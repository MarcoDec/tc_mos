<?php

namespace App\Controller\Production\Engine;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Production\Engine\ItemEventEnginePlanningRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemEventEnginePlanningController
{
    public function __construct(private readonly EntityManagerInterface $em, private ItemEventEnginePlanningRepository $repository, private LoggerInterface $logger) {
    }

   /**
    * @param Request $request
    * @return array
    * @throws \ReflectionException
    */
    public function __invoke(Request $request): array{
        $tab = [
            'id' => $request->get('id'),
            'date' => $request->get('date'),
            'name' => $request->get('name'),
            'type' => $request->get('type'),
            'quantiteCode' => $request->get('quantiteCode'),
            'quantiteValue' => $request->get('quantiteValue'),
            'currentPage' => $request->get('page')
        ];
        
        $itemId = $request->get('api');
        $currentPage = $request->get('page');

        $sourceItem = $this->repository->findByEventEngineId($itemId, $tab);
        $entityStr = 'equipementItem';    
        return $sourceItem;
    }
}
