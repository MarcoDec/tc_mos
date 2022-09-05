<?php

namespace App\Controller\Quality\Reception;

use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Order\Item;
use App\Entity\Quality\Reception\Check;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CheckController {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @return Check<Component|Product>[]
     */
    public function __invoke(int $id): array {
        $item = $this->em->find(Item::class, $id);
        if (empty($item)) {
            throw new NotFoundHttpException();
        }
        $checks = $item->getChecks();
        foreach ($checks as $check) {
            $this->em->persist($check);
        }
        $this->em->flush();
        return $checks->all();
    }
}
