<?php

namespace App\Controller\Needs;

use DateTime;
use DateInterval;
//use Psr\Log\LoggerInterface;
use App\Service\MeasureManager;
use App\Entity\Project\Product\Product;
//use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use App\Repository\Project\Product\ProductRepository;
use App\Repository\Selling\Order\ProductItemRepository;
use App\Repository\Logistics\Stock\ProductStockRepository;
use App\Repository\Project\Product\NomenclatureRepository;
use App\Repository\Purchase\Component\ComponentRepository;
use App\Repository\Logistics\Stock\ComponentStockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\Purchase\Order\ItemRepository as PurchaseItemRepository;
use App\Repository\Manufacturing\Product\ManufacturingProductItemRepository;
use App\Repository\Selling\Customer\ProductRepository as ProductCustomerRepository;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Selling\Order\ProductItem;
use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Project\Product\Nomenclature;
use App\Entity\Production\Manufacturing\Order as ManufacturingOrder;
use App\Service\RedisService;
use App\EventSubscriber\CacheUpdateSubscriber;
use App\Entity\Purchase\Order\Item as PurchaseItem;
Use App\Entity\Purchase\Component\Component;
#[
    ApiResource(
        collectionOperations: [
            'get' => [
                'method' => 'GET',
                'path' => '/api/needs/products',
            ],
            'get_components' => [
                'method' => 'GET',
                'path' => '/api/needs/components',
            ]
        ],
        itemOperations: [
            'get' => [
                'method' => 'GET',
                'path' => '/api/needs/{id}'
            ]
        ]
    )
]
class NeedsController extends AbstractController
{
    private ProductItemRepository $productItemRepository;
    private ProductStockRepository $productStockRepository;
    private ProductRepository $productRepository;
    private ProductCustomerRepository $productCustomerRepository;
    private PurchaseItemRepository $purchaseItemRepository;
    private ManufacturingProductItemRepository $manufacturingProductItemRepository;
    private NomenclatureRepository $nomenclatureRepository;
    private ComponentRepository $componentRepository;
    private ComponentStockRepository $componentStockRepository;
    private MeasureManager $measureManager;
    private RedisService $redisService;
//    private CacheUpdateSubscriber $cacheUpdateSubscriber;

    public function __construct(
//        private readonly EntityManagerInterface $em,
        ProductItemRepository $productItemRepository,
        ProductStockRepository $productStockRepository,
        ProductRepository $productRepository,
        ProductCustomerRepository $productCustomerRepository,
        PurchaseItemRepository $purchaseItemRepository,
        ManufacturingProductItemRepository $manufacturingProductItemRepository,
        NomenclatureRepository $nomenclatureRepository,
        ComponentRepository $componentRepository,
        ComponentStockRepository $componentStockRepository,
        MeasureManager $measureManager,
//        private LoggerInterface $logger,
        RedisService $redisService,
        CacheUpdateSubscriber $cacheUpdateSubscriber,

    ) {
        $this->productItemRepository = $productItemRepository;
        $this->productStockRepository = $productStockRepository;
        $this->productRepository = $productRepository;
        $this->productCustomerRepository = $productCustomerRepository;
        $this->purchaseItemRepository = $purchaseItemRepository;
        $this->manufacturingProductItemRepository = $manufacturingProductItemRepository;
        $this->nomenclatureRepository = $nomenclatureRepository;
        $this->componentRepository = $componentRepository;
        $this->componentStockRepository = $componentStockRepository;
        $this->measureManager = $measureManager;
        $this->redisService = $redisService;
        $this->cacheUpdateSubscriber = $cacheUpdateSubscriber;
    }

    #[Route('/api/needs/products', name: 'needs_products', methods: ['GET'])]
    public function index(): JsonResponse
    {
        //region récupération de cache via redis
        
        // Récupérer l'instance de Predis\Client à partir de RedisService
        $redisClient = $this->redisService->getClient();
        // Créer une instance de RedisAdapter en passant l'instance de Predis\Client
        $redisAdapter = new RedisAdapter($redisClient);
        // Créer une instance de TagAwareAdapter en passant l'instance de RedisAdapter
        $cache = new TagAwareAdapter($redisAdapter);
        $cacheKeyCreationDate = 'api_needs_creation_date_product';
        $cacheKeyStocks = 'api_needs_stocks_product';
        $cacheKeySelling = 'api_needs_selling_order_item';
        $cacheKeymanufacturingOrders = 'api_needs_manufacturing_orders';
        $cacheKeyProducts = 'api_needs_products';
        $cacheItemCreationDate = $cache->getItem($cacheKeyCreationDate);
        $cacheCreationDates = $cacheItemCreationDate->get();

        //region Vérifie si les données de produit sont en cache
        $cacheproducts = $cache->getItem($cacheKeyProducts);
        if (!$cacheproducts->isHit()) {
            // $products = $this->productRepository->findByEmbBlockerAndEmbState();
            $products = $this->productRepository->findBy(['embState.state' => ['agreed', 'warning'], 'embBlocker.state' => 'enabled', 'deleted' => false]);
            $cacheproducts->set($products);
            $cache->save($cacheproducts);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[$cacheKeyProducts] = $cacheItemCreationDate->get();
        } else {
            /** @var Product $products */
            $products = $cacheproducts->get();
        }        
        $productcustomers = $this->productCustomerRepository->findBy(['deleted' => false]);
        //endregion

        //region Vérifie si les données de vente sont en cache
        $cacheItemSelling = $cache->getItem($cacheKeySelling);
        if (!$cacheItemSelling->isHit()) {
            // Les données de vente ne sont pas en cache, donc on les génère et on les met en cache
            // $sellingItems = $this->productItemRepository->findByEmbBlockerAndEmbState();
            $sellingItems = $this->productItemRepository->findBy(['embState.state' => ['agreed', 'partially_delivered'], 'embBlocker.state' => ['enabled'], 'deleted' => false]);
            $cacheItemSelling->set($sellingItems);
            $cache->save($cacheItemSelling);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[$cacheKeySelling] = $cacheItemCreationDate->get();
        } else {
            /** @var ProductItem $sellingItems */
            $sellingItems = $cacheItemSelling->get();
        }      
        //endregion  
        
        //region Vérifie si les données de fabrication sont en cache
        $cacheItemManufacturing = $cache->getItem($cacheKeymanufacturingOrders);
        if (!$cacheItemManufacturing->isHit()) {
            // Les données de fabrication ne sont pas en cache, donc on les génère et on les met en cache
            $manufacturingOrders = $this->manufacturingProductItemRepository->findBy(['embState.state' => ['asked', 'agreed'], 'embBlocker.state' => 'enabled', 'deleted' => false]);
            // $manufacturingOrders = $this->manufacturingProductItemRepository->findByEmbBlockerAndEmbState();
            $cacheItemManufacturing->set($manufacturingOrders);
            $cache->save($cacheItemManufacturing);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[$cacheKeymanufacturingOrders] = $cacheItemCreationDate->get();   
        }else {
            /** @var ManufacturingOrder $manufacturingOrders */
            $manufacturingOrders = $cacheItemManufacturing->get();
        }
        //endregion
        
        //region Vérifie si les données stocks sont en cache
        $cacheProductStocks = $cache->getItem($cacheKeyStocks);
        if (!$cacheProductStocks->isHit()) {
            // Les données stocks ne sont pas en cache, donc on les génère et on les met en cache
            // $stocks = $this->productStockRepository->findAll();
            $stocks = $this->productStockRepository->findBy(['deleted' => false, 'jail' => false]);
            $cacheProductStocks->set($stocks);
            $cache->save($cacheProductStocks);
           // if (!$cacheCreationDates->isHit()) {
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[$cacheKeyStocks] = $cacheItemCreationDate->get();
           // }
        }else {
            /** @var ProductStock $stocks */
            $stocks = $cacheProductStocks->get();
        }
        //endregion

        //endregion

        $productChartsData = $this->generateProductChartsData($sellingItems, $manufacturingOrders, $stocks, $products);

        $productFamilies = $this->getProductFamilies($products);
        $customersData = $this->generateCustomersData($productcustomers);
        $productsData = [];
        foreach ($products as $prod) {
            $productsData[] = $this->generateProductData($prod, $productChartsData, $stocks);
        }
        $productChartsData = $this->finalizeProductChartsData($productChartsData);
        if($cacheItemCreationDate->isHit() === false) {
            if($cacheItemCreationDate->getMetadata() === []) {
                $cacheItemCreationDate->set($cacheCreationDates);
                $value = $cacheItemCreationDate->get();
                if (is_array($value) && count($value) === 4) {
                    // Le tableau a 4 éléments
                    $cache->save($cacheItemCreationDate);  
                }
            }
        }
        // Récupère les dates de création du cache
        // $cacheCreationDates = $cache->getItem($cacheKeyCreationDate)->get();
        return new JsonResponse([
            'productChartsData' => $productChartsData,
            'productFamilies' => $productFamilies,
            'products' => $productsData,
            'customers' => $customersData,
        ]);
    }

