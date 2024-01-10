<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231030153221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout currency à balance_sheet';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance_sheet ADD currency_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE balance_sheet ADD CONSTRAINT FK_194A20B038248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('CREATE INDEX IDX_194A20B038248176 ON balance_sheet (currency_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE balance_sheet DROP FOREIGN KEY FK_194A20B038248176');
        $this->addSql('DROP INDEX IDX_194A20B038248176 ON balance_sheet');
        $this->addSql('ALTER TABLE balance_sheet DROP currency_id');
    }
}
