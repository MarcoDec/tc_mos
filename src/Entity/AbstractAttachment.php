<?php

namespace App\Entity;

use App\Controller\File\FileUploadController;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable()
 */
abstract class AbstractAttachment extends Entity
{
   /** @var string Photo*/
   public const IS_PIC = 'IS_PIC';

   public const OTHERS = 'OTHERS';

   public const API_GROUPS_URL=['attachment:read'];
   public const API_GROUPS_CATEGORY=['attachment:read','attachment:write'];
   public const API_GROUPS_EXPIRATION_DATE=['attachment:read'];

   public const API_DEFAULT_PATH = '/attachments';
   public const API_DEFAULT_UPLOAD_CONTROLLER = FileUploadController::class;
   public const API_DEFAULT_OPENAPI_CONTEXT = [
      'description' => "Créer un fichier",
      'summary' => "Créer un fichier"
   ];
   public const API_DEFAULT_DENORMALIZATION_CONTEXT = ['groups' => [self::API_GROUP_WRITE]];
   public const API_DEFAULT_NORMALIZATION_CONTEXT = ['groups'=> [self::API_GROUP_READ]];
   public const API_GROUP_WRITE = 'attachment:write';
   public const API_GROUP_READ = 'attachment:read';
   public const API_DEFAULT_COLLECTIONS_OPERATIONS = [
      'upload' => [
         'input_formats'=>[
            'multipart'=>[ 'multipart/form-data' ]
         ],
         'read' => true,
         'write' => true,
         //'output' => true,
         //'input' => true,
         'deserialize'=>false,
         'method' => 'POST',
         'path' => self::API_DEFAULT_PATH,
         'controller' => self::API_DEFAULT_UPLOAD_CONTROLLER,
         'openapi_context' => self::API_DEFAULT_OPENAPI_CONTEXT,
         'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
         'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
      ]
   ];

   public abstract function getCategory(): string;

   public abstract function setCategory(string $category): void;

   public abstract function getExpirationDate(): ?DateTimeInterface;

   public abstract function setExpirationDate(?DateTimeInterface $expirationDate): void;

   public abstract function getUrl(): string;

   public abstract function setUrl(string $url): void;

   public abstract function getFile(): ?File;

   public abstract function getFilepath(): ?string;

   public abstract function setFile(?File $file): self;

   public abstract function getExpirationDirectoriesParameter():string;
   public abstract function getExpirationDurationParameter():string;
   public abstract function getParameterClass():string;


   public function getBaseFolder():string {
      $path = explode('\\', get_class($this));
      return "/".array_pop($path)."/".$this->getId();
   }
}