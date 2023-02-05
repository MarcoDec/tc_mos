<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Collection;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Entity\Hr\Employee\Employee;
use App\Migrations\Migration;
use Doctrine\DBAL\Schema\Schema;
use InvalidArgumentException;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class Version20221201134808 extends Migration {
    private UserPasswordHasherInterface $hasher;

    public function setHasher(UserPasswordHasherInterface $hasher): void {
        $this->hasher = $hasher;
    }

    public function up(Schema $schema): void {
        $this->addSql(<<<'SQL'
CREATE FUNCTION UCFIRST (s VARCHAR(255))
    RETURNS VARCHAR(255) DETERMINISTIC
    RETURN CONCAT(UCASE(LEFT(s, 1)), LCASE(SUBSTRING(s, 2)))
SQL);
        parent::up($schema);
        $this->addSql('DROP FUNCTION UCFIRST');
    }

    protected function defineQueries(): void {
        // rank 0
        $this->upCountries();
        // rank 1
        $this->upCarriers();
        $this->upColors();
        $this->upComponentFamilies();
        $this->upCronJobs();
        $this->upEmployeeEventTypes();
        $this->upEmployees();
        $this->upEngineGroups();
        $this->upIncoterms();
        $this->upInvoiceTimeDues();
        $this->upOutTrainers();
        $this->upProductFamilies();
        $this->upQualityTypes();
        $this->upRejectTypes();
        $this->upUnits();
        // rank 2
        $this->upAttributes();
        $this->upNotifications();
        $this->upOperationTypes();
        // clean
        $this->push('DROP TABLE `country`');
    }

    private function generateEmployee(string $username): string {
        ($user = new Employee())
            ->setUsername($username)
            ->setPassword($this->hasher->hashPassword($user, 'super'))
            ->addRole(Role::HR_ADMIN)
            ->addRole(Role::LOGISTICS_ADMIN)
            ->addRole(Role::MANAGEMENT_ADMIN)
            ->addRole(Role::PROJECT_ADMIN)
            ->addRole(Role::PRODUCTION_ADMIN)
            ->addRole(Role::PURCHASE_ADMIN)
            ->addRole(Role::QUALITY_ADMIN);
        return sprintf(
            '(%s, %s, %s)',
            $this->platform->quoteStringLiteral((string) $user->getPassword()),
            $this->platform->quoteStringLiteral(implode(',', $user->getRoles())),
            $this->platform->quoteStringLiteral((string) $user->getUsername())
        );
    }

    private function insert(string $table): void {
        $filename = "/var/www/html/TConcept-GPAO/migrations-data/exportjson_table_$table.json";
        $file = file_get_contents($filename);
        if (empty($file)) {
            throw new InvalidArgumentException("$filename not found.");
        }
        /** @var array<int, array<string, bool|float|int|null|string>> $decoded */
        $decoded = json_decode($file, true, 512, JSON_THROW_ON_ERROR);
        $rows = (new Collection($decoded))->map(static fn (array $row): Collection => new Collection($row));
        $this->push(sprintf(
            "INSERT INTO `$table` (%s) VALUES %s",
            $rows->maxKeys()->implode(', '),
            $rows
                ->filter(static fn (Collection $row): bool => $row->has('id') === false || (int) $row->get('id') > 0)
                ->map(
                    fn (Collection $row): string => '('
                        .$row
                            ->map(function (bool|float|int|null|string $column): string {
                                if (is_string($column)) {
                                    $column = trim($column);
                                    if ($column === '' || $column === '0000-00-00 00:00:00') {
                                        $column = null;
                                    }
                                }
                                if ($column === null) {
                                    return 'NULL';
                                }
                                return is_string($column = $this->connection->quote($column)) ? $column : 'NULL';
                            })
                            ->implode(', ')
                        .')'
                )
                ->implode(', ')
        ));
    }

    private function upAttributes(): void {
        $this->push(<<<'SQL'
CREATE TABLE `attribut` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `libelle` VARCHAR(100) NOT NULL,
    `attribut_id_family` VARCHAR(255) DEFAULT NULL,
    `type` VARCHAR(255) DEFAULT NULL,
    `unit_id` INT DEFAULT NULL
)
SQL);
        $this->insert('attribut');
        $this->push(<<<'SQL'
CREATE TABLE `attribute` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `type` ENUM('bool','color','int','percent','text','unit') DEFAULT 'text' NOT NULL COMMENT '(DC2Type:attribute)',
    `unit_id` INT UNSIGNED DEFAULT NULL,
    `attribut_id_family` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_FA7AEFFBF8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`)
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `attribute` (`description`, `name`, `type`, `unit_id`, `attribut_id_family`)
SELECT UCFIRST(`description`), UCFIRST(`libelle`), `type`, `unit_id`, `attribut_id_family`
FROM `attribut`
WHERE `statut` = FALSE
SQL);
        $this->push('DROP TABLE `attribut`');
        $this->push(<<<'SQL'
CREATE TABLE `attribute_family` (
    `attribute_id` INT UNSIGNED NOT NULL,
    `family_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_87070F01B6E62EFA` FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`id`) ON DELETE CASCADE,
    CONSTRAINT `IDX_87070F01C35E566A` FOREIGN KEY (`family_id`) REFERENCES `component_family` (`id`) ON DELETE CASCADE,
    PRIMARY KEY(`attribute_id`, `family_id`)
)
SQL);
    }

    private function upCarriers(): void {
        $this->push(<<<'SQL'
CREATE TABLE `carrier` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `nom` TEXT NOT NULL,
    `date_creation` DATETIME DEFAULT NULL,
    `date_modification` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id_user_creation` INT UNSIGNED DEFAULT NULL,
    `id_user_modification` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('carrier');
        $this->push('RENAME TABLE `carrier` TO `old_carrier`');
        $this->push(<<<'SQL'
CREATE TABLE `carrier` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `address_address` VARCHAR(160) DEFAULT NULL,
    `address_address2` VARCHAR(110) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(80) DEFAULT NULL,
    `address_phone_number` VARCHAR(18) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `name` VARCHAR(50) NOT NULL
)
SQL);
        $this->push('INSERT INTO `carrier` (`name`) SELECT `nom` FROM `old_carrier` WHERE `statut` = FALSE');
        $this->push('DROP TABLE `old_carrier`');
    }

    private function upColors(): void {
        $this->push(<<<'SQL'
CREATE TABLE `couleur` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `ral` VARCHAR(10) DEFAULT NULL,
    `rgb` VARCHAR(7) DEFAULT NULL
)
SQL);
        $this->insert('couleur');
        $this->push(<<<'SQL'
CREATE TABLE `color` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `name` VARCHAR(20) NOT NULL,
    `rgb` CHAR(7) NOT NULL COMMENT '(DC2Type:char)'
)
SQL);
        $this->push('INSERT INTO `color` (`name`, `rgb`) SELECT UCFIRST(`name`), `rgb` FROM `couleur`');
        $this->push('DROP TABLE `couleur`');
    }

    private function upComponentFamilies(): void {
        $this->push(<<<'SQL'
CREATE TABLE `component_family` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `family_name` VARCHAR(25) NOT NULL,
    `copperable` BOOLEAN DEFAULT FALSE NOT NULL,
    `customsCode` VARCHAR(255) DEFAULT NULL,
    `icon` INT UNSIGNED DEFAULT NULL,
    `prefix` VARCHAR(3) DEFAULT NULL
)
SQL);
        $this->insert('component_family');
        $this->push(<<<'SQL'
CREATE TABLE `component_subfamily` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `subfamily_name` VARCHAR(50) NOT NULL,
    `id_family` INT UNSIGNED NOT NULL,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL
)
SQL);
        $this->insert('component_subfamily');
        $this->push(<<<'SQL'
CREATE TABLE `family` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `code` CHAR(3) NOT NULL COMMENT '(DC2Type:char)',
    `copperable` BOOLEAN DEFAULT FALSE NOT NULL,
    `customs_code` VARCHAR(10) DEFAULT NULL,
    `lft` INT DEFAULT NULL,
    `lvl` INT DEFAULT NULL,
    `name` VARCHAR(30) NOT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `rgt` INT DEFAULT NULL,
    `root_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_79FF2A21727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `family` (`id`),
    CONSTRAINT `IDX_79FF2A2179066886` FOREIGN KEY (`root_id`) REFERENCES `family` (`id`)
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `family` (`old_id`, `code`, `copperable`, `customs_code`, `name`)
SELECT
    `id`,
    UCASE(SUBSTR(IFNULL(`prefix`, `family_name`), 1, 3)),
    `copperable`,
    `customsCode`,
    UCFIRST(`family_name`)
FROM `component_family`
WHERE `statut` = FALSE
SQL);
        $this->push('UPDATE `family` SET `root_id` = `id`');
        $this->push(<<<'SQL'
INSERT INTO `family` (`old_id`, `code`, `copperable`, `name`, `parent_id`, `root_id`)
SELECT
    `component_subfamily`.`id`,
    `family`.`code`,
    `family`.`copperable`,
    UCFIRST(`component_subfamily`.`subfamily_name`),
    `family`.`id`,
    `family`.`id`
FROM `component_subfamily`
INNER JOIN `family` ON `component_subfamily`.`id_family` = `family`.`old_id`
WHERE `component_subfamily`.`statut` = FALSE
SQL);
        $this->push('DROP TABLE `component_family`');
        $this->push('DROP TABLE `component_subfamily`');
        $this->push('RENAME TABLE `family` TO `component_family`');
    }

    private function upCountries(): void {
        $this->push(<<<'SQL'
CREATE TABLE `country` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `code` VARCHAR(2) NOT NULL,
    `code_iso` VARCHAR(3) DEFAULT NULL,
    `libelle` TEXT,
    `in_eu` BOOLEAN DEFAULT FALSE NOT NULL,
    `active` BOOLEAN DEFAULT TRUE NOT NULL,
    `phone_prefix` VARCHAR(255) DEFAULT NULL,
    `date_creation` DATETIME DEFAULT NULL,
    `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `id_user_creation` INT UNSIGNED DEFAULT NULL,
    `id_user_modification` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('country');
    }

    private function upCronJobs(): void {
        $this->push(<<<'SQL'
CREATE TABLE `cron_job` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `command` CHAR(20) NOT NULL COMMENT '(DC2Type:char)',
    `last` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `next` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `period` CHAR(6) NOT NULL COMMENT '(DC2Type:char)'
)
SQL);
    }

    private function upEmployeeEventTypes(): void {
        $this->push(<<<'SQL'
CREATE TABLE `employee_eventlist` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `motif` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('employee_eventlist');
        $this->push(<<<'SQL'
CREATE TABLE `employee_event_type` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `name` VARCHAR(30) NOT NULL,
    `to_status` ENUM('agreed','warning') DEFAULT NULL COMMENT '(DC2Type:employee_event)'
)
SQL);
        $this->push('INSERT INTO `employee_event_type` (`name`) SELECT UCFIRST(`motif`) FROM `employee_eventlist`');
        $this->push('DROP TABLE `employee_eventlist`');
    }

    private function upEmployees(): void {
        $this->push(<<<'SQL'
CREATE TABLE `employee` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `password` CHAR(60) DEFAULT NULL COMMENT '(DC2Type:char)',
    `roles` SET('ROLE_HR_ADMIN','ROLE_HR_READER','ROLE_HR_WRITER','ROLE_LOGISTICS_ADMIN','ROLE_LOGISTICS_READER','ROLE_LOGISTICS_WRITER','ROLE_MANAGEMENT_ADMIN','ROLE_PRODUCTION_ADMIN','ROLE_PRODUCTION_READER','ROLE_PRODUCTION_WRITER','ROLE_PROJECT_ADMIN','ROLE_PURCHASE_ADMIN','ROLE_QUALITY_ADMIN','ROLE_QUALITY_READER','ROLE_QUALITY_WRITER','ROLE_USER') NOT NULL COMMENT '(DC2Type:role)',
    `username` VARCHAR(20) DEFAULT NULL
)
SQL);
        $this->push('INSERT INTO `employee` (`password`, `roles`, `username`) VALUES '.implode(',', [
            $this->generateEmployee('super'),
            $this->generateEmployee('super-tn')
        ]));
    }

    private function upEngineGroups(): void {
        $this->push(<<<'SQL'
CREATE TABLE `engine_group` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `libelle` VARCHAR(255) NOT NULL,
    `code` VARCHAR(3) NOT NULL,
    `id_family_group` INT UNSIGNED NOT NULL,
    `organe_securite` BOOLEAN DEFAULT FALSE NOT NULL,
    `formation_specifique` INT NOT NULL DEFAULT 0
)
SQL);
        $this->insert('engine_group');
        $this->push('RENAME TABLE `engine_group` TO `old_engine_group`');
        $this->push(<<<'SQL'
CREATE TABLE `engine_group` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `code` VARCHAR(3) NOT NULL,
    `name` VARCHAR(35) NOT NULL,
    `safety_device` BOOLEAN DEFAULT FALSE NOT NULL,
    `type` ENUM('counter-part','tool','workstation') NOT NULL COMMENT '(DC2Type:engine)'
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `engine_group` (`code`, `name`, `safety_device`, `type`)
SELECT UCASE(`code`), UCFIRST(`libelle`), `organe_securite` != FALSE, IF(`id_family_group` = 1, 'workstation', 'tool')
FROM `old_engine_group`
SQL);
        $this->push('DROP TABLE `old_engine_group`');
    }

    private function upIncoterms(): void {
        $this->push(<<<'SQL'
CREATE TABLE `incoterms` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `code` VARCHAR(30) DEFAULT NULL,
    `label` VARCHAR(60) DEFAULT NULL,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL
)
SQL);
        $this->insert('incoterms');
        $this->push('RENAME TABLE `incoterms` TO `old_incoterms`');
        $this->push(<<<'SQL'
CREATE TABLE `incoterms` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `code` VARCHAR(25) NOT NULL,
    `name` VARCHAR(50) NOT NULL
)
SQL);
        $this->push('INSERT INTO `incoterms` (`code`, `name`) SELECT `code`, `label` FROM `old_incoterms` WHERE `statut` = FALSE');
        $this->push('DROP TABLE `old_incoterms`');
    }

    private function upInvoiceTimeDues(): void {
        $this->push(<<<'SQL'
CREATE TABLE `invoicetimedue` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `libelle` VARCHAR(255) NOT NULL,
    `days` TINYINT UNSIGNED NOT NULL,
    `endofmonth` BOOLEAN NOT NULL,
    `date_creation` DATETIME DEFAULT NULL,
    `date_modification` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id_user_creation` INT UNSIGNED DEFAULT NULL,
    `id_user_modification` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('invoicetimedue');
        $this->push(<<<'SQL'
CREATE TABLE `invoice_time_due` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `days` TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '(DC2Type:tinyint)',
    `days_after_end_of_month` TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '(DC2Type:tinyint)',
    `end_of_month` BOOLEAN DEFAULT FALSE NOT NULL,
    `name` VARCHAR(40) NOT NULL
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `invoice_time_due` (`days`, `end_of_month`, `name`)
SELECT `days`, `endofmonth`, UCFIRST(`libelle`)
FROM `invoicetimedue`
WHERE `statut` = FALSE
SQL);
        $this->push('DROP TABLE `invoicetimedue`');
    }

    private function upNotifications(): void {
        $this->push(<<<'SQL'
CREATE TABLE `notification` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `category` ENUM('default') DEFAULT 'default' NOT NULL COMMENT '(DC2Type:notification)',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `read` TINYINT(1) DEFAULT 0 NOT NULL,
    `subject` VARCHAR(50) NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_BF5476CAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `employee` (`id`)
)
SQL);
    }

    private function upOperationTypes(): void {
        $this->push(<<<'SQL'
CREATE TABLE `operation_type` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `isAssemblage` BOOLEAN DEFAULT FALSE NOT NULL
)
SQL);
        $this->insert('operation_type');
        $this->push(<<<'SQL'
CREATE TABLE `type` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `assembly` BOOLEAN DEFAULT FALSE NOT NULL,
    `name` VARCHAR(40) NOT NULL
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `type` (`old_id`, `assembly`, `name`)
SELECT `id`, `isAssemblage`, UCFIRST(`name`)
FROM `operation_type`
SQL);
        $this->push('DROP TABLE `operation_type`');
        $this->push('RENAME TABLE `type` TO `operation_type`');
        $this->push(<<<'SQL'
CREATE TABLE `operation_type_component_family` (
    `operation_type_id` INT UNSIGNED NOT NULL,
    `component_family_id` INT UNSIGNED NOT NULL
)
SQL);
        $this->insert('operation_type_component_family');
        $this->push(<<<'SQL'
CREATE TABLE `type_family` (
    `family_id` INT UNSIGNED NOT NULL,
    `type_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_58A4AEF9C35E566A` FOREIGN KEY (`family_id`) REFERENCES `component_family` (`id`) ON DELETE CASCADE,
    CONSTRAINT `IDX_86314A63C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `operation_type` (`id`) ON DELETE CASCADE,
    PRIMARY KEY(`type_id`, `family_id`)
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `type_family` (`family_id`, `type_id`)
SELECT `component_family`.`id`, `operation_type`.`id`
FROM `operation_type_component_family`
INNER JOIN `component_family`
    ON `operation_type_component_family`.`operation_type_id` = `component_family`.`old_id`
    AND `component_family`.`parent_id` IS NULL
INNER JOIN `operation_type` ON `operation_type_component_family`.`component_family_id` = `operation_type`.`old_id`
SQL);
        $this->push('DROP TABLE `operation_type_component_family`');
        $this->push('RENAME TABLE `type_family` TO `operation_type_component_family`');
        $this->push('ALTER TABLE `operation_type` DROP `old_id`');
    }

    private function upOutTrainers(): void {
        $this->push(<<<'SQL'
CREATE TABLE `employee_extformateur` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `nom` VARCHAR(255) NOT NULL,
    `prenom` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `code_postal` INT UNSIGNED NOT NULL,
    `ville` VARCHAR(255) NOT NULL,
    `id_phone_prefix` INT UNSIGNED NOT NULL,
    `tel` VARCHAR(255) NOT NULL,
    `society` VARCHAR(255) NOT NULL,
    `id_user_creation` INT UNSIGNED NOT NULL,
    `date_creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)
SQL);
        $this->insert('employee_extformateur');
        $this->push(<<<'SQL'
CREATE TABLE `out_trainer` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `address_address` VARCHAR(160) DEFAULT NULL,
    `address_address2` VARCHAR(110) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(80) DEFAULT NULL,
    `address_phone_number` VARCHAR(18) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `name` VARCHAR(30) NOT NULL,
    `surname` VARCHAR(30) NOT NULL
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `out_trainer` (
    `address_address`,
    `address_city`,
    `address_country`,
    `address_phone_number`,
    `address_zip_code`,
    `name`,
    `surname`
) SELECT
    `employee_extformateur`.`address`,
    `employee_extformateur`.`ville`,
    UCASE(`country`.`code`),
    `employee_extformateur`.`tel`,
    `employee_extformateur`.`code_postal`,
    UCFIRST(`employee_extformateur`.`prenom`),
    UCASE(`employee_extformateur`.`nom`)
FROM `employee_extformateur`
LEFT JOIN `country` ON `employee_extformateur`.`id_phone_prefix` = `country`.`id`
SQL);
        $this->push('DROP TABLE `employee_extformateur`');
    }

    private function upProductFamilies(): void {
        $this->push(<<<'SQL'
CREATE TABLE `product_family` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `customsCode` VARCHAR(255) DEFAULT NULL,
    `family_name` VARCHAR(25) NOT NULL,
    `icon` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('product_family');
        $this->push(<<<'SQL'
CREATE TABLE `product_subfamily` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `subfamily_name` VARCHAR(50) NOT NULL,
    `id_family` INT UNSIGNED UNSIGNED NOT NULL
)
SQL);
        $this->insert('product_subfamily');
        $this->push(<<<'SQL'
CREATE TABLE `family` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `customs_code` VARCHAR(10) DEFAULT NULL,
    `lft` INT DEFAULT NULL,
    `lvl` INT DEFAULT NULL,
    `name` VARCHAR(30) NOT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `rgt` INT DEFAULT NULL,
    `root_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_C79A60FF727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `family` (`id`),
    CONSTRAINT `IDX_C79A60FF79066886` FOREIGN KEY (`root_id`) REFERENCES `family` (`id`)
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `family` (`old_id`, `customs_code`, `name`)
SELECT `id`, `customsCode`, UCFIRST(`family_name`)
FROM `product_family`
WHERE `statut` = FALSE
SQL);
        $this->push('UPDATE `family` SET `root_id` = `id`');
        $this->push(<<<'SQL'
INSERT INTO `family` (`old_id`, `name`, `parent_id`, `root_id`)
SELECT `product_subfamily`.`id`, UCFIRST(`product_subfamily`.`subfamily_name`), `family`.`id`, `family`.`id`
FROM `product_subfamily`
INNER JOIN `family` ON `product_subfamily`.`id_family` = `family`.`old_id`
SQL);
        $this->push('DROP TABLE `product_family`');
        $this->push('DROP TABLE `product_subfamily`');
        $this->push('ALTER TABLE `family` DROP `old_id`');
        $this->push('RENAME TABLE `family` TO `product_family`');
    }

    private function upQualityTypes(): void {
        $this->push(<<<'SQL'
CREATE TABLE `qualitycontrol` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `qualitycontrol` VARCHAR(255) NOT NULL,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL
)
SQL);
        $this->insert('qualitycontrol');
        $this->push(<<<'SQL'
CREATE TABLE `quality_type` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `name` VARCHAR(40) NOT NULL
)
SQL);
        $this->push('INSERT INTO `quality_type` (`name`) SELECT UCFIRST(`qualitycontrol`) FROM `qualitycontrol`');
        $this->push('DROP TABLE `qualitycontrol`');
    }

    private function upRejectTypes(): void {
        $this->push(<<<'SQL'
CREATE TABLE `production_rejectlist` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `libelle` VARCHAR(255) DEFAULT NULL,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_user_creation` INT UNSIGNED NOT NULL,
    `date_creation` DATETIME DEFAULT NULL,
    `id_user_modification` INT UNSIGNED NOT NULL,
    `date_modification` DATETIME DEFAULT NULL
)
SQL);
        $this->insert('production_rejectlist');
        $this->push(<<<'SQL'
CREATE TABLE `reject_type` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `name` VARCHAR(40) NOT NULL
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `reject_type` (`name`)
SELECT UCFIRST(`libelle`)
FROM `production_rejectlist`
WHERE `libelle` IS NOT NULL
SQL);
        $this->push('DROP TABLE `production_rejectlist`');
    }

    private function upUnits(): void {
        $this->push(<<<'SQL'
CREATE TABLE `unit` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `base` DOUBLE PRECISION DEFAULT 1 NOT NULL,
    `unit_short_lbl` VARCHAR(6) NOT NULL,
    `unit_complete_lbl` VARCHAR(50) NOT NULL,
    `parent` INT UNSIGNED DEFAULT NULL,
    `type` ENUM('area','boolean','electrical-resistance','electric-current','length','mass','power','temperature','time','unitary','voltage','volume') NOT NULL COMMENT '(DC2Type:unit)'
)
SQL);
        $this->insert('unit');
        $this->push('RENAME TABLE `unit` TO `old_unit`');
        $this->push(<<<'SQL'
CREATE TABLE `unit` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `base` DOUBLE PRECISION DEFAULT 1 NOT NULL,
    `code` VARCHAR(6) NOT NULL COLLATE `utf8mb3_bin`,
    `lft` INT DEFAULT NULL,
    `lvl` INT DEFAULT NULL,
    `name` VARCHAR(50) NOT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `rgt` INT DEFAULT NULL,
    `root_id` INT UNSIGNED DEFAULT NULL,
    `type` ENUM('area','boolean','currency','electrical-resistance','electric-current','length','mass','power','temperature','time','unitary','voltage','volume') NOT NULL COMMENT '(DC2Type:unit)',
    UNIQUE KEY `UNIQ_DCBB0C5377153098` (`code`),
    UNIQUE KEY `UNIQ_DCBB0C535E237E06` (`name`)
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `unit` (`id`, `base`, `code`, `name`, `parent_id`, `root_id`, `type`)
SELECT `id`, `base`, `unit_short_lbl`, UCFIRST(`unit_complete_lbl`), `parent`, IFNULL(`parent`, `id`), `type`
FROM `old_unit`
SQL);
        $this->push('DROP TABLE `old_unit`');
        $this->push(<<<'SQL'
ALTER TABLE `unit`
    ADD CONSTRAINT `IDX_DCBB0C53727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `unit` (`id`),
    ADD CONSTRAINT `IDX_DCBB0C5379066886` FOREIGN KEY (`root_id`) REFERENCES `unit` (`id`)
SQL);
        $this->push("INSERT INTO `unit` (`id`, `base`, `code`, `name`, `type`) VALUES (22, 1, 'EUR', {$this->platform->quoteStringLiteral(Currencies::getName('EUR'))}, 'currency')");
        $this->push('UPDATE `unit` SET `root_id` = 22 WHERE `id` = 22');
        $this->push(
            'INSERT INTO `unit` (`code`, `name`, `parent_id`, `root_id`, `type`) VALUES '
            .(new Collection(Currencies::getCurrencyCodes()))
                ->filter(static fn (string $code): bool => $code !== 'EUR')
                ->map(fn (string $code): string => "({$this->platform->quoteStringLiteral($code)}, {$this->platform->quoteStringLiteral(Currencies::getName($code))}, 22, 22, 'currency')")
                ->implode(',')
        );
    }
}