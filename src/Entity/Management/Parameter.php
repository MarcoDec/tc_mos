<?php

namespace App\Entity\Management;

use App\Doctrine\Type\Type;
use App\Entity\Entity;
use App\Entity\Hr\Parameter as HrParam;
use App\Entity\Purchase\Parameter as PurchaseParam;
use App\Entity\Production\Parameter as ProductionParam;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
   ORM\MappedSuperclass,
   ORM\DiscriminatorColumn('process', 'string'),
   ORM\DiscriminatorMap(Parameter::PROCESSES),
   ORM\Entity,
   ORM\InheritanceType('SINGLE_TABLE')
]
class Parameter extends Entity {

   #[
      ORM\Column(nullable: true)
   ]
   private string|null $description;

   #[ ORM\Column(type: "string")
      ]
   private string $name;

    public const PROCESSES = [
        'hr' => HrParam::class,
        'management' => self::class,
        'purchase' => PurchaseParam::class,
        'production' => ProductionParam::class
    ];

    #[
       ORM\JoinColumn('link'),
       ORM\ManyToOne(self::class, null, 'EAGER')
    ]
    private Parameter|null $link;

    #[
       ORM\Column(nullable: true)
    ]
    private string|null $target;

    #[
       ORM\Column(type: 'type')
       ]
    private string|null $type;

    /**
     * AppAssert\Directories(parameters={PurchaseParam::SUPPLIER_DIRECTORIES})
     * AppAssert\ExpirationDir(parameters={PurchaseParam::SUPPLIER_EXPIRATION_DIRECTORIES}).
     */
    #[
       Assert\NotBlank,
       ORM\Column
       ]
    private string|null $value;

    final public function getLink(): ?self {
        return $this->link;
    }

    final public function getTarget(): ?string {
        return $this->target;
    }

    final public function getType(): ?string {
        return $this->type;
    }

   /**
    * @return array<mixed>
    */
    final public function getTypedValue():array {
        switch ($this->type) {
            case Type::TYPE_SELECT_MULTIPLE_LINK:
            case Type::TYPE_ARRAY:
                return !empty($this->value) ? explode(',', $this->value) : [];
            default:
                return [];
        }
    }

    final public function getValue(): ?string {
        return $this->value;
    }

   /**
    * @param array<null|string> $names
    * @return bool
    */
    final public function isIn(array $names): bool {
        return in_array($this->name, $names);
    }

    final public function setLink(?self $link): self {
        $this->link = $link;

        return $this;
    }

    final public function setTarget(?string $target): self {
        $this->target = $target;

        return $this;
    }

    final public function setType(?string $type): self {
        $this->type = $type;

        return $this;
    }

    final public function setValue(?string $value): self {
        $this->value = $value;

        return $this;
    }

   /**
    * @return string
    */
   public function getName(): string
   {
      return $this->name;
   }

   /**
    * @param string $name
    * @return Parameter
    */
   public function setName(string $name): self
   {
      $this->name = $name;
      return $this;
   }

   /**
    * @return string|null
    */
   public function getDescription(): ?string
   {
      return $this->description;
   }

   /**
    * @param string|null $description
    * @return Parameter
    */
   public function setDescription(?string $description): self
   {
      $this->description = $description;
      return $this;
   }



}
