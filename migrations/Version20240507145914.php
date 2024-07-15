<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507145914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des champs EDI sur la table customer';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer ADD edi_kind VARCHAR(255) DEFAULT NULL, ADD edi_orders_maturity VARCHAR(255) DEFAULT NULL, ADD edi_order_type VARCHAR(255) DEFAULT NULL, ADD is_edi_asn TINYINT(1) DEFAULT 0 NOT NULL, ADD is_edi_orders TINYINT(1) DEFAULT 0 NOT NULL, ADD web_edi_url VARCHAR(255) DEFAULT NULL, ADD web_edi_infos TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer DROP edi_kind, DROP edi_orders_maturity, DROP edi_order_type, DROP is_edi_asn, DROP is_edi_orders, DROP web_edi_url, DROP web_edi_infos');
    }
}
