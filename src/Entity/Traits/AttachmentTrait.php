<?php

namespace App\Entity\Traits;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\AbstractAttachment;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait AttachmentTrait
{
   #[
      ORM\Column,
      ApiProperty( description: 'Catégorie de fichier', example: 'PIC' ),
      Groups(AbstractAttachment::API_GROUPS_CATEGORY)
   ]
   private string $category = AbstractAttachment::OTHERS;
   #[
      ORM\Column(type: "date", nullable: true),
      ApiProperty( description: "Date d'expiration du fichier", example: '12/12/2023' ),
      Groups(AbstractAttachment::API_GROUPS_EXPIRATION_DATE)
   ]
   private DateTimeInterface|null $expirationDate;
   #[
      ORM\Column,
      ApiProperty( description: "Url d'accès au fichier", example: '/employees/cv.docx' ),
      Groups(AbstractAttachment::API_GROUPS_URL)
   ]
   private string $url='';
   /**
    * @var File|null
    * @Vich\UploadableField(mapping="attachment", fileNameProperty="filePath")
    */
   #[
      ApiProperty( description: "Fichier à uploader", example: '12/12/2023' ),
      Groups(['attachment:write'])
   ]
   private ?File $file = null;

   #[
      ApiProperty( description: "Répertoire cible de stockage du fichier", example: "/contrats"),
      Groups(['attachment:write'])
      ]
   private string $targetFolder ='';

   /**
    * @return string
    */
   public function getCategory(): string
   {
      return $this->category;
   }

   /**
    * @param string $category
    */
   public function setCategory(string $category): void
   {
      $this->category = $category;
   }

   /**
    * @return DateTimeInterface|null
    */
   public function getExpirationDate(): ?DateTimeInterface
   {
      return $this->expirationDate;
   }

   /**
    * @param DateTimeInterface|null $expirationDate
    */
   public function setExpirationDate(?DateTimeInterface $expirationDate): void
   {
      $this->expirationDate = $expirationDate;
   }


   /**
    * @return string
    */
   public function getUrl(): string
   {
      return $this->url;
   }

   /**
    * @param string $url
    */
   public function setUrl(string $url): void
   {
      $this->url = $url;
   }

   public function getFile(): ?File {
      return $this->file;
   }

   function getFilepath(): ?string {
      return $this->file?->getPathname();
   }

   public function setFile(?File $file): self {
      $this->file = $file;
      return $this;
   }

   /**
    * @return string
    */
   public function getTargetFolder(): string
   {
      return $this->targetFolder;
   }

   /**
    * @param string $targetFolder
    */
   public function setTargetFolder(string $targetFolder): void
   {
      $this->targetFolder = $targetFolder;
   }


}