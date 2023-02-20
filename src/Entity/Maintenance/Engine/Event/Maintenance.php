<?php

namespace App\Entity\Maintenance\Engine\Event;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Maintenance\Engine\Planning;
use App\Entity\Production\Engine\Event\Event;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Entity]
class Maintenance extends Event {
    #[
        ApiProperty(description: 'Planifié par', example: '/api/plannings/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:event'])
    ]
    private ?Planning $plannedBy = null;

    final public function getPlannedBy(): ?Planning {
        return $this->plannedBy;
    }

    final public function setPlannedBy(?Planning $plannedBy): self {
        $this->plannedBy = $plannedBy;
        return $this;
    }
}
