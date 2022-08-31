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

final class Version20220830142635 extends AbstractMigration {
    private UserPasswordHasherInterface $hasher;

    /** @var Collection<int, string> */
    private readonly Collection $phoneQueries;

    /** @var Collection<int, string> */
    private readonly Collection $queries;

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
CREATE FUNCTION IS_NUMBER(s VARCHAR(255))
    RETURNS BOOLEAN DETERMINISTIC
    RETURN s REGEXP '^[0-9]+$'
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
        $this->addSql('DROP FUNCTION IS_NUMBER');
        $this->addSql('DROP FUNCTION IF_EMPTY');
    }

    protected function addSql(string $sql, array $params = [], array $types = []): void {
        parent::addSql(self::trim($sql), $params, $types);
    }

    private function addQuery(string $query): void {
        $this->queries->push(self::trim($query));
    }

    /**
     * @param string[] $roles
     */
    private function generateEmployee(int $company, string $initials, string $password, array $roles, string $username): void {
        ($user = (new Employee()))->setPassword($this->hasher->hashPassword($user, $password));
        /** @var string $hashedPassword */
        $hashedPassword = $user->getPassword();
        foreach ($roles as $role) {
            $user->addRole($role);
        }
        $this->addQuery(sprintf(
            <<<SQL
INSERT INTO `employee` (`company_id`, `emb_roles_roles`, `initials`, `name`, `password`, `surname`, `username`)
VALUES ($company, %s, '$initials', 'Super', %s, 'SUPER', '$username')
SQL,
            $this->platform->quoteStringLiteral(implode(',', $user->getRoles())),
            $this->platform->quoteStringLiteral($hashedPassword)
        ));
    }

    private function getPhoneQueries(): string {
        return $this->phoneQueries->join('; ');
    }

    private function getQueries(): string {
        return $this->queries->join('; ');
    }

    /**
     * @param string[] $columns
     * @param string[] $dates
     */
    private function insert(string $table, array $columns, array $dates = []): void {
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
                /** @phpstan-ignore-next-line */
                ->filter(static fn (array $row): bool => !isset($row['id']) || (int) ($row['id']) > 0)
                ->map(function (array $row) use ($columns, $dates): string {
                    $mapped = [];
                    foreach ($columns as $column) {
                        $value = $row[$column] ?? null;
                        if (is_string($value)) {
                            $value = trim($value);
                            if (strlen($value) === 0) {
                                $value = null;
                            }
                            if (!empty($value) && in_array($column, $dates) && str_ends_with($value, '00')) {
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
    `address`.`id`,
    `address`.`address1`,
    `address`.`address2`,
    `address`.`city`,
    UCASE(`country`.`code`),
    `address`.`zip`,
    `customer`.`id`,
    `address`.`nom`,
    '$type'
FROM `address`
INNER JOIN `society` ON `address`.`id_customer` = `society`.`old_id`
INNER JOIN `customer` ON `society`.`id` = `customer`.`society_id`
LEFT JOIN `country` ON `address`.`id_country` = `country`.`id`
WHERE `address`.`statut` = 0
AND `address`.`typeaddress` LIKE '%$like%'
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
        $this->upOperationTypes();
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
        $this->upProjectOperations();
        $this->upSupplierComponents();
        $this->upSupplies();
        $this->upTeams();
        $this->upWarehouses();
        $this->upZones();
        // rank 4
        $this->upBills();
        $this->upComponentSupplierPrices();
        $this->upCustomerProductPrices();
        $this->upEmployees();
        $this->upEngines();
        $this->upSellingOrders();
        $this->upStocks();
        // rank 5
        $this->upDeliveryNotes();
        $this->upEmployeeEvents();
        $this->upItRequests();
        $this->upManufacturingOrders();
        $this->upNotifications();
        $this->upPlannings();
        $this->upPurchaseOrders();
        $this->upSellingOrderItems();
        $this->upSkills();
        // rank 6
        $this->upEngineEvents();
        $this->upExpeditions();
        $this->upManufacturingOperations();
        $this->upPurchaseOrderItems();
        // clean
        $this->addQuery('DROP TABLE `country`');
        $this->addQuery('DROP TABLE `customcode`');
        $this->addQuery('DROP TABLE `locale`');
        $this->addQuery('ALTER TABLE `attribute` DROP `old_id`');
        $this->addQuery('ALTER TABLE `bill` DROP `old_id`');
        $this->addQuery('ALTER TABLE `component` DROP `old_id`');
        $this->addQuery('ALTER TABLE `component_family` DROP `old_subfamily_id`');
        $this->addQuery('ALTER TABLE `customer_address` DROP `old_id`');
        $this->addQuery('ALTER TABLE `customer_contact` DROP `old_id`');
        $this->addQuery('ALTER TABLE `employee` DROP `old_id`, DROP `matricule`, DROP `id_society`');
        $this->addQuery('ALTER TABLE `engine` DROP `old_id`');
        $this->addQuery('ALTER TABLE `engine_group` DROP `old_id`');
        $this->addQuery('ALTER TABLE `expedition` DROP `old_id`');
        $this->addQuery('ALTER TABLE `invoice_time_due` DROP `id_old_invoicetimedue`, DROP `id_old_invoicetimeduesupplier`');
        $this->addQuery('ALTER TABLE `manufacturing_order` DROP `old_id`');
        $this->addQuery('ALTER TABLE `planning` DROP `old_id`');
        $this->addQuery('ALTER TABLE `product` DROP `old_id`, DROP `id_society`');
        $this->addQuery('ALTER TABLE `product_customer` DROP `old_id`');
        $this->addQuery('ALTER TABLE `product_family` DROP `old_subfamily_id`');
        $this->addQuery('ALTER TABLE `purchase_order` DROP `old_id`');
        $this->addQuery('ALTER TABLE `selling_order` DROP `old_id`');
        $this->addQuery('ALTER TABLE `selling_order_item` DROP `old_id`');
        $this->addQuery('ALTER TABLE `skill_type` DROP `old_id`');
        $this->addQuery('ALTER TABLE `society` DROP `old_id`');
        $this->addQuery('ALTER TABLE `stock` DROP `old_id`');
        $this->addQuery('ALTER TABLE `supplier_component` DROP `old_id`');
        $this->addQuery('ALTER TABLE `warehouse` DROP `old_id`');
    }

    private function upAttributes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `attribut` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `libelle` VARCHAR(100) NOT NULL,
    `attribut_id_family` VARCHAR(255) DEFAULT NULL
)
SQL);
        $this->insert('attribut', ['id', 'statut', 'description', 'libelle', 'attribut_id_family']);
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
INSERT INTO `attribute` (`old_id`, `deleted`, `description`, `name`, `attribut_id_family`)
SELECT `id`, `statut`, `description`, `libelle`, `attribut_id_family`
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
    `customer_id` INT UNSIGNED DEFAULT NULL,
    `due_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `emb_blocker_state` ENUM('blocked', 'disabled', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:blocker_state)',
    `emb_state_state` ENUM('billed', 'draft', 'partially_paid', 'paid') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:bill_state)',
    `excl_tax_code` VARCHAR(6) DEFAULT NULL,
    `excl_tax_denominator` VARCHAR(6) DEFAULT NULL,
    `excl_tax_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `force_vat` ENUM('TVA par défaut selon le pays du client', 'Force AVEC TVA', 'Force SANS TVA') DEFAULT 'TVA par défaut selon le pays du client' NOT NULL COMMENT '(DC2Type:vat_message_force)',
    `incl_tax_code` VARCHAR(6) DEFAULT NULL,
    `incl_tax_denominator` VARCHAR(6) DEFAULT NULL,
    `incl_tax_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `notes` TEXT DEFAULT NULL,
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
    `customer_id`,
    `due_date`,
    `emb_blocker_state`,
    `emb_state_state`,
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
    `invoicecustomer`.`id`,
    `invoicecustomer`.`date_facturation`,
    `company`.`id`,
    `customer_contact`.`id`,
    `customer`.`id`,
    `invoicecustomer`.`date_echeance`,
    CASE
        WHEN `invoicecustomer`.`id_invoicecustomerstatus` IN (1, 2, 3, 4) THEN 'enabled'
        WHEN `invoicecustomer`.`id_invoicecustomerstatus` = 6 THEN 'blocked'
        ELSE 'disabled'
    END,
    CASE
        WHEN `invoicecustomer`.`id_invoicecustomerstatus` IN (2, 6) THEN 'billed'
        WHEN `invoicecustomer`.`id_invoicecustomerstatus` = 3 THEN 'partially_paid'
        WHEN `invoicecustomer`.`id_invoicecustomerstatus` = 4 THEN 'paid'
        ELSE 'draft'
    END,
    'EUR',
    `invoicecustomer`.`total_ht`,
    CASE
        WHEN `invoicecustomer`.`force_tva` = 1 THEN 'Force SANS TVA'
        WHEN `invoicecustomer`.`force_tva` = 2 THEN 'Force AVEC TVA'
        ELSE 'TVA par défaut selon le pays du client'
    END,
    'EUR',
    `invoicecustomer`.`total_ttc`,
    IF(
        LENGTH(CONCAT(IFNULL(`invoicecustomer`.`info_public`, ''), IFNULL(`invoicecustomer`.`infos_privees`, ''), IFNULL(`invoicecustomer`.`commentaire`, ''))) = 0,
        NULL,
        CONCAT(IFNULL(`invoicecustomer`.`info_public`, ''), IFNULL(`invoicecustomer`.`infos_privees`, ''), IFNULL(`invoicecustomer`.`commentaire`, ''))
    ),
    `invoicecustomer`.`invoice_number`,
    'EUR',
    `vat_message`.`id`,
    `invoicecustomer`.`tva`
FROM `invoicecustomer`
INNER JOIN `society` `society_customer` ON `invoicecustomer`.`id_customer` = `society_customer`.`old_id`
INNER JOIN `customer` ON `society_customer`.`id` = `customer`.`society_id`
LEFT JOIN `society` `society_company` ON `invoicecustomer`.`id_society` = `society_company`.`old_id`
LEFT JOIN `company` ON `society_company`.`id` = `company`.`society_id`
LEFT JOIN `customer_contact` ON `invoicecustomer`.`id_contact` = `customer_contact`.`old_id`
LEFT JOIN `vat_message` ON `invoicecustomer`.`id_messagetva` = `vat_message`.`id`
WHERE `invoicecustomer`.`statut` = 0
SQL);
        $this->addQuery('DROP TABLE `invoicecustomer`');
    }

    private function upCarriers(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `carrier` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `nom` TEXT NOT NULL
)
SQL);
        $this->insert('carrier', ['id', 'statut', 'nom']);
        $this->addQuery('RENAME TABLE `carrier` TO `old_carrier`');
        $this->addQuery(<<<'SQL'
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
        $this->addQuery('INSERT INTO `carrier` (`name`) SELECT `nom` FROM `old_carrier` WHERE `statut` = 0');
        $this->addQuery('DROP TABLE `old_carrier`');
    }

    private function upColors(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `couleur` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `rgb` VARCHAR(7) DEFAULT NULL
)
SQL);
        $this->insert('couleur', ['id', 'name', 'rgb']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `color` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `name` VARCHAR(20) NOT NULL,
    `rgb` CHAR(7) NOT NULL COMMENT '(DC2Type:char)'
)
SQL);
        $this->addQuery('INSERT INTO `color` (`name`, `rgb`) SELECT UCFIRST(`name`), `rgb` FROM `couleur` WHERE `rgb` IS NOT NULL');
        $this->addQuery('DROP TABLE `couleur`');
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
SELECT `component`.`id`, `attribute`.`id`, `component_attribut`.`valeur_attribut`
FROM `component_attribut`
INNER JOIN `attribute` ON `component_attribut`.`id_attribut` = `attribute`.`old_id`
INNER JOIN `component` ON `component_attribut`.`id_component` = `component`.`old_id`
WHERE `component_attribut`.`valeur_attribut` IS NOT NULL AND TRIM(`component_attribut`.`valeur_attribut`) != ''
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
    `family_name` VARCHAR(255) NOT NULL,
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
    `id_component` INT UNSIGNED DEFAULT NULL,
    `section` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `traction` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `tolerance_traction` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `obligation_traction` BOOLEAN DEFAULT TRUE NOT NULL,
    `hauteur` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `tolerance_hauteur` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `obligation_hauteur` BOOLEAN DEFAULT TRUE NOT NULL,
    `largeur` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `tolerance_largeur` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `obligation_largeur` BOOLEAN DEFAULT TRUE NOT NULL
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
CREATE TABLE `component_reference_value` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `component_id` INT UNSIGNED DEFAULT NULL,
    `height_required` BOOLEAN DEFAULT TRUE NOT NULL,
    `height_tolerance_code` VARCHAR(6) DEFAULT NULL,
    `height_tolerance_denominator` VARCHAR(6) DEFAULT NULL,
    `height_tolerance_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `height_value_code` VARCHAR(6) DEFAULT NULL,
    `height_value_denominator` VARCHAR(6) DEFAULT NULL,
    `height_value_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `section_code` VARCHAR(6) DEFAULT NULL,
    `section_denominator` VARCHAR(6) DEFAULT NULL,
    `section_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `tensile_required` BOOLEAN DEFAULT TRUE NOT NULL,
    `tensile_tolerance_code` VARCHAR(6) DEFAULT NULL,
    `tensile_tolerance_denominator` VARCHAR(6) DEFAULT NULL,
    `tensile_tolerance_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `tensile_value_code` VARCHAR(6) DEFAULT NULL,
    `tensile_value_denominator` VARCHAR(6) DEFAULT NULL,
    `tensile_value_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `width_required` BOOLEAN DEFAULT TRUE NOT NULL,
    `width_tolerance_code` VARCHAR(6) DEFAULT NULL,
    `width_tolerance_denominator` VARCHAR(6) DEFAULT NULL,
    `width_tolerance_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `width_value_code` VARCHAR(6) DEFAULT NULL,
    `width_value_denominator` VARCHAR(6) DEFAULT NULL,
    `width_value_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CONSTRAINT `IDX_648B6870E2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `component_reference_value` (
    `component_id`,
    `height_required`,
    `height_tolerance_code`,
    `height_tolerance_value`,
    `height_value_code`,
    `height_value_value`,
    `section_code`,
    `section_value`,
    `tensile_required`,
    `tensile_tolerance_code`,
    `tensile_tolerance_value`,
    `tensile_value_code`,
    `tensile_value_value`,
    `width_required`,
    `width_tolerance_code`,
    `width_tolerance_value`,
    `width_value_code`,
    `width_value_value`
) SELECT
    `component`.`id`,
    `component_quality_values`.`obligation_hauteur`,
    'mm',
    `component_quality_values`.`tolerance_hauteur`,
    'mm',
    `component_quality_values`.`hauteur`,
    'mm²',
    `component_quality_values`.`section`,
    `component_quality_values`.`obligation_traction`,
    'mm²',
    `component_quality_values`.`tolerance_traction`,
    'mm²',
    `component_quality_values`.`traction`,
    `component_quality_values`.`obligation_largeur`,
    'mm',
    `component_quality_values`.`tolerance_largeur`,
    'mm',
    `component_quality_values`.`largeur`
FROM `component_quality_values`
INNER JOIN `component` ON `component_quality_values`.`id_component` = `component`.`old_id`
SQL);
        $this->addQuery('DROP TABLE `component_quality_values`');
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
        $this->insert(
            table: 'component',
            columns: [
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
            ],
            dates: ['endOfLife']
        );
        $this->addQuery('RENAME TABLE `component` TO `component_old`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `component` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `copper_weight_code` VARCHAR(6) DEFAULT NULL,
    `copper_weight_denominator` VARCHAR(6) DEFAULT NULL,
    `copper_weight_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `customs_code` VARCHAR(16) DEFAULT NULL,
    `emb_blocker_state` ENUM('blocked', 'disabled', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:blocker_state)',
    `emb_state_state` ENUM('agreed', 'draft', 'warning') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:component_manufacturing_operation_state)',
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
    `customs_code`,
    `emb_blocker_state`,
    `emb_state_state`,
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
    `component_old`.`customcode`,
    CASE
        WHEN `component_old`.`id_componentstatus` IN (3, 5) THEN 'disabled'
        WHEN `component_old`.`id_componentstatus` = 4 THEN 'blocked'
        ELSE 'enabled'
    END,
    IF(`component_old`.`id_componentstatus` IN (2, 3, 4, 5), 'agreed', 'warning'),
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
    `supplier_component`.`id`,
    'EUR',
    `component_supplier_price`.`price`,
    `unit`.`code`,
    `component_supplier_price`.`quantity`,
    `component_supplier_price`.`refsupplier`
FROM `component_supplier_price`
INNER JOIN `supplier_component` ON `component_supplier_price`.`id_component_supplier` = `supplier_component`.`old_id`
INNER JOIN `component` ON `supplier_component`.`component_id` = `component`.`id`
LEFT JOIN `unit` ON `component`.`unit_id` = `unit`.`id`
WHERE `component_supplier_price`.`statut` = 0
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
    `address_address` VARCHAR(160) DEFAULT NULL,
    `address_address2` VARCHAR(110) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(80) DEFAULT NULL,
    `address_phone_number` VARCHAR(255) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `default` BOOLEAN DEFAULT FALSE NOT NULL,
    `kind` ENUM('comptabilité', 'chiffrage', 'direction', 'ingénierie', 'fabrication', 'achat', 'qualité', 'commercial', 'approvisionnement') DEFAULT 'comptabilité' NOT NULL COMMENT '(DC2Type:contact)',
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
    `contact_old`.`id`,
    `contact_old`.`address1`,
    `contact_old`.`address2`,
    `contact_old`.`city`,
    UCASE(`country`.`code`),
    `contact_old`.`email`,
    `contact_old`.`phone`,
    `contact_old`.`zip`,
    `contact_old`.`prenom`,
    `contact_old`.`mobile`,
    `customer`.`id`,
    `contact_old`.`nom`
FROM `contact_old`
INNER JOIN `society` ON `contact_old`.`id_customer` = `society`.`old_id`
INNER JOIN `customer` ON `society`.`id` = `customer`.`society_id`
LEFT JOIN `country` ON `contact_old`.`id_country` = `country`.`id`
WHERE `contact_old`.`statut` = 0
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `supplier_contact` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `address_address` VARCHAR(160) DEFAULT NULL,
    `address_address2` VARCHAR(110) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(80) DEFAULT NULL,
    `address_phone_number` VARCHAR(255) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `default` BOOLEAN DEFAULT FALSE NOT NULL,
    `kind` ENUM('comptabilité', 'chiffrage', 'direction', 'ingénierie', 'fabrication', 'achat', 'qualité', 'commercial', 'approvisionnement') DEFAULT 'comptabilité' NOT NULL COMMENT '(DC2Type:contact)',
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
    `contact_old`.`address1`,
    `contact_old`.`address2`,
    `contact_old`.`city`,
    UCASE(`country`.`code`),
    `contact_old`.`email`,
    `contact_old`.`phone`,
    `contact_old`.`zip`,
    `contact_old`.`prenom`,
    `contact_old`.`mobile`,
    `supplier`.`id`,
    `contact_old`.`nom`
FROM `contact_old`
INNER JOIN `society` ON `contact_old`.`id_supplier` = `society`.`old_id`
INNER JOIN `supplier` ON `society`.`id` = `supplier`.`society_id`
LEFT JOIN `country` ON `contact_old`.`id_country` = `country`.`id`
WHERE `contact_old`.`statut` = 0
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
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `active` BOOLEAN DEFAULT FALSE NOT NULL,
    `base` DOUBLE PRECISION DEFAULT 1 NOT NULL,
    `code` CHAR(3) NOT NULL COMMENT '(DC2Type:char)',
    `parent_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_6956883F727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `currency` (`id`)
)
SQL);
        $currencies = collect(Currencies::getCurrencyCodes())
            ->map(fn (string $code): string => sprintf(
                "(%s, {$this->platform->quoteStringLiteral($code)}, %s)",
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
  `zip` VARCHAR(255) DEFAULT NULL,
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
    `address_address` VARCHAR(160) DEFAULT NULL,
    `address_address2` VARCHAR(110) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(80) DEFAULT NULL,
    `address_phone_number` VARCHAR(18) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `customer_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `type` ENUM('billing', 'delivery') NOT NULL COMMENT '(DC2Type:customer_address)',
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
    `product_customer_price`.`price`,
    `product_customer`.`id`,
    `unit`.`code`,
    `product_customer_price`.`quantity`
FROM `product_customer_price`
INNER JOIN `product_customer` ON `product_customer_price`.`id_product_customer` = `product_customer`.`old_id`
INNER JOIN `product` ON `product_customer`.`product_id` = `product`.`id`
LEFT JOIN `unit` ON `product`.`unit_id` = `unit`.`id`
WHERE `product_customer_price`.`statut` = 0
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
SELECT `product_customer_old`.`id`, `customer`.`id`, `product`.`id`
FROM `product_customer_old`
INNER JOIN `society` ON `product_customer_old`.`id_customer` = `society`.`old_id`
INNER JOIN `customer` ON `society`.`id` = `customer`.`society_id`
INNER JOIN `product` ON `product_customer_old`.`id_product` = `product`.`old_id`
WHERE `product_customer_old`.`statut` = 0
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
SELECT `product_customer`.`id`, `company`.`id`
FROM `product_customer_old`
INNER JOIN `product_customer` ON `product_customer_old`.`id` = `product_customer`.`old_id`
INNER JOIN `product` ON `product_customer_old`.`id_product` = `product`.`old_id`
INNER JOIN `society` ON `product`.`id_society` = `society`.`old_id`
INNER JOIN `company` ON `society`.`id` = `company`.`society_id`
WHERE `product_customer_old`.`statut` = 0
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
    `date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `emb_state_state` ENUM('agreed', 'asked', 'closed', 'rejected') DEFAULT 'asked' NOT NULL COMMENT '(DC2Type:event_state)',
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
    `date`,
    `emb_state_state`,
    `freight_surcharge_code`,
    `freight_surcharge_value`,
    `non_billable`,
    `ref`
) SELECT
    `bill`.`id`,
    `company`.`id`,
    `deliveryform`.`date_depart`,
    'closed',
    'EUR',
    `deliveryform`.`supplement_fret`,
    `deliveryform`.`no_invoice`,
    `deliveryform`.`deliveryform_number`
FROM `deliveryform`
LEFT JOIN `society` ON `deliveryform`.`id_society` = `society`.`old_id`
LEFT JOIN `company` ON `society`.`id` = `company`.`society_id`
LEFT JOIN `invoicecustomer_deliveryform`
    ON `deliveryform`.`id` = `invoicecustomer_deliveryform`.`id_deliveryform`
    AND `invoicecustomer_deliveryform`.`statut` = 0
LEFT JOIN `bill`
    ON `deliveryform`.`invoice_number` = `bill`.`ref`
    AND `invoicecustomer_deliveryform`.`id_invoicecustomer` = `bill`.`old_id`
WHERE `deliveryform`.`statut` = 0
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
    `old_employee_event`.`date_event`,
    `employee`.`id`,
    `employee`.`company_id`,
    `old_employee_event`.`description`,
    `event_type`.`id`
FROM `old_employee_event`
INNER JOIN `employee` ON `old_employee_event`.`id_employee` = `employee`.`old_id`
INNER JOIN `event_type` ON `old_employee_event`.`id_motif` = `event_type`.`id`
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
    `nom_pers_a_contacter` VARCHAR(255) DEFAULT NULL,
    `prenom_pers_a_contacter` VARCHAR(255) DEFAULT NULL,
    `id_phone_prefix_pers_a_contacter` INT UNSIGNED DEFAULT NULL,
    `tel_pers_a_contacter` VARCHAR(255) DEFAULT NULL,
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
    `matricule` INT UNSIGNED DEFAULT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `address_address` VARCHAR(160) DEFAULT NULL,
    `address_address2` VARCHAR(110) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(80) DEFAULT NULL,
    `address_phone_number` VARCHAR(18) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `birthday` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `birth_city` VARCHAR(255) DEFAULT NULL,
    `company_id` INT UNSIGNED DEFAULT NULL,
    `id_society` INT UNSIGNED DEFAULT NULL,
    `emb_roles_roles` TEXT NOT NULL COMMENT '(DC2Type:simple_array)',
    `emb_blocker_state` ENUM('blocked', 'disabled', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:blocker_state)',
    `emb_state_state` ENUM('agreed', 'warning') DEFAULT 'warning' NOT NULL COMMENT '(DC2Type:employee_engine_state)',
    `entry_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `gender` ENUM('female', 'male') DEFAULT 'male' COMMENT '(DC2Type:gender_place)',
    `initials` VARCHAR(255) NOT NULL,
    `level_of_study` VARCHAR(255) DEFAULT NULL,
    `manager_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(30) NOT NULL,
    `notes` VARCHAR(255) DEFAULT NULL,
    `password` VARCHAR(60) DEFAULT NULL COMMENT '(DC2Type:char)',
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
        $this->generateEmployee(
            company: 1,
            initials: 'super',
            password: 'super',
            roles: [
                Roles::ROLE_ACCOUNTING_ADMIN,
                Roles::ROLE_HR_ADMIN,
                Roles::ROLE_IT_ADMIN,
                Roles::ROLE_LEVEL_DIRECTOR,
                Roles::ROLE_LOGISTICS_ADMIN,
                Roles::ROLE_MAINTENANCE_ADMIN,
                Roles::ROLE_MANAGEMENT_ADMIN,
                Roles::ROLE_PRODUCTION_ADMIN,
                Roles::ROLE_PROJECT_ADMIN,
                Roles::ROLE_PURCHASE_ADMIN,
                Roles::ROLE_QUALITY_ADMIN,
                Roles::ROLE_SELLING_ADMIN
            ],
            username: 'super'
        );
        $this->generateEmployee(
            company: 4,
            initials: 'su-md',
            password: 'super',
            roles: [
                Roles::ROLE_ACCOUNTING_ADMIN,
                Roles::ROLE_HR_ADMIN,
                Roles::ROLE_IT_ADMIN,
                Roles::ROLE_LEVEL_DIRECTOR,
                Roles::ROLE_LOGISTICS_ADMIN,
                Roles::ROLE_MAINTENANCE_ADMIN,
                Roles::ROLE_MANAGEMENT_ADMIN,
                Roles::ROLE_PRODUCTION_ADMIN,
                Roles::ROLE_PROJECT_ADMIN,
                Roles::ROLE_PURCHASE_ADMIN,
                Roles::ROLE_QUALITY_ADMIN,
                Roles::ROLE_SELLING_ADMIN
            ],
            username: 'super-md'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'su-tn',
            password: 'super',
            roles: [
                Roles::ROLE_ACCOUNTING_ADMIN,
                Roles::ROLE_HR_ADMIN,
                Roles::ROLE_IT_ADMIN,
                Roles::ROLE_LEVEL_DIRECTOR,
                Roles::ROLE_LOGISTICS_ADMIN,
                Roles::ROLE_MAINTENANCE_ADMIN,
                Roles::ROLE_MANAGEMENT_ADMIN,
                Roles::ROLE_PRODUCTION_ADMIN,
                Roles::ROLE_PROJECT_ADMIN,
                Roles::ROLE_PURCHASE_ADMIN,
                Roles::ROLE_QUALITY_ADMIN,
                Roles::ROLE_SELLING_ADMIN
            ],
            username: 'super-tn'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnloa',
            password: 'tnloa',
            roles: [Roles::ROLE_LOGISTICS_ADMIN],
            username: 'tnloa'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnlor',
            password: 'tnlor',
            roles: [Roles::ROLE_LOGISTICS_READER],
            username: 'tnlor'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnlow',
            password: 'tnlow',
            roles: [Roles::ROLE_LOGISTICS_WRITER],
            username: 'tnlow'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnmaa',
            password: 'tnmaa',
            roles: [Roles::ROLE_MAINTENANCE_ADMIN],
            username: 'tnmaa'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnmar',
            password: 'tnmar',
            roles: [Roles::ROLE_MAINTENANCE_READER],
            username: 'tnmar'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnmaw',
            password: 'tnmaw',
            roles: [Roles::ROLE_MAINTENANCE_WRITER],
            username: 'tnmaw'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnpoa',
            password: 'tnpoa',
            roles: [Roles::ROLE_PRODUCTION_ADMIN],
            username: 'tnpoa'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnpor',
            password: 'tnpor',
            roles: [Roles::ROLE_PRODUCTION_READER],
            username: 'tnpor'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnpow',
            password: 'tnpow',
            roles: [Roles::ROLE_PRODUCTION_WRITER],
            username: 'tnpow'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnqua',
            password: 'tnqua',
            roles: [Roles::ROLE_QUALITY_ADMIN],
            username: 'tnqua'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnqur',
            password: 'tnqur',
            roles: [Roles::ROLE_QUALITY_READER],
            username: 'tnqur'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'tnquw',
            password: 'tnquw',
            roles: [Roles::ROLE_QUALITY_WRITER],
            username: 'tnquw'
        );
        $this->generateEmployee(
            company: 3,
            initials: 'user',
            password: 'user',
            roles: [],
            username: 'user'
        );
        $this->addQuery(<<<'SQL'
INSERT INTO `employee` (
    `old_id`,
    `matricule`,
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
    `employee_old`.`id`,
    `employee_old`.`matricule`,
    `employee_old`.`address`,
    `employee_old`.`ville`,
    UCASE(`country`.`code`),
    `employee_old`.`email`,
    `employee_old`.`tel`,
    `employee_old`.`code_postal`,
    `employee_old`.`d_naissance`,
    `employee_old`.`lieu_de_naissance`,
    `company`.`id`,
    `employee_old`.`id_society`,
    CASE
        WHEN `employee_old`.`id_role` = 100 THEN 'ROLE_ACCOUNTING_ADMIN,ROLE_HR_ADMIN,ROLE_IT_ADMIN,ROLE_LOGISTICS_ADMIN,ROLE_MAINTENANCE_ADMIN,ROLE_MANAGEMENT_ADMIN,ROLE_PRODUCTION_ADMIN,ROLE_PROJECT_ADMIN,ROLE_PURCHASE_ADMIN,ROLE_QUALITY_ADMIN,ROLE_SELLING_ADMIN,ROLE_USER'
        WHEN `employee_old`.`id_role` < 300 OR (`employee_old`.`id_role` % 100 = 0 AND `employee_old`.`id_role` NOT IN (300, 400, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500)) THEN 'ROLE_ACCOUNTING_ADMIN,ROLE_HR_ADMIN,ROLE_LOGISTICS_ADMIN,ROLE_MAINTENANCE_ADMIN,ROLE_MANAGEMENT_ADMIN,ROLE_PRODUCTION_ADMIN,ROLE_PROJECT_ADMIN,ROLE_PURCHASE_ADMIN,ROLE_QUALITY_ADMIN,ROLE_SELLING_ADMIN,ROLE_USER'
        WHEN `employee_old`.`id_role` < 311 THEN 'ROLE_PRODUCTION_ADMIN,ROLE_QUALITY_ADMIN,ROLE_USER'
        WHEN `employee_old`.`id_role` < 400 THEN 'ROLE_PRODUCTION_WRITER,ROLE_QUALITY_WRITER,ROLE_USER'
        WHEN `employee_old`.`id_role` = 400 THEN 'ROLE_HR_ADMIN,ROLE_USER'
        WHEN `employee_old`.`id_role` < 500 THEN 'ROLE_HR_WRITER,ROLE_USER'
        WHEN `employee_old`.`id_role` < 600 THEN 'ROLE_HR_WRITER,ROLE_LOGISTICS_WRITER,ROLE_PRODUCTION_READER,ROLE_PROJECT_READER,ROLE_PURCHASE_WRITER,ROLE_QUALITY_READER,ROLE_SELLING_WRITER,ROLE_USER'
        WHEN `employee_old`.`id_role` IN (600, 1000) THEN 'ROLE_PRODUCTION_ADMIN,ROLE_USER'
        WHEN `employee_old`.`id_role` < 700 OR (`employee_old`.`id_role` > 1000 AND `employee_old`.`id_role` < 1100) THEN 'ROLE_PRODUCTION_WRITER,ROLE_USER'
        WHEN `employee_old`.`id_role` = 700 THEN 'ROLE_LOGISTICS_ADMIN,ROLE_PRODUCTION_ADMIN,ROLE_PROJECT_ADMIN,ROLE_PURCHASE_ADMIN,ROLE_SELLING_ADMIN,ROLE_USER'
        WHEN `employee_old`.`id_role` < 800 THEN 'ROLE_LOGISTICS_WRITER,ROLE_PRODUCTION_WRITER,ROLE_PROJECT_WRITER,ROLE_PURCHASE_WRITER,ROLE_SELLING_WRITER,ROLE_USER'
        WHEN `employee_old`.`id_role` < 910 THEN 'ROLE_PROJECT_ADMIN,ROLE_PURCHASE_ADMIN,ROLE_SELLING_ADMIN,ROLE_USER'
        WHEN `employee_old`.`id_role` < 1000 THEN 'ROLE_PROJECT_WRITER,ROLE_PURCHASE_WRITER,ROLE_SELLING_WRITER,ROLE_USER'
        WHEN `employee_old`.`id_role` = 1100 THEN 'ROLE_PRODUCTION_ADMIN,ROLE_PROJECT_ADMIN,ROLE_QUALITY_ADMIN,ROLE_USER'
        WHEN `employee_old`.`id_role` < 1300 THEN 'ROLE_PRODUCTION_WRITER,ROLE_PROJECT_WRITER,ROLE_QUALITY_WRITER,ROLE_USER'
        WHEN `employee_old`.`id_role` < 1320 THEN 'ROLE_PRODUCTION_ADMIN,ROLE_PROJECT_ADMIN,ROLE_QUALITY_ADMIN,ROLE_SELLING_ADMIN,ROLE_USER'
        WHEN `employee_old`.`id_role` < 1400 THEN 'ROLE_PRODUCTION_WRITER,ROLE_PROJECT_WRITER,ROLE_QUALITY_WRITER,ROLE_SELLING_WRITER,ROLE_USER'
        ELSE 'ROLE_USER'
    END,
    `employee_old`.`d_entree`,
    IF(`employee_old`.`sexe` = 'F', 'female', 'male'),
    IF_EMPTY(`employee_old`.`initials`, CONCAT(UCASE(LEFT(`employee_old`.`nom`, 2)), '-', UCFIRST(LEFT(`employee_old`.`prenom`, 2)))),
    `employee_old`.`lvl_etude`,
    `employee_old`.`id_resp`,
    `employee_old`.`prenom`,
    `employee_old`.`password`,
    CASE
        WHEN `employee_old`.`situation` LIKE 'mari%' THEN 'married'
        WHEN `employee_old`.`situation` LIKE 've%' THEN 'windowed'
        ELSE 'single'
    END,
    `employee_old`.`n_secu`,
    `employee_old`.`nom`,
    `employee_old`.`user_gp`,
    `employee_old`.`login`,
    `employee_old`.`nom_pers_a_contacter`,
    `employee_old`.`prenom_pers_a_contacter`,
    `employee_old`.`id_phone_prefix_pers_a_contacter`,
    `employee_old`.`tel_pers_a_contacter`
FROM `employee_old`
LEFT JOIN `country` ON `employee_old`.`id_phone_prefix` = `country`.`id`
INNER JOIN `society` ON `employee_old`.`id_society` = `society`.`old_id`
INNER JOIN `company` ON `society`.`id` = `company`.`society_id`
WHERE `employee_old`.`status` = 0
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
    `password` TEXT NOT NULL,
    `nom` VARCHAR(255) NOT NULL,
    `prenom` VARCHAR(255) DEFAULT NULL
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
        $this->addQuery('UPDATE `employee` SET `name` = UCFIRST(`name`), `surname` = UCASE(`surname`)');
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
    UCFIRST(`prenom_pers_a_contacter`),
    `tel_pers_a_contacter`,
    UCASE(`nom_pers_a_contacter`),
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
    `date` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `done` BOOLEAN DEFAULT FALSE NOT NULL,
    `emergency` TINYINT UNSIGNED DEFAULT 1 COMMENT '(DC2Type:tinyint)',
    `employee_id` INT UNSIGNED DEFAULT NULL,
    `emb_state_state` ENUM('agreed', 'asked', 'closed', 'rejected') DEFAULT 'asked' NOT NULL COMMENT '(DC2Type:event_state)',
    `engine_id` INT UNSIGNED DEFAULT NULL,
    `intervention_notes` TEXT DEFAULT NULL,
    `managing_company_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) DEFAULT NULL,
    `notes` VARCHAR(255) DEFAULT NULL,
    `planned_by_id` INT UNSIGNED DEFAULT NULL,
    `type` ENUM('maintenance', 'request') NOT NULL COMMENT '(DC2Type:engine_event)',
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
    `emb_state_state`,
    `date`,
    `done`,
    `engine_id`,
    `managing_company_id`,
    `notes`,
    `planned_by_id`,
    `type`
) SELECT
    IF(`status_planning` = 1, 'agreed', 'closed'),
    `engine_maintenance_planning`.`date_planning`,
    `engine_maintenance_planning`.`status_planning` != 1,
    `engine`.`id`,
    `zone`.`company_id`,
    UCFIRST(`engine_maintenance_planning`.`comment`),
    `planning`.`id`,
    'maintenance'
