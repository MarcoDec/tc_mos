<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521122754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la colonne is_forecast dans la table selling_order_item';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE selling_order_item ADD is_forecast TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE selling_order_item DROP is_forecast');
    }
}
