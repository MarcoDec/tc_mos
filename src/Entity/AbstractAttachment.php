<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Interfaces\FileEntity;
use App\Entity\Traits\FileTrait;
use Doctrine\ORM\Mapping as ORM;

#[
   ORM\MappedSuperclass,
   ApiResource
]
abstract class AbstractAttachment extends Entity implements FileEntity
{
   use FileTrait;
   /** @var string Photo*/
   public const IS_PIC = 'IS_PIC';
   public const OTHERS = 'OTHERS';
   #[ ORM\Column]
   private string $category = self::OTHERS;
   #[ ORM\Column(type: "date", nullable: true)]
   private \DateTimeInterface|null $expirationDate;
   #[ ORM\Column]
   private string $url='';

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
    * @return \DateTimeInterface|null
    */
   public function getExpirationDate(): ?\DateTimeInterface
   {
      return $this->expirationDate;
   }

   /**
    * @param \DateTimeInterface|null $expirationDate
    */
   public function setExpirationDate(?\DateTimeInterface $expirationDate): void
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
}