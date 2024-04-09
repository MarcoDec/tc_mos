<?php

namespace App\DataPersister\Logistics\Label;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Logistics\Label\Carton;
use App\Service\LabelCartonManager;

class CartonDataPersister implements DataPersisterInterface
{
    public function __construct(private readonly LabelCartonManager $labelCartonManager) {
    }
    public function supports($data): bool
    {
        return $data instanceof Carton;
    }

    public function persist($data): void
    {
        $this->labelCartonManager->generateZPL($data);
    }

    public function remove($data): void
    {
        $this->labelCartonManager->removeLabel($data);
    }
}