    /**
     * @Route("/api/needs/components", name="needs_components", methods={"GET"})
     */
    public function getComponents(): JsonResponse
    {
        // Récupérer l'instance de Predis\Client à partir de RedisService
        $redisClient = $this->redisService->getClient();
        // Créer une instance de RedisAdapter en passant l'instance de Predis\Client
        $redisAdapter = new RedisAdapter($redisClient);
        // Créer une instance de TagAwareAdapter en passant l'instance de RedisAdapter
        $cache = new TagAwareAdapter($redisAdapter);

        $cacheKeyStocks = 'api_needs_stocks_product';
        $cacheKeySelling = 'api_needs_selling_order_item';
        $cacheKeymanufacturingOrders = 'api_needs_manufacturing_orders';
        $cacheKeyProducts = 'api_needs_products';
        $cacheKeyCreationDate = 'api_needs_creation_date_product';
        $cacheItemCreationDate = $cache->getItem($cacheKeyCreationDate);
        $cacheCreationDates = $cacheItemCreationDate->get();

        $cachekeyComponentStock = 'api_needs_stock_component';
        $cachekeyPurchaseItem = 'api_needs_purchase_item';
        $cacheKeyCreationDateComponent = 'api_needs_creation_date_component';
        $cacheItemCreationDateComponent = $cache->getItem($cacheKeyCreationDateComponent);
        $cacheCreationDatesComponent = $cacheItemCreationDateComponent->get();

        //region Vérifie si les données de produit sont en cache
        $cacheproducts = $cache->getItem($cacheKeyProducts);
        if (!$cacheproducts->isHit()) {
            // $products = $this->productRepository->findByEmbBlockerAndEmbState();
            $products = $this->productRepository->findBy(['embState.state' => ['agreed', 'warning'], 'embBlocker.state' => 'enabled', 'deleted' => false]);
            $cacheproducts->set($products);
            $cache->save($cacheproducts);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[$cacheKeyProducts] = $cacheItemCreationDate->get();
        } else {
            /** @var Product $products */
            $products = $cacheproducts->get();
        }
        //endregion
        //region Vérifie si les données stocks sont en cache
        $cacheProductStocks = $cache->getItem($cacheKeyStocks);
        if (!$cacheProductStocks->isHit()) {
            // Les données stocks ne sont pas en cache, donc on les génère et on les met en cache
            // $stocks = $this->productStockRepository->findAll();
            $stocks = $this->productStockRepository->findBy(['deleted' => false, 'jail' => false]);
            $cacheProductStocks->set($stocks);
            $cache->save($cacheProductStocks);
            // if (!$cacheCreationDates->isHit()) {
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[$cacheKeyStocks] = $cacheItemCreationDate->get();
            // }
        }else {
            /** @var ProductStock $stocks */
            $stocks = $cacheProductStocks->get();
        }
        //endregion
        //region Vérifie si les données de vente sont en cache
        $cacheItemSelling = $cache->getItem($cacheKeySelling);
        if (!$cacheItemSelling->isHit()) {
            // Les données de vente ne sont pas en cache, donc on les génère et on les met en cache
            // $sellingItems = $this->productItemRepository->findByEmbBlockerAndEmbState();
            $sellingItems = $this->productItemRepository->findBy(['embState.state' => ['agreed', 'partially_delivered'], 'embBlocker.state' => ['enabled'], 'deleted' => false]);
            $cacheItemSelling->set($sellingItems);
            $cache->save($cacheItemSelling);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[$cacheKeySelling] = $cacheItemCreationDate->get();
        } else {
            /** @var ProductItem $sellingItems */
            $sellingItems = $cacheItemSelling->get();
        }
        //endregion
        //region Vérifie si les données fabrication sont en cache
        $cacheItemManufacturing = $cache->getItem($cacheKeymanufacturingOrders);
        if (!$cacheItemManufacturing->isHit()) {
            // Les données de fabrication ne sont pas en cache, donc on les génère et on les met en cache
            $manufacturingOrders = $this->manufacturingProductItemRepository->findBy(['embState.state' => ['asked', 'agreed'], 'embBlocker.state' => 'enabled', 'deleted' => false]);
            // $manufacturingOrders = $this->manufacturingProductItemRepository->findByEmbBlockerAndEmbState();
            $cacheItemManufacturing->set($manufacturingOrders);
            $cache->save($cacheItemManufacturing);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[$cacheKeymanufacturingOrders] = $cacheItemCreationDate->get();
        }else {
            /** @var ManufacturingOrder $manufacturingOrders */
            $manufacturingOrders = $cacheItemManufacturing->get();
        }
        //endregion
        $productChartsData = $this->generateProductChartsData($sellingItems, $manufacturingOrders, $stocks, $products);
        $productsData = [];
        foreach ($products as $prod) {
            $productsData[] = $this->generateProductData($prod, $productChartsData, $stocks);
        }
        $productChartsData = $this->finalizeProductChartsData($productChartsData);

        //region On initialise le calcul des besoins en composants à partir des 'newOfsNeeds' dans productsData et des 'quantity_requested_value' dans productChartsData
        $componentsNeeds = [];
        foreach ($products as $prod) {
            $productId = $prod->getId();
            $productData = $this->generateProductData($prod, $productChartsData, $stocks);
            //region On récupère les besoins d'OF associé au produit courant
            $currentProductData = array_values(array_filter($productsData, function ($productData) use ($productId) {
                return $productData['productId'] === $productId;
            }))[0];
            $currentProductChartData = array_values(array_filter($productChartsData, function ($productChartData) use ($productId) {
                return $productChartData['productId'] === $productId;
            }))[0];
            dump([
                'currentProductData' => $currentProductData,
                'currentProductChartData' => $currentProductChartData
            ]);
            $newOFNeedsData = $currentProductData['newOFNeeds'] ?? [];
            $ofNeedsData = $currentProductChartData['quantity_requested_value'] ?? [];
            //endregion
            //region on récupère la nomenclature du produit
            $cacheKeyNomenclature = 'api_needs_nomenclature_' . $productId;
            $cacheItemNomenclature = $cache->getItem($cacheKeyNomenclature);
            if (!$cacheItemNomenclature->isHit()) {
                $nomenclatures = $this->nomenclatureRepository->findBy(['product' => $productId, 'deleted' => false]);
                $cacheItemNomenclature->set($nomenclatures);
                $cache->save($cacheItemNomenclature);
                $cacheItemCreationDateComponent->set(date('Y-m-d H:i:s'));
                $cacheCreationDatesComponent[$cacheKeyNomenclature] = $cacheItemCreationDateComponent->get();
            } else {
                $nomenclatures = $cacheItemNomenclature->get();
            }
            //endregion
            //region On parcourt chaque item de la nomenclature et on calcule les besoins en composants
            /** @var Nomenclature $itemNomenclature */
            foreach ($nomenclatures as $itemNomenclature) {
                $currentComponent = $itemNomenclature->getComponent();
                $bomQuantity = $itemNomenclature->getQuantity()->getValue();
                $componentId = $currentComponent->getId();
                //region on ajoute les besoins issus des nouveaux OFs
                foreach ($newOFNeedsData as $newOFNeed) {
                    $date = $newOFNeed['date'];
                    $quantity = $newOFNeed['quantity'];
                    $needs = $quantity * $bomQuantity;
                    if (!isset($componentsNeeds[$componentId])) {
                        $componentsNeeds[$componentId] = [];
                    }
                    if (!isset($componentsNeeds[$componentId][$date])) {
                        $componentsNeeds[$componentId][$date] = 0;
                    }
                    $componentsNeeds[$componentId][$date] += $needs;
                }
                //endregion
                //region on ajoute les besoins issus des OFs en cours
                foreach ($ofNeedsData as $date => $ofQuantity) {
                    $date = $date;
                    $quantity = $ofQuantity;
                    $needs = $quantity * $bomQuantity;
                    if (!isset($componentsNeeds[$componentId])) {
                        $componentsNeeds[$componentId] = [];
                    }
                    if (!isset($componentsNeeds[$componentId][$date])) {
                        $componentsNeeds[$componentId][$date] = 0;
                    }
                    $componentsNeeds[$componentId][$date] += $needs;
                }
                //endregion
            }
            //endregion
        }
        //endregion
        dump([
            'productChartsData' => $productChartsData,
            'products' => $productsData,
            'componentsNeeds' => $componentsNeeds
        ]);
        //region New Vérifie si les données ComponentStock sont en cache
        $cacheItemComponentStock = $cache->getItem($cachekeyComponentStock);
        if (!$cacheItemComponentStock->isHit()) {
            $filteredStocks = $this->componentStockRepository->findBy(['deleted' => false, 'jail' => false]);
            $cacheItemComponentStock->set($filteredStocks);
            $cache->save($cacheItemComponentStock);
            $cacheItemCreationDateComponent->set(date('Y-m-d H:i:s'));
            $cacheCreationDatesComponent[$cachekeyComponentStock] = $cacheItemCreationDateComponent->get();
        } else {
            /** @var ComponentStock $filteredStocks */
            $filteredStocks = $cacheItemComponentStock->get();
        }
        //endregion
        //region Vérifie si les données PurchaseItem sont en cache
        $cachePurchaseItem = $cache->getItem($cachekeyPurchaseItem);
        if (!$cachePurchaseItem->isHit()) {
            $purchaseItems = $this->purchaseItemRepository->findBy(['embState.state' => ['forecast', 'agreed', 'partially_received'], 'embBlocker.state' => ['enabled', 'delayed'], 'deleted' => false]);
            $cachePurchaseItem->set($purchaseItems);
            $cache->save($cachePurchaseItem);
            $cacheItemCreationDateComponent->set(date('Y-m-d H:i:s'));
            $cacheCreationDatesComponent[$cachekeyPurchaseItem] = $cacheItemCreationDateComponent->get();
        } else {
            /** @var PurchaseItem $purchaseItems */
            $purchaseItems = $cachePurchaseItem->get();
        }
        //endregion
        //region inversion de la matrice
        $invertedMatrix = [];
        $componentChartData = [];
        $componentfield = [];

        foreach ($products as $prod) {
            $productId = $prod->getId();
            //region on récupère la nomenclature du produit
            $cacheKeyNomenclature = 'api_needs_nomenclature_' . $productId;
            $cacheItemNomenclature = $cache->getItem($cacheKeyNomenclature);
            if (!$cacheItemNomenclature->isHit()) {
                $nomenclatures = $this->nomenclatureRepository->findBy(['product' => $productId, 'deleted' => false]);
                $cacheItemNomenclature->set($nomenclatures);
                $cache->save($cacheItemNomenclature);
            $cacheItemCreationDateComponent->set(date('Y-m-d H:i:s'));
            $cacheCreationDatesComponent[$cacheKeyNomenclature] = $cacheItemCreationDateComponent->get();
            } else {
                /** @var Nomenclature $nomenclatures */
                $nomenclatures = $cacheItemNomenclature->get();
            }
            //endregion



            //TODO: voir l'intérêt, c'est louche
            $minStock = $this->generateMinStock($prod);
            $this->updateStockMinimumForProduct($productChartsData, $productId, $minStock);


            //TODO: Voir si on supprime la définition de la clé $productId
            $productsData[$productId] = $productData;


            $newOFNeedsData = $productData['newOFNeeds'];
            $maxQuantity = $productData['totalSellingQuantity'];

            if (!empty($maxQuantity)) {
                $this->processNewOFNeedsData($newOFNeedsData, $nomenclatures, $productId, $productChartsData, $invertedMatrix);
            }
        }
        //endregion
        //region Itérer sur les stocks filtrés pour calculer les sommes des quantités de stock
        foreach ($filteredStocks as $filteredStock) {
            $stockComponentId = $filteredStock->getItem()->getId();
            $stockComponentQuantity = $this->calculateConvertQuantity($filteredStock->getQuantity());
            if (!isset($stockQuantities[$stockComponentId])) {
                $stockQuantities[$stockComponentId] = 0;
            }
            // Ajouter la quantité de stock à la somme existante pour ce stockComponentId
            $stockQuantities[$stockComponentId] += $stockComponentQuantity;
        }
        //endregion
        //region Etablissement de la base de temps $uniqueDates
        $uniqueDates = [];
        foreach ($invertedMatrix as $compositeKey => $orders) {
            foreach ($orders as $order) {
                $label = $order['label'];
                if (!isset($uniqueDates[$compositeKey]) || !in_array($label, $uniqueDates[$compositeKey])) {
                    $uniqueDates[$compositeKey][] = $label;
                }
            }
        }

        foreach ($purchaseItems as $purchaseItem) {
            $purchaseItemComponent = $purchaseItem->getItem()->getId();
            $purchaseItemDate = $purchaseItem->getConfirmedDate()->format('d/m/Y');
            if (!isset($uniqueDates[$purchaseItemComponent]) || !in_array($purchaseItemDate, $uniqueDates[$purchaseItemComponent])) {
                $uniqueDates[$purchaseItemComponent][] = $purchaseItemDate;
            }
        }
        //endregion

        foreach ($uniqueDates as $componentId => $dates) {
            $uniqueDatesForComponent = array_unique($dates);
            usort($uniqueDatesForComponent, function ($a, $b) {
                $dateA = DateTime::createFromFormat('d/m/Y', $a);
                $dateB = DateTime::createFromFormat('d/m/Y', $b);
                return $dateA <=> $dateB;
            });
            if (!empty($uniqueDatesForComponent)) {
                $this->addPreviousDate($uniqueDatesForComponent);
            }
            // Obtenez les commandes spécifiques à ce composant
            $ordersForComponent = $invertedMatrix[$componentId] ?? [];
            $needsPerDate = $this->getNeedsPerDate($ordersForComponent, $uniqueDatesForComponent);
            $unitsPerDate = $this->getUnitsPerDate($ordersForComponent, $uniqueDatesForComponent);

            $cacheKeyComponent = 'api_needs_component_' . $componentId;
            $cacheItemComponent = $cache->getItem($cacheKeyComponent);
            if (!$cacheItemComponent->isHit()) {
            $componentId = intval($componentId);
            if($componentId !== 0){
                $components = $this->componentRepository->findById($componentId);
            }
                $cacheItemComponent->set($components);
                $cache->save($cacheItemComponent);
            $cacheItemCreationDateComponent->set(date('Y-m-d H:i:s'));
            $cacheCreationDatesComponent[$cacheKeyComponent] = $cacheItemCreationDateComponent->get();
            }else{
                 /** @var Component $components */
                $components = $cacheItemComponent->get();
            }
            $stockMinimum =  $components->getMinStock()->getValue();
            $FamilyName = $components->getFamily()->getFullName();
            $componentCode = $components->getCode();

            $stockMinimumPerDate = [];
            foreach ($uniqueDatesForComponent as $date) {
                $stockMinimumPerDate[$date] = $stockMinimum;
            }
            $stockTotalPerDate = [];
            $stockTotal = 0;
            if (isset($stockQuantities[$componentId])) {
                $stockTotal += $stockQuantities[$componentId];
            }
            foreach ($uniqueDatesForComponent as $date) {
                $stockTotalPerDate[$date] = $stockTotal;
            }

            $purchaseQuantityPerComponent = [];
            $purchaseCodePerComponent = [];
            $stockProgressPerDateForComponent = [];

            foreach ($uniqueDatesForComponent as $date) {
                $remainingQuantity = 0;
                $purchaseQuantity = 0;
                $ReceiptQuantity = 0;
                $purchaseCode = null;
                $ReceiptCode = null;

                foreach ($purchaseItems as $purchaseItem) {
                    $purchaseItemComponent = $purchaseItem->getItem()->getId();
                    $purchaseItemDate = $purchaseItem->getConfirmedDate()->format('d/m/Y');
                    if ($purchaseItemComponent === $componentId && $purchaseItemDate === $date) {
                        $purchaseQuantity = $this->calculateConvertQuantity($purchaseItem->getConfirmedQuantity());
                        $purchaseCode = $purchaseItem->getConfirmedQuantity()->getCode();
                        $ReceiptQuantity = $purchaseItem->getReceiptQuantity()->getValue();
                        $ReceiptCode = $purchaseItem->getReceiptQuantity()->getCode();
                        $remainingQuantity = $purchaseQuantity - $ReceiptQuantity;
                    }

                    $purchaseQuantityPerComponent[$date] = $remainingQuantity;
                    $purchaseCodePerComponent[$date] = $purchaseCode;
                }
                $purchaseQuantityPerDate[$componentId] = $purchaseQuantityPerComponent;
                $purchaseCodePerDate[$componentId] = $purchaseCodePerComponent;

                $stockProgressPerDateForComponent[$date] = $stockTotalPerDate[$date] - $needsPerDate[$date] + $purchaseQuantityPerComponent[$date];
            }
            $componentStockDefault = [];
            $i = 1;
            if (isset($stockMinimumPerDate) && isset($stockProgressPerDateForComponent)) {
                $allStocksBelowMinimum = true;
                foreach ($uniqueDatesForComponent as $date) {
                    if (!isset($stockProgressPerDateForComponent[$date]) || !isset($stockMinimumPerDate[$date]) || $stockProgressPerDateForComponent[$date] >= $stockMinimumPerDate[$date]) {
                        $allStocksBelowMinimum = false;
                        break;
                    }
                }
                if ($allStocksBelowMinimum) {
                    foreach ($uniqueDatesForComponent as $date) {
                        $componentStockDefault[$i] = ['date' => $date];
                        $i = $i + 1;
                    }
                }
            }
            $newSupplierOrderNeeds = [];
            $index = 1;
            foreach ($componentStockDefault as $stockDefault) {
                $date = DateTime::createFromFormat('d/m/Y', $stockDefault['date']);
                $date->modify('-1 month');
                $quantity = $stockMinimumPerDate[$stockDefault['date']] - $stockProgressPerDateForComponent[$stockDefault['date']];
                $newSupplierOrderNeeds[$index++] = [
                    'date' => $date->format('d/m/Y'),
                    'quantity' => $quantity
                ];
            }
            if (!empty($componentStockDefault)) {
                $componentfield[$componentId] = [
                    'componentStockDefaults' => $componentStockDefault,
                    'family' => $FamilyName,
                    'newSupplierOrderNeeds' => $newSupplierOrderNeeds,
                    'ref' => $componentCode
                ];
            }
            $componentChartData[$componentId] = [
                'labels' => $uniqueDatesForComponent,
                'stockMinimum' => $stockMinimumPerDate,
                'stockProgress' => $stockProgressPerDateForComponent
            ];
            $uniqueDates = [];
        }

        if($cacheItemCreationDate->isHit() === false) {
            if($cacheItemCreationDate->getMetadata() === []) {
                $cacheItemCreationDate->set($cacheCreationDates);
                $value = $cacheItemCreationDate->get();
                if (is_array($value) && count($value) === 4) {
                    // Le tableau a 4 éléments
                    $cache->save($cacheItemCreationDate);  
                } 
                }
            }  
        
        $cacheCreationDates = $cache->getItem($cacheKeyCreationDate)->get();
        
        if($cacheItemCreationDateComponent->isHit() === false) {
            if($cacheItemCreationDateComponent->getMetadata() === []) {
                $cacheItemCreationDateComponent->set($cacheCreationDatesComponent);
                $value = $cacheItemCreationDateComponent->get();
                if (is_array($value) && count($value) >= 1) {
                    $cache->save($cacheItemCreationDateComponent);  
                } 
            }
        }  
        $cacheCreationDatesComponent = $cache->getItem($cacheKeyCreationDateComponent)->get();

        return new JsonResponse([
            'componentChartData' => $componentChartData,
            'component' => $componentfield

        ]);
    }

