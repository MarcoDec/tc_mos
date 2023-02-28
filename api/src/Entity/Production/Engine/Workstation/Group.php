<?php

declare(strict_types=1);

namespace App\Entity\Production\Engine\Workstation;

use App\Entity\Production\Engine\Group as EngineGroup;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Group extends EngineGroup {
}
