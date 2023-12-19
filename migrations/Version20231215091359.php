<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231215091359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout Largeur maximale et hauteure maximale des étiquettes en inch pour les imprimantes';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE printer ADD max_label_width DOUBLE PRECISION DEFAULT NULL, ADD max_label_height DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE printer DROP max_label_width, DROP max_label_height');
    }
}
