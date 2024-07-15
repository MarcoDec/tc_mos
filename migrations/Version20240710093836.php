<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240710093836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adaptation de la table customer_product_price pour la gestion des référence de prix';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer_product_price ADD ref VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer_product_price DROP ref');
    }
}
