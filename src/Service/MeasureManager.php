<?php

namespace App\Service;

use App\Entity\Embeddable\Measure;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Management\Unit;
use App\Repository\Management\UnitRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;


final class MeasureManager {
    public function __construct(
        private readonly CacheInterface     $cache,
        private readonly UnitRepository     $unitRepo,
        private readonly RequestStack       $stack,
        private LoggerInterface             $logger
    ) {
    }

    public function convertMeasure(Measure $measure, Unit $unit2, ?Unit $denominator = null): Measure
    {
        // Convertir la mesure dans une autre unité
        $convertedMeasure = $measure->convert($unit2);
       // dump(['convertedMeasure',$convertedMeasure]);
    
        // Si un dénominateur est spécifié, convertir également la mesure du dénominateur
        if ($denominator !== null) {
            // Mettre à jour la valeur convertie avec le dénominateur
            $convertedMeasure->setValue($convertedMeasure->getValue() / $measure->getDenominatorUnit()->getBase());

        }
        // Retourner la mesure convertie
        return $convertedMeasure;
    }
    

   public function ConvertAfterMul(Unit $unit1,float $quantity1, Unit $unit2, float $quantity2 ): array {
        $newMeasure1 = new Measure();
        $newMeasure1->setCode($unit1->getCode());
        $newMeasure1->setDenominator($unit1->getBase());
        $newMeasure1->setUnit($unit1); 
        $newMeasure1->setValue($quantity1);
    
        $newMeasure2 = new Measure();
        $newMeasure2->setCode($unit2->getCode());
        $newMeasure2->setDenominator($unit2->getBase());
        $newMeasure2->setUnit($unit2); 
        $newMeasure2->setValue($quantity2);
    
        // Convertir les mesures avant la multiplication
        $convertedMeasure1 = $newMeasure1->convert($newMeasure2->getSafeUnit(), $newMeasure2->getDenominatorUnit());
        $convertedMeasure2 = $newMeasure2->convert($newMeasure1->getSafeUnit(), $newMeasure1->getDenominatorUnit());
        // Effectuer la multiplication des valeurs converties
        $product = $convertedMeasure1->getValue() * $convertedMeasure2->getValue();
    
        $totalMeasure = new Measure();
        $totalMeasure->setValue($product);
    
        // Retourner un tableau contenant convertedMeasure1 et convertedMeasure2
        return [
            'convertedMeasure1' => $convertedMeasure1,
            'convertedMeasure2' => $convertedMeasure2,
            'totalMeasure' => $totalMeasure,
        ];
    }    
    

}
?> 