<?php

namespace App\DataPersister\Logistics\Stock;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class TransferDataPersister implements DataPersisterInterface {
    public function __construct(private readonly EntityManagerInterface $em, private readonly RequestStack $requests) {
    }

    /**
     * @param Stock<Component|Product> $data
     */
    public function persist($data): void {
        /** @var Request $request */
        $request = $this->requests->getCurrentRequest();
        /** @var Stock<Component|Product> $previous */
        $previous = $request->attributes->get('previous_data');
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
