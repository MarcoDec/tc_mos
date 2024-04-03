<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240119124005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change la valeur de la colonne type de la table attribute en fonction du champ name: nombre => int ';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'UPDATE attribute 
            SET type = \'int\' 
            WHERE name LIKE \'%nombre%\' OR name LIKE \'%Nombre%\'
            '
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'UPDATE attribute 
            SET type = \'text\' 
            WHERE name LIKE \'%nombre%\' OR name LIKE \'%Nombre%\' 
            '
        );
    }
}