    function getUnitsPerDate($orders, $uniqueDates)
    {
        $unitsPerDate = [];
        foreach ($uniqueDates as $date) {
            $unit = null;
            foreach ($orders as $order) {
                if ($order['label'] === $date) {
                    $unit = $order['unite'];
                    break;
                }
            }
            $unitsPerDate[$date] = $unit;
        }
        return $unitsPerDate;
    }


    function getNeedsPerDate($orders, $uniqueDates)
    {
        $needsPerDate = [];
        foreach ($uniqueDates as $date) {
            $needs = 0;
            foreach ($orders as $order) {
                if ($order['label'] === $date) {
                    $needs = $order['needs'];
                    break;
                }
            }
            $needsPerDate[$date] = $needs;
        }
        return $needsPerDate;
    }

    private function calculateConvertQuantity($quantity): float
    {
        $QuantityToConvert = $quantity->getValue();
        $unit = $quantity->getUnit();
        $unitToConvert = $unit !== null ? $unit->getParent() : null;


        if ($unitToConvert !== null) {
            $denominatorUnit = $quantity->getDenominatorUnit();
            if ($denominatorUnit !== null) {
                $convertedMeasure = $this->measureManager->convertMeasure($quantity, $unitToConvert, $denominatorUnit);
            } else {
                $convertedMeasure = $this->measureManager->convertMeasure($quantity, $unitToConvert);
            }

            $QuantityToConvert = $convertedMeasure->getValue();
        }

        return $QuantityToConvert;
    }


