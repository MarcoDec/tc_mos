<?php

namespace App\Controller\Management\Company;

use App\Entity\Management\Society\Company\BalanceSheet;
use App\Entity\Management\Society\Company\BalanceSheetItem;
use App\Filesystem\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

class BalanceSheetItemPatchController extends BalanceSheetItemAbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileManager            $fileManager) {
        parent::__construct($entityManager, $fileManager);
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request):BalanceSheetItem {
        //region On récupère l'entité de départ
        $entityId= $request->attributes->get('id');
        $this->entity = $this->entityManager->getRepository(BalanceSheetItem::class)->findOneBy(['id' => $entityId]);
//        dump(['entity' => $this->entity]);
        //endregion
        //region On récupère les données de la requête
        // $this->getEntity($request);
        // $requestContent = $request->getContent();
        // $extractedValues = $this->parseMultipartFormData($requestContent);
        $extractedValues = $request->request->all();
        $currencyProperties = ['amount-value','amount-code', 'unitPrice-value', 'unitPrice-code', 'vat-value', 'vat-code'];
        $unitProperties = ['quantity-value', 'quantity-code'];
        $dateProperties = ['paymentDate', 'billDate'];
        $relationProperties = ['balanceSheet' => BalanceSheet::class];
        foreach ($extractedValues as $property => $value) {
            if (in_array($property, $currencyProperties)) {
                $propertyName = explode('-', $property)[0];
                $measureProperty = explode('-', $property)[1];
                if ($measureProperty === 'value') {
                    $measure = $this->entity->{'get'.ucfirst($propertyName)}();
                    $this->entity->{'set'.ucfirst($propertyName)}($this->updateMeasureValue($measure, $value));
                }
                if ($measureProperty === 'code') {
                    $measure = $this->entity->{'get'.ucfirst($propertyName)}();
                    $this->entity->{'set'.ucfirst($propertyName)}($this->updateCurrencyMeasureCode($measure, $value));
                }
            } elseif (in_array($property, $unitProperties)) {
                $propertyName = explode('-', $property)[0];
                $measureProperty = explode('-', $property)[1];
                if ($measureProperty === 'value') {
                    $measure = $this->entity->{'get'.ucfirst($propertyName)}();
                    $this->entity->{'set'.ucfirst($propertyName)}($this->updateMeasureValue($measure, $value));
                }
                if ($measureProperty === 'code') {
                    $measure = $this->entity->{'get'.ucfirst($propertyName)}();
                    $this->entity->{'set'.ucfirst($propertyName)}($this->updateUnitMeasureCode($measure, $value));
                }
            } elseif (in_array($property, $dateProperties)){
                $this->entity->{'set'.ucfirst($property)}(new \DateTime($value));
            } elseif (in_array($property, array_keys($relationProperties))) {
                $class = $relationProperties[$property];
                $relatedEntityId = $this->getIdFromIri($value);
                $this->entity->{'set'.ucfirst($property)}($this->entityManager->getRepository($class)->findOneBy(['id' => $relatedEntityId]));
            }
            else {
                $this->entity->{'set'.ucfirst($property)}($value);
            }
        }
        //endregion
        //region On persiste l'entité
        $file = $request->files->get('file');
        $host = $request->getSchemeAndHttpHost();
//        $file = $this->getFileFromFILES();
//        dump(['FILES' => $_FILES, 'file' => $file]);
        if ($file !== null) {
            if ($file->getError()===1) {
                throw new FileException($file->getErrorMessage());
            }
            $this->entity->setFile($file);
            $this->saveFileAndUrl($file, $request);
            $saveFolder = $this->entity->getBaseFolder();
            if ($this->entity->getSubCategory() !== 'null') {
                $saveFolder .= '/' . $this->entity->getSubCategory();
            } else $this->entity->setSubCategory('');
            $this->fileManager->persistFile($saveFolder, $file);
            $this->entity->setUrl($host.'/uploads'.$saveFolder.'/'.str_replace(' ', '_', $file->getClientOriginalName()));
            $this->entityManager->flush();
        }
        //$this->getFileAndPersist($request);
        //$this->entityManager->flush();
        //dump(['entity' => $this->entity]);
        //endregion
        return $this->entity;
    }
}