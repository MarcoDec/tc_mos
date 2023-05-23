<?php

namespace App\Entity\Hr;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Management\Parameter as BaseParameter;
use Doctrine\ORM\Mapping as ORM;

#[
   ORM\Entity
]
class Parameter extends BaseParameter {
    public const EMPLOYEE_ATTACHMENT_CATEGORIES = 'EMPLOYEE_ATTACHMENT_CATEGORIES';
    public const EMPLOYEE_CHANGE_WARNING_WORKFLOW = 'EMPLOYEE_CHANGE_WARNING_WORKFLOW'; //Nombre de jours avant le passage de « sous-surveillance » à « actif »
    public const EMPLOYEE_COMPETENCE_REMINDER = 'EMPLOYEE_COMPETENCE_REMINDER'; //Nombre de jours avant le prochain rappel de formation
    public const EMPLOYEE_DIRECTORIES = 'EMPLOYEE_DIRECTORIES';////Liste représentant les choix possibles des catégories de pièces jointes accepté par Employee Format de name : ATTACHMENT_CATEGORY_{nom de l'entité au format UPPERCASE} Format de value : choix séparé par une virgule
    public const EMPLOYEE_EXPIRATION_DIRECTORIES = 'EMPLOYEE_EXPIRATION_DIRECTORIES'; // Répertoires où sont stockés les fichiers périssables des employés.
    public const EMPLOYEE_EXPIRATION_DURATION = 'EMPLOYEE_EXPIRATION_DURATION'; // Durée en jour d'expiration des répertoires où sont stockés les fichiers périssables des employés.
    public const EMPLOYEE_MISSING_CHART_CATEGORY = 'EMPLOYEE_MISSING_CHART_CATEGORY'; //Catégories d'événements à inclure sur la courbe d'absentéïsme
    public const EMPLOYEE_BLOCKED_TO_WATCHDOG_DURATION = "EMPLOYEE_BLOCKED_TO_WATCHDOG_DURATION";//Nombre de jours avant le passage de « bloqué » à « sous-surveillance »
}