    private function processNewOFNeedsData(array $newOFNeedsData, array $nomenclatures, int $productId, array $productChartsData, array &$invertedMatrix)
    {
        $components = $this->componentRepository->findById($productId);

        foreach ($newOFNeedsData as $newOFNeed) {
            $date = $newOFNeed['date'];
            $product_quantity = $newOFNeed['quantity'];
            foreach ($nomenclatures as $nomenclature) {
                $component = $nomenclature->getComponent();
                $productt = $nomenclature->getProduct();

                $uniteNomenclature =  $nomenclature->getQuantity()->getUnit();
                $nomenclature_quantity = $nomenclature->getQuantity()->getValue();
                $unitNomenclature_code = $uniteNomenclature->getCode();

                $needs = $product_quantity * $nomenclature_quantity;
                $components = $this->componentRepository->findById($component->getId());

                $unitComponent = $components->getUnit();
                $unitComponent_code = $components->getUnit()->getCode();

                if ($productt && $productt->getId() === $productId && $component) {
                    $compositeKey = $component->getId();
                    if (!isset($invertedMatrix[$compositeKey])) {
                        $invertedMatrix[$compositeKey] = [];
                    }

                    $dateExists = false;

                    foreach ($invertedMatrix[$compositeKey] as &$orderNeed) {
                        if ($orderNeed['label'] === $date) {
                            if (isset($productChartsData[$productId]['stockMinimum'][$date])) {
                                $orderNeed['stockMinimum'] = $productChartsData[$productId]['stockMinimum'][$date];
                            }

                            if ($unitNomenclature_code === $unitComponent_code) {
                                $orderNeed['unite'] = $unitComponent_code;
                                $orderNeed['needs'] += $needs;
                            } else {
                                $convertedMeasure = $this->measureManager->ConvertAfterMul($uniteNomenclature, $nomenclature_quantity, $unitComponent, $product_quantity);
                                $orderNeed['unite'] = $convertedMeasure["convertedMeasure1"]->getCode();
                                $orderNeed['needs'] += $convertedMeasure["totalMeasure"]->getValue();
                            }

                            $dateExists = true;
                            break;
                        }
                    }

                    if (!$dateExists) {
                        if ($unitNomenclature_code === $unitComponent_code) {
                            $invertedMatrix[$compositeKey][] = [
                                'label' => $date,
                                'needs' => $needs,
                                'stockMinimum' => $components->getMinStock()->getValue(),
                                'unite' => $unitComponent_code,
                            ];
                        } else {
                            $convertedMeasure = $this->measureManager->ConvertAfterMul($uniteNomenclature, $nomenclature_quantity, $unitComponent, $product_quantity);
                            $invertedMatrix[$compositeKey][] = [
                                'label' => $date,
                                'needs' => $convertedMeasure['totalMeasure']->getValue(),
                                'stockMinimum' => $components->getMinStock()->getValue(),
                                'unite' => $convertedMeasure["convertedMeasure1"]->getCode(),
                            ];
                        }
                    }
                }
            }
        }
    }

