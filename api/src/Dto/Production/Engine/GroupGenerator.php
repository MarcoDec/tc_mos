<?php

declare(strict_types=1);

namespace App\Dto\Production\Engine;

use ApiPlatform\Metadata\ApiProperty;
use App\Doctrine\Type\Production\Engine\EngineType;
use App\Dto\Generator;
use App\Entity\Production\Engine\Group;
use Symfony\Component\Validator\Constraints as Assert;

class GroupGenerator implements Generator {
    #[
        ApiProperty(description: 'Code', example: 'TA'),
        Assert\Length(min: 2, max: 3),
        Assert\NotBlank
    ]
    private ?string $code = null;

    #[
        ApiProperty(description: 'Name', example: 'Table d\'assemblage'),
        Assert\Length(min: 3, max: 35),
        Assert\NotBlank
    ]
    private ?string $name = null;

    #[
        ApiProperty(description: 'Organe sécurité', default: false, example: false)
    ]
    private bool $safetyDevice = false;

    #[
        ApiProperty(description: 'Type', example: EngineType::TYPE_WORKSTATION, openapiContext: ['enum' => EngineType::ENUM]),
        Assert\Choice(EngineType::ENUM),
        Assert\NotBlank
    ]
    private ?string $type = null;

    public function generate(): Group {
        $class = EngineType::TYPES[$this->type];
        return (new $class())
            ->setCode($this->code)
            ->setName($this->name)
            ->setSafetyDevice($this->safetyDevice);
    }

    public function getCode(): ?string {
        return $this->code;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function isSafetyDevice(): bool {
        return $this->safetyDevice;
    }

    public function setCode(?string $code): self {
        $this->code = $code;
        return $this;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    public function setSafetyDevice(bool $safetyDevice): self {
        $this->safetyDevice = $safetyDevice;
        return $this;
    }

    public function setType(?string $type): self {
        $this->type = $type;
        return $this;
    }
}
