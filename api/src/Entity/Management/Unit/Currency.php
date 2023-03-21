<?php

declare(strict_types=1);

namespace App\Entity\Management\Unit;

use App\Repository\Management\Unit\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency extends Unit {
}
