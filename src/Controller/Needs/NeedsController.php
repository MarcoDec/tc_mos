<?php

namespace App\Controller\Needs;

use DateTime;
use DateInterval;
//use Psr\Log\LoggerInterface;
use App\Service\MeasureManager;
use App\Entity\Project\Product\Product;
//use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Measure;
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
    //region déclaration des propriétés, constantes et constructeur
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
    }
    const API_NEEDS_CREATION_DATE_PRODUCT = 'api_needs_creation_date_product';
    const API_NEEDS_STOCKS_PRODUCT = 'api_needs_stocks_product';
    const API_NEEDS_SELLING_ORDER_ITEM = 'api_needs_selling_order_item';
    const API_NEEDS_MANUFACTURING_ORDERS = 'api_needs_manufacturing_orders';
    const API_NEEDS_PRODUCTS = 'api_needs_products';

    const API_NEEDS_CREATION_DATE_COMPONENT = 'api_needs_creation_date_component';
    const API_NEEDS_STOCKS_COMPONENT = 'api_needs_stocks_component';
    const API_NEEDS_PURCHASE_ITEM = 'api_needs_purchase_item';
    const API_NEEDS_COMPONENTS = 'api_needs_components';

    const MANUFACURING_ORDER_DELAY_BEFORE_CUSTOMER_RECEPTION = 'P4W';
    const PURCHASE_ORDER_DELAY_BEFORE_COMPONENT_USE = 'P3W'; // Réception FR + 1 semaine trajet site de fab + 1 semaine de stockage
    //endregion
    
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
        $cacheItemCreationDate = $cache->getItem(self::API_NEEDS_CREATION_DATE_PRODUCT);
        $cacheCreationDates = $cacheItemCreationDate->get();

        //region Vérifie si les données de produit sont en cache
        $cacheproducts = $cache->getItem(self::API_NEEDS_PRODUCTS);
        if (!$cacheproducts->isHit()) {
            // $products = $this->productRepository->findByEmbBlockerAndEmbState();
            $products = $this->productRepository->findBy(['embState.state' => ['agreed', 'warning'], 'embBlocker.state' => 'enabled', 'deleted' => false]);
            $cacheproducts->set($products);
            $cache->save($cacheproducts);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[self::API_NEEDS_CREATION_DATE_PRODUCT] = $cacheItemCreationDate->get();
        } else {
            /** @var Product $products */
            $products = $cacheproducts->get();
        }        
        $productcustomers = $this->productCustomerRepository->findBy(['deleted' => false]);
        //endregion

        //region Vérifie si les données de vente sont en cache
        $cacheItemSelling = $cache->getItem(self::API_NEEDS_SELLING_ORDER_ITEM);
        if (!$cacheItemSelling->isHit()) {
            // Les données de vente ne sont pas en cache, donc on les génère et on les met en cache
            // $sellingItems = $this->productItemRepository->findByEmbBlockerAndEmbState();
            $sellingItems = $this->productItemRepository->findBy(['embState.state' => ['agreed', 'partially_delivered'], 'embBlocker.state' => ['enabled'], 'deleted' => false]);
            $cacheItemSelling->set($sellingItems);
            $cache->save($cacheItemSelling);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[self::API_NEEDS_SELLING_ORDER_ITEM] = $cacheItemCreationDate->get();
        } else {
            /** @var ProductItem $sellingItems */
            $sellingItems = $cacheItemSelling->get();
        }      
        //endregion  
        
        //region Vérifie si les données de fabrication sont en cache
        $cacheItemManufacturing = $cache->getItem(self::API_NEEDS_MANUFACTURING_ORDERS);
        if (!$cacheItemManufacturing->isHit()) {
            // Les données de fabrication ne sont pas en cache, donc on les génère et on les met en cache
            $manufacturingOrders = $this->manufacturingProductItemRepository->findBy(['embState.state' => ['asked', 'agreed'], 'embBlocker.state' => 'enabled', 'deleted' => false]);
            // $manufacturingOrders = $this->manufacturingProductItemRepository->findByEmbBlockerAndEmbState();
            $cacheItemManufacturing->set($manufacturingOrders);
            $cache->save($cacheItemManufacturing);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[self::API_NEEDS_MANUFACTURING_ORDERS] = $cacheItemCreationDate->get();   
        }else {
            /** @var ManufacturingOrder $manufacturingOrders */
            $manufacturingOrders = $cacheItemManufacturing->get();
        }
        //endregion
        
        //region Vérifie si les données stocks sont en cache
        $cacheProductStocks = $cache->getItem(self::API_NEEDS_STOCKS_PRODUCT);
        if (!$cacheProductStocks->isHit()) {
            // Les données stocks ne sont pas en cache, donc on les génère et on les met en cache
            // $stocks = $this->productStockRepository->findAll();
            $stocks = $this->productStockRepository->findBy(['deleted' => false, 'jail' => false]);
            $cacheProductStocks->set($stocks);
            $cache->save($cacheProductStocks);
           // if (!$cacheCreationDates->isHit()) {
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[self::API_NEEDS_STOCKS_PRODUCT] = $cacheItemCreationDate->get();
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
        //region On récupére l'instance de Predis\Client à partir de RedisService
        $redisClient = $this->redisService->getClient();
        // Créer une instance de RedisAdapter en passant l'instance de Predis\Client
        $redisAdapter = new RedisAdapter($redisClient);
        // Créer une instance de TagAwareAdapter en passant l'instance de RedisAdapter
        $cache = new TagAwareAdapter($redisAdapter);

        $cacheItemCreationDate = $cache->getItem(self::API_NEEDS_CREATION_DATE_PRODUCT);
        $cacheCreationDates = $cacheItemCreationDate->get();

        $cacheItemCreationDateComponent = $cache->getItem(self::API_NEEDS_CREATION_DATE_COMPONENT);
        $cacheCreationDatesComponent = $cacheItemCreationDateComponent->get();

        $cacheDateComponent = $cache->getItem(self::API_NEEDS_COMPONENTS);
        //endregion
        //region Récupération des données d'entrée
        //region On vérifie si les données de produit sont en cache
        $cacheproducts = $cache->getItem(self::API_NEEDS_PRODUCTS);
        if (!$cacheproducts->isHit()) {
            // $products = $this->productRepository->findByEmbBlockerAndEmbState();
            $products = $this->productRepository->findBy(['embState.state' => ['agreed', 'warning'], 'embBlocker.state' => 'enabled', 'deleted' => false]);
            $cacheproducts->set($products);
            $cache->save($cacheproducts);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[self::API_NEEDS_PRODUCTS] = $cacheItemCreationDate->get();
        } else {
            /** @var Product $products */
            $products = $cacheproducts->get();
        }
        //endregion
        //region On vérifie si les données stocks sont en cache
        $cacheProductStocks = $cache->getItem(self::API_NEEDS_STOCKS_PRODUCT);
        if (!$cacheProductStocks->isHit()) {
            // Les données stocks ne sont pas en cache, donc on les génère et on les met en cache
            // $stocks = $this->productStockRepository->findAll();
            $stocks = $this->productStockRepository->findBy(['deleted' => false, 'jail' => false]);
            $cacheProductStocks->set($stocks);
            $cache->save($cacheProductStocks);
            // if (!$cacheCreationDates->isHit()) {
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[self::API_NEEDS_STOCKS_PRODUCT] = $cacheItemCreationDate->get();
            // }
        }else {
            /** @var ProductStock $stocks */
            $stocks = $cacheProductStocks->get();
        }
        //endregion
        //region On vérifie si les données de vente sont en cache
        $cacheItemSelling = $cache->getItem(self::API_NEEDS_SELLING_ORDER_ITEM);
        if (!$cacheItemSelling->isHit()) {
            // Les données de vente ne sont pas en cache, donc on les génère et on les met en cache
            // $sellingItems = $this->productItemRepository->findByEmbBlockerAndEmbState();
            $sellingItems = $this->productItemRepository->findBy(['embState.state' => ['agreed', 'partially_delivered'], 'embBlocker.state' => ['enabled'], 'deleted' => false]);
            $cacheItemSelling->set($sellingItems);
            $cache->save($cacheItemSelling);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[self::API_NEEDS_SELLING_ORDER_ITEM] = $cacheItemCreationDate->get();
        } else {
            /** @var ProductItem $sellingItems */
            $sellingItems = $cacheItemSelling->get();
        }
        //endregion
        //region On vérifie si les données fabrication sont en cache
        $cacheItemManufacturing = $cache->getItem(self::API_NEEDS_MANUFACTURING_ORDERS);
        if (!$cacheItemManufacturing->isHit()) {
            // Les données de fabrication ne sont pas en cache, donc on les génère et on les met en cache
            $manufacturingOrders = $this->manufacturingProductItemRepository->findBy(['embState.state' => ['asked', 'agreed'], 'embBlocker.state' => 'enabled', 'deleted' => false]);
            // $manufacturingOrders = $this->manufacturingProductItemRepository->findByEmbBlockerAndEmbState();
            $cacheItemManufacturing->set($manufacturingOrders);
            $cache->save($cacheItemManufacturing);
            $cacheItemCreationDate->set(date('Y-m-d H:i:s'));
            $cacheCreationDates[self::API_NEEDS_MANUFACTURING_ORDERS] = $cacheItemCreationDate->get();
        }else {
            /** @var ManufacturingOrder $manufacturingOrders */
            $manufacturingOrders = $cacheItemManufacturing->get();
        }
        //endregion
        //endregion
        //region On génère les données graphiques produits
        $productChartsData = $this->generateProductChartsData($sellingItems, $manufacturingOrders, $stocks, $products);
        $productsData = [];
        foreach ($products as $prod) {
            $productsData[] = $this->generateProductData($prod, $productChartsData, $stocks);
        }
        $productChartsData = $this->finalizeProductChartsData($productChartsData);
        //endregion
        
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
                //Si il n'y a aucun besoin Produit, on zappe
                if (count($ofNeedsData)===0 && count($newOFNeedsData) === 0) {
                    $date = date('d/m/Y');
                    $componentsNeeds[$componentId] = [$date => 0];
                    continue;
                }
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
        //region On tri le tableau componentsNeeds par date
        foreach ($componentsNeeds as $componentId => $componentNeeds) {
            //les dates sont sous forme de chaine texte dd/mm/yyyy
            uksort($componentNeeds, function ($a, $b) {
                $dateA = DateTime::createFromFormat('d/m/Y', $a);
                $dateB = DateTime::createFromFormat('d/m/Y', $b);
                return $dateA > $dateB;
            });
            $componentsNeeds[$componentId] = $componentNeeds;
        }
        //endregion
        
        //region On récupère les ids des composants
        $componentIds = array_keys($componentsNeeds);
        //endregion
        //region Récupération des données composants
        $cacheCreationDateComponent = $cache->getItem(self::API_NEEDS_CREATION_DATE_COMPONENT);
        $cacheComponents = $cache->getItem(self::API_NEEDS_COMPONENTS);
        if (!$cacheComponents->isHit()) {
            $filteredComponents = $this->componentRepository->findBy(['deleted' => false, 'id' => $componentIds]);
            $cacheComponents->set($filteredComponents);
            $cache->save($cacheComponents);
            $cacheCreationDateComponent->set(date('Y-m-d H:i:s'));
            $cacheCreationDatesComponent[self::API_NEEDS_COMPONENTS] = $cacheItemCreationDateComponent->get();
        } else {
            /** @var Component $filteredComponents */
            $filteredComponents = $cacheComponents->get();
        }
        //endregion
        //region Vérifie si les données ComponentStock sont en cache
        $cacheItemComponentStock = $cache->getItem(self::API_NEEDS_STOCKS_COMPONENT);
        if (!$cacheItemComponentStock->isHit()) {
            $filteredStocks = $this->componentStockRepository->findBy(['deleted' => false, 'jail' => false]);
            $cacheItemComponentStock->set($filteredStocks);
            $cache->save($cacheItemComponentStock);
            $cacheItemCreationDateComponent->set(date('Y-m-d H:i:s'));
            $cacheCreationDatesComponent[self::API_NEEDS_STOCKS_COMPONENT] = $cacheItemCreationDateComponent->get();
        } else {
            /** @var ComponentStock $filteredStocks */
            $filteredStocks = $cacheItemComponentStock->get();
        }
        //endregion
        //region Vérifie si les données PurchaseItem sont en cache
        $cachePurchaseItem = $cache->getItem(self::API_NEEDS_PURCHASE_ITEM);
        if (!$cachePurchaseItem->isHit()) {
            $purchaseItems = $this->purchaseItemRepository->findBy(['embState.state' => ['forecast', 'agreed', 'partially_received'], 'embBlocker.state' => ['enabled', 'delayed'], 'deleted' => false]);
            $cachePurchaseItem->set($purchaseItems);
            $cache->save($cachePurchaseItem);
            $cacheItemCreationDateComponent->set(date('Y-m-d H:i:s'));
            $cacheCreationDatesComponent[self::API_NEEDS_PURCHASE_ITEM] = $cacheItemCreationDateComponent->get();
        } else {
            /** @var PurchaseItem $purchaseItems */
            $purchaseItems = $cachePurchaseItem->get();
        }
        //endregion
        
        //region ## $totalManufacturingQuantity $totalOnGoingPurchaseQuantity ## On établit la base de temps commune pour les componentsNeeds, et les purchaseItems
        $componentTimeBase = [];
        $totalManufacturingQuantity = [];
        foreach ($componentsNeeds as $componentId => $componentNeeds) {
            foreach ($componentNeeds as $date => $quantity) {
                if (!isset($totalManufacturingQuantity[$componentId])) {
                    $totalManufacturingQuantity[$componentId] = 0;
                }
                $totalManufacturingQuantity[$componentId] += $quantity;
                if (!in_array($date, $componentTimeBase)) {
                    $componentTimeBase[$componentId][] = $date;
                }
            }
        }
        dump([
            'totalManufacturingQuantity' => $totalManufacturingQuantity,
        ]);
        $totalOnGoingPurchaseQuantity = [];
        foreach ($purchaseItems as $purchaseItem) {
            $purchaseItemComponent = $purchaseItem->getItem()->getId();
            //Si l'id du composant n'est pas dans componentIds, on zappe
            if (!in_array($purchaseItemComponent, $componentIds)) {
                continue;
            }
            $purchaseItemDate = $purchaseItem->getConfirmedDate()->format('d/m/Y');
            if (!isset($totalOnGoingPurchaseQuantity[$purchaseItemComponent])) {
                $totalOnGoingPurchaseQuantity[$purchaseItemComponent] = 0;
            }
            $totalOnGoingPurchaseQuantity[$purchaseItemComponent] += $purchaseItem->getConfirmedQuantity()->getValue();
            $totalOnGoingPurchaseQuantity[$purchaseItemComponent] -= $purchaseItem->getReceivedQuantity()->getValue();
            if (!in_array($purchaseItemDate, $componentTimeBase)) {
                $componentTimeBase[$purchaseItemComponent][] = $purchaseItemDate;
            }
        }
        //On ajoute à totalOngoingPurchaseQuantity les valeurs pour les componentIds qui n'ont pas de purchaseItems et on positionne la quantité à 0
        foreach ($componentIds as $componentId) {
            if (!isset($totalOnGoingPurchaseQuantity[$componentId])) {
                $totalOnGoingPurchaseQuantity[$componentId] = 0;
            }
        }
        // On trie la base de temps
        foreach ($componentTimeBase as $componentId => $componentTime) {
            usort($componentTime, function ($a, $b) {
                $dateA = DateTime::createFromFormat('d/m/Y', $a);
                $dateB = DateTime::createFromFormat('d/m/Y', $b);
                return $dateA <=> $dateB;
            });
            $componentTimeBase[$componentId] = $componentTime;
        };
        //endregion
        
        //region ## $stockQuantities[$stockComponentId] ## Itérer sur les stocks filtrés pour calculer les sommes des quantités de stock 
        /** var ComponentStock $filteredStock */
        $stockQuantities = [];
        foreach($componentIds as $componentId) {
            $stockQuantities[$componentId] = 0;
        }
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

        //region A partir de la base de temps pour chaque composant, on calcule l'évolution des stocks
        // On récupère les id de composant à partir de la base de temps
        $componentStocksProgress = [];
        foreach($componentIds as $componentId) {
            $currentTimeBase = $componentTimeBase[$componentId];
            $componentStocksProgress[$componentId] = [];
            // On initialise le stockProgress à la valeur du stock courant
            if (isset($stockQuantities[$componentId])) {
                $stockProgress = $stockQuantities[$componentId];
            } else {
                $stockProgress = 0;
            }
            // On parcourt les dates de la base de temps
            foreach ($currentTimeBase as $date) {
                //On retire les quantités nécessaires à la fabrication des produits
                $stockProgress -= $componentsNeeds[$componentId][$date] ?? 0;
                //On ajoute les quantités reçues des commandes fournisseurs
                foreach ($purchaseItems as $purchaseItem) {
                    $purchaseItemComponent = $purchaseItem->getItem()->getId();
                    $purchaseItemDate = $purchaseItem->getConfirmedDate()->format('d/m/Y');
                    if ($purchaseItemComponent === $componentId && $purchaseItemDate === $date) {
                        $stockProgress += $this->calculateConvertQuantity($purchaseItem->getConfirmedQuantity());
                        //Suppression des quantités reçues des commandes fournisseurs car ils sont déjà dans le stock
                        $stockProgress -= $purchaseItem->getReceivedQuantity()->getValue();
                        //TODO: gérer l'unité préférée associé au composant
                    }
                }
                //On ajoute le stockProgress à la date courante
                $componentStocksProgress[$componentId][$date] = $stockProgress;
            }
            //On ajoute le total des stocks courant le jour précédent le premier jour de la base de temps
            $previousDate = DateTime::createFromFormat('d/m/Y', $currentTimeBase[0]);
            $previousDate->sub(new DateInterval('P1D'));
            $previousDateStr = $previousDate->format('d/m/Y');
            //On ajoute la date en premier dans la base de temps
            array_unshift($componentTimeBase[$componentId], $previousDateStr);
            //On ajoute le stock courant en premier dans le tableau de stockProgress
            $componentStocksProgress[$componentId][$previousDateStr] = $stockQuantities[$componentId];
        }
        // On trie la base de temps et les componentStocksProgress selon les mêmes index
        foreach ($componentTimeBase as $componentId => $componentTime) {
            usort($componentTime, function ($a, $b) {
                $dateA = DateTime::createFromFormat('d/m/Y', $a);
                $dateB = DateTime::createFromFormat('d/m/Y', $b);
                return $dateA <=> $dateB;
            });
            $componentTimeBase[$componentId] = $componentTime;
            $currentComponentStockProgress = $componentStocksProgress[$componentId];
            uksort($currentComponentStockProgress, function ($a, $b){
                $dateA = DateTime::createFromFormat('d/m/Y', $a);
                $dateB = DateTime::createFromFormat('d/m/Y', $b);
                return $dateA <=> $dateB;
            });
            $componentStocksProgress[$componentId] = $currentComponentStockProgress;
        };
        //endregion
        
        //region ## $stocksMinimum[$componentId] ## On génère les données de stockMinimum pour le composant et on l'itère sur la base de temps
        $componentsStockMinimum = [];
        $stocksMinimum = [];
        foreach ($componentTimeBase as $componentId => $componentTime) {
            // On récupère le stockMinimum pour le composant courant dans $filteredComponents
            $currentFilteredComponents = array_filter($filteredComponents, function ($component) use ($componentId) {
                return $component->getId() === $componentId;
            });
            $currentComponent = array_values($currentFilteredComponents)[0];
            // $currentComponent = $filteredComponents[$componentId];
            $stockMinimum = $currentComponent->getMinStock()->getValue();
            $stocksMinimum[$componentId] = $stockMinimum;
            foreach ($componentTime as $date) {
                $componentsStockMinimum[$componentId][$date] = $stockMinimum;
            }
        }
        //endregion
        
        //region Calcul des besoins de commande fournisseur pour chaque composant
        $componentsPurchaseNeeds = [];

        foreach ($componentTimeBase as $componentId => $componentTime) {
            $maximumToOrder = $stocksMinimum[$componentId] + $totalManufacturingQuantity[$componentId] - $stockQuantities[$componentId] - $totalOnGoingPurchaseQuantity[$componentId];

            if ($maximumToOrder < 0) {
                $maximumToOrder = 0;
            }
            $componentsPurchaseNeeds[$componentId] = [];
            $cumulatedPurchaseNeeds = 0;
            foreach ($componentTime as $date) {
                // On compare le stockProgress avec le stockMinimum et lorsque stockProgress est en dessous de stockMinimum, alors on ajoute la différence en tant que nouveau besoin de commande fournisseur
                if (isset($componentStocksProgress[$componentId])) {
                    $stockProgress = $componentStocksProgress[$componentId][$date];
                } else {
                    $stockProgress = 0;
                }
                $stockMinimum = $componentsStockMinimum[$componentId][$date];
                if (($stockProgress + $cumulatedPurchaseNeeds) < $stockMinimum && $cumulatedPurchaseNeeds < $maximumToOrder) {
                    $quantity = $stockMinimum - ($stockProgress + $cumulatedPurchaseNeeds);
                    //On positionne la date de besoin de commande fournisseur à 1 mois avant la date courante en utilisant la constante MANUFACURING_ORDER_DELAY_BEFORE_CUSTOMER_RECEPTION
                    $dateObject = DateTime::createFromFormat('d/m/Y', $date); //new DateInterval(self::MANUFACURING_ORDER_DELAY_BEFORE_CUSTOMER_RECEPTION)
                    $dateObject->sub(new DateInterval(self::PURCHASE_ORDER_DELAY_BEFORE_COMPONENT_USE));
                    //On vérifie si la quantité est positive
                    if ($quantity > 0) {
                        $quantity = $cumulatedPurchaseNeeds + $quantity > $maximumToOrder ? $maximumToOrder - $cumulatedPurchaseNeeds : $quantity;
                        $componentsPurchaseNeeds[$componentId][$dateObject->format('d/m/Y')] = $quantity;
                        $cumulatedPurchaseNeeds += $quantity;
                    }
                }
            }
        }
        
        //endregion
        
        //region génération des données des graphiques composants
        $componentChartData = [];
        $componentfield = [];
        foreach ($componentIds as $componentId) {
            $currentComponent = array_values(array_filter($filteredComponents, function ($component) use ($componentId) {
                return $component->getId() === $componentId;
            }))[0];
            $component = $currentComponent;
            $minStock = $component->getMinStock()->getValue();
            $totalCurrentStock = $stockQuantities[$componentId] ?? 0;
            $id = $component->getId();
            $componentName = $component->getName();
            $componentCode = $component->getCode();
            $componentUnit = $component->getUnit()->getCode();
            $componentStockProgress = array_values($componentStocksProgress[$componentId] ?? []);
            $componentStockMinimum = array_values($componentsStockMinimum[$componentId] ?? []);
            $componentPurchaseNeeds = $componentsPurchaseNeeds[$componentId] ?? [];
            $currentFilteredPurchaseItems = array_filter($purchaseItems, function ($purchaseItem) use ($componentId) {
                return $purchaseItem->getItem()->getId() === $componentId;
            });
            $currentPurchaseItems = array_map(function ($purchaseItem) {
                return $purchaseItem->getConfirmedQuantity()->getValue() - $purchaseItem->getReceivedQuantity()->getValue();
            }, $currentFilteredPurchaseItems);
            //$currentPurchaseItems = $purchaseItems[$componentId] ?? [];
            $totalComponentPurchaseQuantity = array_sum($currentPurchaseItems);
            $componentChartData[] = [
                'componentId' => $id,
                'componentName' => $componentName,
                'componentCode' => $componentCode,
                'componentUnit' => $componentUnit,
                'stockProgress' => $componentStockProgress,
                'stockMinimum' => $componentStockMinimum,
                'labels' => $componentTimeBase[$componentId]
            ];
            $componentfield[] = [
                'componentId' => $id,
                'componentName' => $componentName,
                'componentCode' => $componentCode,
                'componentUnit' => $componentUnit,
                'minStock' => $minStock,
                'totalManufacturingQuantity' => $totalManufacturingQuantity[$componentId],
                'totalCurrentStock' => $totalCurrentStock,
                'totalComponentPurchaseQuantity' => $totalComponentPurchaseQuantity,
                'purchaseNeeds' => $componentPurchaseNeeds
            ];
        }
        //endregion
        
        //region mise en cache ?
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
        
        $cacheCreationDates = $cache->getItem(self::API_NEEDS_CREATION_DATE_PRODUCT)->get();
        
        if($cacheItemCreationDateComponent->isHit() === false) {
            if($cacheItemCreationDateComponent->getMetadata() === []) {
                $cacheItemCreationDateComponent->set($cacheCreationDatesComponent);
                $value = $cacheItemCreationDateComponent->get();
                if (is_array($value) && count($value) >= 1) {
                    $cache->save($cacheItemCreationDateComponent);  
                } 
            }
        }  
        $cacheCreationDatesComponent = $cache->getItem(self::API_NEEDS_CREATION_DATE_COMPONENT)->get();
        //endregion
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

    private function calculateConvertQuantity(Measure $quantity): float
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
        //region On initialise productChartData avec les stocks actuels à la date du jour
        $productChartsData = [];
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
                'manufacturingOrders' => [],
                'confirmed_quantity_value' => [],
                'sellingOrderItemsInfo' => [],
                'quantity_requested_value' => [],
                'manufacturingOrderItemsInfo' => [],
                'manufacturingOrderItems' => [],
            ];
        }
        //endregion

        // Traitement des ventes (remplis productChartsData[productId][confirmed_quantity_value])
        $this->processSellingItems($sellingItems, $productChartsData);

        // Traitement des items de manufacturing (remplis aussi ? productChartsData[productId][confirmed_quantity_value])
        $this->processManufacturingOrders($manufacturingOrders, $productChartsData);


        // Ordonner les labels et les stocks progress
        $this->sortLabelsAndStockProgress($productChartsData, $products);

        // On ajoute le stock courant à la 1ère date pour chaque produit
        // Traitement des stocks (remplis aussi ? productChartsData[productId][stockProgress])
        $this->processStocks($stocks, $productChartsData);

        // Cumuler les stocks progress pour chaque produit en y ajoutant le stock courant à chaque date
        $this->cumulateStockProgress($productChartsData, $stocks);
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

    private function cumulateStockProgress(array &$productChartsData, array $stocks): void
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
        // On recupère le stock courant
        $totalStocks = [];
        foreach ($stocks as $stock) {
            $productId = $stock->getItem()->getId();
            $quantityValue = $stock->getQuantity()->getValue();
            if (!isset($totalStocks[$productId])) {
                $totalStocks[$productId] = 0;
            }
            $totalStocks[$productId] += $quantityValue;
        }
        foreach ($productChartsData as &$productChartData) {
            // On récupère la date du 1er stockprogress
            $date = $productChartData['labels'][0];
            $previousDate = \DateTime::createFromFormat('d/m/Y', $date);
            $previousDate->sub(new DateInterval('P1D'));
            $previousDateStr = $previousDate->format('d/m/Y');
            $productId = $productChartData['productId'];
            if (!isset($totalStocks[$productId])) {
                $totalStocks[$productId] = 0;
            }
            $totalStock = $totalStocks[$productId];
            //on ajoute le stock courant à la date précédente
            $productChartData['stockProgress'] = array_merge([$totalStock], $productChartData['stockProgress']);
            $productChartData['labels'] = array_merge([$previousDateStr], $productChartData['labels']);
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


    private function sortLabelsAndStockProgress(array &$productChartsData, array $products): void
    {
        foreach ($productChartsData as $index => &$product) {
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
                'quantity_requested_value' => [$date => $quantity],
                'manufacturingOrderItemsInfo' => [$date => "+".$quantity." Produits (OF)"],
                'productId' => $productId
            ];
        } else {
            $foundProductChartData['labels'][] = $date;
            $foundProductChartData['manufacturingOrderItems'][$date][] = $manufacturingOrder;
            if (!isset($foundProductChartData['manufacturingOrderItemsInfo'][$date])) {
                $foundProductChartData['manufacturingOrderItemsInfo'][$date] = "+".$quantity." Produits (OF)";
                $foundProductChartData['quantity_requested_value'][$date] = $quantity;
            }
            else {
                $foundProductChartData['manufacturingOrderItemsInfo'][$date] .= "+" . $quantity . " Produits (OF)";
                $foundProductChartData['quantity_requested_value'][$date] += $quantity;
            }
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
        $sellingOrderItemQuantityLeft = $sellingItem->getConfirmedQuantity()->getValue() - $sellingItem->getSentQuantity()->getValue();
        $sellingOrderItemInfo = "-".$sellingOrderItemQuantityLeft." Produits (Expédition)";
        if (empty($foundProductChartData)) {
            $foundProductChartData[] = [
                'labels' => [$date],
                'stockMinimum' => [],
                'stockProgress' => [],
                'sellingOrderItems' => [$date => $sellingOrderItemQuantityLeft],
                'sellingOrderItemsInfo' => [$date => $sellingOrderItemInfo],
                'sellingOrderItemsDetails' => [$date => $sellingItem],
                'productId' => $productId
            ];
            return $foundProductChartData;
        } else {
            $foundProductChartData['labels'][] = $date;
            $foundProductChartData['confirmed_quantity_value'][$date] = $quantity;
            if (isset($foundProductChartData['sellingOrderItems'][$date])) {
                $foundProductChartData['sellingOrderItems'][$date] += $sellingOrderItemQuantityLeft;
                $foundProductChartData['sellingOrderItemsInfo'][$date] .= $sellingOrderItemInfo;
                $foundProductChartData['sellingOrderItemsDetails'][$date] = $sellingItem;
            }
            else {
                $foundProductChartData['sellingOrderItems'][$date] = $sellingOrderItemQuantityLeft;
                $foundProductChartData['sellingOrderItemsInfo'][$date] = $sellingOrderItemInfo;
                $foundProductChartData['sellingOrderItemsDetails'][$date] = $sellingItem;
            }
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
    private function generateNewOFNeedsData(Product $prod, array $productChartsData, $totalSellingQuantity, $totalOnGoingManufacturing, $allStock): array
    {
        $newOFNeeds = [];
        $minStock = $prod->getMinStock()->getValue();
        $maximumToOrder = $minStock + $totalSellingQuantity - $allStock -$totalOnGoingManufacturing;
        if ($maximumToOrder < 0) {
            $maximumToOrder = 0;
        }
        //On récupère les données de productChartsData pour le produit $prod
        $currentProductChartsData = array_values(array_filter($productChartsData, function ($productChartData) use ($prod) {
            return $productChartData['productId'] === $prod->getId();
        }))[0];
        $cumulatedNewOFQuantity = 0;
        foreach ($currentProductChartsData['labels'] as $index => $date) {
            if (isset($currentProductChartsData['stockProgress'][$index]) && isset($currentProductChartsData['stockMinimum'][$index])) {
                $stockProgress = $currentProductChartsData['stockProgress'][$index];
                $stockMinimum = $currentProductChartsData['stockMinimum'][$index];
                if ($stockProgress + $cumulatedNewOFQuantity < $stockMinimum && $maximumToOrder > $cumulatedNewOFQuantity) {
                    $dateTime = DateTime::createFromFormat('d/m/Y', $date);
                    if ($dateTime) {
                        $dateTime->sub(new DateInterval(self::MANUFACURING_ORDER_DELAY_BEFORE_CUSTOMER_RECEPTION));
                        $quantity = $stockMinimum - $stockProgress - $cumulatedNewOFQuantity;
                        // On vérifie si la quantité est positive
                        if ($quantity > 0) {
                            $quantity = $quantity > $maximumToOrder ? $maximumToOrder : $quantity;
                            $newOFNeeds[] = [
                                'date' => $dateTime->format('d/m/Y'),
                                'quantity' => $quantity,
                                'details' => $currentProductChartsData
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
        $allStock = $this->generateProductStockData($prod, $stocks);
        $totalSellingQuantity = $this->generateMaxQuantityData($prod, $productChartsData);
        $totalOnGoingManufacturing = $this->generateTotalOnGoingManufacturing($prod, $productChartsData);
        $newOFNeeds = $this->generateNewOFNeedsData($prod, $productChartsData, $totalSellingQuantity, $totalOnGoingManufacturing, $allStock);
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