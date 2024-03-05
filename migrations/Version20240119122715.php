<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240119122715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change la valeur de la colonne type de la table attribute en fonction du champ name: Couleurs => color ';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE attribute SET type = \'color\' WHERE name LIKE \'%Couleur%\' OR name LIKE \'%couleur%\'');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE attribute SET type = \'text\' WHERE name LIKE \'%Couleur%\' OR name LIKE \'%couleur%\'');
    }
}