    private function addPreviousDate(&$uniqueDates)
    {
        // Convertir les dates en objets DateTime pour trouver la date la plus ancienne
        $dateTimeDates = array_map(function ($date) {
            return \DateTime::createFromFormat('d/m/Y', $date);
        }, $uniqueDates);

        // Trouver la date la plus ancienne
        $minDate = min($dateTimeDates);

        // Soustraire un jour à la date la plus ancienne
        $minDate->sub(new DateInterval('P1D'));

        // Formater la date au format requis
        $previousDate = $minDate->format('d/m/Y');

        // Ajouter la date précédente au début du tableau uniqueDates
        array_unshift($uniqueDates, $previousDate);
    }

    private function generateProductChartsData(array $sellingItems, array $manufacturingOrders, array $stocks, array $products): array
    {
        $productChartsData = [];
        // On initialise productChartData avec les stocks actuels à la date du jour
        $date = new DateTime('now');
        $dateStr = $date->format('d/m/Y');
        /** Product $product */
        foreach ($products as $product) {
            $productId = $product->getId();
            $productChartsData[] = [
                'labels' => [$dateStr],
                'stockMinimum' => [$product->getMinStock()->getValue()],
                'stockProgress' => [0],
                'productId' => $productId,
                'sellingOrderItems' => [],
                'manufacturingOrders' => []
            ];
        }

        // Traitement des ventes (remplis productChartsData[productId][confirmed_quantity_value])
        $this->processSellingItems($sellingItems, $productChartsData);

        // Traitement des items de manufacturing (remplis aussi ? productChartsData[productId][confirmed_quantity_value])
        $this->processManufacturingOrders($manufacturingOrders, $productChartsData);


        // Ordonner les labels et les stocks progress
        $this->sortLabelsAndStockProgress($productChartsData);

        // On ajoute le stock courant à la 1ère date pour chaque produit
        // Traitement des stocks (remplis aussi ? productChartsData[productId][stockProgress])
        $this->processStocks($stocks, $productChartsData);

        // Cumuler les stocks progress pour chaque produit en y ajoutant le stock courant à chaque date
        $this->cumulateStockProgress($productChartsData);
        // On positionne stockMin pour toutes les dates à la valeur de stockMin à la date du jour
        $this->updateStockMinimum($productChartsData, $products);

        return $productChartsData;
    }

