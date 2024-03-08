<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240119124559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change la valeur de la colonne type de la table attribute en fonction du champ name: dimension (mm) => measureSelect ';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'UPDATE attribute 
                SET type = \'measureSelect\', unit_id = 16 
                WHERE name LIKE \'%Taille%\' OR name LIKE \'%taille%\'
                OR name LIKE \'%Dia%\' OR name LIKE \'%dia%\'
                OR name LIKE \'%Longueur%\' OR name LIKE \'%longueur%\'
                OR name LIKE \'%Largeur%\' OR name LIKE \'%largeur%\' or name LIKE \'%Largueur%\' OR name LIKE \'%largueur%\'
                OR name LIKE \'%Dimension%\' OR name LIKE \'%dimension%\'
                OR name LIKE \'%Tolérance%\' OR name LIKE \'%tolérance%\'
                OR name LIKE \'%Épaisseur%\' OR name LIKE \'%épaisseur%\'
                '
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'UPDATE attribute 
                SET type = \'text\', unit_id = NULL 
                WHERE name LIKE \'%Taille%\' OR name LIKE \'%taille%\'
                OR name LIKE \'%Dia%\' OR name LIKE \'%dia%\'
                OR name LIKE \'%Longueur%\' OR name LIKE \'%longueur%\'
                OR name LIKE \'%Largeur%\' OR name LIKE \'%largeur%\'
                OR name LIKE \'%Dimension%\' OR name LIKE \'%dimension%\'
                OR name LIKE \'%Tolérance%\' OR name LIKE \'%tolérance%\'
                OR name LIKE \'%Épaisseur%\' OR name LIKE \'%épaisseur%\'
                '
        );
    }
}
