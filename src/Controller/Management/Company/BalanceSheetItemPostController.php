<?php

namespace App\Controller\Management\Company;

use App\Entity\Management\Currency;
use App\Entity\Management\Society\Company\BalanceSheetItem;
use App\Entity\Management\Unit;
use App\Filesystem\FileManager;
use App\Entity\Embeddable\Measure;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;

class BalanceSheetItemPostController
{
    private BalanceSheetItem $entity;
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileManager            $fileManager) {
    }

    private function getMeasureGen($requestParameters, $valueTxt, $currencyTxt, $unitClass): Measure {
        $idRegExp= '/(\d+)$/';
        $measure= new Measure();
        $measure->setValue(floatval($requestParameters[$valueTxt]));
        preg_match($idRegExp, $requestParameters[$currencyTxt], $matches);
        if (!empty($matches)) {
            $currencyId = $matches[0];
            $currency = $this->entityManager->getRepository($unitClass)->findOneBy(['id' => $currencyId]);
            $measure->setUnit($currency);
            $measure->setCode($currency->getCode());
        }
        return $measure;
    }
    private function getMeasureCurrency($requestParameters, $valueTxt, $currencyTxt): Measure {
        return $this->getMeasureGen($requestParameters, $valueTxt, $currencyTxt, Currency::class);
    }

    private function getMeasureUnit($requestParameters, $valueTxt, $currencyTxt): Measure {
        return $this->getMeasureGen($requestParameters, $valueTxt, $currencyTxt, Unit::class);
    }

    public function __invoke(Request $request):BalanceSheetItem {
        $this->getEntity($request);
        $requestParameters = $request->request->all();
//        dump([
//            'request' => $request,
//            'requestParameters' => $requestParameters,
//            'entity' => $this->entity
//        ]);
        $currencyProperties = ['amount', 'unitPrice', 'vat'];
        $unitProperties = ['quantity'];
        foreach ($currencyProperties as $property) {
            if (isset($requestParameters[$property.'-value']) && isset($requestParameters[$property.'-code'])) {
                $this->entity->{'set'.ucfirst($property)}($this->getMeasureCurrency($requestParameters, $property.'-value', $property.'-code'));
            }
        }
        foreach ($unitProperties as $property) {
            if (isset($requestParameters[$property.'-value']) && isset($requestParameters[$property.'-code'])) {
                $this->entity->{'set'.ucfirst($property)}($this->getMeasureUnit($requestParameters, $property.'-value', $property.'-code'));
            }
        }
//        dump(['entity' => $this->entity]);
        $this->getFileAndPersist($request);
        return $this->entity;
    }
    public function getEntity(Request $request):void {
        $entity = $request->attributes->get('data');
        $class = get_class($entity);
        if (!($entity instanceof BalanceSheetItem)) {
            throw new RuntimeException("l'entité $class n'hérite pas de App\\Entity\\Management\\Society\\Company\\BalanceSheetItem");
        } else {
            $this->entity = $entity;
        }
    }
    public function getFileAndPersist(Request $request, bool $withError = true):void {
        $host = $request->getSchemeAndHttpHost();
        $file = $request->files->get('file');
        if ($file === null) {
            // no file uploaded
        } else {
            if ($withError && $file->getError()===1) {
                throw new FileException($file->getErrorMessage());
            }
            $this->entity->setFile($file);
            $this->entityManager->persist($this->entity);
            $this->entityManager->flush(); // pour récupération id utilisé par default dans getBaseFolder
            $saveFolder = $this->entity->getBaseFolder();
            if ($this->entity->getSubCategory() !== 'null') {
                $saveFolder .= '/' . $this->entity->getSubCategory();
            } else $this->entity->setSubCategory('');
            $this->fileManager->persistFile($saveFolder, $file);
            $this->entity->setUrl($host.'/uploads'.$saveFolder.'/'.str_replace(' ', '_', $file->getClientOriginalName()));
            $this->entityManager->flush(); // pour persist du chemin vers le fichier
        }

    }
}