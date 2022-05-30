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

final class Version20220527152146 extends AbstractMigration {
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
        $this->upCarriers();
        $this->upComponentFamilies();
        $this->upColors();
        $this->upInvoiceTimeDue();
        $this->upUnits();
        $this->upUsers();
        $this->upVatMessages();
    }

    private function alterTable(string $table, string $comment): void {
        $this->addSql("ALTER TABLE `$table` COMMENT '$comment'");
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
    ADD `address_address` VARCHAR(50) DEFAULT NULL,
    ADD `address_address2` VARCHAR(50) DEFAULT NULL,
    ADD `address_city` VARCHAR(50) DEFAULT NULL,
    ADD `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    ADD `address_email` VARCHAR(60) DEFAULT NULL,
    ADD `address_phone_number` VARCHAR(20) DEFAULT NULL,
    ADD `address_zip_code` VARCHAR(10) DEFAULT NULL,
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
    ADD `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `name` `name` VARCHAR(20) NOT NULL,
    CHANGE `rgb` `rgb` CHAR(7) NOT NULL COMMENT '(DC2Type:char)',
    DROP `ral`
SQL);
        $this->addSql('DROP INDEX `couleur_id_uindex` ON `color`');
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
        $this->addSql('ALTER TABLE `component_family` CHANGE `code` `code` CHAR(3) NOT NULL COMMENT \'(DC2Type:char)\'');
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
        $this->addSql(<<<'SQL'
CREATE TABLE `invoice_time_due_copy` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `deleted` TINYINT(1) NOT NULL DEFAULT '0',
  `name` VARCHAR(40) NOT NULL,
  `days` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '(DC2Type:tinyint)',
  `end_of_month` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `days_after_end_of_month` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '(DC2Type:tinyint)'
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
