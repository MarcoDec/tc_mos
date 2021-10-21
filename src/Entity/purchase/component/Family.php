<?php

namespace App\Entity;

use App\Repository\FamilyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FamilyRepository::class)]
class Family extends Entity
{


    #[ORM\Column( length: 255, nullable: true)]
    private ?string $code=null;

    #[ORM\Column(type: 'boolean')]
    private $copperable = false;

    #[ORM\Column(type: 'string', length: 255)]
    private $customsCode;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToOne()]
    private ?Family $parent=null;



    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCopperable(): ?bool
    {
        return $this->copperable;
    }

    public function setCopperable(bool $copperable): self
    {
        $this->copperable = $copperable;

        return $this;
    }

    public function getCustomsCode(): ?string
    {
        return $this->customsCode;
    }

    public function setCustomsCode(string $customsCode): self
    {
        $this->customsCode = $customsCode;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
