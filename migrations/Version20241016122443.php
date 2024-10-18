<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016122443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `accessoire` (
        `id` int NOT NULL,
        `connecteur_id` int DEFAULT NULL,
        `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `id_component` int DEFAULT NULL,
        `id_connecteur` int DEFAULT NULL,
        `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `attribute` (
            `id` int UNSIGNED NOT NULL,
            `unit_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `type` enum(\'bool\',\'color\',\'int\',\'percent\',\'text\',\'measure\',\'measureSelect\') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'text\' COMMENT \'(DC2Type:attribute)\'
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `attribute_family` (
            `attribute_id` int UNSIGNED NOT NULL,
            `family_id` int UNSIGNED NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `color` (
            `id` int UNSIGNED NOT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
            `rgb` char(7) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT \'(DC2Type:char)\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `company` (
            `id` int UNSIGNED NOT NULL,
            `society_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `delivery_time` tinyint UNSIGNED NOT NULL DEFAULT \'0\' COMMENT \'(DC2Type:tinyint)\',
            `delivery_time_open_days` tinyint(1) NOT NULL DEFAULT \'1\',
            `engine_hour_rate` double UNSIGNED NOT NULL DEFAULT \'0\',
            `general_margin` double UNSIGNED NOT NULL DEFAULT \'0\',
            `handling_hour_rate` double UNSIGNED NOT NULL DEFAULT \'0\',
            `ip` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `management_fees` double UNSIGNED NOT NULL DEFAULT \'0\',
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `notes` text COLLATE utf8mb4_unicode_ci,
            `number_of_team_per_day` tinyint UNSIGNED NOT NULL DEFAULT \'0\' COMMENT \'(DC2Type:tinyint)\',
            `work_timetable` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `component` (
            `id` int UNSIGNED NOT NULL,
            `family_id` int UNSIGNED NOT NULL,
            `parent_id` int UNSIGNED DEFAULT NULL,
            `unit_id` int UNSIGNED NOT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `customs_code` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `end_of_life` date DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\',
            `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `index` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'0\',
            `managed_stock` tinyint(1) NOT NULL DEFAULT \'0\',
            `manufacturer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `manufacturer_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `need_gasket` tinyint(1) NOT NULL DEFAULT \'0\',
            `notes` text COLLATE utf8mb4_unicode_ci,
            `order_info` text COLLATE utf8mb4_unicode_ci,
            `ppm_rate` smallint UNSIGNED NOT NULL DEFAULT \'10\',
            `quality` smallint UNSIGNED NOT NULL DEFAULT \'0\',
            `reach` tinyint(1) NOT NULL DEFAULT \'0\',
            `rohs` tinyint(1) NOT NULL DEFAULT \'0\',
            `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `copper_weight_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `copper_weight_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `copper_weight_value` double NOT NULL DEFAULT \'0\',
            `emb_blocker_state` enum(\'blocked\',\'disabled\',\'enabled\',\'under_waiver\') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'enabled\' COMMENT \'(DC2Type:blocker_state)\',
            `emb_state_state` enum(\'agreed\',\'draft\',\'warning\',\'closed\') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'draft\' COMMENT \'(DC2Type:component_state)\',
            `forecast_volume_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `forecast_volume_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `forecast_volume_value` double NOT NULL DEFAULT \'0\',
            `min_stock_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `min_stock_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `min_stock_value` double NOT NULL DEFAULT \'0\',
            `weight_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `weight_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `weight_value` double NOT NULL DEFAULT \'0\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `component_attachment` (
            `id` int UNSIGNED NOT NULL,
            `component_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `expiration_date` date DEFAULT NULL,
            `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `component_attribute` (
            `id` int UNSIGNED NOT NULL,
            `attribute_id` int UNSIGNED NOT NULL,
            `color_id` int UNSIGNED DEFAULT NULL,
            `component_id` int UNSIGNED NOT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `measure_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `measure_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `measure_value` double NOT NULL DEFAULT \'0\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `component_family` (
            `id` int UNSIGNED NOT NULL,
            `parent_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
            `code` char(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT \'(DC2Type:char)\',
            `copperable` tinyint(1) NOT NULL DEFAULT \'0\',
            `customs_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `connecteur` (
            `id` int NOT NULL,
            `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `difficulte_assemblage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `id_bom` int DEFAULT NULL,
            `id_component` int DEFAULT NULL,
            `id_product` int DEFAULT NULL,
            `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `employee` (
            `id` int UNSIGNED NOT NULL,
            `company_id` int UNSIGNED DEFAULT NULL,
            `manager_id` int UNSIGNED DEFAULT NULL,
            `team_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `old_id` int DEFAULT NULL,
            `birth_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `birthday` datetime DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            `entry_date` date DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\',
            `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `gender` enum(\'female\',\'male\') COLLATE utf8mb4_unicode_ci DEFAULT \'male\' COMMENT \'(DC2Type:gender_place)\',
            `initials` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `level_of_study` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `matricule` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
            `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `password` char(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'(DC2Type:char)\',
            `plain_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `situation` enum(\'married\',\'single\',\'windowed\') COLLATE utf8mb4_unicode_ci DEFAULT \'single\' COMMENT \'(DC2Type:situation_place)\',
            `social_security_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `surname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `time_card` int DEFAULT NULL,
            `user_enabled` tinyint(1) NOT NULL DEFAULT \'0\',
            `username` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_address` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_address2` varchar(110) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_country` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'(DC2Type:char)\',
            `address_email` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_phone_number` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_zip_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `emb_blocker_state` enum(\'blocked\',\'disabled\',\'enabled\',\'under_waiver\') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'enabled\' COMMENT \'(DC2Type:blocker_state)\',
            `emb_roles_roles` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT \'(DC2Type:simple_array)\',
            `emb_state_state` enum(\'agreed\',\'warning\',\'closed\') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'warning\' COMMENT \'(DC2Type:employee_state)\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `employee_attachment` (
            `id` int UNSIGNED NOT NULL,
            `employee_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'doc\',
            `expiration_date` date DEFAULT NULL,
            `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `employee_contact` (
            `id` int UNSIGNED NOT NULL,
            `employee_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `phone` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `surname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                
        $this->addSql('CREATE TABLE `nomenclature` (
            `id` int UNSIGNED NOT NULL,
            `component_id` int UNSIGNED DEFAULT NULL,
            `product_id` int UNSIGNED NOT NULL,
            `sub_product_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `mandated` tinyint(1) NOT NULL DEFAULT \'1\',
            `quantity_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `quantity_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `quantity_value` double NOT NULL DEFAULT \'0\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                
        $this->addSql('CREATE TABLE `product` (
            `id` int UNSIGNED NOT NULL,
            `family_id` int UNSIGNED NOT NULL,
            `parent_id` int UNSIGNED DEFAULT NULL,
            `unit_id` int UNSIGNED NOT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            `customs_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `end_of_life` date DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\',
            `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `index` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `internal_index` tinyint UNSIGNED NOT NULL DEFAULT \'1\' COMMENT \'(DC2Type:tinyint)\',
            `kind` enum(\'EI\',\'Prototype\',\'Série\',\'Pièce de rechange\') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'Prototype\' COMMENT \'(DC2Type:product_kind)\',
            `label_logo` smallint NOT NULL DEFAULT \'0\',
            `managed_copper` tinyint(1) NOT NULL DEFAULT \'0\',
            `name` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `notes` text COLLATE utf8mb4_unicode_ci,
            `packaging_incorrect` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `packaging_kind` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `auto_duration_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `auto_duration_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `auto_duration_value` double NOT NULL DEFAULT \'0\',
            `costing_auto_duration_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `costing_auto_duration_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `costing_auto_duration_value` double NOT NULL DEFAULT \'0\',
            `costing_manual_duration_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `costing_manual_duration_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `costing_manual_duration_value` double NOT NULL DEFAULT \'0\',
            `emb_blocker_state` enum(\'blocked\',\'disabled\',\'enabled\',\'under_waiver\') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'enabled\' COMMENT \'(DC2Type:blocker_state)\',
            `emb_state_state` enum(\'agreed\',\'draft\',\'to_validate\',\'warning\',\'closed\') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'draft\' COMMENT \'(DC2Type:product_state)\',
            `forecast_volume_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `forecast_volume_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `forecast_volume_value` double NOT NULL DEFAULT \'0\',
            `manual_duration_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `manual_duration_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `manual_duration_value` double NOT NULL DEFAULT \'0\',
            `max_proto_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `max_proto_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `max_proto_value` double NOT NULL DEFAULT \'0\',
            `min_delivery_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `min_delivery_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `min_delivery_value` double NOT NULL DEFAULT \'0\',
            `min_prod_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `min_prod_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `min_prod_value` double NOT NULL DEFAULT \'0\',
            `min_stock_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `min_stock_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `min_stock_value` double NOT NULL DEFAULT \'0\',
            `packaging_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `packaging_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `packaging_value` double NOT NULL DEFAULT \'0\',
            `price_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `price_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `price_value` double NOT NULL DEFAULT \'0\',
            `price_without_copper_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `price_without_copper_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `price_without_copper_value` double NOT NULL DEFAULT \'0\',
            `production_delay_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `production_delay_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `production_delay_value` double NOT NULL DEFAULT \'0\',
            `transfert_price_supplies_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `transfert_price_supplies_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `transfert_price_supplies_value` double NOT NULL DEFAULT \'0\',
            `transfert_price_work_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `transfert_price_work_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `transfert_price_work_value` double NOT NULL DEFAULT \'0\',
            `weight_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `weight_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `weight_value` double NOT NULL DEFAULT \'0\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                
        $this->addSql('CREATE TABLE `product_attachment` (
            `id` int UNSIGNED NOT NULL,
            `product_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `expiration_date` date DEFAULT NULL,
            `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                
        $this->addSql('CREATE TABLE `product_family` (
            `id` int UNSIGNED NOT NULL,
            `parent_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            `customs_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                
        $this->addSql('CREATE TABLE `skill` (
            `id` int UNSIGNED NOT NULL,
            `employee_id` int UNSIGNED DEFAULT NULL,
            `in_trainer_id` int UNSIGNED DEFAULT NULL,
            `product_id` int UNSIGNED DEFAULT NULL,
            `reminded_child_id` int UNSIGNED DEFAULT NULL,
            `kind_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `ended_date` date DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\',
            `level` tinyint UNSIGNED NOT NULL DEFAULT \'0\' COMMENT \'(DC2Type:tinyint)\',
            `remindable` tinyint(1) NOT NULL DEFAULT \'0\',
            `reminded_date` date DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\',
            `started_date` date DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                
        $this->addSql('CREATE TABLE `skill_type` (
            `id` int UNSIGNED NOT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                
        $this->addSql('CREATE TABLE `society` (
            `id` int UNSIGNED NOT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `bank_details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `legal_form` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `notes` text COLLATE utf8mb4_unicode_ci,
            `siren` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `accounting_account` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `ar` tinyint(1) NOT NULL DEFAULT \'0\',
            `ppm_rate` smallint UNSIGNED NOT NULL DEFAULT \'10\',
            `vat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_address` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_address2` varchar(110) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_country` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT \'(DC2Type:char)\',
            `address_email` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_phone_number` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address_zip_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `copper_last` datetime DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            `copper_managed` tinyint(1) NOT NULL DEFAULT \'0\',
            `copper_next` datetime DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            `copper_type` enum(\'à la livraison\',\'mensuel\',\'semestriel\') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'mensuel\' COMMENT \'(DC2Type:copper)\',
            `copper_index_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `copper_index_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `copper_index_value` double NOT NULL DEFAULT \'0\',
            `invoice_min_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `invoice_min_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `invoice_min_value` double NOT NULL DEFAULT \'0\',
            `order_min_code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `order_min_denominator` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
            `order_min_value` double NOT NULL DEFAULT \'0\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                
        $this->addSql('CREATE TABLE `team` (
            `id` int UNSIGNED NOT NULL,
            `company_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                      
        $this->addSql('CREATE TABLE `token` (
            `id` int UNSIGNED NOT NULL,
            `employee_id` int UNSIGNED NOT NULL,
            `expire_at` datetime NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            `token` char(120) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT \'(DC2Type:char)\'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                      
        $this->addSql('CREATE TABLE `unit` (
            `id` int UNSIGNED NOT NULL,
            `parent_id` int UNSIGNED DEFAULT NULL,
            `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
            `base` double NOT NULL DEFAULT \'1\',
            `code` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
            `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                      
        $this->addSql('CREATE TABLE `voie` (
            `id` int NOT NULL,
            `connecteur_id` int DEFAULT NULL,
            `commentaire` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `couleur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `couleur_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `couleur_3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
                      
        $this->addSql('ALTER TABLE `accessoire`
            ADD PRIMARY KEY (`id`),
            ADD KEY `IDX_8FD026A4A8D987E` (`connecteur_id`);');
              
        $this->addSql('ALTER TABLE `connecteur`
            ADD PRIMARY KEY (`id`);');

        $this->addSql('ALTER TABLE `voie`
        ADD PRIMARY KEY (`id`),
        ADD KEY `IDX_A57CE9784A8D987E` (`connecteur_id`);');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
