<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230717121920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_family DROP file_path');
        $this->addSql('ALTER TABLE customer ADD quality_portal_password VARCHAR(255) DEFAULT NULL, ADD quality_portal_url VARCHAR(255) DEFAULT NULL, ADD quality_portal_username VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE operation_employee DROP FOREIGN KEY IDX_B8E90A2C44AC3583');
        $this->addSql('ALTER TABLE operation_employee DROP FOREIGN KEY IDX_B8E90A2C8C03F15C');
        $this->addSql('ALTER TABLE operation_employee ADD id INT UNSIGNED AUTO_INCREMENT NOT NULL, ADD deleted TINYINT(1) DEFAULT 0 NOT NULL, CHANGE operation_id operation_id INT UNSIGNED DEFAULT NULL, CHANGE employee_id employee_id INT UNSIGNED DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE operation_employee ADD CONSTRAINT FK_B8E90A2C8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE operation_employee ADD CONSTRAINT FK_B8E90A2C44AC3583 FOREIGN KEY (operation_id) REFERENCES manufacturing_operation (id)');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind ENUM(\'ARRAY\', \'SELECT_MULTIPLE_LINK\', \'INTEGER\') NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_family ADD file_path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE customer DROP quality_portal_password, DROP quality_portal_url, DROP quality_portal_username');
        $this->addSql('ALTER TABLE operation_employee MODIFY id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE operation_employee DROP FOREIGN KEY FK_B8E90A2C8C03F15C');
        $this->addSql('ALTER TABLE operation_employee DROP FOREIGN KEY FK_B8E90A2C44AC3583');
        $this->addSql('DROP INDEX `PRIMARY` ON operation_employee');
        $this->addSql('ALTER TABLE operation_employee DROP id, DROP deleted, CHANGE employee_id employee_id INT UNSIGNED NOT NULL, CHANGE operation_id operation_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE operation_employee ADD CONSTRAINT IDX_B8E90A2C44AC3583 FOREIGN KEY (operation_id) REFERENCES manufacturing_operation (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE operation_employee ADD CONSTRAINT IDX_B8E90A2C8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE operation_employee ADD PRIMARY KEY (operation_id, employee_id)');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind VARCHAR(255) NOT NULL');
    }
}
