<?php
namespace App\Controller\Needs;

use DateTime;
use DateInterval;
use App\Entity\Needs\Needs;
use Psr\Log\LoggerInterface;
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
        $filteredStocks = $this->componentStockRepository->findStocksByCriteria();
        $purchaseItems = $this->purchaseItemRepository->findByEmbBlockerAndEmbState();
        $manufacturingOrders = $this->manufacturingProductItemRepository->findByEmbBlockerAndEmbState();
        $stocks = $this->productStockRepository->findAll();
        $sellingItems = $this->productItemRepository->findByEmbBlockerAndEmbState();
        $productChartsData = $this->generateProductChartsData($sellingItems, $manufacturingOrders, $stocks);
        
        foreach($filteredStocks as $filteredStock){
        $componentId = $filteredStock->getItem()->getId(); 
        $quantity = $filteredStock->getQuantity()->getValue();
        if (isset($componentQuantities[$componentId])) {
            $componentQuantities[$componentId] += $quantity;
        } else {
            $componentQuantities[$componentId] = $quantity;
        }
        }

    // Recherche de la date correspondante pour ce composant
    /* if (isset($datesForComponents[$componentId])) {
        foreach ($datesForComponents[$componentId] as &$dateEntry) {
            if ($dateEntry['date'] === $confirmedDate) {
                $dateEntry['stockValue'] += $confirmedQuantity;
                break;
            }
        }
    }*/
   /* foreach ($purchaseItems as $purchaseItem){
        $confirmedQuantity = $purchaseItem->getConfirmedQuantity()->getValue();
        dump($purchaseItem);
        $purchaseItemId = $purchaseItem->getItem()->getId();
        $confirmedDate = $purchaseItem->getConfirmedDate()->format('d/m/Y');
    }*/

   // $invertedMatrix = [];
   /* foreach ($products as $prod) {
        $productId = $prod->getId();
        $maxQuantity = $this->generateMaxQuantityData($prod, $productChartsData);
        $productsData[$productId] = $this->generateProductData($prod, $productChartsData, $stocks);
        $newOFNeedsData = $this->generateNewOFNeedsData($prod, $productChartsData);
        $nomenclatures = $this->nomenclatureRepository->findByProductId($productId);
    if (!empty($maxQuantity)) {
        foreach ($newOFNeedsData as $newOFNeed) {
            $date = $newOFNeed['date'];
            $product_quantity = $newOFNeed['quantity'];
            foreach ($nomenclatures as $nomenclature) {
                $component = $nomenclature->getComponent();
                $productt = $nomenclature->getProduct();
                //nomenclature
                $uniteNomenclature =  $nomenclature->getQuantity()->getUnit();
                $nomenclature_quantity = $nomenclature->getQuantity()->getValue();
                $unitNomenclature_code = $uniteNomenclature->getCode();

                $needs = $product_quantity * $nomenclature_quantity;
                $components = $this->componentRepository->findById($component->getId());
                //component
                $unitComponent = $components->getUnit();
                $unitComponent_code = $components->getUnit()->getCode();


                if ($productt && $productt->getId() === $productId) {
                    // Créer une clé composite pour chaque composant
                    $compositeKey = $components->getId();
                    //dump($stockComponents);
                    $stockMinimum = $components->getMinStock()->getValue();
                    $sumQuantities = 0;*/

                //$compositeId = $stockComponent->getId();
                // Ajouter la date au tableau correspondant au composant
               /* if (!isset($datesForComponents[$compositeKey])) {
                  $datesForComponents[$compositeKey] = [];
                }

                $datesForComponents[$compositeKey][] = [
                    'date' => $date,
                    'stockMinimum' => $stockMinimum,
                    'stockValue' =>''
                ];*/
                
            
                
                   /* if (!isset($invertedMatrix[$compositeKey]['Needs'])) {
                        $invertedMatrix[$compositeKey]['Needs'] = [];

                    }*/
                    // Indicateur pour vérifier si la date existe déjà
                  //  $dateExists = false;

                   /* foreach ($invertedMatrix[$compositeKey]['Needs'] as &$orderNeed) {
                        if ($orderNeed['date'] === $date) {
                            // Si Unite et unitComponent sont égales, ajouter $needs
                            if ($unitNomenclature_code === $unitComponent_code) {

                                $orderNeed['unite'] = $unitComponent_code;
                                $orderNeed['quantity_nomenclature'] .= $nomenclature_quantity . ' __ ';
                                $orderNeed['quantity_produit'] .=  $product_quantity . ' __ ';
                                $orderNeed['needs'] += $needs;
                            } else {
                                $convertedMeasure = $this->measureManager->ConvertAfterMul($uniteNomenclature, $nomenclature_quantity, $unitComponent, $product_quantity);

                                $orderNeed['unite'] = $convertedMeasure["convertedMeasure1"]->getCode();
                                $orderNeed['quantity_nomenclature'] .= $convertedMeasure["convertedMeasure1"]->getValue() . ' / ';
                                $orderNeed['quantity_produit'] .=  $convertedMeasure["convertedMeasure2"]->getValue() . ' / ';
                                $orderNeed['needs'] += $convertedMeasure["totalMeasure"]->getValue();
                            }
                            // Définir l'indicateur à true
                            $dateExists = true;
                            break;
                        }
                    }*/
                    // Si la date n'existe pas, ajouter une nouvelle entrée
                /* if (!$dateExists) {
                        if ($unitNomenclature_code === $unitComponent_code) {
                            $invertedMatrix[$compositeKey]['Needs'][] = [
                                'date' => $date,
                                'quantity_nomenclature' => $nomenclature_quantity . ' __ ',
                                'quantity_produit' => $product_quantity . ' __ ',
                                'needs' => $needs,
                                'unite' => $unitComponent_code,
                            ];
                        } else {

                            $convertedMeasure = $this->measureManager->ConvertAfterMul($uniteNomenclature, $nomenclature_quantity, $unitComponent, $product_quantity);

                            $invertedMatrix[$compositeKey]['Needs'][] = [
                                'date' => $date,
                                'quantity_nomenclature' => $convertedMeasure["convertedMeasure1"]->getValue() . ' / ',
                                'quantity_produit' => $convertedMeasure["convertedMeasure2"]->getValue() . ' / ',
                                'needs' => $convertedMeasure['totalMeasure']->getValue(),
                                'unite' => $convertedMeasure["convertedMeasure1"]->getCode(),
                            ];
                        }
                }*/
              //  }
           // }
           
       // }
   // }
  //  }
           //$componentChartData = $this->processDatesAndStockMinimumForComponents($datesForComponents);

        return new JsonResponse([

            'components' => '',
            // 'components' => $invertedMatrix,
            // 'componentChartData' => $componentChartData,

        ]);
}

    private function processDatesAndStockMinimumForComponents(array &$datesForComponents): array
    {
        $result = [];
    
        foreach ($datesForComponents as $compositeKey => $dates) {
            $labels = [];
            $stockMinimum = [];
            $stockValue = [];
    
            foreach ($dates as $dateInfo) {
                $labels[] = $dateInfo['date'];
                $stockMinimum[] = $dateInfo['stockMinimum'];
                $stockValue[] = $dateInfo['stockValue'];

            }
            $labels = array_values(array_unique($labels));
            // Trier les dates en ordre croissant
            usort($labels, function ($a, $b) {
                $dateA = DateTime::createFromFormat('d/m/Y', $a);
                $dateB = DateTime::createFromFormat('d/m/Y', $b);

                return $dateA <=> $dateB;
            });

            $result[$compositeKey] = [
                'labels' => $labels,
                'stockMinimum' => $stockMinimum,
                'stockValue' => $stockValue
            ];
        }
    
        return $result;
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
        foreach ($productChartsData[$productId]['labels'] as $date) {
            if (!isset($productChartsData[$productId]['stockProgress'][$date])) {
                $productChartsData[$productId]['stockProgress'][$date] = 0;
            }
            $productChartsData[$productId]['stockProgress'][$date] += $quantityValue;
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

    /**
     * Mise à jour de $productChartsData pour un produit donné avec la date et la quantité spécifiées.
     */
    private function updateProductChartsData(array $productChartsData, int $productId, string $date, float $quantity): array
    {
        $productChartsData[$productId] ??= [
            'labels' => [],
            'stockMinimum' => [],
            'stockProgress' => [],
        ];

        $productChartsData[$productId]['labels'][] = $date;

        $productChartsData[$productId]['confirmed_quantity_value'][$date] = $quantity;

        return $productChartsData;
    }

    /**
     * Get product families from the given products.
     *
     * @param Product[] $products
     * @return array
     */
    private function getProductFamilies(array $products): array
    {
        $productFamilies = [];

        foreach ($products as $prod) {
            $family = $prod->getFamily();

            // Vérifier si le produit a une famille associée
            if ($family !== null) {
                $familyId = $family->getId();

                // Ajouter les informations au tableau $productFamilies
                $productFamilies[$familyId] = [
                    'familyName' => $family->getName(),
                    'pathName' => $family->getFullName(),
                ];
            }
        }

        return $productFamilies;
    }

    /**
     * Générer les données pour les clients.
     */
    private function generateCustomersData(array $productCustomers): array
    {
        $customers = [];
        $societiesByCustomer = [];
        $customerProducts = [];

        foreach ($productCustomers as $productcustomer) {
            $customerId = $productcustomer->getCustomer()->getId();
            $productId = $productcustomer->getProduct()->getId();

            // Récupération de l'ID de la société associée au client
            $societyId = $this->productCustomerRepository->findByCustomerIdSocieties($customerId);

            // Vérifiez si l'ID de la société est disponible
            if (!empty($societyId)) {
                // Initialisation de $customerProducts pour le client s'il n'existe pas
                $customerProducts[$customerId] ??= [];
                $customerProducts[$customerId][] = $productId;

                // Stockez l'ID de la société associée au client
                $societiesByCustomer[$customerId] = $societyId;
            }
        }

        // Créez l'array $customers en utilisant les ID de la société stockés
        foreach ($customerProducts as $customerId => $products) {
            $customers[] = [
                'id' => $customerId,
                'products' => $products,
                'society' => $societyId ?? null,
            ];
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
            $component = $nomenclature->getComponent();

            if ($component && !in_array($component->getId(), $components)) {
                $components[] = $component->getId();
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
        $allStock = 0;

        foreach ($stocks as $stock) {
            if ($stock->getItem()->getId() === $prod->getId()) {
                $quantityValue = $stock->getQuantity()->getValue();
                $allStock += $quantityValue;
            }
        }

        return $allStock;
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
        $indexStockDefault = 0; // Initialize $indexStockDefault here
        $indexNewOFNeeds = 0;

        if (array_key_exists($prod->getId(), $productChartsData)) {
            $stockDefault = [];
            $maxQuantity = null;

            foreach ($productChartsData[$prod->getId()]['labels'] as $date) {

                $stockProgress = $productChartsData[$prod->getId()]['stockProgress'][$date];
                $stockMinimum = $productChartsData[$prod->getId()]['stockMinimum'][$date];

                if ($stockProgress < $stockMinimum) {
                    $indexStockDefault++;
                    $stockDefault[$indexStockDefault] = ['date' => $date];
                    $dateTime = DateTime::createFromFormat('d/m/Y', $date);
                    if ($dateTime) {
                        $dateTime->sub(new DateInterval('P1W'));
                        $quantity = $stockMinimum - $stockProgress;

                        if (!isset($maxQuantity) || $quantity > $maxQuantity) {
                            $maxQuantity = $quantity;
                        }

                        $indexNewOFNeeds++;
                        $newOFNeeds[$indexNewOFNeeds] = [
                            'date' => $dateTime->format('d/m/Y'),
                            'quantity' => $quantity,
                        ];
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

                $stockProgress = $productChartsData[$prod->getId()]['stockProgress'][$date];
                $stockMinimum = $productChartsData[$prod->getId()]['stockMinimum'][$date];

                $quantity = $stockMinimum - $stockProgress;

                if (!isset($maxQuantity) || $quantity > $maxQuantity) {
                    $maxQuantity = $quantity;
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
        // Your logic to calculate $minStock
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
                $stockProgress = $productChartsData[$productId]['stockProgress'][$date];
                $stockMinimum = $productChartsData[$productId]['stockMinimum'][$date];

                // Your logic to determine if stockProgress is less than stockMinimum
                if ($stockProgress < $stockMinimum) {
                    // Your logic to fill $stockDefault
                    $stockDefault[] = ['date' => $date];
                }
            }
        }

        return $stockDefault;
    }

    private function generateProductData($prod, $productChartsData, $stocks): array
    {
        $components = $this->generateComponentsData($prod);

        $family = $prod->getFamily();
        $familyId = ($family !== null) ? $family->getId() : null;

        $minStock = $this->generateMinStock($prod);
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
    // Additional function to finalize productChartsData
    private function finalizeProductChartsData(array $productChartsData): array
    {
        foreach ($productChartsData as &$product) {
            $product['stockProgress'] = array_values($product['stockProgress']);
            $product['stockMinimum'] = array_values($product['stockMinimum']);
            // Supprimer les clés quantity_requested_value et confirmed_quantity_value
            unset($product['quantity_requested_value']);
            unset($product['confirmed_quantity_value']);
        }

        return $productChartsData;
    }

    private function updateStockMinimumForProduct(array &$productChartsData, $productId, $minStock): void
    {
        if (array_key_exists($productId, $productChartsData)) {
            foreach ($productChartsData[$productId]['labels'] as $date) {
                $productChartsData[$productId]['stockMinimum'][$date] = $minStock;
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
}