FROM `engine_maintenance_planning`
INNER JOIN `engine` ON `engine_maintenance_planning`.`id_engine_maintenance` = `engine`.`old_id`
INNER JOIN `planning` ON `engine_maintenance_planning`.`id_maintenance` = `planning`.`old_id`
LEFT JOIN `zone` ON `engine`.`zone_id` = `zone`.`id`
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
    `emb_state_state`,
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
    IF(`statut` = 2, 'closed', 'asked'),
    `engine_request_event`.`date_intervention`,
    `engine_request_event`.`statut` = 2,
    `engine_request_event`.`urgence`,
    `employee`.`id`,
    `engine`.`id`,
    UCFIRST(`engine_request_event`.`commentaire_intervention`),
    `company`.`id`,
    UCFIRST(`engine_request_event`.`commentaire`),
    'request'
FROM `engine_request_event`
INNER JOIN `engine` ON `engine_request_event`.`id_engine` = `engine`.`old_id`
LEFT JOIN `employee` ON `engine_request_event`.`id_user_intervention` = `employee`.`old_id`
LEFT JOIN `society` ON `engine_request_event`.`id_society`= `society`.`old_id`
LEFT JOIN `company` ON `society`.`id` = `company`.`society_id`
SQL);
        $this->addQuery('DROP TABLE `engine_request_event`');
    }

    private function upEngineGroups(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine_group` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `libelle` VARCHAR(255) DEFAULT NULL,
    `code` VARCHAR(3) DEFAULT NULL,
    `id_family_group` INT UNSIGNED DEFAULT NULL,
    `organe_securite` BOOLEAN DEFAULT FALSE NOT NULL
)
SQL);
        $this->insert('engine_group', ['id', 'code', 'libelle', 'id_family_group', 'organe_securite']);
        $this->addQuery('RENAME TABLE `engine_group` TO `old_engine_group`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine_group` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `code` VARCHAR(3) NOT NULL,
    `name` VARCHAR(35) NOT NULL,
    `safety_device` BOOLEAN DEFAULT FALSE NOT NULL,
    `type` ENUM('counter-part', 'tool', 'workstation') NOT NULL COMMENT '(DC2Type:engine)'
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `engine_group` (`old_id`, `code`, `name`, `safety_device`, `type`)
SELECT `id`, `code`, UCFIRST(`libelle`), `organe_securite`, IF(`id_family_group` = 1, 'workstation', 'tool')
FROM `old_engine_group`
SQL);
        $this->addQuery('DROP TABLE `old_engine_group`');
    }

    private function upEngines(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `nom` VARCHAR(255) DEFAULT NULL,
    `id_society` INT UNSIGNED DEFAULT NULL,
    `emplacement` VARCHAR(255) DEFAULT NULL,
    `id_engine_group` INT UNSIGNED DEFAULT NULL,
    `marque` VARCHAR(255) DEFAULT NULL,
    `date_fabrication` DATE DEFAULT NULL,
    `id_fabricant` INT DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    `reffourn` VARCHAR(255) DEFAULT NULL,
    `proprietaire` VARCHAR(100) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `d_entree` DATE DEFAULT NULL,
    `numero_serie` VARCHAR(255) DEFAULT NULL,
    `capabilite` TINYINT UNSIGNED DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `operateur_max` INT UNSIGNED DEFAULT NULL,
    `piece_jointe_validation` VARCHAR(255) DEFAULT NULL,
    `date_validation` DATE DEFAULT NULL COMMENT '1 year after validation'
)
SQL);
        $this->insert('engine', [
            'nom',
            'id_society',
            'emplacement',
            'id_engine_group',
            'marque',
            'date_fabrication',
            'id_fabricant',
            'ref',
            'reffourn',
            'proprietaire',
            'description',
            'd_entree',
            'numero_serie',
            'capabilite',
            'notes',
            'operateur_max',
            'piece_jointe_validation',
            'date_validation'
        ]);
        $this->addQuery('UPDATE `engine` SET `id_fabricant` = IF(`id_fabricant` IS NULL OR `id_fabricant` <= 0, NULL, `id_fabricant`)');
        $this->addQuery('ALTER TABLE `engine` CHANGE `id_fabricant` `id_fabricant` INT UNSIGNED DEFAULT NULL');
        $this->addQuery('RENAME TABLE `engine` TO `old_engine`');
        $this->addQuery('CREATE TABLE `computer_engine` (`id_engine` INT UNSIGNED DEFAULT NULL, `id_society_zone` INT UNSIGNED DEFAULT NULL)');
        $this->insert('computer_engine', ['id_engine', 'id_society_zone']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `engine` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `brand` VARCHAR(255) DEFAULT NULL,
    `code` VARCHAR(10) DEFAULT NULL,
    `emb_blocker_state` ENUM('blocked', 'disabled', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:blocker_state)',
    `emb_state_state` ENUM('agreed', 'warning') DEFAULT 'warning' NOT NULL COMMENT '(DC2Type:employee_engine_state)',
    `entry_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `group_id` INT UNSIGNED DEFAULT NULL,
    `max_operator` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '(DC2Type:tinyint)',
    `name` VARCHAR(255) NOT NULL,
    `notes` TEXT,
    `type` ENUM('counter-part','tool','workstation') NOT NULL COMMENT '(DC2Type:engine)',
    `zone_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_E8A81A8D9F2C3FAB` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`),
    CONSTRAINT `IDX_E8A81A8DFE54D947` FOREIGN KEY (`group_id`) REFERENCES `engine_group` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `engine` (
    `old_id`,
    `brand`,
    `code`,
    `emb_blocker_state`,
    `emb_state_state`,
    `entry_date`,
    `group_id`,
    `max_operator`,
    `name`,
    `type`,
    `zone_id`
) SELECT
    `old_engine`.`id`,
    `old_engine`.`marque`,
    `old_engine`.`ref`,
    CASE
        WHEN `old_engine`.`capabilite` IN (1, 2) THEN 'enabled'
        WHEN `old_engine`.`capabilite` = 3 THEN 'blocked'
        ELSE 'disabled'
    END,
    IF(`old_engine`.`capabilite` IN (2, 3), 'warning', 'agreed'),
    `old_engine`.`d_entree`,
    `engine_group`.`id`,
    `old_engine`.`operateur_max`,
    `old_engine`.`nom`,
    `engine_group`.`type`,
    `zone`.`id`