    private function updateStockMinimum(array &$productChartsData, array $products): void
    {
        foreach ($productChartsData as &$productChartData) {
            $productId = $productChartData['productId'];
            $product = array_values(array_filter($products, function ($product) use ($productId) {
                return $product->getId() === $productId;
            }))[0];
            $stockMinimum = $product->getMinStock()->getValue();
            $productChartData['stockMinimum'] = array_fill(0, count($productChartData['labels']), $stockMinimum);
        }
    }

    private function cumulateStockProgress(array &$productChartsData): void
    {
        foreach ($productChartsData as &$productChartData) {
            $stockProgress = $productChartData['stockProgress'];
            $cumulatedStockProgress = [];
            $cumulatedStockProgress[0] = $stockProgress[0];
            for ($i = 1; $i < count($stockProgress); $i++) {
                $cumulatedStockProgress[$i] = $cumulatedStockProgress[$i - 1] + $stockProgress[$i];
            }
            $productChartData['stockProgress'] = $cumulatedStockProgress;
        }
    }

    private function processSellingItems(array $sellingItems, array &$productChartsData): void
    {
        foreach ($sellingItems as $sellingItem) {
            $productId = $sellingItem->getItem()->getId();
            $confirmedDate = $sellingItem->getConfirmedDate()->format('d/m/Y');
            $confirmedQuantityValue = $sellingItem->getConfirmedQuantity()->getValue();
            $productChartsData = $this->updateProductChartsDataWithSellingItem($productChartsData, $productId, $confirmedDate, $confirmedQuantityValue, $sellingItem);
        }
    }

    private function processManufacturingOrders(array $manufacturingOrders, array &$productChartsData): void
    {
        foreach ($manufacturingOrders as $manufacturingOrder) {
            $productId = $manufacturingOrder->getProduct()->getId();
            $manufacturingDate = $manufacturingOrder->getManufacturingDate()->format('d/m/Y');
            $quantityRequestedValue = $manufacturingOrder->getQuantityRequested()->getValue();
            $productChartsData = $this->updateProductChartsDataWithManufacturingOrder($productChartsData, $productId, $manufacturingDate, $quantityRequestedValue, $manufacturingOrder);
        }
    }

    private function processStocks(array $stocks, array &$productChartsData): void
    {
        foreach ($stocks as $stock) {
            $productId = $stock->getItem()->getId();
            $quantityValue = $stock->getQuantity()->getValue();
            $this->updateStockProgress($productChartsData, $productId, $quantityValue);
        }
    }

    private function updateStockProgress(array &$productChartsData, int $productId, float $quantityValue): void
    {
        //On récupère la valeur de 'stockProgress' pour le produit $productId à partir d'un filtre sur $productChartsData
        $currentStockProgressColl = array_values(array_filter($productChartsData, function ($productChartData) use ($productId) {
            return $productChartData['productId'] === $productId;
        }));
        $currentStockProgress = $currentStockProgressColl[0]['stockProgress'];
        $currentStockProgress[0] += $quantityValue;
        //On met à jour la valeur de 'stockProgress' pour le produit $productId
        $productChartsData = array_map(function ($productChartData) use ($productId, $currentStockProgress) {
            if ($productChartData['productId'] === $productId) {
                $productChartData['stockProgress'] = $currentStockProgress;
            }
            return $productChartData;
        }, $productChartsData);
    }


