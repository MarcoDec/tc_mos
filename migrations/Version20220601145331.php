<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Hr\Employee\Employee;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class Version20220601145331 extends AbstractMigration {
    private UserPasswordHasherInterface $hasher;

    public function getDescription(): string {
        return 'Migration initiale : transfert selon le nouveau modèle de données.';
    }

    public function setHasher(UserPasswordHasherInterface $hasher): void {
        $this->hasher = $hasher;
    }

    public function up(Schema $schema): void {
        $this->addSql(<<<'SQL'
CREATE FUNCTION UCFIRST (s VARCHAR(255))
    RETURNS VARCHAR(255) DETERMINISTIC
    RETURN CONCAT(UCASE(LEFT(s, 1)), LCASE(SUBSTRING(s, 2)))
SQL);
        $this->upUsers();
        $this->upCarriers();
        $this->upComponentFamilies();
        $this->upColors();
        $this->upCrons();
        $this->upCurrencies();
        $this->upEngineGroups();
        $this->upEventTypes();
        $this->upIncoterms();
        $this->upInvoiceTimeDue();
        $this->upNotifications();
        $this->upOutTrainers();
        $this->upProductFamilies();
        $this->upQualityTypes();
        $this->upRejectTypes();
        $this->upSkills();
        $this->upTimeSlots();
        $this->upUnits();
        $this->upVatMessages();
        $this->upProducts();
        $this->addSql('DROP FUNCTION UCFIRST');
        $this->addSql('DROP TABLE `country`');
        $this->addSql('DROP TABLE `customcode`');
    }

    private function alterTable(string $table, string $comment): void {
        /** @phpstan-ignore-next-line */
        $this->addSql("ALTER TABLE `$table` COMMENT {$this->connection->quote($comment)}");
        $this->addSql("ALTER TABLE `$table` DEFAULT CHARACTER SET utf8mb4");
        $this->addSql("ALTER TABLE `$table` CHARACTER SET utf8mb4");
        $this->addSql("ALTER TABLE `$table` DEFAULT COLLATE `utf8mb4_unicode_ci`");
        $this->addSql("ALTER TABLE `$table` COLLATE `utf8mb4_unicode_ci`");
        $this->addSql("ALTER TABLE `$table` ENGINE = InnoDB");
    }

    private function upCarriers(): void {
        $this->alterTable('carrier', 'Transporteur');
        $this->addSql(<<<'SQL'
ALTER TABLE `carrier`
    ADD `address_address` VARCHAR(50) DEFAULT NULL AFTER `id`,
    ADD `address_address2` VARCHAR(50) DEFAULT NULL AFTER `address_address`,
    ADD `address_city` VARCHAR(50) DEFAULT NULL AFTER `address_address2`,
    ADD `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)' AFTER `address_city`,
    ADD `address_email` VARCHAR(60) DEFAULT NULL AFTER `address_country`,
    ADD `address_phone_number` VARCHAR(20) DEFAULT NULL AFTER `address_email`,
    ADD `address_zip_code` VARCHAR(10) DEFAULT NULL AFTER `address_phone_number`,
    DROP `date_creation`,
    DROP `date_modification`,
    DROP `id_user_creation`,
    DROP `id_user_modification`,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `nom` `name` VARCHAR(50) NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
    }

    private function upColors(): void {
        $this->addSql('RENAME TABLE `couleur` TO `color`');
        $this->alterTable('color', 'Couleur');
        $this->addSql(<<<'SQL'
ALTER TABLE `color`
    ADD `deleted` TINYINT(1) DEFAULT 0 NOT NULL AFTER `id`,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `name` `name` VARCHAR(20) NOT NULL,
    CHANGE `rgb` `rgb` CHAR(7) NOT NULL COMMENT '(DC2Type:char)',
    DROP `ral`
SQL);
        $this->addSql('DROP INDEX `couleur_id_uindex` ON `color`');
        $this->addSql('UPDATE `color` SET `name` = UCFIRST(`name`)');
    }

    private function upComponentFamilies(): void {
        $this->alterTable('component_family', 'Famille de composant');
        $this->addSql(<<<'SQL'
ALTER TABLE `component_family`
    ADD `parent_id` INT UNSIGNED DEFAULT NULL,
    DROP `icon`,
    CHANGE `customsCode` `customs_code` VARCHAR(10) DEFAULT NULL,
    CHANGE `family_name` `name` VARCHAR(40) NOT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `prefix` `code` CHAR(3) DEFAULT NULL COMMENT '(DC2Type:char)',
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addSql('ALTER TABLE `component_family` ADD CONSTRAINT `IDX_79FF2A21727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `component_family` (`id`)');
        $this->addSql('UPDATE `component_family` SET `code` = UPPER(SUBSTR(`name`, 1, 3))');
        $this->addSql(<<<'SQL'
INSERT INTO `component_family` (`name`, `deleted`, `parent_id`, `code`)
SELECT
    `component_subfamily`.`subfamily_name`,
    `component_subfamily`.`statut`,
    `component_subfamily`.`id_family`,
    `component_family`.`code`
FROM `component_subfamily`
INNER JOIN `component_family` ON `component_subfamily`.`id_family` = `component_family`.`id`
SQL);
        $this->addSql('DROP TABLE `component_subfamily`');
        $this->addSql(<<<'SQL'
UPDATE `component_family` `f`
INNER JOIN `component_family` `parent` ON `f`.`parent_id` = `parent`.`id`
SET `f`.`code` = `parent`.`code`
WHERE `f`.`code` IS NULL
SQL);
        $this->addSql('UPDATE `component_family` SET `name` = UCFIRST(`name`)');
        $this->addSql('ALTER TABLE `component_family` CHANGE `code` `code` CHAR(3) NOT NULL COMMENT \'(DC2Type:char)\'');
    }

    private function upCrons(): void {
        $this->addSql(<<<'SQL'
CREATE TABLE `cron_job` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `command` CHAR(18) NOT NULL COMMENT '(DC2Type:char)',
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `last` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `next` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `period` CHAR(6) NOT NULL COMMENT '(DC2Type:char)'
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);
    }

    private function upCurrencies(): void {
        $this->addSql(<<<'SQL'
CREATE TABLE `currency` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `base` DOUBLE PRECISION DEFAULT '1' NOT NULL,
    `code` CHAR(3) NOT NULL COMMENT '(DC2Type:char)',
    `active` TINYINT(1) DEFAULT 0 NOT NULL,
    CONSTRAINT `IDX_6956883F727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `currency` (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);
    }

    private function upEngineGroups(): void {
        $this->addSql('DROP INDEX `code` ON `engine_group`');
        $this->alterTable('engine_group', 'Groupe d\'équipement');
        $this->addSql(<<<'SQL'
ALTER TABLE `engine_group`
    ADD `deleted` TINYINT(1) DEFAULT 0 NOT NULL AFTER `code`,
    ADD `type` ENUM('counter-part', 'tool', 'workstation') NOT NULL COMMENT '(DC2Type:engine_type)',
    DROP formation_specifique,
    CHANGE `code` `code` VARCHAR(3) NOT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `libelle` `name` VARCHAR(35) NOT NULL,
    CHANGE `organe_securite` `safety_device` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addSql(<<<'SQL'
UPDATE `engine_group` SET
    `name` = UCFIRST(`name`),
    `type` = IF(`id_family_group` = 1, 'workstation', 'tool')
SQL);
        $this->addSql('ALTER TABLE `engine_group` DROP `id_family_group`');
    }

    private function upEventTypes(): void {
        $this->addSql('RENAME TABLE `employee_eventlist` TO `event_type`');
        $this->alterTable('event_type', 'Type d\'événements');
        $this->addSql(<<<'SQL'
ALTER TABLE `event_type`
    ADD `deleted` TINYINT(1) DEFAULT 0 NOT NULL AFTER `id`,
    ADD `to_status` ENUM('blocked', 'disabled', 'enabled', 'warning') DEFAULT NULL COMMENT '(DC2Type:employee_current_place)',
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `motif` `name` VARCHAR(30) NOT NULL
SQL);
        $this->addSql('UPDATE `event_type` SET `name` = UCFIRST(`name`)');
    }

    private function upIncoterms(): void {
        $this->addSql('DROP INDEX `uk_c_input_reason` ON `incoterms`');
        $this->alterTable('incoterms', 'Incoterms');
        $this->addSql(<<<'SQL'
ALTER TABLE `incoterms`
    CHANGE `code` `code` VARCHAR(11) NOT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `label` `name` VARCHAR(50) NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addSql('UPDATE `incoterms` SET `name` = UCFIRST(`name`)');
    }

    private function upInvoiceTimeDue(): void {
        $this->addSql(<<<'SQL'
INSERT INTO `invoicetimedue` (`statut`, `libelle`, `days`, `endofmonth`, `date_creation`, `date_modification`, `id_user_creation`, `id_user_modification`)
SELECT `statut`, `libelle`, `days`, `endofmonth`, `date_creation`, `date_modification`, `id_user_creation`, `id_user_modification`
FROM `invoicetimeduesupplier`
SQL);
        $this->addSql('DROP TABLE `invoicetimeduesupplier`');
        $this->addSql('RENAME TABLE `invoicetimedue` TO `invoice_time_due`');
        $this->alterTable('invoice_time_due', 'Délai de paiement des factures');
        $this->addSql(<<<'SQL'
ALTER TABLE `invoice_time_due`
    ADD `days_after_end_of_month` TINYINT UNSIGNED DEFAULT '0' NOT NULL COMMENT '(DC2Type:tinyint)',
    DROP `date_creation`,
    DROP `date_modification`,
    DROP `id_user_creation`,
    DROP `id_user_modification`,
    CHANGE `days` `days` TINYINT UNSIGNED DEFAULT '0' NOT NULL COMMENT '(DC2Type:tinyint)',
    CHANGE `endofmonth` `end_of_month` TINYINT UNSIGNED DEFAULT '0' NOT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `libelle` `name` VARCHAR(40) NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addSql(<<<'SQL'
DELETE `i1`
FROM `invoice_time_due` as `i1`, `invoice_time_due` as `i2`
WHERE `i1`.`id` > `i2`.`id`
AND `i1`.`name` = `i2`.`name`
SQL);
        $this->addSql('UPDATE `invoice_time_due` SET `name` = UCFIRST(`name`)');
        $this->addSql(<<<'SQL'
CREATE TABLE `invoice_time_due_copy` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `days` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '(DC2Type:tinyint)',
  `days_after_end_of_month` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '(DC2Type:tinyint)',
  `deleted` TINYINT(1) NOT NULL DEFAULT '0',
  `end_of_month` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `name` VARCHAR(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE `utf8mb4_unicode_ci` COMMENT='Délai de paiement des factures'
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `invoice_time_due_copy` (`deleted`, `name`, `days`, `end_of_month`, `days_after_end_of_month`)
SELECT `deleted`, `name`, `days`, `end_of_month`, `days_after_end_of_month`
FROM `invoice_time_due`
SQL);
        $this->addSql('DROP TABLE `invoice_time_due`');
        $this->addSql('RENAME TABLE `invoice_time_due_copy` TO `invoice_time_due`');
    }

    private function upNotifications(): void {
        $this->addSql(<<<'SQL'
CREATE TABLE `notification` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `category` ENUM('default') DEFAULT 'default' NOT NULL COMMENT '(DC2Type:notification_category)',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `read` TINYINT(1) DEFAULT 0 NOT NULL,
    `subject` VARCHAR(50) DEFAULT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_BF5476CAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `employee` (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);
    }

    private function upOutTrainers(): void {
        $this->addSql('RENAME TABLE `employee_extformateur` TO `out_trainer`');
        $this->alterTable('out_trainer', 'Formateur extérieur');
        $this->addSql(<<<'SQL'
ALTER TABLE `out_trainer`
    ADD `deleted` TINYINT(1) DEFAULT 0 NOT NULL AFTER `address_zip_code`,
    ADD `address_address2` VARCHAR(50) DEFAULT NULL AFTER `address_address`,
    ADD `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)' AFTER `address_city`,
    ADD `address_email` VARCHAR(60) DEFAULT NULL AFTER `address_country`,
    DROP `id_user_creation`,
    DROP `date_creation`,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `prenom` `name` VARCHAR(30) NOT NULL,
    CHANGE `nom` `surname` VARCHAR(30) NOT NULL,
    CHANGE `address` `address_address` VARCHAR(50) DEFAULT NULL,
    CHANGE `ville` `address_city` VARCHAR(50) DEFAULT NULL,
    CHANGE `code_postal` `address_zip_code` VARCHAR(10) DEFAULT NULL
SQL);
        $this->addSql(<<<'SQL'
UPDATE `out_trainer`
INNER JOIN `country` ON `out_trainer`.`id_phone_prefix` = `country`.`id`
SET `out_trainer`.`tel` = CONCAT(`country`.`phone_prefix`, `out_trainer`.`tel`),
`out_trainer`.`address_country` = UCASE(`country`.`code`),
`out_trainer`.`surname` = UCASE(`out_trainer`.`surname`),
`out_trainer`.`name` = UCFIRST(`out_trainer`.`name`)
SQL);
        $this->addSql(<<<'SQL'
ALTER TABLE `out_trainer`
    DROP `id_phone_prefix`,
    DROP `society`,
    CHANGE `tel` `address_phone_number` VARCHAR(20) DEFAULT NULL
SQL);
    }

    private function upProductFamilies(): void {
        $this->alterTable('product_family', 'Famille de produit');
        $this->addSql(<<<'SQL'
ALTER TABLE `product_family`
    ADD `parent_id` INT UNSIGNED DEFAULT NULL,
    DROP `icon`,
    CHANGE `customsCode` `customs_code` VARCHAR(10) DEFAULT NULL,
    CHANGE `family_name` `name` VARCHAR(30) NOT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addSql('ALTER TABLE `product_family` ADD CONSTRAINT `IDX_C79A60FF727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `product_family` (`id`)');
        $this->addSql(<<<'SQL'
INSERT INTO `product_family` (`name`, `parent_id`)
SELECT `subfamily_name`, `id_family`
FROM `product_subfamily`
SQL);
        $this->addSql('DROP TABLE `product_subfamily`');
        $this->addSql('UPDATE `product_family` SET `name` = UCFIRST(`name`)');
    }

    private function upProducts(): void {
        $this->alterTable('product', 'Produit');
        $this->addSql('DELETE FROM `product` WHERE `statut` = 1');
        $this->addSql('UPDATE `product` SET `conditionnement` = 1 WHERE `conditionnement` = \'\'');
        $this->addSql(<<<'SQL'
ALTER TABLE `product`
    ADD `auto_duration_code` VARCHAR(3) DEFAULT NULL,
    ADD `auto_duration_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `costing_manual_duration_code` VARCHAR(3) DEFAULT NULL,
    ADD `costing_manual_duration_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `parent_id` INT UNSIGNED DEFAULT NULL,
    ADD `unit_id` INT UNSIGNED NOT NULL,
    ADD `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    ADD `forecast_volume_code` VARCHAR(3) DEFAULT NULL,
    ADD `forecast_volume_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `manual_duration_code` VARCHAR(3) DEFAULT NULL,
    ADD `manual_duration_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `max_proto_code` VARCHAR(3) DEFAULT NULL,
    ADD `max_proto_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `min_delivery_code` VARCHAR(3) DEFAULT NULL,
    ADD `min_delivery_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `min_prod_code` VARCHAR(3) DEFAULT NULL,
    ADD `min_prod_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `min_stock_code` VARCHAR(3) DEFAULT NULL,
    ADD `min_stock_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `packaging_code` VARCHAR(3) DEFAULT NULL,
    ADD `packaging_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `price_code` VARCHAR(3) DEFAULT NULL,
    ADD `price_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `price_without_copper_code` VARCHAR(3) DEFAULT NULL,
    ADD `price_without_copper_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `production_delay_code` VARCHAR(3) DEFAULT NULL,
    ADD `production_delay_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `transfert_price_supplies_code` VARCHAR(3) DEFAULT NULL,
    ADD `transfert_price_supplies_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `transfert_price_work_code` VARCHAR(3) DEFAULT NULL,
    ADD `transfert_price_work_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `weight_code` VARCHAR(3) DEFAULT NULL,
    ADD `weight_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `costing_auto_duration_code` VARCHAR(3) DEFAULT NULL,
    ADD `costing_auto_duration_denominator` VARCHAR(3) DEFAULT NULL,
    ADD `customs_code` VARCHAR(10) DEFAULT NULL,
    DROP `id_product_family`,
    DROP `c_200`,
    DROP `c_300`,
    DROP `c_600`,
    DROP `c_700`,
    DROP `c_800`,
    DROP `date_creation`,
    DROP `date_modification`,
    DROP `id_user_creation`,
    DROP `id_user_modification`,
    DROP `isBrokenLinkSolved`,
    DROP `price_tn`,
    DROP `price_md`,
    DROP `price_old`,
    DROP `freeze`,
    DROP `id_customer`,
    DROP `id_factory`,
    DROP `id_country`,
    DROP `id_society`,
    DROP `commande_ouverte`,
    DROP `nb_pcs_h_auto`,
    DROP `nb_pcs_h_manu`,
    DROP `qtcu`,
    DROP `temps_inertie`,
    DROP `imds`,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `id_product_subfamily` `family_id` INT UNSIGNED NOT NULL,
    CHANGE `id_incoterms` `incoterms_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `date_expiration` `expiration_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE `indice` `index` VARCHAR(3) NOT NULL,
    CHANGE `indice_interne` `internal_index` TINYINT UNSIGNED DEFAULT '1' NOT NULL COMMENT '(DC2Type:tinyint)',
    CHANGE `gestion_cu` `managed_copper` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `designation` `name` VARCHAR(80) NOT NULL,
    CHANGE `info_public` `notes` TEXT DEFAULT NULL,
    CHANGE `conditionnement` `packaging_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `typeconditionnement` `packaging_kind` VARCHAR(30) NOT NULL,
    CHANGE `temps_auto` `auto_duration_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `tps_chiff_auto` `costing_auto_duration_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `tps_chiff_manu` `costing_manual_duration_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `volume_previsionnel` `forecast_volume_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `temps_manu` `manual_duration_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `max_proto_quantity` `max_proto_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `livraison_minimum` `min_delivery_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `min_prod_quantity` `min_prod_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `stock_minimum` `min_stock_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `price` `price_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `price_without_cu` `price_without_copper_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `production_delay` `production_delay_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `transfert_price_supplies` `transfert_price_supplies_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `transfert_price_work` `transfert_price_work_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `weight` `weight_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `ref` `ref` VARCHAR(30) NOT NULL,
    CHANGE `id_productstatus` `current_place_name` VARCHAR(255) NOT NULL,
    CHANGE `is_prototype` `kind` VARCHAR(255) NOT NULL
SQL);
        $this->addSql(<<<'SQL'
UPDATE `product`
SET `product`.`incoterms_id` = NULL
WHERE NOT EXISTS (SELECT * FROM `incoterms` WHERE `incoterms`.`id` = `product`.`incoterms_id`)
SQL);
        $this->addSql(<<<'SQL'
UPDATE `product` `p`
INNER JOIN `product` `parent` ON `p`.`id` = `parent`.`id_product_child`
SET `p`.`parent_id` = `parent`.`id`
SQL);
        $this->addSql(<<<'SQL'
UPDATE `product` SET
`product`.`auto_duration_code` = 'h',
`product`.`costing_auto_duration_code` = 'h',
`product`.`forecast_volume_code` = 'U',
`product`.`customs_code` = (SELECT `customcode`.`code` FROM `customcode` WHERE `customcode`.`id` = `product`.`id_customcode`),
`product`.`current_place_name` = CASE
    WHEN `product`.`current_place_name` = 1 THEN 'draft'
    WHEN `product`.`current_place_name` = 2 THEN 'to_validate'
    WHEN `product`.`current_place_name` = 3 THEN 'agreed'
    WHEN `product`.`current_place_name` = 4 THEN 'under_exemption'
    WHEN `product`.`current_place_name` = 5 THEN 'blocked'
    WHEN `product`.`current_place_name` = 6 THEN 'disabled'
    ELSE 'draft'
END,
`product`.`kind` = CASE
    WHEN `product`.`kind` = 0 THEN 'Série'
    WHEN `product`.`kind` = 1 THEN 'Prototype'
    WHEN `product`.`kind` = 2 THEN 'EI'
    ELSE 'Prototype'
END,
`product`.`manual_duration_code` = 'h',
`product`.`max_proto_code` = 'U',
`product`.`min_delivery_code` = 'U',
`product`.`min_prod_code` = 'U',
`product`.`min_stock_code` = 'U',
`product`.`packaging_code` = 'U',
`product`.`price_code` = 'EUR',
`product`.`price_without_copper_code` = 'EUR',
`product`.`production_delay_code` = 'j',
`product`.`transfert_price_supplies_code` = 'EUR',
`product`.`transfert_price_work_code` = 'EUR',
`product`.`unit_id` = (SELECT `unit`.`id` FROM `unit` WHERE `unit`.`code` = 'U'),
`product`.`weight_code` = 'g'
SQL);
        $this->addSql(<<<'SQL'
ALTER TABLE `product`
    ADD CONSTRAINT `IDX_D34A04ADC35E566A` FOREIGN KEY (`family_id`) REFERENCES `product_family` (`id`),
    ADD CONSTRAINT `IDX_D34A04AD43D02C80` FOREIGN KEY (`incoterms_id`) REFERENCES `incoterms` (`id`),
    ADD CONSTRAINT `IDX_D34A04AD727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `product` (`id`),
    ADD CONSTRAINT `IDX_D34A04ADF8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`),
    DROP `id_product_child`,
    DROP `id_customcode`,
    CHANGE `current_place_name` `current_place_name` ENUM('agreed', 'blocked', 'disabled', 'draft', 'to_validate', 'under_exemption') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:product_current_place)',
    CHANGE `kind` `kind` ENUM('EI', 'Prototype', 'Série', 'Pièce de rechange') DEFAULT 'Prototype' NOT NULL COMMENT '(DC2Type:product_kind)'
SQL);
        $this->addSql(<<<'SQL'
CREATE TABLE `product_copy` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `auto_duration_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_duration_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_duration_value` double NOT NULL DEFAULT '0',
  `costing_auto_duration_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costing_auto_duration_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costing_auto_duration_value` double NOT NULL DEFAULT '0',
  `costing_manual_duration_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costing_manual_duration_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costing_manual_duration_value` double NOT NULL DEFAULT '0',
  `current_place_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)',
  `current_place_name` enum('agreed','blocked','disabled','draft','to_validate','under_exemption') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT '(DC2Type:product_current_place)',
  `customs_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `expiration_date` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
  `family_id` int UNSIGNED NOT NULL,
  `forecast_volume_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forecast_volume_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forecast_volume_value` double NOT NULL DEFAULT '0',
  `incoterms_id` int UNSIGNED DEFAULT NULL,
  `index` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_index` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT '(DC2Type:tinyint)',
  `kind` enum('EI','Prototype','Série','Pièce de rechange') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Prototype' COMMENT '(DC2Type:product_kind)',
  `managed_copper` tinyint(1) NOT NULL DEFAULT '0',
  `manual_duration_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manual_duration_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manual_duration_value` double NOT NULL DEFAULT '0',
  `max_proto_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_proto_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_proto_value` double NOT NULL DEFAULT '0',
  `min_delivery_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_delivery_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_delivery_value` double NOT NULL DEFAULT '0',
  `min_prod_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_prod_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_prod_value` double NOT NULL DEFAULT '0',
  `min_stock_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_stock_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_stock_value` double NOT NULL DEFAULT '0',
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `packaging_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packaging_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packaging_kind` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `packaging_value` double NOT NULL DEFAULT '0',
  `parent_id` int UNSIGNED DEFAULT NULL,
  `price_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_value` double NOT NULL DEFAULT '0',
  `price_without_copper_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_without_copper_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_without_copper_value` double NOT NULL DEFAULT '0',
  `production_delay_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `production_delay_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `production_delay_value` double NOT NULL DEFAULT '0',
  `ref` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transfert_price_supplies_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfert_price_supplies_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfert_price_supplies_value` double NOT NULL DEFAULT '0',
  `transfert_price_work_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfert_price_work_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfert_price_work_value` double NOT NULL DEFAULT '0',
  `unit_id` int UNSIGNED NOT NULL,
  `weight_code` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_denominator` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_value` double NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Produit';
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `product_copy` (
  `auto_duration_code`,
  `auto_duration_denominator`,
  `auto_duration_value`,
  `costing_auto_duration_code`,
  `costing_auto_duration_denominator`,
  `costing_auto_duration_value`,
  `costing_manual_duration_code`,
  `costing_manual_duration_denominator`,
  `costing_manual_duration_value`,
  `current_place_date`,
  `current_place_name`,
  `customs_code`,
  `deleted`,
  `expiration_date`,
  `family_id`,
  `forecast_volume_code`,
  `forecast_volume_denominator`,
  `forecast_volume_value`,
  `incoterms_id`,
  `index`,
  `internal_index`,
  `kind`,
  `managed_copper`,
  `manual_duration_code`,
  `manual_duration_denominator`,
  `manual_duration_value`,
  `max_proto_code`,
  `max_proto_denominator`,
  `max_proto_value`,
  `min_delivery_code`,
  `min_delivery_denominator`,
  `min_delivery_value`,
  `min_prod_code`,
  `min_prod_denominator`,
  `min_prod_value`,
  `min_stock_code`,
  `min_stock_denominator`,
  `min_stock_value`,
  `name`,
  `notes`,
  `packaging_code`,
  `packaging_denominator`,
  `packaging_kind`,
  `packaging_value`,
  `parent_id`,
  `price_code`,
  `price_denominator`,
  `price_value`,
  `price_without_copper_code`,
  `price_without_copper_denominator`,
  `price_without_copper_value`,
  `production_delay_code`,
  `production_delay_denominator`,
  `production_delay_value`,
  `ref`,
  `transfert_price_supplies_code`,
  `transfert_price_supplies_denominator`,
  `transfert_price_supplies_value`,
  `transfert_price_work_code`,
  `transfert_price_work_denominator`,
  `transfert_price_work_value`,
  `unit_id`,
  `weight_code`,
  `weight_denominator`,
  `weight_value`
) SELECT
  `auto_duration_code`,
  `auto_duration_denominator`,
  `auto_duration_value`,
  `costing_auto_duration_code`,
  `costing_auto_duration_denominator`,
  `costing_auto_duration_value`,
  `costing_manual_duration_code`,
  `costing_manual_duration_denominator`,
  `costing_manual_duration_value`,
  `current_place_date`,
  `current_place_name`,
  `customs_code`,
  `deleted`,
  `expiration_date`,
  `family_id`,
  `forecast_volume_code`,
  `forecast_volume_denominator`,
  `forecast_volume_value`,
  `incoterms_id`,
  `index`,
  `internal_index`,
  `kind`,
  `managed_copper`,
  `manual_duration_code`,
  `manual_duration_denominator`,
  `manual_duration_value`,
  `max_proto_code`,
  `max_proto_denominator`,
  `max_proto_value`,
  `min_delivery_code`,
  `min_delivery_denominator`,
  `min_delivery_value`,
  `min_prod_code`,
  `min_prod_denominator`,
  `min_prod_value`,
  `min_stock_code`,
  `min_stock_denominator`,
  `min_stock_value`,
  `name`,
  `notes`,
  `packaging_code`,
  `packaging_denominator`,
  `packaging_kind`,
  `packaging_value`,
  `parent_id`,
  `price_code`,
  `price_denominator`,
  `price_value`,
  `price_without_copper_code`,
  `price_without_copper_denominator`,
  `price_without_copper_value`,
  `production_delay_code`,
  `production_delay_denominator`,
  `production_delay_value`,
  `ref`,
  `transfert_price_supplies_code`,
  `transfert_price_supplies_denominator`,
  `transfert_price_supplies_value`,
  `transfert_price_work_code`,
  `transfert_price_work_denominator`,
  `transfert_price_work_value`,
  `unit_id`,
  `weight_code`,
  `weight_denominator`,
  `weight_value`
FROM `product`;
SQL);
        $this->addSql('DROP TABLE `product`');
        $this->addSql('RENAME TABLE `product_copy` TO `product`');
        $this->addSql(<<<'SQL'
ALTER TABLE `product`
    ADD CONSTRAINT `IDX_D34A04ADC35E566A` FOREIGN KEY (`family_id`) REFERENCES `product_family` (`id`),
    ADD CONSTRAINT `IDX_D34A04AD43D02C80` FOREIGN KEY (`incoterms_id`) REFERENCES `incoterms` (`id`),
    ADD CONSTRAINT `IDX_D34A04AD727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `product` (`id`),
    ADD CONSTRAINT `IDX_D34A04ADF8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`)
SQL);
    }

    private function upQualityTypes(): void {
        $this->addSql('RENAME TABLE `qualitycontrol` TO `quality_type`');
        $this->alterTable('quality_type', 'Type qualité');
        $this->addSql(<<<'SQL'
ALTER TABLE `quality_type`
    CHANGE `qualitycontrol` `name` VARCHAR(40) NOT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addSql('UPDATE `quality_type` SET `name` = UCFIRST(`name`)');
    }

    private function upRejectTypes(): void {
        $this->addSql('RENAME TABLE `production_rejectlist` TO `reject_type`');
        $this->alterTable('reject_type', 'Type de rebus');
        $this->addSql(<<<'SQL'
ALTER TABLE `reject_type`
    DROP `id_user_creation`,
    DROP `date_creation`,
    DROP `id_user_modification`,
    DROP `date_modification`,
    CHANGE `libelle` `name` VARCHAR(40) NOT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addSql('DELETE FROM `reject_type` WHERE `deleted` = 1');
        $this->addSql('UPDATE `reject_type` SET `name` = UCFIRST(`name`)');
        $this->addSql(<<<'SQL'
CREATE TABLE `reject_type_copy` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE `utf8mb4_unicode_ci` COMMENT='Type de rebus'
SQL);
        $this->addSql('INSERT INTO `reject_type_copy` (`deleted`, `name`) SELECT `deleted`, `name` FROM `reject_type`');
        $this->addSql('DROP TABLE `reject_type`');
        $this->addSql('RENAME TABLE `reject_type_copy` TO `reject_type`');
    }

    private function upSkills(): void {
        $this->addSql(<<<'SQL'
CREATE TABLE `skill_type` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `name` VARCHAR(50) NOT NULL
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);
    }

    private function upTimeSlots(): void {
        $this->addSql(<<<'SQL'
CREATE TABLE `time_slot` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `end` TIME NOT NULL COMMENT '(DC2Type:time_immutable)',
    `end_break` TIME DEFAULT NULL COMMENT '(DC2Type:time_immutable)',
    `name` VARCHAR(10) NOT NULL,
    `start` TIME NOT NULL COMMENT '(DC2Type:time_immutable)',
    `start_break` TIME DEFAULT NULL COMMENT '(DC2Type:time_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE `utf8mb4_unicode_ci` COMMENT='Plages horaires'
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `time_slot` (`end`, `end_break`, `name`, `start`, `start_break`) VALUES
('13:30:00', NULL, 'Matin', '05:30:00', NULL),
('17:30:00', '13:30:00', 'Journée', '07:30:00', '12:30:00'),
('21:30:00',  NULL, 'Après-midi', '13:30:00', NULL),
('08:00:00',  NULL, 'Samedi', '13:00:00', NULL)
SQL);
    }

    private function upUnits(): void {
        $this->alterTable('unit', 'Unité');
        $this->addSql(<<<'SQL'
ALTER TABLE `unit`
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `parent` `parent_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `unit_complete_lbl` `name` VARCHAR(50) NOT NULL,
    CHANGE `unit_short_lbl` `code` VARCHAR(6) NOT NULL
SQL);
        $this->addSql('ALTER TABLE `unit` ADD CONSTRAINT `IDX_DCBB0C53727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `unit` (`id`)');
        $this->addSql('UPDATE `unit` SET `name` = UCFIRST(`name`)');
    }

    private function upUsers(): void {
        ($user = (new Employee()))
            ->setPassword($this->hasher->hashPassword($user, 'super'))
            ->addRole(Roles::ROLE_ACCOUNTING_ADMIN)
            ->addRole(Roles::ROLE_HR_ADMIN)
            ->addRole(Roles::ROLE_IT_ADMIN)
            ->addRole(Roles::ROLE_LEVEL_DIRECTOR)
            ->addRole(Roles::ROLE_LOGISTICS_ADMIN)
            ->addRole(Roles::ROLE_MAINTENANCE_ADMIN)
            ->addRole(Roles::ROLE_MANAGEMENT_ADMIN)
            ->addRole(Roles::ROLE_PRODUCTION_ADMIN)
            ->addRole(Roles::ROLE_PROJECT_ADMIN)
            ->addRole(Roles::ROLE_PURCHASE_ADMIN)
            ->addRole(Roles::ROLE_QUALITY_ADMIN)
            ->addRole(Roles::ROLE_SELLING_ADMIN);
        $this->addSql(<<<'SQL'
CREATE TABLE `employee` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `emb_roles_roles` TEXT NOT NULL COMMENT '(DC2Type:simple_array)',
    `name` VARCHAR(30) NOT NULL,
    `password` CHAR(60) NOT NULL  COMMENT '(DC2Type:char)',
    `username` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE `utf8mb4_unicode_ci` COMMENT='Employé'
SQL);
        $this->addSql(sprintf(
            'INSERT INTO employee (name, password, username, emb_roles_roles) VALUES (\'Super\', %s, \'super\', %s)',
            /** @phpstan-ignore-next-line */
            $this->connection->quote($user->getPassword()),
            /** @phpstan-ignore-next-line */
            $this->connection->quote(implode(',', $user->getRoles()))
        ));
        $this->addSql(<<<'SQL'
CREATE TABLE `token` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `employee_id` INT UNSIGNED NOT NULL,
    `expire_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `token` CHAR(120) NOT NULL COMMENT '(DC2Type:char)',
    CONSTRAINT `IDX_5F37A13B8C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE `utf8mb4_unicode_ci` COMMENT='Token'
SQL);
    }

    private function upVatMessages(): void {
        $this->addSql('RENAME TABLE `messagetva` TO `vat_message`');
        $this->alterTable('vat_message', 'Message TVA');
        $this->addSql(<<<'SQL'
ALTER TABLE `vat_message`
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `message` `name` VARCHAR(120) NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    DROP date_creation,
    DROP date_modification,
    DROP id_user_creation,
    DROP id_user_modification
SQL);
    }
}
