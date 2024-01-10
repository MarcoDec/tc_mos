<?php

namespace App\Controller\Production\Engine;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Production\Engine\ItemEventEngineDeleteRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemEventEngineDeleteController
{
    public function __construct(private readonly EntityManagerInterface $em, private ItemEventEngineDeleteRepository $repository, private LoggerInterface $logger) {
    }

   /**
    * @param Request $request
    * @return array
    * @throws \ReflectionException
    */
    public function __invoke(Request $request): array{
        $tab = [
            'id' => $request->get('id'),
        ];
        
        $itemId = $request->get('api');
        $currentPage = $request->get('page');

        $sourceItem = $this->repository->findByEventEngineDeleteId($itemId, $tab);
        $entityStr = 'deleteEquipementItem';    
        return $sourceItem;
    }
}