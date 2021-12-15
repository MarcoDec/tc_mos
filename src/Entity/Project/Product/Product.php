<?php

namespace App\Entity\Project\Product;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Logistics\Incoterms;
use App\Entity\Project\Product\Family;
use App\Entity\Entity;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Traits\BarCodeTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\RefTrait;
use App\Entity\Interfaces\BarCodeInterface;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: ['ref' => 'partial']),
    ApiResource(
        description: 'Produit',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les produits',
                    'summary' => 'Récupère les produits',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un produit',
                    'summary' => 'Créer un produit',
                ]
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un produit',
                    'summary' => 'Supprime un produit',
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie un produit',
                    'summary' => 'Modifie un produit',
                ]
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_PROJECT_WRITER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:product', 'write:incoterms'],
            'openapi_definition_name' => 'Product-write'
        ],
        normalizationContext: [
            'groups' => ['read:society', 'read:id', 'read:incoterms'],
            'openapi_definition_name' => 'Product-read'
        ],
    ),
    ORM\Entity
]
class Product extends Entity implements BarCodeInterface
{
    use BarCodeTrait;
    use NameTrait, RefTrait {
        RefTrait::__toString insteadof NameTrait;
    }

    public const KIND_EI = 'EI';
    public const KIND_PROTOTYPE = 'Prototype';
    public const KIND_SERIES = 'Série';
    public const KIND_SPARE = 'Pièce de rechange';
    public const PRODUCT_KINDS = [
        self::KIND_EI => self::KIND_EI,
        self::KIND_PROTOTYPE => self::KIND_PROTOTYPE,
        self::KIND_SERIES => self::KIND_SERIES,
        self::KIND_SPARE => self::KIND_SPARE,
    ];

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'children')]
    private ?Product $parent;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Product::class)]
    private $children;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $customsCode;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $expirationDate;

    #[ORM\ManyToOne(targetEntity: Family::class)]
    private ?Family $family;

    #[ORM\ManyToOne(targetEntity: Incoterms::class)]
    private $incoterms;

    #[ORM\Column(type: 'string', length: 255, name:'product_index')]
    private string $index;

    #[ORM\Column(options: ['default' => 1, 'unsigned' => true], type: 'smallint')]
    private int $internalIndex = 1;

    #[ORM\Column(options: ['default' => self::KIND_PROTOTYPE], type: 'string', length: 255)]
    private ?string $kind = self::KIND_PROTOTYPE;

    #[ORM\Column(options: ['default' => false], type: 'boolean')]
    private bool $managedCopper = false;

    #[ORM\Column(options: ['default' => 3, 'unsigned' => true], type: 'smallint')]
    private int $maxProto = 3;

    #[ORM\Column(options: ['default' => 10, 'unsigned' => true], type: 'smallint')]
    private int $minDelivery = 10;

    #[ORM\Column(options: ['default' => 10, 'unsigned' => true], type: 'smallint')]
    private int $minProd = 10;

    #[ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'integer')]
    private int $minStock = 0;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $notes;

    #[ORM\Column(options: ['default' => 1, 'unsigned' => true], type: 'integer')]
    private int $packaging = 1;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $packagingKind;

    #[ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float')]
    private float $price = 0;

    #[ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float')]
    private float $priceWithoutCopper = 0;

    #[ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'smallint')]
    private int $productionDelay = 0;

    #[ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float')]
    private float $transfertPriceSupplies = 0;

    #[ORM\Column(options: ['default' => 0, 'unsigned' => true], type: 'float')]
    private float $transfertPriceWork = 0;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public static function getBarCodeTableNumber(): string {
        return self::PRODUCT_BAR_CODE_TABLE_NUMBER;
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

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getCustomsCode(): ?string
    {
        return $this->customsCode;
    }

    public function setCustomsCode(?string $customsCode): self
    {
        $this->customsCode = $customsCode;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getFamily(): ?Family
    {
        return $this->family;
    }

    public function setFamily(?Family $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getIncoterms(): ?Incoterms
    {
        return $this->incoterms;
    }

    public function setIncoterms(?Incoterms $incoterms): self
    {
        $this->incoterms = $incoterms;

        return $this;
    }

    public function getIndex(): ?string
    {
        return $this->index;
    }

    public function setIndex(string $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function getInternalIndex(): ?int
    {
        return $this->internalIndex;
    }

    public function setInternalIndex(int $internalIndex): self
    {
        $this->internalIndex = $internalIndex;

        return $this;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(string $kind): self
    {
        $this->kind = $kind;

        return $this;
    }

    public function getManagedCopper(): ?bool
    {
        return $this->managedCopper;
    }

    public function setManagedCopper(bool $managedCopper): self
    {
        $this->managedCopper = $managedCopper;

        return $this;
    }

    public function getMaxProto(): ?int
    {
        return $this->maxProto;
    }

    public function setMaxProto(int $maxProto): self
    {
        $this->maxProto = $maxProto;

        return $this;
    }

    public function getMinDelivery(): ?int
    {
        return $this->minDelivery;
    }

    public function setMinDelivery(int $minDelivery): self
    {
        $this->minDelivery = $minDelivery;

        return $this;
    }

    public function getMinProd(): ?int
    {
        return $this->minProd;
    }

    public function setMinProd(int $minProd): self
    {
        $this->minProd = $minProd;

        return $this;
    }

    public function getMinStock(): ?int
    {
        return $this->minStock;
    }

    public function setMinStock(int $minStock): self
    {
        $this->minStock = $minStock;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getPackaging(): ?int
    {
        return $this->packaging;
    }

    public function setPackaging(int $packaging): self
    {
        $this->packaging = $packaging;

        return $this;
    }

    public function getPackagingKind(): ?string
    {
        return $this->packagingKind;
    }

    public function setPackagingKind(?string $packagingKind): self
    {
        $this->packagingKind = $packagingKind;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceWithoutCopper(): ?float
    {
        return $this->priceWithoutCopper;
    }

    public function setPriceWithoutCopper(float $priceWithoutCopper): self
    {
        $this->priceWithoutCopper = $priceWithoutCopper;

        return $this;
    }

    public function getProductionDelay(): ?int
    {
        return $this->productionDelay;
    }

    public function setProductionDelay(int $productionDelay): self
    {
        $this->productionDelay = $productionDelay;

        return $this;
    }

    public function getTransfertPriceSupplies(): ?float
    {
        return $this->transfertPriceSupplies;
    }

    public function setTransfertPriceSupplies(float $transfertPriceSupplies): self
    {
        $this->transfertPriceSupplies = $transfertPriceSupplies;

        return $this;
    }

    public function getTransfertPriceWork(): ?float
    {
        return $this->transfertPriceWork;
    }

    public function setTransfertPriceWork(float $transfertPriceWork): self
    {
        $this->transfertPriceWork = $transfertPriceWork;

        return $this;
    }
}
