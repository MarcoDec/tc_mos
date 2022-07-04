<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Hr\Employee\Employee;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Illuminate\Support\Collection;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class Version20220630075750 extends AbstractMigration {
    private UserPasswordHasherInterface $hasher;

    public function getDescription(): string {
        return 'Migration initiale : transfert selon le nouveau modèle de données.';
    }

    public function postUp(Schema $schema): void {
        $this->upPhoneNumbers('out_trainer', 'tel');
        $this->upPhoneNumbers('society', 'phone');

        $attributes = $this->connection->executeQuery('SELECT `id`, `attribut_id_family` FROM `attribute` WHERE `attribut_id_family` IS NOT NULL');
        /** @var Collection<int, string> $insert */
        $insert = new Collection();
        while ($attribute = $attributes->fetchAssociative()) {
            /** @var array{attribut_id_family:string, id: int} $attribute */
            $insert = $insert->merge(
                collect(explode('#', $attribute['attribut_id_family']))
                    ->map(static fn (string $id): string => "({$attribute['id']}, $id)")
            );
        }
        $this->connection->executeQuery("INSERT INTO `attribute_family` (`attribute_id`, `family_id`) VALUES {$insert->join(',')}");
        $this->connection->executeQuery('ALTER TABLE `attribute` DROP `attribut_id_family`');
        $this->postUpComponentAttributes();
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
        // rank 1
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
        // rank 2
        $this->upAttributes();
        $this->upComponents();
        $this->upProducts();
        $this->upSocieties();
        // rank 3
        $this->upComponentAttributes();
        $this->upManufacturers();
        $this->upNomenclatures();
        // old_id
        $this->addSql('ALTER TABLE `component` DROP `old_id`');
        $this->addSql('ALTER TABLE `component_family` DROP `old_subfamily_id`');
        $this->addSql('ALTER TABLE `invoice_time_due` DROP `old_id`');
        $this->addSql('ALTER TABLE `product` DROP `old_id`');
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

    private function postUpComponentAttributes(): void {
        /** @var Collection<int, array{attribute: int, family: int, unit: string}> $definitions */
        $definitions = collect($this->connection->executeQuery(<<<'SQL'
SELECT `a`.`id` as `attribute`, `f`.`id` as `family`, `u`.`code` as `unit`
FROM `attribute` `a`
LEFT JOIN `unit` `u` ON `a`.`unit_id` = `u`.`id`
INNER JOIN `attribute_family` `af` ON `a`.`id` = `af`.`attribute_id`
INNER JOIN `component_family` `f` ON `af`.`family_id` = `f`.`id`
SQL)->fetchAllAssociative());
        /** @var Collection<int, int> $attributes */
        $attributes = new Collection();
        foreach ($definitions as $definition) {
            $attributes->push($definition['attribute']);
        }
        $attributes = $attributes->unique()->sort();
        if ($attributes->isEmpty()) {
            $this->connection->executeQuery('DELETE FROM `component_attribute`');
        } else {
            $this->connection->executeQuery(
                sql: 'DELETE FROM `component_attribute` WHERE `attribute_id` NOT IN (:attributes)',
                params: ['attributes' => $attributes->all()],
                types: ['attributes' => Connection::PARAM_INT_ARRAY]
            );
        }
        /** @var Collection<int, Collection<int, array{attribute: int, family: int, unit: string}>> $families */
        $families = $definitions->mapToGroups(static fn (array $definition): array => [$definition['family'] => $definition]);
        /** @var array{id: int, family: int, parent: int}[] $components */
        $components = $this->connection->executeQuery(<<<'SQL'
SELECT `c`.`id`, `c`.`family_id` as `family`, `f`.`parent_id` as `parent`
FROM `component` `c`
INNER JOIN `component_family` `f` ON `c`.`family_id` = `f`.`id`
SQL)->fetchAllAssociative();
        foreach ($components as $component) {
            $family = $families->get($component['family']) ?? $families->get($component['parent']);
            if (empty($family)) {
                $this->connection->executeQuery(
                    sql: 'DELETE FROM `component_attribute` WHERE `component_id` = :component',
                    params: ['component' => $component['id']]
                );
            } else {
                /** @var Collection<int, array{attribute_id: int, id: int}> $componentAttributes */
                $componentAttributes = collect($this->connection->executeQuery(
                    sql: 'SELECT `id`, `attribute_id` FROM `component_attribute` WHERE `component_id` = :component',
                    params: ['component' => $component['id']]
                )->fetchAllAssociative());
                /** @var Collection<int, int> $familyAttributes */
                $familyAttributes = new Collection();
                foreach ($family as $attribute) {
                    $compAttr = $componentAttributes->first(static fn (array $compAttr): bool => $compAttr['attribute_id'] === $attribute['attribute']);
                    if (empty($compAttr)) {
                        $this->connection->executeQuery(
                            sql: 'INSERT INTO `component_attribute` (`component_id`, `attribute_id`, `measure_code`) VALUES (:component, :attribute, :unit)',
                            params: ['attribute' => $attribute['attribute'], 'component' => $component['id'], 'unit' => $attribute['unit']]
                        );
                    } else {
                        $this->connection->executeQuery(
                            sql: 'UPDATE `component_attribute` SET `measure_code` = :unit WHERE `id` = :id',
                            params: ['id' => $compAttr['id'], 'unit' => $attribute['unit']]
                        );
                    }
                    $familyAttributes->push($attribute['attribute']);
                }
                $familyAttributes = $familyAttributes->unique()->sort();
                if ($familyAttributes->isEmpty()) {
                    $this->connection->executeQuery(
                        sql: 'DELETE FROM `component_attribute` WHERE `component_id` = :component',
                        params: ['component' => $component['id']]
                    );
                } else {
                    $this->connection->executeQuery(
                        sql: 'DELETE FROM `component_attribute` WHERE `attribute_id` NOT IN (:attributes) AND `component_id` = :component',
                        params: ['attributes' => $familyAttributes->all(), 'component' => $component['id']],
                        types: ['attributes' => Connection::PARAM_INT_ARRAY]
                    );
                }
            }
        }
    }

    private function upAttributes(): void {
        $this->addSql('RENAME TABLE `attribut` TO `attribute`');
        $this->alterTable('attribute', 'Attribut');
        $this->addSql(<<<'SQL'
ALTER TABLE `attribute`
    DROP `isBrokenLinkSolved`,
    CHANGE `description` `description` VARCHAR(255) DEFAULT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `libelle` `name` VARCHAR(255) NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addSql('ALTER TABLE `attribute` ADD CONSTRAINT `IDX_FA7AEFFBF8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`)');
        $this->addSql(<<<'SQL'
CREATE TABLE `attribute_family` (
    `attribute_id` INT UNSIGNED NOT NULL,
    `family_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY(`attribute_id`, `family_id`),
    CONSTRAINT `IDX_87070F01C35E566A` FOREIGN KEY (`family_id`) REFERENCES `component_family` (`id`) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);
        $this->addSql(<<<'SQL'
CREATE TABLE `attribute_copy` (
  `id` int UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `old_id` int UNSIGNED NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_id` int UNSIGNED DEFAULT NULL,
  `attribut_id_family` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Attribut';
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `attribute_copy` (`old_id`, `deleted`, `description`, `name`, `unit_id`, `attribut_id_family`, `type`)
SELECT `id`, `deleted`, `description`, `name`, `unit_id`, `attribut_id_family`, `type`
FROM `attribute`
SQL);
        $this->addSql('DROP TABLE `attribute`');
        $this->addSql('RENAME TABLE `attribute_copy` TO `attribute`');
        $this->addSql('ALTER TABLE `attribute` ADD CONSTRAINT `IDX_FA7AEFFBF8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`);');
        $this->addSql('ALTER TABLE `attribute_family` ADD CONSTRAINT `IDX_87070F01B6E62EFA` FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`id`) ON DELETE CASCADE;');
    }

    private function upCarriers(): void {
        $this->alterTable('carrier', 'Transporteur');
        $this->addSql(<<<'SQL'
ALTER TABLE `carrier`
    ADD `address_address` VARCHAR(80) DEFAULT NULL AFTER `id`,
    ADD `address_address2` VARCHAR(60) DEFAULT NULL AFTER `address_address`,
    ADD `address_city` VARCHAR(50) DEFAULT NULL AFTER `address_address2`,
    ADD `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)' AFTER `address_city`,
    ADD `address_email` VARCHAR(60) DEFAULT NULL AFTER `address_country`,
    ADD `address_phone_number` VARCHAR(18) DEFAULT NULL AFTER `address_email`,
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

    private function upComponentAttributes(): void {
        $this->addSql('RENAME TABLE `component_attribut` TO `component_attribute`');
        $this->alterTable('component_attribute', 'Caractéristique d\'un composant');
        $this->addSql(<<<'SQL'
ALTER TABLE `component_attribute`
    ADD `color_id` INT UNSIGNED DEFAULT NULL,
    ADD `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    ADD `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    ADD `measure_code` VARCHAR(6) DEFAULT NULL,
    ADD `measure_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `measure_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `id_attribut` `attribute_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `id_component` `component_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `valeur_attribut` `value` VARCHAR(255) DEFAULT NULL,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`id`)
SQL);
        $this->addSql(<<<'SQL'
UPDATE `component_attribute` SET
`component_attribute`.`attribute_id` = (SELECT `attribute`.`id` FROM `attribute` WHERE `attribute`.`old_id` = `component_attribute`.`attribute_id`),
`component_attribute`.`component_id` = (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `component_attribute`.`component_id`)
SQL);
        $this->addSql('DELETE FROM `component_attribute` WHERE `attribute_id` IS NULL OR `component_id` IS NULL OR `value` IS NULL');
        $this->addSql(<<<'SQL'
ALTER TABLE `component_attribute`
    CHANGE `attribute_id` `attribute_id` INT UNSIGNED NOT NULL,
    CHANGE `component_id` `component_id` INT UNSIGNED NOT NULL,
    ADD CONSTRAINT `IDX_248373AAB6E62EFA` FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`id`),
    ADD CONSTRAINT `IDX_248373AA7ADA1FB5` FOREIGN KEY (`color_id`) REFERENCES `color` (`id`),
    ADD CONSTRAINT `IDX_248373AAE2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`)
SQL);
    }

    private function upComponentFamilies(): void {
        $this->alterTable('component_family', 'Famille de composant');
        $this->addSql(<<<'SQL'
ALTER TABLE `component_family`
    ADD `old_subfamily_id` INT UNSIGNED DEFAULT NULL,
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
INSERT INTO `component_family` (`old_subfamily_id`, `name`, `deleted`, `parent_id`, `code`)
SELECT
    `component_subfamily`.`id`,
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

    private function upComponents(): void {
        $this->alterTable('component', 'Composant');
        $this->addSql('DELETE FROM `component` WHERE `statut` = 1');
        $this->addSql(<<<'SQL'
ALTER TABLE `component`
    ADD `copper_weight_code` VARCHAR(6) DEFAULT NULL,
    ADD `copper_weight_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    ADD `forecast_volume_code` VARCHAR(6) DEFAULT NULL,
    ADD `forecast_volume_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `parent_id` INT UNSIGNED DEFAULT NULL,
    ADD `ppm_rate` SMALLINT UNSIGNED DEFAULT 10 NOT NULL,
    ADD `min_stock_code` VARCHAR(6) DEFAULT NULL,
    ADD `min_stock_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `weight_code` VARCHAR(6) DEFAULT NULL,
    ADD `weight_denominator` VARCHAR(6) DEFAULT NULL,
    DROP `c_200`,
    DROP `c_300`,
    DROP `c_600`,
    DROP `c_700`,
    DROP `c_800`,
    DROP `date_creation`,
    DROP `date_modification`,
    DROP `flag_error_stock`,
    DROP `id_component_family`,
    DROP `id_society`,
    DROP `id_supplier`,
    DROP `id_supplier2`,
    DROP `id_supplier3`,
    DROP `id_user_creation`,
    DROP `id_user_modification`,
    DROP `newFieldsMarker`,
    DROP `price`,
    DROP `price_type`,
    DROP `qc`,
    DROP `quality`,
    DROP `reach`,
    DROP `reach_attachment`,
    DROP `ref`,
    DROP `rohs`,
    DROP `rohs_attachment`,
    CHANGE `customcode` `customs_code` VARCHAR(16) DEFAULT NULL,
    CHANGE `designation` `name` VARCHAR(255) NOT NULL,
    CHANGE `endOfLife` `end_of_life` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE `fabricant` `manufacturer` VARCHAR(255) DEFAULT NULL,
    CHANGE `fabricant_reference` `manufacturer_code` VARCHAR(255) DEFAULT NULL,
    CHANGE `gestion_stock` `managed_stock` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `indice` `index` VARCHAR(5) NOT NULL,
    CHANGE `info_commande` `order_info` TEXT DEFAULT NULL,
    CHANGE `info_public` `notes` TEXT DEFAULT NULL,
    CHANGE `id_component_subfamily` `family_id` INT UNSIGNED NOT NULL,
    CHANGE `id_componentstatus` `current_place_name` VARCHAR(255) NOT NULL,
    CHANGE `id_unit` `unit_id` INT UNSIGNED NOT NULL,
    CHANGE `need_joint` `need_gasket` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `poid_cu` `copper_weight_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `stock_minimum` `min_stock_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `volume_previsionnel` `forecast_volume_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `weight` `weight_value` DOUBLE PRECISION DEFAULT '0' NOT NULL
SQL);
        $this->addSql(<<<'SQL'
UPDATE `component` `c`
INNER JOIN `component_family` `f` ON `c`.`family_id` = `f`.`old_subfamily_id`
INNER JOIN `unit` `u` ON `c`.`unit_id` = `u`.`id`
SET `c`.`family_id` = `f`.`id`,
`c`.`copper_weight_code` = 'Kg',
`c`.`copper_weight_denominator` = 'km',
`c`.`current_place_name` = CASE
    WHEN `c`.`current_place_name` = 1 THEN 'draft'
    WHEN `c`.`current_place_name` = 2 THEN 'agreed'
    WHEN `c`.`current_place_name` = 3 THEN 'disabled'
    WHEN `c`.`current_place_name` = 4 THEN 'blocked'
    WHEN `c`.`current_place_name` = 5 THEN 'under_exemption'
    ELSE 'draft'
END,
`c`.`forecast_volume_code` = `u`.`code`,
`c`.`min_stock_code` = `u`.`code`,
`c`.`weight_code` = 'g'
SQL);
        $this->addSql(<<<'SQL'
CREATE TABLE `component_copy` (
  `id` int UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `old_id` INT UNSIGNED NOT NULL,
  `copper_weight_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copper_weight_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copper_weight_value` double NOT NULL DEFAULT '0',
  `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `current_place_name` ENUM('agreed', 'blocked', 'disabled', 'draft', 'under_exemption') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:component_current_place)',
  `customs_code` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `end_of_life` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
  `family_id` int UNSIGNED NOT NULL,
  `forecast_volume_code` varchar(6) DEFAULT NULL,
  `forecast_volume_denominator` varchar(6) DEFAULT NULL,
  `forecast_volume_value` double NOT NULL DEFAULT '0',
  `index` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manufacturer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `managed_stock` tinyint(1) NOT NULL DEFAULT '0',
  `min_stock_code` varchar(6) DEFAULT NULL,
  `min_stock_denominator` varchar(6) DEFAULT NULL,
  `min_stock_value` double NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `need_gasket` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `order_info` text COLLATE utf8mb4_unicode_ci,
  `parent_id` int UNSIGNED DEFAULT NULL,
  `ppm_rate` smallint UNSIGNED NOT NULL DEFAULT '10',
  `unit_id` int UNSIGNED NOT NULL,
  `weight_code` varchar(6) DEFAULT NULL,
  `weight_denominator` varchar(6) DEFAULT NULL,
  `weight_value` double NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Composant';
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `component_copy` (
  `old_id`,
  `copper_weight_code`,
  `copper_weight_denominator`,
  `copper_weight_value`,
  `current_place_date`,
  `current_place_name`,
  `customs_code`,
  `deleted`,
  `end_of_life`,
  `family_id`,
  `forecast_volume_code`,
  `forecast_volume_denominator`,
  `forecast_volume_value`,
  `index`,
  `manufacturer`,
  `manufacturer_code`,
  `managed_stock`,
  `min_stock_code`,
  `min_stock_denominator`,
  `min_stock_value`,
  `name`,
  `need_gasket`,
  `notes`,
  `order_info`,
  `parent_id`,
  `ppm_rate`,
  `unit_id`,
  `weight_code`,
  `weight_denominator`,
  `weight_value`
) SELECT
  `id`,
  `copper_weight_code`,
  `copper_weight_denominator`,
  `copper_weight_value`,
  `current_place_date`,
  `current_place_name`,
  `customs_code`,
  `deleted`,
  `end_of_life`,
  `family_id`,
  `forecast_volume_code`,
  `forecast_volume_denominator`,
  `forecast_volume_value`,
  `index`,
  `manufacturer`,
  `manufacturer_code`,
  `managed_stock`,
  `min_stock_code`,
  `min_stock_denominator`,
  `min_stock_value`,
  `name`,
  `need_gasket`,
  `notes`,
  `order_info`,
  `parent_id`,
  `ppm_rate`,
  `unit_id`,
  `weight_code`,
  `weight_denominator`,
  `weight_value`
FROM `component`
SQL);
        $this->addSql('DROP TABLE `component`');
        $this->addSql('RENAME TABLE `component_copy` TO `component`');
        $this->addSql(<<<'SQL'
ALTER TABLE `component`
    ADD CONSTRAINT `IDX_49FEA157C35E566A` FOREIGN KEY (`family_id`) REFERENCES `component_family` (`id`),
    ADD CONSTRAINT `IDX_49FEA157727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `component` (`id`),
    ADD CONSTRAINT `IDX_49FEA157F8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`)
SQL);
    }

    private function upCrons(): void {
        $this->addSql(<<<'SQL'
CREATE TABLE `cron_job` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `command` CHAR(20) NOT NULL COMMENT '(DC2Type:char)',
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
    `active` TINYINT(1) DEFAULT 0 NOT NULL,
    `base` DOUBLE PRECISION DEFAULT '1' NOT NULL,
    `code` CHAR(3) NOT NULL COMMENT '(DC2Type:char)',
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_6956883F727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `currency` (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);
        $currencies = collect(Currencies::getCurrencyCodes())
            ->map(fn (string $code): string => sprintf(
                /** @phpstan-ignore-next-line */
                "(%s, {$this->connection->quote($code)}, %s)",
                $code !== 'EUR' ? 1 : 'NULL',
                in_array($code, ['CHF', 'EUR', 'MDL', 'RUB', 'TND', 'USD', 'VND']) ? 'TRUE' : 'FALSE'
            ));
        /** @var string $parent */
        $parent = $currencies->first(static fn (string $query): bool => str_contains($query, 'EUR'));
        $this->addSql(sprintf(
            'INSERT INTO `currency` (`parent_id`, `code`, `active`) VALUES %s',
            $currencies
                ->filter(static fn (string $query): bool => !str_contains($query, 'EUR'))
                ->prepend($parent)
                ->join(',')
        ));
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
  `old_id` INT UNSIGNED NOT NULL,
  `days` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '(DC2Type:tinyint)',
  `days_after_end_of_month` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '(DC2Type:tinyint)',
  `deleted` TINYINT(1) NOT NULL DEFAULT '0',
  `end_of_month` TINYINT UNSIGNED NOT NULL DEFAULT '0',
  `name` VARCHAR(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE `utf8mb4_unicode_ci` COMMENT='Délai de paiement des factures'
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `invoice_time_due_copy` (`old_id`, `deleted`, `name`, `days`, `end_of_month`, `days_after_end_of_month`)
SELECT `id`, `deleted`, `name`, `days`, `end_of_month`, `days_after_end_of_month`
FROM `invoice_time_due`
SQL);
        $this->addSql('DROP TABLE `invoice_time_due`');
        $this->addSql('RENAME TABLE `invoice_time_due_copy` TO `invoice_time_due`');
    }

    private function upManufacturers(): void {
        $this->addSql('RENAME TABLE `engine_fabricant_ou_contact` TO `manufacturer`');
        $this->alterTable('manufacturer', 'Fabricant');
        $this->addSql(<<<'SQL'
ALTER TABLE `manufacturer`
    ADD `address_country` VARCHAR(255) NOT NULL,
    ADD `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    ADD `society_id` INT UNSIGNED DEFAULT NULL,
    DROP `date_creation`,
    DROP `id_user_creation`,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `nom` `name` VARCHAR(255) DEFAULT NULL
SQL);
        $this->addSql('UPDATE `manufacturer` SET `name` = TRIM(CONCAT(`name`, \' \', `prenom`)) WHERE `prenom` IS NOT NULL');
        $this->addSql(<<<'SQL'
UPDATE `manufacturer`
INNER JOIN `country` ON `manufacturer`.`id_phone_prefix` = `country`.`id`
SET `manufacturer`.`address_country` = UCASE(`country`.`code`)
SQL);
        $this->addSql('ALTER TABLE `society` ADD `manufacturer_id` INT UNSIGNED DEFAULT NULL');
        $this->addSql(<<<'SQL'
INSERT INTO `society` (
    `address_address`,
    `address_city`,
    `address_country`,
    `address_zip_code`,
    `manufacturer_id`,
    `name`,
    `phone`
) SELECT
    `address`,
    `ville`,
    `address_country`,
    `code_postal`,
    `id`,
    `name`,
    `tel`
FROM `manufacturer`
SQL);
        $this->addSql(<<<'SQL'
ALTER TABLE `manufacturer`
    ADD CONSTRAINT `IDX_3D0AE6DCE6389D24` FOREIGN KEY (`society_id`) REFERENCES `society` (`id`),
    DROP `address`,
    DROP `address_country`,
    DROP `id_phone_prefix`,
    DROP `code_postal`,
    DROP `prenom`,
    DROP `ville`,
    DROP `tel`
SQL);
        $this->addSql(<<<'SQL'
UPDATE `manufacturer`
INNER JOIN `society` ON `manufacturer`.`id` = `society`.`manufacturer_id`
SET `manufacturer`.`society_id` = `society`.`id`
SQL);
        $this->addSql('ALTER TABLE `society` DROP `manufacturer_id`');
    }

    private function upNomenclatures(): void {
        $this->addSql('RENAME TABLE `productcontent` TO `nomenclature`');
        $this->alterTable('nomenclature', 'Nomenclature');
        $this->addSql('DELETE FROM `nomenclature` WHERE `statut` = 1');
        $this->addSql(<<<'SQL'
ALTER TABLE `nomenclature`
    ADD `quantity_code` VARCHAR(6) DEFAULT NULL,
    ADD `quantity_denominator` VARCHAR(6) DEFAULT NULL,
    DROP `date_creation`,
    DROP `date_modification`,
    DROP `id_operation`,
    DROP `id_user_creation`,
    DROP `id_user_modification`,
    DROP `isBrokenLinkSolved`,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `id_component` `component_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `id_product` `product_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `mandat` `mandated` TINYINT(1) DEFAULT 1 NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `quantity` `quantity_value` DOUBLE PRECISION DEFAULT '0' NOT NULL
SQL);
        $this->addSql(<<<'SQL'
UPDATE `nomenclature`
SET `nomenclature`.`component_id` = (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `nomenclature`.`component_id`),
`nomenclature`.`product_id` = (SELECT `product`.`id` FROM `product` WHERE `product`.`old_id` = `nomenclature`.`product_id`),
`nomenclature`.`quantity_code` = (
    SELECT `unit`.`code`
    FROM `unit`
    INNER JOIN `component`
        ON `unit`.`id` = `component`.`unit_id`
        AND `component`.`id` = `nomenclature`.`component_id`
)
SQL);
        $this->addSql('DELETE FROM `nomenclature` WHERE `component_id` IS NULL OR `product_id` IS NULL OR `quantity_value` <= 0');
        $this->addSql(<<<'SQL'
ALTER TABLE `nomenclature`
    ADD CONSTRAINT `IDX_799A3652E2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`),
    ADD CONSTRAINT `IDX_799A36524584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
SQL);
        $this->addSql(<<<'SQL'
CREATE TABLE `nomenclature_copy` (
  `id` int UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `product_id` int UNSIGNED NOT NULL,
  `component_id` int UNSIGNED NOT NULL,
  `quantity_value` double NOT NULL DEFAULT '0',
  `mandated` tinyint(1) NOT NULL DEFAULT '1',
  `quantity_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Nomenclature';
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `nomenclature_copy` (`deleted`, `product_id`, `component_id`, `quantity_value`, `mandated`, `quantity_code`, `quantity_denominator`)
SELECT `deleted`, `product_id`, `component_id`, `quantity_value`, `mandated`, `quantity_code`, `quantity_denominator`
FROM `nomenclature`
SQL);
        $this->addSql('DROP TABLE `nomenclature`');
        $this->addSql('RENAME TABLE `nomenclature_copy` TO `nomenclature`');
        $this->addSql(<<<'SQL'
ALTER TABLE `nomenclature`
    ADD CONSTRAINT `IDX_799A3652E2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`),
    ADD CONSTRAINT `IDX_799A36524584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
SQL);
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
    ADD `address_address2` VARCHAR(60) DEFAULT NULL AFTER `address_address`,
    ADD `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)' AFTER `address_city`,
    ADD `address_email` VARCHAR(60) DEFAULT NULL AFTER `address_country`,
    DROP `id_user_creation`,
    DROP `date_creation`,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `prenom` `name` VARCHAR(30) NOT NULL,
    CHANGE `nom` `surname` VARCHAR(30) NOT NULL,
    CHANGE `address` `address_address` VARCHAR(80) DEFAULT NULL,
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
        $this->addSql('ALTER TABLE `out_trainer` DROP `id_phone_prefix`, DROP `society`');
    }

    private function upPhoneNumbers(string $table, string $phoneProp): void {
        $items = $this->connection->executeQuery(<<<SQL
SELECT `id`, `$phoneProp`, `address_country`
FROM `$table`
WHERE `$phoneProp` IS NOT NULL
SQL);
        $util = PhoneNumberUtil::getInstance();
        while ($item = $items->fetchAssociative()) {
            /** @var string[] $item */
            $phone = null;
            try {
                $phone = $util->parse($item[$phoneProp], $item['address_country']);
            } catch (NumberParseException) {
            }
            $this->connection->prepare("UPDATE `$table` SET `$phoneProp` = :phone WHERE `id` = :id")
                ->executeQuery([
                    'id' => $item['id'],
                    'phone' => !empty($phone) && $util->isValidNumber($phone)
                        ? $util->format($phone, PhoneNumberFormat::INTERNATIONAL)
                        : null
                ]);
        }
        $this->connection->executeQuery("ALTER TABLE `$table` CHANGE `$phoneProp` `address_phone_number` VARCHAR(18) DEFAULT NULL");
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
        $this->addSql('UPDATE `product` SET `conditionnement` = 1 WHERE `conditionnement` IS NULL');
        $this->addSql(<<<'SQL'
ALTER TABLE `product`
    ADD `auto_duration_code` VARCHAR(6) DEFAULT NULL,
    ADD `auto_duration_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `costing_manual_duration_code` VARCHAR(6) DEFAULT NULL,
    ADD `costing_manual_duration_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `parent_id` INT UNSIGNED DEFAULT NULL,
    ADD `unit_id` INT UNSIGNED NOT NULL,
    ADD `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    ADD `forecast_volume_code` VARCHAR(6) DEFAULT NULL,
    ADD `forecast_volume_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `manual_duration_code` VARCHAR(6) DEFAULT NULL,
    ADD `manual_duration_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `max_proto_code` VARCHAR(6) DEFAULT NULL,
    ADD `max_proto_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `min_delivery_code` VARCHAR(6) DEFAULT NULL,
    ADD `min_delivery_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `min_prod_code` VARCHAR(6) DEFAULT NULL,
    ADD `min_prod_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `min_stock_code` VARCHAR(6) DEFAULT NULL,
    ADD `min_stock_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `packaging_code` VARCHAR(6) DEFAULT NULL,
    ADD `packaging_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `price_code` VARCHAR(6) DEFAULT NULL,
    ADD `price_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `price_without_copper_code` VARCHAR(6) DEFAULT NULL,
    ADD `price_without_copper_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `production_delay_code` VARCHAR(6) DEFAULT NULL,
    ADD `production_delay_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `transfert_price_supplies_code` VARCHAR(6) DEFAULT NULL,
    ADD `transfert_price_supplies_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `transfert_price_work_code` VARCHAR(6) DEFAULT NULL,
    ADD `transfert_price_work_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `weight_code` VARCHAR(6) DEFAULT NULL,
    ADD `weight_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `costing_auto_duration_code` VARCHAR(6) DEFAULT NULL,
    ADD `costing_auto_duration_denominator` VARCHAR(6) DEFAULT NULL,
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
    CHANGE `date_expiration` `end_of_life` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
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
    CHANGE `ref` `code` VARCHAR(30) NOT NULL,
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
  `old_id` INT UNSIGNED NOT NULL,
  `auto_duration_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_duration_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_duration_value` double NOT NULL DEFAULT '0',
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `costing_auto_duration_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costing_auto_duration_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costing_auto_duration_value` double NOT NULL DEFAULT '0',
  `costing_manual_duration_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costing_manual_duration_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costing_manual_duration_value` double NOT NULL DEFAULT '0',
  `current_place_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)',
  `current_place_name` enum('agreed','blocked','disabled','draft','to_validate','under_exemption') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT '(DC2Type:product_current_place)',
  `customs_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `end_of_life` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
  `family_id` int UNSIGNED NOT NULL,
  `forecast_volume_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forecast_volume_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `forecast_volume_value` double NOT NULL DEFAULT '0',
  `incoterms_id` int UNSIGNED DEFAULT NULL,
  `index` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `internal_index` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT '(DC2Type:tinyint)',
  `kind` enum('EI','Prototype','Série','Pièce de rechange') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Prototype' COMMENT '(DC2Type:product_kind)',
  `managed_copper` tinyint(1) NOT NULL DEFAULT '0',
  `manual_duration_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manual_duration_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manual_duration_value` double NOT NULL DEFAULT '0',
  `max_proto_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_proto_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_proto_value` double NOT NULL DEFAULT '0',
  `min_delivery_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_delivery_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_delivery_value` double NOT NULL DEFAULT '0',
  `min_prod_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_prod_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_prod_value` double NOT NULL DEFAULT '0',
  `min_stock_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_stock_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_stock_value` double NOT NULL DEFAULT '0',
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `packaging_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packaging_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packaging_kind` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `packaging_value` double NOT NULL DEFAULT '0',
  `parent_id` int UNSIGNED DEFAULT NULL,
  `price_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_value` double NOT NULL DEFAULT '0',
  `price_without_copper_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_without_copper_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_without_copper_value` double NOT NULL DEFAULT '0',
  `production_delay_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `production_delay_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `production_delay_value` double NOT NULL DEFAULT '0',
  `transfert_price_supplies_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfert_price_supplies_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfert_price_supplies_value` double NOT NULL DEFAULT '0',
  `transfert_price_work_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfert_price_work_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfert_price_work_value` double NOT NULL DEFAULT '0',
  `unit_id` int UNSIGNED NOT NULL,
  `weight_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_value` double NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Produit';
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `product_copy` (
  `old_id`,
  `auto_duration_code`,
  `auto_duration_denominator`,
  `auto_duration_value`,
  `code`,
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
  `end_of_life`,
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
  `id`,
  `auto_duration_code`,
  `auto_duration_denominator`,
  `auto_duration_value`,
  `code`,
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
  `end_of_life`,
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
        $this->addSql('DELETE FROM `reject_type` WHERE `statut` = 1 OR `libelle` IS NULL');
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

    private function upSocieties(): void {
        $this->alterTable('society', 'Société');
        $this->addSql('DELETE FROM `society` WHERE `statut` = 1');
        $this->addSql('UPDATE `society` SET `ar_enabled` = `ar_enabled` = 1 OR `ar_customer_enabled` = 1');
        $this->addSql('UPDATE `society` SET `indice_cu` = 0 WHERE `indice_cu` IS NULL');
        $this->addSql('UPDATE `society` SET `invoice_minimum` = 0 WHERE `invoice_minimum` IS NULL');
        $this->addSql('UPDATE `society` SET `info_public` = `info_private` WHERE `info_public` IS NULL');
        $this->addSql(<<<'SQL'
UPDATE `society`
SET `info_public` = CONCAT(`info_public`, `info_private`)
WHERE `info_public` IS NOT NULL
AND `info_private` IS NOT NULL
SQL);
        $this->addSql(<<<'SQL'
ALTER TABLE `society`
    ADD `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    ADD `bank_details` VARCHAR(255) DEFAULT NULL,
    ADD `copper_index_code` VARCHAR(6) DEFAULT NULL,
    ADD `copper_index_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `copper_type` ENUM('à la livraison', 'mensuel', 'semestriel') DEFAULT 'mensuel' NOT NULL COMMENT '(DC2Type:copper_type)',
    ADD `invoice_min_code` VARCHAR(6) DEFAULT NULL,
    ADD `invoice_min_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `order_min_code` VARCHAR(6) DEFAULT NULL,
    ADD `order_min_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `ppm_rate` SMALLINT UNSIGNED DEFAULT 10 NOT NULL,
    DROP `ar_customer_enabled`,
    DROP `blocked`,
    DROP `capital`,
    DROP `city_timezone`,
    DROP `comptaPortal`,
    DROP `confidenceCriteria`,
    DROP `currency`,
    DROP `date_creation`,
    DROP `date_modification`,
    DROP `delai_livraison`,
    DROP `deliveryTimeOpenDays`,
    DROP `fax`,
    DROP `generalMargin`,
    DROP `gest_quality`,
    DROP `id_customer_group`,
    DROP `id_locale`,
    DROP `id_soc_gest`,
    DROP `id_soc_gest_customer`,
    DROP `id_societystatus`,
    DROP `id_user_creation`,
    DROP `id_user_modification`,
    DROP `invoicecustomer_by_email`,
    DROP `info_private`,
    DROP `ip`,
    DROP `is_company`,
    DROP `is_customer`,
    DROP `is_secured`,
    DROP `is_supplier`,
    DROP `isBrokenLinkSolved`,
    DROP `logisticPortal`,
    DROP `managementFees`,
    DROP `maxOutstanding`,
    DROP `monthlyOutstanding`,
    DROP `nbBL`,
    DROP `nbInvoice`,
    DROP `numberOfTeamPerDay`,
    DROP `pied_page_achat`,
    DROP `pied_page_vente`,
    DROP `pied_page_achat_en`,
    DROP `pied_page_vente_en`,
    DROP `pied_page_documents`,
    DROP `pied_page_avoir`,
    DROP `pied_page_avoir_en`,
    DROP `quality`,
    DROP `qualityPortal`,
    DROP `rib`,
    DROP `sujet_email_facture`,
    DROP `taux_operation_auto`,
    DROP `taux_operation_auto_md`,
    DROP `taux_operation_manu`,
    DROP `taux_operation_manu_md`,
    DROP `workTimetable`,
    CHANGE `address1` `address_address` VARCHAR(80) DEFAULT NULL,
    CHANGE `address2` `address_address2` VARCHAR(60) DEFAULT NULL,
    CHANGE `ar_enabled` `ar` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `city` `address_city` VARCHAR(50) DEFAULT NULL,
    CHANGE `compte_compta` `accounting_account` VARCHAR(50) DEFAULT NULL,
    CHANGE `email` `address_email` VARCHAR(60) DEFAULT NULL,
    CHANGE `force_tva` `force_vat` VARCHAR(255) DEFAULT NULL,
    CHANGE `formejuridique` `legal_form` VARCHAR(50) DEFAULT NULL,
    CHANGE `id` `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    CHANGE `id_incoterms` `incoterms_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `id_invoicetimedue` `invoice_time_due_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `id_messagetva` `vat_message_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `indice_cu` `copper_index_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `indice_cu_date` `copper_last` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    CHANGE `indice_cu_date_fin` `copper_next` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    CHANGE `indice_cu_enabled` `copper_managed` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `info_public` `notes` TEXT DEFAULT NULL,
    CHANGE `invoice_minimum` `invoice_min_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `nom` `name` VARCHAR(255) DEFAULT NULL,
    CHANGE `order_minimum` `order_min_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `siren` `siren` VARCHAR(50) DEFAULT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `tva` `vat` VARCHAR(255) DEFAULT NULL,
    CHANGE `web` `web` VARCHAR(255) DEFAULT NULL,
    CHANGE `zip` `address_zip_code` VARCHAR(10) DEFAULT NULL
SQL);
        $this->addSql(<<<'SQL'
UPDATE `society`
INNER JOIN `country` ON `society`.`id_country` = `country`.`id`
SET `society`.`address_country` = UCASE(`country`.`code`)
SQL);
        $this->addSql(<<<'SQL'
UPDATE `society`
SET `force_vat` = CASE
    WHEN `force_vat` = 1 THEN 'Force AVEC TVA'
    WHEN `force_vat` = 2 THEN 'Force SANS TVA'
    ELSE 'TVA par défaut selon le pays du client'
END
SQL);
        $this->addSql(<<<'SQL'
UPDATE `society`
SET `society`.`incoterms_id` = NULL
WHERE NOT EXISTS (SELECT * FROM `incoterms` WHERE `incoterms`.`id` = `society`.`incoterms_id`)
SQL);
        $this->addSql(<<<'SQL'
UPDATE `society`
SET `society`.`invoice_time_due_id` = (
    SELECT `invoice_time_due`.`id`
    FROM `invoice_time_due`
    WHERE `invoice_time_due`.`old_id` = `society`.`invoice_time_due_id`
    OR `invoice_time_due`.`old_id` = `society`.`id_invoicetimeduesupplier`
    LIMIT 1
)
SQL);
        $this->addSql(<<<'SQL'
UPDATE `society`
SET `society`.`vat_message_id` = NULL
WHERE NOT EXISTS (SELECT * FROM `vat_message` WHERE `vat_message`.`id` = `society`.`vat_message_id`)
SQL);
        $this->addSql(<<<'SQL'
ALTER TABLE `society`
    ADD CONSTRAINT `IDX_D6461F243D02C80` FOREIGN KEY (`incoterms_id`) REFERENCES `incoterms` (`id`),
    ADD CONSTRAINT `IDX_D6461F2C8D5B586` FOREIGN KEY (`invoice_time_due_id`) REFERENCES `invoice_time_due` (`id`),
    ADD CONSTRAINT `IDX_D6461F26C896AD9` FOREIGN KEY (`vat_message_id`) REFERENCES `vat_message` (`id`),
    CHANGE `force_vat` `force_vat` ENUM('TVA par défaut selon le pays du client', 'Force AVEC TVA', 'Force SANS TVA') DEFAULT 'TVA par défaut selon le pays du client' NOT NULL COMMENT '(DC2Type:vat_message_force)',
    DROP `id_invoicetimeduesupplier`,
    DROP `id_country`
SQL);
        $this->addSql(<<<'SQL'
CREATE TABLE `society_copy` (
  `id` int UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `accounting_account` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_address` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_address2` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_country` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:char)',
  `address_email` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_zip_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ar` tinyint(1) NOT NULL DEFAULT '0',
  `bank_details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copper_index_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copper_index_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copper_index_value` double NOT NULL DEFAULT '0',
  `copper_last` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `copper_managed` tinyint(1) NOT NULL DEFAULT '0',
  `copper_next` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `copper_type` enum('à la livraison','mensuel','semestriel') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mensuel' COMMENT '(DC2Type:copper_type)',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `force_vat` enum('TVA par défaut selon le pays du client','Force AVEC TVA','Force SANS TVA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TVA par défaut selon le pays du client' COMMENT '(DC2Type:vat_message_force)',
  `incoterms_id` int UNSIGNED DEFAULT NULL,
  `invoice_min_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_min_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_min_value` double NOT NULL DEFAULT '0',
  `invoice_time_due_id` int UNSIGNED DEFAULT NULL,
  `legal_form` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `order_min_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_min_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_min_value` double NOT NULL DEFAULT '0',
  `ppm_rate` smallint UNSIGNED NOT NULL DEFAULT '10',
  `siren` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_message_id` int UNSIGNED DEFAULT NULL,
  `web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Société';
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `society_copy` (
    `accounting_account`,
    `address_address`,
    `address_address2`,
    `address_city`,
    `address_country`,
    `address_email`,
    `phone`,
    `address_zip_code`,
    `ar`,
    `bank_details`,
    `copper_index_code`,
    `copper_index_denominator`,
    `copper_index_value`,
    `copper_last`,
    `copper_managed`,
    `copper_next`,
    `copper_type`,
    `deleted`,
    `force_vat`,
    `incoterms_id`,
    `invoice_min_code`,
    `invoice_min_denominator`,
    `invoice_min_value`,
    `invoice_time_due_id`,
    `legal_form`,
    `name`,
    `notes`,
    `order_min_code`,
    `order_min_denominator`,
    `order_min_value`,
    `ppm_rate`,
    `siren`,
    `vat`,
    `vat_message_id`,
    `web`
) SELECT
    `accounting_account`,
    `address_address`,
    `address_address2`,
    `address_city`,
    `address_country`,
    `address_email`,
    `phone`,
    `address_zip_code`,
    `ar`,
    `bank_details`,
    `copper_index_code`,
    `copper_index_denominator`,
    `copper_index_value`,
    `copper_last`,
    `copper_managed`,
    `copper_next`,
    `copper_type`,
    `deleted`,
    `force_vat`,
    `incoterms_id`,
    `invoice_min_code`,
    `invoice_min_denominator`,
    `invoice_min_value`,
    `invoice_time_due_id`,
    `legal_form`,
    `name`,
    `notes`,
    `order_min_code`,
    `order_min_denominator`,
    `order_min_value`,
    `ppm_rate`,
    `siren`,
    `vat`,
    `vat_message_id`,
    `web`
FROM `society`
SQL);
        $this->addSql('DROP TABLE `society`');
        $this->addSql('RENAME TABLE `society_copy` TO `society`');
        $this->addSql(<<<'SQL'
ALTER TABLE `society`
  ADD CONSTRAINT `IDX_D6461F243D02C80` FOREIGN KEY (`incoterms_id`) REFERENCES `incoterms` (`id`),
  ADD CONSTRAINT `IDX_D6461F2C8D5B586` FOREIGN KEY (`invoice_time_due_id`) REFERENCES `invoice_time_due` (`id`),
  ADD CONSTRAINT `IDX_D6461F26C896AD9` FOREIGN KEY (`vat_message_id`) REFERENCES `vat_message` (`id`)
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
    CHANGE `unit_short_lbl` `code` VARCHAR(6) NOT NULL COLLATE `utf8_bin`
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
