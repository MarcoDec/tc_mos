<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Logistics\Carrier;
use App\Entity\Logistics\Incoterms;
use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Management\Color;
use App\Entity\Management\InvoiceTimeDue;
use App\Entity\Management\Unit;
use App\Entity\Management\VatMessage;
use App\Entity\Production\Engine\Manufacturer\Manufacturer;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @phpstan-type Data Carrier|Color|ComponentStock|Incoterms|InvoiceTimeDue|Manufacturer|ProductStock|Unit|VatMessage
 */
final class SimpleDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param Data    $data
     * @param mixed[] $context
     */
    public function persist($data, array $context = []): Carrier|Color|ComponentStock|Incoterms|InvoiceTimeDue|Manufacturer|ProductStock|Unit|VatMessage {
        $this->em->persist($data);
        $this->em->flush();
        return $data;
    }

    /**
     * @param Data    $data
     * @param mixed[] $context
     */
    public function remove($data, array $context = []): void {
    }

    /**
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return (
            $data instanceof Carrier
            || $data instanceof Color
            || $data instanceof Incoterms
            || $data instanceof InvoiceTimeDue
            || $data instanceof Manufacturer
            || $data instanceof Stock
            || $data instanceof Unit
            || $data instanceof VatMessage
        ) && (
            (isset($context['collection_operation_name']) && in_array($context['collection_operation_name'], ['post', 'receipt']))
            || (isset($context['item_operation_name']) && in_array($context['item_operation_name'], ['out', 'patch']))
        );
    }
}
