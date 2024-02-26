<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240119125901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change la valeur de la colonne type de la table attribute en fonction du champ name: courant => A ';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'UPDATE attribute 
                SET type = \'measureSelect\', unit_id = 15 
                WHERE name LIKE \'%courant%\' OR name LIKE \'%Courant%\'
                '
        );
        $this->addSql(
            'UPDATE attribute 
                SET type = \'measureSelect\', unit_id = 18 
                WHERE name LIKE \'%Voltage%\' OR name LIKE \'%voltage%\'
                '
        );
        $this->addSql(
            'UPDATE attribute 
                SET type = \'measureSelect\', unit_id = 20 
                WHERE name LIKE \'%Résistance%\' OR name LIKE \'%résistance%\'
                '
        );

        $this->addSql(
            'UPDATE attribute 
                SET type = \'measureSelect\', unit_id = 17 
                WHERE name LIKE \'%T°%\'
                '
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'UPDATE attribute 
                SET type = \'text\', unit_id = NULL 
                WHERE name LIKE \'%courant%\' OR name LIKE \'%Courant%\'
                '
        );
        $this->addSql(
            'UPDATE attribute 
                SET type = \'text\', unit_id = NULL 
                WHERE name LIKE \'%Voltage%\' OR name LIKE \'%voltage%\'
                '
        );
        $this->addSql(
            'UPDATE attribute 
                SET type = \'text\', unit_id = NULL 
                WHERE name LIKE \'%Résistance%\' OR name LIKE \'%résistance%\'
                '
        );
        $this->addSql(
            'UPDATE attribute 
                SET type = \'text\', unit_id = NULL 
                WHERE name LIKE \'%T°%\'
                '
        );

    }
}
