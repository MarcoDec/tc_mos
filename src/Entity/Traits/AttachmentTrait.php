<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\AbstractAttachment;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;

trait AttachmentTrait {
    #[
      ORM\Column,
      ApiProperty(description: 'Catégorie de fichier', required: true, example: 'doc'),
      Groups(AbstractAttachment::API_GROUPS_CATEGORY)
   ]
   private string $category = 'doc';

    #[
      ORM\Column(type: 'date', nullable: true),
      ApiProperty(description: 'Date d\'expiration du fichier', example: '12/12/2023'),
      Groups(AbstractAttachment::API_GROUPS_EXPIRATION_DATE)
   ]
   private DateTimeInterface|null $expirationDate = null;

    /**
     * @var File|null
     */
    #[
      ApiProperty(description: 'Fichier à uploader', required: true, example: '12/12/2023'),
      Groups(['attachment:write'])
   ]
   private ?File $file = null;

    #[
      ORM\Column,
      ApiProperty(description: 'Url d\'accès au fichier', example: 'http://localhost:8000/uploads/Employee/22/contrat/20211105.docx'),
      Groups(AbstractAttachment::API_GROUPS_URL)
   ]
   private string $url = '';

    public function getCategory(): string {
        return $this->category;
    }

    public function getExpirationDate(): ?DateTimeInterface {
        return $this->expirationDate;
    }

    public function getFile(): ?File {
        return $this->file;
    }

    public function getFilepath(): ?string {
        return $this->file?->getPathname();
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function setCategory(string $category): void {
        $this->category = $category;
    }

    public function setExpirationDate(?DateTimeInterface $expirationDate): void {
        $this->expirationDate = $expirationDate;
    }

    public function setFile(?File $file): self {
        $this->file = $file;
        return $this;
    }

    public function setUrl(string $url): void {
        $this->url = $url;
    }
}
