<?php

namespace App\Entity\Management;

use App\Doctrine\Type\Type;
use App\Entity\Entity;
use App\Entity\Hr\Parameter as HrParam;
use App\Entity\Traits\NameTrait;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
   ORM\MappedSuperclass,
   ORM\DiscriminatorColumn("process","string"),
   ORM\DiscriminatorMap(Parameter::PROCESSES),
   ORM\Entity,
   ORM\InheritanceType("SINGLE_TABLE")
]
class Parameter extends Entity {
    use NameTrait;

    public const PROCESSES = [
        'hr' => HrParam::class,
        'management' => Parameter::class,
    ];

    #[
       ORM\JoinColumn("link"),
       ORM\ManyToOne(Parameter::class,null,"EAGER")
    ]
    private Parameter|null $link;

    #[
       ORM\Column(nullable: true)
    ]
    private string|null $target;

    #[
       ORM\Column(type: "type")
       ]
    private string|null $type;
    /**
     * AppAssert\Directories(parameters={PurchaseParam::SUPPLIER_DIRECTORIES})
     * AppAssert\ExpirationDir(parameters={PurchaseParam::SUPPLIER_EXPIRATION_DIRECTORIES})
     */
    #[
       Assert\NotBlank,
       ORM\Column
       ]
    private string|null $value;

    final public function getLink(): ?Parameter {
        return $this->link;
    }

    final public function getTarget(): ?string {
        return $this->target;
    }

    final public function getType(): ?string {
        return $this->type;
    }

    final public function getTypedValue() {
        switch ($this->type) {
            case Type::TYPE_SELECT_MULTIPLE_LINK:
            case Type::TYPE_ARRAY:
                return !empty($this->value) ? explode(',', $this->value) : [];
            default:
                return null;
        }
    }

    final public function getValue(): ?string {
        return $this->value;
    }

    final public function isIn(array $names): bool {
        return in_array($this->name, $names);
    }

    final public function setLink(?Parameter $link): Parameter {
        $this->link = $link;

        return $this;
    }

    final public function setTarget(?string $target): self {
        $this->target = $target;

        return $this;
    }

    final public function setType(?string $type): Parameter {
        $this->type = $type;

        return $this;
    }

    final public function setValue(?string $value): Parameter {
        $this->value = $value;

        return $this;
    }
}
