<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use InvalidArgumentException;

final class Version20220620150400 extends AbstractMigration {
    public function getDescription(): string {
        return 'Migration initiale : récupération de la base de données sans aucun changement.';
    }

    public function up(Schema $schema): void {
        $this->addSql(<<<'SQL'
CREATE TABLE `attribut` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` int(11) NOT NULL COMMENT '0 = Active, 1 = Deleted	',
  `description` varchar(200) DEFAULT NULL,
  `libelle` varchar(100) NOT NULL,
  `attribut_id_family` varchar(255) DEFAULT NULL,
  `isBrokenLinkSolved` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('attribut');
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
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
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
CREATE TABLE `customcode` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` tinyint(4) NOT NULL COMMENT '0 = Active, 1 = Deleted',
  `code` varchar(255) NOT NULL,
  `libelle` text NOT NULL,
  `date_creation` datetime DEFAULT NULL COMMENT 'date de création',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date modification',
  `id_user_creation` int(11) DEFAULT NULL,
  `id_user_modification` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('customcode');
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
CREATE TABLE `product` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` tinyint(4) NOT NULL COMMENT '0 = Active, 1 = Deleted',
  `freeze` int(1) NOT NULL DEFAULT '0',
  `id_customer` int(11) NOT NULL,
  `id_product_family` int(11) NOT NULL,
  `id_product_subfamily` int(11) NOT NULL,
  `id_factory` int(11) DEFAULT NULL,
  `id_country` int(11) NOT NULL,
  `id_society` int(11) NOT NULL DEFAULT '0',
  `id_productstatus` int(11) NOT NULL DEFAULT '1',
  `ref` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `commande_ouverte` varchar(100) DEFAULT NULL,
  `price_without_cu` double(24,5) NOT NULL COMMENT 'inutilisé, uniquement pour besoin de sauvegarde',
  `price` double(24,5) NOT NULL,
  `transfert_price_work` double(24,5) NOT NULL DEFAULT '0.00000' COMMENT 'prix de cession main d''oeuvre',
  `transfert_price_supplies` double(24,5) NOT NULL DEFAULT '0.00000' COMMENT 'prix de cession matière',
  `nb_pcs_h_auto` float NOT NULL DEFAULT 0,
  `nb_pcs_h_manu` float NOT NULL DEFAULT 0,
  `weight` double(10,2) NOT NULL,
  `indice` varchar(15) NOT NULL,
  `indice_interne` int(3) NOT NULL DEFAULT '1',
  `id_product_child` int(11) NOT NULL,
  `gestion_cu` tinyint(4) NOT NULL,
  `qtcu` decimal(10,3) NOT NULL,
  `id_customcode` int(11) NOT NULL DEFAULT '0',
  `date_expiration` date DEFAULT NULL,
  `conditionnement` varchar(255) DEFAULT NULL COMMENT 'quantite par carton',
  `typeconditionnement` varchar(255) NOT NULL COMMENT 'bac, palette, carton ...',
  `min_prod_quantity` int(11) NOT NULL COMMENT 'minimum de mise en production',
  `stock_minimum` int(11) NOT NULL COMMENT 'quantite minimum en stock',
  `production_delay` int(11) NOT NULL COMMENT 'delai de réappro (jours)',
  `livraison_minimum` int(11) NOT NULL COMMENT 'equivalent de la MOQ',
  `volume_previsionnel` int(11) NOT NULL COMMENT 'volume de vente prévu annuellement pour le client',
  `id_incoterms` int(11) DEFAULT NULL COMMENT 'cas particulier',
  `info_public` text DEFAULT NULL,
  `is_prototype` tinyint(4) DEFAULT NULL COMMENT '0=série, 1=proto, 2=echantillon',
  `c_200` tinyint(4) DEFAULT '0' COMMENT 'validation DIRECTION',
  `c_300` tinyint(4) DEFAULT '0' COMMENT 'validation qualité',
  `c_600` tinyint(4) DEFAULT '0' COMMENT 'validation production',
  `c_700` tinyint(4) DEFAULT '0' COMMENT 'validation logistique',
  `c_800` tinyint(4) DEFAULT '0' COMMENT 'validation achat',
  `date_creation` datetime DEFAULT NULL COMMENT 'date de création',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date modification',
  `id_user_creation` int(11) DEFAULT NULL,
  `id_user_modification` int(11) DEFAULT NULL,
  `price_old` double(24,5) NOT NULL DEFAULT '0.00000',
  `temps_auto` varchar(255) DEFAULT NULL,
  `temps_manu` varchar(255) DEFAULT NULL,
  `temps_inertie` float DEFAULT NULL,
  `price_tn` float DEFAULT NULL,
  `price_md` float DEFAULT NULL,
  `imds` tinyint(1) NOT NULL DEFAULT '0',
  `tps_chiff_auto` float UNSIGNED NOT NULL DEFAULT '0',
  `tps_chiff_manu` float UNSIGNED NOT NULL DEFAULT '0',
  `isBrokenLinkSolved` tinyint(1) NOT NULL DEFAULT '0',
  `max_proto_quantity` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('product');
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
  `libelle` varchar(255) DEFAULT NULL,
  `id_user_creation` int(11) NOT NULL,
  `date_creation` datetime DEFAULT NULL,
  `id_user_modification` int(11) NOT NULL,
  `date_modification` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('production_rejectlist');
        $this->addSql(<<<'SQL'
CREATE TABLE `society` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `statut` tinyint(4) NOT NULL COMMENT '0 = Active, 1 = Deleted',
  `id_soc_gest` int(11) NOT NULL COMMENT 'societé gérant le FOURNISSEUR',
  `id_soc_gest_customer` int(11) NOT NULL COMMENT 'societé gérant le CLIENT',
  `nom` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `capital` varchar(255) DEFAULT NULL,
  `address1` text DEFAULT NULL,
  `address2` text DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `city_timezone` varchar(255) DEFAULT NULL,
  `id_customer_group` int(11) NOT NULL DEFAULT '0',
  `id_country` int(11) NOT NULL,
  `id_locale` int(11) NOT NULL,
  `id_invoicetimedue` int(11) DEFAULT '0',
  `id_societystatus` int(11) NOT NULL,
  `id_invoicetimeduesupplier` int(11) NOT NULL DEFAULT '0',
  `invoice_minimum` int(11) DEFAULT NULL COMMENT 'mini de facturation (client)',
  `order_minimum` int(11) NOT NULL DEFAULT '0' COMMENT 'mini de commande (fournisseur)',
  `web` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `formejuridique` varchar(255) DEFAULT NULL,
  `siren` varchar(255) DEFAULT NULL,
  `tva` varchar(255) DEFAULT NULL,
  `compte_compta` varchar(50) DEFAULT NULL,
  `info_public` text DEFAULT NULL,
  `info_private` text DEFAULT NULL,
  `blocked` tinyint(1) DEFAULT NULL,
  `delai_livraison` int(11) DEFAULT NULL COMMENT 'délai d''acheminement en jours',
  `ar_enabled` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'le fournisseur envoie des accusés de recepton (1=oui, 0=non)',
  `gest_quality` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=Oui / 0=Non',
  `ar_customer_enabled` tinyint(4) NOT NULL DEFAULT '1',
  `indice_cu_enabled` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Pour les fournisseurs. Pour les clients défini auto(0) ou manu (1)',
  `indice_cu` double(24,4) DEFAULT NULL,
  `indice_cu_date` date DEFAULT NULL,
  `indice_cu_date_fin` date DEFAULT NULL,
  `taux_operation_manu` double(10,2) DEFAULT NULL,
  `id_incoterms` int(11) DEFAULT NULL COMMENT 'incoterms CLIENT cas général',
  `taux_operation_auto` double(10,2) DEFAULT NULL,
  `quality` int(11) DEFAULT NULL,
  `rib` text,
  `pied_page_achat` text DEFAULT NULL,
  `pied_page_vente` text DEFAULT NULL,
  `pied_page_achat_en` text DEFAULT NULL,
  `pied_page_vente_en` text DEFAULT NULL,
  `pied_page_documents` text DEFAULT NULL,
  `pied_page_avoir` text DEFAULT NULL,
  `pied_page_avoir_en` text DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL COMMENT 'date de création',
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date modification',
  `id_user_creation` int(11) DEFAULT NULL,
  `id_user_modification` int(11) DEFAULT NULL,
  `is_supplier` tinyint(4) DEFAULT NULL,
  `is_customer` tinyint(4) DEFAULT NULL,
  `is_company` tinyint(4) DEFAULT NULL,
  `is_secured` int(1) NOT NULL DEFAULT '1' COMMENT 'si le client a une gestion securisee du stock de composant',
  `force_tva` int(11) NOT NULL DEFAULT '0' COMMENT 'selecteur par defaut en création de facture',
  `id_messagetva` int(11) NOT NULL DEFAULT '0' COMMENT 'selecteur par defaut en création de facture',
  `invoicecustomer_by_email` tinyint(1) NOT NULL DEFAULT '0',
  `sujet_email_facture` varchar(255) DEFAULT NULL,
  `deliveryTimeOpenDays` tinyint(4) NOT NULL DEFAULT '1',
  `generalMargin` float NOT NULL DEFAULT '0',
  `ip` varchar(200) DEFAULT NULL,
  `managementFees` float NOT NULL DEFAULT '0',
  `numberOfTeamPerDay` int(11) NOT NULL DEFAULT '0',
  `workTimetable` text DEFAULT NULL,
  `comptaPortal` int(11) DEFAULT NULL,
  `confidenceCriteria` int(11) NOT NULL DEFAULT '0',
  `currency` varchar(20) NOT NULL DEFAULT '€',
  `logisticPortal` int(11) DEFAULT NULL,
  `maxOutstanding` int(11) NOT NULL DEFAULT '0',
  `monthlyOutstanding` int(11) NOT NULL DEFAULT '0',
  `nbBL` int(11) NOT NULL DEFAULT '0',
  `nbInvoice` int(11) NOT NULL DEFAULT '0',
  `qualityPortal` int(11) DEFAULT NULL DEFAULT '0',
  `isBrokenLinkSolved` tinyint(1) NOT NULL DEFAULT '0',
  `taux_operation_auto_md` int(11) DEFAULT NULL,
  `taux_operation_manu_md` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SQL);
        $this->insert('society');
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
                                    if (strlen($value) === 0) {
                                        $value = null;
                                    }
                                }
                                /** @phpstan-ignore-next-line */
                                return $value === null || $value === '0000-00-00 00:00:00' || $value === '0000-00-00'
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
