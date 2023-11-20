<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601130800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE production_quality_reject (id INT UNSIGNED AUTO_INCREMENT NOT NULL, production_operation_id INT UNSIGNED DEFAULT NULL, reject_type_id INT UNSIGNED DEFAULT NULL, quality_control_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, quantity_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, quantity_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, quantity_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, INDEX IDX_BDFC73BB7A26702 (production_operation_id), INDEX IDX_BDFC73BB6939465C (reject_type_id), INDEX IDX_BDFC73BB9A8E648E (quality_control_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE production_quality_reject ADD CONSTRAINT FK_BDFC73BB7A26702 FOREIGN KEY (production_operation_id) REFERENCES manufacturing_operation (id)');
        $this->addSql('ALTER TABLE production_quality_reject ADD CONSTRAINT FK_BDFC73BB6939465C FOREIGN KEY (reject_type_id) REFERENCES reject_type (id)');
        $this->addSql('ALTER TABLE production_quality_reject ADD CONSTRAINT FK_BDFC73BB9A8E648E FOREIGN KEY (quality_control_id) REFERENCES production_quality (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production_quality_reject DROP FOREIGN KEY FK_BDFC73BB7A26702');
        $this->addSql('ALTER TABLE production_quality_reject DROP FOREIGN KEY FK_BDFC73BB6939465C');
        $this->addSql('ALTER TABLE production_quality_reject DROP FOREIGN KEY FK_BDFC73BB9A8E648E');
        $this->addSql('DROP TABLE production_quality_reject');
    }
}
