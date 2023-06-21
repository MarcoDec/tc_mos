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

        $tab = []; 
        
        $itemId = $request->get('api');
        $input = $request->request->all();
        $content = $request->getContent();
        $currentPage = $request->get('page');
        dump($currentPage);

        // var_dump($input);
        dump($content);

        $sourceItem = $this->repository->findBySupplierId($itemId, $currentPage);
        dump($sourceItem);
        $entityStr = 'componentItem';
  
        // if (empty($sourceItem)) {
        //    throw new NotFoundHttpException();
        // }
        // dump($sourceItem);
        // $sourceItem = $this->repository->find($itemId);
        // $content = json_decode($sourceItem);
        // var_dump($a);
        // if (empty($sourceItem)) {
        //    throw new NotFoundHttpException();
        // }
        // $refClass = new \ReflectionClass($sourceItem);

        // foreach ($sourceItem as &$element) {
        //     dump($element);
        //     $id = $element.getId();
        //     $element = array_merge($element, ['@id' => $id]);
        //     dump($element);
        // }

        // dump($sourceItem);
        return $sourceItem;
    }
}