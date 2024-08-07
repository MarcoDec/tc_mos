<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240709142739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du champ ref dans la table customer_component_price';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer_component_price ADD ref VARCHAR(255) DEFAULT NULL');}

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE customer_component_price DROP ref');
    }
}
