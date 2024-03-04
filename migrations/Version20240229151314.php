<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229151314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add warehouse_id to zone table and make warehouse_id nullable';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee CHANGE matricule matricule VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE warehouse CHANGE old_id old_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE zone ADD warehouse_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE zone ADD CONSTRAINT FK_A0EBC0075080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('CREATE INDEX IDX_A0EBC0075080ECDE ON zone (warehouse_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee CHANGE matricule matricule INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE warehouse CHANGE old_id old_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE zone DROP FOREIGN KEY FK_A0EBC0075080ECDE');
        $this->addSql('DROP INDEX IDX_A0EBC0075080ECDE ON zone');
        $this->addSql('ALTER TABLE zone DROP warehouse_id');
    }
}
