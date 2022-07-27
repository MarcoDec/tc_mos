<?php

namespace App\DataPersister\Logistics\Stock;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Logistics\Stock\Stock;
use App\Service\MeasureHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class TransferDataPersister implements DataPersisterInterface {
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MeasureHydrator $hydrator,
        private readonly RequestStack $requests
    ) {
    }

    /**
     * @param Stock<MeasuredInterface> $data
     */
    public function persist($data): void {
        $this->hydrator->hydrateIn($data);
        /** @var Request $request */
        $request = $this->requests->getCurrentRequest();
        /** @var Stock<MeasuredInterface> $previous */
        $previous = $request->attributes->get('previous_data');
        $this->hydrator->hydrateIn($previous);
        $this->em->persist($transfered = clone $data);
        $data
            ->setQuantity($previous->getQuantity())
            ->substract(clone $transfered->getQuantity())
            ->setWarehouse($previous->getWarehouse());
        $this->em->flush();
    }

    public function remove($data): void {
    }

    public function supports($data): bool {
        $request = $this->requests->getCurrentRequest();
        return !empty($request)
            && $request->attributes->get('_api_item_operation_name') === 'transfer'
            && $data instanceof Stock;
    }
}
