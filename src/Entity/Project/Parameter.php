<?php

namespace App\Entity\Project;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Management\Parameter as BaseParameter;
use Doctrine\ORM\Mapping as ORM;

#[
   ORM\Entity
]
class Parameter extends BaseParameter {
    public const PRODUCT_ATTACHMENT_CATEGORIES = 'PRODUCT_ATTACHMENT_CATEGORIES';
    public const PRODUCT_EXPIRATION_DIRECTORIES = 'PRODUCT_EXPIRATION_DIRECTORIES';
    public const PRODUCT_EXPIRATION_DURATION = 'PRODUCT_EXPIRATION_DURATION'; // Durée en mois d'expiration des répertoires où sont stockés les fichiers périssables des fournisseurs.

    public const PRODUCT_IDS_TERMINAUX = 'PRODUCT_IDS_TERMINAUX'; // Ids des terminaux à prendre en compte pour les gammes de fabrication. Ne pas intégrer les joints
    public const PRODUCT_IDS_JOINTS = 'PRODUCT_IDS_IDS_JOINTS'; // Ids des terminaux à prendre en compte pour les gammes de fabrication. Ne pas intégrer les joints

}
