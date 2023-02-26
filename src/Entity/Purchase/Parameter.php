<?php

namespace App\Entity\Purchase;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Management\Parameter as BaseParameter;
use Doctrine\ORM\Mapping as ORM;

#[
   ORM\Entity,
   ApiResource
]
class Parameter extends BaseParameter {
    public const SUPPLIER_ATTACHMENT_CATEGORIES = 'SUPPLIER_ATTACHMENT_CATEGORIES'; //Liste représentant les choix possibles des catégories de pièces jointes accepté par Supplier
    public const SUPPLIER_EXPIRATION_DIRECTORIES = 'SUPPLIER_EXPIRATION_DIRECTORIES'; // Répertoires où sont stockés les fichiers périssables des fournisseurs.
    public const SUPPLIER_EXPIRATION_DURATION = 'SUPPLIER_EXPIRATION_DURATION'; // Durée en mois d'expiration des répertoires où sont stockés les fichiers périssables des fournisseurs.
}
