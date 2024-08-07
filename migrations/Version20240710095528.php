<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240710095528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adaptation des entités supplier_product et product';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE supplier_product ADD administered_by_id INT UNSIGNED DEFAULT NULL, ADD kind ENUM(\'EI\', \'Prototype\', \'Série\', \'Pièce de rechange\') DEFAULT \'Série\' NOT NULL COMMENT \'(DC2Type:product_kind)\'');
        $this->addSql('ALTER TABLE supplier_product ADD CONSTRAINT FK_522F70B22753AB70 FOREIGN KEY (administered_by_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_522F70B22753AB70 ON supplier_product (administered_by_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE supplier_product DROP FOREIGN KEY FK_522F70B22753AB70');
        $this->addSql('DROP INDEX IDX_522F70B22753AB70 ON supplier_product');
        $this->addSql('ALTER TABLE supplier_product DROP administered_by_id, DROP kind');
    }
}
