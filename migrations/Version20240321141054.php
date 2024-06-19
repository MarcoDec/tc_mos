<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240321141054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Définition des catégories des documents par défault pour clocking et purchase_order_attachment,
        ajout des champs supplier_old_id et order_supplier_old_id dans edi, 
        modification de la description du type de request_date dans preparation';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clocking CHANGE category category VARCHAR(255) DEFAULT \'doc\' NOT NULL');
        $this->addSql('ALTER TABLE edi ADD supplier_old_id INT DEFAULT NULL, ADD order_supplier_old_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE preparation CHANGE request_date request_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE purchase_order_attachment CHANGE category category VARCHAR(255) DEFAULT \'doc\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clocking CHANGE category category VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE edi DROP supplier_old_id, DROP order_supplier_old_id');
        $this->addSql('ALTER TABLE preparation CHANGE request_date request_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE purchase_order_attachment CHANGE category category VARCHAR(255) NOT NULL');
    }
}
