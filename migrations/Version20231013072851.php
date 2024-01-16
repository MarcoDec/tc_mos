<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231013072851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add equivalent to nomenclature';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nomenclature ADD equivalent_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE nomenclature ADD CONSTRAINT FK_799A3652D1487398 FOREIGN KEY (equivalent_id) REFERENCES component_equivalent (id)');
        $this->addSql('CREATE INDEX IDX_799A3652D1487398 ON nomenclature (equivalent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nomenclature DROP FOREIGN KEY FK_799A3652D1487398');
        $this->addSql('DROP INDEX IDX_799A3652D1487398 ON nomenclature');
        $this->addSql('ALTER TABLE nomenclature DROP equivalent_id');
    }
}
