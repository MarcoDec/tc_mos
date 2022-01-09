<?php

namespace App\DataFixtures;

use App\Doctrine\Type\Type;
use App\Entity\Hr\Parameter;
use App\Entity\Management\Parameter as ParameterAlias;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HrParametersFixtures extends Fixture
{
   const PARAMETERS = [
      [
         "name" => Parameter::EMPLOYEE_ATTACHMENT_CATEGORIES,
         "description" => "Liste représentant les choix possibles des catégories de pièces jointes accepté par Employee\nFormat de name : ATTACHMENT_CATEGORY_{nom de l'entité au format UPPERCASE}\nFormat de value : choix séparé par une virgule",
         "process" => "HR",
         "value" => "contrats,doc_a_date,doc_a_date/formations,doc,qualité",
         "type" => Type::TYPE_ARRAY
      ],
      [
         'name' => Parameter::EMPLOYEE_EXPIRATION_DIRECTORIES,
         "description" => "Répertoires où sont stockés les fichiers périssables des employés.",
         "process" => "HR",
         "value" => "doc_a_date",
         "type" => Type::TYPE_ARRAY
      ],
      [
         'name' => Parameter::EMPLOYEE_EXPIRATION_DURATION,
         "description" => "Durée par défault des fichiers périssables des employés en mois",
         "process" => "HR",
         "value" => 120,
         "type" => Type::TYPE_INTEGER
      ],
      [
         'name' => Parameter::EMPLOYEE_MISSING_CHART_CATEGORY,
         'description' => "Catégories d'événements à inclure sur la courbe d'absentéïsme",
         'value' => 'ABSENCE,mise a pied,ABANDON DE POSTE',
         "type" => Type::TYPE_ARRAY
      ],
      [
         "name" => Parameter::EMPLOYEE_COMPETENCE_REMINDER,
         "description" => "Nombre de jours avant le prochain rappel de formation",
         "process" => "HR",
         "value" => "180",
         "type" => Type::TYPE_INTEGER
      ],
      [
         "name" => Parameter::EMPLOYEE_CHANGE_WARNING_WORKFLOW,
         "description" => "Nombre de jours avant le passage de « sous-surveillance » à « actif »",
         "process" => "HR",
         "value" => "90",
         "type" => Type::TYPE_INTEGER
      ]
   ];

   public function load(ObjectManager $manager): void
   {
      foreach (self::PARAMETERS as $parameter) {
         $newHrParameter = new Parameter();
         $newHrParameter
            ->setName($parameter['name'])
            ->setValue($parameter['value'])
            ->setTarget(ParameterAlias::PROCESSES['hr'])
            ->setType($parameter['type'])
         ;
         $manager->persist($newHrParameter);
      }
      $manager->flush();
   }
}
