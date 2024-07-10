<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240515100624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des relations entre les items et les commandes parentes pour fix mot clé réservé order pour les tris par API platform';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE purchase_order_item DROP FOREIGN KEY IDX_5ED948C38D9F6D38');
        $this->addSql('DROP INDEX IDX_5ED948C38D9F6D38 ON purchase_order_item');
        $this->addSql('ALTER TABLE purchase_order_item CHANGE order_id parent_order_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_order_item ADD CONSTRAINT FK_5ED948C31252C1E9 FOREIGN KEY (parent_order_id) REFERENCES purchase_order (id)');
        $this->addSql('CREATE INDEX IDX_5ED948C31252C1E9 ON purchase_order_item (parent_order_id)');
        $this->addSql('ALTER TABLE selling_order_item DROP FOREIGN KEY IDX_8A64F2308D9F6D38');
        $this->addSql('DROP INDEX IDX_8A64F2308D9F6D38 ON selling_order_item');
        $this->addSql('ALTER TABLE selling_order_item CHANGE order_id parent_order_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE selling_order_item ADD CONSTRAINT FK_8A64F2301252C1E9 FOREIGN KEY (parent_order_id) REFERENCES selling_order (id)');
        $this->addSql('CREATE INDEX IDX_8A64F2301252C1E9 ON selling_order_item (parent_order_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE purchase_order_item DROP FOREIGN KEY FK_5ED948C31252C1E9');
        $this->addSql('DROP INDEX IDX_5ED948C31252C1E9 ON purchase_order_item');
        $this->addSql('ALTER TABLE purchase_order_item CHANGE parent_order_id order_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_order_item ADD CONSTRAINT IDX_5ED948C38D9F6D38 FOREIGN KEY (order_id) REFERENCES purchase_order (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5ED948C38D9F6D38 ON purchase_order_item (order_id)');
        $this->addSql('ALTER TABLE selling_order_item DROP FOREIGN KEY FK_8A64F2301252C1E9');
        $this->addSql('DROP INDEX IDX_8A64F2301252C1E9 ON selling_order_item');
        $this->addSql('ALTER TABLE selling_order_item CHANGE parent_order_id order_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE selling_order_item ADD CONSTRAINT IDX_8A64F2308D9F6D38 FOREIGN KEY (order_id) REFERENCES selling_order (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8A64F2308D9F6D38 ON selling_order_item (order_id)');
    }
}
