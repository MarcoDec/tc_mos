<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017122544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE voie ADD couleur1 VARCHAR(255) DEFAULT NULL, ADD couleur2 VARCHAR(255) DEFAULT NULL, ADD couleur3 VARCHAR(255) DEFAULT NULL, DROP couleur_1, DROP couleur_2, DROP couleur_3');
    }

    public function down(Schema $schema): void
    {

    }
}