FROM `old_engine`
INNER JOIN `engine_group` ON `old_engine`.`id_engine_group` = `engine_group`.`old_id`
LEFT JOIN `computer_engine` ON `old_engine`.`id` = `computer_engine`.`id_engine`
LEFT JOIN `zone` ON `computer_engine`.`id_society_zone` = `zone`.`id`
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
    `old_engine`.`reffourn`,
    `old_engine`.`date_fabrication`,
    `engine`.`id`,
    `manufacturer`.`id`,
    `old_engine`.`numero_serie`
FROM `old_engine`
INNER JOIN `engine` ON `old_engine`.`id` = `engine`.`old_id`
LEFT JOIN `society` ON `old_engine`.`id_fabricant` = `society`.`old_id`
LEFT JOIN `manufacturer` ON `society`.`id` = `manufacturer`.`society_id`
SQL);
        $this->addQuery('DROP TABLE `old_engine`');
    }

    private function upEventTypes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee_eventlist` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `motif` VARCHAR(30) NOT NULL,
    `to_status` ENUM('agreed', 'warning') DEFAULT NULL COMMENT '(DC2Type:employee_engine_state)'
)
SQL);
        $this->insert('employee_eventlist', ['id', 'motif']);
        $this->addQuery('ALTER TABLE `employee_eventlist` CHANGE `motif` `name` VARCHAR(30) NOT NULL');
        $this->addQuery('UPDATE `employee_eventlist` SET `name` = UCFIRST(`name`)');
        $this->addQuery('RENAME TABLE `employee_eventlist` TO `event_type`');
    }

    private function upExpeditions(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `expedition` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_ordercustomer_product` INT UNSIGNED DEFAULT NULL,
    `id_stock` INT UNSIGNED DEFAULT NULL,
    `batchnumber` VARCHAR(255) DEFAULT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `quantity` INT UNSIGNED DEFAULT NULL,
    `date_livraison` DATE DEFAULT NULL,
    `id_deliveryform` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('expedition', [
            'id',
            'statut',
            'id_ordercustomer_product',
            'id_stock',
            'batchnumber',
            'location',
            'quantity',
            'date_livraison',
            'id_deliveryform'
        ]);
        $this->addQuery('RENAME TABLE `expedition` TO `old_expedition`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `expedition` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `batch_number` VARCHAR(255) DEFAULT NULL,
    `date` DATE NOT NULL COMMENT '(DC2Type:date_immutable)',
    `item_id` INT UNSIGNED DEFAULT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `note_id` INT UNSIGNED DEFAULT NULL,
    `stock_id` INT UNSIGNED DEFAULT NULL,
    `quantity_code` VARCHAR(6) DEFAULT NULL,
    `quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CONSTRAINT `IDX_692907E126F525E` FOREIGN KEY (`item_id`) REFERENCES `selling_order_item` (`id`),
    CONSTRAINT `IDX_692907E26ED0855` FOREIGN KEY (`note_id`) REFERENCES `delivery_note` (`id`),
    CONSTRAINT `IDX_692907EDCD6110` FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `expedition` (
    `old_id`,
    `batch_number`,
    `date`,
    `item_id`,
    `location`,
    `stock_id`,
    `quantity_code`,
    `quantity_value`
) SELECT
    `old_expedition`.`id`,
    `old_expedition`.`batchnumber`,
    `old_expedition`.`date_livraison`,
    `selling_order_item`.`id`,
    `old_expedition`.`location`,
    `stock`.`id`,
    'U',
    `old_expedition`.`quantity`
FROM `old_expedition`
LEFT JOIN `selling_order_item` ON `old_expedition`.`id_ordercustomer_product` = `selling_order_item`.`old_id`
LEFT JOIN `stock` ON `old_expedition`.`id_stock` = `stock`.`old_id`
SQL);
        $this->addQuery('DROP TABLE `old_expedition`');
    }

    private function upIncoterms(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `incoterms` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `code` VARCHAR(25) NOT NULL,
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
UPDATE `invoicetimedue`
LEFT JOIN `invoicetimedue` `duplicate`
    ON `invoicetimedue`.`libelle` = `duplicate`.`libelle`
    AND `invoicetimedue`.`id` < `duplicate`.`id`
SET `invoicetimedue`.`id_old_invoicetimeduesupplier` = `duplicate`.`id_old_invoicetimeduesupplier`
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
    `engine_fabricant_ou_contact`.`address`,
    `engine_fabricant_ou_contact`.`ville`,
    UCASE(`country`.`code`),
    `engine_fabricant_ou_contact`.`code_postal`,
    `engine_fabricant_ou_contact`.`id`,
    IF(`engine_fabricant_ou_contact`.`prenom` IS NULL, TRIM(`engine_fabricant_ou_contact`.`nom`), TRIM(CONCAT(`engine_fabricant_ou_contact`.`nom`, ' ', `engine_fabricant_ou_contact`.`prenom`))),
    `engine_fabricant_ou_contact`.`tel`
FROM `engine_fabricant_ou_contact`
LEFT JOIN `country` ON `engine_fabricant_ou_contact`.`id_phone_prefix` = `country`.`id`
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

    private function upManufacturingOperations(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `production_operation` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_society` INT UNSIGNED DEFAULT NULL,
    `ofnumber` VARCHAR(100) DEFAULT NULL,
    `id_poste` INT UNSIGNED DEFAULT NULL,
    `id_outils` INT UNSIGNED DEFAULT NULL,
    `date_debut` DATETIME DEFAULT NULL,
    `date_fin` DATETIME DEFAULT NULL,
    `quantity` INT DEFAULT NULL,
    `statut_production` INT UNSIGNED DEFAULT NULL,
    `commentaire` TEXT DEFAULT NULL,
    `nature_cloture` INT UNSIGNED DEFAULT NULL,
    `retouche` INT UNSIGNED DEFAULT NULL,
    `matricule_responsable` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('production_operation', [
            'id',
            'id_society',
            'ofnumber',
            'id_poste',
            'id_outils',
            'date_debut',
            'date_fin',
            'quantity',
            'statut_production',
            'commentaire',
            'nature_cloture',
            'retouche',
            'matricule_responsable'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `manufacturing_operation` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `emb_blocker_state` ENUM('blocked', 'closed', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:closer_state)',
    `emb_state_state` ENUM('agreed', 'draft', 'warning') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:component_manufacturing_operation_state)',
    `notes` VARCHAR(255) DEFAULT NULL,
    `operation_id` INT UNSIGNED DEFAULT NULL,
    `order_id` INT UNSIGNED DEFAULT NULL,
    `pic_id` INT UNSIGNED DEFAULT NULL,
    `started_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `workstation_id` INT UNSIGNED DEFAULT NULL,
    `zone_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_82088FAF44AC3583` FOREIGN KEY (`operation_id`) REFERENCES `project_operation` (`id`),
    CONSTRAINT `IDX_82088FAF8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `manufacturing_order` (`id`),
    CONSTRAINT `IDX_82088FAF9E51FD91` FOREIGN KEY (`pic_id`) REFERENCES `employee` (`id`),
    CONSTRAINT `IDX_82088FAFE29BB7D` FOREIGN KEY (`workstation_id`) REFERENCES `engine` (`id`),
    CONSTRAINT `IDX_82088FAF9F2C3FAB` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `manufacturing_operation` (
    `emb_blocker_state`,
    `emb_state_state`,
    `notes`,
    `order_id`,
    `pic_id`,
    `started_date`,
    `workstation_id`
) SELECT
    CASE
        WHEN `production_operation`.`statut_production` = 3 THEN 'blocked'
        WHEN `production_operation`.`statut_production` = 4 THEN 'closed'
        ELSE 'enabled'
    END,
    CASE
        WHEN `production_operation`.`statut_production` IN (1, 3, 4) THEN 'agreed'
        WHEN `production_operation`.`statut_production` = 2 THEN 'warning'
        ELSE 'draft'
    END,
    `production_operation`.`commentaire`,
    `manufacturing_order`.`id`,
    (SELECT MIN(`employee`.`id`) FROM `employee` WHERE `employee`.`matricule` = `production_operation`.`matricule_responsable`),
    `production_operation`.`date_debut`,
    `engine`.`id`
FROM `production_operation`
INNER JOIN `manufacturing_order` ON `production_operation`.`ofnumber` = `manufacturing_order`.`old_id`
LEFT JOIN `engine` ON `production_operation`.`id_poste` = `engine`.`old_id`
SQL);
        $this->addQuery('DROP TABLE `production_operation`');
        $this->addQuery(<<<'SQL'
CREATE TABLE `operation_employee` (
    `operation_id` INT UNSIGNED NOT NULL,
    `employee_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_B8E90A2C44AC3583` FOREIGN KEY (`operation_id`) REFERENCES `manufacturing_operation` (`id`) ON DELETE CASCADE,
    CONSTRAINT `IDX_B8E90A2C8C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE CASCADE,
     PRIMARY KEY(`operation_id`, `employee_id`)
)
SQL);
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
            'date_fabrication'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `manufacturing_order` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `actual_quantity_code` VARCHAR(6) DEFAULT NULL,
    `actual_quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `actual_quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `company_id` INT UNSIGNED DEFAULT NULL,
    `delivery_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `emb_blocker_state` ENUM('blocked', 'closed', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:closer_state)',
    `emb_state_state` ENUM('agreed', 'asked', 'rejected') DEFAULT 'asked' NOT NULL COMMENT '(DC2Type:manufacturing_order_state)',
    `index` TINYINT UNSIGNED DEFAULT 1 NOT NULL COMMENT '(DC2Type:tinyint)',
    `manufacturing_company_id` INT UNSIGNED DEFAULT NULL,
    `manufacturing_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `notes` TEXT DEFAULT NULL,
    `order_id` INT UNSIGNED DEFAULT NULL,
    `product_id` INT UNSIGNED DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    `quantity_produced_code` VARCHAR(6) DEFAULT NULL,
    `quantity_produced_denominator` VARCHAR(6) DEFAULT NULL,
    `quantity_produced_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `quantity_requested_code` VARCHAR(6) DEFAULT NULL,
    `quantity_requested_denominator` VARCHAR(6) DEFAULT NULL,
    `quantity_requested_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CONSTRAINT `IDX_34010DB1979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_34010DB1E26A3063` FOREIGN KEY (`manufacturing_company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_34010DB18D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `selling_order` (`id`),
    CONSTRAINT `IDX_34010DB14584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `manufacturing_order` (
    `old_id`,
    `actual_quantity_code`,
    `actual_quantity_value`,
    `company_id`,
    `delivery_date`,
    `emb_blocker_state`,
    `emb_state_state`,
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
    `orderfabrication`.`id`,
    `unit`.`code`,
    `orderfabrication`.`quantity_real`,
    `company`.`id`,
    `orderfabrication`.`date_livraison`,
    CASE
        WHEN `orderfabrication`.`id_orderfabricationstatus` IN (5, 6) THEN 'closed'
        WHEN `orderfabrication`.`id_orderfabricationstatus` = 7 THEN 'blocked'
        ELSE 'enabled'
    END,
    CASE
        WHEN `orderfabrication`.`id_orderfabricationstatus` IN (3, 4, 5, 7, 8) THEN 'agreed'
        WHEN `orderfabrication`.`id_orderfabricationstatus` = 6 THEN 'rejected'
        ELSE 'asked'
    END,
    `orderfabrication`.`indice`,
    `supplier`.`id`,
    `orderfabrication`.`date_fabrication`,
    `orderfabrication`.`info_public`,
    `selling_order`.`id`,
    `product`.`id`,
    `orderfabrication`.`ofnumber`,
    `unit`.`code`,
    `orderfabrication`.`quantity_done`,
    `unit`.`code`,
    `orderfabrication`.`quantity`
FROM `orderfabrication`
INNER JOIN `product` ON `orderfabrication`.`id_product` = `product`.`old_id`
LEFT JOIN `unit` ON `product`.`unit_id` = `unit`.`id`
INNER JOIN `society` `society_company` ON `orderfabrication`.`id_society` = `society_company`.`old_id`
INNER JOIN `company` ON `society_company`.`id` = `company`.`society_id`
INNER JOIN `society` `society_supplier` ON `orderfabrication`.`id_supplier` = `society_supplier`.`old_id`
INNER JOIN `supplier` ON `society_supplier`.`id` = `supplier`.`society_id`
LEFT JOIN `selling_order` ON `orderfabrication`.`id_ordercustomer` = `selling_order`.`old_id`
WHERE `orderfabrication`.`statut` = 0
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
    `product`.`id`,
    `component`.`id`,
    `quantity`,
    `mandat`,
    `unit`.`code`
FROM `productcontent`
INNER JOIN `component` ON `productcontent`.`id_component` = `component`.`old_id`
LEFT JOIN `unit` ON `component`.`unit_id` = `unit`.`id`
INNER JOIN `product` ON `productcontent`.`id_product` = `product`.`old_id`
WHERE `statut` = 0
AND `quantity` IS NOT NULL AND TRIM(`quantity`) != '' AND `quantity` > 0
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

    private function upOperationTypes(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `operation_type` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `isAssemblage` BOOLEAN DEFAULT FALSE NOT NULL,
    `name` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('operation_type', ['id', 'isAssemblage', 'name']);
        $this->addQuery('ALTER TABLE `operation_type` CHANGE `isAssemblage` `assembly` BOOLEAN DEFAULT FALSE NOT NULL');
        $this->addQuery(<<<'SQL'
CREATE TABLE `operation_type_component_family` (
    `component_family_id` INT UNSIGNED NOT NULL,
    `operation_type_id` INT UNSIGNED NOT NULL,
    CONSTRAINT `IDX_58A4AEF9C35E566A` FOREIGN KEY (`component_family_id`) REFERENCES `component_family` (`id`) ON DELETE CASCADE,
    CONSTRAINT `IDX_86314A63C54C8C93` FOREIGN KEY (`operation_type_id`) REFERENCES `operation_type` (`id`) ON DELETE CASCADE,
    PRIMARY KEY(`operation_type_id`, `component_family_id`)
)
SQL);
        $this->insert('operation_type_component_family', ['component_family_id', 'operation_type_id']);
        $this->addQuery(<<<'SQL'
ALTER TABLE `operation_type_component_family`
    CHANGE `component_family_id` `family_id` INT UNSIGNED NOT NULL,
    CHANGE `operation_type_id` `type_id` INT UNSIGNED NOT NULL
SQL);
    }

    private function upOutTrainers(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `employee_extformateur` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `address` VARCHAR(80) NOT NULL,
    `address_address2` VARCHAR(110) DEFAULT NULL,
    `ville` VARCHAR(50) NOT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(80) DEFAULT NULL,
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
    CHANGE `address` `address_address` VARCHAR(160) DEFAULT NULL,
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
    `id_engine` INT UNSIGNED DEFAULT NULL,
    `designation` TEXT DEFAULT NULL,
    `type` VARCHAR(255) DEFAULT NULL,
    `quantity` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('engine_maintenance', ['id', 'id_engine', 'designation', 'type', 'quantity']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `planning` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `engine_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `quantity_code` VARCHAR(6) DEFAULT NULL,
    `quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    CONSTRAINT `IDX_D499BFF6E78C9C0A` FOREIGN KEY (`engine_id`) REFERENCES `engine` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `planning` (`old_id`, `engine_id`, `name`, `quantity_code`, `quantity_value`)
SELECT
    `engine_maintenance`.`id`,
    `engine`.`id`,
    `engine_maintenance`.`designation`,
    CASE
        WHEN `engine_maintenance`.`type` = 'd' THEN (SELECT `unit`.`code` FROM `unit` WHERE `unit`.`code` = 'j')
        WHEN `engine_maintenance`.`type` = 'u' THEN (SELECT `unit`.`code` FROM `unit` WHERE `unit`.`code` = 'U')
        WHEN `engine_maintenance`.`type` = 'b' THEN (SELECT `unit`.`code` FROM `unit` WHERE `unit`.`code` = 'fds')
    END,
    `engine_maintenance`.`quantity`
FROM `engine_maintenance`
INNER JOIN `engine` ON `engine_maintenance`.`id_engine` = `engine`.`old_id`
SQL);
        $this->addQuery('DROP TABLE `engine_maintenance`');
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
    CHANGE `statut` `deleted` BOOLEAN DEFAULT FALSE NOT NULL
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
    `temps_auto` DOUBLE PRECISION DEFAULT NULL,
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
    `id_incoterms` INT UNSIGNED DEFAULT NULL,
    `indice` VARCHAR(255) DEFAULT NULL,
    `indice_interne` TINYINT UNSIGNED DEFAULT 1 NOT NULL,
    `is_prototype` BOOLEAN DEFAULT NULL,
    `gestion_cu` BOOLEAN DEFAULT FALSE NOT NULL,
    `temps_manu` DOUBLE PRECISION DEFAULT NULL,
    `max_proto_quantity` DOUBLE PRECISION DEFAULT NULL,
    `livraison_minimum` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `min_prod_quantity` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `stock_minimum` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `designation` VARCHAR(255) DEFAULT NULL,
    `info_public` TEXT DEFAULT NULL,
    `typeconditionnement` VARCHAR(255) DEFAULT NULL,
    `conditionnement` VARCHAR(255) DEFAULT NULL,
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
    `code` VARCHAR(50) NOT NULL,
    `costing_auto_duration_code` VARCHAR(6) DEFAULT NULL,
    `costing_auto_duration_denominator` VARCHAR(6) DEFAULT NULL,
    `costing_auto_duration_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `costing_manual_duration_code` VARCHAR(6) DEFAULT NULL,
    `costing_manual_duration_denominator` VARCHAR(6) DEFAULT NULL,
    `costing_manual_duration_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `customs_code` VARCHAR(10) DEFAULT NULL,
    `emb_blocker_state` ENUM('blocked', 'disabled', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:blocker_state)',
    `emb_state_state` ENUM('agreed', 'draft', 'to_validate', 'warning') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:product_state)',
    `end_of_life` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `family_id` INT UNSIGNED NOT NULL,
    `forecast_volume_code` VARCHAR(6) DEFAULT NULL,
    `forecast_volume_denominator` VARCHAR(6) DEFAULT NULL,
    `forecast_volume_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `id_product_child` INT UNSIGNED NOT NULL,
    `incoterms_id` INT UNSIGNED DEFAULT NULL,
    `index` VARCHAR(10) DEFAULT NULL,
    `internal_index` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '(DC2Type:tinyint)',
    `kind` enum('EI','Prototype','Série','Pièce de rechange') NOT NULL DEFAULT 'Prototype' COMMENT '(DC2Type:product_kind)',
    `managed_copper` BOOLEAN DEFAULT FALSE NOT NULL,
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
    `name` VARCHAR(160) DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `packaging_code` VARCHAR(6) DEFAULT NULL,
    `packaging_denominator` VARCHAR(6) DEFAULT NULL,
    `packaging_incorrect` VARCHAR(255) DEFAULT NULL,
    `packaging_kind` VARCHAR(60) DEFAULT NULL,
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
    `customs_code`,
    `emb_blocker_state`,
    `emb_state_state`,
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
    `packaging_incorrect`,
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
    `product_old`.`id`,
    `product_old`.`id_society`,
    'h',
    IFNULL(`product_old`.`temps_auto`, 0),
    `product_old`.`ref`,
    'h',
    IFNULL(`product_old`.`tps_chiff_auto`, 0),
    'h',
    IFNULL(`product_old`.`tps_chiff_manu`, 0),
    `customcode`.`code`,
    CASE
        WHEN `product_old`.`id_productstatus` = 5 THEN 'blocked'
        WHEN `product_old`.`id_productstatus` = 6 THEN 'disabled'
        ELSE 'enabled'
    END,
    CASE
        WHEN `product_old`.`id_productstatus` = 2 THEN 'to_validate'
        WHEN `product_old`.`id_productstatus` IN (3, 6) THEN 'agreed'
        WHEN `product_old`.`id_productstatus` IN (4, 5) THEN 'warning'
        ELSE 'draft'
    END,
    `product_old`.`date_expiration`,
    `product_family`.`id`,
    'U',
    `product_old`.`volume_previsionnel`,
    `product_old`.`id_product_child`,
    `incoterms`.`id`,
    `product_old`.`indice`,
    `product_old`.`indice_interne`,
    CASE
        WHEN `product_old`.`is_prototype` = 0 THEN 'Série'
        WHEN `product_old`.`is_prototype` = 1 THEN 'Prototype'
        WHEN `product_old`.`is_prototype` = 2 THEN 'EI'
        ELSE 'Prototype'
    END,
    `product_old`.`gestion_cu`,
    'h',
    IFNULL(`product_old`.`temps_manu`, 0),
    'U',
    IFNULL(`product_old`.`max_proto_quantity`, 0),
    'U',
    `product_old`.`livraison_minimum`,
    'U',
    `product_old`.`min_prod_quantity`,
    'U',
    `product_old`.`stock_minimum`,
    `product_old`.`designation`,
    `product_old`.`info_public`,
    'U',
    IF(`product_old`.`conditionnement` IS NULL OR IS_NUMBER(`product_old`.`conditionnement`), NULL, `product_old`.`conditionnement`),
    `product_old`.`typeconditionnement`,
    IF(`product_old`.`conditionnement` IS NOT NULL AND IS_NUMBER(`product_old`.`conditionnement`), `product_old`.`conditionnement`, 1),
    (SELECT `product`.`id` FROM `product` WHERE `product`.`id_product_child` = `product_old`.`id`),
    'EUR',
    `product_old`.`price`,
    'EUR',
    `product_old`.`price_without_cu`,
    'j',
    `product_old`.`production_delay`,
    'EUR',
    `product_old`.`transfert_price_supplies`,
    'EUR',
    `product_old`.`transfert_price_work`,
    (SELECT `unit`.`id` FROM `unit` WHERE `unit`.`code` = 'U'),
    'g',
    `product_old`.`weight`
FROM `product_old`
LEFT JOIN `customcode` ON `product_old`.`id_customcode` = `customcode`.`id`
LEFT JOIN `incoterms` ON `product_old`.`id_incoterms` = `incoterms`.`id`
LEFT JOIN `product_family` ON `product_old`.`id_product_subfamily` = `product_family`.`old_subfamily_id`
WHERE `product_old`.`statut` = 0
SQL);
        $this->addQuery('DROP TABLE `product_old`');
        $this->addQuery('ALTER TABLE `product` DROP `id_product_child`');
    }

    private function upProjectOperations(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `operation` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `code` VARCHAR(255) DEFAULT NULL,
    `designation` TEXT DEFAULT NULL,
    `limite` VARCHAR(255) DEFAULT NULL,
    `cadence` INT UNSIGNED DEFAULT NULL,
    `id_factory` INT UNSIGNED DEFAULT NULL,
    `price` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `tms` DATETIME DEFAULT NULL,
    `id_user` INT UNSIGNED DEFAULT NULL,
    `lastmod` DATETIME DEFAULT NULL,
    `type` TINYINT DEFAULT NULL COMMENT '0 = MANU ; 1 = AUTO',
    `id_operation_type` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->insert('operation', [
            'id',
            'statut',
            'code',
            'designation',
            'limite',
            'cadence',
            'id_factory',
            'price',
            'tms',
            'id_user',
            'lastmod',
            'type',
            'id_operation_type'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `project_operation` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `auto` BOOLEAN DEFAULT FALSE NOT NULL,
    `boundary` VARCHAR(255) DEFAULT NULL,
    `cadence_code` VARCHAR(6) DEFAULT NULL,
    `cadence_denominator` VARCHAR(6) DEFAULT NULL,
    `cadence_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `code` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `price_code` VARCHAR(6) DEFAULT NULL,
    `price_denominator` VARCHAR(6) DEFAULT NULL,
    `price_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `time_code` VARCHAR(6) DEFAULT NULL,
    `time_denominator` VARCHAR(6) DEFAULT NULL,
    `time_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `type_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_8BDE3BAC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `operation_type` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `project_operation` (
    `auto`,
    `boundary`,
    `cadence_code`,
    `cadence_value`,
    `code`,
    `name`,
    `price_code`,
    `price_value`,
    `type_id`
) SELECT
    `operation`.`type`,
    UCFIRST(`operation`.`limite`),
    'U',
    `operation`.`cadence`,
    `operation`.`code`,
    UCFIRST(`operation`.`designation`),
    'EUR',
    `operation`.`price`,
    `operation_type`.`id`
FROM `operation`
LEFT JOIN `operation_type` ON `operation`.`id_operation_type` = `operation_type`.`id`
SQL);
        $this->addQuery('DROP TABLE `operation`');
    }

    private function upPurchaseOrderItems(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `ordersupplier_component` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_ordersupplier` INT UNSIGNED DEFAULT NULL,
    `open_order` VARCHAR(255) DEFAULT NULL,
    `id_component` INT UNSIGNED DEFAULT NULL,
    `id_product` INT UNSIGNED DEFAULT NULL,
    `id_society_destination` INT UNSIGNED DEFAULT NULL,
    `quantity` INT UNSIGNED DEFAULT NULL,
    `quantity_souhaitee` INT UNSIGNED DEFAULT NULL,
    `quantity_received` INT UNSIGNED DEFAULT NULL,
    `price` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `price_surcharge_cu` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `texte` TEXT DEFAULT NULL,
    `refsupplier` VARCHAR(255) DEFAULT NULL DEFAULT '',
    `invoice` TINYINT DEFAULT NULL DEFAULT 0,
    `date_souhaitee` DATE DEFAULT NULL,
    `date_livraison` DATE DEFAULT NULL,
    `date` DATE DEFAULT NULL
)
SQL);
        $this->insert('ordersupplier_component', [
            'id',
            'statut',
            'id_ordersupplier',
            'open_order',
            'id_component',
            'id_product',
            'id_society_destination',
            'quantity',
            'quantity_souhaitee',
            'quantity_received',
            'price',
            'price_surcharge_cu',
            'texte',
            'refsupplier',
            'invoice',
            'date_souhaitee',
            'date_livraison',
            'date'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `purchase_order_item` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `component_id` INT UNSIGNED DEFAULT NULL,
    `confirmed_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `confirmed_quantity_code` VARCHAR(6) DEFAULT NULL,
    `confirmed_quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `confirmed_quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `copper_price_code` VARCHAR(6) DEFAULT NULL,
    `copper_price_denominator` VARCHAR(6) DEFAULT NULL,
    `copper_price_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `emb_blocker_state` ENUM('blocked', 'closed', 'delayed', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:purchase_order_item_closer_state)',
    `emb_state_state` ENUM('agreed', 'delivered', 'draft', 'forecast', 'initial', 'monthly', 'partially_delivered') DEFAULT 'initial' NOT NULL COMMENT '(DC2Type:purchase_order_item_state)',
    `notes` VARCHAR(255) DEFAULT NULL,
    `order_id` INT UNSIGNED DEFAULT NULL,
    `price_code` VARCHAR(6) DEFAULT NULL,
    `price_denominator` VARCHAR(6) DEFAULT NULL,
    `price_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `product_id` INT UNSIGNED DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    `requested_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `requested_quantity_code` VARCHAR(6) DEFAULT NULL,
    `requested_quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `requested_quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `target_company_id` INT UNSIGNED DEFAULT NULL,
    `type` ENUM('component', 'product') NOT NULL COMMENT '(DC2Type:item)',
    CONSTRAINT `IDX_5ED948C3E2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`),
    CONSTRAINT `IDX_5ED948C38D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `purchase_order` (`id`),
    CONSTRAINT `IDX_5ED948C34584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
    CONSTRAINT `IDX_5ED948C3A577C247` FOREIGN KEY (`target_company_id`) REFERENCES `company` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `purchase_order_item` (
    `component_id`,
    `confirmed_date`,
    `confirmed_quantity_code`,
    `confirmed_quantity_value`,
    `copper_price_code`,
    `copper_price_value`,
    `emb_blocker_state`,
    `emb_state_state`,
    `notes`,
    `order_id`,
    `price_code`,
    `price_value`,
    `ref`,
    `requested_date`,
    `requested_quantity_code`,
    `requested_quantity_value`,
    `target_company_id`,
    `type`
) SELECT
    `component`.`id`,
    `ordersupplier_component`.`date_livraison`,
    `unit`.`code`,
    `ordersupplier_component`.`quantity`,
    'EUR',
    `ordersupplier_component`.`price_surcharge_cu`,
    IF(
        `purchase_order`.`emb_state_state` IN ('agreed', 'partially_delivery')
            OR `purchase_order`.`emb_blocker_state` = 'closed',
        CASE
            WHEN IFNULL(`ordersupplier_component`.`quantity`, 0) = IFNULL(`ordersupplier_component`.`quantity_received`, 0) THEN 'closed'
            WHEN IFNULL(`ordersupplier_component`.`quantity_received`, 0) > 0 THEN 'enabled'
            ELSE 'enabled'
        END,
        'enabled'
    ),
    IF(
        `purchase_order`.`emb_state_state` IN ('agreed', 'partially_delivery')
            OR `purchase_order`.`emb_blocker_state` = 'closed',
        CASE
            WHEN IFNULL(`ordersupplier_component`.`quantity`, 0) = IFNULL(`ordersupplier_component`.`quantity_received`, 0) THEN 'delivered'
            WHEN IFNULL(`ordersupplier_component`.`quantity_received`, 0) > 0 THEN 'partially_delivered'
            ELSE 'agreed'
        END,
        'draft'
    ),
    `ordersupplier_component`.`texte`,
    `purchase_order`.`id`,
    'EUR',
    `ordersupplier_component`.`price`,
    `ordersupplier_component`.`refsupplier`,
    `ordersupplier_component`.`date_souhaitee`,
    `unit`.`code`,
    `ordersupplier_component`.`quantity_souhaitee`,
    `company`.`id`,
    'component'
FROM `ordersupplier_component`
INNER JOIN `component` ON `ordersupplier_component`.`id_component` = `component`.`old_id`
LEFT JOIN `unit` ON `component`.`unit_id` = `unit`.`id`
INNER JOIN `purchase_order` ON `ordersupplier_component`.`id_ordersupplier` = `purchase_order`.`old_id`
INNER JOIN `society` ON `ordersupplier_component`.`id_society_destination` = `society`.`old_id`
INNER JOIN `company` ON `society`.`id` = `company`.`society_id`
WHERE `ordersupplier_component`.`statut` = 0
SQL);
        $this->addQuery('DROP TABLE `ordersupplier_component`');
    }

    private function upPurchaseOrders(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `ordersupplier` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_supplier` INT UNSIGNED DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    `id_ordersupplierstatus` INT UNSIGNED DEFAULT NULL,
    `open_order` VARCHAR(255) DEFAULT NULL,
    `id_address` INT UNSIGNED DEFAULT NULL,
    `id_society` INT UNSIGNED DEFAULT NULL,
    `supplement_fret` BOOLEAN DEFAULT FALSE NOT NULL,
    `info_public` TEXT DEFAULT NULL COMMENT 'info interne',
    `commentaire` TEXT DEFAULT NULL COMMENT 'info affichee sur le document envoyé'
)
SQL);
        $this->insert('ordersupplier', [
            'id',
            'statut',
            'id_supplier',
            'ref',
            'id_ordersupplierstatus',
            'open_order',
            'id_address',
            'id_society',
            'supplement_fret',
            'info_public',
            'commentaire'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `purchase_order` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `company_id` INT UNSIGNED DEFAULT NULL,
    `contact_id` INT UNSIGNED DEFAULT NULL,
    `delivery_company_id` INT UNSIGNED DEFAULT NULL,
    `emb_blocker_state` ENUM('blocked', 'closed', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:closer_state)',
    `emb_state_state` ENUM('agreed', 'cart', 'delivered', 'draft', 'initial', 'partially_delivered') DEFAULT 'initial' NOT NULL COMMENT '(DC2Type:purchase_order_state)',
    `notes` TEXT DEFAULT NULL,
    `order_id` INT UNSIGNED DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    `supplement_fret` BOOLEAN DEFAULT FALSE NOT NULL,
    `supplier_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_21E210B2979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_21E210B2E7A1254A` FOREIGN KEY (`contact_id`) REFERENCES `supplier_contact` (`id`),
    CONSTRAINT `IDX_21E210B289DE8DF2` FOREIGN KEY (`delivery_company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_21E210B28D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `selling_order` (`id`),
    CONSTRAINT `IDX_21E210B22ADD6D8C` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `purchase_order` (
    `old_id`,
    `company_id`,
    `delivery_company_id`,
    `emb_blocker_state`,
    `emb_state_state`,
    `notes`,
    `ref`,
    `supplement_fret`,
    `supplier_id`
) SELECT
    `ordersupplier`.`id`,
    `company`.`id`,
    `company`.`id`,
    IF(`ordersupplier`.`id_ordersupplierstatus` IN (6, 7), 'closed', 'enabled'),
    CASE
        WHEN `ordersupplier`.`id_ordersupplierstatus` = 4 THEN 'agreed'
        WHEN `ordersupplier`.`id_ordersupplierstatus` = 5 THEN 'partially_delivered'
        WHEN `ordersupplier`.`id_ordersupplierstatus` = 6 THEN 'delivered'
        ELSE 'draft'
    END,
    IF(
        `ordersupplier`.`info_public` IS NULL,
        `ordersupplier`.`commentaire`,
        IF(`ordersupplier`.`commentaire` IS NULL, NULL, CONCAT(`ordersupplier`.`info_public`, `ordersupplier`.`commentaire`))
    ),
    `ordersupplier`.`ref`,
    `ordersupplier`.`supplement_fret`,
    `supplier`.`id`
FROM `ordersupplier`
INNER JOIN `society` `society_company` ON `ordersupplier`.`id_society` = `society_company`.`old_id`
INNER JOIN `company` ON `society_company`.`id` = `company`.`society_id`
INNER JOIN `society` `society_supplier` ON `ordersupplier`.`id_supplier` = `society_supplier`.`old_id`
INNER JOIN `supplier` ON `society_supplier`.`id` = `supplier`.`society_id`
WHERE `ordersupplier`.`statut` = 0
SQL);
        $this->addQuery('DROP TABLE `ordersupplier`');
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
    CHANGE `statut` `deleted` BOOLEAN DEFAULT FALSE NOT NULL
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

    private function upSellingOrderItems(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `ordercustomer_product` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `id_ordercustomer` INT UNSIGNED DEFAULT NULL,
    `id_product` INT UNSIGNED DEFAULT NULL,
    `quantity` INT UNSIGNED DEFAULT NULL,
    `quantity_souhaitee` INT UNSIGNED DEFAULT NULL,
    `quantity_sent` INT UNSIGNED DEFAULT NULL,
    `price` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `texte` TEXT DEFAULT NULL,
    `ref_ordercustomer_product` VARCHAR(255) DEFAULT NULL,
    `date_souhaitee` DATE DEFAULT NULL,
    `date_livraison` DATE DEFAULT NULL
)
SQL);
        $this->insert('ordercustomer_product', [
            'id',
            'statut',
            'id_ordercustomer',
            'id_product',
            'quantity',
            'quantity_souhaitee',
            'quantity_sent',
            'price',
            'texte',
            'ref_ordercustomer_product',
            'date_souhaitee',
            'date_livraison'
        ]);
        $this->addQuery(<<<'SQL'
CREATE TABLE `selling_order_item` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `ar_sent` BOOLEAN DEFAULT FALSE NOT NULL,
    `component_id` INT UNSIGNED DEFAULT NULL,
    `confirmed_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `confirmed_quantity_code` VARCHAR(6) DEFAULT NULL,
    `confirmed_quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `confirmed_quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `emb_blocker_state` ENUM('blocked', 'closed', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:closer_state)',
    `emb_state_state` ENUM('agreed', 'delivered', 'draft', 'partially_delivered') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:selling_order_item_state)',
    `notes` VARCHAR(255) DEFAULT NULL,
    `order_id` INT UNSIGNED DEFAULT NULL,
    `price_code` VARCHAR(6) DEFAULT NULL,
    `price_denominator` VARCHAR(6) DEFAULT NULL,
    `price_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `product_id` INT UNSIGNED DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    `requested_date` DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
    `requested_quantity_code` VARCHAR(6) DEFAULT NULL,
    `requested_quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `requested_quantity_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `type` ENUM('component', 'product') NOT NULL COMMENT '(DC2Type:item)',
    CONSTRAINT `IDX_8A64F230E2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`),
    CONSTRAINT `IDX_8A64F2308D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `selling_order` (`id`),
    CONSTRAINT `IDX_8A64F2304584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `selling_order_item` (
    `old_id`,
    `confirmed_date`,
    `confirmed_quantity_code`,
    `confirmed_quantity_value`,
    `emb_blocker_state`,
    `emb_state_state`,
    `product_id`,
    `notes`,
    `order_id`,
    `price_code`,
    `price_value`,
    `ref`,
    `requested_date`,
    `requested_quantity_code`,
    `requested_quantity_value`,
    `type`
) SELECT
    `ordercustomer_product`.`id`,
    `ordercustomer_product`.`date_livraison`,
    `unit`.`code`,
    IFNULL(`ordercustomer_product`.`quantity`, 0),
    CASE
        WHEN `selling_order`.`emb_blocker_state` IN ('closed', 'disabled')
            OR (`selling_order`.`emb_state_state` IN ('agreed', 'partially_delivered') AND `ordercustomer_product`.`quantity_sent` >= `ordercustomer_product`.`quantity`)
            THEN 'closed'
        WHEN `selling_order`.`emb_blocker_state` = 'blocked' THEN 'blocked'
        WHEN `selling_order`.`emb_state_state` IN ('agreed', 'partially_delivered') AND `ordercustomer_product`.`quantity_sent` >= `ordercustomer_product`.`quantity` THEN 'closed'
        ELSE 'enabled'
    END,
    CASE
        WHEN `selling_order`.`emb_blocker_state` = 'closed' THEN 'delivered'
        WHEN `selling_order`.`emb_blocker_state` LIKE 'blocked' THEN 'agreed'
        WHEN `selling_order`.`emb_state_state` IN ('agreed', 'partially_delivered')
            THEN IF(`ordercustomer_product`.`quantity_sent` >= `ordercustomer_product`.`quantity`, 'delivered', IF(`ordercustomer_product`.`quantity_sent` > 0, 'partially_delivered', 'agreed'))
        ELSE 'draft'
    END,
    `product`.`id`,
    `ordercustomer_product`.`texte`,
    `selling_order`.`id`,
    'EUR',
    IFNULL(`ordercustomer_product`.`price`, 0),
    `ordercustomer_product`.`ref_ordercustomer_product`,
    `ordercustomer_product`.`date_souhaitee`,
    `unit`.`code`,
    IFNULL(`ordercustomer_product`.`quantity_souhaitee`, 0),
    'product'
FROM `ordercustomer_product`
INNER JOIN `product` ON `ordercustomer_product`.`id_product` = `product`.`old_id`
LEFT JOIN `unit` ON `product`.`unit_id` = `unit`.`id`
INNER JOIN `selling_order` ON `ordercustomer_product`.`id_ordercustomer` = `selling_order`.`old_id`
WHERE `ordercustomer_product`.`statut` = 0
SQL);
        $this->addQuery('DROP TABLE `ordercustomer_product`');
    }

    private function upSellingOrders(): void {
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
CREATE TABLE `selling_order` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `billed_to_id` INT UNSIGNED DEFAULT NULL,
    `company_id` INT UNSIGNED DEFAULT NULL,
    `customer_id` INT UNSIGNED DEFAULT NULL,
    `destination_id` INT UNSIGNED DEFAULT NULL,
    `emb_blocker_state` ENUM('blocked', 'closed', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:closer_state)',
    `emb_state_state` ENUM('agreed', 'delivered', 'draft', 'partially_delivered', 'to_validate') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:selling_order_state)',
    `kind` ENUM('EI', 'Prototype', 'Série', 'Pièce de rechange') DEFAULT 'Prototype' NOT NULL COMMENT '(DC2Type:product_kind)',
    `notes` VARCHAR(255) DEFAULT NULL,
    `ref` VARCHAR(255) DEFAULT NULL,
    CONSTRAINT `IDX_9CCD846B994CB78` FOREIGN KEY (`billed_to_id`) REFERENCES `customer_address` (`id`),
    CONSTRAINT `IDX_9CCD846B979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
    CONSTRAINT `IDX_9CCD846B9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
    CONSTRAINT `IDX_9CCD846B816C6140` FOREIGN KEY (`destination_id`) REFERENCES `customer_address` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `selling_order` (`old_id`, `billed_to_id`, `company_id`, `customer_id`, `emb_blocker_state`, `emb_state_state`, `kind`, `notes`, `ref`)
SELECT
    `ordercustomer`.`id`,
    `customer_address`.`id`,
    `company`.`id`,
    `customer`.`id`,
    CASE
        WHEN `ordercustomer`.`id_ordercustomerstatus` IN (8, 10, 11) THEN 'closed'
        WHEN `ordercustomer`.`id_ordercustomerstatus` = 12 THEN 'blocked'
        ELSE 'enabled'
    END,
    CASE
        WHEN `ordercustomer`.`id_ordercustomerstatus` IN (2, 13) THEN 'to_validate'
        WHEN `ordercustomer`.`id_ordercustomerstatus` IN (3, 4, 5, 6, 11, 12) THEN 'agreed'
        WHEN `ordercustomer`.`id_ordercustomerstatus` IN (7, 9) THEN 'partially_delivered'
        WHEN `ordercustomer`.`id_ordercustomerstatus` IN (8, 10) THEN 'delivered'
        ELSE 'draft'
    END,
    'Série',
    `ordercustomer`.`info_public`,
    `ordercustomer`.`ref`
FROM `ordercustomer`
INNER JOIN `society` `society_company` ON `ordercustomer`.`id_society` = `society_company`.`old_id`
INNER JOIN `company` ON `society_company`.`id` = `company`.`society_id`
INNER JOIN `society` `society_customer` ON `ordercustomer`.`id_customer` = `society_customer`.`old_id`
INNER JOIN `customer` ON `society_customer`.`id` = `customer`.`society_id`
LEFT JOIN `customer_address` ON `ordercustomer`.`id_address` = `customer_address`.`old_id` AND `customer_address`.`type` = 'billing'
WHERE `ordercustomer`.`statut` = 0
SQL);
        $this->addQuery('DROP TABLE `ordercustomer`');
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
    `employee`.`id`,
    `employee_histcompetence`.`date_cloture_formation`,
    `engine`.`id`,
    `formateur`.`id`,
    `employee_histcompetence`.`niveau`,
    `out_trainer`.`id`,
    `employee_histcompetence`.`date_form`,
    `skill_type`.`id`
FROM `employee_histcompetence`
INNER JOIN `employee` ON `employee_histcompetence`.`id_employee` = `employee`.`old_id`
LEFT JOIN `engine` ON `employee_histcompetence`.`id_engine` = `engine`.`old_id`
LEFT JOIN `employee` `formateur` ON `employee_histcompetence`.`id_formateur` = `formateur`.`old_id`
LEFT JOIN `out_trainer` ON `employee_histcompetence`.`id_extformateur` = `out_trainer`.`id`
INNER JOIN `skill_type` ON `employee_histcompetence`.`id_competence` = `skill_type`.`old_id`
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
        $this->addQuery('INSERT INTO `skill_type` (`old_id`, `name`) SELECT `id`, UCFIRST(`designation`) FROM `employee_competencelist` WHERE `statut` = 0');
        $this->addQuery('DROP TABLE `employee_competencelist`');
    }

    private function upSocieties(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `society` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `nom` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) DEFAULT NULL,
    `address1` VARCHAR(255) DEFAULT NULL,
    `address2` VARCHAR(255) DEFAULT NULL,
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
    `email` VARCHAR(255) DEFAULT NULL,
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
    `address_address` VARCHAR(160) DEFAULT NULL,
    `address_address2` VARCHAR(110) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(80) DEFAULT NULL,
    `phone` VARCHAR(255) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `ar` BOOLEAN DEFAULT FALSE NOT NULL,
    `bank_details` VARCHAR(255) DEFAULT NULL,
    `copper_index_code` VARCHAR(6) DEFAULT NULL,
    `copper_index_denominator` VARCHAR(6) DEFAULT NULL,
    `copper_index_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `copper_last` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_managed` BOOLEAN DEFAULT FALSE NOT NULL,
    `copper_next` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_type` ENUM('à la livraison','mensuel','semestriel') NOT NULL DEFAULT 'mensuel' COMMENT '(DC2Type:copper)',
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
    `society_old`.`id`,
    `society_old`.`compte_compta`,
    `society_old`.`address1`,
    `society_old`.`address2`,
    `society_old`.`city`,
    UCASE(`country`.`code`),
    `society_old`.`email`,
    `society_old`.`phone`,
    `society_old`.`zip`,
    `society_old`.`ar_enabled` = 1 OR `society_old`.`ar_customer_enabled` = 1,
    IFNULL(`society_old`.`indice_cu`, 0),
    `society_old`.`indice_cu_date`,
    `society_old`.`indice_cu_enabled`,
    `society_old`.`indice_cu_date_fin`,
    CASE
        WHEN `society_old`.`force_tva` = 1 THEN 'Force AVEC TVA'
        WHEN `society_old`.`force_tva` = 2 THEN 'Force SANS TVA'
        ELSE 'TVA par défaut selon le pays du client'
    END,
    `incoterms`.`id`,
    IFNULL(`society_old`.`invoice_minimum`, 0),
    (
        SELECT `invoice_time_due`.`id`
        FROM `invoice_time_due`
        WHERE `invoice_time_due`.`id_old_invoicetimedue` = `society_old`.`id_invoicetimedue`
        OR `invoice_time_due`.`id_old_invoicetimeduesupplier` = `society_old`.`id_invoicetimeduesupplier`
        LIMIT 1
    ),
    `society_old`.`formejuridique`,
    `society_old`.`nom`,
    IF(
        `society_old`.`info_public` IS NULL,
        `society_old`.`info_private`,
        IF(`society_old`.`info_private` IS NULL, NULL, CONCAT(`society_old`.`info_public`, `society_old`.`info_private`))
    ),
    `society_old`.`order_minimum`,
    `society_old`.`siren`,
    `society_old`.`tva`,
    `vat_message`.`id`,
    `society_old`.`web`
FROM `society_old`
LEFT JOIN `country` ON `society_old`.`id_country` = `country`.`id`
LEFT JOIN `incoterms` ON `society_old`.`id_incoterms` = `incoterms`.`id`
LEFT JOIN `vat_message` ON `society_old`.`id_messagetva` = `vat_message`.`id`
WHERE `society_old`.`statut` = 0
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
SELECT `society_old`.`nom`, `society`.`id`
FROM `society_old`
INNER JOIN `society` ON `society_old`.`id` = `society`.`old_id`
WHERE `society_old`.`is_company` = 1 AND `society_old`.`statut` = 0
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `customer` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `accounting_portal_password` VARCHAR(255) DEFAULT NULL,
    `accounting_portal_url` VARCHAR(255) DEFAULT NULL,
    `accounting_portal_username` VARCHAR(255) DEFAULT NULL,
    `address_address` VARCHAR(160) DEFAULT NULL,
    `address_address2` VARCHAR(110) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(80) DEFAULT NULL,
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
    `copper_type` ENUM('à la livraison', 'mensuel', 'semestriel') DEFAULT 'mensuel' NOT NULL COMMENT '(DC2Type:copper)',
    `currency_id` INT UNSIGNED NOT NULL,
    `emb_blocker_state` ENUM('blocked', 'disabled', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:blocker_state)',
    `emb_state_state` ENUM('agreed', 'draft') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:customer_state)',
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
    `emb_blocker_state`,
    `emb_state_state`,
    `invoice_by_email`,
    `language`,
    `name`,
    `nb_deliveries`,
    `nb_invoices`,
    `notes`,
    `payment_terms_id`,
    `society_id`
) SELECT
    `society_old`.`address1`,
    `society_old`.`address2`,
    `society_old`.`city`,
    UCASE(`country`.`code`),
    `society_old`.`email`,
    `society_old`.`phone`,
    `society_old`.`zip`,
    'j',
    7,
    IFNULL(`society_old`.`indice_cu`, 0),
    `society_old`.`indice_cu_date`,
    `society_old`.`indice_cu_enabled`,
    `society_old`.`indice_cu_date_fin`,
    `currency`.`id`,
    IF( `society_old`.`id_societystatus` = 3, 'blocked', 'enabled'),
    IF( `society_old`.`id_societystatus` IN (2, 3), 'agreed', 'draft'),
    `society_old`.`invoicecustomer_by_email`,
    UCASE(`locale`.`code`),
    `society_old`.`nom`,
    10,
    10,
    IF(
        `society_old`.`info_public` IS NULL,
        `society_old`.`info_private`,
        IF(`society_old`.`info_private` IS NULL, NULL, CONCAT(`society_old`.`info_public`, `society_old`.`info_private`))
    ),
    (
        SELECT `invoice_time_due`.`id`
        FROM `invoice_time_due`
        WHERE `invoice_time_due`.`id_old_invoicetimedue` = `society_old`.`id_invoicetimedue`
        OR `invoice_time_due`.`id_old_invoicetimeduesupplier` = `society_old`.`id_invoicetimeduesupplier`
        LIMIT 1
    ),
    `society`.`id`
FROM `society_old`
LEFT JOIN `country` ON `society_old`.`id_country` = `country`.`id`
LEFT JOIN `currency` ON `currency`.`code` = 'EUR'
LEFT JOIN `locale` ON `society_old`.`id_locale` = `locale`.`id`
INNER JOIN `society` ON `society_old`.`id` = `society`.`old_id`
WHERE `society_old`.`is_customer` = 1 AND `society_old`.`statut` = 0
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
SELECT `customer`.`id`, `company`.`id`
FROM `society_old`
INNER JOIN `society` `society_customer` ON `society_old`.`id` = `society_customer`.`old_id`
INNER JOIN `customer` ON `society_customer`.`id` = `customer`.`society_id`
INNER JOIN `society` `society_company` ON `society_old`.`id_soc_gest_customer` = `society_company`.`old_id`
INNER JOIN `company` ON `society_company`.`id` = `company`.`society_id`
WHERE `society_old`.`is_customer` = 1
AND `society_old`.`statut` = 0
SQL);
        $this->addQuery(<<<'SQL'
CREATE TABLE `supplier` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `address_address` VARCHAR(160) DEFAULT NULL,
    `address_address2` VARCHAR(110) DEFAULT NULL,
    `address_city` VARCHAR(50) DEFAULT NULL,
    `address_country` CHAR(2) DEFAULT NULL COMMENT '(DC2Type:char)',
    `address_email` VARCHAR(80) DEFAULT NULL,
    `address_phone_number` VARCHAR(255) DEFAULT NULL,
    `address_zip_code` VARCHAR(10) DEFAULT NULL,
    `confidence_criteria` TINYINT UNSIGNED DEFAULT '0' NOT NULL COMMENT '(DC2Type:tinyint)',
    `copper_index_code` VARCHAR(6) DEFAULT NULL,
    `copper_index_denominator` VARCHAR(6) DEFAULT NULL,
    `copper_index_value` DOUBLE PRECISION DEFAULT 0 NOT NULL,
    `copper_last` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_managed` BOOLEAN DEFAULT FALSE NOT NULL,
    `copper_next` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    `copper_type` ENUM('à la livraison', 'mensuel', 'semestriel') DEFAULT 'mensuel' NOT NULL COMMENT '(DC2Type:copper)',
    `currency_id` INT UNSIGNED NOT NULL,
    `emb_blocker_state` ENUM('blocked', 'disabled', 'enabled') DEFAULT 'enabled' NOT NULL COMMENT '(DC2Type:blocker_state)',
    `emb_state_state` ENUM('agreed', 'draft', 'to_validate', 'warning') DEFAULT 'draft' NOT NULL COMMENT '(DC2Type:supplier_state)',
    `language` VARCHAR(255) DEFAULT NULL,
    `managed_production` BOOLEAN DEFAULT FALSE NOT NULL,
    `managed_quality` BOOLEAN DEFAULT FALSE NOT NULL,
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
    `emb_blocker_state`,
    `emb_state_state`,
    `language`,
    `name`,
    `notes`,
    `society_id`
) SELECT
    `society_old`.`address1`,
    `society_old`.`address2`,
    `society_old`.`city`,
    UCASE(`country`.`code`),
    `society_old`.`email`,
    `society_old`.`phone`,
    `society_old`.`zip`,
    IFNULL(`society_old`.`indice_cu`, 0),
    `society_old`.`indice_cu_date`,
    `society_old`.`indice_cu_enabled`,
    `society_old`.`indice_cu_date_fin`,
    `currency`.`id`,
    IF(`society_old`.`id_societystatus` = 3, 'blocked', 'enabled'),
    CASE
        WHEN `society_old`.`id_societystatus` = 2 THEN 'agreed'
        WHEN `society_old`.`id_societystatus` = 3 THEN 'warning'
        ELSE 'to_validate'
    END,
    UCASE(`locale`.`code`),
    `society_old`.`nom`,
    IF(
        `society_old`.`info_public` IS NULL,
        `society_old`.`info_private`,
        IF(`society_old`.`info_private` IS NULL, NULL, CONCAT(`society_old`.`info_public`, `society_old`.`info_private`))
    ),
    `society`.`id`
FROM `society_old`
LEFT JOIN `country` ON `society_old`.`id_country` = `country`.`id`
LEFT JOIN `currency` ON `currency`.`code` = 'EUR'
LEFT JOIN `locale` ON `society_old`.`id_locale` = `locale`.`id`
INNER JOIN `society`ON `society_old`.`id` = `society`.`old_id`
WHERE `society_old`.`is_supplier` = 1 AND `society_old`.`statut` = 0
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
SELECT `supplier`.`id`, `company`.`id`
FROM `society_old`
INNER JOIN `society` `society_supplier` ON `society_old`.`id` = `society_supplier`.`old_id`
INNER JOIN `supplier` ON `society_supplier`.`id` = `supplier`.`society_id`
INNER JOIN `society` `society_company` ON `society_old`.`id_soc_gest` = `society_company`.`old_id`
INNER JOIN `company` ON `society_company`.`id` = `company`.`society_id`
WHERE `society_old`.`is_supplier` = 1
AND `society_old`.`statut` = 0
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
    `old_id` INT UNSIGNED NOT NULL,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `batch_number` VARCHAR(255) DEFAULT NULL,
    `component_id` INT UNSIGNED DEFAULT NULL,
    `jail` BOOLEAN DEFAULT FALSE NOT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `product_id` INT UNSIGNED DEFAULT NULL,
    `quantity_code` VARCHAR(6) DEFAULT NULL,
    `quantity_denominator` VARCHAR(6) DEFAULT NULL,
    `quantity_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    `type` ENUM('component', 'product') NOT NULL COMMENT '(DC2Type:item)',
    `warehouse_id` INT UNSIGNED DEFAULT NULL,
    CONSTRAINT `IDX_4B365660E2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`),
    CONSTRAINT `IDX_4B3656604584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
    CONSTRAINT `IDX_4B3656605080ECDE` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `stock` (
    `old_id`,
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
    `old_stock`.`id`,
    `old_stock`.`batchnumber`,
    `component`.`id`,
    `old_stock`.`jail`,
    `old_stock`.`location`,
    `product`.`id`,
    IF(`component`.`id` IS NOT NULL, `unit_component`.`code`, `unit_product`.`code`),
    `old_stock`.`quantity`,
    IF(`component`.`id` IS NOT NULL, 'component', 'product'),
    `warehouse`.`id`
FROM `old_stock`
LEFT JOIN `component` ON `old_stock`.`id_component` = `component`.`old_id`
LEFT JOIN `unit` `unit_component` ON `component`.`unit_id` = `unit_component`.`id`
LEFT JOIN `product` ON `old_stock`.`id_product` = `product`.`old_id`
LEFT JOIN `unit` `unit_product` ON `product`.`unit_id` = `unit_product`.`id`
LEFT JOIN `warehouse` ON `old_stock`.`id_warehouse` = `warehouse`.`old_id`
WHERE `old_stock`.`statut` = 0
AND `old_stock`.`quantity` IS NOT NULL AND TRIM(`old_stock`.`quantity`) != '' AND `old_stock`.`quantity` > 0
AND (`component`.`id` IS NOT NULL OR `product`.`id` IS NOT NULL)
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
    `incoterms`.`id`,
    `unit`.`code`,
    `component_supplier`.`moq`,
    `unit`.`code`,
    `component_supplier`.`typeconditionnement`,
    `component_supplier`.`conditionnement`,
    100,
    `supplier`.`id`
FROM `component_supplier`
INNER JOIN `component` ON `component_supplier`.`id_component` = `component`.`old_id`
LEFT JOIN `unit` ON `component`.`unit_id` = `unit`.`id`
LEFT JOIN `incoterms` ON `component_supplier`.`id_incoterms` = `incoterms`.`id`
INNER JOIN `society` ON `component_supplier`.`id_supplier` = `society`.`old_id`
INNER JOIN `supplier` ON `society`.`id` = `supplier`.`society_id`
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
    `product`.`id`,
    `company`.`id`,
    `product_supplier`.`percentage`,
    `incoterms`.`id`,
    `product_supplier`.`refsupplier`
FROM `product_supplier`
INNER JOIN `product` ON `product_supplier`.`id_product` = `product`.`old_id`
INNER JOIN `society` ON `product_supplier`.`id_supplier` = `society`.`old_id`
INNER JOIN `company` ON `society`.`id` = `company`.`society_id`
LEFT JOIN `incoterms` ON `product_supplier`.`id_incoterms` = `incoterms`.`id`
WHERE `product_supplier`.`statut` = 0
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
    `warehouse_old`.`id`,
    `company`.`id`,
    CASE
        WHEN `warehouse_old`.`warehouse_name` LIKE '%prison%' THEN 'prison'
        WHEN `warehouse_old`.`warehouse_name` LIKE '%fabrication%' OR `warehouse_old`.`warehouse_name` LIKE '%production%' THEN 'production'
        WHEN `warehouse_old`.`warehouse_name` LIKE '%réception%' OR `warehouse_old`.`warehouse_name` LIKE '%import%' THEN 'réception'
        WHEN `warehouse_old`.`warehouse_name` LIKE '%vente%' THEN 'magasin pièces finies'
        WHEN `warehouse_old`.`warehouse_name` LIKE '%expédition%' OR `warehouse_old`.`warehouse_name` LIKE '%export%' THEN 'expédition'
        WHEN `warehouse_old`.`warehouse_name` LIKE '%besoin%' OR `warehouse_old`.`warehouse_name` LIKE '%magasin%' THEN 'magasin matières premières'
        WHEN `warehouse_old`.`warehouse_name` LIKE '%camion%' THEN 'camion'
        ELSE NULL
    END,
    `warehouse_old`.`warehouse_name`
FROM `warehouse_old`
INNER JOIN `society` ON `warehouse_old`.`id_society` = `society`.`old_id`
INNER JOIN `company` ON `society`.`id` = `company`.`society_id`
WHERE `warehouse_old`.`statut` = 0
SQL);
        $this->addQuery('DROP TABLE `warehouse_old`');
    }

    private function upZones(): void {
        $this->addQuery(<<<'SQL'
CREATE TABLE `society_zone` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_society` INT UNSIGNED NOT NULL,
    `nom` VARCHAR(255) NOT NULL
)
SQL);
        $this->insert('society_zone', ['id', 'id_society', 'nom']);
        $this->addQuery(<<<'SQL'
CREATE TABLE `zone` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `company_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    CONSTRAINT `IDX_A0EBC007979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`)
)
SQL);
        $this->addQuery(<<<'SQL'
INSERT INTO `zone` (`company_id`, `name`)
SELECT `company`.`id`, UCFIRST(`society_zone`.`nom`)
FROM `society_zone`
INNER JOIN `society` ON `society_zone`.`id_society` = `society`.`old_id`
INNER JOIN `company` ON `society`.`id` = `company`.`society_id`
SQL);
        $this->addQuery('DROP TABLE `society_zone`');
    }
}
