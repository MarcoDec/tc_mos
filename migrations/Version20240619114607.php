<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240619114607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adaptation des tables purchase_order et purchase_order_item pour les besoins de la gestion des commandes d\'achat.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE purchase_order DROP FOREIGN KEY IDX_21E210B28D9F6D38');
        $this->addSql('DROP INDEX IDX_21E210B28D9F6D38 ON purchase_order');
        $this->addSql('ALTER TABLE purchase_order ADD selling_order_id INT UNSIGNED DEFAULT NULL, ADD is_open_order TINYINT(1) DEFAULT 1 NOT NULL, ADD kind ENUM(\'EI\', \'Prototype\', \'Série\', \'Pièce de rechange\') DEFAULT \'Prototype\' NOT NULL COMMENT \'(DC2Type:product_kind)\', ADD order_family VARCHAR(255) DEFAULT \'fixed\' NOT NULL, ADD total_fixed_price_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_fixed_price_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_fixed_price_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, ADD total_forecast_price_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_forecast_price_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_forecast_price_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE order_id purchase_company_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_order ADD CONSTRAINT FK_21E210B29BA84D1C FOREIGN KEY (purchase_company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE purchase_order ADD CONSTRAINT FK_21E210B298F9022F FOREIGN KEY (selling_order_id) REFERENCES selling_order (id)');
        $this->addSql('CREATE INDEX IDX_21E210B29BA84D1C ON purchase_order (purchase_company_id)');
        $this->addSql('CREATE INDEX IDX_21E210B298F9022F ON purchase_order (selling_order_id)');
        $this->addSql('ALTER TABLE purchase_order_item ADD ar_received TINYINT(1) DEFAULT 0 NOT NULL, ADD is_forecast TINYINT(1) DEFAULT 0 NOT NULL, ADD total_item_price_code VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_item_price_denominator VARCHAR(6) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_bin`, ADD total_item_price_value DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE purchase_order DROP FOREIGN KEY FK_21E210B29BA84D1C');
        $this->addSql('ALTER TABLE purchase_order DROP FOREIGN KEY FK_21E210B298F9022F');
        $this->addSql('DROP INDEX IDX_21E210B29BA84D1C ON purchase_order');
        $this->addSql('DROP INDEX IDX_21E210B298F9022F ON purchase_order');
        $this->addSql('ALTER TABLE purchase_order ADD order_id INT UNSIGNED DEFAULT NULL, DROP purchase_company_id, DROP selling_order_id, DROP is_open_order, DROP kind, DROP order_family, DROP total_fixed_price_code, DROP total_fixed_price_denominator, DROP total_fixed_price_value, DROP total_forecast_price_code, DROP total_forecast_price_denominator, DROP total_forecast_price_value');
        $this->addSql('ALTER TABLE purchase_order ADD CONSTRAINT IDX_21E210B28D9F6D38 FOREIGN KEY (order_id) REFERENCES selling_order (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_21E210B28D9F6D38 ON purchase_order (order_id)');
        $this->addSql('ALTER TABLE purchase_order_item DROP ar_received, DROP is_forecast, DROP total_item_price_code, DROP total_item_price_denominator, DROP total_item_price_value');
    }
}
