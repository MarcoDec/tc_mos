<?php

namespace App\Controller\Production\Planning\Items;

use DateTime;
use DateTimeImmutable;
use App\Entity\Embeddable\Measure;
use App\Entity\Project\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Selling\Order\ProductItem;
use App\Entity\Logistics\Stock\ProductStock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use App\Repository\Project\Product\ProductRepository;
use App\Repository\Selling\Order\ProductItemRepository;
use App\Repository\Logistics\Stock\ProductStockRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductionPlanningItemsController
{
    public function __construct(private EntityManagerInterface $em, private ProductRepository $productRepository, private ProductItemRepository $itemRepository, private ProductStockRepository $productStockRepository )
    {
    }
   /**
    * @param Request $request
    * @return array
    * @throws \ReflectionException
    */
    public function __invoke(): JsonResponse {

        $products = $this->em->getRepository(Product::class)->findAll();
        $productsByYearWeek = [];
        $dataFields = [];
        $data = [];
        foreach ($products as $product) {
            $productId = $product->getId();
            $customerNames = $this->productRepository->getCustomerName($productId);
            $stockQuantity = $this->productStockRepository->getQuantityByProductId($productId);
            $SellingItems = $this->itemRepository->findByProductId($productId);

            // Formatte les noms des clients séparés par <br>
            $formattedCustomerNames = implode('<br>', array_column($customerNames, 'name'));

            $companies = $product->getCompanies()->toArray();
            $companyNames = implode('<br>', array_map(fn($company) => $company->getName(), $companies));
            $caustingAutoDuration = $product->getCostingAutoDuration();
            $caustingManualDuration = $product->getCostingManualDuration();
            $tempsChiffrage = $caustingAutoDuration->add($caustingManualDuration);
            $Autoduration = $product->getManualDuration();
            $manualDuration = $product->getAutoDuration();
            $tempsAtelier = $Autoduration->add($manualDuration);
            $forecastVolume = $product->getForecastVolume();
            $forecastVolumeValue = $forecastVolume->getValue();
            $threePercentValue = $forecastVolumeValue * 0.03;
            
        foreach ($SellingItems as $SellingItem) {

                $date = $SellingItem->getConfirmedDate();
                $confirmedQuantity = $SellingItem->getConfirmedQuantity()->getValue();
                $QuantitySent = $SellingItem->getQuantityToSent()->getValue();
                $weekNumber = $date->format('W');
                $year = $date->format('Y');
                $YearWeek = "$year$weekNumber";
                $embBlocker = $SellingItem->getEmbBlocker()->getState();
                $embState = $SellingItem->getEmbState()->getState();

            if($embBlocker === 'enabled' && !in_array($embState, ['delivered','billed','paid'])){
                if ($this->is_semaine_passee($date) === true) {
                    $date_week = "RETARD" ;
                   if (!isset($confirmedProductQuantities[$productId])) {
                        $confirmedProductQuantities[$productId] = 0;
                    }
                    $confirmedProductQuantities[$productId] += $confirmedQuantity;
                    
                   if (!isset($sentProductQuantities[$productId])) {
                    $sentProductQuantities[$productId] = 0;
                    }
                    $sentProductQuantities[$productId] += $QuantitySent;

                    if (!isset($lateProductQuantities[$productId])) {
                        $lateProductQuantities[$productId] = 0;
                    }
                    $lateProductQuantities[$productId] = $confirmedProductQuantities[$productId] - $sentProductQuantities[$productId];
                }   
                if($this->is_semaine_passee($date) === false) {
                    $date_week = "PAS DE RETARD" ;
                    dump($date_week);
                    if (!isset($productsByYearWeek[$YearWeek])) {
                        $productsByYearWeek[$YearWeek] = [];
                    }
                    $productsByYearWeek[$YearWeek][$productId] = ($productsByYearWeek[$YearWeek][$productId] ?? 0) + $confirmedQuantity;
                }    
            }  
        }
            $data[] = [
                'id' => $product->getId(),
                'produit'=> $product->getCode(),
                'indice' => $product->getIndex(),
                'designation' => $product->getName(),
                'compagnie' => $companyNames,
                'client' => $formattedCustomerNames,
                'stock' => $stockQuantity,
                'Temps Chiffrage' => $tempsChiffrage->getValue()." ".$tempsChiffrage->getCode(),         
                'temps atelier'=>$tempsAtelier->getValue()." ".$tempsAtelier->getCode(),
                'volu_previ'=>$forecastVolume->getValue()." ".$forecastVolume->getCode(),
                '3pc_volu_previ' =>$threePercentValue." ".$forecastVolume->getCode(),
                'retard' => isset($lateProductQuantities[$product->getId()]) ? $lateProductQuantities[$product->getId()] : "",
                ];
        }

        $dataItems = ['items' => $data];
        dump($productsByYearWeek);
        foreach ($dataItems['items'] as &$productData) {
            $productId = $productData['id'];
            // Ajouter les quantités par semaine et par année directement dans $productData
            foreach ($productsByYearWeek as $YearWeek => $productQuantities) {
                if (isset($productQuantities[$productId])) {
                    $productData[$YearWeek] = $productQuantities[$productId];
                } else {
                    $productData[$YearWeek] = '';
                }
            }
        }
        foreach ($productsByYearWeek as $key => $value) {
            $type = 'integer';
            $dataFields[] = [
                'label' => $key,
                'name' => $key,
                'type' => $type
            ];
        }
        $responseData = array_merge($dataItems, ['fields' => $dataFields]);

        return new JsonResponse($responseData);
    }

   public function is_semaine_passee(DateTimeImmutable $date) {
        $semaine = $date->format('W');
        $annee = $date->format('Y');

        $annee_courante = date('Y');
        $semaine_courante = date('W');

        if ($annee_courante > $annee || ($annee == $annee_courante && $semaine_courante > $semaine)) {
            return true;
        }
        return false;
    }
}