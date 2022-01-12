<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Management\Society\Company;
use App\Entity\Purchase\Supplier\Supplier;
use App\Entity\Quality\Reception\Reference;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

trait ReferenceTrait
{
   /**
    * @var Collection<int, Company>
    */
   #[
      ApiProperty(description: 'Compagnies', readableLink: false, example: ['/api/companies/2', '/api/companies/18']),
      ORM\ManyToMany(targetEntity: Company::class, mappedBy: 'references', fetch: 'EXTRA_LAZY'),
      Serializer\Groups(['read:companies', 'write:companies'])
   ]
   private ArrayCollection $companies;
   #[
      ApiProperty(description: 'Global ?', required: true, example: true),
      ORM\Column(type: 'boolean', options: ['default' => false]),
      Serializer\Groups(['read:reference', 'write:reference'])
   ]
   private bool $global = false;
   #[
      ApiProperty(description: 'IPv4', required: true, example: '255.255.255.254'),
      ORM\Column(type: 'string', options: ['default' => Reference::KIND_QTE]),
      Assert\NotBlank,
      Serializer\Groups(['read:reference', 'write:reference'])
   ]
   private string $kind = Reference::KIND_QTE;

   /**
    * @var Collection<int, Supplier>
    */
   #[
      ApiProperty(description: 'Références', readableLink: false, example: ['/api/suppliers/2', '/api/suppliers/15']),
      ORM\ManyToMany(targetEntity: Supplier::class, mappedBy: 'references', fetch: 'EXTRA_LAZY'),
      Serializer\Groups(['read:references', 'write:references'])
   ]
   private ArrayCollection $suppliers;

   #[Pure] public function __construct() {
      $this->companies = new ArrayCollection();
      $this->suppliers = new ArrayCollection();
   }

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
    * @return Collection|Supplier[]
    */
   public function getSuppliers(): ArrayCollection {
      return $this->suppliers;
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

   public function getKind(): string {
      return $this->kind;
   }

   public function getGlobal(): ?bool {
      return $this->global;
   }

   public function isGlobal(): bool {
      return $this->global;
   }

   public function setGlobal(bool $global): self {
      $this->global = $global;
      return $this;
   }

   public function setKind(string $kind): void {
      $this->kind = $kind;
   }
}