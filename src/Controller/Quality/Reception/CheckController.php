<?php

namespace App\Controller\Quality\Reception;

use App\Entity\Management\Society\Company\Company;
use App\Entity\Project\Product\Family as ProductFamily;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family as ComponentFamily;
use App\Entity\Purchase\Order\Item;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Quality\Reception\Check;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CheckController {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @return array<int, Check<Component|Product, Company|Component|ComponentFamily|Product|ProductFamily|Supplier>>
     */
    public function __invoke(int $id): array {
        $item = $this->em->getRepository(Item::class)->findOneByReceipt($id);
        if (empty($item)) {
            throw new NotFoundHttpException();
        }
        $checks = $item->getChecks();
        foreach ($checks as $check) {
            $this->em->persist($check);
        }
        $this->em->flush();
        /** @phpstan-ignore-next-line */
        return $checks->all();
    }
}