    private function sortLabelsAndStockProgress(array &$productChartsData): void
    {
        foreach ($productChartsData as &$product) {
//            $product['labels'] = array_values(array_unique($product['labels']));
            $uniqueLabelsValues = array_values(array_unique($product['labels']));
            usort($uniqueLabelsValues, function($a, $b) {
                $dateA = \DateTime::createFromFormat('d/m/Y', $a);
                $dateB = \DateTime::createFromFormat('d/m/Y', $b);
                return $dateA <=> $dateB;
            });
            $newStockProgress = [];
            // Initialisation de 'stockProgress' comme tableau pour chaque date
            foreach ($uniqueLabelsValues as $key => $date) {
                // On récupère l'ancien index de la date
                $oldIndex = array_search($date, $product['labels']);
                // On récupère l'ancienne valeur de 'stockProgress' à l'ancien index
                $oldStockProgressValue = $product['stockProgress'][$oldIndex] ?? 0;
                // On initialise $newStockProgress avec la valeur de 'stockProgress' à l'ancien index
                $newStockProgress[] = $oldStockProgressValue;

                $confirmedQuantity = $product['confirmed_quantity_value'][$date] ?? 0;
                $newStockProgress[$key] -= $confirmedQuantity;

                $quantityRequested = $product['quantity_requested_value'][$date] ?? 0;
                $newStockProgress[$key] += $quantityRequested;
            }
            $product['labels'] = $uniqueLabelsValues;
            $product['stockProgress'] = $newStockProgress;
        }
    }


    private function updateProductChartsDataWithManufacturingOrder(array $productChartsData, int $productId, string $date, float $quantity, Object $manufacturingOrder): array
    {
        // On récupère l'élément de productChartsData dont le productId = $productId, si l'élément n'existe pas on le crée
        $foundProductChartDataArray = array_filter($productChartsData, function ($productChartData) use ($productId) {
            return $productChartData['productId'] === $productId;
        });
        // On récupère le premier élément de la collection $foundProductChartDataArray (pas forcément index 0)
        $keys = array_keys($foundProductChartDataArray);
        $foundProductChartData = $foundProductChartDataArray[$keys[0]] ?? null;
        if (empty($foundProductChartData)) {
            $productChartsData[] = [
                'labels' => [$date],
                'stockMinimum' => [],
                'stockProgress' => [],
                'sellingOrderItems' => [],
                'manufacturingOrderItems' => [$date => [$manufacturingOrder]],
                'productId' => $productId
            ];
        } else {
            $foundProductChartData['labels'][] = $date;
            $foundProductChartData['quantity_requested_value'][$date] = $quantity;
            $foundProductChartData['manufacturingOrderItems'][$date][] = $manufacturingOrder;
        }
        // On met à jour le tableau productChartsData avec l'élément modifié
        return array_map(function ($productChartData) use ($productId, $foundProductChartData) {
            if ($productChartData['productId'] === $productId) {
                $productChartData = $foundProductChartData;
            }
            return $productChartData;
        }, $productChartsData);
    }
    private function updateProductChartsDataWithSellingItem(array $productChartsData, int $productId, string $date, float $quantity, ProductItem $sellingItem): array
    {
        // On récupère l'élément de productChartsData dont le productId = $productId, si l'élément n'existe pas on le crée
        $foundProductChartDataArray = array_filter($productChartsData, function ($productChartData) use ($productId) {
            return $productChartData['productId'] === $productId;
        });
        // On récupère le premier élément de la collection $foundProductChartDataArray (pas forcément index 0)
        $keys = array_keys($foundProductChartDataArray);
        $foundProductChartData = $foundProductChartDataArray[$keys[0]] ?? null;
        if (empty($foundProductChartData)) {
            $foundProductChartData[] = [
                'labels' => [$date],
                'stockMinimum' => [],
                'stockProgress' => [],
                'sellingOrderItems' => [$date => $sellingItem->getConfirmedQuantity()->getValue()],
                'productId' => $productId
            ];
            return $foundProductChartData;
        } else {
            $foundProductChartData['labels'][] = $date;
            $foundProductChartData['confirmed_quantity_value'][$date] = $quantity;
            if (isset($foundProductChartData['sellingOrderItems'][$date])) $foundProductChartData['sellingOrderItems'][$date] += $sellingItem->getConfirmedQuantity()->getValue();
            else $foundProductChartData['sellingOrderItems'][$date] = $sellingItem->getConfirmedQuantity()->getValue();
            // On met à jour le tableau productChartsData avec l'élément modifié
            $newProductsChartData = array_map(function ($productChartData) use ($productId, $foundProductChartData) {
                if ($productChartData['productId'] === $productId) {
                    $productChartData = $foundProductChartData;
                }
                return $productChartData;
            }, $productChartsData);
            return $newProductsChartData;
        }
    }

    private function getProductFamilies(array $products): array
    {
        return array_reduce($products, function ($result, $prod) {
            $family = $prod->getFamily();
            if ($family) {
                $result[$family->getId()] = [
                    'familyName' => $family->getName(),
                    'pathName' => $family->getFullName(),
                ];
            }
            return $result;
        }, []);
    }

    /**
     * Generate customers data from product customers.
     *
     * @param ProductCustomer[] $productCustomers
     * @return array
     */
    private function generateCustomersData(array $productCustomers): array
    {
        $customers = [];

        foreach ($productCustomers as $productCustomer) {
            $customerId = $productCustomer->getCustomer()->getId();
            $productId = $productCustomer->getProduct()->getId();
            $societyId = $this->productCustomerRepository->findByCustomerIdSocieties($customerId);

            if (!empty($societyId)) {
                // Ajouter les données directement au tableau $customers
                $customers[] = [
                    'id' => $customerId,
                    'products' => [$productId],
                    'society' => $societyId,
                ];
            }
        }

        return $customers;
    }

    /**
     * Generate components data for a given product.
     *
     * @param Product $prod
     * @return array
     */
    private function generateComponentsData(Product $prod): array
    {
        $components = [];
        $nomenclatures = $this->nomenclatureRepository->findByProductId($prod->getId());

        foreach ($nomenclatures as $nomenclature) {
            $componentId = $nomenclature->getComponent()->getId();

            // Vérifier si le composant n'est pas déjà présent dans le tableau
            if (!in_array($componentId, $components)) {
                $components[] = $componentId;
            }
        }

        return $components;
    }

