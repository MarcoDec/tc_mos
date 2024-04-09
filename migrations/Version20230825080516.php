<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230825080516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_family ADD file_path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE engine DROP FOREIGN KEY FK_E8A81A8DFE6572EB');
        $this->addSql('ALTER TABLE engine ADD CONSTRAINT FK_E8A81A8DFE6572EB FOREIGN KEY (manufacturer_engine_id) REFERENCES manufacturer_engine (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE manufacturer_engine DROP FOREIGN KEY IDX_E8A81A8DFE54D948');
        $this->addSql('DROP INDEX IDX_E8A81A8DFE54D948 ON manufacturer_engine');
        $this->addSql('ALTER TABLE manufacturer_engine DROP group_id, CHANGE type type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind ENUM(\'ARRAY\', \'SELECT_MULTIPLE_LINK\', \'INTEGER\') NOT NULL');
        $this->addSql('ALTER TABLE reference CHANGE sample_quantity sample_quantity INT DEFAULT NULL, CHANGE min_value_value min_value_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE max_value_value max_value_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_family DROP file_path');
        $this->addSql('ALTER TABLE engine DROP FOREIGN KEY FK_E8A81A8DFE6572EB');
        $this->addSql('ALTER TABLE engine ADD CONSTRAINT FK_E8A81A8DFE6572EB FOREIGN KEY (manufacturer_engine_id) REFERENCES manufacturer_engine (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE manufacturer_engine ADD group_id INT UNSIGNED DEFAULT NULL, CHANGE type type ENUM(\'counter-part\', \'tool\', \'workstation\') NOT NULL COMMENT \'(DC2Type:engine)\'');
        $this->addSql('ALTER TABLE manufacturer_engine ADD CONSTRAINT IDX_E8A81A8DFE54D948 FOREIGN KEY (group_id) REFERENCES engine_group (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E8A81A8DFE54D948 ON manufacturer_engine (group_id)');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reference CHANGE sample_quantity sample_quantity INT UNSIGNED DEFAULT 1, CHANGE min_value_value min_value_value DOUBLE PRECISION DEFAULT \'0\', CHANGE max_value_value max_value_value DOUBLE PRECISION DEFAULT \'0\'');
    }
}
