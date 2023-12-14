<?php
namespace App\Entity\Logistics\Label;

use ApiPlatform\Core\Action\PlaceholderAction;
use App\Controller\Logistics\Label\LabelPrintingController;
use App\Entity\Entity;
use App\Entity\Management\Unit;
use App\Entity\Traits\FileTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
#[
    ApiFilter(filterClass: SearchFilter::class, properties: [
        'customerAddressName' => 'partial',
        'customerDestinationPoint' => 'partial',
        'manufacturer' => 'partial',
        'productDescription' => 'partial',
        'productReference' => 'partial',
        'productIndice' => 'partial',
        'batchnumber' => 'partial',
        'quantity' => 'partial',
        'grossWeight' => 'partial',
        'netWeight' => 'partial',
        'labelNumber' => 'partial',
        'logisticReference' => 'partial',
        'shipFromAddressName' => 'partial',
        'vendorNumber' => 'partial',
        'labelKind' => 'partial',
        'operator' => 'partial'
    ]),
    ApiFilter(filterClass: OrderFilter::class, properties: [
        'customerAddressName',
        'customerDestinationPoint',
        'manufacturer',
        'productDescription',
        'productReference',
        'productIndice',
        'batchnumber',
        'quantity',
        'grossWeight',
        'netWeight',
        'labelNumber',
        'logisticReference',
        'shipFromAddressName',
        'vendorNumber',
        'labelKind',
        'operator'
    ]),
    ApiFilter(filterClass: DateFilter::class, properties: [
        'date'
    ]),
    ApiResource(
        collectionOperations: [
            'get' => [
                'method' => 'GET',
                'path' => '/label-cartons',
                'openapi_context' => [
                  'description' => 'Get all label cartons',
                  'summary' => 'Get all label cartons'
                ]
            ],
            'post' => [
                'denormalization_context' => [
                    'groups' => ['create:label-carton'],
                    'openapi_definition_name' => 'Component-create'
                ],
                'method' => 'POST',
                'path' => '/label-cartons',
                'controller' => PlaceholderAction::class
            ]
        ],
        itemOperations: [
            'get' => [
                'path' => '/label-cartons/{id}',
                'openapi_context' => [
                    'description' => 'Get a label carton by id',
                    'summary' => 'Get a label carton by id'
                ]
            ],
            'patch' => [
                'path' => '/label-cartons/{id}',
                'openapi_context' => [
                    'description' => 'Update a label carton by id',
                    'summary' => 'Update a label carton by id'
                ]
            ],
            'delete' => [
                'path' => '/label-cartons/{id}',
                'openapi_context' => [
                    'description' => 'Delete a label carton by id',
                    'summary' => 'Delete a label carton by id'
                ]
            ],
            'print' => [
                'method' => 'POST',
                'path' => '/label-cartons/{id}/print',
                'openapi_context' => [
                    'description' => 'Print a label carton by id',
                    'summary' => 'Print a label carton by id'
                ],
                'controller' => LabelPrintingController::class
            ],
        ],
        denormalizationContext: ['groups' => ['write:file', 'write:label-carton']],
        normalizationContext: ['groups' => ['read:id', 'read:file', 'read:label-carton']]
    ),
    ORM\Entity(),
    ORM\Table(name: 'label_carton'),
]
class Carton extends Entity // implements MeasuredInterface, FileEntity
{
    use FileTrait;
    #[
        ApiProperty(description: 'Numéro de référence logistique (ref produit fournisseur)'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $logisticReference; // Numéro de référence logistique (ref produit fournisseur) -> barcode

    #[
        ApiProperty(description: 'Numéro de référence produit (ref produit client)'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $productReference; // Numéro de référence produit (ref produit client) -> barcode

    #[
        ApiProperty(description: 'Indice du produit'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $productIndice; // Indice du produit
    #[
        ApiProperty(description: 'Nom du produit'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $productDescription;
    #[
        ApiProperty(description: 'Fabricant'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $manufacturer;
    #[
        ApiProperty(description: 'Nom du site à livrer'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $customerAddressName; // Nom du site client à livrer
    #[
        ApiProperty(description: 'Nom du site de départ'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $shipFromAddressName; // Nom du site de départ
    #[
        ApiProperty(description: 'Point de destination client'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $customerDestinationPoint; // Point de destination client
    #[
        ApiProperty(description: 'Numéro de lot'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $batchnumber; // Numéro de lot
    #[
        ApiProperty(description: 'Quantité'),
        ORM\Column(type: 'integer'),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private int $quantity; // Quantity of product in the box -> barcode
    #[
        ApiProperty(description: 'Poids net (avec unité)'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $netWeight; // Poids net

    #[
        ApiProperty(description: 'Poids brut (avec unité)'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $grossWeight; // Poids brut
    #[
        ApiProperty(description: 'Date de livraison (D) ou de Fabrication (P)'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $dateStr; // Format D{YYMMDD} for dispatch date P{YYMMDD} for production date
    #[
        ApiProperty(description: 'Date de livraison ou de fabrication'),
        ORM\Column(type: 'date', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private \DateTime $date;
    #[
        ApiProperty(description: 'Nombre de Cartons (=1)'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private int $numberOfBoxes = 1;
    #[
        ApiProperty(description: 'Numéro unique du Packaging'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $labelNumber; // Numéro unique du Packaging -> barcode
    #[
        ApiProperty(description: 'Numéro du fournisseur dans ERP client'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $vendorNumber; // our supplier number in the customer's ERP system -> barcode

    #[
        ApiProperty(description: 'Lien pièce jointe'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton'])
    ]
    private ?string $url = null;
    #[
        ApiProperty(description: 'Lien image'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton'])
    ]
    protected ?string $filePath = null;

    #[
        ApiProperty(description: 'ZPL'),
        ORM\Column(type: 'text', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton'])
    ]
    private ?string $zpl;

    #[
        ApiProperty(description: 'Type de label'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private string $labelKind = 'TConcept';
    #[
        ApiProperty(description: 'Opérateur'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:label-carton', 'create:label-carton', 'write:label-carton'])
    ]
    private ?string $operator = null;

    public function getFilepath(): ?string
    {
        return $this->filePath;
    }

    public function getMeasures(): array
    {
        return [];
    }

    public function getUnitMeasures(): array
    {
        return [];
    }

    public function getCurrencyMeasures(): array
    {
        return [];
    }

    public function getUnit(): ?Unit
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getLogisticReference(): ?string
    {
        return $this->logisticReference;
    }

    /**
     * @param string|null $logisticReference
     * @return Carton
     */
    public function setLogisticReference(?string $logisticReference): Carton
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
     * @return Carton
     */
    public function setProductReference(?string $productReference): Carton
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
     * @return Carton
     */
    public function setProductIndice(?string $productIndice): Carton
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
     * @return Carton
     */
    public function setProductDescription(?string $productDescription): Carton
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
     * @return Carton
     */
    public function setManufacturer(?string $manufacturer): Carton
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
     * @return Carton
     */
    public function setCustomerAddressName(?string $customerAddressName): Carton
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
     * @return Carton
     */
    public function setShipFromAddressName(?string $shipFromAddressName): Carton
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
     * @return Carton
     */
    public function setCustomerDestinationPoint(?string $customerDestinationPoint): Carton
    {
        $this->customerDestinationPoint = $customerDestinationPoint;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBatchnumber(): ?string
    {
        return $this->batchnumber;
    }

    /**
     * @param string|null $batchnumber
     * @return Carton
     */
    public function setBatchnumber(?string $batchnumber): Carton
    {
        $this->batchnumber = $batchnumber;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Carton
     */
    public function setQuantity(int $quantity): Carton
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNetWeight(): ?string
    {
        return $this->netWeight;
    }

    /**
     * @param string|null $netWeight
     * @return Carton
     */
    public function setNetWeight(?string $netWeight): Carton
    {
        $this->netWeight = $netWeight;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGrossWeight(): ?string
    {
        return $this->grossWeight;
    }

    /**
     * @param string|null $grossWeight
     * @return Carton
     */
    public function setGrossWeight(?string $grossWeight): Carton
    {
        $this->grossWeight = $grossWeight;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDateStr(): ?string
    {
        return $this->dateStr;
    }

    /**
     * @param string|null $dateStr
     * @return Carton
     */
    public function setDateStr(?string $dateStr): Carton
    {
        $this->dateStr = $dateStr;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Carton
     */
    public function setDate(\DateTime $date): Carton
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfBoxes(): int
    {
        return $this->numberOfBoxes;
    }

    /**
     * @param int $numberOfBoxes
     * @return Carton
     */
    public function setNumberOfBoxes(int $numberOfBoxes): Carton
    {
        $this->numberOfBoxes = $numberOfBoxes;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabelNumber(): ?string
    {
        return $this->labelNumber;
    }

    /**
     * @param string|null $labelNumber
     * @return Carton
     */
    public function setLabelNumber(?string $labelNumber): Carton
    {
        $this->labelNumber = $labelNumber;
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
     * @return Carton
     */
    public function setVendorNumber(?string $vendorNumber): Carton
    {
        $this->vendorNumber = $vendorNumber;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     * @return Carton
     */
    public function setUrl(?string $url): Carton
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return ?string
     */
    public function getZpl(): ?string
    {
        return $this->zpl;
    }

    /**
     * @param ?string $zpl
     * @return Carton
     */
    public function setZpl(?string $zpl): Carton
    {
        $this->zpl = $zpl;
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
     * @return Carton
     */
    public function setLabelKind(string $labelKind): Carton
    {
        $this->labelKind = $labelKind;
        return $this;
    }

    public function getImageUrl(): string {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getOperator(): ?string
    {
        return $this->operator;
    }

    /**
     * @param string|null $operator
     * @return Carton
     */
    public function setOperator(?string $operator): Carton
    {
        $this->operator = $operator;
        return $this;
    }


}