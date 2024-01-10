<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231103080619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance_sheet_item ADD balance_sheet_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE balance_sheet_item ADD CONSTRAINT FK_14C65321A092B715 FOREIGN KEY (balance_sheet_id) REFERENCES balance_sheet (id)');
        $this->addSql('CREATE INDEX IDX_14C65321A092B715 ON balance_sheet_item (balance_sheet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance_sheet_item DROP FOREIGN KEY FK_14C65321A092B715');
        $this->addSql('DROP INDEX IDX_14C65321A092B715 ON balance_sheet_item');
        $this->addSql('ALTER TABLE balance_sheet_item DROP balance_sheet_id');
    }
}
