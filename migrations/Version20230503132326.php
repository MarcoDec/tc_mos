<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230503132326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE supplier_administered_by (supplier_id INT UNSIGNED NOT NULL, company_id INT UNSIGNED NOT NULL, INDEX IDX_8124AD3C2ADD6D8C (supplier_id), INDEX IDX_8124AD3C979B1AD6 (company_id), PRIMARY KEY(supplier_id, company_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE supplier_administered_by ADD CONSTRAINT FK_8124AD3C2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE supplier_administered_by ADD CONSTRAINT FK_8124AD3C979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE supplier_company DROP FOREIGN KEY IDX_CEDA7D502ADD6D8C');
        $this->addSql('ALTER TABLE supplier_company DROP FOREIGN KEY IDX_CEDA7D50979B1AD6');
        $this->addSql('DROP TABLE supplier_company');
        $this->addSql('ALTER TABLE parameter CHANGE type type ENUM(\'ARRAY\', \'SELECT_MULTIPLE_LINK\', \'INTEGER\') NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE supplier_company (supplier_id INT UNSIGNED NOT NULL, company_id INT UNSIGNED NOT NULL, INDEX IDX_CEDA7D50979B1AD6 (company_id), INDEX IDX_CEDA7D502ADD6D8C (supplier_id), PRIMARY KEY(supplier_id, company_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE supplier_company ADD CONSTRAINT IDX_CEDA7D502ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE supplier_company ADD CONSTRAINT IDX_CEDA7D50979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE supplier_administered_by DROP FOREIGN KEY FK_8124AD3C2ADD6D8C');
        $this->addSql('ALTER TABLE supplier_administered_by DROP FOREIGN KEY FK_8124AD3C979B1AD6');
        $this->addSql('DROP TABLE supplier_administered_by');
        $this->addSql('ALTER TABLE parameter CHANGE type type VARCHAR(255) NOT NULL');
    }
}
