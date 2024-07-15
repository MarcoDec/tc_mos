<?php

namespace App\Entity\Purchase\Supplier;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Management\Unit;
use App\Entity\Purchase\Component\Component as TechnicalSheet;
use App\Entity\Traits\Price\MainPriceTrait;
use App\Filter\RelationFilter;
use App\Repository\Purchase\Supplier\ComponentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use App\Entity\Purchase\Supplier\Price\ComponentPrice as SupplierComponentPrice;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;

#[
    ApiFilter(filterClass: RelationFilter::class, properties: ['component', 'supplier']),
    ApiResource(
        description: 'Composant',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les composants',
                    'summary' => 'Récupère les composants'
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer un composant',
                    'summary' => 'Créer un composant'
                ],
                'security' => 'is_granted(\'' . Roles::ROLE_PURCHASE_WRITER . '\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime un composant',
                    'summary' => 'Supprime un composant'
                ]
            ],
            'get' => NO_ITEM_GET_OPERATION,
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifier un composant',
                    'summary' => 'Modifier un composant',
                ]
            ],
        ],
        shortName: 'SupplierComponent',
        attributes: [
            'security' => 'is_granted(\'' . Roles::ROLE_PURCHASE_READER . '\')'
        ],
        denormalizationContext: [
            'groups' => ['write:measure', 'write:supplier-component', 'write:main-price'],
            'openapi_definition_name' => 'SupplierComponent-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:measure', 'read:supplier-component', 'read:main-price'],
            'openapi_definition_name' => 'SupplierComponent-read',
            'skip_null_values' => false
        ],
        paginationClientEnabled: true
    ),
    ORM\Entity(repositoryClass: ComponentRepository::class),
    ORM\Table(name: 'supplier_component')
]
class Component extends Entity
{
    use MainPriceTrait;
    #[
        ApiProperty(description: 'Composant', readableLink: false, example: '/api/components/1'),
        ORM\ManyToOne(targetEntity: TechnicalSheet::class, inversedBy: 'supplierComponents'),
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private ?TechnicalSheet $component = null;

    /** @var DoctrineCollection<int, SupplierComponentPrice> */
    #[
        ApiProperty(description: 'Prix', example: '[]'),
        ORM\OneToMany(mappedBy: 'component', targetEntity: SupplierComponentPrice::class, fetch: 'EAGER'),
        Serializer\Groups(['read:supplier-component'])
    ]
    private DoctrineCollection $prices;

    #[
        ApiProperty(description: 'Fournisseur', readableLink: false, example: '/api/suppliers/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:supplier-component', 'write:supplier-component'])
    ]
    private ?Supplier $supplier = null;


    public function __construct()
    {
        $this->initialize();
        $this->prices = new ArrayCollection();
    }

    #[
        ApiProperty(description: 'Meilleur prix'),
        Serializer\Groups(['read:id', 'read:supplier-component'])
    ]
    final public function getBestPrice(): Measure
    {
        $bestPrice = new Measure();
        //On récupère tous les prix
        $prices = $this->getPrices();
        //dump(['prices'=>$prices]);
        if (count($prices) > 0) {
            /** @var SupplierComponentPrice $supplierComponentPrice */
            $filteredPrices = $prices
                ->filter(function ($supplierComponentPrice) { // On retire tous les enregistrements qui ont une quantité à zéro ou un prix à zéro
                    $quantity = $supplierComponentPrice->getQuantity()->getValue();
                    $price = $supplierComponentPrice->getPrice()->getValue();
                    return $price > 0;
                })->toArray();
            usort($filteredPrices, function ($a, $b) {
                return $b->getPrice()->getValue() < $a->getPrice()->getValue();
            });
            $bestPrice = $filteredPrices[0]->getPrice();
        }
        return $bestPrice;
    }

    final public function getComponent(): ?TechnicalSheet
    {
        return $this->component;
    }

    public function getPrices(): ArrayCollection|DoctrineCollection
    {
        return $this->prices->filter(function ($price) {
            return $price->isDeleted() === false;
        });
    }
    final public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    final public function getUnit(): ?Unit
    {
        return $this->component?->getUnit();
    }

    final public function setComponent(?TechnicalSheet $component): self
    {
        $this->component = $component;
        return $this;
    }
    final public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;
        return $this;
    }
}