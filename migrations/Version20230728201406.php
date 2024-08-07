<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230728201406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE engine ADD manufacturer_engine_id INT UNSIGNED DEFAULT NULL, ADD serial_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE engine ADD CONSTRAINT FK_E8A81A8DFE6572EB FOREIGN KEY (manufacturer_engine_id) REFERENCES manufacturer_engine (id)');
        $this->addSql('CREATE INDEX IDX_E8A81A8DFE6572EB ON engine (manufacturer_engine_id)');
        $this->addSql('ALTER TABLE manufacturer_engine DROP FOREIGN KEY IDX_F514547DE78C9C0A');
        $this->addSql('DROP INDEX UNIQ_F514547DE78C9C0A ON manufacturer_engine');
        $this->addSql('ALTER TABLE manufacturer_engine DROP engine_id, CHANGE serial_number part_number VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE engine DROP FOREIGN KEY FK_E8A81A8DFE6572EB');
        $this->addSql('DROP INDEX IDX_E8A81A8DFE6572EB ON engine');
        $this->addSql('ALTER TABLE engine DROP manufacturer_engine_id, DROP serial_number');
        $this->addSql('ALTER TABLE manufacturer_engine ADD engine_id INT UNSIGNED DEFAULT NULL, CHANGE part_number serial_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE manufacturer_engine ADD CONSTRAINT IDX_F514547DE78C9C0A FOREIGN KEY (engine_id) REFERENCES engine (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F514547DE78C9C0A ON manufacturer_engine (engine_id)');
    }
}
