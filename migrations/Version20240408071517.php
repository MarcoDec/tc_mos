<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240408071517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la colonne contact_id et order_family dans la table selling_order';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE selling_order ADD contact_id INT UNSIGNED DEFAULT NULL, ADD order_family VARCHAR(255) DEFAULT \'fixed\' NOT NULL');
        $this->addSql('ALTER TABLE selling_order ADD CONSTRAINT FK_9CCD846BE7A1254A FOREIGN KEY (contact_id) REFERENCES customer_contact (id)');
        $this->addSql('CREATE INDEX IDX_9CCD846BE7A1254A ON selling_order (contact_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE selling_order DROP FOREIGN KEY FK_9CCD846BE7A1254A');
        $this->addSql('DROP INDEX IDX_9CCD846BE7A1254A ON selling_order');
        $this->addSql('ALTER TABLE selling_order DROP contact_id, DROP order_family');
    }
}
