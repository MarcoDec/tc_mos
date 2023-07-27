<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230725134937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE manufacturing_order ADD CONSTRAINT FK_34010DB14584665A FOREIGN KEY (product_id) REFERENCES product_customer (id)');
        $this->addSql('ALTER TABLE operation_employee RENAME INDEX fk_b8e90a2c8c03f15c TO IDX_B8E90A2C8C03F15C');
        $this->addSql('ALTER TABLE operation_employee RENAME INDEX fk_b8e90a2c44ac3583 TO IDX_B8E90A2C44AC3583');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind ENUM(\'ARRAY\', \'SELECT_MULTIPLE_LINK\', \'INTEGER\') NOT NULL');
        $this->addSql('ALTER TABLE selling_order_item DROP FOREIGN KEY IDX_8A64F2304584665A');
        $this->addSql('ALTER TABLE selling_order_item ADD CONSTRAINT FK_8A64F2304584665A FOREIGN KEY (product_id) REFERENCES product_customer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE manufacturing_order DROP FOREIGN KEY FK_34010DB14584665A');
        $this->addSql('ALTER TABLE operation_employee RENAME INDEX idx_b8e90a2c8c03f15c TO FK_B8E90A2C8C03F15C');
        $this->addSql('ALTER TABLE operation_employee RENAME INDEX idx_b8e90a2c44ac3583 TO FK_B8E90A2C44AC3583');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE selling_order_item DROP FOREIGN KEY FK_8A64F2304584665A');
        $this->addSql('ALTER TABLE selling_order_item ADD CONSTRAINT IDX_8A64F2304584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
