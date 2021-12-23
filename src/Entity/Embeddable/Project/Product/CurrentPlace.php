<?php

namespace App\Entity\Embeddable\Project\Product;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_AGREED = 'agreed';
    public const WF_PLACE_BLOCKED = 'blocked';
    public const WF_PLACE_DISABLED = 'disabled';
    public const WF_PLACE_DRAFT = 'draft';
    public const WF_PLACE_TO_VALIDATE = 'to_validate';
    public const WF_PLACE_UNDER_EXEMPTION = 'under_exemption';
    public const WF_TR_BLOCK = 'block';
    public const WF_TR_DISABLE = 'disable';
    public const WF_TR_PARTIAL_UNLOCK = 'partial_unlock';
    public const WF_TR_PARTIAL_VALIDATION = 'partial_validation';
    public const WF_TR_SUBMIT_VALIDATION = 'submit_validation';
    public const WF_TR_UNLOCK = 'unlock';
    public const WF_TR_VALIDATE = 'validate';

    public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_DRAFT);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_DISABLED:
            case self::WF_PLACE_BLOCKED:
                return 3;
            case self::WF_PLACE_AGREED:
                return 1;
            default:
                return 2;
        }
    }
}