    /**
     * Generate product stock data for a given product.
     *
     * @param Product $prod
     * @param array $stocks
     * @return float
     */
    private function generateProductStockData(Product $prod, array $stocks): float
    {
        $totalStock = 0;

        foreach ($stocks as $stock) {
            if ($stock->getItem()->getId() === $prod->getId()) {
                $quantity = $stock->getQuantity()->getValue();
                $totalStock += $quantity;
            }
        }

        return $totalStock;
    }

    /**
     * Generate new OF needs data for a given product.
     *
     * @param Product $prod
     * @param array $productChartsData
     * @return array
     */
    private function generateNewOFNeedsData(Product $prod, array $productChartsData): array
    {
        $newOFNeeds = [];
        //On récupère les données de productChartsData pour le produit $prod
        $currentProductChartsData = array_values(array_filter($productChartsData, function ($productChartData) use ($prod) {
            return $productChartData['productId'] === $prod->getId();
        }))[0];
        $cumulatedNewOFQuantity = 0;
        foreach ($currentProductChartsData['labels'] as $index => $date) {
            if (isset($currentProductChartsData['stockProgress'][$index]) && isset($currentProductChartsData['stockMinimum'][$index])) {
                $stockProgress = $currentProductChartsData['stockProgress'][$index];
                $stockMinimum = $currentProductChartsData['stockMinimum'][$index];
                if ($stockProgress + $cumulatedNewOFQuantity < $stockMinimum) {
                    $dateTime = DateTime::createFromFormat('d/m/Y', $date);
                    if ($dateTime) {
                        $dateTime->sub(new DateInterval('P1W'));
                        $quantity = $stockMinimum - $stockProgress - $cumulatedNewOFQuantity;
                        if ($quantity > 0) {
                            $newOFNeeds[] = [
                                'date' => $dateTime->format('d/m/Y'),
                                'quantity' => $quantity,
                            ];
                            $cumulatedNewOFQuantity += $quantity;
                        }
                    }
                }
            }
        }

        return $newOFNeeds;
    }

    /**
     * Generate the maximum quantity data for a given product.
     *
     * @param Product $prod
     * @param array $productChartsData
     * @return float|null
     */
    private function generateMaxQuantityData(Product $prod, array $productChartsData): ?float
    {
        $currentProductChartsData = array_values(array_filter($productChartsData, function ($productChartData) use ($prod) {
            return $productChartData['productId'] === $prod->getId();
        }))[0];
        if (!isset($currentProductChartsData['confirmed_quantity_value'])) return 0.0;
        $confirmedQuantityValues = array_values($currentProductChartsData['confirmed_quantity_value']);
        // On revoie la somme des valeurs de $confirmedQuantityValues
        $sumConfirmedQuantityValues = array_sum($confirmedQuantityValues);
        return $sumConfirmedQuantityValues;
    }

    /**
     * Generate $minStock for a given product.
     *
     * @param Product $prod
     * @return float
     */
    private function generateMinStock(Product $prod): float
    {
        return $prod->getMinStock()->getValue();
    }

    /**
     * Generate $stockDefault for a given product and productChartsData.
     *
     * @param Product $prod
     * @param array $productChartsData
     * @return array
     */
    private function generateStockDefault(Product $prod, array $productChartsData): array
    {
        $productId = $prod->getId();
        $stockDefault = [];

        if (array_key_exists($productId, $productChartsData)) {
            foreach ($productChartsData[$productId]['labels'] as $date) {
                // Vérifiez si la clé existe dans le tableau
                if (isset($productChartsData[$productId]['stockProgress'][$date]) && isset($productChartsData[$productId]['stockMinimum'][$date])) {
                    $stockProgress = $productChartsData[$productId]['stockProgress'][$date];
                    $stockMinimum = $productChartsData[$productId]['stockMinimum'][$date];

                    // Your logic to determine if stockProgress is less than stockMinimum
                    if ($stockProgress < $stockMinimum) {
                        // Your logic to fill $

                        $stockDefault[] = ['date' => $date];
                    }
                }
            }
        }

        return $stockDefault;
    }
    private function generateTotalOnGoingManufacturing(Product $prod, array $productChartsData): float
    {
        $currentProductChartsData = array_values(array_filter($productChartsData, function ($productChartData) use ($prod) {
            return $productChartData['productId'] === $prod->getId();
        }))[0];
        if (!isset($currentProductChartsData['quantity_requested_value'])) return 0.0;
        $requestedQuantityValues = array_values($currentProductChartsData['quantity_requested_value']);
        // On revoie la somme des valeurs de $requestedQuantityValues
        return array_sum($requestedQuantityValues);
    }
    private function generateProductData(Product $prod, $productChartsData, $stocks): array
    {
        $components = $this->generateComponentsData($prod);

        $familyId = $prod->getFamily()->getId();
        $minStock = $prod->getMinStock()->getValue();
        $newOFNeeds = $this->generateNewOFNeedsData($prod, $productChartsData);
        $allStock = $this->generateProductStockData($prod, $stocks);
        $totalSellingQuantity = $this->generateMaxQuantityData($prod, $productChartsData);
        $totalOnGoingManufacturing = $this->generateTotalOnGoingManufacturing($prod, $productChartsData);
        $stockDefault = $this->generateStockDefault($prod, $productChartsData);

        return [
            'component' => $components,
            'family' => $familyId,
            'minStock' => $minStock,
            'newOFNeeds' => $newOFNeeds,
            'productDesg' => $prod->getName(),
            'productRef' => $prod->getCode(),
            'productIndex' => $prod->getIndex(),
            'productStock' => $allStock,
            'totalSellingQuantity' => $totalSellingQuantity,
            'stockDefault' => $stockDefault,
            'productId' => $prod->getId(),
            'totalOnGoingManufacturing' => $totalOnGoingManufacturing
        ];
    }

    private function finalizeProductChartsData(array $productChartsData): array
    {
        foreach ($productChartsData as &$product) {
            $product['stockProgress'] = array_values($product['stockProgress']);
            $product['stockMinimum'] = array_values($product['stockMinimum']);
        }
        return $productChartsData;
    }

    private function updateStockMinimumForProduct(array &$productChartsData, int $productId, float $minStock): void
    {
        $currentProductChartsData = array_values(array_filter($productChartsData, function ($productChartData) use ($productId) {
            return $productChartData['productId'] === $productId;
        }))[0];
        $labels = $currentProductChartsData['labels'];
        $currentProductChartsData['stockMinimum'] = array_fill_keys($labels, $minStock);
    }
}