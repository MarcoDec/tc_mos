<?php

namespace App\Controller\Manufacturing\Order\Needs;

use App\Entity\Management\Currency;
use App\Entity\Management\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Doctrine\Persistence\ManagerRegistry;
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
use App\Entity\Purchase\Order\Item as PurchaseItem;
use App\Repository\Manufacturing\Product\ManufacturingProductItemRepository;
use App\Entity\Purchase\Component\Component;
use App\Entity\Logistics\Stock\ComponentStock;
use App\Service\MeasureManager;
use App\Service\RedisService;
use App\Repository\Project\Product\NomenclatureRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;

class ManufacturingOrderNeedsController {
    public function __construct(
        private EntityManagerInterface $em,
        private OrderRepository $sellingOrderRepository,
        private readonly CacheInterface     $cache,
        private readonly RequestStack       $stack,
        private LoggerInterface             $logger,
        private RedisService $redisService,
        private ManagerRegistry $registry
    ) {
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
            $orderData['finProd']=  $manufacturingOrder->getDeliveryDate()?->format('Y-m-d');
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
        $manufacturingProductItemRepository = new ManufacturingProductItemRepository($this->registry, Order::class);
        $nomenclatureRepository = new NomenclatureRepository($this->registry);
        $componentRepository = $this->em->getRepository(Component::class);
        $componentStockRepository = $this->em->getRepository(ComponentStock::class);

        $cache = $this->cache;
        $stack = $this->stack;
        $logger = $this->logger;
        $unitRepository = $this->em->getRepository(Unit::class);
        $currencyRepository = $this->em->getRepository(Currency::class);
        $measureManager = new MeasureManager($cache, $unitRepository, $currencyRepository, $stack, $logger);

        $redisService = $this->redisService;

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
            $redisService);
        $ofsNeeds = json_decode($needsController->index($companyId)->getContent());
        $products = $ofsNeeds->products;
        $productChartsData = $ofsNeeds->productChartsData;
        $data = [];
        foreach ($products as $product) {
            $currentChartsData = array_filter($productChartsData, function($productChartsData) use ($product) {
                return $productChartsData->productId === $product->productId;
            });
            $sellingDetails =$currentChartsData->sellingOrderItemsDetails ?? [];
            foreach ($product->newOFNeeds as $ofNeed) {
                // Les dates $ofNEed->date sont fournis au format texte jj/mm/aaaa et doivent être convertis en objet DateTime
                $date = \DateTime::createFromFormat('d/m/Y', $ofNeed->date);
                $manufacturingOrderData['date'] = $date->format('Y-m-d');
                $manufacturingOrderData['quantity'] = $ofNeed->quantity;
                $manufacturingOrderData['product'] = $product->productRef.'-'.$product->productIndex;
                $manufacturingOrderData['productIri'] = '/api/products/'.$product->productId;
                $manufacturingOrderData['sellingDetails'] = $sellingDetails;
                $data[] = $manufacturingOrderData;
            }
        }
        return new JsonResponse($data);
    }
}