<?php

namespace App\Controller\Purchase\Component;

use App\Entity\Embeddable\Measure;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Component\Component;
use App\Repository\Purchase\Component\ComponentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Purchase\Component\Family as ComponentFamily;

class ComponentController
{
   public function __construct(private readonly EntityManagerInterface $em, private ComponentRepository $componentRepository, private LoggerInterface $logger) {
   }

   /**
    * @param Request $request
    * @return Component
    * @throws \ReflectionException
    */
   public function __invoke(Request $request): Component {
      $componentId = $request->get('id');
      $sourceComponent = $this->componentRepository->find($componentId);
//      dump('ComponentController');
      if (empty($sourceComponent)) {
         throw new NotFoundHttpException();
      }
      $refClass = new \ReflectionClass($sourceComponent);
      $process = $request->get('process');
      $content = json_decode($request->getContent());
      foreach ($content as $key => $value) {
         $refProps = $refClass->getProperty($key);
         $attributeGroups = $refProps->getAttributes(Groups::class);
         if (count($attributeGroups)>0) {
            $groups = $attributeGroups[0]->getArguments()[0];
            //on contrôle les droits de modification en fonction du $process
            if (in_array('write:component:'.$process, $groups)) {
               if (getType($refProps->getValue($sourceComponent)) === 'object'){
                  switch (get_class($refProps->getValue($sourceComponent))) {
                     case Measure::class:
                        $newMeasure = new Measure();
                        $unit = $this->em->getRepository(Unit::class)->findOneBy(['code' => $value->code]);
                        $newMeasure->setUnit($unit);
                        $newMeasure->setCode($value->code);
                        $newMeasure->setValue($value->value);
                        if (isset($value->denominator)){
                           $denominatorUnit = $this->em->getRepository(Unit::class)->findOneBy(['code'=>$value->denominator]);
                           $newMeasure->setDenominatorUnit($denominatorUnit);
                           $newMeasure->setDenominator($value->denominator);
                        }
                        $refProps->setValue($sourceComponent,$newMeasure);
                        break;
                     case Unit::class:
                        $test = str_contains($value, '/api/units/');
                        //si c'est une IRI on récupère l'id
                        if ($test) {
                           $splitted = preg_split('/\//', $value);
                           $id = intval($splitted[3]);
                           $unit = $this->em->getRepository(Unit::class)->find($id);
                        } else {
                           $unit = $this->em->getRepository(Unit::class)->findOneBy(['code' => $value]);
                        }
                        $refProps->setValue($sourceComponent, $unit);
                        break;                        
                     default:
                        if ($key == 'family') {
                           $test = str_contains($value, '/api/component-families/');
                           if ($test) {
                              $splitted = preg_split('/\//', $value);
                              $id = intval($splitted[3]);
                              $unit = $this->em->getRepository(ComponentFamily::class)->find($id);
                              $refProps->setValue($sourceComponent, $unit);
                           } else {
                              $this->logger->warning('#1 Impossible de modifier '.$key.' valeur non IRI '.$value);
                           }
                        } else {
                           $this->logger->warning('#1 Impossible de modifier '.$key.' type non encore géré '.get_class($refProps->getValue($sourceComponent)));
                        }
                  }
               } else {
                  if ($refProps->getValue($sourceComponent)===null||in_array(getType($refProps->getValue($sourceComponent)) ,["boolean", 'integer', 'double', 'string'])) {
                     $refProps->setValue($sourceComponent, $value);
                  } else {
                     $this->logger->warning('#2 Impossible de modifier '.$key.' type non encore géré '.getType($refProps->getValue($sourceComponent)));
                  }
               }

            } else {
               // on ne peut pas modifier la propriété, on ajoute alors la tentative de modification dans le log
               $this->logger->warning('Tentative de modification de la propriété '.$key.' dans le processus '.$process, [$content]);
            }
         }
      }
      return $sourceComponent;
   }
}