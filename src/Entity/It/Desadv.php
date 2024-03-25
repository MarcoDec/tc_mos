<?php

namespace App\Entity\It;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Entity;
use App\Entity\Interfaces\FileEntity;
use App\Entity\Traits\FileTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[
    ApiResource(
        description: 'Desadv',
        collectionOperations: ['get', 'post'],
        itemOperations: ['get', 'patch', 'delete']
    ),
    ORM\Entity
    ]
class Desadv extends Entity implements FileEntity
{
    use FileTrait;

    #[
        ApiProperty(description: 'Date de réception du message', example: '2023-08-31'),
        ORM\Column(type: 'datetime')
    ]
    private DateTime $messageDate;
    #[
        ApiProperty(description: 'Référence du message', example: '123456789'),
        ORM\Column(type: 'string', length: 255)
    ]
    private string $ref;
    #[
        ApiProperty(description: 'JSON du message', example: '{"key": "value"}'),
        ORM\Column(type: 'text')
    ]
    private string $json;
    #[
        ApiProperty(description: 'Mode EDI', example: 'test'),
        ORM\Column(name: 'edi_mode', type: 'string', length: 255)
    ]
    private string $ediMode;
    #[
        ApiProperty(description: 'ID GP|AN du fournisseur', example: '1'),
        ORM\Column(name: 'supplier_id', type: 'integer')
    ]
    private ?int $supplierOldId;

    /**
     * @return DateTime
     */
    public function getMessageDate(): DateTime
    {
        return $this->messageDate;
    }

    /**
     * @param DateTime $messageDate
     * @return Desadv
     */
    public function setMessageDate(DateTime $messageDate): Desadv
    {
        $this->messageDate = $messageDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getRef(): string
    {
        return $this->ref;
    }

    /**
     * @param string $ref
     * @return Desadv
     */
    public function setRef(string $ref): Desadv
    {
        $this->ref = $ref;
        return $this;
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }

    /**
     * @param string $json
     * @return Desadv
     */
    public function setJson(string $json): Desadv
    {
        $this->json = $json;
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
     * @return Desadv
     */
    public function setEdiMode(string $ediMode): Desadv
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
     * @return Desadv
     */
    public function setSupplierOldId(?int $supplierOldId): Desadv
    {
        $this->supplierOldId = $supplierOldId;
        return $this;
    }


}