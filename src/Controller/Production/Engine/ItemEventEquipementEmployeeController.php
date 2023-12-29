<?php

namespace App\Controller\Production\Engine;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Production\Engine\ItemEventEquipementEmployeeRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemEventEquipementEmployeeController
{
    public function __construct(private readonly EntityManagerInterface $em, private ItemEventEquipementEmployeeRepository $repository, private LoggerInterface $logger) {
    }

   /**
    * @param Request $request
    * @return array
    * @throws \ReflectionException
    */
    public function __invoke(Request $request): array{
        $tab = [
            'id' => $request->get('id'),
            'name' => $request->get('name'),
            'surname' => $request->get('surname'),
            'currentPage' => $request->get('page')
        ];
        
        $itemId = $request->get('api');
        $currentPage = $request->get('page');

        $sourceItem = $this->repository->findByEmployeeEngineId($itemId, $tab);
        $entityStr = 'equipementItem';    
        return $sourceItem;
    }
}