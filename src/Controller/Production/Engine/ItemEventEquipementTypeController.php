<?php

namespace App\Controller\Production\Engine;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Production\Engine\ItemEventEquipementTypeRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemEventEquipementTypeController
{
    public function __construct(private readonly EntityManagerInterface $em, private ItemEventEquipementTypeRepository $repository, private LoggerInterface $logger) {
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
            'done' => $request->get('done'),
            'emergency' => $request->get('emergency'),
            'etat' => $request->get('etat'),
            'interventionNotes' => $request->get('interventionNotes'),
            'type' => $request->get('type'),
            'currentPage' => $request->get('page')
        ];
        
        $itemId = $request->get('api');
        $currentPage = $request->get('page');

        $sourceItem = $this->repository->findByEventEngineId($itemId, $tab);
        $entityStr = 'equipementItem';    
        return $sourceItem;
    }
}
