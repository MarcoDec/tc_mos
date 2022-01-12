<?php

namespace App\Entity\Accounting;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Embeddable\Accounting\Bill\CurrentPlace;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Management\Society\Company;
use App\Entity\Management\VatMessage;
use App\Entity\Selling\Customer\Contact;
use App\Entity\Selling\Customer\Customer;
use App\Entity\Traits\CompanyTrait;
use App\Entity\Traits\NotesTrait;
use App\Entity\Traits\RefTrait;
use DateTime;
use DateTimeInterface;
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
            'patch' => [
                'openapi_context' => [
                    'description' => 'Modifie une facture',
                    'summary' => 'Modifie une facture',
                ],
                'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_WRITER.'\')'
            ]
        ],
        attributes: [
            'security' => 'is_granted(\''.Roles::ROLE_ACCOUNTING_READER.'\')'
        ],
        denormalizationContext: [
            'groups' => ['write:ref', 'write:company', 'write:notes', 'write:bill', 'write:customer-contact', 'write:name'],
            'openapi_definition_name' => 'Bill-write'
        ],
        normalizationContext: [
            'groups' => ['read:id', 'read:ref', 'read:company', 'read:notes', 'read:bill', 'read:customer-contact', 'read:name'],
            'openapi_definition_name' => 'Bill-read'
        ],
    ),
    ORM\Entity
]
class Bill extends Entity {
    use CompanyTrait;
    use NotesTrait;
    use RefTrait;

    #[
        ApiProperty(description: 'Companie', required: false, example: '/api/companies/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Company::class),
        Serializer\Groups(['read:company', 'write:company'])
    ]
    protected ?Company $company;

    #[
        ApiProperty(description: 'Notes', required: false, example: 'Lorem ipsum dolores'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:notes', 'write:notes'])
    ]
    protected ?string $notes = null;

    #[
        ApiProperty(description: 'Référence', example: 'DJH545'),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:ref', 'write:ref'])
    ]
    protected ?string $ref = null;

    #[
        ApiProperty(description: 'Date de facturation', required: false, example: '2022-24-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private DateTimeInterface $billingDate;

    #[
        ApiProperty(description: 'Contact', required: true, readableLink: false, example: '/api/customer-contacts/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Contact::class),
        Serializer\Groups(['read:customer-contact', 'write:customer-contact'])
    ]
    private ?Contact $contact = null;

    #[
        ApiProperty(description: 'Statut', required: true, example: 'partially_paid'),
        ORM\Embedded(CurrentPlace::class),
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private CurrentPlace $currentPlace;

    #[
        ApiProperty(description: 'Client', required: false, readableLink: false, example: '/api/customers/1'),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: Customer::class),
        Serializer\Groups(['read:customer', 'write:customer'])
    ]
    private ?Customer $customer = null;

    #[
        ApiProperty(description: 'Date de facturation', required: false, example: '2022-27-03'),
        Assert\Date,
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private ?DateTimeInterface $dueDate = null;

    #[
        ApiProperty(description: 'Prix HT', required: true, example: 0),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private float $exclTax = 0;

    #[
        ApiProperty(description: 'Forcer la TVA', required: true, example: 0),
        ORM\Column(options: ['default' => VatMessage::FORCE_DEFAULT, 'unsigned' => true], type: 'smallint'),
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private int $forceVat = VatMessage::FORCE_DEFAULT;

    #[
        ApiProperty(description: 'Prix TTC', required: true, example: 0),
        ORM\Column(type: 'float', options: ['default' => 0, 'unsigned' => true]),
        Assert\PositiveOrZero,
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private float $inclTax = 0;

    #[
        ApiProperty(description: 'TVA', required: false, example: 10),
        ORM\Column(nullable: true),
        Serializer\Groups(['read:bill', 'write:bill'])
    ]
    private float $vat = 0;

    #[
        ApiProperty(description: 'Message TVA', required: false),
        ORM\ManyToOne(fetch: 'EAGER', targetEntity: VatMessage::class),
        Serializer\Groups(['read:name', 'write:name'])
    ]
    private ?VatMessage $vatMessage;

    public function __construct() {
        $this->billingDate = new DateTime();
    }

    final public function getBillingDate(): DateTimeInterface {
        return $this->billingDate;
    }

    final public function getContact(): ?Contact {
        return $this->contact;
    }

    public function getCurrentPlace(): CurrentPlace {
        return $this->currentPlace;
    }

    final public function getCustomer(): ?Customer {
        return $this->customer;
    }

    final public function getDueDate(): ?DateTimeInterface {
        return $this->dueDate;
    }

    final public function getExclTax(): float {
        return $this->exclTax;
    }

    final public function getForceVat(): int {
        return $this->forceVat;
    }

    final public function getInclTax(): float {
        return $this->inclTax;
    }

    final public function getVat(): float {
        return $this->vat;
    }

    final public function getVatMessage(): ?VatMessage {
        return $this->vatMessage;
    }

    final public function setBillingDate(DateTimeInterface $billingDate): self {
        $this->billingDate = $billingDate;
        return $this;
    }

    final public function setContact(?Contact $contact): self {
        $this->contact = $contact;
        return $this;
    }

    public function setCurrentPlace(CurrentPlace $currentPlace): void {
        $this->currentPlace = $currentPlace;
    }

    final public function setCustomer(?Customer $customer): self {
        $this->customer = $customer;
        return $this;
    }

    final public function setDueDate(?DateTimeInterface $dueDate): self {
        $this->dueDate = $dueDate;
        return $this;
    }

    final public function setExclTax(float $exclTax): self {
        $this->exclTax = $exclTax;
        return $this;
    }

    final public function setForceVat(int $forceVat): self {
        $this->forceVat = $forceVat;
        return $this;
    }

    final public function setInclTax(float $inclTax): self {
        $this->inclTax = $inclTax;
        return $this;
    }

    final public function setVat(float $vat): self {
        $this->vat = $vat;
        return $this;
    }

    final public function setVatMessage(?VatMessage $vatMessage): self {
        $this->vatMessage = $vatMessage;
        return $this;
    }
}
