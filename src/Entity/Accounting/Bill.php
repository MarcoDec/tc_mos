<?php

namespace App\Entity\Accounting;

use ApiPlatform\Core\Action\PlaceholderAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Doctrine\DBAL\Types\Management\VatMessageForce;
use App\Entity\Accounting\Item as AccountingItem;
use App\Entity\Embeddable\Accounting\State;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Embeddable\Measure;
use App\Entity\Entity;
use App\Entity\Management\Society\Company\Company;
use App\Entity\Management\VatMessage;
use App\Entity\Selling\Customer\Contact;
use App\Entity\Selling\Customer\Customer;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Facture',
        collectionOperations: [
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère les factures',
                    'summary' => 'Récupère les factures',
                ]
            ],
            'post' => [
                'openapi_context' => [
                    'description' => 'Créer une facture',
                    'summary' => 'Créer une facture',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')'
            ]
        ],
        itemOperations: [
            'delete' => [
                'openapi_context' => [
                    'description' => 'Supprime une facture',
                    'summary' => 'Supprime une facture',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_ADMIN.'\')'
            ],
            'get' => [
                'openapi_context' => [
                    'description' => 'Récupère une facture',
                    'summary' => 'Récupère une facture',
                ]
            ],
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une facture',
                    'summary' => 'Modifie une facture',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')'
            ],
            'promote' => [
                'controller' => PlaceholderAction::class,
                'deserialize' => false,
                'method' => 'PATCH',
                'openapi_context' => [
                    'description' => 'Transite la facture à son prochain statut de workflow',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'transition',
                            'required' => true,
                            'schema' => ['enum' => [...State::TRANSITIONS, ...Blocker::TRANSITIONS], 'type' => 'string']
                        ],
                        [
                            'in' => 'path',
                            'name' => 'workflow',
                            'required' => true,
                            'schema' => ['enum' => ['bill', 'blocker'], 'type' => 'string']
                        ]
                    ],
                    'requestBody' => null,
                    'summary' => 'Transite la facture à son prochain statut de workflow'
                ],
                'path' => '/bills/{id}/promote/{workflow}/to/{transition}',
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')',
                'validate' => false
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:bill', 'write:measure'],
            'openapi_definition_name' => 'Bill-write'
        ],
        normalizationContext: [
            'groups' => ['read:bill', 'read:id', 'read:measure', 'read:state'],
            'openapi_definition_name' => 'Bill-read',
            'skip_null_values' => false
        ],
    ),
    ORM\Entity
]
class Bill extends Entity {
    #[
        ORM\Embedded,
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    public Blocker $embBlocker;

    #[
        ORM\OneToMany(targetEntity: AccountingItem::class, mappedBy: 'bill'),
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private Collection $bill_items;

