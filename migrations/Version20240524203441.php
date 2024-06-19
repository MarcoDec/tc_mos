<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524203441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des champs prix total de vente dans selling_order';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE selling_order ADD total_fixed_price_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_fixed_price_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_fixed_price_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, ADD total_forecast_price_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_forecast_price_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_forecast_price_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE selling_order DROP total_fixed_price_code, DROP total_fixed_price_denominator, DROP total_fixed_price_value, DROP total_forecast_price_code, DROP total_forecast_price_denominator, DROP total_forecast_price_value');
    }
}
