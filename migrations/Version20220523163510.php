<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use InvalidArgumentException;

final class Version20220523163510 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migration initiale : récupération de la base de données sans aucun changement.';
    }

    public function up(Schema $schema): void {
        $this->addSql(<<<'SQL'
CREATE TABLE `component_family` (
  `id` tinyint(3) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `family_name` varchar(25) NOT NULL,
  `statut` tinyint(4) NOT NULL DEFAULT '0',
  `copperable` tinyint(4) NOT NULL DEFAULT '0',
  `customsCode` varchar(255) DEFAULT NULL,
  `icon` int(11) DEFAULT NULL,
  `prefix` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL);
        $this->insert('component_family');
        $this->addSql(<<<'SQL'
CREATE TABLE `component_subfamily` (
  `id` smallint(6) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `subfamily_name` varchar(50) NOT NULL,
  `id_family` tinyint(3) UNSIGNED NOT NULL,
  `statut` tinyint(4) NOT NULL DEFAULT '0',
  CONSTRAINT `unic` UNIQUE (`subfamily_name`,`id_family`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL);
        $this->insert('component_subfamily');
        $this->addSql(<<<'SQL'
CREATE TABLE `couleur` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Nom de la couleur',
  `ral` varchar(10) DEFAULT NULL COMMENT 'Code couleur RAL',
  `rgb` varchar(7) DEFAULT NULL COMMENT 'Code couleur RGB',
  CONSTRAINT `couleur_id_uindex` UNIQUE (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Liste des couleurs que peuvent avoir les fils';
SQL);
        $this->insert('couleur');
        $this->addSql(<<<'SQL'
CREATE TABLE `invoicetimedue` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` tinyint(4) NOT NULL COMMENT '0 = Active, 1 = Deleted',
  `libelle` varchar(255) NOT NULL,
  `days` int(11) NOT NULL,
  `endofmonth` tinyint(4) NOT NULL,
  `date_creation` datetime DEFAULT NULL COMMENT 'date de création',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date modification',
  `id_user_creation` int(11) DEFAULT NULL,
  `id_user_modification` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='condition de paiement CLIENT';
SQL);
        $this->insert('invoicetimedue');
        $this->addSql(<<<'SQL'
CREATE TABLE `invoicetimeduesupplier` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` tinyint(4) NOT NULL COMMENT '0 = Active, 1 = Deleted',
  `libelle` varchar(255) NOT NULL,
  `days` int(11) NOT NULL,
  `endofmonth` tinyint(4) NOT NULL,
  `date_creation` datetime DEFAULT NULL COMMENT 'date de création',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date modification',
  `id_user_creation` int(11) DEFAULT NULL,
  `id_user_modification` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='condition de paiement FOURNISSEUR';
SQL);
        $this->insert('invoicetimeduesupplier');
        $this->addSql(<<<'SQL'
CREATE TABLE `messagetva` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` tinyint(4) NOT NULL COMMENT '0 = Active, 1 = Deleted',
  `message` text NOT NULL,
  `date_creation` datetime NOT NULL COMMENT 'date de création',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date modification',
  `id_user_creation` int(11) DEFAULT NULL,
  `id_user_modification` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('messagetva');
    }

    private function insert(string $table): void {
        $filename = __DIR__."/../migrations-data/exportjson_table_$table.json";
        $file = file_get_contents($filename);
        if (!$file) {
            throw new InvalidArgumentException("$filename not found.");
        }
        /** @var array<int, array<string, mixed>> $decoded */
        $decoded = json_decode($file, true);
        $json = collect($decoded);
        $this->addSql(sprintf(
            "INSERT INTO `$table` (%s) VALUES %s",
            collect($json->first())
                ->keys()
                ->map(static fn (string $key): string => "`$key`")
                ->join(','),
            $json
                ->map(function (array $row): string {
                    return '('
                        .collect($row)
                            ->map(function ($value) {
                                if (is_string($value)) {
                                    $value = trim($value);
                                }
                                return $value === null || $value === '0000-00-00 00:00:00'
                                    ? 'NULL'
                                    : $this->connection->quote($value);
                            })
                            ->join(',')
                        .')';
                })
                ->join(',')
        ));
    }
}
