<?php

namespace App\Entity;

use App\Controller\File\FileUploadController;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractAttachment extends Entity {
    public const API_DEFAULT_COLLECTIONS_OPERATIONS = [
        'upload' => [
            'input_formats' => [
                'multipart' => ['multipart/form-data']
            ],
            'read' => true,
            'write' => true,
            'deserialize' => false, //OK
            'method' => 'POST',
            'path' => self::API_DEFAULT_PATH,
            'controller' => self::API_DEFAULT_UPLOAD_CONTROLLER,
            'openapi_context' => self::API_DEFAULT_OPENAPI_CONTEXT,
            'denormalization_context' => self::API_DEFAULT_DENORMALIZATION_CONTEXT,
            'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
        ]
    ];
    public const API_DEFAULT_DENORMALIZATION_CONTEXT = ['groups' => [self::API_GROUP_WRITE]];
    public const API_DEFAULT_ITEM_OPERATIONS = [
        'get' => [
            'openapi_context' => [
                   'description' => 'Récupère un fichier',
                   'summary' => 'Récupère un fichier'
               ],
            'normalization_context' => self::API_DEFAULT_NORMALIZATION_CONTEXT
        ],
        'delete' => [
            'openapi_context' => [
                   'description' => 'Supprime un fichier',
                   'summary' => 'Supprime un fichier'
               ]
        ]
    ];
    public const API_DEFAULT_NORMALIZATION_CONTEXT = ['groups' => [self::API_GROUP_READ]];
    public const API_DEFAULT_OPENAPI_CONTEXT = [
        'description' => 'Créer un fichier',
        'summary' => 'Créer un fichier'
    ];
    public const API_DEFAULT_PATH = '/attachments';
    public const API_DEFAULT_UPLOAD_CONTROLLER = FileUploadController::class;
    public const API_GROUP_READ = 'attachment:read';
    public const API_GROUP_WRITE = 'attachment:write';
    public const API_GROUPS_CATEGORY = ['attachment:read', 'attachment:write'];
    public const API_GROUPS_EXPIRATION_DATE = ['attachment:read'];
    public const API_GROUPS_URL = ['attachment:read'];

    /** @var string Photo */
    public const IS_PIC = 'IS_PIC';

    public const OTHERS = 'OTHERS';

    public bool $hasParameter;

    public function __construct()
    {
       $this->hasParameter = true;
    }

   abstract public function getCategory(): string;

    abstract public function getExpirationDate(): ?DateTimeInterface;

    abstract public function getExpirationDirectoriesParameter(): string;

    abstract public function getExpirationDurationParameter(): string;
    abstract public function getExpirationDateStr(): string;

    abstract public function getFile(): ?File;

    abstract public function getFilepath(): ?string;

    abstract public function getParameterClass(): string;

    abstract public function getUrl(): string;

    abstract public function setCategory(string $category): void;

    abstract public function setExpirationDate(?DateTimeInterface $expirationDate): void;

    abstract public function setFile(?File $file): self;

    abstract public function setUrl(string $url): void;

    public function getBaseFolder(): string {
        $path = explode('\\', static::class);
        return '/'.array_pop($path).'/'.$this->getId();
    }
}