    #[
        ApiProperty(description: 'Date de facturation', example: '2022-03-24'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private ?DateTimeImmutable $billingDate;

    #[
        ApiProperty(description: 'Compagnie', example: '/api/companies/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private ?Company $company;

    #[
        ApiProperty(description: 'Contact', readableLink: false, example: '/api/customer-contacts/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private ?Contact $contact = null;

    #[
        ApiProperty(description: 'Client', readableLink: false, example: '/api/customers/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private ?Customer $customer = null;

    #[
        ApiProperty(description: 'Date de facturation', example: '2022-03-27'),
        ORM\Column(type: 'date_immutable', nullable: true),
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private ?DateTimeImmutable $dueDate = null;

    #[
        ORM\Embedded,
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private State $embState;

    #[
        ApiProperty(description: 'Prix HT', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private Measure $exclTax;

    #[
        ApiProperty(description: 'Forcer la TVA', required: true, example: VatMessageForce::TYPE_FORCE_DEFAULT, openapiContext: ['enum' => VatMessageForce::TYPES]),
        Assert\Choice(choices: VatMessageForce::TYPES),
        ORM\Column(type: 'vat_message_force', options: ['default' => VatMessageForce::TYPE_FORCE_DEFAULT]),
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private string $forceVat = VatMessageForce::TYPE_FORCE_DEFAULT;

    #[
        ApiProperty(description: 'Prix TTC', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private Measure $inclTax;

    #[
        ApiProperty(description: 'Notes', example: 'Lorem ipsum dolores'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private ?string $notes = null;

    #[
        ApiProperty(description: 'Référence', example: 'DJH545'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:bill'])
    ]
    private ?string $ref = null;

    #[
        ApiProperty(description: 'TVA', openapiContext: ['$ref' => '#/components/schemas/Measure-price']),
        ORM\Embedded,
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private Measure $vat;

    #[
        ApiProperty(description: 'Message TVA', readableLink: false, example: '/api/vat-messages/1'),
        ORM\ManyToOne,
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private ?VatMessage $vatMessage = null;

    public function __construct() {
        $this->billingDate = new DateTimeImmutable();
        $this->embBlocker = new Blocker();
        $this->embState = new State();
        $this->exclTax = new Measure();
        $this->inclTax = new Measure();
        $this->vat = new Measure();
        $this->bill_items = new ArrayCollection();
    }

    final public function getBillingDate(): ?DateTimeImmutable {
        return $this->billingDate;
    }

    public function getBillItems(): Collection {
        return $this->bill_items;
    }

    final public function getBlocker(): string {
        return $this->embBlocker->getState();
    }

    final public function getCompany(): ?Company {
        return $this->company;
    }

    final public function getContact(): ?Contact {
        return $this->contact;
    }

    final public function getCustomer(): ?Customer {
        return $this->customer;
    }

    final public function getDueDate(): ?DateTimeImmutable {
        return $this->dueDate;
    }

    final public function getEmbBlocker(): Blocker {
        return $this->embBlocker;
    }

    final public function getEmbState(): State {
        return $this->embState;
    }

    final public function getExclTax(): Measure {
        return $this->exclTax;
    }

    final public function getForceVat(): string {
        return $this->forceVat;
    }

    final public function getInclTax(): Measure {
        return $this->inclTax;
    }

    final public function getNotes(): ?string {
        return $this->notes;
    }

    final public function getRef(): ?string {
        return $this->ref;
    }

    final public function getState(): string {
        return $this->embState->getState();
    }

    final public function getVat(): Measure {
        return $this->vat;
    }

    final public function getVatMessage(): ?VatMessage {
        return $this->vatMessage;
    }

    final public function setBillingDate(?DateTimeImmutable $billingDate): self {
        $this->billingDate = $billingDate;
        return $this;
    }

    final public function setBillItems(Collection $billItems): self {
        $this->bill_items = $billItems;

        foreach ($billItems as $billItem) {
            $billItem->setBill($this);
        }

        return $this;
    }

    final public function setBlocker(string $state): self {
        $this->embBlocker->setState($state);
        return $this;
    }

    final public function setCompany(?Company $company): self {
        $this->company = $company;
        return $this;
    }

    final public function setContact(?Contact $contact): self {
        $this->contact = $contact;
        return $this;
    }

    final public function setCustomer(?Customer $customer): self {
        $this->customer = $customer;
        return $this;
    }

    final public function setDueDate(?DateTimeImmutable $dueDate): self {
        $this->dueDate = $dueDate;
        return $this;
    }

    final public function setEmbBlocker(Blocker $embBlocker): self {
        $this->embBlocker = $embBlocker;
        return $this;
    }

    final public function setEmbState(State $embState): self {
        $this->embState = $embState;
        return $this;
    }

    final public function setExclTax(Measure $exclTax): self {
        $this->exclTax = $exclTax;
        return $this;
    }

    final public function setForceVat(string $forceVat): self {
        $this->forceVat = $forceVat;
        return $this;
    }

    final public function setInclTax(Measure $inclTax): self {
        $this->inclTax = $inclTax;
        return $this;
    }

    final public function setNotes(?string $notes): self {
        $this->notes = $notes;
        return $this;
    }

    final public function setRef(?string $ref): self {
        $this->ref = $ref;
        return $this;
    }

    final public function setState(string $state): self {
        $this->embState->setState($state);
        return $this;
    }

    final public function setVat(Measure $vat): self {
        $this->vat = $vat;
        return $this;
    }

    final public function setVatMessage(?VatMessage $vatMessage): self {
        $this->vatMessage = $vatMessage;
        return $this;
    }
}
