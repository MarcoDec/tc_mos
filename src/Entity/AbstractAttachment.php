<?php

namespace App\Entity;

use App\Controller\File\FileController;

abstract class AbstractAttachment extends Entity
{
   /** @var string Photo*/
   public const IS_PIC = 'IS_PIC';

   public const OTHERS = 'OTHERS';

   public const API_GROUPS_URL=['attachment:read'];
   public const API_GROUPS_CATEGORY=['attachment:read','attachment:write','attachment:write'];
   public const API_GROUPS_EXPIRATION_DATE=['attachment:read'];

   public const API_DEFAULT_PATH = '/attachments';
   public const API_DEFAULT_CONTROLLER = FileController::class;
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
         'controller' => self::API_DEFAULT_CONTROLLER,
         'openapi_context' => self::API_DEFAULT_OPENAPI_CONTEXT,
         'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
         'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
      ]
   ];

}