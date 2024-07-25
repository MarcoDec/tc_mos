<?php

namespace App\Controller\Manufacturing\Order\Needs;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Production\Manufacturing\Order;
use App\Repository\Selling\Order\OrderRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\Manufacturing\Order\ManufacturingOrderRepository;
use App\Controller\Needs\NeedsController;
use App\Entity\Selling\Order\ProductItem;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Project\Product\Product;
use App\Entity\Selling\Customer\Price\Product as ProductCustomer;
use App\PurchaseItem;
use App\ManufacturingProductItem;
use App\Nomenclature;
use App\Component;
use App\ComponentStock;
use App\MeasureManager;
use App\RedisService;
use App\CacheUpdateSubscriber;

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
                return $this->collapseNewOfsItems($request);
                break;
            default:
                return new JsonResponse(['error' => 'Route non gérée'], 404);
        }
    }

    private function getOrderData(Order $manufacturingOrder): array
    {
        $companyId = $_GET['companyId'];
        $selling = $manufacturingOrder->getSellingOrder();
        $commande = $selling ? $selling->getRef() : '';
        $customerName = '';
        if ($selling) {
            $sellingOrders = $this->sellingOrderRepository->findById($manufacturingOrder->getSellingOrder()?->getId());
            foreach ($sellingOrders as $sellingOrder) {
                $customer = $sellingOrder->getCustomer();
                $customerName = $customer ? $customer->getName() : '';
            }
        }

        $data = [
            'client' => $customerName,
            'cmde' => $commande,
            'debutProd' => $manufacturingOrder->getManufacturingDate()?->format('Y-m-d'),
            'produit' => $manufacturingOrder->getProduct()?->getCode(),
            'quantite' => $manufacturingOrder->getQuantityRequested()->getValue() ." ".$manufacturingOrder->getQuantityRequested()->getCode(),
        ];

        return $data;
    }
    
    public function collapseOfsToConfirmItems(): JsonResponse
    {
        $companyId = $_GET['companyId'];
        $manufacturingOrders = $this->em->getRepository(Order::class)->findBy(['manufacturingCompany' => $companyId, 'embState.state' => 'asked', 'embBlocker.state' => ['enabled', 'blocked']]);
        $data = [];
        foreach ($manufacturingOrders as $manufacturingOrder) {
            $orderData = $this->getOrderData($manufacturingOrder);
            $manuCompany = $manufacturingOrder->getmanufacturingCompany();
            $siteDeProduction = $manuCompany->getName();
            $orderData['siteDeProduction'] = $siteDeProduction;
            $orderData['manufacturingOrderId'] = $manufacturingOrder->getId();
            $orderData['of'] = $manufacturingOrder->getRef();
            $orderData['Indice OF']= $manufacturingOrder->getIndex();
            $orderData['Indice']= $manufacturingOrder->getProduct()?->getIndex();
            $orderData['Blocker']= $manufacturingOrder->getEmbBlocker()->getState();
            $data[] = $orderData;
        }
        return new JsonResponse($data);
    }

    public function collapseOnGoingLocalOfItems(): JsonResponse
    {
        $companyId = $_GET['companyId'];
        $manufacturingOrders = $this->em->getRepository(Order::class)->findBy(['manufacturingCompany' => $companyId, 'embState.state' => 'agreed', 'embBlocker.state' => ['enabled', 'blocked']]);
        $data = [];
        foreach ($manufacturingOrders as $manufacturingOrder) {
            $manuCompany = $manufacturingOrder->getmanufacturingCompany();
            $siteDeProduction = $manuCompany->getName();
            $orderData = $this->getOrderData($manufacturingOrder);
            $orderData['quantiteProduite'] = $manufacturingOrder->getQuantityDone()->getValue() ." ".$manufacturingOrder->getQuantityDone()->getCode();
            $orderData['Etat']= $manufacturingOrder->getEmbState()->getState();
            $orderData['Blocker']= $manufacturingOrder->getEmbBlocker()->getState();
            $orderData['siteDeProduction'] = $siteDeProduction;
            $orderData['of'] = $manufacturingOrder->getRef();
            $orderData['Indice OF']= $manufacturingOrder->getIndex();
            $orderData['Indice']= $manufacturingOrder->getProduct()->getIndex();
            $orderData['finProd']=  $manufacturingOrder->getDeliveryDate()->format('Y-m-d');
            $data[] = $orderData;
        }
        return new JsonResponse($data);
    }

    public function collapseNewOfsItems(): JsonResponse
    {
        $companyId = $_GET['companyId'];
        //On récupère le retour de retour de la fonction index() de NeedsController.php
        // src/Controller/Needs/NeedsController.php
        $productItemRepository = $this->em->getRepository(ProductItem::class);
        $productStockRepository = $this->em->getRepository(ProductStock::class);
        $productRepository = $this->em->getRepository(Product::class);
        $productCustomerRepository = $this->em->getRepository(ProductCustomer::class);
        $purchaseItemRepository = $this->em->getRepository(PurchaseItem::class);
        $manufacturingProductItemRepository = $this->em->getRepository(ManufacturingProductItem::class);
        $nomenclatureRepository = $this->em->getRepository(Nomenclature::class);
        $componentRepository = $this->em->getRepository(Component::class);
        $componentStockRepository = $this->em->getRepository(ComponentStock::class);
        $measureManager = new MeasureManager();
        $redisService = new RedisService();
        $cacheUpdateSubscriber = new CacheUpdateSubscriber($redisService);

        $needsController = new NeedsController(
            $productItemRepository, 
            $productStockRepository,
            $productRepository,
            $productCustomerRepository,
            $purchaseItemRepository,
            $manufacturingProductItemRepository,
            $nomenclatureRepository,
            $componentRepository,
            $componentStockRepository,
            $measureManager,
            $redisService, 
            $cacheUpdateSubscriber);
        //TODO: Récupérer le calcul des besoins
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