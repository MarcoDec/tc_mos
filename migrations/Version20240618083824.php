<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240618083824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout relation entre delivery_note et selling_order';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery_note ADD selling_order_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE delivery_note ADD CONSTRAINT FK_1E21328E98F9022F FOREIGN KEY (selling_order_id) REFERENCES selling_order (id)');
        $this->addSql('CREATE INDEX IDX_1E21328E98F9022F ON delivery_note (selling_order_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery_note DROP FOREIGN KEY FK_1E21328E98F9022F');
        $this->addSql('DROP INDEX IDX_1E21328E98F9022F ON delivery_note');
        $this->addSql('ALTER TABLE delivery_note DROP selling_order_id');
    }
}
