<?php

namespace App\Controller\Manufacturing\Schedule;

use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Project\Product\Product;
use App\Repository\Logistics\Stock\ProductStockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\Project\Product\ProductRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repository\Selling\Order\ProductItemRepository;
use Symfony\Component\Serializer\Encoder\JsonEncode;

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
            // Formatte les noms des clients séparés par <br>
            $formattedCustomerNames = implode('<br>', array_column($customerNames, 'name'));
            
            /*foreach( $product->getCompanies() as $company){
                dump($company->getName());
            }*/
           // $formattedCompaniesNames = implode('<br>', array_column($product->getCompanies(), 'name'));
            //dump($formattedCompaniesNames);
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
                '3pc_volu_previ' => $threePercentValue
            ];

        }
        return new JsonResponse($data);
    
    }
}
