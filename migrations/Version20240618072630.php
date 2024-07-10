<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240618072630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renommage du champ order_id en selling_order_id dans la table manufacturing_order';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE manufacturing_order DROP FOREIGN KEY IDX_34010DB18D9F6D38');
        $this->addSql('DROP INDEX IDX_34010DB18D9F6D38 ON manufacturing_order');
        $this->addSql('ALTER TABLE manufacturing_order CHANGE order_id selling_order_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE manufacturing_order ADD CONSTRAINT FK_34010DB198F9022F FOREIGN KEY (selling_order_id) REFERENCES selling_order (id)');
        $this->addSql('CREATE INDEX IDX_34010DB198F9022F ON manufacturing_order (selling_order_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE manufacturing_order DROP FOREIGN KEY FK_34010DB198F9022F');
        $this->addSql('DROP INDEX IDX_34010DB198F9022F ON manufacturing_order');
        $this->addSql('ALTER TABLE manufacturing_order CHANGE selling_order_id order_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE manufacturing_order ADD CONSTRAINT IDX_34010DB18D9F6D38 FOREIGN KEY (order_id) REFERENCES selling_order (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_34010DB18D9F6D38 ON manufacturing_order (order_id)');
    }
}
