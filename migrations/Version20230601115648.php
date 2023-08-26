<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601115648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE preparation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, asked_by_id INT UNSIGNED DEFAULT NULL, component_id INT UNSIGNED DEFAULT NULL, ofnumber_id INT UNSIGNED DEFAULT NULL, operator_id INT UNSIGNED DEFAULT NULL, to_warehouse_id INT UNSIGNED DEFAULT NULL, from_warehouse_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, request_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, realization_date DATETIME DEFAULT NULL, target_location VARCHAR(255) DEFAULT NULL, priority INT DEFAULT 1 NOT NULL, valide TINYINT(1) DEFAULT 0 NOT NULL, requested_quantity_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, requested_quantity_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, requested_quantity_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, sent_quantity_code VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, sent_quantity_denominator VARCHAR(6) DEFAULT NULL COLLATE `utf8mb3_bin`, sent_quantity_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, INDEX IDX_F9F0AAF44F7A72E4 (asked_by_id), INDEX IDX_F9F0AAF4E2ABAFFF (component_id), INDEX IDX_F9F0AAF4A2FA6D9B (ofnumber_id), INDEX IDX_F9F0AAF4584598A3 (operator_id), INDEX IDX_F9F0AAF484154F02 (to_warehouse_id), INDEX IDX_F9F0AAF46DC63638 (from_warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE preparation ADD CONSTRAINT FK_F9F0AAF44F7A72E4 FOREIGN KEY (asked_by_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE preparation ADD CONSTRAINT FK_F9F0AAF4E2ABAFFF FOREIGN KEY (component_id) REFERENCES component (id)');
        $this->addSql('ALTER TABLE preparation ADD CONSTRAINT FK_F9F0AAF4A2FA6D9B FOREIGN KEY (ofnumber_id) REFERENCES manufacturing_order (id)');
        $this->addSql('ALTER TABLE preparation ADD CONSTRAINT FK_F9F0AAF4584598A3 FOREIGN KEY (operator_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE preparation ADD CONSTRAINT FK_F9F0AAF484154F02 FOREIGN KEY (to_warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE preparation ADD CONSTRAINT FK_F9F0AAF46DC63638 FOREIGN KEY (from_warehouse_id) REFERENCES warehouse (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE preparation DROP FOREIGN KEY FK_F9F0AAF44F7A72E4');
        $this->addSql('ALTER TABLE preparation DROP FOREIGN KEY FK_F9F0AAF4E2ABAFFF');
        $this->addSql('ALTER TABLE preparation DROP FOREIGN KEY FK_F9F0AAF4A2FA6D9B');
        $this->addSql('ALTER TABLE preparation DROP FOREIGN KEY FK_F9F0AAF4584598A3');
        $this->addSql('ALTER TABLE preparation DROP FOREIGN KEY FK_F9F0AAF484154F02');
        $this->addSql('ALTER TABLE preparation DROP FOREIGN KEY FK_F9F0AAF46DC63638');
        $this->addSql('DROP TABLE preparation');
    }
}
