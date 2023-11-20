<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601092046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE production_quality (id INT UNSIGNED AUTO_INCREMENT NOT NULL, employee_id INT UNSIGNED DEFAULT NULL, production_operation_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, comment TEXT NOT NULL, number_of_control INT DEFAULT NULL, record_date DATETIME DEFAULT NULL, type SMALLINT DEFAULT NULL, INDEX IDX_69A786CD8C03F15C (employee_id), INDEX IDX_69A786CD7A26702 (production_operation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE production_quality_value (id INT UNSIGNED AUTO_INCREMENT NOT NULL, component_id INT UNSIGNED DEFAULT NULL, component_stock_id INT UNSIGNED DEFAULT NULL, production_operation_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, date_quality DATETIME DEFAULT NULL, matricule_qualite INT NOT NULL, hauteur_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, hauteur_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, hauteur_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, largeur_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, largeur_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, largeur_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, section_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, section_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, section_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, traction_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, traction_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, traction_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, INDEX IDX_93252DE3E2ABAFFF (component_id), INDEX IDX_93252DE34FCFF5FD (component_stock_id), INDEX IDX_93252DE37A26702 (production_operation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE production_quality ADD CONSTRAINT FK_69A786CD8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE production_quality ADD CONSTRAINT FK_69A786CD7A26702 FOREIGN KEY (production_operation_id) REFERENCES manufacturing_operation (id)');
        $this->addSql('ALTER TABLE production_quality_value ADD CONSTRAINT FK_93252DE3E2ABAFFF FOREIGN KEY (component_id) REFERENCES component (id)');
        $this->addSql('ALTER TABLE production_quality_value ADD CONSTRAINT FK_93252DE34FCFF5FD FOREIGN KEY (component_stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE production_quality_value ADD CONSTRAINT FK_93252DE37A26702 FOREIGN KEY (production_operation_id) REFERENCES manufacturing_operation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production_quality DROP FOREIGN KEY FK_69A786CD8C03F15C');
        $this->addSql('ALTER TABLE production_quality DROP FOREIGN KEY FK_69A786CD7A26702');
        $this->addSql('ALTER TABLE production_quality_value DROP FOREIGN KEY FK_93252DE3E2ABAFFF');
        $this->addSql('ALTER TABLE production_quality_value DROP FOREIGN KEY FK_93252DE34FCFF5FD');
        $this->addSql('ALTER TABLE production_quality_value DROP FOREIGN KEY FK_93252DE37A26702');
        $this->addSql('DROP TABLE production_quality');
        $this->addSql('DROP TABLE production_quality_value');
    }
}
