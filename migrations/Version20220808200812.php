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
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Psr\Log\LoggerInterface;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\UnicodeString;

final class Version20220808200812 extends AbstractMigration {
    private UserPasswordHasherInterface $hasher;

    /** @var Collection<int, string> */
    private Collection $phoneQueries;

    /** @var Collection<int, string> */
    private Collection $queries;

    public function __construct(Connection $connection, LoggerInterface $logger) {
        parent::__construct($connection, $logger);
        $this->phoneQueries = new Collection();
        $this->queries = new Collection();
    }

    private static function getVersion(): string {
        $matches = [];
        preg_match('/Version\d+/', __CLASS__, $matches);
        return $matches[0];
    }

    private static function trim(string $str): string {
        return (new UnicodeString($str))
            ->replaceMatches('/\s+/', ' ')
            ->replace('( ', '(')
            ->replace(' )', ')')
            ->toString();
    }

    public function postUp(Schema $schema): void {
        $this->upPhoneNumbers('customer', 'address_phone_number');
        $this->upPhoneNumbers('customer_contact', 'address_phone_number');
        $this->upPhoneNumbers('customer_contact', 'mobile', 'mobile');
        $this->upPhoneNumbers('employee', 'address_phone_number');
        $this->upPhoneNumbers('employee_contact', 'phone', 'phone');
        $this->upPhoneNumbers('out_trainer', 'tel');
        $this->upPhoneNumbers('society', 'phone');
        $this->upPhoneNumbers('supplier', 'address_phone_number');
        $this->upPhoneNumbers('supplier_contact', 'address_phone_number');
        $this->upPhoneNumbers('supplier_contact', 'mobile', 'mobile');

        $version = self::getVersion();
        $this->connection->executeQuery("CREATE PROCEDURE postUp$version() BEGIN {$this->getPhoneQueries()}; END");
        $this->connection->executeQuery("CALL postUp$version");
        $this->connection->executeQuery("DROP PROCEDURE postUp$version");
        $this->connection->executeQuery('ALTER TABLE `employee_contact` DROP `address_country`');
    }

    public function setHasher(UserPasswordHasherInterface $hasher): void {
        $this->hasher = $hasher;
    }

    public function up(Schema $schema): void {
        $this->addSql(<<<'SQL'
CREATE FUNCTION IF_EMPTY(s VARCHAR(255), def VARCHAR(255))
    RETURNS VARCHAR(255) DETERMINISTIC
    RETURN IF(s IS NULL OR TRIM(s) = '', def, s)
SQL);
        $this->addSql(<<<'SQL'
CREATE FUNCTION UCFIRST (s VARCHAR(255))
    RETURNS VARCHAR(255) DETERMINISTIC
    RETURN CONCAT(UCASE(LEFT(s, 1)), LCASE(SUBSTRING(s, 2)))
SQL);
        $this->addSql(<<<'SQL'
CREATE PROCEDURE LINK_COMPONENTS_ATTRIBUTES()
BEGIN
    INSERT INTO `component_attribute` (`component_id`, `attribute_id`, `measure_code`)
    SELECT `c`.`id`, `a`.`id`, `u`.`code`
    FROM `component` `c`
    INNER JOIN `component_family` `f` ON `c`.`family_id` = `f`.`id`
    INNER JOIN `attribute_family` `af` ON `f`.`id` = `af`.`family_id`
    INNER JOIN `attribute` `a` ON `af`.`attribute_id` = `a`.`id`
    LEFT JOIN `unit` `u` ON `a`.`unit_id` = `u`.`id`
    ON DUPLICATE KEY UPDATE `deleted` = 0, `measure_code` = `u`.`code`;
    UPDATE `component_attribute` `ca`
    LEFT JOIN `component` `c` ON `ca`.`component_id` = `c`.`id`
    LEFT JOIN `component_family` `f` ON `c`.`family_id` = `f`.`id`
    LEFT JOIN `attribute` `a` ON `ca`.`attribute_id` = `a`.`id`
    LEFT JOIN `attribute_family` `af` ON `a`.`id` = `af`.`attribute_id` AND `f`.`id` = `af`.`family_id`
    SET `ca`.`deleted` = 1
    WHERE `af`.`attribute_id` IS NULL OR `af`.`family_id` IS NULL;
END
SQL);
        $this->setProcedure();
        $version = self::getVersion();
        $this->addSql("CREATE PROCEDURE up$version() BEGIN {$this->getQueries()}; END");
        $this->addSql("CALL up$version");
        $this->addSql("DROP PROCEDURE up$version");
        $this->addSql('DROP FUNCTION UCFIRST');
        $this->addSql('DROP FUNCTION IF_EMPTY');
    }

    protected function addSql(string $sql, array $params = [], array $types = []): void {
        parent::addSql(self::trim($sql), $params, $types);
    }

    private function addQuery(string $query): void {
        $this->queries->push(self::trim($query));
    }

    private function getPhoneQueries(): string {
        return $this->phoneQueries->join('; ');
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
                        $mapped[$column] = $value === null || $value === '0000-00-00 00:00:00' || $value === '0000-00-00'
                            ? 'NULL'
                            : $this->connection->quote($value);
                    }
                    return '('.implode(',', $mapped).')';
                })
                ->join(',')
        ));
    }

    private function insertCustomerAddresses(string $type, string $like): void {
        $this->addQuery(<<<SQL
INSERT INTO `customer_address` (
    `old_id`,
    `address_address`,
    `address_address2`,
    `address_city`,
    `address_country`,
    `address_zip_code`,
    `customer_id`,
    `name`,
    `type`
) SELECT
    `id`,
    `address1`,
    `address2`,
    `city`,
    (SELECT UCASE(`country`.`code`) FROM `country` WHERE `country`.`id` = `address`.`id_country`),
    `zip`,
    (
        SELECT `customer`.`id`
        FROM `customer`
        WHERE `customer`.`society_id` = (
            SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `address`.`id_customer`
        )
    ),
    `nom`,
    '$type'
FROM `address`
WHERE `statut` = 0
AND `typeaddress` LIKE '%$like%'
AND EXISTS (
    SELECT `customer`.`id`
    FROM `customer`
    WHERE `customer`.`society_id` = (
        SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `address`.`id_customer`
    )
)
SQL);
    }

    private function setProcedure(): void {
        // rank 0
        $this->upCountries();
        $this->upCustomCode();
        $this->upLocales();
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
        $this->upOutTrainers();
        $this->upProductFamilies();
        $this->upQualityTypes();
        $this->upRejectTypes();
        $this->upSkillTypes();
        $this->upTimeSlots();
        $this->upUnits();
        $this->upVatMessages();
        // rank 2
        $this->upAttributes();
        $this->upComponents();
        $this->upProducts();
        $this->upSocieties();
        // rank 3
        $this->upCompanyEvents();
        $this->upComponentAttributes();
        $this->upComponentReferenceValues();
        $this->upContacts();
        $this->upCustomerAddresses();
        $this->upCustomerEvents();
        $this->upCustomerProducts();
        $this->upManufacturers();
        $this->upNomenclatures();
        $this->upPrinters();
        $this->upSupplierComponents();
        $this->upSupplies();
        $this->upTeams();
        $this->upWarehouses();
        $this->upZones();
        // rank 4
        $this->upBills();
        $this->upComponentSupplierPrices();
        $this->upCustomerOrders();
        $this->upCustomerProductPrices();
        $this->upEmployees();
        $this->upEngines();
        $this->upStocks();
        // rank 5
        $this->upDeliveryNotes();
        $this->upEmployeeEvents();
        $this->upItRequests();
        $this->upManufacturingOrders();
        $this->upNotifications();
        $this->upPlannings();
        $this->upSkills();
        // rank 6
        $this->upEngineEvents();
        // clean
        $this->addQuery('ALTER TABLE `attribute` DROP `old_id`');
        $this->addQuery('ALTER TABLE `bill` DROP `old_id`');
        $this->addQuery('ALTER TABLE `component` DROP `old_id`');
        $this->addQuery('ALTER TABLE `component_family` DROP `old_subfamily_id`');
        $this->addQuery('ALTER TABLE `customer_address` DROP `old_id`');
        $this->addQuery('ALTER TABLE `customer_contact` DROP `old_id`');
        $this->addQuery('ALTER TABLE `customer_order` DROP `old_id`');
        $this->addQuery('ALTER TABLE `employee` DROP `id_society`, DROP `old_id`');
        $this->addQuery('ALTER TABLE `invoice_time_due` DROP `id_old_invoicetimedue`, DROP `id_old_invoicetimeduesupplier`');
        $this->addQuery('ALTER TABLE `product` DROP `id_society`, DROP `old_id`');
        $this->addQuery('ALTER TABLE `product_customer` DROP `old_id`');
        $this->addQuery('ALTER TABLE `product_family` DROP `old_subfamily_id`');
        $this->addQuery('ALTER TABLE `skill_type` DROP `old_id`');
        $this->addQuery('ALTER TABLE `society` DROP `old_id`');
        $this->addQuery('ALTER TABLE `supplier_component` DROP `old_id`');
        $this->addQuery('ALTER TABLE `warehouse` DROP `old_id`');
        $this->addQuery('DROP TABLE `country`');
        $this->addQuery('DROP TABLE `customcode`');
        $this->addQuery('DROP TABLE `locale`');
    }

    private function upAttributes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `attribut` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
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
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
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
        INSERT INTO `attribute_family` (`attribute_id`, `family_id`)
        SELECT @attribute_id, `parent_id` FROM `component_family` WHERE `id` = @attribute_family AND `parent_id` IS NOT NULL;
        INSERT INTO `attribute_family` (`attribute_id`, `family_id`)
        SELECT @attribute_id, `id` FROM `component_family` WHERE `parent_id` = @attribute_family;
    END WHILE;
    SET @attribute_i = @attribute_i + 1;
END WHILE
SQL);
        $this->addQuery('ALTER TABLE `attribute` DROP `attribut_id_family`');
    }

    private function upBills(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `invoicecustomer` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `invoice_number` INT UNSIGNED DEFAULT NULL,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_customer` INT UNSIGNED DEFAULT NULL,
    `id_society` INT UNSIGNED DEFAULT NULL,
    `id_invoicecustomerstatus` INT UNSIGNED DEFAULT NULL,
    `id_messagetva` INT UNSIGNED DEFAULT NULL,
    `id_contact` INT UNSIGNED DEFAULT NULL,
    `info_public` TEXT DEFAULT NULL,
    `date_facturation` DATE DEFAULT NULL,
    `date_echeance` DATE DEFAULT NULL,
    `total_ht` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `tva` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `total_ttc` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `force_tva` TINYINT UNSIGNED DEFAULT NULL DEFAULT 0 COMMENT '0 = sans effet, 1 = force SANS TVA, 2 = force AVEC TVA',
    `commentaire` TEXT DEFAULT NULL,
    `infos_privees` TEXT DEFAULT NULL
)
SQL);
        $this->insert('invoicecustomer', [
            'id',
            'invoice_number',
            'statut',
            'id_customer',
            'id_society',
            'id_invoicecustomerstatus',
            'id_messagetva',
            'id_contact',
            'info_public',
            'date_facturation',
            'date_echeance',
            'total_ht',
            'tva',
            'total_ttc',
            'force_tva',
            'commentaire',
            'infos_privees'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `bill` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `billing_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `company_id` INT UNSIGNED DEFAULT NULL,
    `contact_id` INT UNSIGNED DEFAULT NULL,
    `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` ENUM('bill', 'disabled', 'draft', 'litigation', 'paid', 'partially_paid') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:bill_current_place)',
    `customer_id` INT UNSIGNED DEFAULT NULL,
    `due_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `excl_tax_code` VARCHAR(6) DEFAULT NULL,
    `excl_tax_denominator` VARCHAR(6) DEFAULT NULL,
    `excl_tax_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `force_vat` ENUM('TVA par défaut selon le pays du client', 'Force AVEC TVA', 'Force SANS TVA') DEFAULT 'TVA par défaut selon le pays du client' NOT NULL COMMENT '(DC2Type:vat_message_force)',
    `incl_tax_code` VARCHAR(6) DEFAULT NULL,
    `incl_tax_denominator` VARCHAR(6) DEFAULT NULL,
    `incl_tax_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `notes` VARCHAR(255) DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    `vat_code` VARCHAR(6) DEFAULT NULL,
    `vat_denominator` VARCHAR(6) DEFAULT NULL,
    `vat_message_id` INT UNSIGNED DEFAULT NULL,
    `vat_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CONSTRAINT `IDX_7A2119E3979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_7A2119E3E7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `customer_contact` (`id`),
    CONSTRAINT `IDX_7A2119E39395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
    CONSTRAINT `IDX_7A2119E36C896AD9` FOREIGN KEY (`vat_message_id`) REFERENCES `vat_message` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `bill` (
    `old_id`,
    `billing_date`,
    `company_id`,
    `contact_id`,
    `current_place_name`,
    `customer_id`,
    `due_date`,
    `excl_tax_code`,
    `excl_tax_value`,
    `force_vat`,
    `incl_tax_code`,
    `incl_tax_value`,
    `notes`,
    `ref`,
    `vat_code`,
    `vat_message_id`,
    `vat_value`
) SELECT
    `id`,
    `date_facturation`,
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `invoicecustomer`.`id_society`)),
    (SELECT `customer_contact`.`id` FROM `customer_contact` WHERE `customer_contact`.`old_id` = `invoicecustomer`.`id_contact`),
    CASE
        WHEN `id_invoicecustomerstatus` = 1 THEN 'draft'
        WHEN `id_invoicecustomerstatus` = 2 THEN 'bill'
        WHEN `id_invoicecustomerstatus` = 3 THEN 'partially_paid'
        WHEN `id_invoicecustomerstatus` = 4 THEN 'paid'
        WHEN `id_invoicecustomerstatus` = 6 THEN 'litigation'
        ELSE 'disabled'
    END,
    (SELECT `customer`.`id` FROM `customer` WHERE `customer`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `invoicecustomer`.`id_customer`)),
    `date_echeance`,
    'EUR',
    `total_ht`,
    CASE
        WHEN `force_tva` = 1 THEN 'Force SANS TVA'
        WHEN `force_tva` = 2 THEN 'Force AVEC TVA'
        ELSE 'TVA par défaut selon le pays du client'
    END,
    'EUR',
    `total_ttc`,
    IF(
        LENGTH(CONCAT(IFNULL(`info_public`, ''), IFNULL(`infos_privees`, ''), IFNULL(`commentaire`, ''))) = 0,
        NULL,
        CONCAT(IFNULL(`info_public`, ''), IFNULL(`infos_privees`, ''), IFNULL(`commentaire`, ''))
    ),
    `invoice_number`,
    'EUR',
    (SELECT `vat_message`.`id` FROM `vat_message` WHERE `vat_message`.`id` = `invoicecustomer`.`id_messagetva`),
    `tva`
