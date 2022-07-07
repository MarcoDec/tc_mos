<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Hr\Employee\Employee;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\UnicodeString;

final class Version20220704122123 extends AbstractMigration {
    private UserPasswordHasherInterface $hasher;

    /** @var Collection<int, string> */
    private Collection $queries;

    public function __construct(Connection $connection, LoggerInterface $logger) {
        parent::__construct($connection, $logger);
        $this->queries = new Collection();
    }

    private static function getVersion(): string {
        $matches = [];
        preg_match('/Version\d+/', __CLASS__, $matches);
        return $matches[0];
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
        $this->setProcedure();
        $version = self::getVersion();
        $this->addSql("CREATE PROCEDURE $version() BEGIN {$this->getQueries()}; END");
        $this->addSql("CALL $version");
        $this->addSql("DROP PROCEDURE $version");
        $this->addSql('DROP FUNCTION UCFIRST');
    }

    private function addQuery(string $query): void {
        $this->queries->push(
            (new UnicodeString($query))
                ->replaceMatches('/\s+/', ' ')
                ->replace('( ', '(')
                ->replace(' )', ')')
                ->toString()
        );
    }

    private function getQueries(): string {
        return $this->queries->join('; ');
    }

    /**
     * @param string[] $columns
     */
    private function insert(string $table, array $columns): void {
        $filename = __DIR__."/../migrations-data/exportjson_table_$table.json";
        $file = file_get_contents($filename);
        if (!$file) {
            throw new InvalidArgumentException("$filename not found.");
        }
        /** @var array<int, array<string, mixed>> $decoded */
        $decoded = json_decode($file, true);
        $json = collect($decoded);
        $this->addQuery(sprintf(
            "INSERT INTO `$table` (%s) VALUES %s",
            collect($columns)->map(static fn (string $key): string => "`$key`")->join(','),
            $json
                ->map(function (array $row) use ($columns): string {
                    $mapped = [];
                    foreach ($columns as $column) {
                        $value = $row[$column] ?? null;
                        if (is_string($value)) {
                            $value = trim($value);
                            if (strlen($value) === 0) {
                                $value = null;
                            }
                        }
                        /** @phpstan-ignore-next-line */
                        $mapped[$column] = $value === null || $value === '0000-00-00 00:00:00' || $value === '0000-00-00'
                            ? 'NULL'
                            : $this->connection->quote($value);
                    }
                    return '('.implode(',', $mapped).')';
                })
                ->join(',')
        ));
    }

    private function setProcedure(): void {
        // rank 0
        $this->upCountries();
        $this->upCustomCode();
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
        // clean
        $this->addQuery('DROP TABLE `country`');
        $this->addQuery('DROP TABLE `customcode`');
    }

