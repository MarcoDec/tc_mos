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
    public const COMPONENT_ATTACHMENT_CATEGORIES = 'COMPONENT_ATTACHMENT_CATEGORIES'; //Liste représentant les choix possibles des catégories de pièces jointes accepté par Composant
    public const COMPONENT_EXPIRATION_DIRECTORIES = 'COMPONENT_EXPIRATION_DIRECTORIES'; // Répertoires où sont stockés les fichiers périssables des composants.
    public const COMPONENT_EXPIRATION_DURATION = 'COMPONENT_EXPIRATION_DURATION'; // Durée en mois d'expiration des répertoires où sont stockés les fichiers périssables des composants.
    public const COMPONENT_ID_CORDON = 'COMPONENT_ID_CORDON'; // Liste des id des familles de composant de type cordon. Se présente comme une liste de numéros séparés par un virgule
    public const COMPONENT_ID_CORDON_FIL = 'COMPONENT_ID_CORDON_FIL'; // Liste des id des familles de composant de type fil ou cordon
    public const COMPONENT_ID_COSSES = 'COMPONENT_ID_COSSES'; // Liste des id des familles de composant de type cosse
    public const COMPONENT_ID_GAINES = 'COMPONENT_ID_GAINES'; // Liste des id des familles de composant de type cosse
    public const COMPONENT_EXPIRATION_DATE_WARNING = 'COMPONENT_EXPIRATION_DATE_WARNING'; // Défini combien de jours l'alerte doit s'afficher avant la fin de vie d'un composant.

}