FROM `invoicecustomer`
WHERE `statut` = 0
AND EXISTS (SELECT `customer`.`id` FROM `customer` WHERE `customer`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `invoicecustomer`.`id_customer`))
SQL);
        $this->addQuery('DROP TABLE `invoicecustomer`');
    }

    private function upCarriers(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `carrier` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN NOT NULL,
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
    CHANGE `statut` `deleted` BOOLEAN DEFAULT FALSE NOT NULL
SQL);
    }

    private function upColors(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `couleur` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `name` VARCHAR(20) NOT NULL,
    `rgb` CHAR(7) NOT NULL COMMENT '(DC2Type:char)'
)
SQL);
        $this->insert('couleur', ['id', 'name', 'rgb']);
        $this->addQuery('UPDATE `couleur` SET `name` = UCFIRST(`name`)');
        $this->addQuery('RENAME TABLE `couleur` TO `color`');
    }

    private function upCompanyEvents(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `company_event` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `company_id` INT UNSIGNED NOT NULL,
    `date` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `done` BOOLEAN DEFAULT FALSE NOT NULL,
    `kind` VARCHAR(255) DEFAULT 'holiday' NOT NULL,
    `managing_company_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_7C5FA8C2E7E23CE8` FOREIGN KEY (`managing_company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_7C5FA8C2979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
)
SQL);
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
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `component_id` INT UNSIGNED NOT NULL,
    `attribute_id` INT UNSIGNED NOT NULL,
    `value` VARCHAR(255) DEFAULT NULL,
    `color_id` INT UNSIGNED DEFAULT NULL,
    `measure_code` VARCHAR(6) DEFAULT NULL,
    `measure_denominator` VARCHAR(6) DEFAULT NULL,
    `measure_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    UNIQUE KEY `UNIQ_248373AAB6E62EFAE2ABAFFF` (`attribute_id`, `component_id`),
    CONSTRAINT `IDX_248373AAB6E62EFA` FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`id`),
    CONSTRAINT `IDX_248373AA7ADA1FB5` FOREIGN KEY (`color_id`) REFERENCES `color` (`id`),
    CONSTRAINT `IDX_248373AAE2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`)
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
        $this->addQuery('CALL LINK_COMPONENTS_ATTRIBUTES');
    }

    private function upComponentFamilies(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `component_family` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `prefix` VARCHAR(3) DEFAULT NULL,
    `copperable` BOOLEAN DEFAULT FALSE NOT NULL,
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
    `id_family` TINYINT UNSIGNED NOT NULL
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
    CHANGE `statut` `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    ADD CONSTRAINT `IDX_79FF2A21727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `component_family` (`id`)
SQL);
    }

    private function upComponentReferenceValues(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `component_quality_values` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_component` INT UNSIGNED DEFAULT NULL,
    `obligation_hauteur` BOOLEAN DEFAULT TRUE NOT NULL,
    `height_tolerance_code` VARCHAR(6) DEFAULT 'mm',
    `height_tolerance_denominator` VARCHAR(6) DEFAULT NULL,
    `tolerance_hauteur` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `height_value_code` VARCHAR(6) DEFAULT 'mm',
    `height_value_denominator` VARCHAR(6) DEFAULT NULL,
    `hauteur` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `section_code` VARCHAR(6) DEFAULT 'mm²',
    `section_denominator` VARCHAR(6) DEFAULT NULL,
    `section` DOUBLE PRECISION UNSIGNED DEFAULT 0 NOT NULL,
    `obligation_traction` BOOLEAN DEFAULT TRUE NOT NULL,
    `tensile_tolerance_code` VARCHAR(6) DEFAULT 'mm²',
    `tensile_tolerance_denominator` VARCHAR(6) DEFAULT NULL,
    `tolerance_traction` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `tensile_value_code` VARCHAR(6) DEFAULT 'mm²',
    `tensile_value_denominator` VARCHAR(6) DEFAULT NULL,
    `traction` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `obligation_largeur` BOOLEAN DEFAULT TRUE NOT NULL,
    `width_tolerance_code` VARCHAR(6) DEFAULT 'mm',
    `width_tolerance_denominator` VARCHAR(6) DEFAULT NULL,
    `tolerance_largeur` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `width_value_code` VARCHAR(6) DEFAULT 'mm',
    `width_value_denominator` VARCHAR(6) DEFAULT NULL,
    `largeur` DOUBLE PRECISION DEFAULT 0 NOT NULL
)
SQL);
        $this->insert('component_quality_values', [
            'id',
            'id_component',
            'section',
            'traction',
            'tolerance_traction',
            'obligation_traction',
            'hauteur',
            'tolerance_hauteur',
            'obligation_hauteur',
            'largeur',
            'tolerance_largeur',
            'obligation_largeur'
        ]);
        $this->addQuery(<<<'SQL'
UPDATE `component_quality_values`
SET `id_component` = (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `component_quality_values`.`id_component`)
SQL);
        $this->addQuery(<<<'SQL'
ALTER TABLE `component_quality_values`
    CHANGE `id_component` `component_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `hauteur` `height_value_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CHANGE `height_tolerance_code` `height_tolerance_code` VARCHAR(6) DEFAULT NULL,
    CHANGE `height_value_code` `height_value_code` VARCHAR(6) DEFAULT NULL,
    CHANGE `largeur` `width_value_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CHANGE `obligation_hauteur` `height_required` BOOLEAN DEFAULT TRUE NOT NULL,
    CHANGE `obligation_largeur` `width_required` BOOLEAN DEFAULT TRUE NOT NULL,
    CHANGE `obligation_traction` `tensile_required` BOOLEAN DEFAULT TRUE NOT NULL,
    CHANGE `section_code` `section_code` VARCHAR(6) DEFAULT NULL,
    CHANGE `section` `section_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CHANGE `tensile_tolerance_code` `tensile_tolerance_code` VARCHAR(6) DEFAULT NULL,
    CHANGE `tensile_value_code` `tensile_value_code` VARCHAR(6) DEFAULT NULL,
    CHANGE `tolerance_hauteur` `height_tolerance_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CHANGE `tolerance_largeur` `width_tolerance_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CHANGE `tolerance_traction` `tensile_tolerance_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CHANGE `traction` `tensile_value_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CHANGE `width_tolerance_code` `width_tolerance_code` VARCHAR(6) DEFAULT NULL,
    CHANGE `width_value_code` `width_value_code` VARCHAR(6) DEFAULT NULL,
    ADD CONSTRAINT `IDX_648B6870E2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`)
SQL);
        $this->addQuery('RENAME TABLE `component_quality_values` TO `component_reference_value`');
    }

    private function upComponents(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `component` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `poid_cu` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `id_componentstatus` INT NOT NULL DEFAULT 1,
    `customcode` VARCHAR(16) DEFAULT NULL,
    `endOfLife` DATE DEFAULT NULL,
    `id_component_subfamily` INT UNSIGNED NOT NULL,
    `volume_previsionnel` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `indice` VARCHAR(5) NOT NULL DEFAULT '0',
    `gestion_stock` BOOLEAN DEFAULT FALSE NOT NULL,
    `fabricant` VARCHAR(255) DEFAULT NULL,
    `fabricant_reference` VARCHAR(255) DEFAULT NULL,
    `stock_minimum` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `designation` VARCHAR(255) NOT NULL,
    `need_joint` BOOLEAN DEFAULT 0 NOT NULL,
    `info_public` TEXT DEFAULT NULL,
    `info_commande` TEXT DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    `id_unit` INT UNSIGNED NOT NULL,
    `weight` DOUBLE PRECISION DEFAULT 0 NOT NULL
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
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `copper_weight_code` VARCHAR(6) DEFAULT NULL,
    `copper_weight_denominator` VARCHAR(6) DEFAULT NULL,
    `copper_weight_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` ENUM('agreed', 'blocked', 'disabled', 'draft', 'under_exemption') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:component_current_place)',
    `customs_code` VARCHAR(16) DEFAULT NULL,
    `end_of_life` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `family_id` INT UNSIGNED NOT NULL,
    `forecast_volume_code` VARCHAR(6) DEFAULT NULL,
    `forecast_volume_denominator` VARCHAR(6) DEFAULT NULL,
    `forecast_volume_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `index` VARCHAR(5) DEFAULT '0' NOT NULL,
    `manufacturer` VARCHAR(255) DEFAULT NULL,
    `manufacturer_code` VARCHAR(255) DEFAULT NULL,
    `managed_stock` BOOLEAN DEFAULT FALSE NOT NULL,
    `min_stock_code` VARCHAR(6) DEFAULT NULL,
    `min_stock_denominator` VARCHAR(6) DEFAULT NULL,
    `min_stock_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `need_gasket` BOOLEAN DEFAULT 0 NOT NULL,
    `notes` TEXT,
    `order_info` TEXT,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `ppm_rate` SMALLINT UNSIGNED DEFAULT 10 NOT NULL,
    `unit_id` INT UNSIGNED NOT NULL,
    `weight_code` VARCHAR(6) DEFAULT NULL,
    `weight_denominator` VARCHAR(6) DEFAULT NULL,
    `weight_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
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
WHERE `component_old`.`statut` = 0
SQL);
        $this->addQuery('DROP TABLE `component_old`');
    }

    private function upComponentSupplierPrices(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `component_supplier_price` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_component_supplier` INT UNSIGNED DEFAULT NULL,
    `price` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `quantity` INT UNSIGNED DEFAULT NULL,
    `is_oem` BOOLEAN DEFAULT FALSE NOT NULL,
    `texte` TEXT DEFAULT NULL,
    `refsupplier` VARCHAR(255) DEFAULT NULL
)
SQL);
        $this->insert('component_supplier_price', [
            'id',
            'statut',
            'id_component_supplier',
            'price',
            'quantity',
            'is_oem',
            'texte',
            'refsupplier'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `supplier_component_price` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `component_id` INT UNSIGNED DEFAULT NULL,
    `price_code` VARCHAR(6) DEFAULT NULL,
    `price_denominator` VARCHAR(6) DEFAULT NULL,
    `price_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `quantity_code` VARCHAR(6) DEFAULT NULL,
    `quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_6CCD4783E2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `supplier_component` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `supplier_component_price` (
    `component_id`,
    `price_code`,
    `price_value`,
    `quantity_code`,
    `quantity_value`,
    `ref`
) SELECT
    (SELECT `supplier_component`.`id` FROM `supplier_component` WHERE `supplier_component`.`old_id` = `component_supplier_price`.`id_component_supplier`),
    'EUR',
    `price`,
    (
        SELECT `unit`.`code`
        FROM `unit`
        WHERE `unit`.`id` = (
            SELECT `component`.`unit_id`
            FROM `component`
            WHERE `component`.`id` = (
                SELECT `supplier_component`.`component_id` FROM `supplier_component` WHERE `supplier_component`.`old_id` = `component_supplier_price`.`id_component_supplier`
            )
        )
    ),
    `quantity`,
    `refsupplier`
FROM `component_supplier_price`
WHERE `component_supplier_price`.`statut` = 0
AND EXISTS (SELECT `supplier_component`.`id` FROM `supplier_component` WHERE `supplier_component`.`old_id` = `component_supplier_price`.`id_component_supplier`)
SQL);
        $this->addQuery('DROP TABLE `component_supplier_price`');
    }

    private function upContacts(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `contact` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_customer` INT UNSIGNED DEFAULT NULL,
    `id_supplier` INT UNSIGNED DEFAULT NULL,
    `nom` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(255) DEFAULT NULL,
    `prenom` VARCHAR(255) DEFAULT NULL,
    `address1` TEXT,
    `address2` TEXT,
    `zip` VARCHAR(255) DEFAULT NULL,
    `city` VARCHAR(255) DEFAULT NULL,
    `id_country` INT UNSIGNED DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `mobile` VARCHAR(255) DEFAULT NULL
)
SQL);
        $this->insert('contact', [
            'id',
            'statut',
            'id_customer',
            'id_supplier',
            'nom',
            'phone',
            'prenom',
            'address1',
            'address2',
            'zip',
            'city',
            'id_country',
            'email',
            'mobile'
        ]);
        $this->addQuery('RENAME TABLE `contact` TO `contact_old`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `customer_contact` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `address_address` VARCHAR(80) DEFAULT NULL,
    `address_address2` VARCHAR(60) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(60) DEFAULT NULL,
    `address_phone_number` VARCHAR(255) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `default` BOOLEAN DEFAULT FALSE NOT NULL,
    `kind` ENUM('comptabilité', 'chiffrage', 'direction', 'ingénierie', 'fabrication', 'achat', 'qualité', 'commercial', 'approvisionnement') DEFAULT 'comptabilité' NOT NULL COMMENT '(DC2Type:contact_type)',
    `mobile` VARCHAR(255) DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL,
    `society_id` INT UNSIGNED NOT NULL,
    `surname` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_50BF4286E6389D24` FOREIGN KEY (`society_id`) REFERENCES `customer` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `customer_contact` (
    `old_id`,
    `address_address`,
    `address_address2`,
    `address_city`,
    `address_country`,
    `address_email`,
    `address_phone_number`,
    `address_zip_code`,
    `name`,
    `mobile`,
    `society_id`,
    `surname`
) SELECT
    `id`,
    `address1`,
    `address2`,
    `city`,
    (SELECT UCASE(`country`.`code`) FROM `country` WHERE `country`.`id` = `contact_old`.`id_country`),
    `email`,
    `phone`,
    `zip`,
    `prenom`,
    `mobile`,
    (SELECT `customer`.`id` FROM `customer` WHERE `customer`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `contact_old`.`id_customer`)),
    `nom`
FROM `contact_old`
WHERE `statut` = 0
AND EXISTS (SELECT `customer`.`id` FROM `customer` WHERE `customer`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `contact_old`.`id_customer`))
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `supplier_contact` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `address_address` VARCHAR(80) DEFAULT NULL,
    `address_address2` VARCHAR(60) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(60) DEFAULT NULL,
    `address_phone_number` VARCHAR(255) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `default` BOOLEAN DEFAULT FALSE NOT NULL,
    `kind` ENUM('comptabilité', 'chiffrage', 'direction', 'ingénierie', 'fabrication', 'achat', 'qualité', 'commercial', 'approvisionnement') DEFAULT 'comptabilité' NOT NULL COMMENT '(DC2Type:contact_type)',
    `mobile` VARCHAR(255) DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL,
    `society_id` INT UNSIGNED NOT NULL,
    `surname` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_CD079227E6389D24` FOREIGN KEY (`society_id`) REFERENCES `supplier` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `supplier_contact` (
    `address_address`,
    `address_address2`,
    `address_city`,
    `address_country`,
    `address_email`,
    `address_phone_number`,
    `address_zip_code`,
    `name`,
    `mobile`,
    `society_id`,
    `surname`
) SELECT
    `address1`,
    `address2`,
    `city`,
    (SELECT UCASE(`country`.`code`) FROM `country` WHERE `country`.`id` = `contact_old`.`id_country`),
    `email`,
    `phone`,
    `zip`,
    `prenom`,
    `mobile`,
    (SELECT `supplier`.`id` FROM `supplier` WHERE `supplier`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `contact_old`.`id_supplier`)),
    `nom`
