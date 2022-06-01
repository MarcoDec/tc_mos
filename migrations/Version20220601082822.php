<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use InvalidArgumentException;

final class Version20220601082822 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migration initiale : récupération de la base de données sans aucun changement.';
    }

    public function up(Schema $schema): void {
        $this->addSql(<<<'SQL'
CREATE TABLE `carrier` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` tinyint(4) NOT NULL COMMENT '0 = Active, 1 = Deleted',
  `nom` text NOT NULL,
  `date_creation` datetime DEFAULT NULL COMMENT 'date de création',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date modification',
  `id_user_creation` int(11) DEFAULT NULL,
  `id_user_modification` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('carrier');
        $this->addSql(<<<'SQL'
CREATE TABLE `component_family` (
  `id` tinyint(3) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `prefix` varchar(3) DEFAULT NULL,
  `copperable` tinyint(4) NOT NULL DEFAULT '0',
  `customsCode` varchar(255) DEFAULT NULL,
  `statut` tinyint(4) NOT NULL DEFAULT '0',
  `family_name` varchar(25) NOT NULL,
  `icon` int(11) DEFAULT NULL
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
CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `statut` tinyint(4) NOT NULL COMMENT '0 = Active, 1 = Deleted',
  `code` varchar(2) NOT NULL,
  `code_iso` varchar(3) DEFAULT NULL,
  `libelle` text,
  `in_eu` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si le pays appartient a l''union europeenne',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `phone_prefix` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL COMMENT 'date de création',
  `date_modification` timestamp DEFAULT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date modification',
  `id_user_creation` int(11) DEFAULT NULL,
  `id_user_modification` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL);
        $this->insert('country');
        $this->addSql(<<<'SQL'
CREATE TABLE `employee_eventlist` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `motif` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('employee_eventlist');
        $this->addSql(<<<'SQL'
CREATE TABLE `employee_extformateur` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `ville` varchar(255) NOT NULL,
  `tel` varchar(255) NOT NULL,
  `code_postal` int(11) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `id_phone_prefix` int(11) NOT NULL,
  `society` varchar(255) NOT NULL,
  `id_user_creation` int(11) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('employee_extformateur');
        $this->addSql(<<<'SQL'
CREATE TABLE `engine_group` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `code` varchar(3) NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `id_family_group` int(11) NOT NULL,
  `organe_securite` int(1) NOT NULL DEFAULT '0',
  `formation_specifique` int(11) NOT NULL DEFAULT '0',
  CONSTRAINT `code` UNIQUE (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('engine_group');
        $this->addSql(<<<'SQL'
CREATE TABLE `incoterms` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `code` varchar(30) DEFAULT NULL,
  `statut` tinyint(4) NOT NULL DEFAULT '0',
  `label` varchar(60) DEFAULT NULL,
  CONSTRAINT `uk_c_input_reason` UNIQUE (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL);
        $this->insert('incoterms');
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
        $this->addSql(<<<'SQL'
CREATE TABLE `product_family` (
  `id` tinyint(3) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `customsCode` varchar(255) DEFAULT NULL,
  `statut` int(11) NOT NULL DEFAULT '0',
  `family_name` varchar(25) NOT NULL,
  `icon` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL);
        $this->insert('product_family');
        $this->addSql(<<<'SQL'
CREATE TABLE `product_subfamily` (
  `id` smallint(6) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `subfamily_name` varchar(50) NOT NULL,
  `id_family` tinyint(3) UNSIGNED NOT NULL,
  CONSTRAINT `unic` UNIQUE (`subfamily_name`,`id_family`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL);
        $this->insert('product_subfamily');
        $this->addSql(<<<'SQL'
CREATE TABLE `production_rejectlist` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` int(1) NOT NULL DEFAULT '0',
  `libelle` varchar(255) NOT NULL,
  `id_user_creation` int(11) NOT NULL,
  `date_creation` datetime DEFAULT NULL,
  `id_user_modification` int(11) NOT NULL,
  `date_modification` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('production_rejectlist');
        $this->addSql(<<<'SQL'
CREATE TABLE `qualitycontrol` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` int(11) NOT NULL,
  `qualitycontrol` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('qualitycontrol');
        $this->addSql(<<<'SQL'
CREATE TABLE `unit` (
  `id` tinyint(4) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `base` DOUBLE PRECISION DEFAULT '1' NOT NULL,
  `unit_short_lbl` varchar(15) NOT NULL,
  `statut` tinyint(4) NOT NULL COMMENT '0 = Active, 1 = Deleted',
  `unit_complete_lbl` varchar(40) NOT NULL,
  `parent` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='liste des unites de mesure pour composant';
SQL);
        $this->insert('unit');
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