    private function upAttributes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `attribut` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) DEFAULT 0 NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `libelle` VARCHAR(100) NOT NULL,
    `attribut_id_family` VARCHAR(255) DEFAULT NULL,
    `unit_id` INT UNSIGNED DEFAULT NULL,
    `type` VARCHAR(255) DEFAULT NULL
)
SQL);
        $this->insert('attribut', ['id', 'statut', 'description', 'libelle', 'attribut_id_family', 'unit_id', 'type']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `attribute` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `unit_id` INT UNSIGNED DEFAULT NULL,
    `attribut_id_family` VARCHAR(255) DEFAULT NULL,
    `type` enum('bool', 'color', 'int', 'percent', 'text', 'unit') DEFAULT 'text' NOT NULL COMMENT '(DC2Type:attribute)',
    CONSTRAINT `IDX_FA7AEFFBF8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `attribute` (`old_id`, `deleted`, `description`, `name`, `unit_id`, `attribut_id_family`, `type`)
SELECT `id`, `statut`, `description`, `libelle`, `unit_id`, `attribut_id_family`, `type`
FROM `attribut`
SQL);
        $this->addQuery('DROP TABLE `attribut`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `attribute_family` (
    `attribute_id` INT UNSIGNED NOT NULL,
    `family_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY(`attribute_id`, `family_id`),
    CONSTRAINT `IDX_87070F01B6E62EFA` FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`id`) ON DELETE CASCADE,
    CONSTRAINT `IDX_87070F01C35E566A` FOREIGN KEY (`family_id`) REFERENCES `component_family` (`id`) ON DELETE CASCADE
)
SQL);
        $this->addQuery(<<<'SQL'
SET @attribute_i = 0;
SELECT COUNT(*) INTO @attribute_count FROM `attribute` WHERE `attribut_id_family` IS NOT NULL;
WHILE @attribute_i < @attribute_count DO
    SET @attribute_sql = CONCAT(
        'SELECT `id`, `attribut_id_family` ',
        'INTO @attribute_id, @attribute_families ',
        'FROM `attribute` ',
        'WHERE `attribut_id_family` IS NOT NULL ',
        'LIMIT 1 OFFSET ',
        @attribute_i
    );
    PREPARE attribute_stmt FROM @attribute_sql;
    EXECUTE attribute_stmt;
    WHILE LENGTH(@attribute_families) > 0 DO
        SET @attribute_family = SUBSTRING(@attribute_families, 1, 1);
        IF LENGTH(@attribute_families) >= 3 THEN
            SET @attribute_families = SUBSTRING(@attribute_families, 3);
        ELSE
            SET @attribute_families = '';
        END IF;
        INSERT INTO `attribute_family` (`attribute_id`, `family_id`) VALUES (@attribute_id, @attribute_family);
    END WHILE;
    SET @attribute_i = @attribute_i + 1;
END WHILE
SQL);
        $this->addQuery('ALTER TABLE `attribute` DROP `attribut_id_family`');
    }

    private function upCarriers(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `carrier` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) NOT NULL,
    `address_address` VARCHAR(80) DEFAULT NULL,
    `address_address2` VARCHAR(60) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(60) DEFAULT NULL,
    `address_phone_number` VARCHAR(18) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `nom` TEXT NOT NULL
)
SQL);
        $this->insert('carrier', ['id', 'statut', 'nom']);
        $this->addQuery(<<<'SQL'
ALTER TABLE `carrier`
    CHANGE `nom` `name` VARCHAR(50) NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
    }

    private function upColors(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `couleur` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `name` VARCHAR(20) NOT NULL,
    `rgb` CHAR(7) NOT NULL COMMENT '(DC2Type:char)'
)
SQL);
        $this->insert('couleur', ['id', 'name', 'rgb']);
        $this->addQuery('UPDATE `couleur` SET `name` = UCFIRST(`name`)');
        $this->addQuery('RENAME TABLE `couleur` TO `color`');
    }

    private function upComponentAttributes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `component_attribut` (
    `id_component` INT NOT NULL,
    `id_attribut` INT NOT NULL,
    `valeur_attribut` VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY(`id_component`, `id_attribut`)
)
SQL);
        $this->insert('component_attribut', ['id_component', 'id_attribut', 'valeur_attribut']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `component_attribute` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `component_id` INT UNSIGNED NOT NULL,
    `attribute_id` INT UNSIGNED NOT NULL,
    `value` VARCHAR(255) DEFAULT NULL,
    `color_id` INT UNSIGNED DEFAULT NULL,
    `measure_code` VARCHAR(6) DEFAULT NULL,
    `measure_denominator` VARCHAR(6) DEFAULT NULL,
    `measure_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    UNIQUE KEY `UNIQ_248373AAB6E62EFAE2ABAFFF` (`attribute_id`, `component_id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `component_attribute` (`component_id`, `attribute_id`, `value`)
SELECT
    (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `component_attribut`.`id_component`),
    (SELECT `attribute`.`id` FROM `attribute` WHERE `attribute`.`old_id` = `component_attribut`.`id_attribut`),
    `valeur_attribut`
FROM `component_attribut`
WHERE `valeur_attribut` IS NOT NULL AND TRIM(`valeur_attribut`) != ''
AND EXISTS (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `component_attribut`.`id_component`)
AND EXISTS (SELECT `attribute`.`id` FROM `attribute` WHERE `attribute`.`old_id` = `component_attribut`.`id_attribut`)
SQL);
        $this->addQuery('DROP TABLE `component_attribut`');
        $this->addQuery(<<<'SQL'
INSERT INTO `component_attribute` (`component_id`, `attribute_id`, `measure_code`)
SELECT `c`.`id`, `a`.`id`, `u`.`code`
FROM `component` `c`
INNER JOIN `component_family` `f` ON `c`.`family_id` = `f`.`id`
INNER JOIN `attribute_family` `af` ON `f`.`parent_id` = `af`.`family_id`
INNER JOIN `attribute` `a` ON `af`.`attribute_id` = `a`.`id`
LEFT JOIN `unit` `u` ON `a`.`unit_id` = `u`.`id`
ON DUPLICATE KEY UPDATE `measure_code` = `u`.`code`
SQL);
        $this->addQuery(<<<'SQL'
DELETE `ca`
FROM `component_attribute` `ca`
LEFT JOIN `component` `c` ON `ca`.`component_id` = `c`.`id`
LEFT JOIN `component_family` `f` ON `c`.`family_id` = `f`.`id`
LEFT JOIN `attribute` `a` ON `ca`.`attribute_id` = `a`.`id`
LEFT JOIN `attribute_family` `af` ON `a`.`id` = `af`.`attribute_id` AND `f`.`parent_id` = `af`.`family_id`
WHERE `af`.`attribute_id` IS NULL OR `af`.`family_id` IS NULL
SQL);
    }

    private function upComponentFamilies(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `component_family` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) NOT NULL DEFAULT '0',
    `prefix` VARCHAR(3) DEFAULT NULL,
    `copperable` TINYINT(1) NOT NULL DEFAULT '0',
    `customsCode` VARCHAR(255) DEFAULT NULL,
    `family_name` VARCHAR(25) NOT NULL,
    `old_subfamily_id` INT UNSIGNED DEFAULT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('component_family', ['id', 'statut', 'prefix', 'customsCode', 'family_name']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `component_subfamily` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `subfamily_name` VARCHAR(50) NOT NULL,
    `id_family` TINYINT(3) UNSIGNED NOT NULL
)
SQL);
        $this->insert('component_subfamily', ['id', 'subfamily_name', 'id_family']);
        $this->addQuery(<<<'SQL'
INSERT INTO `component_family` (`family_name`, `old_subfamily_id`, `parent_id`)
SELECT
    `component_subfamily`.`subfamily_name`,
    `component_subfamily`.`id`,
    `component_subfamily`.`id_family`
FROM `component_subfamily`
INNER JOIN `component_family` ON `component_subfamily`.`id_family` = `component_family`.`id`
SQL);
        $this->addQuery('DROP TABLE `component_subfamily`');
        $this->addQuery(<<<'SQL'
UPDATE `component_family` `f`
LEFT JOIN `component_family` `parent` ON `f`.`parent_id` = `parent`.`id`
SET `f`.`prefix` = IF(`f`.`parent_id` IS NULL, UPPER(SUBSTR(`f`.`family_name`, 1, 3)), UPPER(SUBSTR(`parent`.`family_name`, 1, 3))),
`f`.`family_name` = UCFIRST(`f`.`family_name`)
SQL);
        $this->addQuery(<<<'SQL'
ALTER TABLE `component_family`
    CHANGE `customsCode` `customs_code` VARCHAR(10) DEFAULT NULL,
    CHANGE `family_name` `name` VARCHAR(40) NOT NULL,
    CHANGE `prefix` `code` CHAR(3) NOT NULL COMMENT '(DC2Type:char)',
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    ADD CONSTRAINT `IDX_79FF2A21727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `component_family` (`id`)
SQL);
    }

    private function upComponents(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `component` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) DEFAULT 0 NOT NULL,
    `poid_cu` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `id_componentstatus` INT NOT NULL DEFAULT '1',
    `customcode` VARCHAR(16) DEFAULT NULL,
    `endOfLife` DATE DEFAULT NULL,
    `id_component_subfamily` INT UNSIGNED NOT NULL,
    `volume_previsionnel` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `indice` VARCHAR(5) NOT NULL DEFAULT '0',
    `gestion_stock` TINYINT(1) DEFAULT 0 NOT NULL,
    `fabricant` VARCHAR(255) DEFAULT NULL,
    `fabricant_reference` VARCHAR(255) DEFAULT NULL,
    `stock_minimum` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `designation` VARCHAR(255) NOT NULL,
    `need_joint` TINYINT(1) DEFAULT 0 NOT NULL,
    `info_public` TEXT DEFAULT NULL,
    `info_commande` TEXT DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    `id_unit` INT UNSIGNED NOT NULL,
    `weight` DOUBLE PRECISION DEFAULT '0' NOT NULL
)
SQL);
        $this->insert('component', [
            'id',
            'statut',
            'poid_cu',
            'id_componentstatus',
            'customcode',
            'endOfLife',
            'id_component_subfamily',
            'volume_previsionnel',
            'gestion_stock',
            'fabricant',
            'fabricant_reference',
            'stock_minimum',
            'designation',
            'need_joint',
            'info_public',
            'info_commande',
            'ref',
            'id_unit',
            'weight'
        ]);
        $this->addQuery('RENAME TABLE `component` TO `component_old`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `component` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `copper_weight_code` VARCHAR(6) DEFAULT NULL,
    `copper_weight_denominator` VARCHAR(6) DEFAULT NULL,
    `copper_weight_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` ENUM('agreed', 'blocked', 'disabled', 'draft', 'under_exemption') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:component_current_place)',
    `customs_code` VARCHAR(16) DEFAULT NULL,
    `end_of_life` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `family_id` INT UNSIGNED NOT NULL,
    `forecast_volume_code` VARCHAR(6) DEFAULT NULL,
    `forecast_volume_denominator` VARCHAR(6) DEFAULT NULL,
    `forecast_volume_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `index` VARCHAR(5) NOT NULL DEFAULT '0',
    `manufacturer` VARCHAR(255) DEFAULT NULL,
    `manufacturer_code` VARCHAR(255) DEFAULT NULL,
    `managed_stock` TINYINT(1) DEFAULT 0 NOT NULL,
    `min_stock_code` VARCHAR(6) DEFAULT NULL,
    `min_stock_denominator` VARCHAR(6) DEFAULT NULL,
    `min_stock_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `need_gasket` TINYINT(1) DEFAULT 0 NOT NULL,
    `notes` TEXT,
    `order_info` TEXT,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `ppm_rate` SMALLINT UNSIGNED NOT NULL DEFAULT '10',
    `unit_id` INT UNSIGNED NOT NULL,
    `weight_code` VARCHAR(6) DEFAULT NULL,
    `weight_denominator` VARCHAR(6) DEFAULT NULL,
    `weight_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CONSTRAINT `IDX_49FEA157C35E566A` FOREIGN KEY (`family_id`) REFERENCES `component_family` (`id`),
    CONSTRAINT `IDX_49FEA157727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `component` (`id`),
    CONSTRAINT `IDX_49FEA157F8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `component` (
    `old_id`,
    `copper_weight_code`,
    `copper_weight_denominator`,
    `copper_weight_value`,
    `current_place_name`,
    `customs_code`,
    `end_of_life`,
    `family_id`,
    `forecast_volume_code`,
    `forecast_volume_value`,
    `index`,
    `manufacturer`,
    `manufacturer_code`,
    `managed_stock`,
    `min_stock_code`,
    `min_stock_value`,
    `name`,
    `need_gasket`,
    `notes`,
    `order_info`,
    `unit_id`,
    `weight_code`,
    `weight_value`
) SELECT
    `component_old`.`id`,
    'Kg',
    'km',
    `component_old`.`poid_cu`,
    CASE
        WHEN `component_old`.`id_componentstatus` = 1 THEN 'draft'
        WHEN `component_old`.`id_componentstatus` = 2 THEN 'agreed'
        WHEN `component_old`.`id_componentstatus` = 3 THEN 'disabled'
        WHEN `component_old`.`id_componentstatus` = 4 THEN 'blocked'
        WHEN `component_old`.`id_componentstatus` = 5 THEN 'under_exemption'
        ELSE 'draft'
    END,
    `component_old`.`customcode`,
    `component_old`.`endOfLife`,
    `component_family`.`id`,
    `unit`.`code`,
    `component_old`.`volume_previsionnel`,
    `component_old`.`indice`,
    `component_old`.`fabricant`,
    `component_old`.`fabricant_reference`,
    `component_old`.`gestion_stock`,
    `unit`.`code`,
    `component_old`.`stock_minimum`,
    `component_old`.`designation`,
    `component_old`.`need_joint`,
    `component_old`.`info_public`,
    `component_old`.`info_commande`,
    `component_old`.`id_unit`,
    'g',
    `component_old`.`weight`
FROM `component_old`
INNER JOIN `component_family` ON `component_old`.`id_component_subfamily` = `component_family`.`old_subfamily_id`
INNER JOIN `unit` ON `component_old`.`id_unit` = `unit`.`id`
WHERE `component_old`.`statut` = 1
SQL);
        $this->addQuery('DROP TABLE `component_old`');
    }

    private function upCountries(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `country` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) NOT NULL,
    `code` VARCHAR(2) NOT NULL,
    `phone_prefix` VARCHAR(255) DEFAULT NULL
)
SQL);
        $this->insert('country', ['id', 'statut', 'code', 'phone_prefix']);
    }

    private function upCrons(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `cron_job` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `command` CHAR(20) NOT NULL COMMENT '(DC2Type:char)',
    `last` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `next` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `period` CHAR(6) NOT NULL COMMENT '(DC2Type:char)'
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);
    }

    private function upCurrencies(): void {
        $this->addQuery(<<<'SQL'
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
        $this->addQuery(sprintf(
            'INSERT INTO `currency` (`parent_id`, `code`, `active`) VALUES %s',
            $currencies
                ->filter(static fn (string $query): bool => !str_contains($query, 'EUR'))
                ->prepend($parent)
                ->join(',')
        ));
    }

    private function upCustomCode(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `customcode` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) NOT NULL,
    `code` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('customcode', ['id', 'statut', 'code']);
    }

    private function upEngineGroups(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine_group` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `code` VARCHAR(3) NOT NULL,
    `libelle` VARCHAR(35) NOT NULL,
    `id_family_group` INT NOT NULL,
    `organe_securite` TINYINT(1) NOT NULL DEFAULT '0',
    `type` ENUM('counter-part', 'tool', 'workstation') NOT NULL COMMENT '(DC2Type:engine_type)'
)
SQL);
        $this->insert('engine_group', ['id', 'code', 'libelle', 'id_family_group', 'organe_securite']);
        $this->addQuery(<<<'SQL'
UPDATE `engine_group` SET
    `libelle` = UCFIRST(`libelle`),
    `type` = IF(`id_family_group` = 1, 'workstation', 'tool')
SQL);
        $this->addQuery(<<<'SQL'
ALTER TABLE `engine_group`
    DROP `id_family_group`,
    CHANGE `libelle` `name` VARCHAR(35) NOT NULL,
    CHANGE `organe_securite` `safety_device` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
    }

    private function upEventTypes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee_eventlist` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `motif` VARCHAR(30) NOT NULL,
    `to_status` ENUM('blocked', 'disabled', 'enabled', 'warning') DEFAULT NULL COMMENT '(DC2Type:employee_current_place)'
)
SQL);
        $this->insert('employee_eventlist', ['id', 'motif']);
        $this->addQuery('ALTER TABLE `employee_eventlist` CHANGE `motif` `name` VARCHAR(30) NOT NULL');
        $this->addQuery('UPDATE `employee_eventlist` SET `name` = UCFIRST(`name`)');
        $this->addQuery('RENAME TABLE `employee_eventlist` TO `event_type`');
    }

    private function upIncoterms(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `incoterms` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) DEFAULT 0 NOT NULL,
    `code` varchar(11) NOT NULL,
    `label` varchar(50) DEFAULT NULL
)
SQL);
        $this->insert('incoterms', ['id', 'statut', 'code', 'label']);
        $this->addQuery(<<<'SQL'
ALTER TABLE `incoterms`
    CHANGE `label` `name` VARCHAR(50) NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addQuery('UPDATE `incoterms` SET `name` = UCFIRST(`name`)');
    }

    private function upInvoiceTimeDue(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `invoicetimedue` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_old_invoicetimedue` INT UNSIGNED DEFAULT NULL,
    `id_old_invoicetimeduesupplier` INT UNSIGNED DEFAULT NULL,
    `statut` TINYINT(1) NOT NULL,
    `days` TINYINT UNSIGNED DEFAULT '0' NOT NULL COMMENT '(DC2Type:tinyint)',
    `endofmonth` TINYINT UNSIGNED DEFAULT '0' NOT NULL COMMENT '(DC2Type:tinyint)',
    `libelle` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('invoicetimedue', ['id', 'statut', 'days', 'endofmonth', 'libelle']);
        $this->addQuery('UPDATE `invoicetimedue` SET `id_old_invoicetimedue` = `id`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `invoicetimeduesupplier` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) NOT NULL,
    `days` TINYINT UNSIGNED DEFAULT '0' NOT NULL COMMENT '(DC2Type:tinyint)',
    `endofmonth` TINYINT UNSIGNED DEFAULT '0' NOT NULL COMMENT '(DC2Type:tinyint)',
    `libelle` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('invoicetimeduesupplier', ['id', 'statut', 'days', 'endofmonth', 'libelle']);
        $this->addQuery(<<<'SQL'
INSERT INTO `invoicetimedue` (`id_old_invoicetimeduesupplier`, `statut`, `libelle`, `days`, `endofmonth`)
SELECT `id`, `statut`, `libelle`, `days`, `endofmonth`
FROM `invoicetimeduesupplier`
SQL);
        $this->addQuery(<<<'SQL'
DELETE `i1`
FROM `invoicetimedue` as `i1`, `invoicetimedue` as `i2`
WHERE `i1`.`id` > `i2`.`id`
AND `i1`.`libelle` = `i2`.`libelle`
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `invoice_time_due` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_old_invoicetimedue` INT UNSIGNED DEFAULT NULL,
    `id_old_invoicetimeduesupplier` INT UNSIGNED DEFAULT NULL,
    `deleted` TINYINT(1) NOT NULL DEFAULT '0',
    `days` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '(DC2Type:tinyint)',
    `days_after_end_of_month` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '(DC2Type:tinyint)',
    `end_of_month` TINYINT UNSIGNED NOT NULL DEFAULT '0',
    `name` VARCHAR(40) NOT NULL
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `invoice_time_due` (`id_old_invoicetimedue`, `id_old_invoicetimeduesupplier`, `deleted`, `days`, `end_of_month`, `name`)
SELECT `id_old_invoicetimedue`, `id_old_invoicetimeduesupplier`, `statut`, `days`, `endofmonth`, UCFIRST(`libelle`)
FROM `invoicetimedue`
SQL);
        $this->addQuery('DROP TABLE `invoicetimedue`');
        $this->addQuery('DROP TABLE `invoicetimeduesupplier`');
    }

    private function upManufacturers(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine_fabricant_ou_contact` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `nom` VARCHAR(255) DEFAULT NULL,
    `prenom` VARCHAR(255) DEFAULT NULL,
    `address` VARCHAR(255) DEFAULT NULL,
    `code_postal` INT DEFAULT NULL,
    `ville` VARCHAR(255) DEFAULT NULL,
    `id_phone_prefix` INT DEFAULT NULL,
    `tel` VARCHAR(255) DEFAULT NULL,
    `society_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_3D0AE6DCE6389D24` FOREIGN KEY (`society_id`) REFERENCES `society` (`id`)
)
SQL);
        $this->insert('engine_fabricant_ou_contact', [
            'id',
            'nom',
            'prenom',
            'address',
            'code_postal',
            'ville',
            'id_phone_prefix',
            'tel'
        ]);
        $this->addQuery('ALTER TABLE `society` ADD `manufacturer_id` INT UNSIGNED DEFAULT NULL');
        $this->addQuery(<<<'SQL'
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
    (SELECT UCASE(`country`.`code`) FROM `country` WHERE `country`.`id` = `engine_fabricant_ou_contact`.`id_phone_prefix`),
    `code_postal`,
    `id`,
    IF(`prenom` IS NULL, TRIM(`nom`), TRIM(CONCAT(`nom`, ' ', `prenom`))),
    `tel`
FROM `engine_fabricant_ou_contact`
SQL);
        $this->addQuery(<<<'SQL'
ALTER TABLE `engine_fabricant_ou_contact`
    DROP `address`,
    DROP `id_phone_prefix`,
    DROP `code_postal`,
    DROP `prenom`,
    DROP `ville`,
    DROP `tel`,
    CHANGE `nom` `name` VARCHAR(255) DEFAULT NULL
SQL);
        $this->addQuery(<<<'SQL'
UPDATE `engine_fabricant_ou_contact`
INNER JOIN `society` ON `engine_fabricant_ou_contact`.`id` = `society`.`manufacturer_id`
SET `engine_fabricant_ou_contact`.`society_id` = `society`.`id`
SQL);
        $this->addQuery('ALTER TABLE `society` DROP `manufacturer_id`');
        $this->addQuery('RENAME TABLE `engine_fabricant_ou_contact` TO `manufacturer`');
    }

    private function upNomenclatures(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `productcontent` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) DEFAULT 0 NOT NULL,
    `id_product` INT UNSIGNED DEFAULT NULL,
    `id_component` INT UNSIGNED DEFAULT NULL,
    `quantity` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `mandat` TINYINT(1) DEFAULT 0 NOT NULL
)
SQL);
        $this->insert('productcontent', ['id', 'statut', 'id_product', 'id_component', 'quantity', 'mandat']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `nomenclature` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `component_id` INT UNSIGNED NOT NULL,
    `quantity_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `mandated` TINYINT(1) DEFAULT 1 NOT NULL,
    `quantity_code` VARCHAR(6) DEFAULT NULL,
    `quantity_denominator` VARCHAR(6) DEFAULT NULL,
    CONSTRAINT `IDX_799A3652E2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`),
    CONSTRAINT `IDX_799A36524584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `nomenclature` (`product_id`, `component_id`, `quantity_value`, `mandated`, `quantity_code`)
SELECT
    (SELECT `product`.`id` FROM `product` WHERE `product`.`old_id` = `productcontent`.`id_product`),
    (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `productcontent`.`id_component`),
    `quantity`,
    `mandat`,
    (SELECT `unit`.`code` FROM `unit` INNER JOIN `component` ON `unit`.`id` = `component`.`unit_id` AND `component`.`id` = `productcontent`.`id_component`)
FROM `productcontent`
WHERE `statut` = 0
AND `quantity` IS NOT NULL AND TRIM(`quantity`) != '' AND `quantity` > 0
AND EXISTS (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `productcontent`.`id_component`)
AND EXISTS (SELECT `product`.`id` FROM `product` WHERE `product`.`old_id` = `productcontent`.`id_product`)
SQL);
        $this->addQuery('DROP TABLE `productcontent`');
    }

    private function upNotifications(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `notification` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `category` ENUM('default') DEFAULT 'default' NOT NULL COMMENT '(DC2Type:notification_category)',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `read` TINYINT(1) DEFAULT 0 NOT NULL,
    `subject` VARCHAR(50) DEFAULT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_BF5476CAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `employee` (`id`)
)
SQL);
    }

    private function upOutTrainers(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee_extformateur` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `address` VARCHAR(80) NOT NULL,
    `address_address2` VARCHAR(60) DEFAULT NULL,
    `ville` VARCHAR(50) NOT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(60) DEFAULT NULL,
    `code_postal` INT NOT NULL,
    `prenom` VARCHAR(30) NOT NULL,
    `nom` VARCHAR(30) NOT NULL,
    `id_phone_prefix` INT NOT NULL,
    `tel` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('employee_extformateur', ['id', 'address', 'ville', 'code_postal', 'prenom', 'nom', 'id_phone_prefix', 'tel']);
        $this->addQuery(<<<'SQL'
ALTER TABLE `employee_extformateur`
    CHANGE `prenom` `name` VARCHAR(30) NOT NULL,
    CHANGE `nom` `surname` VARCHAR(30) NOT NULL,
    CHANGE `address` `address_address` VARCHAR(80) DEFAULT NULL,
    CHANGE `ville` `address_city` VARCHAR(50) DEFAULT NULL,
    CHANGE `code_postal` `address_zip_code` VARCHAR(10) DEFAULT NULL
SQL);
        $this->addQuery(<<<'SQL'
UPDATE `employee_extformateur`
INNER JOIN `country` ON `employee_extformateur`.`id_phone_prefix` = `country`.`id`
SET `employee_extformateur`.`tel` = CONCAT(`country`.`phone_prefix`, `employee_extformateur`.`tel`),
`employee_extformateur`.`address_country` = UCASE(`country`.`code`),
`employee_extformateur`.`surname` = UCASE(`employee_extformateur`.`surname`),
`employee_extformateur`.`name` = UCFIRST(`employee_extformateur`.`name`)
SQL);
        $this->addQuery('RENAME TABLE `employee_extformateur` TO `out_trainer`');
    }

    private function upProductFamilies(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `product_family` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) DEFAULT 0 NOT NULL,
    `customsCode` VARCHAR(10) DEFAULT NULL,
    `family_name` VARCHAR(30) NOT NULL,
    `old_subfamily_id` INT UNSIGNED DEFAULT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_C79A60FF727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `product_family` (`id`)
)
SQL);
        $this->insert('product_family', ['id', 'statut', 'customsCode', 'family_name']);
        $this->addQuery(<<<'SQL'
ALTER TABLE `product_family`
    CHANGE `customsCode` `customs_code` VARCHAR(10) DEFAULT NULL,
    CHANGE `family_name` `name` VARCHAR(30) NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `product_subfamily` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `subfamily_name` VARCHAR(30) NOT NULL,
  `id_family` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('product_subfamily', ['id', 'subfamily_name', 'id_family']);
        $this->addQuery(<<<'SQL'
INSERT INTO `product_family` (`name`, `old_subfamily_id`, `parent_id`)
SELECT `subfamily_name`, `id`, `id_family`
FROM `product_subfamily`
SQL);
        $this->addQuery('DROP TABLE `product_subfamily`');
        $this->addQuery('UPDATE `product_family` SET `name` = UCFIRST(`name`)');
    }

    private function upProducts(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `product` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) DEFAULT 0 NOT NULL,
    `temps_auto` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `ref` VARCHAR(255) NOT NULL,
    `tps_chiff_auto` DOUBLE PRECISION DEFAULT NULL,
    `tps_chiff_manu` DOUBLE PRECISION DEFAULT NULL,
    `id_productstatus` INT NOT NULL DEFAULT '1',
    `date_expiration` DATE DEFAULT NULL,
    `id_product_subfamily` INT UNSIGNED NOT NULL,
    `volume_previsionnel` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `id_customcode` INT UNSIGNED NOT NULL,
    `id_product_child` INT UNSIGNED NOT NULL,
    `id_incoterms` INT UNSIGNED NOT NULL,
    `indice` VARCHAR(3) NOT NULL,
    `indice_interne` TINYINT UNSIGNED DEFAULT '1' NOT NULL,
    `is_prototype` TINYINT UNSIGNED DEFAULT NULL,
    `gestion_cu` TINYINT(1) DEFAULT 0 NOT NULL,
    `temps_manu` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `max_proto_quantity` DOUBLE PRECISION DEFAULT NULL,
    `livraison_minimum` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `min_prod_quantity` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `stock_minimum` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `designation` VARCHAR(80) NOT NULL,
    `info_public` TEXT DEFAULT NULL,
    `typeconditionnement` VARCHAR(30) NOT NULL,
    `conditionnement` DOUBLE PRECISION DEFAULT NULL,
    `price` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `price_without_cu` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `production_delay` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `transfert_price_supplies` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `transfert_price_work` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `weight` DOUBLE PRECISION DEFAULT '0' NOT NULL
)
SQL);
        $this->insert('product', [
            'id',
            'statut',
            'temps_auto',
            'ref',
            'tps_chiff_auto',
            'tps_chiff_manu',
            'id_productstatus',
            'date_expiration',
            'id_product_subfamily',
            'volume_previsionnel',
            'id_customcode',
            'id_product_child',
            'id_incoterms',
            'indice',
            'indice_interne',
            'is_prototype',
            'gestion_cu',
            'temps_manu',
            'max_proto_quantity',
            'livraison_minimum',
            'min_prod_quantity',
            'stock_minimum',
            'designation',
            'info_public',
            'typeconditionnement',
            'conditionnement',
            'price',
            'price_without_cu',
            'production_delay',
            'transfert_price_supplies',
            'transfert_price_work',
            'weight'
        ]);
        $this->addQuery('RENAME TABLE `product` TO `product_old`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `product` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `auto_duration_code` VARCHAR(6) DEFAULT NULL,
    `auto_duration_denominator` VARCHAR(6) DEFAULT NULL,
    `auto_duration_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `code` VARCHAR(30) NOT NULL,
    `costing_auto_duration_code` VARCHAR(6) DEFAULT NULL,
    `costing_auto_duration_denominator` VARCHAR(6) DEFAULT NULL,
    `costing_auto_duration_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `costing_manual_duration_code` VARCHAR(6) DEFAULT NULL,
    `costing_manual_duration_denominator` VARCHAR(6) DEFAULT NULL,
    `costing_manual_duration_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `current_place_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` enum('agreed','blocked','disabled','draft','to_validate','under_exemption') NOT NULL DEFAULT 'draft' COMMENT '(DC2Type:product_current_place)',
    `customs_code` VARCHAR(10) DEFAULT NULL,
    `end_of_life` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `family_id` INT UNSIGNED NOT NULL,
    `forecast_volume_code` VARCHAR(6) DEFAULT NULL,
    `forecast_volume_denominator` VARCHAR(6) DEFAULT NULL,
    `forecast_volume_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `id_product_child` INT UNSIGNED NOT NULL,
    `incoterms_id` INT UNSIGNED DEFAULT NULL,
    `index` VARCHAR(3) NOT NULL,
    `internal_index` TINYINT UNSIGNED NOT NULL DEFAULT '1' COMMENT '(DC2Type:tinyint)',
    `kind` enum('EI','Prototype','Série','Pièce de rechange') NOT NULL DEFAULT 'Prototype' COMMENT '(DC2Type:product_kind)',
    `managed_copper` TINYINT(1) DEFAULT 0 NOT NULL,
    `manual_duration_code` VARCHAR(6) DEFAULT NULL,
    `manual_duration_denominator` VARCHAR(6) DEFAULT NULL,
    `manual_duration_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `max_proto_code` VARCHAR(6) DEFAULT NULL,
    `max_proto_denominator` VARCHAR(6) DEFAULT NULL,
    `max_proto_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `min_delivery_code` VARCHAR(6) DEFAULT NULL,
    `min_delivery_denominator` VARCHAR(6) DEFAULT NULL,
    `min_delivery_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `min_prod_code` VARCHAR(6) DEFAULT NULL,
    `min_prod_denominator` VARCHAR(6) DEFAULT NULL,
    `min_prod_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `min_stock_code` VARCHAR(6) DEFAULT NULL,
    `min_stock_denominator` VARCHAR(6) DEFAULT NULL,
    `min_stock_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `name` VARCHAR(80) NOT NULL,
    `notes` text DEFAULT NULL,
    `packaging_code` VARCHAR(6) DEFAULT NULL,
    `packaging_denominator` VARCHAR(6) DEFAULT NULL,
    `packaging_kind` VARCHAR(30) NOT NULL,
    `packaging_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `price_code` VARCHAR(6) DEFAULT NULL,
    `price_denominator` VARCHAR(6) DEFAULT NULL,
    `price_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `price_without_copper_code` VARCHAR(6) DEFAULT NULL,
    `price_without_copper_denominator` VARCHAR(6) DEFAULT NULL,
    `price_without_copper_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `production_delay_code` VARCHAR(6) DEFAULT NULL,
    `production_delay_denominator` VARCHAR(6) DEFAULT NULL,
    `production_delay_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `transfert_price_supplies_code` VARCHAR(6) DEFAULT NULL,
    `transfert_price_supplies_denominator` VARCHAR(6) DEFAULT NULL,
    `transfert_price_supplies_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `transfert_price_work_code` VARCHAR(6) DEFAULT NULL,
    `transfert_price_work_denominator` VARCHAR(6) DEFAULT NULL,
    `transfert_price_work_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `unit_id` INT UNSIGNED NOT NULL,
    `weight_code` VARCHAR(6) DEFAULT NULL,
    `weight_denominator` VARCHAR(6) DEFAULT NULL,
    `weight_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CONSTRAINT `IDX_D34A04ADC35E566A` FOREIGN KEY (`family_id`) REFERENCES `product_family` (`id`),
    CONSTRAINT `IDX_D34A04AD43D02C80` FOREIGN KEY (`incoterms_id`) REFERENCES `incoterms` (`id`),
    CONSTRAINT `IDX_D34A04AD727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `product` (`id`),
    CONSTRAINT `IDX_D34A04ADF8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `product` (
    `old_id`,
    `auto_duration_code`,
    `auto_duration_value`,
    `code`,
    `costing_auto_duration_code`,
    `costing_auto_duration_value`,
    `costing_manual_duration_code`,
    `costing_manual_duration_value`,
    `current_place_name`,
    `customs_code`,
    `end_of_life`,
    `family_id`,
    `forecast_volume_code`,
    `forecast_volume_value`,
    `id_product_child`,
    `incoterms_id`,
    `index`,
    `internal_index`,
    `kind`,
    `managed_copper`,
    `manual_duration_code`,
    `manual_duration_value`,
    `max_proto_code`,
    `max_proto_value`,
    `min_delivery_code`,
    `min_delivery_value`,
    `min_prod_code`,
    `min_prod_value`,
    `min_stock_code`,
    `min_stock_value`,
    `name`,
    `notes`,
    `packaging_code`,
    `packaging_kind`,
    `packaging_value`,
    `parent_id`,
    `price_code`,
    `price_value`,
    `price_without_copper_code`,
    `price_without_copper_value`,
    `production_delay_code`,
    `production_delay_value`,
    `transfert_price_supplies_code`,
    `transfert_price_supplies_value`,
    `transfert_price_work_code`,
    `transfert_price_work_value`,
    `unit_id`,
    `weight_code`,
    `weight_value`
) SELECT
    `id`,
    'h',
    `temps_auto`,
    `ref`,
    'h',
    IFNULL(`tps_chiff_auto`, 0),
    'h',
    IFNULL(`tps_chiff_manu`, 0),
    CASE
        WHEN `product_old`.`id_productstatus` = 1 THEN 'draft'
        WHEN `product_old`.`id_productstatus` = 2 THEN 'to_validate'
        WHEN `product_old`.`id_productstatus` = 3 THEN 'agreed'
        WHEN `product_old`.`id_productstatus` = 4 THEN 'under_exemption'
        WHEN `product_old`.`id_productstatus` = 5 THEN 'blocked'
        WHEN `product_old`.`id_productstatus` = 6 THEN 'disabled'
        ELSE 'draft'
    END,
    (SELECT `customcode`.`code` FROM `customcode` WHERE `customcode`.`id` = `product_old`.`id_customcode`),
    `date_expiration`,
    (SELECT `product_family`.`id` FROM `product_family` WHERE `product_family`.`old_subfamily_id` = `product_old`.`id_product_subfamily`),
    'U',
    `volume_previsionnel`,
    `id_product_child`,
    (SELECT `incoterms`.`id` FROM `incoterms` WHERE `incoterms`.`id` = `product_old`.`id_incoterms`),
    `indice`,
    `indice_interne`,
    CASE
        WHEN `product_old`.`is_prototype` = 0 THEN 'Série'
        WHEN `product_old`.`is_prototype` = 1 THEN 'Prototype'
        WHEN `product_old`.`is_prototype` = 2 THEN 'EI'
        ELSE 'Prototype'
    END,
    `gestion_cu`,
    'h',
    `temps_manu`,
    'U',
    IFNULL(`max_proto_quantity`, 0),
    'U',
    `livraison_minimum`,
    'U',
    `min_prod_quantity`,
    'U',
    `stock_minimum`,
    `designation`,
    `info_public`,
    'U',
    `typeconditionnement`,
    IFNULL(`conditionnement`, 1),
    (SELECT `product`.`id` FROM `product` WHERE `product`.`id_product_child` = `product_old`.`id`),
    'EUR',
    `price`,
    'EUR',
    `price_without_cu`,
    'j',
    `production_delay`,
    'EUR',
    `transfert_price_supplies`,
    'EUR',
    `transfert_price_work`,
    (SELECT `unit`.`id` FROM `unit` WHERE `unit`.`code` = 'U'),
    'g',
    `weight`
FROM `product_old`
WHERE `statut` = 0
SQL);
        $this->addQuery('DROP TABLE `product_old`');
    }

    private function upQualityTypes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `qualitycontrol` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) DEFAULT 0 NOT NULL,
    `qualitycontrol` varchar(40) NOT NULL
)
SQL);
        $this->insert('qualitycontrol', ['id', 'statut', 'qualitycontrol']);
        $this->addQuery(<<<'SQL'
ALTER TABLE `qualitycontrol`
    CHANGE `qualitycontrol` `name` VARCHAR(40) NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addQuery('UPDATE `qualitycontrol` SET `name` = UCFIRST(`name`)');
        $this->addQuery('RENAME TABLE `qualitycontrol` TO `quality_type`');
    }

    private function upRejectTypes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `production_rejectlist` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) DEFAULT 0 NOT NULL,
    `libelle` VARCHAR(255) DEFAULT NULL
)
SQL);
        $this->insert('production_rejectlist', ['id', 'statut', 'libelle']);
        $this->addQuery('DELETE FROM `production_rejectlist` WHERE `statut` = 1 OR `libelle` IS NULL');
        $this->addQuery(<<<'SQL'
CREATE TABLE `reject_type` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL
)
SQL);
        $this->addQuery('INSERT INTO `reject_type` (`name`) SELECT UCFIRST(`libelle`) FROM `production_rejectlist`');
        $this->addQuery('DROP TABLE `production_rejectlist`');
    }

    private function upSkills(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `skill_type` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `name` VARCHAR(50) NOT NULL
)
SQL);
    }

    private function upSocieties(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `society` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` TINYINT(1) DEFAULT 0 NOT NULL,
    `nom` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) DEFAULT NULL,
    `address1` VARCHAR(80) DEFAULT NULL,
    `address2` VARCHAR(60) DEFAULT NULL,
    `zip` VARCHAR(10) DEFAULT NULL,
    `city` VARCHAR(50) DEFAULT NULL,
    `id_country` INT UNSIGNED NOT NULL,
    `id_invoicetimedue` INT UNSIGNED DEFAULT NULL,
    `id_invoicetimeduesupplier` INT UNSIGNED DEFAULT NULL,
    `invoice_minimum` DOUBLE PRECISION DEFAULT NULL,
    `order_minimum` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `web` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(60) DEFAULT NULL,
    `formejuridique` VARCHAR(50) DEFAULT NULL,
    `siren` VARCHAR(50) DEFAULT NULL,
    `tva` VARCHAR(255) DEFAULT NULL,
    `compte_compta` VARCHAR(50) DEFAULT NULL,
    `info_public` TEXT DEFAULT NULL,
    `info_private` TEXT DEFAULT NULL,
    `ar_enabled` TINYINT(1) DEFAULT 0 NOT NULL,
    `ar_customer_enabled` TINYINT(1) DEFAULT 0 NOT NULL,
    `indice_cu_enabled` TINYINT(1) DEFAULT 0 NOT NULL,
    `indice_cu` DOUBLE PRECISION DEFAULT NULL,
    `indice_cu_date` DATETIME DEFAULT NULL,
    `indice_cu_date_fin` DATETIME DEFAULT NULL,
    `id_incoterms` INT UNSIGNED DEFAULT NULL,
    `force_tva` TINYINT DEFAULT NULL,
    `id_messagetva` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('society', [
            'id',
            'statut',
            'nom',
            'phone',
            'address1',
            'address2',
            'zip',
            'city',
            'id_country',
            'id_invoicetimedue',
            'id_invoicetimeduesupplier',
            'invoice_minimum',
            'order_minimum',
            'web',
            'email',
            'formejuridique',
            'siren',
            'tva',
            'compte_compta',
            'info_public',
            'info_private',
            'ar_enabled',
            'ar_customer_enabled',
            'indice_cu_enabled',
            'indice_cu',
            'indice_cu_date',
            'indice_cu_date_fin',
            'id_incoterms',
            'force_tva',
            'id_messagetva'
        ]);
        $this->addQuery('RENAME TABLE `society` TO `society_old`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `society` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `accounting_account` VARCHAR(50) DEFAULT NULL,
    `address_address` VARCHAR(80) DEFAULT NULL,
    `address_address2` VARCHAR(60) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(60) DEFAULT NULL,
    `phone` VARCHAR(255) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `ar` TINYINT(1) DEFAULT 0 NOT NULL,
    `bank_details` VARCHAR(255) DEFAULT NULL,
    `copper_index_code` VARCHAR(6) DEFAULT NULL,
    `copper_index_denominator` VARCHAR(6) DEFAULT NULL,
    `copper_index_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `copper_last` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_managed` TINYINT(1) DEFAULT 0 NOT NULL,
    `copper_next` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_type` ENUM('à la livraison','mensuel','semestriel') NOT NULL DEFAULT 'mensuel' COMMENT '(DC2Type:copper_type)',
    `force_vat` ENUM('TVA par défaut selon le pays du client','Force AVEC TVA','Force SANS TVA') NOT NULL DEFAULT 'TVA par défaut selon le pays du client' COMMENT '(DC2Type:vat_message_force)',
    `incoterms_id` INT UNSIGNED DEFAULT NULL,
    `invoice_min_code` VARCHAR(6) DEFAULT NULL,
    `invoice_min_denominator` VARCHAR(6) DEFAULT NULL,
    `invoice_min_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `invoice_time_due_id` INT UNSIGNED DEFAULT NULL,
    `legal_form` VARCHAR(50) DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `order_min_code` VARCHAR(6) DEFAULT NULL,
    `order_min_denominator` VARCHAR(6) DEFAULT NULL,
    `order_min_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `ppm_rate` SMALLINT UNSIGNED NOT NULL DEFAULT '10',
    `siren` VARCHAR(50) DEFAULT NULL,
    `vat` VARCHAR(255) DEFAULT NULL,
    `vat_message_id` INT UNSIGNED DEFAULT NULL,
    `web` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_D6461F243D02C80` FOREIGN KEY (`incoterms_id`) REFERENCES `incoterms` (`id`),
    CONSTRAINT `IDX_D6461F2C8D5B586` FOREIGN KEY (`invoice_time_due_id`) REFERENCES `invoice_time_due` (`id`),
    CONSTRAINT `IDX_D6461F26C896AD9` FOREIGN KEY (`vat_message_id`) REFERENCES `vat_message` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `society` (
    `accounting_account`,
    `address_address`,
    `address_address2`,
    `address_city`,
    `address_country`,
    `address_email`,
    `phone`,
    `address_zip_code`,
    `ar`,
    `copper_index_value`,
    `copper_last`,
    `copper_managed`,
    `copper_next`,
    `force_vat`,
    `incoterms_id`,
    `invoice_min_value`,
    `invoice_time_due_id`,
    `legal_form`,
    `name`,
    `notes`,
    `order_min_value`,
    `siren`,
    `vat`,
    `vat_message_id`,
    `web`
) SELECT
    `compte_compta`,
    `address1`,
    `address2`,
    `city`,
    (SELECT UCASE(`country`.`code`) FROM `country` WHERE `country`.`id` = `society_old`.`id_country`),
    `email`,
    `phone`,
    `zip`,
    `ar_enabled` = 1 OR `ar_customer_enabled` = 1,
    IFNULL(`indice_cu`, 0),
    `indice_cu_date`,
    `indice_cu_enabled`,
    `indice_cu_date_fin`,
    CASE
        WHEN `force_tva` = 1 THEN 'Force AVEC TVA'
        WHEN `force_tva` = 2 THEN 'Force SANS TVA'
        ELSE 'TVA par défaut selon le pays du client'
    END,
    (SELECT `incoterms`.`id` FROM `incoterms` WHERE `incoterms`.`id` = `society_old`.`id_incoterms`),
    IFNULL(`invoice_minimum`, 0),
    (
        SELECT `invoice_time_due`.`id`
        FROM `invoice_time_due`
        WHERE `invoice_time_due`.`id_old_invoicetimedue` = `society_old`.`id_invoicetimedue`
        OR `invoice_time_due`.`id_old_invoicetimeduesupplier` = `society_old`.`id_invoicetimeduesupplier`
        LIMIT 1
    ),
    `formejuridique`,
    `nom`,
    IF(
        `info_public` IS NULL,
        `info_private`,
        IF(`info_private` IS NULL, NULL, CONCAT(`info_public`, `info_private`))
    ),
    `order_minimum`,
    `siren`,
    `tva`,
    (SELECT `vat_message`.`id` FROM `vat_message` WHERE `vat_message`.`id` = `society_old`.`id_messagetva`),
    `web`
FROM `society_old`
SQL);
        $this->addQuery('DROP TABLE `society_old`');
    }

    private function upTimeSlots(): void {
        $this->addQuery(<<<'SQL'
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
        $this->addQuery(<<<'SQL'
INSERT INTO `time_slot` (`end`, `end_break`, `name`, `start`, `start_break`) VALUES
('13:30:00', NULL, 'Matin', '05:30:00', NULL),
('17:30:00', '13:30:00', 'Journée', '07:30:00', '12:30:00'),
('21:30:00',  NULL, 'Après-midi', '13:30:00', NULL),
('08:00:00',  NULL, 'Samedi', '13:00:00', NULL)
SQL);
    }

    private function upUnits(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `unit` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` TINYINT(1) DEFAULT 0 NOT NULL,
  `base` DOUBLE PRECISION DEFAULT '1' NOT NULL,
  `unit_short_lbl` VARCHAR(6) NOT NULL,
  `unit_complete_lbl` varchar(50) NOT NULL,
  `parent` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('unit', ['id', 'statut', 'base', 'unit_short_lbl', 'unit_complete_lbl', 'parent']);
        $this->addQuery(<<<'SQL'
ALTER TABLE `unit`
    CHANGE `parent` `parent_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    CHANGE `unit_complete_lbl` `name` VARCHAR(50) NOT NULL,
    CHANGE `unit_short_lbl` `code` VARCHAR(6) NOT NULL COLLATE `utf8_bin`,
    ADD CONSTRAINT `IDX_DCBB0C53727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `unit` (`id`)
SQL);
        $this->addQuery('UPDATE `unit` SET `name` = UCFIRST(`name`)');
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
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `emb_roles_roles` TEXT NOT NULL COMMENT '(DC2Type:simple_array)',
    `name` VARCHAR(30) NOT NULL,
    `password` CHAR(60) NOT NULL  COMMENT '(DC2Type:char)',
    `username` VARCHAR(20) NOT NULL
)
SQL);
        $this->addQuery(sprintf(
            'INSERT INTO `employee` (`name`, `password`, `username`, `emb_roles_roles`) VALUES (\'Super\', %s, \'super\', %s)',
            /** @phpstan-ignore-next-line */
            $this->connection->quote($user->getPassword()),
            /** @phpstan-ignore-next-line */
            $this->connection->quote(implode(',', $user->getRoles()))
        ));
        $this->addQuery(<<<'SQL'
CREATE TABLE `token` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `employee_id` INT UNSIGNED NOT NULL,
    `expire_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `token` CHAR(120) NOT NULL COMMENT '(DC2Type:char)',
    CONSTRAINT `IDX_5F37A13B8C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`)
)
SQL);
    }

    private function upVatMessages(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `messagetva` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` TINYINT(1) DEFAULT 0 NOT NULL,
  `message` TEXT NOT NULL
)
SQL);
        $this->insert('messagetva', ['id', 'statut', 'message']);
        $this->addQuery(<<<'SQL'
ALTER TABLE `messagetva`
    CHANGE `message` `name` VARCHAR(120) NOT NULL,
    CHANGE `statut` `deleted` TINYINT(1) DEFAULT 0 NOT NULL
SQL);
        $this->addQuery('RENAME TABLE `messagetva` TO `vat_message`');
    }
}
