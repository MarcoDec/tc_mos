<?php

namespace App\Entity\Selling;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Management\Parameter as BaseParameter;
use Doctrine\ORM\Mapping as ORM;

#[
   ORM\Entity
]
class Parameter extends BaseParameter {
    public const CUSTOMER_ATTACHMENT_CATEGORIES = 'CUSTOMER_ATTACHMENT_CATEGORIES';
    public const CUSTOMER_EXPIRATION_DIRECTORIES = 'CUSTOMER_EXPIRATION_DIRECTORIES';
    public const CUSTOMER_EXPIRATION_DURATION = 'CUSTOMER_EXPIRATION_DURATION';
}
