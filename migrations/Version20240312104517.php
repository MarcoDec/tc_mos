<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312104517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add received_quantity_code, received_quantity_denominator, received_quantity_value to purchase_order_item';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE purchase_order_item ADD received_quantity_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD received_quantity_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD received_quantity_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE purchase_order_item DROP received_quantity_code, DROP received_quantity_denominator, DROP received_quantity_value');
    }
}
