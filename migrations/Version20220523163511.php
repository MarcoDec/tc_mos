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

final class Version20220523163511 extends AbstractMigration {
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
        $this->upComponentFamilies();
        $this->upColors();
        $this->upInvoiceTimeDue();
        $this->upUsers();
    }

    private function upColors(): void {
        $this->addSql('RENAME TABLE `couleur` TO `color`');
        $this->addSql(<<<'SQL'
ALTER TABLE `color`
    ADD `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `name` `name` VARCHAR(20) NOT NULL,
    CHANGE `rgb` `rgb` CHAR(7) NOT NULL COMMENT '(DC2Type:char)',
    DROP `ral`
SQL);
    }

    private function upComponentFamilies(): void {
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
        $this->addSql('RENAME TABLE `invoicetimedue` TO `invoice_time_due`');
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='condition de paiement CLIENT'
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `invoice_time_due_copy` (`deleted`, `name`, `days`, `end_of_month`, `days_after_end_of_month`)
SELECT `deleted`, `name`, `days`, `end_of_month`, `days_after_end_of_month`
FROM `invoice_time_due`
SQL);
        $this->addSql('DROP TABLE `invoice_time_due`');
        $this->addSql('RENAME TABLE `invoice_time_due_copy` TO `invoice_time_due`');
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
    `password` CHAR(60) NOT NULL,
    `username` VARCHAR(20) NOT NULL
)
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
    `token` CHAR(120) NOT NULL,
    CONSTRAINT `token_employee_id_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`)
)
SQL);
    }
}
