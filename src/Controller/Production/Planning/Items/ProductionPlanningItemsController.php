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
            $costingAutoDuration = $product->getCostingAutoDuration();
            $costingManualDuration = $product->getCostingManualDuration();
            $costingDuration = $costingAutoDuration->add($costingManualDuration);
            $autoDuration = $product->getManualDuration();
            $manualDuration = $product->getAutoDuration();
            $tempsAtelier = $autoDuration->add($manualDuration);
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
                if ($this->isWeekBeforeNow($date) === true) {
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
                if($this->isWeekBeforeNow($date) === true) {
                    $date_week = "PAS DE RETARD" ;
//                    dump($date_week);
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
                'Temps Chiffrage' => $costingDuration->getValue()." ".$costingDuration->getCode(),
                'temps atelier'=>$tempsAtelier->getValue()." ".$tempsAtelier->getCode(),
                'volu_previ'=>$forecastVolume->getValue(),
                '3pc_volu_previ' =>$threePercentValue,
                'retard' => isset($lateProductQuantities[$product->getId()]) ? $lateProductQuantities[$product->getId()] : "",
                ];
        }

        $dataItems = ['items' => $data];
        // On trie $productsByYearWeek par clé croissante
        $yearWeeks = array_keys($productsByYearWeek);
        sort($yearWeeks);
        // On ne garde que les 12 premiers éléments des clés
        //$limitedYearWeeks = array_slice($yearWeeks, 0, 12);
//        $limitedYearWeeks = $yearWeeks;
        //Construction du tableau des YearWeek des 12 prochaines semaine incluant la semaine courante
        $nextTwelveWeeks = [];
        $currentDate = new DateTime();
        for ($i = 0; $i < 12; $i++) {
            // Clone la date pour éviter de modifier l'original lors de l'ajout de semaines
            $weekDate = clone $currentDate;
            $weekDate->modify("+{$i} week");
            $yearWeek = $weekDate->format('oW'); // 'o' pour l'année ISO, 'W' pour le numéro de semaine ISO
            $nextTwelveWeeks[] = $yearWeek;
        }
        // On ne garde que les éléments de $productsByYearWeek qui ont leur clé dans $limitedYearWeeks
        $filteredProductsByYearWeek = array_filter($productsByYearWeek, static function($value, $key) use ($nextTwelveWeeks) {
            return in_array($key, $nextTwelveWeeks, true);
        }, ARRAY_FILTER_USE_BOTH);
        ksort($filteredProductsByYearWeek);
        // On boucle sur $nextTwelveWeek afin d'obtenir le tableau des champs à ajouter
        foreach ($nextTwelveWeeks as $YearWeek) {
            $dataFields[] = [
                'label' => $YearWeek,
                'name' => $YearWeek,
                'type' => 'integer'
            ];
            foreach ($dataItems['items'] as &$productData) {
                $productId = $productData['id'];
                // Ajouter les quantités par semaine et par année directement dans $productData
                if (isset($filteredProductsByYearWeek[$YearWeek][$productId])) {
                    $productData[$YearWeek] = $filteredProductsByYearWeek[$YearWeek][$productId];
                } else {
                    $productData[$YearWeek] = '';
                }
            }
        }
//        dump($filteredProductsByYearWeek);
//        foreach ($dataItems['items'] as &$productData) {
//            $productId = $productData['id'];
//            // Ajouter les quantités par semaine et par année directement dans $productData
//            foreach ($filteredProductsByYearWeek as $YearWeek => $productQuantities) {
//                if (isset($productQuantities[$productId])) {
//                    $productData[$YearWeek] = $productQuantities[$productId];
//                } else {
//                    $productData[$YearWeek] = '';
//                }
//            }
//        }
//        foreach ($filteredProductsByYearWeek as $key => $value) {
//            $type = 'integer';
//            $dataFields[] = [
//                'label' => $key,
//                'name' => $key,
//                'type' => $type
//            ];
//        }
        $responseData = array_merge($dataItems, ['fields' => $dataFields]);

        return new JsonResponse($responseData);
    }

   public function isWeekBeforeNow(DateTimeImmutable $date): bool
   {
        $week = $date->format('W');
        $year = $date->format('Y');

        $currentYear = date('Y');
        $currentWeek = date('W');

       return $currentYear > $year || ($year === $currentYear && $currentWeek > $week);
   }
}