<?php

namespace App\Controller\Manufacturing\Schedule;

use DateTime;
use DateTimeImmutable;
use App\Entity\Project\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Selling\Order\ProductItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use App\Repository\Project\Product\ProductRepository;
use App\Repository\Selling\Order\ProductItemRepository;
use App\Repository\Logistics\Stock\ProductStockRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ManufacturingScheduleController
{
    public function __construct(private EntityManagerInterface $em, private ProductRepository $productRepository, private ProductItemRepository $itemRepository, private ProductStockRepository $productStockRepository )
    {
    }
   /**
    * @param Request $request
    * @return array
    * @throws \ReflectionException
    */
    public function __invoke(): JsonResponse{
        dump('entrer');
        $products = $this->em->getRepository(Product::class)->findAll();

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
                if ($this->is_semaine_passee($date) === true) {

                    $date_week = "RETARD" ;
                   if (!isset($lateProductQuantities[$productId])) {
                        $lateProductQuantities[$productId] = 0;
                    }
                    $lateProductQuantities[$productId] += $confirmedQuantity;
                }   
                if($this->is_semaine_passee($date) === false) {
                  $date_week = "PAS DE RETARD" ; 

                 if (!isset($productsByYearWeek[$YearWeek])) {
                    $productsByYearWeek[$YearWeek] = [];
                }
                $productsByYearWeek[$YearWeek][$productId] = ($productsByYearWeek[$YearWeek][$productId] ?? 0) + $confirmedQuantity;
                }      
            }

            $data[] = [
                'id' => $product->getId(),
                'lien'=> '',
                'produit'=> $product->getCode(),
                'indice' => $product->getIndex(),
                'designation' => $product->getName(),
                'compagnie' => $companyNames,
                'client' => $formattedCustomerNames,
                'stock' => $stockQuantity,
                'temps chiffrage' => $tempsChiffrage->getValue() .'  unité  '.$tempsChiffrage->getCode(),
                'temps atelier' => $tempsAtelier->getValue()  .'  unité  '.$tempsAtelier->getCode(),
                'volu_previ'=> $forecastVolume->getValue(),
                '3pc_volu_previ' => $threePercentValue,
                'retard' => isset($lateProductQuantities[$product->getId()]) ? $lateProductQuantities[$product->getId()] : "",
            ];
        }

        $allYearWeeks = array_keys($productsByYearWeek);

        foreach ($data as &$productData) {
            $productId = $productData['id'];
            $productQuantities = [];
        
            // Parcourir tous les YearWeek
            foreach ($allYearWeeks as $YearWeek) {
                // Vérifier si le produit a une quantité pour ce YearWeek
                if (isset($productsByYearWeek[$YearWeek][$productId])) {
                    $productQuantities[$YearWeek] = $productsByYearWeek[$YearWeek][$productId];
                } else {
                    $productQuantities[$YearWeek] = '';
                }
            }
            $productData['quantites_par_semaine_et_annee'] = $productQuantities;
        }
        
        return new JsonResponse($data);
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
