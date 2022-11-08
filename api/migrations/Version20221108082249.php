<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Collection;
use App\Doctrine\Type\Hr\Employee\Role;
use App\Entity\Hr\Employee\Employee;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\UnicodeString;

final class Version20221108082249 extends AbstractMigration {
    /** @var array<int, string> */
    private const EMPTY = [];

    private UserPasswordHasherInterface $hasher;

    /** @var Collection<int, string> */
    private Collection $queries;

    public function __construct(Connection $connection, LoggerInterface $logger) {
        parent::__construct($connection, $logger);
        $this->queries = new Collection(self::EMPTY);
    }

    private static function getVersion(): string {
        $matches = [];
        preg_match('/Version\d+/', self::class, $matches);
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
        $this->defineQueries();
        $procedure = self::getVersion();
        $this->addSql("CREATE PROCEDURE $procedure() BEGIN {$this->getQueries()}; END");
        $this->addSql("CALL $procedure");
        $this->addSql("DROP PROCEDURE $procedure");
        $this->addSql('DROP FUNCTION UCFIRST');
    }

    private function defineQueries(): void {
        $this->upAttributes();
        $this->upUnits();
        $this->upEmployees();
    }

    private function getQueries(): string {
        return (new UnicodeString($this->queries->implode(';')))
            ->replaceMatches('/\s+/', ' ')
            ->replace('( ', '(')
            ->replace(' )', ')')
            ->toString();
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
                ->filter(static fn (Collection $row): bool => (int) $row->get('id') > 0)
                ->map(
                    fn (Collection $row): string => '('
                        .$row
                            ->map(function (bool|float|int|null|string $column): string {
                                if (is_string($column)) {
                                    $column = trim($column);
                                    if ($column === '') {
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

    private function push(string $query): void {
        $this->queries = $this->queries->push($query);
    }

    private function upAttributes(): void {
        $this->push(<<<'SQL'
CREATE TABLE `attribut` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `libelle` VARCHAR(100) NOT NULL,
    `attribut_id_family` VARCHAR(255) DEFAULT NULL,
    `unit_id` INT DEFAULT NULL,
    `type` VARCHAR(255) DEFAULT NULL
)
SQL);
        $this->insert('attribut');
        $this->push(<<<'SQL'
CREATE TABLE `attribute` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `name` VARCHAR(255) NOT NULL,
    `type` ENUM('bool','color','int','percent','text','unit') NOT NULL COMMENT '(DC2Type:attribute)'
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `attribute` (`description`, `name`, `type`)
SELECT `description`, UCFIRST(`libelle`), `type`
FROM `attribut`
WHERE `statut` = FALSE
SQL);
    }

    private function upEmployees(): void {
        $this->push(<<<'SQL'
CREATE TABLE `employee` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT FALSE NOT NULL,
    `password` CHAR(60) DEFAULT NULL COMMENT '(DC2Type:char)',
    `roles` SET('ROLE_MANAGEMENT_ADMIN','ROLE_PURCHASE_ADMIN') NOT NULL COMMENT '(DC2Type:role)',
    `username` VARCHAR(20) DEFAULT NULL
)
SQL);
        ($user = new Employee())
            ->setUsername('super')
            ->setPassword($this->hasher->hashPassword($user, 'super'));
        $this->push(sprintf(
            'INSERT INTO `employee` (`password`, `roles`, `username`) VALUES (%s, %s, %s)',
            $this->platform->quoteStringLiteral((string) $user->getPassword()),
            $this->platform->quoteStringLiteral(implode(',', [Role::ROLE_MANAGEMENT_ADMIN, Role::ROLE_PURCHASE_ADMIN])),
            $this->platform->quoteStringLiteral((string) $user->getUsername())
        ));
        $this->push('DROP TABLE `attribut`');
    }

    private function upUnits(): void {
        $this->push(<<<'SQL'
CREATE TABLE `unit` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `statut` BOOLEAN DEFAULT FALSE NOT NULL,
    `base` DOUBLE PRECISION DEFAULT 1 NOT NULL,
    `unit_short_lbl` VARCHAR(6) NOT NULL,
    `unit_complete_lbl` VARCHAR(50) NOT NULL,
    `parent` INT UNSIGNED DEFAULT NULL
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
    `name` VARCHAR(50) NOT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL
)
SQL);
        $this->push(<<<'SQL'
INSERT INTO `unit` (`id`, `base`, `code`, `name`, `parent_id`)
SELECT `id`, `base`, `unit_short_lbl`, UCFIRST(`unit_complete_lbl`), `parent`
FROM `old_unit`
SQL);
        $this->push('DROP TABLE `old_unit`');
        $this->push('ALTER TABLE `unit` ADD CONSTRAINT `IDX_DCBB0C53727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `unit` (`id`)');
    }
}
