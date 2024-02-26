<?php

namespace App\Controller\Needs;

use DateTime;
use DateInterval;
use App\Entity\Needs\Needs;
use Psr\Log\LoggerInterface;
use App\Entity\Management\Unit;
use App\Service\MeasureManager;
use App\Service\MeasureHydrator;
use App\Entity\Embeddable\Measure;
use App\Entity\Project\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Management\UnitRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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

ini_set('memory_limit', '4096M');
/**
 * @ApiResource(
 *     collectionOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/api/needs"
 *         }
 *     },
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/api/needs/{id}"
 *         }
 *     }
 * )
 */
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
    private UnitRepository $unitRepository;
    private MeasureManager $measureManager;
    private MeasureHydrator $measureHydrator;

    public function __construct(
        private readonly EntityManagerInterface $em,
        ProductItemRepository $productItemRepository,
        ProductStockRepository $productStockRepository,
        ProductRepository $productRepository,
        ProductCustomerRepository $productCustomerRepository,
        PurchaseItemRepository $purchaseItemRepository,
        ManufacturingProductItemRepository $manufacturingProductItemRepository,
        NomenclatureRepository $nomenclatureRepository,
        ComponentRepository $componentRepository,
        ComponentStockRepository $componentStockRepository,
        UnitRepository $unitRepository,
        MeasureManager $measureManager,
        MeasureHydrator $measureHydrator,

        private LoggerInterface $logger
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
        $this->unitRepository = $unitRepository;
        $this->measureManager = $measureManager;
        $this->measureHydrator = $measureHydrator;
    }

    /**
     * @Route("/api/needs", name="needs", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $products = $this->productRepository->findByEmbBlockerAndEmbState();
        $productcustomers = $this->productCustomerRepository->findAll();
        $sellingItems = $this->productItemRepository->findByEmbBlockerAndEmbState();
        $manufacturingOrders = $this->manufacturingProductItemRepository->findByEmbBlockerAndEmbState();
        $stocks = $this->productStockRepository->findAll();
        $productChartsData = $this->generateProductChartsData($sellingItems, $manufacturingOrders, $stocks);
        $productFamilies = $this->getProductFamilies($products);
        $customersData = $this->generateCustomersData($productcustomers);

        foreach ($products as $prod) {
            $productId = $prod->getId();
            $minStock = $this->generateMinStock($prod);
            $this->updateStockMinimumForProduct($productChartsData, $productId, $minStock);
            $productsData[$productId] = $this->generateProductData($prod, $productChartsData, $stocks);
        }

        $productChartsData = $this->finalizeProductChartsData($productChartsData);
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
        $products = $this->productRepository->findByEmbBlockerAndEmbState();
        $sellingItems = $this->productItemRepository->findByEmbBlockerAndEmbState();
        $manufacturingOrders = $this->manufacturingProductItemRepository->findByEmbBlockerAndEmbState();
        $stocks = $this->productStockRepository->findAll();
        $productChartsData = $this->generateProductChartsData($sellingItems, $manufacturingOrders, $stocks);
        $filteredStocks = $this->componentStockRepository->findStocksByCriteria();
        $purchaseItems = $this->purchaseItemRepository->findByEmbBlockerAndEmbState();

        $invertedMatrix = [];
        
        $componentChartData = [];   

        $componentfield = [];

        foreach ($products as $prod) {
            $productId = $prod->getId();
            $nomenclatures = $this->nomenclatureRepository->findByProductId($productId);
            $minStock = $this->generateMinStock($prod);
            $this->updateStockMinimumForProduct($productChartsData, $productId, $minStock);
            $productsData[$productId] = $this->generateProductData($prod, $productChartsData, $stocks);
            $newOFNeedsData = $productsData[$productId]['newOFNeeds'];
            $maxQuantity = $productsData[$productId]['productToManufacturing'];
            if (!empty($maxQuantity)) {
                $this->processNewOFNeedsData($newOFNeedsData, $nomenclatures, $productId, $productChartsData, $invertedMatrix);
            }
        }
        // Itérer sur les stocks filtrés pour calculer les sommes des quantités de stock
        foreach ($filteredStocks as $filteredStock) {
            $stockComponentId = $filteredStock->getItem()->getId();
            $stockComponentQuantity = $this->calculateStockQuantity($filteredStock, $stockQuantities);

            if (!isset($stockQuantities[$stockComponentId])) {
                $stockQuantities[$stockComponentId] = 0;
            }
            // Ajouter la quantité de stock à la somme existante pour ce stockComponentId
            $stockQuantities[$stockComponentId] += $stockComponentQuantity;
        }
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

        foreach ($uniqueDates as $componentId => $dates) {
           $uniqueDatesForComponent = array_unique($dates);
           usort($uniqueDatesForComponent, function($a, $b) {
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

        //$stockMinimum = $this->getStockMinPerDate($ordersForComponent, $uniqueDatesForComponent);
        $components = $this->componentRepository->findById($componentId);
        $stockMinimum =  $components->getMinStock()->getValue();

        $stockMinimumPerDate = [];
        foreach ($uniqueDatesForComponent as $date) {
            $stockMinimumPerDate[] = $stockMinimum;
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
        $componentStockDefault = [];

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
                    $purchaseQuantity = $purchaseItem->getConfirmedQuantity()->getValue();
                    $purchaseCode = $purchaseItem->getConfirmedQuantity()->getCode();
                   // $ReceiptQuantity = $purchaseItem->getReceiptQuantity()->getValue();
                   // $ReceiptCode = $purchaseItem->getReceiptQuantity()->getCode();
                    $remainingQuantity = $purchaseQuantity - $ReceiptQuantity;
                }
        
                $purchaseQuantityPerComponent[$date] = $remainingQuantity;
                $purchaseCodePerComponent[$date] = $purchaseCode;
            }
            $purchaseQuantityPerDate[$componentId] = $purchaseQuantityPerComponent;
            $purchaseCodePerDate[$componentId] = $purchaseCodePerComponent;
        
            $stockProgressPerDateForComponent[] = $stockTotalPerDate[$date] - $needsPerDate[$date] + $purchaseQuantityPerComponent[$date];
        
            if (isset($stockMinimumPerDate[$date]) && isset($stockProgressPerDateForComponent[$date])) {
                if ($stockProgressPerDateForComponent[$date] < $stockMinimumPerDate[$date]) {
                    $componentStockDefault[] = ['date' => $date];
                }
            }
        }
        


            $componentChartData[$componentId] = [
                'labels' => $uniqueDatesForComponent,
                'stockMinimum' => $stockMinimumPerDate,
               // 'stockTotal' => $stockTotalPerDate,
               // 'purchaseQuantity' => $purchaseQuantityPerComponent,
               // 'unitePurchase' => $purchaseCodePerComponent,
               // 'value_matrice' => $needsPerDate,
               // 'unite_matrice' => $unitsPerDate,
                'stockProgress' => $stockProgressPerDateForComponent
            ];
        
            $componentfield[$componentId] = [
                'componentStockDefaults' => $componentStockDefault,
                'family' => '',
                'ref' => 'code'

            ];
            $uniqueDates = [];

    }


    /*$components = [
        1 => [
            'componentStockDefaults' => [
                1 => ['date' => '2022-07-12'],
                2 => ['date' => '2022-02-08']
            ],
            'family' => [1, 2, 3],
            'newSupplierOrderNeeds' => [
                1 => ['date' => '2022-05-12', 'quantity' => 200],
                2 => ['date' => '2022-02-08', 'quantity' => 100]
            ],
            'ref' => 1
        ],*/
    
        
        return new JsonResponse([
            'componentChartData' => $componentChartData,
            'component' => $componentfield

        ]);
    }
    

    function getUnitsPerDate($orders, $uniqueDates) {
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
    

    function getNeedsPerDate($orders, $uniqueDates) {
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

    private function calculateStockQuantity($filteredStock, &$stockQuantities): float
    {
        $stockComponentId = $filteredStock->getItem()->getId();
        $stockcomponentQuantity = $filteredStock->getQuantity()->getValue();
        $unit2 = $filteredStock->getQuantity()->getUnit()->getParent();
    
        if ($unit2 !== null) {
            $unit1 = $filteredStock->getQuantity()->getUnit();
    
            $denominatorUnit = $filteredStock->getQuantity()->getDenominatorUnit();
            if ($denominatorUnit !== null) {
                $convertedMeasure = $this->measureManager->convertMeasure($filteredStock->getQuantity(), $unit2, $denominatorUnit->getParent());
            }
            $convertedMeasure = $this->measureManager->convertMeasure($filteredStock->getQuantity(), $unit2);
    
            $stockcomponentQuantity = $convertedMeasure->getValue();
        }
    
        return $stockcomponentQuantity;
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

private function generateProductChartsData(array $sellingItems, array $manufacturingOrders, array $stocks): array
{
    $productChartsData = [];

    // Traitement des ventes
    $this->processSellingItems($sellingItems, $productChartsData);

    // Traitement des items de manufacturing
    $this->processManufacturingOrders($manufacturingOrders, $productChartsData);

    // Traitement des stocks
    $this->processStocks($stocks, $productChartsData);

    // Ordonner les labels
    $this->sortLabels($productChartsData);

    return $productChartsData;
}

private function processSellingItems(array $sellingItems, array &$productChartsData): void
{
    foreach ($sellingItems as $sellingItem) {
        $productId = $sellingItem->getItem()->getId();
        $confirmedDate = $sellingItem->getConfirmedDate()->format('d/m/Y');
        $confirmedQuantityValue = $sellingItem->getConfirmedQuantity()->getValue();
        $productChartsData = $this->updateProductChartsData($productChartsData, $productId, $confirmedDate, $confirmedQuantityValue);
    }
}

private function processManufacturingOrders(array $manufacturingOrders, array &$productChartsData): void
{
    foreach ($manufacturingOrders as $manufacturingOrder) {
        $productId = $manufacturingOrder->getProduct()->getId();
        $manufacturingDate = $manufacturingOrder->getManufacturingDate()->format('d/m/Y');
        $quantityRequestedValue = $manufacturingOrder->getQuantityRequested()->getValue();
        $productChartsData = $this->updateProductChartsData($productChartsData, $productId, $manufacturingDate, $quantityRequestedValue);
    }
}

private function processStocks(array $stocks, array &$productChartsData): void
{
    foreach ($stocks as $stock) {
        $productId = $stock->getItem()->getId();
        if (array_key_exists($productId, $productChartsData)) {
            $quantityValue = $stock->getQuantity()->getValue();
            $this->updateStockProgress($productChartsData, $productId, $quantityValue);
        }
    }
}

private function updateStockProgress(array &$productChartsData, int $productId, float $quantityValue): void
{
    if (!array_key_exists($productId, $productChartsData)) {
        return;
    }

    $stockProgress = &$productChartsData[$productId]['stockProgress'];

    foreach ($stockProgress as &$value) {
        $value += $quantityValue;
    }
}


private function sortLabels(array &$productChartsData): void
{
    foreach ($productChartsData as &$product) {
        $product['labels'] = array_values(array_unique($product['labels']));
        sort($product['labels']);

        // Initialisation de 'stockProgress' comme tableau pour chaque date
        foreach ($product['labels'] as $date) {
            $product['stockProgress'][$date] = 0;

            $confirmedQuantity = $product['confirmed_quantity_value'][$date] ?? 0;
            $product['stockProgress'][$date] -= $confirmedQuantity;

            $quantityRequested = $product['quantity_requested_value'][$date] ?? 0;
            $product['stockProgress'][$date] += $quantityRequested;
        }

        // Ordonner les labels
        usort($product['labels'], function ($a, $b) {
            $dateA = \DateTime::createFromFormat('d/m/Y', $a);
            $dateB = \DateTime::createFromFormat('d/m/Y', $b);

            return $dateA <=> $dateB;
        });
    }
}


private function updateProductChartsData(array $productChartsData, int $productId, string $date, float $quantity): array
{
    $productChartsData[$productId] ??= ['labels' => [], 'stockMinimum' => [], 'stockProgress' => []];

    $productChartsData[$productId]['labels'][] = $date;
    $productChartsData[$productId]['confirmed_quantity_value'][$date] = $quantity;

    return $productChartsData;
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

    if (array_key_exists($prod->getId(), $productChartsData)) {
        $stockDefault = [];
        $maxQuantity = null;

        foreach ($productChartsData[$prod->getId()]['labels'] as $date) {
            if (isset($productChartsData[$prod->getId()]['stockProgress'][$date]) && isset($productChartsData[$prod->getId()]['stockMinimum'][$date])) {
                $stockProgress = $productChartsData[$prod->getId()]['stockProgress'][$date];
                $stockMinimum = $productChartsData[$prod->getId()]['stockMinimum'][$date];

                if ($stockProgress < $stockMinimum) {
                    $dateTime = DateTime::createFromFormat('d/m/Y', $date);
                    if ($dateTime) {
                        $dateTime->sub(new DateInterval('P1W'));
                        $quantity = $stockMinimum - $stockProgress;

                        if (!isset($maxQuantity) || $quantity > $maxQuantity) {
                            $maxQuantity = $quantity;
                        }

                        $newOFNeeds[] = [
                            'date' => $dateTime->format('d/m/Y'),
                            'quantity' => $quantity,
                        ];
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
    $maxQuantity = null;

    if (array_key_exists($prod->getId(), $productChartsData)) {
        foreach ($productChartsData[$prod->getId()]['labels'] as $date) {
            if (isset($productChartsData[$prod->getId()]['stockProgress'][$date]) && isset($productChartsData[$prod->getId()]['stockMinimum'][$date])) {
                $stockProgress = $productChartsData[$prod->getId()]['stockProgress'][$date];
                $stockMinimum = $productChartsData[$prod->getId()]['stockMinimum'][$date];

                $quantity = $stockMinimum - $stockProgress;

                if (!isset($maxQuantity) || $quantity > $maxQuantity) {
                    $maxQuantity = $quantity;
                }
            }
        }
    }

    return $maxQuantity;
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

private function generateProductData($prod, $productChartsData, $stocks): array
{
    $components = $this->generateComponentsData($prod);

    $familyId = $prod->getFamily()?->getId();

    $minStock = $prod->getMinStock()->getValue();
    $newOFNeeds = $this->generateNewOFNeedsData($prod, $productChartsData);
    $allStock = $this->generateProductStockData($prod, $stocks);
    $maxQuantity = $this->generateMaxQuantityData($prod, $productChartsData);
    $stockDefault = $this->generateStockDefault($prod, $productChartsData);

    return [
        'component' => $components,
        'family' => $familyId,
        'minStock' => $minStock,
        'newOFNeeds' => $newOFNeeds,
        'productDesg' => $prod->getName(),
        'productRef' => $prod->getCode(),
        'productStock' => $allStock,
        'productToManufacturing' => $maxQuantity,
        'stockDefault' => $stockDefault,
    ];
}

private function finalizeProductChartsData(array $productChartsData): array
{
    foreach ($productChartsData as &$product) {
        $product['stockProgress'] = array_values($product['stockProgress']);
        $product['stockMinimum'] = array_values($product['stockMinimum']);
        unset($product['quantity_requested_value'], $product['confirmed_quantity_value']);
    }

    return $productChartsData;
}

private function updateStockMinimumForProduct(array &$productChartsData, int $productId, float $minStock): void
{
    if (array_key_exists($productId, $productChartsData)) {
        $labels = $productChartsData[$productId]['labels'];
        $productChartsData[$productId]['stockMinimum'] = array_fill_keys($labels, $minStock);
    }
}

}

    /*  $needs = new Needs();
        $needs->setId(1); // Set an arbitrary value for the ID

        $componentChartsData = [
            1 => [
                'labels' => [0.2, 0.3, 0.1, 0.4],
                'stockMinimum' => [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                'stockProgress' => [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
            ],
            2 => [
                'labels' => ['21/11/2017', '21/12/2017', '01/04/2018', '11/05/2019', '11/06/2019'],
                'stockMinimum' => [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                'stockProgress' => [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
            ],
            3 => [
                'labels' => [0.23, 0.3, 0.1, 0.4],
                'stockMinimum' => [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                'stockProgress' => [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
            ],
            4 => [
                'labels' => [0.24, 0.3, 0.1, 0.4],
                'stockMinimum' => [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                'stockProgress' => [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
            ],
            5 => [
                'labels' => [0.25, 0.3, 0.1, 0.4],
                'stockMinimum' => [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                'stockProgress' => [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
            ],
        ];
        // Affectation des données à la propriété componentChartsData de l'instance Needs
        $needs->setComponentChartsData($componentChartsData);
        // Définition des données components
        $components = [
            1 => [
                'componentStockDefaults' => [
                    1 => ['date' => '2022-07-12'],
                    2 => ['date' => '2022-02-08']
                ],
                'family' => [1, 2, 3],
                'newSupplierOrderNeeds' => [
                    1 => ['date' => '2022-05-12', 'quantity' => 200],
                    2 => ['date' => '2022-02-08', 'quantity' => 100]
                ],
                'ref' => 1
            ],
            2 => [
                'componentStockDefaults' => [
                    1 => ['date' => '2022-07-12'],
                    2 => ['date' => '2022-02-08']
                ],
                'family' => [1, 2, 3],
                'newSupplierOrderNeeds' => [
                    1 => ['date' => '2022-05-12', 'quantity' => 200],
                    2 => ['date' => '2022-02-08', 'quantity' => 100]
                ],
                'ref' => 2
            ],
            // ... Ajoutez les autres données comme nécessaire
        ];

        // Affectation des données à la propriété components de l'instance Needs
        $needs->setComponents($components);

        // Définition des données customers
        $customers = [
            1 => [
                'id' => 1,
                'products' => [1, 2, 3, 4],
                'society' => 1
            ],
            // ... Ajoutez les autres données comme nécessaire
        ];

        // Affectation des données à la propriété customers de l'instance Needs
        $needs->setCustomers($customers);

        // Définition des données productChartsData
        $productChartsData = [

            1 => [
                'labels' => [0.2, 0.3, 0.1, 0.4],
                'stockMinimum' => [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                'stockProgress' => [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
            ],
            2 => [
                'labels' => ['21/11/2017', '21/12/2017', '01/04/2018', '11/05/2019', '11/06/2019'],
                'stockMinimum' => [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
                'stockProgress' => [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528]
            ],
            // ... Ajoutez les autres données comme nécessaire
        ];

        // Affectation des données à la propriété productChartsData de l'instance Needs
        $needs->setProductChartsData($productChartsData);

        // Définition des données productFamilies
        $productFamilies = [
            1 => ['familyName' => 'prodFamil1', 
                  'pathName' => 'path1'],
            2 => ['familyName' => 'prodFamil1', 'pathName' => 'path2'],
            // ... Ajoutez les autres données comme nécessaire
        ];

        // Affectation des données à la propriété productFamilies de l'instance Needs
        $needs->setProductFamilies($productFamilies);
        // Définition des données products
        $productsData = [
            1 => [
                'components' => [1, 2],
                'family' => [1, 2, 3],
                'minStock' => 55,
                'newOFNeeds' => [
                    1 => ['date' => '2022-05-12', 'quantity' => '200'],
                    2 => ['date' => '2022-02-08', 'quantity' => '100']
                ],
                'productDesg' => 'd1',
                'productRef' => '4444',
                'productStock' => 1000,
                'productToManufactring' => 1000,
                'stockDefault' => [
                    1 => ['date' => '2022-05-02'],
                    2 => ['date' => '2022-02-08'],
                    3 => ['date' => '2022-03-08']
                ]
            ],
            2 => [
                'components' => [1, 2],
                'family' => [1, 2, 3],
                'minStock' => 75,
                'newOFNeeds' => [
                    1 => ['date' => '2022-05-12', 'quantity' => '200'],
                    2 => ['date' => '2022-02-08', 'quantity' => '100']
                ],
                'productDesg' => 'd1',
                'productRef' => '4444',
                'productStock' => 1000,
                'productToManufactring' => 1000,
                'stockDefault' => [
                    1 => ['date' => '2022-05-02'],
                    2 => ['date' => '2022-02-08'],
                    3 => ['date' => '2022-03-08']
                ]
            ],
            // ... Ajoutez les autres données comme nécessaire
        ];

        // Affectation des données à la propriété products de l'instance Needs
        $needs->setProducts($productsData);



        return $this->json($needs);*/
