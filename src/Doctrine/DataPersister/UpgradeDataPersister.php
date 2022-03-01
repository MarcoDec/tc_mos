<?php

namespace App\Doctrine\DataPersister;

use App\Doctrine\Entity\Project\Product\Product;

final class UpgradeDataPersister extends CloneDataPersister {
    public function supports($data, array $context = []): bool {
        return parent::supports($data, $context) && $data instanceof Product;
    }

    /**
     * @param Product $data
     * @param Product $clone
     */
    protected function update($data, $clone): void {
        if (null !== $parent = $this->em->find(Product::class, $data->getId())) {
            $parent->addChild($clone);
        }
    }
}
