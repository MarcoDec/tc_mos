<?php

namespace App\Entity\Quality\Reception;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Entity;
use App\Entity\Management\Society\Company;
use App\Entity\Project\Product\Family as ProductFamily;
use App\Entity\Project\Product\Product;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family as ComponentFamily;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

abstract class Reference extends Entity {
    use NameTrait;

    public const KIND_DIM = 'Dimensionnel';
    public const KIND_DOC = 'Documentaire';
    public const KIND_GON = 'GO/NOGO';
    public const KIND_QTE = 'Quantitatif';
    public const KIND_VIS = 'Visuel';
    public const KINDS = [self::KIND_DOC, self::KIND_DIM, self::KIND_GON, self::KIND_VIS, self::KIND_QTE];
    public const TYPES = [
        'product' => ProductReference::class,
        'component' => ComponentReference::class,
    ];

    /**
     * @var Collection<int, ComponentFamily|ProductFamily>
     */
    protected $families;

    /**
     * @var Collection<int, Component|Product>
     */
    protected $items;

    /**
     * @var Collection<int, Company>
     */
    #[
        ApiProperty(description: 'Compagnies', readableLink: false, example: ['/api/companies/2', '/api/companies/18']),
        ORM\ManyToMany(fetch: 'EXTRA_LAZY', targetEntity: Company::class, mappedBy: 'references'),
        Serializer\Groups(['read:companies', 'write:companies'])
    ]
    private Collection $companies;

    #[
        ApiProperty(description: 'Global ?', required: true, example: true),
        ORM\Column(type: 'boolean', options: ['default' => false]),
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    private bool $global = false;

    #[
        ApiProperty(description: 'IPv4', required: true, example: '255.255.255.254'),
        ORM\Column(type: 'string', options: ['default' => self::KIND_QTE]),
        Assert\NotBlank,
        Serializer\Groups(['read:reference', 'write:reference'])
    ]
    private string $kind = self::KIND_QTE;

    /**
     * @var Collection<int, Supplier>
     */
    #[
        ApiProperty(description: 'Références', readableLink: false, example: ['/api/suppliers/2', '/api/suppliers/15']),
        ORM\ManyToMany(fetch: 'EXTRA_LAZY', targetEntity: Supplier::class, mappedBy: 'references'),
        Serializer\Groups(['read:references', 'write:references'])
    ]
    private Collection $suppliers;

    public function __construct() {
        $this->companies = new ArrayCollection();
        $this->families = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->suppliers = new ArrayCollection();
    }

    abstract public function addFamily(object $family): self;

    abstract public function addItem(object $item): self;

    abstract public function getItemType(): string;

    abstract public function removeFamily(object $family): self;

    abstract public function removeItem(object $item): self;

    final public function addCompany(Company $company): self {
        if (!$this->companies->contains($company)) {
            $this->companies->add($company);
        }
        return $this;
    }

    public function addSupplier(Supplier $supplier): self {
        if (!$this->suppliers->contains($supplier)) {
            $this->suppliers[] = $supplier;
            $supplier->addReference($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getCompanies(): Collection {
        return $this->companies;
    }

    /**
     * @return Collection<int, ComponentFamily|ProductFamily>
     */
    public function getFamilies(): Collection {
        return $this->families;
    }

    public function getGlobal(): ?bool {
        return $this->global;
    }

    /**
     * @return Collection<int, Component|Product>
     */
    public function getItems(): Collection {
        return $this->items;
    }

    public function getKind(): string {
        return $this->kind;
    }

    /**
     * @return Collection|Supplier[]
     */
    public function getSuppliers(): Collection {
        return $this->suppliers;
    }

    public function isGlobal(): bool {
        return $this->global;
    }

    final public function removeCompany(Company $company): self {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
        }
        return $this;
    }

    public function removeSupplier(Supplier $supplier): self {
        if ($this->suppliers->removeElement($supplier)) {
            $supplier->removeReference($this);
        }

        return $this;
    }

    public function setGlobal(bool $global): self {
        $this->global = $global;
        return $this;
    }

    public function setKind(string $kind): void {
        $this->kind = $kind;
    }
}
