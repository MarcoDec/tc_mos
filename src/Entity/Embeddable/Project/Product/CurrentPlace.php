<?php

namespace App\Entity\Embeddable\Project\Product;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Project\Product\CurrentPlaceType;
use App\Entity\Embeddable\CurrentPlace as AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    final public const TRANSITIONS = [
        self::TR_BLOCK,
        self::TR_DISABLE,
        self::TR_PARTIALLY_UNLOCK,
        self::TR_PARTIALLY_VALIDATE,
        self::TR_SUBMIT_VALIDATION,
        self::TR_UNLOCK,
        self::TR_VALIDATE
    ];

    #[
        ApiProperty(description: 'Nom', openapiContext: ['enum' => CurrentPlaceType::TYPES]),
        Assert\Choice(choices: CurrentPlaceType::TYPES),
        Assert\NotBlank,
        ORM\Column(type: 'product_current_place', options: ['charset' => 'ascii', 'default' => CurrentPlaceType::TYPE_DRAFT]),
        Serializer\Groups(['read:current-place'])
    ]
    protected ?string $name = null;

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : CurrentPlaceType::TYPE_DRAFT);
    }

    #[Pure]
    final public function getTrafficLight(): int {
        return match ($this->getName()) {
            CurrentPlaceType::TYPE_AGREED => 1,
            CurrentPlaceType::TYPE_BLOCKED, CurrentPlaceType::TYPE_DISABLED => 3,
            default => 2,
        };
    }

    final public function isDeletable(): bool {
        return in_array($this->name, [CurrentPlaceType::TYPE_DISABLED, CurrentPlaceType::TYPE_DRAFT]);
    }

    final public function isFrozen(): bool {
        return $this->name === CurrentPlaceType::TYPE_DISABLED;
    }
}