FROM `contact_old`
WHERE `statut` = 0
AND EXISTS (SELECT `supplier`.`id` FROM `supplier` WHERE `supplier`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `contact_old`.`id_supplier`))
SQL);
        $this->addQuery('DROP TABLE `contact_old`');
    }

    private function upCountries(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `country` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN NOT NULL,
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
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `command` CHAR(20) NOT NULL COMMENT '(DC2Type:char)',
    `last` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `next` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `period` CHAR(6) NOT NULL COMMENT '(DC2Type:char)'
)
SQL);
    }

    private function upCurrencies(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `currency` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    `active` BOOLEAN DEFAULT FALSE NOT NULL,
    `base` DOUBLE PRECISION DEFAULT 1 NOT NULL,
    `code` CHAR(3) NOT NULL COMMENT '(DC2Type:char)',
    `parent_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_6956883F727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `currency` (`id`)
)
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
    `statut` BOOLEAN NOT NULL,
    `code` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('customcode', ['id', 'statut', 'code']);
    }

    private function upCustomerAddresses(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `address` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` BOOLEAN DEFAULT FALSE NOT NULL,
  `id_customer` INT UNSIGNED DEFAULT NULL,
  `nom` VARCHAR(255) NOT NULL,
  `address1` TEXT NOT NULL,
  `address2` TEXT DEFAULT NULL,
  `zip` VARCHAR(255) NOT NULL,
  `city` VARCHAR(255) NOT NULL,
  `typeaddress` VARCHAR(255) DEFAULT NULL,
  `id_country` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('address', [
            'id',
            'statut',
            'id_customer',
            'nom',
            'address1',
            'address2',
            'zip',
            'city',
            'typeaddress',
            'id_country'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `customer_address` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `address_address` VARCHAR(80) DEFAULT NULL,
    `address_address2` VARCHAR(60) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(60) DEFAULT NULL,
    `address_phone_number` VARCHAR(18) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `customer_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `type` ENUM('billing', 'delivery') NOT NULL COMMENT '(DC2Type:customer_address_type)',
    CONSTRAINT `IDX_1193CB3F9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`)
)
SQL);
        $this->insertCustomerAddresses('billing', 'factur');
        $this->insertCustomerAddresses('delivery', 'livraison');
        $this->addQuery('DROP TABLE `address`');
    }

    private function upCustomerEvents(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `customer_event` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `customer_id` INT UNSIGNED NOT NULL,
    `date` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `done` BOOLEAN DEFAULT FALSE NOT NULL,
    `kind` VARCHAR(255) DEFAULT 'holiday' NOT NULL,
    `managing_company_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_F59B7F9CE7E23CE8` FOREIGN KEY (`managing_company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_F59B7F9C9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`)
)
SQL);
    }

    private function upCustomerOrders(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `ordercustomer` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_customer` INT UNSIGNED DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    `id_ordercustomerstatus` INT UNSIGNED DEFAULT NULL,
    `id_address` INT UNSIGNED DEFAULT NULL,
    `id_society` INT UNSIGNED DEFAULT NULL,
    `info_public` TEXT DEFAULT NULL
)
SQL);
        $this->insert('ordercustomer', [
            'id',
            'statut',
            'id_customer',
            'ref',
            'id_ordercustomerstatus',
            'id_address',
            'id_society',
            'info_public'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `customer_order` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `billed_to_id` INT UNSIGNED DEFAULT NULL,
    `company_id` INT UNSIGNED DEFAULT NULL,
    `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` ENUM('agreed', 'blocked', 'closed', 'disabled', 'draft', 'partially_delivered', 'to_validate') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:customer_order_current_place)',
    `customer_id` INT UNSIGNED DEFAULT NULL,
    `destination_id` INT UNSIGNED DEFAULT NULL,
    `kind` ENUM('EI', 'Prototype', 'Série', 'Pièce de rechange') DEFAULT 'Prototype' NOT NULL COMMENT '(DC2Type:product_kind)',
    `notes` VARCHAR(255) DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_3B1CE6A3994CB78` FOREIGN KEY (`billed_to_id`) REFERENCES `customer_address` (`id`),
    CONSTRAINT `IDX_3B1CE6A3979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_3B1CE6A39395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
    CONSTRAINT `IDX_3B1CE6A3816C6140` FOREIGN KEY (`destination_id`) REFERENCES `customer_address` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `customer_order` (`old_id`, `billed_to_id`, `company_id`, `current_place_name`, `customer_id`, `kind`, `notes`, `ref`)
SELECT
    `id`,
    (SELECT `customer_address`.`id` FROM `customer_address` WHERE `customer_address`.`old_id` = `ordercustomer`.`id_address` AND `customer_address`.`type` = 'billing'),
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `ordercustomer`.`id_society`)),
    CASE
        WHEN `id_ordercustomerstatus` IN (2, 13) THEN 'to_validate'
        WHEN `id_ordercustomerstatus` IN (3, 4, 5, 6) THEN 'agreed'
        WHEN `id_ordercustomerstatus` IN (7, 9) THEN 'partially_delivered'
        WHEN `id_ordercustomerstatus` IN (8, 10) THEN 'closed'
        WHEN `id_ordercustomerstatus` = 11 THEN 'disabled'
        WHEN `id_ordercustomerstatus` = 12 THEN 'blocked'
        ELSE 'draft'
    END,
    (SELECT `customer`.`id` FROM `customer` WHERE `customer`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `ordercustomer`.`id_customer`)),
    'Série',
    `info_public`,
    `ref`
