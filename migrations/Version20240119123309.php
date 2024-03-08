<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240119123309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change la valeur de la colonne type de la table attribute en fonction du champ name: section => measureSelect ';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('UPDATE attribute SET type = \'measureSelect\', unit_id = 14 WHERE name LIKE \'%section%\' OR name LIKE \'%Section%\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('UPDATE attribute SET type = \'text\', unit_id = NULL WHERE name LIKE \'%section%\' OR name LIKE \'%Section%\'');
    }
}
