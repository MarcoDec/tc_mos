<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220517111344 extends AbstractMigration {
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
  `prefix` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `component_family` (`id`, `family_name`, `statut`, `copperable`, `customsCode`, `icon`, `prefix`) VALUES
(1, 'Cables', 0, 0, NULL, NULL, 'Cab'),
(2, 'Fixations', 0, 0, NULL, NULL, 'Fix'),
(3, 'Protections', 0, 0, NULL, NULL, 'Pro'),
(4, 'Connecteurs', 0, 0, NULL, NULL, 'Con'),
(5, 'Terminaux', 0, 0, NULL, NULL, 'Ter'),
(6, 'Emballage', 0, 0, NULL, NULL, 'Emb'),
(7, 'Fournitures générales', 0, 0, NULL, NULL, 'Fou'),
(8, 'Matière première', 0, 0, NULL, NULL, 'Mat'),
(9, 'Composants électroniques', 0, 0, NULL, NULL, 'Com'),
(10, 'Rechange', 0, 0, NULL, NULL, 'Rec'),
(11, 'Outillage', 0, 0, NULL, NULL, 'Out'),
(12, 'Sous Faisceau', 0, 0, NULL, NULL, 'Sou'),
(13, 'Sous-Produit', 0, 0, NULL, NULL, 'Sou')
SQL);
        $this->addSql(<<<'SQL'
CREATE TABLE `component_subfamily` (
  `id` smallint(6) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `subfamily_name` varchar(50) NOT NULL,
  `id_family` tinyint(3) UNSIGNED NOT NULL,
  `statut` tinyint(4) NOT NULL DEFAULT '0',
  CONSTRAINT `unic` UNIQUE (`subfamily_name`,`id_family`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `component_subfamily` (`id`, `subfamily_name`, `id_family`, `statut`) VALUES
(1, 'Fil', 1, 0),
(2, 'Cordons', 1, 0),
(3, 'Agrafe', 2, 0),
(4, 'Colliers', 2, 0),
(5, 'Scotch', 2, 0),
(6, 'Gaine lisse', 3, 0),
(7, 'Gaine Annel&eacute;e', 3, 0),
(8, 'Gaine tressee', 3, 0),
(9, 'Accessoires', 3, 0),
(10, 'M&acirc;le', 4, 0),
(11, 'Femelle', 4, 0),
(12, 'Clip', 5, 0),
(13, 'Languette', 5, 0),
(14, 'Cosses', 5, 0),
(15, 'Joint', 5, 0),
(16, 'Etiquette', 6, 0),
(17, 'Carton', 6, 0),
(18, 'Gaine thermorétractable', 3, 0),
(19, 'Sac', 6, 0),
(20, 'Accessoires', 6, 0),
(21, 'Autres', 7, 0),
(22, 'Consommable', 7, 0),
(23, 'Petit outillage', 7, 0),
(24, 'Service/Sous-traitance', 7, 0),
(25, 'Accessoires', 4, 0),
(26, 'Accessoires', 2, 0),
(27, 'Matières plastiques', 8, 0),
(28, 'Autres', 2, 0),
(29, 'Relais', 9, 0),
(30, 'Diodes', 9, 0),
(31, 'Résistances', 9, 0),
(32, 'Fusibles', 9, 0),
(33, 'Autres', 9, 0),
(35, 'Divers', 1, 0),
(36, 'KIT', 4, 0),
(37, 'Embouts', 5, 0),
(41, 'Rechange', 10, 0),
(42, 'contre partie pour assemblage connecteur', 4, 0),
(43, 'Outillage', 11, 0),
(44, 'Sous Faisceau E4V', 12, 0),
(45, 'SPD', 13, 0),
(46, 'Sous-famille KUHN', 12, 0),
(47, 'Matière découpe', 8, 0)
SQL);
        $this->addSql(<<<'SQL'
CREATE TABLE `couleur` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Nom de la couleur',
  `ral` varchar(10) DEFAULT NULL COMMENT 'Code couleur RAL',
  `rgb` varchar(7) DEFAULT NULL COMMENT 'Code couleur RGB'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Liste des couleurs que peuvent avoir les fils';
SQL);
        $this->addSql(<<<'SQL'
INSERT INTO `couleur` (`id`, `name`, `ral`, `rgb`) VALUES
(1, 'Rouge', '', '#FF0000'),
(2, 'Bleu', '', '#0000CC'),
(3, 'Vert', '', '#00cc00'),
(4, 'Jaune', '', '#ffff33'),
(5, 'Blanc', '', '#ffffff'),
(6, 'Noir', '', '#000000'),
(7, 'Rose', '', '#ff9999'),
(8, 'Violet', '', '#cc00cc'),
(9, 'Marron', '', '#994c00'),
(10, 'Beige', '', '#ffb266'),
(11, 'Bleu clair', '', '#3399ff'),
(12, 'chocolat', '2014', '#6E2C00'),
(13, 'Gris', '17122018', '#848484'),
(14, 'Orange', '', '#EAA717');
SQL);
    }
}
