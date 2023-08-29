<?php

namespace App\Entity\Logistics;

use App\Entity\Management\Parameter as BaseParameter;
use Doctrine\ORM\Mapping as ORM;

#[
   ORM\Entity
]
class Parameter extends BaseParameter {
    public const WAREHOUSE_ATTACHMENT_CATEGORIES = 'EMPLOYEE_ATTACHMENT_CATEGORIES';
    public const WAREHOUSE_DIRECTORIES = 'EMPLOYEE_DIRECTORIES';////Liste représentant les choix possibles des catégories de pièces jointes accepté par Employee Format de name : ATTACHMENT_CATEGORY_{nom de l'entité au format UPPERCASE} Format de value : choix séparé par une virgule
    public const WAREHOUSE_EXPIRATION_DIRECTORIES = 'EMPLOYEE_EXPIRATION_DIRECTORIES'; // Répertoires où sont stockés les fichiers périssables des employés.
    public const WAREHOUSE_EXPIRATION_DURATION = 'EMPLOYEE_EXPIRATION_DURATION'; // Durée en jour d'expiration des répertoires où sont stockés les fichiers périssables des employés.
}
