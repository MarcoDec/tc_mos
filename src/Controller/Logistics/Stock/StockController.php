<?php

namespace App\Controller\Logistics\Stock;

use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Logistics\Stock\Stock;
use App\Service\MeasureHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class StockController {
    public function __construct(private readonly EntityManagerInterface $em, private readonly MeasureHydrator $hydrator) {
    }

    /**
     * @return Stock<MeasuredInterface>
     */
    public function __invoke(Request $request): Stock {
        /** @var Stock<MeasuredInterface> $data */
        $data = $request->attributes->get('data');
        $this->hydrator->hydrateIn($data);
        /** @var Stock<MeasuredInterface> $previous */
        $previous = $request->attributes->get('previous_data');
        $this->hydrator->hydrateIn($previous);
        $previous->substract($data->getQuantity());
        $request->attributes->set('data', $previous);
        $this->em->persist(clone $data);
        $this->em->persist($previous);
        return $previous;
    }
}
