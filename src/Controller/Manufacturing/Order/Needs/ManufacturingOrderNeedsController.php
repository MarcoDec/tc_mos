<?php

namespace App\Controller\Manufacturing\Order\Needs;

use DateTime;
use DateTimeImmutable;
use App\Entity\Embeddable\Measure;
use App\Entity\Project\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Production\Manufacturing\Order;
use App\Repository\Selling\Order\OrderRepository;
use App\Entity\Production\Manufacturing\Operation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repository\Manufacturing\Order\ManufacturingOrderRepository;
use App\Repository\Manufacturing\Operation\ManufacturingOperationRepository;
use App\Repository\Manufacturing\Component\ManufacturingComponentItemRepository;

class ManufacturingOrderNeedsController
{
    public function __construct(private EntityManagerInterface $em, private ManufacturingOrderRepository $orderRepository, private ManufacturingOperationRepository $operationRepository, private OrderRepository $sellingOrderRepository )
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
        $quantiteProduiteTotale = 0;
        $manufacturingOperations = $this->operationRepository->findByOrderId($manufacturingOrder->getId());
        foreach ($manufacturingOperations as $manufacturingOperation) {
            $quantiteProduiteTotale += $manufacturingOperation->getQuantityProduced()->getValue();
        }
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
            'finProd' => $manufacturingOrder->getDeliveryDate()->format('Y-m-d'),
            'id' => $manufacturingOrder->getId(),
            'of' => $manufacturingOrder->getRef() . $manufacturingOrder->getIndex(),
            'produit' => $manufacturingOrder->getProduct()->getCode(),
            'quantite' => [
                'code' => $manufacturingOrder->getQuantityRequested()->getCode(),
                'value' => $manufacturingOrder->getQuantityRequested()->getValue()
            ],
            'quantiteProduite' => $quantiteProduiteTotale,

        ];

        return $data;
    }

    public function collapseOnGoingLocalOfItems(): JsonResponse
    {
        $manufacturingOrders = $this->em->getRepository(Order::class)->findAll();
        $data = [];
        foreach ($manufacturingOrders as $manufacturingOrder) {
            if ($manufacturingOrder->getEmbState()->getState() === 'asked') {
                $orderData = $this->getOrderData($manufacturingOrder);
                $orderData['etat']=  [
                    'EmbBlocker' => $manufacturingOrder->getEmbBlocker()->getState(),
                    'EmbState' => $manufacturingOrder->getEmbState()->getState()
                ];
            $data[] = $orderData;

            }
        }

        return new JsonResponse($data);
    }

    public function collapseOfsToConfirmItems(): JsonResponse
    {
        $manufacturingOrders = $this->em->getRepository(Order::class)->findAll();
        $data = [];
        foreach ($manufacturingOrders as $manufacturingOrder) {
            if ($manufacturingOrder->getEmbState()->getState() === 'agreed') {
                $manuCompany = $manufacturingOrder->getmanufacturingCompany();
                $siteDeProduction = $manuCompany->getName();
                $orderData = $this->getOrderData($manufacturingOrder);
                $orderData['siteDeProduction'] = $siteDeProduction;
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
            $orderData['minDeLancement']=  [
                'code' => $manufacturingOrder->getProduct()->getMinProd()->getCode(),
                'value' => $manufacturingOrder->getProduct()->getMinProd()->getValue()
            ];
            $data[] = $orderData;
        }
        return new JsonResponse($data);
    }
}