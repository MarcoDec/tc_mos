<?php

namespace App\Entity\Hr;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Management\Parameter as BaseParameter;
use Doctrine\ORM\Mapping as ORM;

#[
   ORM\Entity,
   ApiResource
]
class Parameter extends BaseParameter {
    public const EMPLOYEE_DIRECTORIES = 'EMPLOYEE_DIRECTORIES';
    public const EMPLOYEE_EXPIRATION_DIRECTORIES = 'EMPLOYEE_EXPIRATION_DIRECTORIES';
    public const EMPLOYEE_ATTACHMENT_CATEGORIES = 'EMPLOYEE_ATTACHMENT_CATEGORIES';
    public const EMPLOYEE_MISSING_CHART_CATEGORY = 'EMPLOYEE_MISSING_CHART_CATEGORY';
    public const EMPLOYEE_COMPETENCE_REMINDER = 'EMPLOYEE_COMPETENCE_REMINDER';
    public const EMPLOYEE_CHANGE_WARNING_WORKFLOW = 'EMPLOYEE_CHANGE_WARNING_WORKFLOW';
}
