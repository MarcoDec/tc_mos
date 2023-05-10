<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230427135917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company ADD currency_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F38248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('CREATE INDEX IDX_4FBF094F38248176 ON company (currency_id)');
        $this->addSql('ALTER TABLE parameter CHANGE type type ENUM(\'ARRAY\', \'SELECT_MULTIPLE_LINK\', \'INTEGER\') NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F38248176');
        $this->addSql('DROP INDEX IDX_4FBF094F38248176 ON company');
        $this->addSql('ALTER TABLE company DROP currency_id');
        $this->addSql('ALTER TABLE parameter CHANGE type type VARCHAR(255) NOT NULL');
    }
}
