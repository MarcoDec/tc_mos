<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Entity;
use App\Entity\Logistics\Carrier;
use App\Entity\Logistics\Incoterms;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Management\Color;
use App\Entity\Management\InvoiceTimeDue;
use App\Entity\Management\Unit;
use App\Entity\Management\VatMessage;
use App\Entity\Production\Company\Zone;
use App\Entity\Production\Engine\Group;
use App\Entity\Production\Engine\Manufacturer\Manufacturer;
use App\Entity\Project\Operation\Operation;
use App\Entity\Project\Operation\Type as OperationType;
use App\Entity\Quality\Production\ComponentReferenceValue;
use App\Entity\Quality\Reject\Type as RejectType;
use App\Entity\Quality\Type as QualityType;
use Doctrine\ORM\EntityManagerInterface;

final class SimpleDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param Entity  $data
     * @param mixed[] $context
     */
    public function persist($data, array $context = []): Entity {
        $this->em->persist($data);
        $this->em->flush();
        return $data;
    }

    /**
     * @param Entity  $data
     * @param mixed[] $context
     */
    public function remove($data, array $context = []): void {
    }

    /** @param mixed[] $context */
    public function supports($data, array $context = []): bool {
        return ($data instanceof Zone && isset($context['item_operation_name']) && $context['item_operation_name'] === 'patch')
            || (
                (
                    $data instanceof Carrier
                    || $data instanceof Color
                    || $data instanceof ComponentReferenceValue
                    || $data instanceof Group
                    || $data instanceof Incoterms
                    || $data instanceof InvoiceTimeDue
                    || $data instanceof Manufacturer
                    || $data instanceof Operation
                    || $data instanceof OperationType
                    || $data instanceof QualityType
                    || $data instanceof RejectType
                    || $data instanceof Stock
                    || $data instanceof Unit
                    || $data instanceof VatMessage
                ) && (
                    (isset($context['collection_operation_name']) && in_array($context['collection_operation_name'], ['post', 'receipt']))
                    || (isset($context['item_operation_name']) && in_array($context['item_operation_name'], ['out', 'patch']))
                )
            );
    }
}
