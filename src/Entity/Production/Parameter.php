<?php

namespace App\Entity\Production;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Management\Parameter as BaseParameter;
use Doctrine\ORM\Mapping as ORM;

#[
   ORM\Entity
]
class Parameter extends BaseParameter {
    public const ENGINE_ATTACHMENT_CATEGORIES = 'ENGINE_ATTACHMENT_CATEGORIES'; //Liste représentant les choix possibles des catégories de pièces jointes accepté par Supplier

    public const ENGINE_EXPIRATION_DIRECTORIES = 'ENGINE_EXPIRATION_DIRECTORIES'; // Répertoires où sont stockés les fichiers périssables des fournisseurs.
    public const ENGINE_EXPIRATION_DURATION = 'ENGINE_EXPIRATION_DURATION'; // Durée en mois d'expiration des répertoires où sont stockés les fichiers périssables des fournisseurs.
    public const ENGINE_CHECK_MAINTENANCE = 'ENGINE_CHECK_MAINTENANCE'; //Active ou non la vérification des maintenances lors de démarrage de production
    public const ENGINE_IDS_CONNECTEURS = 'ENGINE_CHECK_MAINTENANCE'; //Ids des familles de Connecteur à prendre en compte pour les associations avec les outillages de test. Ne pas intégrer les accessoires

}
