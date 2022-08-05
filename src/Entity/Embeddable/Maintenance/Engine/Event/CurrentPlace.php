<?php

namespace App\Entity\Embeddable\Maintenance\Engine\Event;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Doctrine\DBAL\Types\Production\Engine\CurrentPlaceType;
use App\Entity\Embeddable\CurrentPlace as AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    final public const TRANSITIONS = [self::TR_VALIDATE];

    #[
        ApiProperty(description: 'Nom', openapiContext: ['enum' => CurrentPlaceType::TYPES]),
        Assert\Choice(choices: CurrentPlaceType::TYPES),
        Assert\NotBlank,
        ORM\Column(type: 'engine_event_current_place', options: ['default' => CurrentPlaceType::TYPE_AGREED]),
        Serializer\Groups(['read:current-place'])
    ]
    protected ?string $name = null;

    public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : CurrentPlaceType::TYPE_AGREED);
    }

    #[Pure]
    final public function getTrafficLight(): int {
        return $this->getName() === CurrentPlaceType::TYPE_AGREED ? 1 : 3;
    }

    final public function isDeletable(): bool {
        return true;
    }

    final public function isFrozen(): bool {
        return $this->name === CurrentPlaceType::TYPE_CLOSED;
    }
}
