<?php

namespace App\Controller\Management\Company;

use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Address;
use App\Entity\Management\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\Management\Company\CompanyRepository;
use DateTimeImmutable;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\Currency;

class CompanyPatchController
{
   public function __construct(private readonly EntityManagerInterface $em, private CompanyRepository $repository, private LoggerInterface $logger) {
   }

   /**
    * @param Request $request
    * @return Company
    * @throws \ReflectionException
    */
   public function __invoke(Request $request): Company {
      $itemId = $request->get('id');
      $sourceItem = $this->repository->find($itemId);
      $entityStr = 'company';

      if (empty($sourceItem)) {
         throw new NotFoundHttpException();
      }
      $refClass = new \ReflectionClass($sourceItem);
      $process = $request->get('process');
      $content = json_decode($request->getContent());
      foreach ($content as $key => $value) {
         $refProps = $refClass->getProperty($key);
         $attributeGroups = $refProps->getAttributes(Groups::class);
         if (count($attributeGroups)>0) {
            $groups = $attributeGroups[0]->getArguments()[0];
            //on contrôle les droits de modification en fonction du $process
            if (in_array('write:'.$entityStr.':'.$process, $groups)) {
               if (getType($refProps->getValue($sourceItem)) === 'object'){
                  switch (get_class($refProps->getValue($sourceItem))) {
                     case Address::class:
                        //dump(['$value'=>$value]);
                        $countryCode = $value->country;
                        $newAddress = new Address();
                        $newAddress->setAddress($value->address);
                        $newAddress->setAddress2($value->address2);
                        $newAddress->setCity($value->city);
                        $newAddress->setCountry($countryCode);
                        $newAddress->setEmail($value->email);
                        $newAddress->setPhoneNumber($value->phoneNumber);
                        $newAddress->setZipCode($value->zipCode);
                        $refProps->setValue($sourceItem,$newAddress);
                        break;
                    case DateTimeImmutable::class:
                        //dump(['DateTimeImmutable','key'=>$key, 'value'=>$value]);
                        $newDate = DateTimeImmutable::createFromFormat('Y-m-d',$value);
                        $refProps->setValue($sourceItem,$newDate);
                        break;
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
                        $refProps->setValue($sourceItem,$newMeasure);
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
                        $refProps->setValue($sourceItem, $unit);
                        break;                        
                     default:
                        //dump([$value,$refProps]);
                        if ($key == 'company') {
                           $test = str_contains($value, '/api/companies/');
                           //si c'est une IRI on récupère l'id
                           if ($test) {
                              $splitted = preg_split('/\//', $value);
                              $id = intval($splitted[3]);
                              $company = $this->em->getRepository(Company::class)->find($id);
                              $refProps->setValue($sourceItem, $company);
                           } else {
                               $this->logger->warning('#3 Impossible de modifier '.$key.' IRI attendue et non recue '.$value);
                           }
                        } elseif ($key == 'currency') {
                           $test = str_contains($value, '/api/currencies/');
                           //si c'est une IRI on récupère l'id
                           if ($test) {
                              $splitted = preg_split('/\//', $value);
                              $id = intval($splitted[3]);
                              $currency = $this->em->getRepository(Currency::class)->find($id);;
                              $refProps->setValue($sourceItem, $currency);
                           } else {
                               $this->logger->warning('#4 Impossible de modifier '.$key.' IRI attendue et non recue '.$value);
                           }
                        } else {
                           $this->logger->warning('#1 Impossible de modifier '.$key.' type non encore géré '.get_class($refProps->getValue($sourceItem)));
                        }
                  }
               } else {
                  //dump('Je suis ici => '.$key);
                  if ($refProps->getValue($sourceItem)===null||in_array(getType($refProps->getValue($sourceItem)) ,["boolean", 'integer', 'double', 'string'])) {
                     $refProps->setValue($sourceItem, $value);
                  } else {
                     $this->logger->warning('#2 Impossible de modifier '.$key.' type non encore géré '.getType($refProps->getValue($sourceItem)));
                  }
               }

            } else {
               // on ne peut pas modifier la propriété, on ajoute alors la tentative de modification dans le log
               $this->logger->warning('Tentative de modification de la propriété '.$key.' dans le processus '.$process, [$content]);
            }
         }
      }
      return $sourceItem;
   }
}