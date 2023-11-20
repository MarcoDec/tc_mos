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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

abstract class BalanceSheetItemAbstractController
{
    protected BalanceSheetItem $entity;
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileManager            $fileManager) {
    }

    protected function getIdFromIri(string $iri): int {
        $idRegExp= '/(\d+)$/';
        preg_match($idRegExp, $iri, $matches);
        if (!empty($matches)) {
            return $matches[0];
        } else {
            throw new RuntimeException("l'iri $iri ne contient pas d'id");
        }
    }

    protected function updateMeasureCode(Measure $measure, string $code, string $class): Measure {
        $unitId = $this->getIdFromIri($code);
        $unit = $this->entityManager->getRepository($class)->findOneBy(['id' => $unitId]);
        $measure->setUnit($unit);
        $measure->setCode($unit->getCode());
        return $measure;
    }
    protected function updateUnitMeasureCode(Measure $measure, string $code): Measure {
        return $this->updateMeasureCode($measure, $code, Unit::class);
    }
    protected function updateCurrencyMeasureCode(Measure $measure, string $code): Measure {
        return $this->updateMeasureCode($measure, $code, Currency::class);
    }
    protected function updateMeasureValue(Measure $measure, string $value): Measure {
        $measure->setValue(floatval($value));
        return $measure;
    }

    protected function getMeasureGen($requestParameters, $valueTxt, $currencyTxt, $unitClass): Measure {
        $idRegExp= '/(\d+)$/';
        $measure= new Measure();
        if ($valueTxt !== null && $requestParameters[$valueTxt] !== null) {
            $measure->setValue(floatval($requestParameters[$valueTxt]));
        }
        if ($currencyTxt !== null && $requestParameters[$currencyTxt] !== null) {
            preg_match($idRegExp, $requestParameters[$currencyTxt], $matches);
            if (!empty($matches)) {
                $currencyId = $matches[0];
                $currency = $this->entityManager->getRepository($unitClass)->findOneBy(['id' => $currencyId]);
                $measure->setUnit($currency);
                $measure->setCode($currency->getCode());
            }
        }
        return $measure;
    }
    protected function getMeasureCurrency($requestParameters, $valueTxt, $currencyTxt): Measure {
        return $this->getMeasureGen($requestParameters, $valueTxt, $currencyTxt, Currency::class);
    }
    protected function getMeasureUnit($requestParameters, $valueTxt, $currencyTxt): Measure {
        return $this->getMeasureGen($requestParameters, $valueTxt, $currencyTxt, Unit::class);
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
    protected function getFileFromFILES(): ?File {
        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            if ($file['error'] === 0) {
                return new File($file['tmp_name']);
            } else {
                throw new FileException($file['error']);
            }
        } else {
            return null;
        }
    }
    public function getFileAndPersist(Request $request, bool $withError = true):void {
        $file = $request->files->get('file');
        if ($file === null) {
            // no file uploaded
            // if a file was present before, we delete it
            //            if ($this->entity->getUrl() !== '') {
            //                dump('delete file');
            //                $this->entity->setUrl('');
            //                $this->entityManager->flush();
            //            }
        } else {
            if ($withError && $file->getError()===1) {
                throw new FileException($file->getErrorMessage());
            }
            $this->entity->setFile($file);
            if ($this->entity->getId() === null) {
                $this->entityManager->persist($this->entity);
                $this->entityManager->flush(); // pour récupération id utilisé par default dans getBaseFolder
            }
            $this->saveFileAndUrl($file, $request);
        }

    }
    protected function saveFileAndUrl(UploadedFile $file, Request $request):void {
        $host = $request->getSchemeAndHttpHost();
        $saveFolder = $this->entity->getBaseFolder();
        if ($this->entity->getSubCategory() !== 'null') {
            $saveFolder .= '/' . $this->entity->getSubCategory();
        } else $this->entity->setSubCategory('');
        $this->fileManager->persistFile($saveFolder, $file);
        $this->entity->setUrl($host.'/uploads'.$saveFolder.'/'.str_replace(' ', '_', $file->getClientOriginalName()));
        $this->entityManager->flush(); // pour persist du chemin vers le fichier
    }

    public function parseMultipartFormData($content) {
        $data = [];
        // Séparation des différents éléments
        $boundary = substr($content, 0, strpos($content, "\r\n"));
//        dump([
//            'content' => $content,
//            'boundary' => $boundary
//        ]);
        foreach (explode($boundary, $content) as $item) {
            if (trim($item)) {
                // Extraction de la clé et de la valeur
                $exploded = explode("\r\n\r\n", trim($item), 2);
                if (count($exploded) === 2) {
                    list($rawHeaders, $value) = $exploded;
                    // Recherche de la clé dans les en-têtes
                    if (preg_match('/name="([^"]+)"/', $rawHeaders, $matches)) {
                        if ($matches[1] === 'file') continue;
                        $value = trim($value);
                        $data[$matches[1]] = $value;
                    }
                }
            }
        }

        return $data;
    }
}