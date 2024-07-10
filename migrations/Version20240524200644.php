<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524200644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des champs prix total de vente et quantité expédiée dans selling_order_item';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE selling_order_item ADD sent_quantity_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD sent_quantity_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD sent_quantity_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, ADD total_item_price_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_item_price_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_item_price_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE selling_order_item DROP sent_quantity_code, DROP sent_quantity_denominator, DROP sent_quantity_value, DROP total_item_price_code, DROP total_item_price_denominator, DROP total_item_price_value');
    }
}