FROM `ordercustomer`
WHERE `statut` = 0
AND EXISTS (SELECT `customer`.`id` FROM `customer` WHERE `customer`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `ordercustomer`.`id_customer`))
SQL);
        $this->addQuery('DROP TABLE `ordercustomer`');
    }

    private function upCustomerProductPrices(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `product_customer_price` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_product_customer` INT UNSIGNED DEFAULT NULL,
    `price` DOUBLE PRECISION DEFAULT 0 DEFAULT NULL,
    `quantity` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('product_customer_price', ['id', 'statut', 'id_product_customer', 'price', 'quantity']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `customer_product_price` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `price_code` VARCHAR(6) DEFAULT NULL,
    `price_denominator` VARCHAR(6) DEFAULT NULL,
    `price_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `product_id` INT UNSIGNED DEFAULT NULL,
    `quantity_code` VARCHAR(6) DEFAULT NULL,
    `quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CONSTRAINT `IDX_FF129864584665A` FOREIGN KEY (`product_id`) REFERENCES `product_customer` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `customer_product_price` (
    `price_code`,
    `price_value`,
    `product_id`,
    `quantity_code`,
    `quantity_value`
) SELECT
    'EUR',
    `price`,
    (SELECT `product_customer`.`id` FROM `product_customer` WHERE `product_customer`.`old_id` = `product_customer_price`.`id_product_customer`),
    (
        SELECT `unit`.`code`
        FROM `unit`
        WHERE `unit`.`id` = (
            SELECT `product`.`unit_id`
            FROM `product`
            WHERE `product`.`id` = (
                SELECT `product_customer`.`product_id` FROM `product_customer` WHERE `product_customer`.`old_id` = `product_customer_price`.`id_product_customer`
            )
        )
    ),
    `quantity`
FROM `product_customer_price`
WHERE `statut` = 0
AND EXISTS (SELECT `product_customer`.`id` FROM `product_customer` WHERE `product_customer`.`old_id` = `product_customer_price`.`id_product_customer`)
SQL);
        $this->addQuery('DROP TABLE `product_customer_price`');
    }

    private function upCustomerProducts(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `product_customer` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_product` INT UNSIGNED NOT NULL,
    `id_customer` INT UNSIGNED NOT NULL
)
SQL);
        $this->insert('product_customer', ['id', 'statut', 'id_product', 'id_customer']);
        $this->addQuery('RENAME TABLE `product_customer` TO `product_customer_old`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `product_customer` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `customer_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_4A89E49E9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
    CONSTRAINT `IDX_4A89E49E4584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `product_customer` (`old_id`, `customer_id`, `product_id`)
SELECT
    `id`,
    (
        SELECT `customer`.`id`
        FROM `customer`
        WHERE `customer`.`society_id` = (
            SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `product_customer_old`.`id_customer`
        )
    ),
    (SELECT `product`.`id` FROM `product` WHERE `product`.`old_id` = `product_customer_old`.`id_product`)
FROM `product_customer_old`
WHERE `statut` = 0
AND EXISTS (
    SELECT `customer`.`id`
    FROM `customer`
    WHERE `customer`.`society_id` = (
        SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `product_customer_old`.`id_customer`
    )
)
AND (SELECT `product`.`id` FROM `product` WHERE `product`.`old_id` = `product_customer_old`.`id_product`)
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `product_company` (
    `product_id` INT UNSIGNED NOT NULL,
    `company_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_9E6612FF4584665A` FOREIGN KEY (`product_id`) REFERENCES `product_customer` (`id`) ON DELETE CASCADE,
    CONSTRAINT `IDX_9E6612FF979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE,
    PRIMARY KEY(product_id, company_id)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `product_company` (`product_id`, `company_id`)
SELECT
    (
        SELECT `product_customer`.`id`
        FROM `product_customer`
        WHERE `product_customer`.`old_id` = `product_customer_old`.`id`
    ),
    (
        SELECT `company`.`id`
        FROM `company`
        WHERE `company`.`society_id` = (
            SELECT `society`.`id`
            FROM `society`
            WHERE `society`.`old_id` = (
                SELECT `product`.`id_society` FROM `product` WHERE `product`.`old_id` = `product_customer_old`.`id_product`
            )
        )
    )
FROM `product_customer_old`
WHERE `statut` = 0
AND EXISTS (
    SELECT `product_customer`.`id`
    FROM `product_customer`
    WHERE `product_customer`.`old_id` = `product_customer_old`.`id`
)
AND EXISTS (
    SELECT `company`.`id`
    FROM `company`
    WHERE `company`.`society_id` = (
        SELECT `society`.`id`
        FROM `society`
        WHERE `society`.`old_id` = (
            SELECT `product`.`id_society` FROM `product` WHERE `product`.`old_id` = `product_customer_old`.`id_product`
        )
    )
)
SQL);
        $this->addQuery('DROP TABLE `product_customer_old`');
    }

    private function upDeliveryNotes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `deliveryform` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deliveryform_number` INT UNSIGNED DEFAULT NULL,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `date_depart` DATE NOT NULL,
    `id_carrier` INT UNSIGNED DEFAULT NULL,
    `tracking_number` VARCHAR(255) DEFAULT NULL,
    `id_customer` INT UNSIGNED DEFAULT NULL,
    `id_ordercustomer` INT UNSIGNED DEFAULT NULL,
    `invoice_number` INT DEFAULT NULL,
    `supplement_fret` INT UNSIGNED DEFAULT NULL,
    `id_society` INT UNSIGNED DEFAULT NULL,
    `no_invoice` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('deliveryform', [
            'id',
            'deliveryform_number',
            'statut',
            'date_depart',
            'id_carrier',
            'tracking_number',
            'id_customer',
            'id_ordercustomer',
            'invoice_number',
            'supplement_fret',
            'id_society',
            'no_invoice'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `invoicecustomer_deliveryform` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_invoicecustomer` INT UNSIGNED DEFAULT NULL,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_deliveryform` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('invoicecustomer_deliveryform', ['id', 'id_invoicecustomer', 'statut', 'id_deliveryform']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `delivery_note` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `bill_id` INT UNSIGNED DEFAULT NULL,
    `company_id` INT UNSIGNED DEFAULT NULL,
    `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` ENUM('draft', 'ready_to_sent', 'sent') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:delivery_note_current_place)',
    `date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `freight_surcharge_code` VARCHAR(6) DEFAULT NULL,
    `freight_surcharge_denominator` VARCHAR(6) DEFAULT NULL,
    `freight_surcharge_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `non_billable` BOOLEAN DEFAULT FALSE NOT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_1E21328E1A8C12F5` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`id`),
    CONSTRAINT `IDX_1E21328E979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `delivery_note` (
    `bill_id`,
    `company_id`,
    `current_place_name`,
    `date`,
    `freight_surcharge_code`,
    `freight_surcharge_value`,
    `non_billable`,
    `ref`
) SELECT
    (
        SELECT `bill`.`id`
        FROM `bill`
        WHERE `bill`.`ref` = `deliveryform`.`invoice_number`
        AND `bill`.`old_id` IN (
            SELECT `invoicecustomer_deliveryform`.`id_invoicecustomer`
            FROM `invoicecustomer_deliveryform`
            WHERE `invoicecustomer_deliveryform`.`id_deliveryform` = `deliveryform`.`id`
            AND `invoicecustomer_deliveryform`.`statut` = 0
        )
    ),
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `deliveryform`.`id_society`)),
    'sent',
    `date_depart`,
    'EUR',
    `supplement_fret`,
    `no_invoice`,
    `deliveryform_number`
FROM `deliveryform`
WHERE `statut` = 0
SQL);
        $this->addQuery('DROP TABLE `deliveryform`');
        $this->addQuery('DROP TABLE `invoicecustomer_deliveryform`');
    }

    private function upEmployeeEvents(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee_event` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `date_event` DATE DEFAULT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `id_employee` INT UNSIGNED DEFAULT NULL,
    `id_motif` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('employee_event', ['id', 'date_event', 'description', 'id_employee', 'id_motif']);
        $this->addQuery('RENAME TABLE `employee_event` TO `old_employee_event`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee_event` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `date` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `done` BOOLEAN DEFAULT FALSE NOT NULL,
    `employee_id` INT UNSIGNED DEFAULT NULL,
    `managing_company_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL,
    `type_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_D3A307DE8C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`),
    CONSTRAINT `IDX_D3A307DEC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `event_type` (`id`),
    CONSTRAINT `IDX_D3A307DEE7E23CE8` FOREIGN KEY (`managing_company_id`) REFERENCES `company` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `employee_event` (`date`, `employee_id`, `managing_company_id`, `name`, `type_id`)
SELECT
    `date_event`,
    (SELECT `employee`.`id` FROM `employee` WHERE `employee`.`old_id` = `old_employee_event`.`id_employee`),
    (SELECT `employee`.`company_id` FROM `employee` WHERE `employee`.`old_id` = `old_employee_event`.`id_employee`),
    `description`,
    (SELECT `event_type`.`id` FROM `event_type` WHERE `event_type`.`id` = `old_employee_event`.`id_motif`)
FROM `old_employee_event`
WHERE EXISTS (SELECT `employee`.`id` FROM `employee` WHERE `employee`.`old_id` = `old_employee_event`.`id_employee`)
AND EXISTS (SELECT `event_type`.`id` FROM `event_type` WHERE `event_type`.`id` = `old_employee_event`.`id_motif`)
SQL);
        $this->addQuery('DROP TABLE `old_employee_event`');
    }

    private function upEmployees(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `status` BOOLEAN DEFAULT FALSE NOT NULL,
    `matricule` INT UNSIGNED DEFAULT NULL,
    `nom` VARCHAR(255) DEFAULT NULL,
    `prenom` VARCHAR(255) DEFAULT NULL,
    `address` VARCHAR(255) DEFAULT NULL,
    `code_postal` INT UNSIGNED DEFAULT NULL,
    `ville` VARCHAR(255) DEFAULT NULL,
    `id_phone_prefix` INT UNSIGNED DEFAULT NULL,
    `tel` VARCHAR(255) DEFAULT NULL,
    `nom_pers_a_contacter` VARCHAR(255) NOT NULL,
    `prenom_pers_a_contacter` VARCHAR(255) NOT NULL,
    `id_phone_prefix_pers_a_contacter` INT UNSIGNED DEFAULT NULL,
    `tel_pers_a_contacter` VARCHAR(255) NOT NULL,
    `d_entree` DATE DEFAULT NULL,
    `n_secu` VARCHAR(255) DEFAULT NULL,
    `d_naissance` DATE DEFAULT NULL,
    `lieu_de_naissance` VARCHAR(255) DEFAULT NULL,
    `lvl_etude` VARCHAR(255) DEFAULT NULL,
    `id_role` INT UNSIGNED DEFAULT NULL,
    `id_role2` INT DEFAULT NULL,
    `id_role3` INT DEFAULT NULL,
    `id_resp` INT UNSIGNED DEFAULT NULL,
    `id_society` INT UNSIGNED DEFAULT NULL,
    `is_former` BOOLEAN DEFAULT FALSE NOT NULL,
    `user_gp` BOOLEAN DEFAULT FALSE NOT NULL,
    `login` VARCHAR(255) DEFAULT NULL,
    `password` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `notes` TEXT,
    `sexe` VARCHAR(1) DEFAULT NULL,
    `situation` VARCHAR(255) DEFAULT NULL,
    `initials` VARCHAR(5) DEFAULT NULL,
    `matricule_pointage` VARCHAR(200) DEFAULT NULL,
    `user_account` INT UNSIGNED DEFAULT NULL,
    `team` enum('matin','journée','soir') DEFAULT NULL
)
SQL);
        $this->insert('employee', [
            'id',
            'status',
            'matricule',
            'nom',
            'prenom',
            'address',
            'code_postal',
            'ville',
            'id_phone_prefix',
            'tel',
            'nom_pers_a_contacter',
            'prenom_pers_a_contacter',
            'id_phone_prefix_pers_a_contacter',
            'tel_pers_a_contacter',
            'd_entree',
            'n_secu',
            'd_naissance',
            'lieu_de_naissance',
            'lvl_etude',
            'id_role',
            'id_role2',
            'id_role3',
            'id_resp',
            'id_society',
            'is_former',
            'user_gp',
            'login',
            'password',
            'email',
            'notes',
            'sexe',
            'situation',
            'initials',
            'matricule_pointage',
            'user_account',
            'team'
        ]);
        $this->addQuery('RENAME TABLE `employee` TO `employee_old`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED DEFAULT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `address_address` VARCHAR(80) DEFAULT NULL,
    `address_address2` VARCHAR(60) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(60) DEFAULT NULL,
    `address_phone_number` VARCHAR(18) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `birthday` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `birth_city` VARCHAR(255) DEFAULT NULL,
    `company_id` INT UNSIGNED DEFAULT NULL,
    `id_society` INT UNSIGNED DEFAULT NULL,
    `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` ENUM('blocked', 'disabled', 'enabled', 'warning') DEFAULT 'warning' NOT NULL COMMENT '(DC2Type:employee_engine_current_place)',
    `emb_roles_roles` TEXT NOT NULL COMMENT '(DC2Type:simple_array)',
    `entry_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `gender` ENUM('female', 'male') DEFAULT 'male' COMMENT '(DC2Type:gender_place)',
    `initials` VARCHAR(255) NOT NULL,
    `level_of_study` VARCHAR(255) DEFAULT NULL,
    `manager_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(30) NOT NULL,
    `notes` VARCHAR(255) DEFAULT NULL,
    `password` CHAR(60) DEFAULT NULL COMMENT '(DC2Type:char)',
    `plain_password` VARCHAR(255) DEFAULT NULL,
    `situation` ENUM('married', 'single', 'windowed') DEFAULT 'single' COMMENT '(DC2Type:situation_place)',
    `social_security_number` VARCHAR(255) DEFAULT NULL,
    `surname` VARCHAR(255) NOT NULL,
    `team_id` INT UNSIGNED DEFAULT NULL,
    `time_card` VARCHAR(255) DEFAULT NULL,
    `user_enabled` BOOLEAN DEFAULT FALSE NOT NULL,
    `username` VARCHAR(20) DEFAULT NULL,
    `nom_pers_a_contacter` VARCHAR(255) DEFAULT NULL,
    `prenom_pers_a_contacter` VARCHAR(255) DEFAULT NULL,
    `id_phone_prefix_pers_a_contacter` INT UNSIGNED DEFAULT NULL,
    `tel_pers_a_contacter` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_5D9F75A1979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_5D9F75A1296CD8AE` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`)
)
SQL);
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
        $this->addQuery(sprintf(
            <<<'SQL'
INSERT INTO `employee` (`emb_roles_roles`, `initials`, `name`, `password`, `surname`, `username`)
VALUES (%s, 'super', 'Super', %s, 'SUPER', 'super')
SQL,
            /** @phpstan-ignore-next-line */
            $this->connection->quote(implode(',', $user->getRoles())),
            /** @phpstan-ignore-next-line */
            $this->connection->quote($user->getPassword())
        ));
        $this->addQuery(<<<'SQL'
INSERT INTO `employee` (
    `old_id`,
    `address_address`,
    `address_city`,
    `address_country`,
    `address_email`,
    `address_phone_number`,
    `address_zip_code`,
    `birthday`,
    `birth_city`,
    `company_id`,
    `id_society`,
    `emb_roles_roles`,
    `entry_date`,
    `gender`,
    `initials`,
    `level_of_study`,
    `manager_id`,
    `name`,
    `password`,
    `situation`,
    `social_security_number`,
    `surname`,
    `user_enabled`,
    `username`,
    `nom_pers_a_contacter`,
    `prenom_pers_a_contacter`,
    `id_phone_prefix_pers_a_contacter`,
    `tel_pers_a_contacter`
) SELECT
    `id`,
    `address`,
    `ville`,
    (SELECT UCASE(`country`.`code`) FROM `country` WHERE `country`.`id` = `employee_old`.`id_phone_prefix`),
    `email`,
    `tel`,
    `code_postal`,
    `d_naissance`,
    `lieu_de_naissance`,
    (
        SELECT `company`.`id`
        FROM `company`
        WHERE `company`.`society_id` = (
            SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `employee_old`.`id_society`
        )
    ),
    `id_society`,
    CASE
        WHEN `id_role` = 100 THEN 'ROLE_ACCOUNTING_ADMIN,ROLE_HR_ADMIN,ROLE_IT_ADMIN,ROLE_LOGISTICS_ADMIN,ROLE_MAINTENANCE_ADMIN,ROLE_MANAGEMENT_ADMIN,ROLE_PRODUCTION_ADMIN,ROLE_PROJECT_ADMIN,ROLE_PURCHASE_ADMIN,ROLE_QUALITY_ADMIN,ROLE_SELLING_ADMIN,ROLE_USER'
        WHEN `id_role` < 300 OR (`id_role` % 100 = 0 AND `id_role` NOT IN (300, 400, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500)) THEN 'ROLE_ACCOUNTING_ADMIN,ROLE_HR_ADMIN,ROLE_LOGISTICS_ADMIN,ROLE_MAINTENANCE_ADMIN,ROLE_MANAGEMENT_ADMIN,ROLE_PRODUCTION_ADMIN,ROLE_PROJECT_ADMIN,ROLE_PURCHASE_ADMIN,ROLE_QUALITY_ADMIN,ROLE_SELLING_ADMIN,ROLE_USER'
        WHEN `id_role` < 311 THEN 'ROLE_PRODUCTION_ADMIN,ROLE_QUALITY_ADMIN,ROLE_USER'
        WHEN `id_role` < 400 THEN 'ROLE_PRODUCTION_WRITER,ROLE_QUALITY_WRITER,ROLE_USER'
        WHEN `id_role` = 400 THEN 'ROLE_HR_ADMIN,ROLE_USER'
        WHEN `id_role` < 500 THEN 'ROLE_HR_WRITER,ROLE_USER'
        WHEN `id_role` < 600 THEN 'ROLE_HR_WRITER,ROLE_LOGISTICS_WRITER,ROLE_PRODUCTION_READER,ROLE_PROJECT_READER,ROLE_PURCHASE_WRITER,ROLE_QUALITY_READER,ROLE_SELLING_WRITER,ROLE_USER'
        WHEN `id_role` IN (600, 1000) THEN 'ROLE_PRODUCTION_ADMIN,ROLE_USER'
        WHEN `id_role` < 700 OR (`id_role` > 1000 AND `id_role` < 1100) THEN 'ROLE_PRODUCTION_WRITER,ROLE_USER'
        WHEN `id_role` = 700 THEN 'ROLE_LOGISTICS_ADMIN,ROLE_PRODUCTION_ADMIN,ROLE_PROJECT_ADMIN,ROLE_PURCHASE_ADMIN,ROLE_SELLING_ADMIN,ROLE_USER'
        WHEN `id_role` < 800 THEN 'ROLE_LOGISTICS_WRITER,ROLE_PRODUCTION_WRITER,ROLE_PROJECT_WRITER,ROLE_PURCHASE_WRITER,ROLE_SELLING_WRITER,ROLE_USER'
        WHEN `id_role` < 910 THEN 'ROLE_PROJECT_ADMIN,ROLE_PURCHASE_ADMIN,ROLE_SELLING_ADMIN,ROLE_USER'
        WHEN `id_role` < 1000 THEN 'ROLE_PROJECT_WRITER,ROLE_PURCHASE_WRITER,ROLE_SELLING_WRITER,ROLE_USER'
        WHEN `id_role` = 1100 THEN 'ROLE_PRODUCTION_ADMIN,ROLE_PROJECT_ADMIN,ROLE_QUALITY_ADMIN,ROLE_USER'
        WHEN `id_role` < 1300 THEN 'ROLE_PRODUCTION_WRITER,ROLE_PROJECT_WRITER,ROLE_QUALITY_WRITER,ROLE_USER'
        WHEN `id_role` < 1320 THEN 'ROLE_PRODUCTION_ADMIN,ROLE_PROJECT_ADMIN,ROLE_QUALITY_ADMIN,ROLE_SELLING_ADMIN,ROLE_USER'
        WHEN `id_role` < 1400 THEN 'ROLE_PRODUCTION_WRITER,ROLE_PROJECT_WRITER,ROLE_QUALITY_WRITER,ROLE_SELLING_WRITER,ROLE_USER'
        ELSE 'ROLE_USER'
    END,
    `d_entree`,
    IF(`sexe` = 'F', 'female', 'male'),
    IF_EMPTY(`initials`, CONCAT(UCASE(LEFT(`nom`, 2)), '-', UCFIRST(LEFT(`prenom`, 2)))),
    `lvl_etude`,
    `id_resp`,
    `prenom`,
    `password`,
    CASE
        WHEN `situation` LIKE 'mari%' THEN 'married'
        WHEN `situation` LIKE 've%' THEN 'windowed'
        ELSE 'single'
    END,
    `n_secu`,
    `nom`,
    `user_gp`,
    `login`,
    `nom_pers_a_contacter`,
    `prenom_pers_a_contacter`,
    `id_phone_prefix_pers_a_contacter`,
    `tel_pers_a_contacter`
FROM `employee_old`
WHERE `status` = 0
SQL);
        $this->addQuery(<<<'SQL'
UPDATE `employee`
LEFT JOIN `employee` `m` ON `employee`.`manager_id` = `m`.`old_id`
SET `employee`.`manager_id` = `m`.`id`
SQL);
        $this->addQuery('ALTER TABLE `employee` ADD CONSTRAINT `IDX_5D9F75A1783E3463` FOREIGN KEY (`manager_id`) REFERENCES `employee` (`id`)');
        $this->addQuery('DROP TABLE `employee_old`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `user` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_society` INT UNSIGNED NOT NULL,
    `login` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `nom` VARCHAR(255) NOT NULL,
    `prenom` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('user', [
            'id',
            'statut',
            'id_society',
            'login',
            'password',
            'nom',
            'prenom'
        ]);
        $this->addQuery(<<<'SQL'
UPDATE `employee`
INNER JOIN `user`
    ON `employee`.`name` LIKE `user`.`prenom`
    AND `employee`.`surname` LIKE `user`.`nom`
    AND `employee`.`id_society` = `user`.`id_society`
    AND `user`.`statut` = 0
SET `employee`.`password` = `user`.`password`
AND `employee`.`username` = `user`.`login`
SQL);
        $this->addQuery('DROP TABLE `user`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `token` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `employee_id` INT UNSIGNED NOT NULL,
    `expire_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `token` CHAR(120) NOT NULL COMMENT '(DC2Type:char)',
    CONSTRAINT `IDX_5F37A13B8C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee_contact` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `employee_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(255) DEFAULT NULL,
    `surname` VARCHAR(255) DEFAULT NULL,
    `address_country` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_CC2EB0378C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `employee_contact` (`employee_id`, `name`, `phone`, `surname`, `address_country`)
SELECT
    `id`,
    `prenom_pers_a_contacter`,
    `tel_pers_a_contacter`,
    `nom_pers_a_contacter`,
    (SELECT UCASE(`country`.`code`) FROM `country` WHERE `country`.`id` = `employee`.`id_phone_prefix_pers_a_contacter`)
FROM `employee`
WHERE `prenom_pers_a_contacter` IS NOT NULL
AND `nom_pers_a_contacter` IS NOT NULL
SQL);
        $this->addQuery('ALTER TABLE `employee` DROP `nom_pers_a_contacter`, DROP `prenom_pers_a_contacter`, DROP `id_phone_prefix_pers_a_contacter`, DROP `tel_pers_a_contacter`');
    }

    private function upEngineEvents(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine_event` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` ENUM('agreed', 'closed', 'draft', 'rejected') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:engine_event_current_place)',
    `date` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `done` BOOLEAN DEFAULT FALSE NOT NULL,
    `emergency` TINYINT UNSIGNED DEFAULT 1 COMMENT '(DC2Type:tinyint)',
    `employee_id` INT UNSIGNED DEFAULT NULL,
    `engine_id` INT UNSIGNED DEFAULT NULL,
    `intervention_notes` TEXT DEFAULT NULL,
    `managing_company_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL,
    `notes` VARCHAR(255) DEFAULT NULL,
    `planned_by_id` INT UNSIGNED DEFAULT NULL,
    `type` ENUM('maintenance', 'request') NOT NULL COMMENT '(DC2Type:engine_event_type)',
    CONSTRAINT `IDX_16C6DEEC8C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`),
    CONSTRAINT `IDX_16C6DEECE78C9C0A` FOREIGN KEY (`engine_id`) REFERENCES `engine` (`id`),
    CONSTRAINT `IDX_16C6DEECE7E23CE8` FOREIGN KEY (`managing_company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_16C6DEEC24E29790` FOREIGN KEY (`planned_by_id`) REFERENCES `planning` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine_maintenance_planning` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_maintenance` INT UNSIGNED NOT NULL,
    `id_engine_maintenance` INT UNSIGNED NOT NULL,
    `date_planning` DATE NOT NULL,
    `date_execution` DATE DEFAULT NULL,
    `status_planning` INT UNSIGNED NOT NULL DEFAULT 0,
    `comment` TEXT
)
SQL);
        $this->insert('engine_maintenance_planning', [
            'id',
            'id_maintenance',
            'id_engine_maintenance',
            'date_planning',
            'date_execution',
            'status_planning',
            'comment'
        ]);
        $this->addQuery(<<<'SQL'
INSERT INTO `engine_event` (
    `current_place_name`,
    `date`,
    `done`,
    `engine_id`,
    `managing_company_id`,
    `notes`,
    `planned_by_id`,
    `type`
) SELECT
    IF(`status_planning` = 1, 'agreed', 'closed'),
    `date_planning`,
    `status_planning` != 1,
    (SELECT `engine`.`id` FROM `engine` WHERE `engine`.`id` = `engine_maintenance_planning`.`id_engine_maintenance`),
    (
        SELECT `zone`.`company_id`
        FROM `zone`
        WHERE `zone`.`id` = (
            SELECT `engine`.`zone_id` FROM `engine` WHERE `engine`.`id` = `engine_maintenance_planning`.`id_engine_maintenance`
        )
    ),
    `comment`,
    (SELECT `planning`.`id` FROM `planning` WHERE `planning`.`id` = `engine_maintenance_planning`.`id_maintenance`),
    'maintenance'
FROM `engine_maintenance_planning`
WHERE EXISTS (SELECT `engine`.`id` FROM `engine` WHERE `engine`.`id` = `engine_maintenance_planning`.`id_engine_maintenance`)
AND EXISTS (SELECT `planning`.`id` FROM `planning` WHERE `planning`.`id` = `engine_maintenance_planning`.`id_maintenance`)
SQL);
        $this->addQuery('DROP TABLE `engine_maintenance_planning`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine_request_event` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_society` INT UNSIGNED NOT NULL,
    `id_engine` INT UNSIGNED NOT NULL,
    `commentaire` VARCHAR(255) DEFAULT NULL,
    `commentaire_intervention` VARCHAR(255) DEFAULT NULL,
    `statut` INT UNSIGNED NOT NULL,
    `urgence` TINYINT UNSIGNED NOT NULL,
    `id_user_intervention` INT UNSIGNED NOT NULL,
    `date_intervention` DATETIME DEFAULT NULL
)
SQL);
        $this->insert('engine_request_event', [
            'id',
            'id_society',
            'id_engine',
            'commentaire',
            'commentaire_intervention',
            'statut',
            'urgence',
            'id_user_intervention',
            'date_intervention'
        ]);
        $this->addQuery(<<<'SQL'
INSERT INTO `engine_event` (
    `current_place_name`,
    `date`,
    `done`,
    `emergency`,
    `employee_id`,
    `engine_id`,
    `intervention_notes`,
    `managing_company_id`,
    `notes`,
    `type`
) SELECT
    IF(`statut` = 2, 'closed', 'draft'),
    `date_intervention`,
    `statut` = 2,
    `urgence`,
    (SELECT `employee`.`id` FROM `employee` WHERE `employee`.`old_id` = `engine_request_event`.`id_user_intervention`),
    (SELECT `engine`.`id` FROM `engine` WHERE `engine`.`id` = `engine_request_event`.`id_engine`),
    `commentaire_intervention`,
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `engine_request_event`.`id_society`)),
    `commentaire`,
    'request'
FROM `engine_request_event`
WHERE EXISTS (SELECT `engine`.`id` FROM `engine` WHERE `engine`.`id` = `engine_request_event`.`id_engine`)
SQL);
        $this->addQuery('DROP TABLE `engine_request_event`');
    }

    private function upEngineGroups(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine_group` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `code` VARCHAR(3) NOT NULL,
    `libelle` VARCHAR(35) NOT NULL,
    `id_family_group` INT NOT NULL,
    `organe_securite` BOOLEAN DEFAULT FALSE NOT NULL,
    `type` ENUM('counter-part', 'tool', 'workstation') NOT NULL COMMENT '(DC2Type:engine_type)'
)
SQL);
        $this->insert('engine_group', ['id', 'code', 'libelle', 'id_family_group', 'organe_securite']);
        $this->addQuery('UPDATE `engine_group` SET `libelle` = UCFIRST(`libelle`), `type` = IF(`id_family_group` = 1, \'workstation\', \'tool\')');
        $this->addQuery(<<<'SQL'
ALTER TABLE `engine_group`
    DROP `id_family_group`,
    CHANGE `libelle` `name` VARCHAR(35) NOT NULL,
    CHANGE `organe_securite` `safety_device` BOOLEAN DEFAULT FALSE NOT NULL
SQL);
    }

    private function upEngines(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `marque` VARCHAR(255) DEFAULT NULL,
    `ref` VARCHAR(255) NOT NULL,
    `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `capabilite` VARCHAR(255) DEFAULT NULL,
    `d_entree` DATE DEFAULT NULL,
    `id_engine_group` INT UNSIGNED DEFAULT NULL,
    `operateur_max` INT(10) NOT NULL,
    `nom` VARCHAR(255) NOT NULL,
    `notes` TEXT DEFAULT NULL,
    `type` ENUM('counter-part', 'tool', 'workstation') NOT NULL COMMENT '(DC2Type:engine_type)',
    `zone_id` INT UNSIGNED DEFAULT NULL,
    `reffourn` VARCHAR(255) DEFAULT NULL,
    `date_fabrication` DATE DEFAULT NULL,
    `id_fabricant` INT DEFAULT NULL,
    `numero_serie` VARCHAR(255) DEFAULT NULL
)
SQL);
        $this->insert('engine', [
            'id',
            'marque',
            'ref',
            'capabilite',
            'd_entree',
            'id_engine_group',
            'operateur_max',
            'nom',
            'notes',
            'reffourn',
            'date_fabrication',
            'id_fabricant',
            'numero_serie'
        ]);
        $this->addQuery('CREATE TABLE `computer_engine` (`id_engine` INT UNSIGNED DEFAULT NULL, `id_society_zone` INT UNSIGNED DEFAULT NULL)');
        $this->insert('computer_engine', ['id_engine', 'id_society_zone']);
        $this->addQuery(<<<'SQL'
UPDATE `engine`
SET `capabilite` = CASE
    WHEN `capabilite` = 1 THEN 'enabled'
    WHEN `capabilite` = 2 THEN 'warning'
    WHEN `capabilite` = 3 THEN 'blocked'
    ELSE 'disabled'
END,
`id_engine_group` = (SELECT `engine_group`.`id` FROM `engine_group` WHERE `engine_group`.`id` = `engine`.`id_engine_group`),
`type` = (SELECT `engine_group`.`type` FROM `engine_group` WHERE `engine_group`.`id` = `engine`.`id_engine_group`),
`zone_id` = (SELECT `zone`.`id` FROM `zone` WHERE `zone`.`id` = (SELECT `computer_engine`.`id_society_zone` FROM `computer_engine` WHERE `computer_engine`.`id_engine` = `engine`.`id`))
SQL);
        $this->addQuery('DROP TABLE `computer_engine`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `manufacturer_engine` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `code` VARCHAR(255) DEFAULT NULL,
    `date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `engine_id` INT UNSIGNED DEFAULT NULL,
    `manufacturer_id` INT UNSIGNED DEFAULT NULL,
    `serial_number` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_F514547DE78C9C0A` FOREIGN KEY (`engine_id`) REFERENCES `engine` (`id`),
    CONSTRAINT `IDX_F514547DA23B42D` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`id`),
    UNIQUE KEY `UNIQ_F514547DE78C9C0A` (`engine_id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `manufacturer_engine` (`code`, `date`, `engine_id`, `manufacturer_id`, `serial_number`)
SELECT
    `reffourn`,
    `date_fabrication`,
    `id`,
    (SELECT `manufacturer`.`id` FROM `manufacturer` WHERE `manufacturer`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `engine`.`id_fabricant`)),
    `numero_serie`
