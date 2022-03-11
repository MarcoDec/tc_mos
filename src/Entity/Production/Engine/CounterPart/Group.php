<?php

namespace App\Entity\Production\Engine\CounterPart;

use App\Entity\Production\Engine\Group as EngineGroup;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Group extends EngineGroup {
    final public function getType(): string {
        return 'counter-part';
    }
}
