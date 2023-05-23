<?php

namespace App\Entity\Management;

use App\Doctrine\Type\Type;
use App\Entity\Entity;
use App\Entity\Hr\Parameter as HrParam;
use App\Entity\Purchase\Parameter as PurchaseParam;
use App\Entity\Production\Parameter as ProductionParam;
use App\Entity\Project\Parameter as ProjectParam;
use App\Entity\Selling\Parameter as SellingParam;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Filter\DiscriminatorFilter;

#[
   ApiFilter(DiscriminatorFilter::class),
   ApiResource(
    description: 'Paramètre de processus Metier',
    collectionOperations: [
        'get' => [
            'openapi_context' => [
                'description' => 'Récupère les paramètres de processus Metier',
                'summary' => 'Récupère les paramètres de processus Metier',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'type',
                        'required' => false,
                        'schema' => ['enum' => ['hr', 'purchase', 'production', 'project', 'selling'], 'type' => 'string']
                    ]
                ],
            ]
        ]
    ],
    itemOperations: [
        'get',
        'patch'
    ],
    shortName: 'Parameter',
    paginationItemsPerPage: 15,
    paginationClientEnabled: true
    ),
   ORM\MappedSuperclass,
   ORM\DiscriminatorColumn('type', 'string'),
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
        'production' => ProductionParam::class,
        'project' => ProjectParam::class,
        'selling' => SellingParam::class
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
    private string|null $kind;

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

    final public function getKind(): ?string {
        return $this->kind;
    }

   /**
    * @return array<mixed>
    */
    final public function getTypedValue():array {
        switch ($this->kind) {
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

    final public function setKind(?string $type): self {
        $this->kind = $type;

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