FROM `engine`
SQL);
        $this->addQuery(<<<'SQL'
ALTER TABLE `engine`
    DROP `reffourn`,
    DROP `date_fabrication`,
    DROP `id_fabricant`,
    DROP `numero_serie`,
    CHANGE `marque` `brand` VARCHAR(255) DEFAULT NULL,
    CHANGE `ref` `code` VARCHAR(10) DEFAULT NULL,
    CHANGE `capabilite` `current_place_name` ENUM('blocked', 'disabled', 'enabled', 'warning') DEFAULT 'warning' NOT NULL COMMENT '(DC2Type:employee_engine_current_place)',
    CHANGE `d_entree` `entry_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CHANGE `id_engine_group` `group_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `operateur_max` `max_operator` TINYINT UNSIGNED DEFAULT 1 NOT NULL COMMENT '(DC2Type:tinyint)',
    CHANGE `nom` `name` VARCHAR(127) NOT NULL,
    ADD CONSTRAINT `IDX_E8A81A8DFE54D947` FOREIGN KEY (`group_id`) REFERENCES `engine_group` (`id`),
    ADD CONSTRAINT `IDX_E8A81A8D9F2C3FAB` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`)
SQL);
    }

    private function upEventTypes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee_eventlist` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `motif` VARCHAR(30) NOT NULL,
    `to_status` ENUM('blocked', 'disabled', 'enabled', 'warning') DEFAULT NULL COMMENT '(DC2Type:employee_engine_current_place)'
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
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `code` VARCHAR(11) NOT NULL,
    `label` VARCHAR(50) DEFAULT NULL
)
SQL);
        $this->insert('incoterms', ['id', 'statut', 'code', 'label']);
        $this->addQuery(<<<'SQL'
ALTER TABLE `incoterms`
    CHANGE `label` `name` VARCHAR(50) NOT NULL,
    CHANGE `statut` `deleted` BOOLEAN DEFAULT FALSE NOT NULL
SQL);
        $this->addQuery('UPDATE `incoterms` SET `name` = UCFIRST(`name`)');
    }

    private function upInvoiceTimeDue(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `invoicetimedue` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_old_invoicetimedue` INT UNSIGNED DEFAULT NULL,
    `id_old_invoicetimeduesupplier` INT UNSIGNED DEFAULT NULL,
    `statut` BOOLEAN NOT NULL,
    `days` TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '(DC2Type:tinyint)',
    `endofmonth` TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '(DC2Type:tinyint)',
    `libelle` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('invoicetimedue', ['id', 'statut', 'days', 'endofmonth', 'libelle']);
        $this->addQuery('UPDATE `invoicetimedue` SET `id_old_invoicetimedue` = `id`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `invoicetimeduesupplier` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN NOT NULL,
    `days` TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '(DC2Type:tinyint)',
    `endofmonth` TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '(DC2Type:tinyint)',
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
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `days` TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '(DC2Type:tinyint)',
    `days_after_end_of_month` TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '(DC2Type:tinyint)',
    `end_of_month` BOOLEAN DEFAULT FALSE NOT NULL,
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

    private function upItRequests(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `request` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `asked_at` DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
    `asked_by_id` INT UNSIGNED DEFAULT NULL,
    `current_place` VARCHAR(255) NOT NULL,
    `delay` DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
    `description` TEXT NOT NULL, name VARCHAR(255) NOT NULL,
    `version` VARCHAR(255) NOT NULL,
    CONSTRAINT `IDX_3B978F9F4F7A72E4` FOREIGN KEY (`asked_by_id`) REFERENCES `employee` (`id`)
)
SQL);
    }

    private function upLocales(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `locale` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN NOT NULL,
    `code` CHAR(2) NOT NULL
)
SQL);
        $this->insert('locale', ['id', 'statut', 'code']);
    }

    private function upManufacturers(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine_fabricant_ou_contact` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
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

    private function upManufacturingOrders(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `orderfabrication` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `ofnumber` INT UNSIGNED DEFAULT NULL,
    `indice` INT UNSIGNED DEFAULT NULL,
    `id_supplier` INT UNSIGNED DEFAULT NULL,
    `id_orderfabricationstatus` INT UNSIGNED DEFAULT NULL,
    `id_society` INT UNSIGNED DEFAULT NULL,
    `id_product` INT UNSIGNED DEFAULT NULL,
    `product_indice` VARCHAR(255) DEFAULT NULL,
    `id_ordercustomer` INT UNSIGNED DEFAULT NULL,
    `quantity` INT UNSIGNED DEFAULT NULL,
    `quantity_done` INT UNSIGNED DEFAULT NULL,
    `quantity_real` INT UNSIGNED DEFAULT NULL,
    `date_livraison` DATE DEFAULT NULL,
    `info_public` TEXT DEFAULT NULL,
    `date_validation` DATE DEFAULT NULL,
    `date_fabrication` DATE DEFAULT NULL
)
SQL);
        $this->insert('orderfabrication', [
            'id',
            'statut',
            'ofnumber',
            'indice',
            'id_supplier',
            'id_orderfabricationstatus',
            'id_society',
            'id_product',
            'product_indice',
            'id_ordercustomer',
            'quantity',
            'quantity_done',
            'quantity_real',
            'date_livraison',
            'info_public',
            'date_validation',
            'date_fabrication'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `manufacturing_order` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `actual_quantity_code` VARCHAR(6) DEFAULT NULL,
    `actual_quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `actual_quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` ENUM('agreed', 'blocked', 'closed', 'disabled', 'draft') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:manufacturing_order_current_place)',
    `company_id` INT UNSIGNED DEFAULT NULL,
    `delivery_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `index` TINYINT UNSIGNED DEFAULT 1 NOT NULL COMMENT '(DC2Type:tinyint)',
    `manufacturing_company_id` INT UNSIGNED DEFAULT NULL,
    `manufacturing_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `notes` VARCHAR(255) DEFAULT NULL,
    `order_id` INT UNSIGNED DEFAULT NULL,
    `product_id` INT UNSIGNED DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    `quantity_produced_code` VARCHAR(6) DEFAULT NULL,
    `quantity_produced_denominator` VARCHAR(6) DEFAULT NULL,
    `quantity_produced_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `quantity_requested_code` VARCHAR(6) DEFAULT NULL,
    `quantity_requested_denominator` VARCHAR(6) DEFAULT NULL,
    `quantity_requested_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `validation_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    CONSTRAINT `IDX_34010DB1979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_34010DB1E26A3063` FOREIGN KEY (`manufacturing_company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_34010DB18D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `customer_order` (`id`),
    CONSTRAINT `IDX_34010DB14584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `manufacturing_order` (
    `actual_quantity_code`,
    `actual_quantity_value`,
    `current_place_name`,
    `company_id`,
    `delivery_date`,
    `index`,
    `manufacturing_company_id`,
    `manufacturing_date`,
    `notes`,
    `order_id`,
    `product_id`,
    `ref`,
    `quantity_produced_code`,
    `quantity_produced_value`,
    `quantity_requested_code`,
    `quantity_requested_value`
) SELECT
    (SELECT `unit`.`code` FROM `unit` WHERE `unit`.`id` = (SELECT `product`.`unit_id` FROM `product` WHERE `product`.`old_id` = `orderfabrication`.`id_product`)),
    `quantity_real`,
    CASE
        WHEN `id_orderfabricationstatus` IN (3, 4, 8) THEN 'agreed'
        WHEN `id_orderfabricationstatus` = 5 THEN 'closed'
        WHEN `id_orderfabricationstatus` = 6 THEN 'disabled'
        WHEN `id_orderfabricationstatus` = 7 THEN 'blocked'
        ELSE 'draft'
    END,
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `orderfabrication`.`id_society`)),
    `date_livraison`,
    `indice`,
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `orderfabrication`.`id_supplier`)),
    `date_fabrication`,
    `info_public`,
    (SELECT `customer_order`.`id` FROM `customer_order` WHERE `customer_order`.`old_id` = `orderfabrication`.`id_ordercustomer`),
    (SELECT `product`.`id` FROM `product` WHERE `product`.`old_id` = `orderfabrication`.`id_product`),
    `ofnumber`,
    (SELECT `unit`.`code` FROM `unit` WHERE `unit`.`id` = (SELECT `product`.`unit_id` FROM `product` WHERE `product`.`old_id` = `orderfabrication`.`id_product`)),
    `quantity_done`,
    (SELECT `unit`.`code` FROM `unit` WHERE `unit`.`id` = (SELECT `product`.`unit_id` FROM `product` WHERE `product`.`old_id` = `orderfabrication`.`id_product`)),
    `quantity`
