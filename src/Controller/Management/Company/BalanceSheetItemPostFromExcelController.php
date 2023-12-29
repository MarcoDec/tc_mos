<?php

namespace App\Controller\Management\Company;

use App\Entity\Management\Currency;
use App\Entity\Management\Society\Company\BalanceSheet;
use App\Entity\Management\Society\Company\BalanceSheetItem;
use App\Entity\Management\Unit;
use App\Filesystem\FileManager;
use App\Entity\Embeddable\Measure;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;

class BalanceSheetItemPostFromExcelController extends BalanceSheetItemAbstractController
{
    protected BalanceSheetItem $entity;
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileManager            $fileManager) {
    }
    public function __invoke(Request $request):BalanceSheetItem {
        $content = $request->getContent();
        $this->entity = new BalanceSheetItem();
        $data = json_decode($content, true);
        // Champs balanceSheet
        $id = $this->getIdFromIri($data['balanceSheet']);
        /** @var BalanceSheet $balanceSheet */
        $balanceSheet = $this->entityManager->getRepository(BalanceSheet::class)->findOneBy(['id' => $id]);
        $this->entity->setBalanceSheet($balanceSheet);
        $defaultCurrency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'EUR']);
        $currencyOfAmount = $defaultCurrency;
        $currencyOfVat = $defaultCurrency;
        if ($balanceSheet->getCompany()->getId() === 3) {
            $currencyOfAmount = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'TND']);
        }
        // Champs texte
        $this->entity->setSubCategory($data['subCategory']);
        $this->entity->setPaymentRef($data['paymentRef']);
        $this->entity->setStakeholder($data['stakeholder']);
        $this->entity->setLabel($data['label']);
        $this->entity->setPaymentMethod($data['paymentMethod']);
        $this->entity->setPaymentCategory($data['paymentCategory']);
        // Champs dates
        $this->entity->setPaymentDate(new \DateTime($data['paymentDate']));
        $this->entity->setBillDate(new \DateTime($data['billDate']));
        // Champs montant
        $amount = new Measure();
        $amount->setValue(floatval($data['amount']));
        $amount->setUnit($currencyOfAmount);
        $amount->setCode($currencyOfAmount->getCode());
        $this->entity->setAmount($amount);
        $vat = new Measure();
        $vat->setValue(floatval($data['vat']));
        $vat->setUnit($currencyOfVat);
        $vat->setCode($currencyOfVat->getCode());
        $this->entity->setVat($vat);
        // Champ Measure
        $quantity = new Measure();
        $quantity->setValue(floatval($data['quantity']));
        //TODO: implémenter logique plus fine un jour Par défault l'unité est U
        $quantity->setUnit($this->entityManager->getRepository(Unit::class)->findOneBy(['code' => 'U']));
        $quantity->setCode('U');
        $this->entity->setQuantity($quantity);

        //Update Totaux balanceSheet
        $balanceSheet->refreshIncomeAndExpense();
        // Persist & Flush
        $this->entityManager->persist($this->entity);
        $this->entityManager->flush();
        return $this->entity;
    }
}