<?php

declare(strict_types=1);

namespace App\Dto\Management\Unit;

use ApiPlatform\Metadata\ApiProperty;
use App\Doctrine\Type\Management\Unit\UnitType;
use App\Dto\Generator;
use App\Entity\Management\Unit\Unit;
use Symfony\Component\Validator\Constraints as Assert;

class UnitGenerator implements Generator {
    #[
        ApiProperty(description: 'Base', example: 1),
        Assert\IdenticalTo(
            value: 1.0,
            message: 'A unit without a parent must have a base equal to {{ compared_value }}.',
            groups: ['base']
        ),
        Assert\NotBlank,
        Assert\Positive
    ]
    private float $base = 1;

    #[
        ApiProperty(description: 'Code', example: 'g'),
        Assert\Length(min: 1, max: 6),
        Assert\NotBlank
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Nom', example: 'Gramme'),
        Assert\Length(min: 5, max: 50),
        Assert\NotBlank
    ]
    private ?string $name = null;

    #[ApiProperty(description: 'Parent', readableLink: false, writableLink: false, example: '/api/units/1')]
    private ?Unit $parent = null;

    #[
        ApiProperty(description: 'Type', example: UnitType::TYPE_UNITARY, openapiContext: ['enum' => UnitType::ENUM]),
        Assert\Choice(UnitType::ENUM),
        Assert\NotBlank
    ]
    private ?string $type = null;

    public function generate(): Unit {
        $class = UnitType::TYPES[$this->type];
        return (new $class())
            ->setBase($this->base)
            ->setCode($this->code)
            ->setName($this->name)
            ->setParent($this->parent);
    }

    public function getBase(): float {
        return $this->base;
    }

    public function getCode(): ?string {
        return $this->code;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getParent(): ?Unit {
        return $this->parent;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setBase(float $base): self {
        $this->base = $base;
        return $this;
    }

    public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    public function setParent(?Unit $parent): self {
        $this->parent = $parent;
        return $this;
    }

    public function setType(?string $type): self {
        $this->type = $type;
        return $this;
    }
}
