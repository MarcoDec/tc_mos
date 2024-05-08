<?php

namespace App\Controller\Manufacturing\Order\Needs;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Production\Manufacturing\Order;
use App\Repository\Selling\Order\OrderRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\Manufacturing\Order\ManufacturingOrderRepository;

class ManufacturingOrderNeedsController
{
    public function __construct(private EntityManagerInterface $em, private ManufacturingOrderRepository $orderRepository, private OrderRepository $sellingOrderRepository )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $route = $request->attributes->get('_route');
        switch ($route) {
            case 'api_manufacturing_orders_collapseOnGoingLocalOfItems_collection':
                return $this->collapseOnGoingLocalOfItems();
                break;
            case 'api_manufacturing_orders_collapseOfsToConfirmItems_collection':
                return $this->collapseOfsToConfirmItems();
                break;
            case 'api_manufacturing_orders_collapseNewOfsItems_collection':
                return $this->collapseNewOfsItems();
                break;
            default:
                return new JsonResponse(['error' => 'Route non gérée'], 404);
        }
    }

    private function getOrderData(Order $manufacturingOrder): array
    {
        $selling = $manufacturingOrder->getOrder();
        $commande = $selling ? $selling->getRef() : '';
        $customerName = '';
        if ($selling) {
            $sellingOrders = $this->sellingOrderRepository->findById($manufacturingOrder->getOrder()->getId());
            foreach ($sellingOrders as $sellingOrder) {
                $customer = $sellingOrder->getCustomer();
                $customerName = $customer ? $customer->getName() : '';
            }
        }

        $data = [
            'client' => $customerName,
            'cmde' => $commande,
            'debutProd' => $manufacturingOrder->getManufacturingDate()->format('Y-m-d'),
            'produit' => $manufacturingOrder->getProduct()->getCode(),
            'quantite' => $manufacturingOrder->getQuantityRequested()->getValue() ." ".$manufacturingOrder->getQuantityRequested()->getCode(),
        ];

        return $data;
    }
    
    public function collapseOfsToConfirmItems(): JsonResponse
    {
        $manufacturingOrders = $this->em->getRepository(Order::class)->findAll();
        $data = [];
        foreach ($manufacturingOrders as $manufacturingOrder) {
            if ($manufacturingOrder->getEmbState()->getState() === 'asked') {
                $orderData = $this->getOrderData($manufacturingOrder);
                $manuCompany = $manufacturingOrder->getmanufacturingCompany();
                $siteDeProduction = $manuCompany->getName();
                $orderData['siteDeProduction'] = $siteDeProduction;
                $orderData['of'] = $manufacturingOrder->getRef();
                $orderData['Indice OF']= $manufacturingOrder->getIndex();
                $orderData['Indice']= $manufacturingOrder->getProduct()->getIndex();
                $data[] = $orderData;
            }
        }
        return new JsonResponse($data);
    }

    public function collapseOnGoingLocalOfItems(): JsonResponse
    {
        $manufacturingOrders = $this->em->getRepository(Order::class)->findAll();
        $data = [];
        foreach ($manufacturingOrders as $manufacturingOrder) {
            if ($manufacturingOrder->getEmbState()->getState() === 'agreed') {
                $manuCompany = $manufacturingOrder->getmanufacturingCompany();
                $siteDeProduction = $manuCompany->getName();
                $orderData = $this->getOrderData($manufacturingOrder);
                $orderData['quantiteProduite'] = $manufacturingOrder->getQuantityDone()->getValue() ." ".$manufacturingOrder->getQuantityDone()->getCode();
                $orderData['Etat']= $manufacturingOrder->getEmbState()->getState();
                $orderData['siteDeProduction'] = $siteDeProduction;
                $orderData['of'] = $manufacturingOrder->getRef();
                $orderData['Indice OF']= $manufacturingOrder->getIndex();
                $orderData['Indice']= $manufacturingOrder->getProduct()->getIndex();
                $orderData['finProd']=  $manufacturingOrder->getDeliveryDate()->format('Y-m-d');
                $data[] = $orderData;
            }
        }

        return new JsonResponse($data);
    }

    public function collapseNewOfsItems(): JsonResponse
    {
        $manufacturingOrders = $this->em->getRepository(Order::class)->findAll();
        $data = [];
        foreach ($manufacturingOrders as $manufacturingOrder) {
            $manuCompany = $manufacturingOrder->getmanufacturingCompany();
            $siteDeProduction = $manuCompany->getName();
            $orderData = $this->getOrderData($manufacturingOrder);
            $orderData['siteDeProduction'] = $siteDeProduction;
            $orderData['etatInitialOF']= $manufacturingOrder->getEmbState()->getState();
            $orderData['minDeLancement']= $manufacturingOrder->getProduct()->getMinProd()->getValue()." ".$manufacturingOrder->getProduct()->getMinProd()->getCode();
            $orderData['finProd']=  $manufacturingOrder->getDeliveryDate()->format('Y-m-d');
            $data[] = $orderData;
        }
        return new JsonResponse($data);
    }
}