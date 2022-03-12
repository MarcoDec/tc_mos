<?php

namespace App\Entity\Project\Product;

use App\Doctrine\DBAL\Types\Project\Product\KindType;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Project\Product\CurrentPlace;
use App\Entity\Entity;
use App\Entity\Interfaces\BarCodeInterface;
use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Logistics\Incoterms;
use App\Entity\Management\Unit;
use App\Entity\Traits\BarCodeTrait;
use App\Validator as AppAssert;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Product extends Entity implements BarCodeInterface, MeasuredInterface {
    use BarCodeTrait;

    #[ORM\Embedded]
    private Measure $autoDuration;

    /** @var Collection<int, self> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $children;

    #[ORM\Embedded]
    private Measure $costingAutoDuration;

    #[ORM\Embedded]
    private Measure $costingManualDuration;

    #[ORM\Embedded(CurrentPlace::class)]
    private CurrentPlace $currentPlace;

    #[
        Assert\Length(min: 4, max: 10),
        ORM\Column(length: 10, nullable: true, options: ['charset' => 'ascii'])
    ]
    private ?string $customsCode = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $expirationDate = null;

    #[
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne
    ]
    private ?Family $family = null;

    #[
        AppAssert\Measure(groups: ['Product-create']),
        ORM\Embedded
    ]
    private Measure $forecastVolume;

    #[ORM\ManyToOne]
    private ?Incoterms $incoterms = null;

    #[
        Assert\Length(min: 1, max: 3, groups: ['Product-admin', 'Product-create']),
        ORM\Column(name: '`index`', length: 3, options: ['charset' => 'ascii'])
    ]
    private ?string $index = null;

    #[
        Assert\NotNull,
        Assert\PositiveOrZero,
        ORM\Column(type: 'tinyint', options: ['default' => 1, 'unsigned' => true])
    ]
    private int $internalIndex = 1;

    #[
        Assert\Choice(choices: KindType::TYPES, groups: ['Product-admin', 'Product-create', 'Product-project']),
        ORM\Column(type: 'product_kind', options: ['default' => KindType::TYPE_PROTOTYPE])
    ]
    private ?string $kind = KindType::TYPE_PROTOTYPE;

    #[ORM\Column(options: ['default' => false])]
    private bool $managedCopper = false;

    #[ORM\Embedded]
    private Measure $manualDuration;

    #[
        AppAssert\Measure(groups: ['Product-project']),
        ORM\Embedded
    ]
    private Measure $maxProto;

    #[ORM\Embedded]
    private Measure $minDelivery;

    #[
        AppAssert\Measure(groups: ['Product-production']),
        ORM\Embedded
    ]
    private Measure $minProd;

    #[
        AppAssert\Measure,
        ORM\Embedded
    ]
    private Measure $minStock;

    #[
        Assert\Length(min: 3, max: 80),
        Assert\NotBlank(groups: ['Product-admin', 'Product-create']),
        ORM\Column(length: 80)
    ]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[
        AppAssert\Measure(groups: ['Product-create', 'Product-production']),
        ORM\Embedded
    ]
    private Measure $packaging;

    #[
        Assert\Length(max: 30, groups: ['Product-create', 'Product-production']),
        ORM\Column(length: 30)
    ]
    private ?string $packagingKind = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    #[ORM\Embedded]
    private Measure $price;

    #[ORM\Embedded]
    private Measure $priceWithoutCopper;

    #[ORM\Embedded]
    private Measure $productionDelay;

    #[
        Assert\Length(min: 3, max: 30),
        ORM\Column(length: 30)
    ]
    private ?string $ref = null;

    #[ORM\Embedded]
    private Measure $transfertPriceSupplies;

    #[ORM\Embedded]
    private Measure $transfertPriceWork;

    #[
        ORM\JoinColumn(nullable: false),
        ORM\ManyToOne
    ]
    private ?Unit $unit = null;

    #[ORM\Embedded]
    private Measure $weight;

    public function __construct() {
        $this->autoDuration = new Measure();
        $this->children = new ArrayCollection();
        $this->currentPlace = new CurrentPlace();
        $this->costingAutoDuration = new Measure();
        $this->costingManualDuration = new Measure();
        $this->forecastVolume = new Measure();
        $this->manualDuration = new Measure();
        $this->maxProto = new Measure();
        $this->minDelivery = new Measure();
        $this->minProd = new Measure();
        $this->minStock = new Measure();
        $this->packaging = new Measure();
        $this->price = new Measure();
        $this->priceWithoutCopper = new Measure();
        $this->productionDelay = new Measure();
        $this->transfertPriceSupplies = new Measure();
        $this->transfertPriceWork = new Measure();
        $this->weight = new Measure();
    }

    public function __clone() {
        parent::__clone();
        $this->children = new ArrayCollection();
        $this->parent = null;
    }

    final public static function getBarCodeTableNumber(): string {
        return self::PRODUCT_BAR_CODE_TABLE_NUMBER;
    }

    final public function addChild(self $children): self {
        if (!$this->children->contains($children)) {
            $this->children->add($children);
            $children->setParent($this);
        }
        return $this;
    }

    final public function getAutoDuration(): Measure {
        return $this->autoDuration;
    }

    /**
     * @return Collection<int, self>
     */
    final public function getChildren(): Collection {
        return $this->children;
    }

    final public function getCostingAutoDuration(): Measure {
        return $this->costingAutoDuration;
    }

    final public function getCostingManualDuration(): Measure {
        return $this->costingManualDuration;
    }

    final public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getCustomsCode(): ?string {
        return $this->customsCode;
    }

    final public function getExpirationDate(): ?DateTimeImmutable {
        return $this->expirationDate;
    }

    final public function getFamily(): ?Family {
        return $this->family;
    }

    final public function getForecastVolume(): Measure {
        return $this->forecastVolume;
    }

    final public function getIncoterms(): ?Incoterms {
        return $this->incoterms;
    }

    final public function getIndex(): ?string {
        return $this->index;
    }

    final public function getInternalIndex(): int {
        return $this->internalIndex;
    }

    final public function getKind(): ?string {
        return $this->kind;
    }

    final public function getManualDuration(): Measure {
        return $this->manualDuration;
    }

    final public function getMaxProto(): Measure {
        return $this->maxProto;
    }

    final public function getMeasures(): array {
        return [
            $this->autoDuration,
            $this->costingAutoDuration,
            $this->costingManualDuration,
            $this->forecastVolume,
            $this->manualDuration,
            $this->maxProto,
            $this->minDelivery,
            $this->minProd,
            $this->minStock,
            $this->packaging,
            $this->price,
            $this->priceWithoutCopper,
            $this->productionDelay,
            $this->transfertPriceSupplies,
            $this->transfertPriceWork,
            $this->weight
        ];
    }

    final public function getMinDelivery(): Measure {
        return $this->minDelivery;
    }

    final public function getMinProd(): Measure {
        return $this->minProd;
    }

    final public function getMinStock(): Measure {
        return $this->minStock;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getPackaging(): Measure {
        return $this->packaging;
    }

    final public function getPackagingKind(): ?string {
        return $this->packagingKind;
    }

    final public function getParent(): ?self {
        return $this->parent;
    }

    final public function getPrice(): Measure {
        return $this->price;
    }

    final public function getPriceWithoutCopper(): Measure {
        return $this->priceWithoutCopper;
    }

    final public function getProductionDelay(): Measure {
        return $this->productionDelay;
    }

    final public function getRef(): ?string {
        return $this->ref;
    }

    final public function getTransfertPriceSupplies(): Measure {
        return $this->transfertPriceSupplies;
    }

    final public function getTransfertPriceWork(): Measure {
        return $this->transfertPriceWork;
    }

    final public function getUnit(): ?Unit {
        return $this->unit;
    }

    final public function getWeight(): Measure {
        return $this->weight;
    }

    #[Pure]
    final public function isDeletable(): bool {
        return $this->currentPlace->isDeletable();
    }

    final public function isManagedCopper(): bool {
        return $this->managedCopper;
    }

    final public function removeChild(self $children): self {
        if ($this->children->contains($children)) {
            $this->children->removeElement($children);
            if ($children->getParent() === $this) {
                $children->setParent(null);
            }
        }
        return $this;
    }

    final public function setAutoDuration(Measure $autoDuration): self {
        $this->autoDuration = $autoDuration;
        return $this;
    }

    final public function setCostingAutoDuration(Measure $costingAutoDuration): self {
        $this->costingAutoDuration = $costingAutoDuration;
        return $this;
    }

    final public function setCostingManualDuration(Measure $costingManualDuration): self {
        $this->costingManualDuration = $costingManualDuration;
        return $this;
    }

    final public function setCurrentPlace(CurrentPlace $currentPlace): self {
        $this->currentPlace = $currentPlace;
        return $this;
    }

    final public function setCustomsCode(?string $customsCode): self {
        $this->customsCode = $customsCode;
        return $this;
    }

    final public function setExpirationDate(?DateTimeImmutable $expirationDate): self {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    final public function setFamily(?Family $family): self {
        $this->family = $family;
        return $this;
    }

    final public function setForecastVolume(Measure $forecastVolume): self {
        $this->forecastVolume = $forecastVolume;
        return $this;
    }

    final public function setIncoterms(?Incoterms $incoterms): self {
        $this->incoterms = $incoterms;
        return $this;
    }

    final public function setIndex(?string $index): self {
        $this->index = $index;
        return $this;
    }

    final public function setInternalIndex(int $internalIndex): self {
        $this->internalIndex = $internalIndex;
        return $this;
    }

    final public function setKind(?string $kind): self {
        $this->kind = $kind;
        return $this;
    }

    final public function setManagedCopper(bool $managedCopper): self {
        $this->managedCopper = $managedCopper;
        return $this;
    }

    final public function setManualDuration(Measure $manualDuration): self {
        $this->manualDuration = $manualDuration;
        return $this;
    }

    final public function setMaxProto(Measure $maxProto): self {
        $this->maxProto = $maxProto;
        return $this;
    }

    final public function setMinDelivery(Measure $minDelivery): self {
        $this->minDelivery = $minDelivery;
        return $this;
    }

    final public function setMinProd(Measure $minProd): self {
        $this->minProd = $minProd;
        return $this;
    }

    final public function setMinStock(Measure $minStock): self {
        $this->minStock = $minStock;
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setPackaging(Measure $packaging): self {
        $this->packaging = $packaging;
        return $this;
    }

    final public function setPackagingKind(?string $packagingKind): self {
        $this->packagingKind = $packagingKind;
        return $this;
    }

    final public function setParent(?self $parent): self {
        $this->parent = $parent;
        return $this;
    }

    final public function setPrice(Measure $price): self {
        $this->price = $price;
        return $this;
    }

    final public function setPriceWithoutCopper(Measure $priceWithoutCopper): self {
        $this->priceWithoutCopper = $priceWithoutCopper;
        return $this;
    }

    final public function setProductionDelay(Measure $productionDelay): self {
        $this->productionDelay = $productionDelay;
        return $this;
    }

    final public function setRef(?string $ref): self {
        $this->ref = $ref;
        return $this;
    }

    final public function setTransfertPriceSupplies(Measure $transfertPriceSupplies): self {
        $this->transfertPriceSupplies = $transfertPriceSupplies;
        return $this;
    }

    final public function setTransfertPriceWork(Measure $transfertPriceWork): self {
        $this->transfertPriceWork = $transfertPriceWork;
        return $this;
    }

    final public function setUnit(?Unit $unit): self {
        $this->unit = $unit;
        return $this;
    }

    final public function setWeight(Measure $weight): self {
        $this->weight = $weight;
        return $this;
    }
}
