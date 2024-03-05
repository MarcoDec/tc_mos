<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231030154940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance_sheet ADD unit_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE balance_sheet ADD CONSTRAINT FK_194A20B0F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('CREATE INDEX IDX_194A20B0F8BD700D ON balance_sheet (unit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance_sheet DROP FOREIGN KEY FK_194A20B0F8BD700D');
        $this->addSql('DROP INDEX IDX_194A20B0F8BD700D ON balance_sheet');
        $this->addSql('ALTER TABLE balance_sheet DROP unit_id');
    }
}