FROM `orderfabrication`
WHERE `statut` = 0
AND EXISTS (SELECT `product`.`id` FROM `product` WHERE `product`.`old_id` = `orderfabrication`.`id_product`)
SQL);
        $this->addQuery('DROP TABLE `orderfabrication`');
    }

    private function upNomenclatures(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `productcontent` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_product` INT UNSIGNED DEFAULT NULL,
    `id_component` INT UNSIGNED DEFAULT NULL,
    `quantity` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `mandat` BOOLEAN DEFAULT TRUE NOT NULL
)
SQL);
        $this->insert('productcontent', ['id', 'statut', 'id_product', 'id_component', 'quantity', 'mandat']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `nomenclature` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `component_id` INT UNSIGNED NOT NULL,
    `quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `mandated` BOOLEAN DEFAULT TRUE NOT NULL,
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
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `category` ENUM('default') DEFAULT 'default' NOT NULL COMMENT '(DC2Type:notification_category)',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `read` BOOLEAN DEFAULT FALSE NOT NULL,
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
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
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
UPDATE `employee_extformateur`
INNER JOIN `country` ON `employee_extformateur`.`id_phone_prefix` = `country`.`id`
SET `employee_extformateur`.`tel` = CONCAT(`country`.`phone_prefix`, `employee_extformateur`.`tel`),
`employee_extformateur`.`address_country` = UCASE(`country`.`code`),
`employee_extformateur`.`nom` = UCASE(`employee_extformateur`.`nom`),
`employee_extformateur`.`prenom` = UCFIRST(`employee_extformateur`.`prenom`)
SQL);
        $this->addQuery(<<<'SQL'
ALTER TABLE `employee_extformateur`
    DROP `id_phone_prefix`,
    CHANGE `prenom` `name` VARCHAR(30) NOT NULL,
    CHANGE `nom` `surname` VARCHAR(30) NOT NULL,
    CHANGE `address` `address_address` VARCHAR(80) DEFAULT NULL,
    CHANGE `ville` `address_city` VARCHAR(50) DEFAULT NULL,
    CHANGE `code_postal` `address_zip_code` VARCHAR(10) DEFAULT NULL
SQL);
        $this->addQuery('RENAME TABLE `employee_extformateur` TO `out_trainer`');
    }

    private function upPhoneNumbers(string $table, string $phoneProp, string $normalizedProp = 'address_phone_number'): void {
        $items = $this->connection->executeQuery("SELECT `id`, `$phoneProp`, `address_country` FROM `$table` WHERE `$phoneProp` IS NOT NULL");
        $util = PhoneNumberUtil::getInstance();
        while ($item = $items->fetchAssociative()) {
            /** @var string[] $item */
            $phone = null;
            try {
                $phone = $util->parse($item[$phoneProp], $item['address_country']);
            } catch (NumberParseException) {
            }
            $phone = !empty($phone) && $util->isValidNumber($phone)
                ? $util->format($phone, PhoneNumberFormat::INTERNATIONAL)
                : null;
            $this->phoneQueries->push("UPDATE `$table` SET `$phoneProp` = '$phone' WHERE `id` = {$item['id']}");
        }
        $this->phoneQueries->push("ALTER TABLE `$table` CHANGE `$phoneProp` `$normalizedProp` VARCHAR(18) DEFAULT NULL");
    }

    private function upPlannings(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine_maintenance` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_engine` INT UNSIGNED DEFAULT NULL,
    `type` VARCHAR(255) NOT NULL,
    `designation` TEXT NOT NULL,
    `quantity_code` VARCHAR(6) DEFAULT NULL,
    `quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `quantity` INT UNSIGNED DEFAULT 0 NOT NULL
)
SQL);
        $this->insert('engine_maintenance', ['id', 'id_engine', 'type', 'designation', 'quantity']);
        $this->addQuery(<<<'SQL'
DELETE FROM `engine_maintenance`
WHERE NOT EXISTS (SELECT `engine`.`id` FROM `engine` WHERE `engine`.`id` = `engine_maintenance`.`id_engine`)
SQL);
        $this->addQuery(<<<'SQL'
UPDATE `engine_maintenance`
SET `quantity_code` = CASE
    WHEN `type` = 'd' THEN (SELECT `unit`.`code` FROM `unit` WHERE `unit`.`code` = 'j')
    WHEN `type` = 'u' THEN (SELECT `unit`.`code` FROM `unit` WHERE `unit`.`code` = 'U')
    WHEN `type` = 'b' THEN (SELECT `unit`.`code` FROM `unit` WHERE `unit`.`code` = 'fds')
END,
`quantity_value` = `quantity`
SQL);
        $this->addQuery(<<<'SQL'
ALTER TABLE `engine_maintenance`
    DROP `quantity`,
    DROP `type`,
    CHANGE `id_engine` `engine_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `designation` `name` VARCHAR(255) NOT NULL,
    ADD CONSTRAINT `IDX_D499BFF6E78C9C0A` FOREIGN KEY (`engine_id`) REFERENCES `engine` (`id`)
SQL);
        $this->addQuery('RENAME TABLE `engine_maintenance` TO `planning`');
    }

    private function upPrinters(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `printer` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `company_id` INT UNSIGNED DEFAULT NULL,
    `ip` VARCHAR(255) DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_8D4C79ED979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `printer` (`company_id`, `ip`, `name`) VALUES
(
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = 1)),
    '192.168.2.115',
    'zpl'
),
(
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = 611)),
    '192.168.3.21',
    'zpl-tn'
),
(
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = 5)),
    '192.168.4.18',
    'zpl-md'
)
SQL);
    }

    private function upProductFamilies(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `product_family` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
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
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `temps_auto` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `ref` VARCHAR(255) NOT NULL,
    `tps_chiff_auto` DOUBLE PRECISION DEFAULT NULL,
    `tps_chiff_manu` DOUBLE PRECISION DEFAULT NULL,
    `id_productstatus` INT NOT NULL DEFAULT 1,
    `date_expiration` DATE DEFAULT NULL,
    `id_product_subfamily` INT UNSIGNED NOT NULL,
    `id_society` INT UNSIGNED NOT NULL,
    `volume_previsionnel` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `id_customcode` INT UNSIGNED NOT NULL,
    `id_product_child` INT UNSIGNED NOT NULL,
    `id_incoterms` INT UNSIGNED NOT NULL,
    `indice` VARCHAR(3) NOT NULL,
    `indice_interne` TINYINT UNSIGNED DEFAULT 1 NOT NULL,
    `is_prototype` BOOLEAN DEFAULT FALSE NOT NULL,
    `gestion_cu` BOOLEAN DEFAULT FALSE NOT NULL,
    `temps_manu` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `max_proto_quantity` DOUBLE PRECISION DEFAULT NULL,
    `livraison_minimum` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `min_prod_quantity` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `stock_minimum` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `designation` VARCHAR(80) NOT NULL,
    `info_public` TEXT DEFAULT NULL,
    `typeconditionnement` VARCHAR(30) NOT NULL,
    `conditionnement` DOUBLE PRECISION DEFAULT NULL,
    `price` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `price_without_cu` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `production_delay` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `transfert_price_supplies` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `transfert_price_work` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `weight` DOUBLE PRECISION DEFAULT 0 NOT NULL
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
            'id_society',
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
    `id_society` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `auto_duration_code` VARCHAR(6) DEFAULT NULL,
    `auto_duration_denominator` VARCHAR(6) DEFAULT NULL,
    `auto_duration_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `code` VARCHAR(30) NOT NULL,
    `costing_auto_duration_code` VARCHAR(6) DEFAULT NULL,
    `costing_auto_duration_denominator` VARCHAR(6) DEFAULT NULL,
    `costing_auto_duration_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `costing_manual_duration_code` VARCHAR(6) DEFAULT NULL,
    `costing_manual_duration_denominator` VARCHAR(6) DEFAULT NULL,
    `costing_manual_duration_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `current_place_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` ENUM('agreed','blocked','disabled','draft','to_validate','under_exemption') NOT NULL DEFAULT 'draft' COMMENT '(DC2Type:product_current_place)',
    `customs_code` VARCHAR(10) DEFAULT NULL,
    `end_of_life` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `family_id` INT UNSIGNED NOT NULL,
    `forecast_volume_code` VARCHAR(6) DEFAULT NULL,
    `forecast_volume_denominator` VARCHAR(6) DEFAULT NULL,
    `forecast_volume_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `id_product_child` INT UNSIGNED NOT NULL,
    `incoterms_id` INT UNSIGNED DEFAULT NULL,
    `index` VARCHAR(3) NOT NULL,
    `internal_index` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '(DC2Type:tinyint)',
    `kind` enum('EI','Prototype','Série','Pièce de rechange') NOT NULL DEFAULT 'Prototype' COMMENT '(DC2Type:product_kind)',
    `managed_copper` TINYINT(1) DEFAULT 0 NOT NULL,
    `manual_duration_code` VARCHAR(6) DEFAULT NULL,
    `manual_duration_denominator` VARCHAR(6) DEFAULT NULL,
    `manual_duration_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `max_proto_code` VARCHAR(6) DEFAULT NULL,
    `max_proto_denominator` VARCHAR(6) DEFAULT NULL,
    `max_proto_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `min_delivery_code` VARCHAR(6) DEFAULT NULL,
    `min_delivery_denominator` VARCHAR(6) DEFAULT NULL,
    `min_delivery_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `min_prod_code` VARCHAR(6) DEFAULT NULL,
    `min_prod_denominator` VARCHAR(6) DEFAULT NULL,
    `min_prod_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `min_stock_code` VARCHAR(6) DEFAULT NULL,
    `min_stock_denominator` VARCHAR(6) DEFAULT NULL,
    `min_stock_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `name` VARCHAR(80) NOT NULL,
    `notes` text DEFAULT NULL,
    `packaging_code` VARCHAR(6) DEFAULT NULL,
    `packaging_denominator` VARCHAR(6) DEFAULT NULL,
    `packaging_kind` VARCHAR(30) NOT NULL,
    `packaging_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `price_code` VARCHAR(6) DEFAULT NULL,
    `price_denominator` VARCHAR(6) DEFAULT NULL,
    `price_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `price_without_copper_code` VARCHAR(6) DEFAULT NULL,
    `price_without_copper_denominator` VARCHAR(6) DEFAULT NULL,
    `price_without_copper_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `production_delay_code` VARCHAR(6) DEFAULT NULL,
    `production_delay_denominator` VARCHAR(6) DEFAULT NULL,
    `production_delay_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `transfert_price_supplies_code` VARCHAR(6) DEFAULT NULL,
    `transfert_price_supplies_denominator` VARCHAR(6) DEFAULT NULL,
    `transfert_price_supplies_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `transfert_price_work_code` VARCHAR(6) DEFAULT NULL,
    `transfert_price_work_denominator` VARCHAR(6) DEFAULT NULL,
    `transfert_price_work_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `unit_id` INT UNSIGNED NOT NULL,
    `weight_code` VARCHAR(6) DEFAULT NULL,
    `weight_denominator` VARCHAR(6) DEFAULT NULL,
    `weight_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CONSTRAINT `IDX_D34A04ADC35E566A` FOREIGN KEY (`family_id`) REFERENCES `product_family` (`id`),
    CONSTRAINT `IDX_D34A04AD43D02C80` FOREIGN KEY (`incoterms_id`) REFERENCES `incoterms` (`id`),
    CONSTRAINT `IDX_D34A04AD727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `product` (`id`),
    CONSTRAINT `IDX_D34A04ADF8BD700D` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `product` (
    `old_id`,
    `id_society`,
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
    `id_society`,
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
        $this->addQuery('ALTER TABLE `product` DROP `id_product_child`');
    }

    private function upQualityTypes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `qualitycontrol` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `qualitycontrol` VARCHAR(40) NOT NULL
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
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `libelle` VARCHAR(255) DEFAULT NULL
)
SQL);
        $this->insert('production_rejectlist', ['id', 'statut', 'libelle']);
        $this->addQuery('DELETE FROM `production_rejectlist` WHERE `statut` = 1 OR `libelle` IS NULL');
        $this->addQuery(<<<'SQL'
CREATE TABLE `reject_type` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `name` varchar(40) NOT NULL
)
SQL);
        $this->addQuery('INSERT INTO `reject_type` (`name`) SELECT UCFIRST(`libelle`) FROM `production_rejectlist`');
        $this->addQuery('DROP TABLE `production_rejectlist`');
    }

    private function upSkills(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee_histcompetence` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_employee` int(11) DEFAULT NULL,
    `id_formateur` int(11) DEFAULT NULL,
    `id_extformateur` int(11) DEFAULT NULL,
    `date_form` date DEFAULT NULL,
    `date_cloture_formation` date DEFAULT NULL,
    `id_competence` int(11) DEFAULT NULL,
    `id_engine` int(11) DEFAULT NULL,
    `niveau` int(11) DEFAULT NULL
)
SQL);
        $this->insert('employee_histcompetence', [
            'id',
            'id_employee',
            'id_formateur',
            'id_extformateur',
            'date_form',
            'date_cloture_formation',
            'id_competence',
            'id_engine',
            'niveau'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `skill` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `employee_id` INT UNSIGNED DEFAULT NULL,
    `ended_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `engine_id` INT UNSIGNED DEFAULT NULL,
    `family_id` INT UNSIGNED DEFAULT NULL,
    `in_trainer_id` INT UNSIGNED DEFAULT NULL,
    `level` TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '(DC2Type:tinyint)',
    `out_trainer_id` INT UNSIGNED DEFAULT NULL,
    `product_id` INT UNSIGNED DEFAULT NULL,
    `remindable` BOOLEAN DEFAULT FALSE NOT NULL,
    `reminded_child_id` INT UNSIGNED DEFAULT NULL,
    `reminded_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `started_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `type_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_5E3DE4778C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`),
    CONSTRAINT `IDX_5E3DE477E78C9C0A` FOREIGN KEY (`engine_id`) REFERENCES `engine` (`id`),
    CONSTRAINT `IDX_5E3DE477C35E566A` FOREIGN KEY (`family_id`) REFERENCES `engine_group` (`id`),
    CONSTRAINT `IDX_5E3DE477B4B58540` FOREIGN KEY (`in_trainer_id`) REFERENCES `employee` (`id`),
    CONSTRAINT `IDX_5E3DE47778A19B66` FOREIGN KEY (`out_trainer_id`) REFERENCES `out_trainer` (`id`),
    CONSTRAINT `IDX_5E3DE4774584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
    CONSTRAINT `IDX_5E3DE47795A13422` FOREIGN KEY (`reminded_child_id`) REFERENCES `skill` (`id`),
    CONSTRAINT `IDX_5E3DE477C54C8C93` FOREIGN KEY (`type_id`) REFERENCES `skill_type` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `skill` (
    `employee_id`,
    `ended_date`,
    `engine_id`,
    `in_trainer_id`,
    `level`,
    `out_trainer_id`,
    `started_date`,
    `type_id`
) SELECT
    (SELECT `employee`.`id` FROM `employee` WHERE `employee`.`old_id` = `employee_histcompetence`.`id_employee`),
    `date_cloture_formation`,
    (SELECT `engine`.`id` FROM `engine` WHERE `engine`.`id` = `employee_histcompetence`.`id_engine`),
    (SELECT `employee`.`id` FROM `employee` WHERE `employee`.`old_id` = `employee_histcompetence`.`id_formateur`),
    `niveau`,
    (SELECT `out_trainer`.`id` FROM `out_trainer` WHERE `out_trainer`.`id` = `employee_histcompetence`.`id_extformateur`),
    `date_form`,
    (SELECT `skill_type`.`id` FROM `skill_type` WHERE `skill_type`.`old_id` = `employee_histcompetence`.`id_competence`)
FROM `employee_histcompetence`
WHERE EXISTS (SELECT `employee`.`id` FROM `employee` WHERE `employee`.`old_id` = `employee_histcompetence`.`id_employee`)
AND EXISTS (SELECT `skill_type`.`id` FROM `skill_type` WHERE `skill_type`.`old_id` = `employee_histcompetence`.`id_competence`)
SQL);
        $this->addQuery('DROP TABLE `employee_histcompetence`');
    }

    private function upSkillTypes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee_competencelist` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `designation` varchar(255) NOT NULL,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL
)
SQL);
        $this->insert('employee_competencelist', ['id', 'designation', 'statut']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `skill_type` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `name` VARCHAR(50) NOT NULL
)
SQL);
        $this->addQuery('INSERT INTO `skill_type` (`old_id`, `name`) SELECT `id`, `designation` FROM `employee_competencelist` WHERE `statut` = 0');
        $this->addQuery('DROP TABLE `employee_competencelist`');
    }

    private function upSocieties(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `society` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `nom` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) DEFAULT NULL,
    `address1` VARCHAR(80) DEFAULT NULL,
    `address2` VARCHAR(60) DEFAULT NULL,
    `zip` VARCHAR(10) DEFAULT NULL,
    `city` VARCHAR(50) DEFAULT NULL,
    `id_country` INT UNSIGNED NOT NULL,
    `id_invoicetimedue` INT UNSIGNED DEFAULT NULL,
    `id_invoicetimeduesupplier` INT UNSIGNED DEFAULT NULL,
    `id_locale` INT UNSIGNED NOT NULL,
    `id_soc_gest` INT UNSIGNED NOT NULL,
    `id_soc_gest_customer` INT UNSIGNED NOT NULL,
    `id_societystatus` INT UNSIGNED NOT NULL,
    `invoicecustomer_by_email` BOOLEAN DEFAULT FALSE NOT NULL,
    `invoice_minimum` DOUBLE PRECISION DEFAULT NULL,
    `order_minimum` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `web` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(60) DEFAULT NULL,
    `formejuridique` VARCHAR(50) DEFAULT NULL,
    `siren` VARCHAR(50) DEFAULT NULL,
    `tva` VARCHAR(255) DEFAULT NULL,
    `compte_compta` VARCHAR(50) DEFAULT NULL,
    `info_public` TEXT DEFAULT NULL,
    `info_private` TEXT DEFAULT NULL,
    `ar_enabled` BOOLEAN DEFAULT FALSE NOT NULL,
    `ar_customer_enabled` BOOLEAN DEFAULT FALSE NOT NULL,
    `indice_cu_enabled` BOOLEAN DEFAULT FALSE NOT NULL,
    `indice_cu` DOUBLE PRECISION DEFAULT NULL,
    `indice_cu_date` DATETIME DEFAULT NULL,
    `indice_cu_date_fin` DATETIME DEFAULT NULL,
    `id_incoterms` INT UNSIGNED DEFAULT NULL,
    `is_company` BOOLEAN DEFAULT FALSE,
    `is_customer` BOOLEAN DEFAULT FALSE,
    `is_supplier` BOOLEAN DEFAULT FALSE,
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
            'id_locale',
            'id_soc_gest',
            'id_soc_gest_customer',
            'id_societystatus',
            'invoice_minimum',
            'invoicecustomer_by_email',
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
            'is_company',
            'is_customer',
            'is_supplier',
            'force_tva',
            'id_messagetva'
        ]);
        $this->addQuery('RENAME TABLE `society` TO `society_old`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `society` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED DEFAULT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
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
    `copper_index_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `copper_last` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_managed` TINYINT(1) DEFAULT 0 NOT NULL,
    `copper_next` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_type` ENUM('à la livraison','mensuel','semestriel') NOT NULL DEFAULT 'mensuel' COMMENT '(DC2Type:copper_type)',
    `force_vat` ENUM('TVA par défaut selon le pays du client','Force AVEC TVA','Force SANS TVA') NOT NULL DEFAULT 'TVA par défaut selon le pays du client' COMMENT '(DC2Type:vat_message_force)',
    `incoterms_id` INT UNSIGNED DEFAULT NULL,
    `invoice_min_code` VARCHAR(6) DEFAULT NULL,
    `invoice_min_denominator` VARCHAR(6) DEFAULT NULL,
    `invoice_min_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `invoice_time_due_id` INT UNSIGNED DEFAULT NULL,
    `legal_form` VARCHAR(50) DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `order_min_code` VARCHAR(6) DEFAULT NULL,
    `order_min_denominator` VARCHAR(6) DEFAULT NULL,
    `order_min_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `ppm_rate` SMALLINT UNSIGNED NOT NULL DEFAULT 10,
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
    `old_id`,
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
    `id`,
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
WHERE `statut` = 0
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `company` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `delivery_time` TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '(DC2Type:tinyint)',
    `delivery_time_open_days` BOOLEAN DEFAULT TRUE NOT NULL,
    `engine_hour_rate` DOUBLE PRECISION UNSIGNED DEFAULT 0 NOT NULL,
    `general_margin` DOUBLE PRECISION UNSIGNED DEFAULT 0 NOT NULL,
    `handling_hour_rate` DOUBLE PRECISION UNSIGNED DEFAULT 0 NOT NULL,
    `ip` VARCHAR(15) DEFAULT NULL,
    `management_fees` DOUBLE PRECISION UNSIGNED DEFAULT 0 NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `notes` TEXT DEFAULT NULL,
    `number_of_team_per_day` TINYINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '(DC2Type:tinyint)',
    `society_id` INT UNSIGNED DEFAULT NULL,
    `work_timetable` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_4FBF094FE6389D24` FOREIGN KEY (`society_id`) REFERENCES `society` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `company` (`name`, `society_id`)
SELECT `nom`, (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_old`.`id`)
FROM `society_old`
WHERE `is_company` = 1 AND `statut` = 0
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `customer` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `accounting_portal_password` VARCHAR(255) DEFAULT NULL,
    `accounting_portal_url` VARCHAR(255) DEFAULT NULL,
    `accounting_portal_username` VARCHAR(255) DEFAULT NULL,
    `address_address` VARCHAR(80) DEFAULT NULL,
    `address_address2` VARCHAR(60) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(60) DEFAULT NULL,
    `address_phone_number` VARCHAR(255) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `conveyance_duration_code` VARCHAR(6) DEFAULT NULL,
    `conveyance_duration_denominator` VARCHAR(6) DEFAULT NULL,
    `conveyance_duration_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `copper_index_code` VARCHAR(6) DEFAULT NULL,
    `copper_index_denominator` VARCHAR(6) DEFAULT NULL,
    `copper_index_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `copper_last` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_managed` BOOLEAN DEFAULT FALSE NOT NULL,
    `copper_next` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_type` ENUM('à la livraison', 'mensuel', 'semestriel') DEFAULT 'mensuel' NOT NULL COMMENT '(DC2Type:copper_type)',
    `currency_id` INT UNSIGNED NOT NULL,
    `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` ENUM('agreed', 'blocked', 'disabled', 'draft') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:customer_current_place)',
    `equivalent_enabled` BOOLEAN DEFAULT FALSE NOT NULL,
    `invoice_by_email` BOOLEAN DEFAULT FALSE NOT NULL,
    `language` VARCHAR(255) DEFAULT NULL,
    `monthly_outstanding_code` VARCHAR(6) DEFAULT NULL,
    `monthly_outstanding_denominator` VARCHAR(6) DEFAULT NULL,
    `monthly_outstanding_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `nb_deliveries` TINYINT UNSIGNED DEFAULT 10 NOT NULL COMMENT '(DC2Type:tinyint)',
    `nb_invoices` TINYINT UNSIGNED DEFAULT 10 NOT NULL COMMENT '(DC2Type:tinyint)',
    `notes` TEXT DEFAULT NULL,
    `outstanding_max_code` VARCHAR(6) DEFAULT NULL,
    `outstanding_max_denominator` VARCHAR(6) DEFAULT NULL,
    `outstanding_max_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `payment_terms_id` INT UNSIGNED NOT NULL,
    `society_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_81398E0938248176` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`),
    CONSTRAINT `IDX_81398E0913B26D4F` FOREIGN KEY (`payment_terms_id`) REFERENCES `invoice_time_due` (`id`),
    CONSTRAINT `IDX_81398E09E6389D24` FOREIGN KEY (`society_id`) REFERENCES `society` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `customer` (
    `address_address`,
    `address_address2`,
    `address_city`,
    `address_country`,
    `address_email`,
    `address_phone_number`,
    `address_zip_code`,
    `conveyance_duration_code`,
    `conveyance_duration_value`,
    `copper_index_value`,
    `copper_last`,
    `copper_managed`,
    `copper_next`,
    `currency_id`,
    `current_place_name`,
    `invoice_by_email`,
    `language`,
    `name`,
    `nb_deliveries`,
    `nb_invoices`,
    `notes`,
    `payment_terms_id`,
    `society_id`
) SELECT
    `address1`,
    `address2`,
    `city`,
    (SELECT UCASE(`country`.`code`) FROM `country` WHERE `country`.`id` = `society_old`.`id_country`),
    `email`,
    `phone`,
    `zip`,
    'j',
    7,
    IFNULL(`indice_cu`, 0),
    `indice_cu_date`,
    `indice_cu_enabled`,
    `indice_cu_date_fin`,
    (SELECT `currency`.`id` FROM `currency` WHERE `currency`.`code` = 'EUR'),
    CASE
        WHEN `id_societystatus` = 1 THEN 'draft'
        WHEN `id_societystatus` = 2 THEN 'agreed'
        WHEN `id_societystatus` = 3 THEN 'blocked'
        ELSE 'draft'
    END,
    `invoicecustomer_by_email`,
    (SELECT UCASE(`locale`.`code`) FROM `locale` WHERE `locale`.`id` = `society_old`.`id_locale`),
    `nom`,
    10,
    10,
    IF(
        `info_public` IS NULL,
        `info_private`,
        IF(`info_private` IS NULL, NULL, CONCAT(`info_public`, `info_private`))
    ),
    (
        SELECT `invoice_time_due`.`id`
        FROM `invoice_time_due`
        WHERE `invoice_time_due`.`id_old_invoicetimedue` = `society_old`.`id_invoicetimedue`
        OR `invoice_time_due`.`id_old_invoicetimeduesupplier` = `society_old`.`id_invoicetimeduesupplier`
        LIMIT 1
    ),
    (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_old`.`id`)
FROM `society_old`
WHERE `is_customer` = 1 AND `statut` = 0
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `customer_company` (
    `customer_id` INT UNSIGNED NOT NULL,
    `company_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_5362ADF19395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE,
    CONSTRAINT `IDX_5362ADF1979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE,
    PRIMARY KEY(`customer_id`, `company_id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `customer_company` (`customer_id`, `company_id`)
SELECT
    (SELECT `customer`.`id` FROM `customer` WHERE `customer`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_old`.`id`)),
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_old`.`id_soc_gest_customer`))
FROM `society_old`
WHERE `is_customer` = 1
AND `statut` = 0
AND EXISTS (SELECT `customer`.`id` FROM `customer` WHERE `customer`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_old`.`id`))
AND EXISTS (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_old`.`id_soc_gest_customer`))
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `supplier` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `address_address` VARCHAR(80) DEFAULT NULL,
    `address_address2` VARCHAR(60) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(60) DEFAULT NULL,
    `address_phone_number` VARCHAR(255) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `confidence_criteria` TINYINT UNSIGNED DEFAULT '0' NOT NULL COMMENT '(DC2Type:tinyint)',
    `copper_index_code` VARCHAR(6) DEFAULT NULL,
    `copper_index_denominator` VARCHAR(6) DEFAULT NULL,
    `copper_index_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `copper_last` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_managed` BOOLEAN DEFAULT FALSE NOT NULL,
    `copper_next` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_type` ENUM('à la livraison', 'mensuel', 'semestriel') DEFAULT 'mensuel' NOT NULL COMMENT '(DC2Type:copper_type)',
    `currency_id` INT UNSIGNED NOT NULL,
    `current_place_date` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    `current_place_name` ENUM('agreed', 'blocked', 'disabled', 'draft', 'to_validate', 'warning') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:supplier_current_place)',
    `language` VARCHAR(255) DEFAULT NULL,
    `managed_production` TINYINT(1) DEFAULT 0 NOT NULL,
    `managed_quality` TINYINT(1) DEFAULT 0 NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `notes` TEXT DEFAULT NULL,
    `society_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_9B2A6C7E38248176` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`),
    CONSTRAINT `IDX_9B2A6C7EE6389D24` FOREIGN KEY (`society_id`) REFERENCES `society` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `supplier` (
    `address_address`,
    `address_address2`,
    `address_city`,
    `address_country`,
    `address_email`,
    `address_phone_number`,
    `address_zip_code`,
    `copper_index_value`,
    `copper_last`,
    `copper_managed`,
    `copper_next`,
    `currency_id`,
    `current_place_name`,
    `language`,
    `name`,
    `notes`,
    `society_id`
) SELECT
    `address1`,
    `address2`,
    `city`,
    (SELECT UCASE(`country`.`code`) FROM `country` WHERE `country`.`id` = `society_old`.`id_country`),
    `email`,
    `phone`,
    `zip`,
    IFNULL(`indice_cu`, 0),
    `indice_cu_date`,
    `indice_cu_enabled`,
    `indice_cu_date_fin`,
    (SELECT `currency`.`id` FROM `currency` WHERE `currency`.`code` = 'EUR'),
    CASE
        WHEN `id_societystatus` = 1 THEN 'to_validate'
        WHEN `id_societystatus` = 2 THEN 'agreed'
        WHEN `id_societystatus` = 3 THEN 'blocked'
        ELSE 'draft'
    END,
    (SELECT UCASE(`locale`.`code`) FROM `locale` WHERE `locale`.`id` = `society_old`.`id_locale`),
    `nom`,
    IF(
        `info_public` IS NULL,
        `info_private`,
        IF(`info_private` IS NULL, NULL, CONCAT(`info_public`, `info_private`))
    ),
    (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_old`.`id`)
FROM `society_old`
WHERE `is_supplier` = 1 AND `statut` = 0
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `supplier_company` (
    `supplier_id` INT UNSIGNED NOT NULL,
    `company_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_CEDA7D502ADD6D8C` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`) ON DELETE CASCADE,
    CONSTRAINT `IDX_CEDA7D50979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE,
    PRIMARY KEY(`supplier_id`, `company_id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `supplier_company` (`supplier_id`, `company_id`)
