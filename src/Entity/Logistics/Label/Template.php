<?php

namespace App\Entity\Logistics\Label;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'labelName' => 'partial',
        'templateFamily' => 'exact',
        'logisticReference' => 'partial',
        'productReference' => 'partial',
        'productIndice' => 'partial',
        'productDescription' => 'partial',
        'manufacturer' => 'partial',
        'customerAddressName' => 'partial',
        'shipFromAddressName' => 'partial',
        'customerDestinationPoint' => 'partial',
        'vendorNumber' => 'partial',
        'labelKind' => 'exact'
    ]),
    ApiResource(
        collectionOperations: [
            'get',
            'post'
        ],
        itemOperations: [
            'get',
            'patch',
            'delete'
        ],
        shortName: 'LabelTemplate'
    ),
    ORM\Entity(),
    ORM\Table(name: 'label_template')
]
class Template extends Entity
{
    const LABEL_FAMILY_COMPONENT = 'component';
    const LABEL_FAMILY_PRODUCT = 'product';
    const LABEL_FAMILY_CARTON = 'carton';
    const LABEL_FAMILY_PALLET = 'pallet';
    const LABEL_FAMILIES = [
        self::LABEL_FAMILY_COMPONENT,
        self::LABEL_FAMILY_PRODUCT,
        self::LABEL_FAMILY_CARTON,
        self::LABEL_FAMILY_PALLET
    ];

    const LABEL_KIND_TCONCEPT = 'TConcept';
    const LABEL_KIND_ETI9 = 'ETI9';
    const LABEL_KINDS = [
        self::LABEL_KIND_TCONCEPT,
        self::LABEL_KIND_ETI9
    ];

    #[
        ApiProperty(description: 'Identifiant du type d\'étiquette'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?string $labelName = null;

    #[
        ApiProperty(description: 'Famille de modèle d\'étiquette'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?string $templateFamily = self::LABEL_FAMILY_CARTON; // valeur par défault
    #[
        ApiProperty(description: 'Numéro de référence logistique (ref produit fournisseur)'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups([ 'read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?string $logisticReference; // Numéro de référence logistique (ref produit fournisseur) -> barcode

    #[
        ApiProperty(description: 'Numéro de référence produit (ref produit client)'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?string $productReference; // Numéro de référence produit (ref produit client) -> barcode

    #[
        ApiProperty(description: 'Indice du produit'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?string $productIndice; // Indice du produit
    #[
        ApiProperty(description: 'Nom du produit'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?string $productDescription;
    #[
        ApiProperty(description: 'Fabricant'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?string $manufacturer;
    #[
        ApiProperty(description: 'Nom du site à livrer'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups([ 'read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?string $customerAddressName; // Nom du site client à livrer
    #[
        ApiProperty(description: 'Nom du site de départ'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?string $shipFromAddressName; // Nom du site de départ
    #[
        ApiProperty(description: 'Point de destination client'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?string $customerDestinationPoint; // Point de destination client

    #[
        ApiProperty(description: 'Numéro du fournisseur dans ERP client'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?string $vendorNumber; // our supplier number in the customer's ERP system -> barcode

    #[
        ApiProperty(description: 'Type de label'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private string $labelKind = self::LABEL_KIND_TCONCEPT; // Type de label

    #[
        ApiProperty(description: 'Largeur de l\'étiquette en inch'),
        ORM\Column(type: 'float', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?float $width = 0.0;
    #[
        ApiProperty(description: 'Hauteur de l\'étiquette en inch'),
        ORM\Column(type: 'float', nullable: true),
        Serializer\Groups(['read:label-template', 'create:label-template', 'write:label-template'])
    ]
    private ?float $height = 0.0;

    /**
     * @return string|null
     */
    public function getLogisticReference(): ?string
    {
        return $this->logisticReference;
    }

    /**
     * @param string|null $logisticReference
     * @return self
     */
    public function setLogisticReference(?string $logisticReference): self
    {
        $this->logisticReference = $logisticReference;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProductReference(): ?string
    {
        return $this->productReference;
    }

    /**
     * @param string|null $productReference
     * @return self
     */
    public function setProductReference(?string $productReference): self
    {
        $this->productReference = $productReference;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProductIndice(): ?string
    {
        return $this->productIndice;
    }

    /**
     * @param string|null $productIndice
     * @return self
     */
    public function setProductIndice(?string $productIndice): self
    {
        $this->productIndice = $productIndice;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProductDescription(): ?string
    {
        return $this->productDescription;
    }

    /**
     * @param string|null $productDescription
     * @return self
     */
    public function setProductDescription(?string $productDescription): self
    {
        $this->productDescription = $productDescription;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    /**
     * @param string|null $manufacturer
     * @return self
     */
    public function setManufacturer(?string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerAddressName(): ?string
    {
        return $this->customerAddressName;
    }

    /**
     * @param string|null $customerAddressName
     * @return self
     */
    public function setCustomerAddressName(?string $customerAddressName): self
    {
        $this->customerAddressName = $customerAddressName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getShipFromAddressName(): ?string
    {
        return $this->shipFromAddressName;
    }

    /**
     * @param string|null $shipFromAddressName
     * @return self
     */
    public function setShipFromAddressName(?string $shipFromAddressName): self
    {
        $this->shipFromAddressName = $shipFromAddressName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerDestinationPoint(): ?string
    {
        return $this->customerDestinationPoint;
    }

    /**
     * @param string|null $customerDestinationPoint
     * @return self
     */
    public function setCustomerDestinationPoint(?string $customerDestinationPoint): self
    {
        $this->customerDestinationPoint = $customerDestinationPoint;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVendorNumber(): ?string
    {
        return $this->vendorNumber;
    }

    /**
     * @param string|null $vendorNumber
     * @return self
     */
    public function setVendorNumber(?string $vendorNumber): self
    {
        $this->vendorNumber = $vendorNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabelKind(): string
    {
        return $this->labelKind;
    }

    /**
     * @param string $labelKind
     * @return self
     */
    public function setLabelKind(string $labelKind): self
    {
        $this->labelKind = $labelKind;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTemplateFamily(): ?string
    {
        return $this->templateFamily;
    }

    /**
     * @param string|null $templateFamily
     * @return Template
     */
    public function setTemplateFamily(?string $templateFamily): Template
    {
        $this->templateFamily = $templateFamily;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabelName(): ?string
    {
        return $this->labelName;
    }

    /**
     * @param string|null $labelName
     * @return Template
     */
    public function setLabelName(?string $labelName): Template
    {
        $this->labelName = $labelName;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getWidth(): ?float
    {
        return $this->width;
    }

    /**
     * @param float|null $width
     * @return Template
     */
    public function setWidth(?float $width): Template
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getHeight(): ?float
    {
        return $this->height;
    }

    /**
     * @param float|null $height
     * @return Template
     */
    public function setHeight(?float $height): Template
    {
        $this->height = $height;
        return $this;
    }

}