<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Hr\Employee\Employee;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class Version20220601082823 extends AbstractMigration {
    private UserPasswordHasherInterface $hasher;

    public function __construct(Connection $connection, LoggerInterface $logger) {
        parent::__construct($connection, $logger);
    }

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
        $this->upCarriers();
        $this->upComponentFamilies();
        $this->upColors();
        $this->upEngineGroups();
        $this->upEventTypes();
        $this->upIncoterms();
        $this->upInvoiceTimeDue();
        $this->upOutTrainers();
        $this->upProductFamilies();
        $this->upQualityTypes();
        $this->upRejectTypes();
        $this->upUnits();
        $this->upUsers();
        $this->upVatMessages();
        $this->addSql('DROP FUNCTION UCFIRST');
        $this->addSql('DROP TABLE `country`');
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
