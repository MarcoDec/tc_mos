<?php

namespace App\Entity\Management\Society;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Entity;
use App\Entity\Selling\Customer\Customer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiResource(
        description: 'Compagnie',
        collectionOperations: [
            'get-options' => [
                'controller' => PlaceholderAction::class,
                'filters' => [],
                'method' => 'GET',
                'normalization_context' => [
                    'groups' => ['read:id', 'read:company:option'],
                    'openapi_definition_name' => 'Company-options',
                    'skip_null_values' => false
                ],
                'openapi_context' => [
                    'description' => 'Récupère les compagnies pour les select',
                    'summary' => 'Récupère les compagnies pour les select',
                ],
                'order' => ['name' => 'asc'],
                'pagination_enabled' => false,
                'path' => '/companies/options'
            ]
        ],
        itemOperations: ['get' => NO_ITEM_GET_OPERATION]
    ),
    ORM\Entity
]
class Company extends Entity {
    /** @var Collection<int, Customer> */
    #[ORM\ManyToMany(targetEntity: Customer::class, mappedBy: 'administeredBy')]
    private Collection $customers;

    #[ORM\Column(nullable: true)]
    private ?string $name = null;

    #[ORM\ManyToOne]
    private ?Society $society = null;

    public function __construct() {
        $this->customers = new ArrayCollection();
    }

    final public function addCustomer(Customer $customer): self {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            $customer->addAdministeredBy($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Customer>
     */
    final public function getCustomers(): Collection {
        return $this->customers;
    }

    final public function getName(): ?string {
        return $this->name;
    }

    final public function getSociety(): ?Society {
        return $this->society;
    }

    #[Serializer\Groups(['read:company:option'])]
    final public function getText(): ?string {
        return $this->getName();
    }

    final public function removeCustomer(Customer $customer): self {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
            $customer->removeAdministeredBy($this);
        }
        return $this;
    }

    final public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    final public function setSociety(?Society $society): self {
        $this->society = $society;
        return $this;
    }
}