SELECT
    (SELECT `supplier`.`id` FROM `supplier` WHERE `supplier`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_old`.`id`)),
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_old`.`id_soc_gest`))
FROM `society_old`
WHERE `is_supplier` = 1
AND `statut` = 0
AND EXISTS (SELECT `supplier`.`id` FROM `supplier` WHERE `supplier`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_old`.`id`))
AND EXISTS (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_old`.`id_soc_gest_customer`))
SQL);
        $this->addQuery('DROP TABLE `society_old`');
    }

    private function upStocks(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `stock` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_warehouse` INT UNSIGNED DEFAULT NULL,
    `id_component` INT UNSIGNED DEFAULT NULL,
    `id_product` INT UNSIGNED DEFAULT NULL,
    `batchnumber` VARCHAR(255) DEFAULT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `quantity` INT DEFAULT NULL,
    `jail` BOOLEAN DEFAULT FALSE NOT NULL
)
SQL);
        $this->insert('stock', [
            'id',
            'statut',
            'id_warehouse',
            'id_component',
            'id_product',
            'batchnumber',
            'location',
            'quantity',
            'jail'
        ]);
        $this->addQuery('RENAME TABLE `stock` TO `old_stock`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `stock` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `batch_number` VARCHAR(255) DEFAULT NULL,
    `component_id` INT UNSIGNED DEFAULT NULL,
    `jail` BOOLEAN DEFAULT FALSE NOT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `product_id` INT UNSIGNED DEFAULT NULL,
    `quantity_code` VARCHAR(6) DEFAULT NULL,
    `quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `quantity_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `type` ENUM('component', 'product') NOT NULL COMMENT '(DC2Type:stock_type)',
    `warehouse_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_4B365660E2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`),
    CONSTRAINT `IDX_4B3656604584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
    CONSTRAINT `IDX_4B3656605080ECDE` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `stock` (
    `batch_number`,
    `component_id`,
    `jail`,
    `location`,
    `product_id`,
    `quantity_code`,
    `quantity_value`,
    `type`,
    `warehouse_id`
) SELECT
    `batchnumber`,
    (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `old_stock`.`id_component`),
    `jail`,
    `location`,
    (SELECT `product`.`id` FROM `product` WHERE `product`.`old_id` = `old_stock`.`id_product`),
    IF(
        EXISTS (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `old_stock`.`id_component`),
        (SELECT `unit`.`code` FROM `unit` WHERE `unit`.`id` = (SELECT `component`.`unit_id` FROM `component` WHERE `component`.`old_id` = `old_stock`.`id_component`)),
        (SELECT `unit`.`code` FROM `unit` WHERE `unit`.`id` = (SELECT `product`.`unit_id` FROM `product` WHERE `product`.`old_id` = `old_stock`.`id_product`))
    ),
    `quantity`,
    IF(
        EXISTS (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `old_stock`.`id_component`),
        'component',
        'product'
    ),
    (SELECT `warehouse`.`id` FROM `warehouse` WHERE `warehouse`.`old_id` = `old_stock`.`id_warehouse`)
FROM `old_stock`
WHERE `statut` = 0
AND `quantity` IS NOT NULL AND TRIM(`quantity`) != '' AND `quantity` > 0
AND (
    EXISTS (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `old_stock`.`id_component`)
    OR EXISTS (SELECT `product`.`id` FROM `product` WHERE `product`.`old_id` = `old_stock`.`id_product`)
)
SQL);
        $this->addQuery('DROP TABLE `old_stock`');
    }

    private function upSupplierComponents(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `component_supplier` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_component` INT UNSIGNED DEFAULT NULL,
    `id_supplier` INT UNSIGNED DEFAULT NULL,
    `id_incoterms` INT UNSIGNED DEFAULT NULL,
    `id_country` INT UNSIGNED DEFAULT NULL,
    `moq` INT UNSIGNED DEFAULT NULL,
    `conditionnement` INT UNSIGNED DEFAULT NULL,
    `typeconditionnement` VARCHAR(255) DEFAULT NULL,
    `timetodelivery` INT UNSIGNED DEFAULT NULL,
    `refsupplier` VARCHAR(255) DEFAULT NULL
)
SQL);
        $this->insert('component_supplier', [
            'id',
            'statut',
            'id_component',
            'id_supplier',
            'id_incoterms',
            'id_country',
            'moq',
            'conditionnement',
            'typeconditionnement',
            'timetodelivery',
            'refsupplier'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `supplier_component` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `code` VARCHAR(255) DEFAULT NULL,
    `component_id` INT UNSIGNED DEFAULT NULL,
    `copper_weight_code` VARCHAR(6) DEFAULT NULL,
    `copper_weight_denominator` VARCHAR(6) DEFAULT NULL,
    `copper_weight_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `delivery_time_code` VARCHAR(6) DEFAULT NULL,
    `delivery_time_denominator` VARCHAR(6) DEFAULT NULL,
    `delivery_time_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `incoterms_id` INT UNSIGNED DEFAULT NULL,
    `index` VARCHAR(255) DEFAULT '0' NOT NULL,
    `moq_code` VARCHAR(6) DEFAULT NULL,
    `moq_denominator` VARCHAR(6) DEFAULT NULL,
    `moq_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `packaging_code` VARCHAR(6) DEFAULT NULL,
    `packaging_denominator` VARCHAR(6) DEFAULT NULL,
    `packaging_kind` VARCHAR(30) DEFAULT NULL,
    `packaging_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `proportion` DOUBLE PRECISION UNSIGNED DEFAULT '100' NOT NULL,
    `supplier_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_D3CC9B89E2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`),
    CONSTRAINT `IDX_D3CC9B8943D02C80` FOREIGN KEY (`incoterms_id`) REFERENCES `incoterms` (`id`),
    CONSTRAINT `IDX_D3CC9B892ADD6D8C` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `supplier_component` (
    `old_id`,
    `code`,
    `component_id`,
    `delivery_time_code`,
    `delivery_time_value`,
    `incoterms_id`,
    `moq_code`,
    `moq_value`,
    `packaging_code`,
    `packaging_kind`,
    `packaging_value`,
    `proportion`,
    `supplier_id`
) SELECT
    `component_supplier`.`id`,
    `component_supplier`.`refsupplier`,
    `component`.`id`,
    'j',
    `component_supplier`.`timetodelivery`,
    (SELECT `incoterms`.`id` FROM `incoterms` WHERE `incoterms`.`id` = `component_supplier`.`id_incoterms`),
    `unit`.`code`,
    `component_supplier`.`moq`,
    `unit`.`code`,
    `component_supplier`.`typeconditionnement`,
    `component_supplier`.`conditionnement`,
    100,
    (SELECT `supplier`.`id` FROM `supplier` WHERE `supplier`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `component_supplier`.`id_supplier`))
FROM `component_supplier`
INNER JOIN `component` ON `component_supplier`.`id_component` = `component`.`old_id`
LEFT JOIN `unit` ON `component`.`unit_id` = `unit`.`id`
WHERE `component_supplier`.`statut` = 0
SQL);
        $this->addQuery('DROP TABLE `component_supplier`');
    }

    private function upSupplies(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `product_supplier` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` BOOLEAN DEFAULT FALSE NOT NULL,
  `id_product` INT UNSIGNED DEFAULT NULL,
  `id_supplier` INT UNSIGNED NOT NULL,
  `percentage` DOUBLE PRECISION UNSIGNED DEFAULT 100 NOT NULL,
  `id_incoterms` INT UNSIGNED NOT NULL,
  `refsupplier` VARCHAR(255) DEFAULT NULL
)
SQL);
        $this->insert('product_supplier', [
            'id',
            'statut',
            'id_product',
            'id_supplier',
            'percentage',
            'id_incoterms',
            'refsupplier'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `supply` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `company_id` INT UNSIGNED NOT NULL,
    `proportion` DOUBLE PRECISION UNSIGNED DEFAULT 100 NOT NULL,
    `incoterms_id` INT UNSIGNED DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_D219948C979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_D219948C43D02C80` FOREIGN KEY (`incoterms_id`) REFERENCES `incoterms` (`id`),
    CONSTRAINT `IDX_D219948C4584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `supply` (`product_id`, `company_id`, `proportion`, `incoterms_id`, `ref`)
SELECT
    (SELECT `product`.`id` FROM `product` WHERE `product`.`old_id` = `product_supplier`.`id_product`),
    (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `product_supplier`.`id_supplier`)),
    `percentage`,
    (SELECT `incoterms`.`id` FROM `incoterms` WHERE `incoterms`.`id` = `product_supplier`.`id_incoterms`),
    `refsupplier`
FROM `product_supplier`
WHERE `statut` = 0
AND EXISTS (SELECT `product`.`id` FROM `product` WHERE `product`.`old_id` = `product_supplier`.`id_product`)
AND EXISTS (SELECT `company`.`id` FROM `company` WHERE `company`.`society_id` = (SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `product_supplier`.`id_supplier`))
SQL);
        $this->addQuery('DROP TABLE `product_supplier`');
    }

    private function upTeams(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `team` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `company_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `time_slot_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_C4E0A61F979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_C4E0A61FD62B0FA` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slot` (`id`)
)
SQL);
    }

    private function upTimeSlots(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `time_slot` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `end` TIME NOT NULL COMMENT '(DC2Type:time_immutable)',
    `end_break` TIME DEFAULT NULL COMMENT '(DC2Type:time_immutable)',
    `name` VARCHAR(10) NOT NULL,
    `start` TIME NOT NULL COMMENT '(DC2Type:time_immutable)',
    `start_break` TIME DEFAULT NULL COMMENT '(DC2Type:time_immutable)'
)
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
  `statut` BOOLEAN DEFAULT FALSE NOT NULL,
  `base` DOUBLE PRECISION DEFAULT 1 NOT NULL,
  `unit_short_lbl` VARCHAR(6) NOT NULL,
  `unit_complete_lbl` VARCHAR(50) NOT NULL,
  `parent` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('unit', ['id', 'statut', 'base', 'unit_short_lbl', 'unit_complete_lbl', 'parent']);
        $this->addQuery(<<<'SQL'
ALTER TABLE `unit`
    CHANGE `parent` `parent_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `statut` `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    CHANGE `unit_complete_lbl` `name` VARCHAR(50) NOT NULL,
    CHANGE `unit_short_lbl` `code` VARCHAR(6) NOT NULL COLLATE `utf8_bin`,
    ADD CONSTRAINT `IDX_DCBB0C53727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `unit` (`id`)
SQL);
        $this->addQuery('UPDATE `unit` SET `name` = UCFIRST(`name`)');
    }

    private function upVatMessages(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `messagetva` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `message` TEXT NOT NULL
)
SQL);
        $this->insert('messagetva', ['id', 'statut', 'message']);
        $this->addQuery(<<<'SQL'
ALTER TABLE `messagetva`
    CHANGE `message` `name` VARCHAR(120) NOT NULL,
    CHANGE `statut` `deleted` BOOLEAN DEFAULT FALSE NOT NULL
SQL);
        $this->addQuery('RENAME TABLE `messagetva` TO `vat_message`');
    }

    private function upWarehouses(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `warehouse` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_society` INT UNSIGNED DEFAULT NULL,
    `families` SET('prison', 'production', 'réception', 'magasin pièces finies', 'expédition', 'magasin matières premières', 'camion') DEFAULT NULL COMMENT '(DC2Type:warehouse_families)',
    `warehouse_name` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('warehouse', ['id', 'statut', 'id_society', 'warehouse_name']);
        $this->addQuery('RENAME TABLE `warehouse` TO `warehouse_old`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `warehouse` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `old_id` INT UNSIGNED DEFAULT NULL,
  `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
  `company_id` INT UNSIGNED DEFAULT NULL,
  `families` SET('prison','production','réception','magasin pièces finies','expédition','magasin matières premières','camion') DEFAULT NULL COMMENT '(DC2Type:warehouse_families)',
  `name` VARCHAR(255) DEFAULT NULL,
  CONSTRAINT `IDX_ECB38BFC979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `warehouse` (`old_id`, `company_id`, `families`, `name`)
SELECT
    `id`,
    (
        SELECT `company`.`id`
        FROM `company`
        WHERE `company`.`society_id` = (
            SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `warehouse_old`.`id_society`
        )
    ),
    CASE
        WHEN `warehouse_name` LIKE '%prison%' THEN 'prison'
        WHEN `warehouse_name` LIKE '%fabrication%' OR `warehouse_name` LIKE '%production%' THEN 'production'
        WHEN `warehouse_name` LIKE '%réception%' OR `warehouse_name` LIKE '%import%' THEN 'réception'
        WHEN `warehouse_name` LIKE '%vente%' THEN 'magasin pièces finies'
        WHEN `warehouse_name` LIKE '%expédition%' OR `warehouse_name` LIKE '%export%' THEN 'expédition'
        WHEN `warehouse_name` LIKE '%besoin%' OR `warehouse_name` LIKE '%magasin%' THEN 'magasin matières premières'
        WHEN `warehouse_name` LIKE '%camion%' THEN 'camion'
        ELSE NULL
    END,
    `warehouse_name`
FROM `warehouse_old`
WHERE `statut` = 0
SQL);
        $this->addQuery('DROP TABLE `warehouse_old`');
    }

    private function upZones(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `society_zone` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_society` INT UNSIGNED NOT NULL,
    `nom` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('society_zone', ['id', 'id_society', 'nom']);
        $this->addQuery(<<<'SQL'
UPDATE `society_zone`
SET `id_society` = (
    SELECT `company`.`id`
    FROM `company`
    WHERE `company`.`society_id` = (
        SELECT `society`.`id` FROM `society` WHERE `society`.`old_id` = `society_zone`.`id_society`
    )
)
SQL);
        $this->addQuery(<<<'SQL'
ALTER TABLE `society_zone`
    CHANGE `id_society` `company_id` INT UNSIGNED NOT NULL,
    CHANGE `nom` `name` VARCHAR(255) NOT NULL,
    ADD CONSTRAINT `IDX_A0EBC007979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
SQL);
        $this->addQuery('RENAME TABLE `society_zone` TO `zone`');
    }
}
