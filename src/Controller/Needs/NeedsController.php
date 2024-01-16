<?php

namespace App\Controller\Needs;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\Selling\Order\ProductItemRepository;
use App\Repository\Manufacturing\Product\ManufacturingProductItemRepository;
use App\Repository\Logistics\Stock\ProductStockRepository;
use App\Repository\Project\Product\ProductRepository;
use App\Repository\Purchase\Order\ItemRepository as PurchaseItemRepository;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Needs\Needs;
use App\Repository\Project\Product\NomenclatureRepository;
use App\Repository\Selling\Customer\ProductRepository as ProductCustomerRepository;
use DateTime;
use DateInterval;


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
    private ManufacturingProductItemRepository $manufacturingProductItemRepository;
    private NomenclatureRepository $nomenclatureRepository;

    public function __construct(
        private readonly EntityManagerInterface $em,
        ProductItemRepository $productItemRepository,
        ProductStockRepository $productStockRepository,
        ProductRepository $productRepository,
        ProductCustomerRepository $productCustomerRepository,
        ManufacturingProductItemRepository $manufacturingProductItemRepository,
        NomenclatureRepository $nomenclatureRepository,
        private LoggerInterface $logger
    ) {
        $this->productItemRepository = $productItemRepository;
        $this->productStockRepository = $productStockRepository;
        $this->productRepository = $productRepository;
        $this->productCustomerRepository = $productCustomerRepository;
        $this->manufacturingProductItemRepository = $manufacturingProductItemRepository;
        $this->nomenclatureRepository = $nomenclatureRepository;
    }

    /**
     * @Route("/api/needs", name="needs", methods={"GET"})
     */
    public function index(): JsonResponse
{
        $sellingItems = $this->productItemRepository->findByEmbBlockerAndEmbState();
        $manufacturingOrders = $this->manufacturingProductItemRepository->findByEmbBlockerAndEmbState();
        $stocks = $this->productStockRepository->findAll();
        $products = $this->productRepository->findByEmbBlockerAndEmbState();
        $productcustomers = $this->productCustomerRepository->findAll();
        $productsData = [];
        $customers = [];

        $productChartsData = [];
        // Traitement des ventes
        foreach ($sellingItems as $sellingItem) {
            $productId = $sellingItem->getItem()->getId();
            $confirmedDate = $sellingItem->getConfirmedDate()->format('d/m/Y');
            $confirmedQuantityValue = $sellingItem->getConfirmedQuantity()->getValue();
            // Initialisation de productChartsData pour le produit s'il n'existe pas
            $productChartsData[$productId] ??= [
                'labels' => [],
                'stockMinimum' => [],
                'stockProgress' => [],
            ];
            // Ajout de la date et de la quantité
            $productChartsData[$productId]['labels'][] = $confirmedDate;
            $productChartsData[$productId]['confirmed_quantity_value'][$confirmedDate] = $confirmedQuantityValue;
        }
       // Traitement des items de manufacturing
        foreach ($manufacturingOrders as $manufacturingOrder) {
            $productId = $manufacturingOrder->getProduct()->getId();
            $manufacturingDate = $manufacturingOrder->getManufacturingDate()->format('d/m/Y');
            $quantityRequestedValue = $manufacturingOrder->getQuantityRequested()->getValue();

            // Initialisation de productChartsData pour le produit s'il n'existe pas
            $productChartsData[$productId] ??= [
                'labels' => [],
                'stockMinimum' => [],
                'stockProgress' => [],
            ];

            // Ajout de la date et de la quantité
            $productChartsData[$productId]['labels'][] = $manufacturingDate;
            $productChartsData[$productId]['quantity_requested_value'][$manufacturingDate] = $quantityRequestedValue;
        } 
        // Fusionner les dates, éliminer les doublons et trier les dates
        foreach ($productChartsData as &$product) {
            $product['labels'] = array_values(array_unique($product['labels']));
            sort($product['labels']);

            // Initialisation de 'stockProgress' comme tableau pour chaque date
            foreach ($product['labels'] as $date) {
                $product['stockProgress'][$date] = 0;

                // Ajouter la soustraction (stockProgress - confirmed_quantity_value) à stockProgress si la clé existe
                $confirmedQuantity = $product['confirmed_quantity_value'][$date] ?? 0;
                $product['stockProgress'][$date] -= $confirmedQuantity;

                // Ajouter quantity_requested_value à stockProgress si la clé existe
                $quantityRequested = $product['quantity_requested_value'][$date] ?? 0;
                $product['stockProgress'][$date] += $quantityRequested;
            }
                // Ordonner les labels
                usort($product['labels'], function($a, $b) {
                    $dateA = \DateTime::createFromFormat('d/m/Y', $a);
                    $dateB = \DateTime::createFromFormat('d/m/Y', $b);

                    return $dateA <=> $dateB;
                });
        }

        foreach ($stocks as $stock) {
            $productId = $stock->getItem()->getId();
            if (array_key_exists($productId, $productChartsData)) {
                $quantityValue = $stock->getQuantity()->getValue();
                // Ajouter la quantité au total de la date correspondante
                foreach ($productChartsData[$productId]['labels'] as $date) {
                    if (!isset($productChartsData[$productId]['stockProgress'][$date])) {
                        $productChartsData[$productId]['stockProgress'][$date] = 0;
                    }
                    $productChartsData[$productId]['stockProgress'][$date] += $quantityValue;
                }
            }
        }

        $productFamilies = [];
        // Ajout du traitement pour l'attribut stockMinimum
        foreach ($products as $prod) {
            $productId = $prod->getId();
            $productName = $prod->getName();
            $productCode = $prod->getCode();
            $nomenclatures = $this->nomenclatureRepository->findByProductId($productId);
            $components = [];
            $allStock = 0;
            $indexStockDefault = 0;
            $indexNewOFNeeds = 0;

            if (array_key_exists($productId, $productChartsData)) {
                $minStock = $prod->getMinStock()->getValue();
                // Ajouter la valeur stockMinimum pour chaque date
                $stockDefault = [];
                $newOFNeeds = [];
                $maxQuantity = NULL; // Initialisez maxQuantity à 0

                foreach ($productChartsData[$productId]['labels']  as $date) {
                    $productChartsData[$productId]['stockMinimum'][$date] = $minStock;
                
                    $stockProgress = $productChartsData[$productId]['stockProgress'][$date];
                    $stockMinimum = $productChartsData[$productId]['stockMinimum'][$date];

                    // Si stockProgress est inférieur à stockMinimum, ajoutez la date à stockDefault
                    if ($stockProgress < $stockMinimum) {
                        $indexStockDefault++; // Incrémentez l'index pour stockDefault
                        $stockDefault[$indexStockDefault] = ['date' => $date];
                        $dateTime = DateTime::createFromFormat('d/m/Y', $date);
                        if ($dateTime) {
                        $dateTime->sub(new DateInterval('P1W')); // Soustraire une semaine
                        $quantity = $stockMinimum - $stockProgress ;
                        // Mettre à jour la valeur maximale de la quantité
                        if (!isset($maxQuantity) || $quantity > $maxQuantity) {
                            $maxQuantity = $quantity;
                        }
                        $indexNewOFNeeds++; // Incrémentez l'index pour newOFNeeds

                        $newOFNeeds[$indexNewOFNeeds] = [
                            'date' => $dateTime->format('d/m/Y'),
                            'quantity' => $quantity,
                        ];
                        }


                    }
                }
            }
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
            // Itérer sur chaque nomenclature pour récupérer les composants
            foreach ($nomenclatures as $nomenclature) {
                $component = $nomenclature->getComponent();
                // Vérifier si le composant existe et s'il n'a pas déjà été ajouté
                if ($component && !in_array($component->getId(), $components)) {
                    $components[] = $component->getId();
                }
            }
            // Itérer sur chaque stock pour ce produit et mettre à jour la somme des stocks
            foreach ($stocks as $stock) {
                if ($stock->getItem()->getId() === $productId) {
                    $quantityValue = $stock->getQuantity()->getValue();
                    $allStock += $quantityValue;
                }
            }

            $productsData[$productId] = [
                'component' => $components,
                'family' => $familyId,
                'minStock' => $minStock,
                'newOFNeeds' => $newOFNeeds,
                'productDesg' => $productName, // nom du produit
                'productRef' => $productCode,
                'productStock' =>  $allStock, // somme de Stock d'un produit donnée depuis la table stock
                'productToManufactring' => $maxQuantity,
                'stockDefault' => $stockDefault,  
            ];

        }

        foreach ($productChartsData as &$product) {
            $product['stockProgress'] = array_values($product['stockProgress']);
            $product['stockMinimum'] = array_values($product['stockMinimum']);
            // Supprimer les clés quantity_requested_value et confirmed_quantity_value
            unset($product['quantity_requested_value']);
            unset($product['confirmed_quantity_value']);
        }

        // Initialisation des tableaux
        $customerProducts = [];
        $societiesByCustomer = [];
        $customers = [];
        // Traitement des relations entre clients et produits
        foreach ($productcustomers as $productcustomer) {
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
                'society' =>  $societyId ?? null,
            ];
        }

        return new JsonResponse([
            'productChartsData' => $productChartsData,
            'productFamilies' => $productFamilies,
            'products' => $productsData,
            'customers' => $customers
        ]);
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
