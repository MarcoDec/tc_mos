<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240618143116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du champ selling_order_id dans la table bill';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bill ADD selling_order_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE bill ADD CONSTRAINT FK_7A2119E398F9022F FOREIGN KEY (selling_order_id) REFERENCES selling_order (id)');
        $this->addSql('CREATE INDEX IDX_7A2119E398F9022F ON bill (selling_order_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bill DROP FOREIGN KEY FK_7A2119E398F9022F');
        $this->addSql('DROP INDEX IDX_7A2119E398F9022F ON bill');
        $this->addSql('ALTER TABLE bill DROP selling_order_id');
    }
}
