<?php

namespace App\Entity\It;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\It\Edi\EdiPostController;
use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Entity;
use App\Entity\Hr\Employee\Employee;
use App\Entity\Interfaces\FileEntity;
use App\Entity\Traits\FileTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use App\Entity\It\EdiTypes as EdiTypes;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        description: 'Edi',
        collectionOperations: [
            'get',
            'post' => [
                'controller' => EdiPostController::class,
                'denormalization_context' => [
                    'groups' => ['write:edi'],
                    'openapi_definition_name' => 'Edi-write'
                ],
                'method' => 'POST',
                'openapi_context' => [
                    'description' => 'Générer un nouveau message EDI',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'ediType',
                            'required' => true,
                            'schema' => [
                                'enum' => EdiTypes::ORDERS,
                                'type' => 'string'
                            ]
                        ]
                    ],
                    'summary' => 'Crée et envoi un nouveau message EDI'
                ],
                'path' => '/edi/{ediType}',
                'read' => false,
                'write' => true,
                'security' => 'is_granted(\''.Roles::ROLE_IT_WRITER.'\')'
            ]
        ],
        itemOperations: ['get', 'patch', 'delete'],
    ),
    ORM\Entity
    ]
class Edi extends Entity implements FileEntity
{
    use FileTrait;
    #[
        ApiProperty(description: 'Date de réception JSON et génération fichier', example: '2023-08-31'),
        ORM\Column(type: 'datetime'),
        Serializer\Groups(['read:edi'])
    ]
    private DateTime $date;
    #[
        ApiProperty(description: 'JSON reçu en entrée', example: '{}'),
        ORM\Column(type: 'text'),
        Serializer\Groups(['read:edi', 'write:edi'])
    ]
    private string $inputJson;
    #[
        ApiProperty(description: 'Référence reçue en entrée', example: '123456'),
        ORM\Column(type: 'text'),
        Serializer\Groups(['read:edi', 'write:edi'])
    ]
    private string $inputRef;
    #[
        ApiProperty(description: 'compte utilisé pour la génération de l\'edi', example: '/api/employees/5'),
        ORM\ManyToOne(targetEntity: Employee::class),
        Serializer\Groups(['read:edi'])
    ]
    private Employee $loginAccount;
    #[
        ApiProperty(description: 'Lien fichier JSON'),
        ORM\Column(type: 'string', nullable: true),
        Serializer\Groups(['read:file', 'read:edi'])
    ]
    private ?string $filePath;
    #[
        ApiProperty(description: 'mode de génération edi', example: EdiTypes::EDI_MODE_TEST, openapiContext: ['enum' => EdiTypes::MODES]),
        Assert\Choice(choices: EdiTypes::MODES),
        ORM\Column(type: 'string'),
        Serializer\Groups(['edi:read', 'write:edi'])
    ]
    private string $ediMode;
    #[
        ApiProperty(description: 'type de message', example: EdiTypes::EDI_ORDER, openapiContext: ['enum' => EdiTypes::ORDERS]),
        Assert\Choice(choices: EdiTypes::ORDERS),
        ORM\Column(type: 'string'),
        Serializer\Groups(['edi:read'])
    ]
    private string $ediType;

    #[
        ApiProperty(description: 'ID GP|AN du fournisseur', example: '1'),
        ORM\Column(type: 'integer', nullable: true),
        Serializer\Groups(['read:edi', 'write:edi'])
    ]
    private ?int $supplierOldId;

    #[
        ApiProperty(description: 'ID GP|AN de la commande fournisseur', example: '1'),
        ORM\Column(type: 'integer', nullable: true),
        Serializer\Groups(['read:edi', 'write:edi'])
    ]
    private ?int $orderSupplierOldId;

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }
    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return Edi
     */
    public function setDate(DateTime $date): Edi
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getInputJson(): string
    {
        return $this->inputJson;
    }

    /**
     * @param string $inputJson
     * @return Edi
     */
    public function setInputJson(string $inputJson): Edi
    {
        $this->inputJson = $inputJson;
        return $this;
    }

    /**
     * @return Employee
     */
    public function getLoginAccount(): Employee
    {
        return $this->loginAccount;
    }

    /**
     * @param Employee $loginAccount
     * @return Edi
     */
    public function setLoginAccount(Employee $loginAccount): Edi
    {
        $this->loginAccount = $loginAccount;
        return $this;
    }

    /**
     * @return string
     */
    public function getEdiType(): string
    {
        return $this->ediType;
    }

    /**
     * @param string $ediType
     * @return Edi
     */
    public function setEdiType(string $ediType): Edi
    {
        $this->ediType = $ediType;
        return $this;
    }

    /**
     * @return string
     */
    public function getInputRef(): string
    {
        return $this->inputRef;
    }

    /**
     * @param string $inputRef
     * @return Edi
     */
    public function setInputRef(string $inputRef): Edi
    {
        $this->inputRef = $inputRef;
        return $this;
    }

    /**
     * @return string
     */
    public function getEdiMode(): string
    {
        return $this->ediMode;
    }

    /**
     * @param string $ediMode
     * @return Edi
     */
    public function setEdiMode(string $ediMode): Edi
    {
        $this->ediMode = $ediMode;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSupplierOldId(): ?int
    {
        return $this->supplierOldId;
    }

    /**
     * @param int|null $supplierOldId
     * @return Edi
     */
    public function setSupplierOldId(?int $supplierOldId): Edi
    {
        $this->supplierOldId = $supplierOldId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrderSupplierOldId(): ?int
    {
        return $this->orderSupplierOldId;
    }

    /**
     * @param int|null $orderSupplierOldId
     * @return Edi
     */
    public function setOrderSupplierOldId(?int $orderSupplierOldId): Edi
    {
        $this->orderSupplierOldId = $orderSupplierOldId;
        return $this;
    }

}
