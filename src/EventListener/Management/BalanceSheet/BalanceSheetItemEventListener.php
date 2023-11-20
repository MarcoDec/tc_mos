<?php

namespace App\EventListener\Management\BalanceSheet;

use App\Entity\Management\Currency;
use App\Entity\Management\Society\Company\BalanceSheetItem;
use App\Entity\Embeddable\Measure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class BalanceSheetItemEventListener
{
    public function __construct(private EntityManagerInterface $entityManager, private LoggerInterface $logger)
    {
    }

    private function loadMeasureUnitAsCurrency(Measure &$measure):void
    {
        if ($measure->getCode() !== '') {
            $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => $measure->getCode()]);
            if ($currency) {
                if ($currency) {
                    $parentCurrency = $currency->getParent();
                    if ($parentCurrency && $parentCurrency->getCode() !== $measure->getCode()) {
                        $parentCurrency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => $parentCurrency->getCode()]);
                        $currency->setParent($parentCurrency);
                    }
                    $measure->setUnit($currency);
                }
            } else {
                $this->logger->error('Impossible de charger l\'unité de mesure', [$measure]);
                throw new \Exception('Impossible de charger l\'unité de mesure');
            }
        }
    }

    private function calculateTotalIncome(BalanceSheetItem $balanceSheetItem): Measure
    {
        $balanceSheetCurrency = $balanceSheetItem->getBalanceSheet()->getCurrency();
        $totalIncome = new Measure();
        $totalIncome->setUnit($balanceSheetCurrency);
        $totalIncome->setCode($balanceSheetCurrency->getCode());
        $this->loadMeasureUnitAsCurrency($totalIncome);
        /** @var BalanceSheetItem $item */
        foreach ($balanceSheetItem->getBalanceSheet()->getBalanceSheetItems() as $item) {
            if ($item->isIncome() && !$item->isDeleted()) {
                $amount = $item->getAmount();
                if ($amount->getValue()>0 && $amount->getCode() !== null) {
                    $this->loadMeasureUnitAsCurrency($amount);
                    $totalIncome->add($amount);
                }
                $vat = $item->getVat();
                if ($vat->getValue()>0 && $vat->getCode() !== null) {
                    $this->loadMeasureUnitAsCurrency($vat);
                    $totalIncome->add($vat);
                }
            }
        }
        return $totalIncome;
    }
    private function calculateTotalExpense(BalanceSheetItem $balanceSheetItem): Measure
    {
        $balanceSheetCurrency = $balanceSheetItem->getBalanceSheet()->getCurrency();
        $totalExpense = new Measure();
        $totalExpense->setCode($balanceSheetCurrency->getCode());
        $totalExpense->setUnit($balanceSheetCurrency);
        $this->loadMeasureUnitAsCurrency($totalExpense);
        foreach ($balanceSheetItem->getBalanceSheet()->getBalanceSheetItems() as $item) {
            if ($item->isExpense() && !$item->isDeleted()) {
                $amount = $item->getAmount();
                if ($amount->getValue() >0 && $amount->getCode() !== null) {
                    $this->loadMeasureUnitAsCurrency($amount);
                    $totalExpense->add($amount);

                }
                $vat = $item->getVat();
                if ($vat->getValue() > 0 && $vat->getCode() !== null) {
                    $this->loadMeasureUnitAsCurrency($vat);
                    $totalExpense->add($vat);
                }
            }
        }
        return $totalExpense;
    }
    private function refreshIncomeAndExpense(BalanceSheetItem $balanceSheetItem, LifecycleEventArgs $event) {
        $parentBalanceSheet = $balanceSheetItem->getBalanceSheet();
        $totalIncome = $this->calculateTotalIncome($balanceSheetItem);
        $totalExpense = $this->calculateTotalExpense($balanceSheetItem);
        $parentBalanceSheet->setTotalIncome($totalIncome);
        $parentBalanceSheet->setTotalExpense($totalExpense);
        $event->getEntityManager()->flush();
    }
    public function postPersist(BalanceSheetItem $balanceSheetItem, LifecycleEventArgs $event): void
    {
        // Lorsqu'on ajoute un nouvel item, on doit mettre à jour les totaux du bilan associé
        $this->refreshIncomeAndExpense($balanceSheetItem,$event);
    }
    public function postUpdate(BalanceSheetItem $balanceSheetItem, LifecycleEventArgs $event): void
    {
        // Lorsqu'on modifie un nouvel item, on doit mettre à jour les totaux du bilan associé
        $this->refreshIncomeAndExpense($balanceSheetItem,$event);
    }
}