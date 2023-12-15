<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231012143021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la table des équivalents de composants';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE component_equivalent (id INT UNSIGNED AUTO_INCREMENT NOT NULL, family_id INT UNSIGNED NOT NULL, unit_id INT UNSIGNED NOT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, INDEX IDX_3F637CB5C35E566A (family_id), INDEX IDX_3F637CB5F8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE component_equivalent ADD CONSTRAINT FK_3F637CB5C35E566A FOREIGN KEY (family_id) REFERENCES component_family (id)');
        $this->addSql('ALTER TABLE component_equivalent ADD CONSTRAINT FK_3F637CB5F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind ENUM(\'ARRAY\', \'SELECT_MULTIPLE_LINK\', \'INTEGER\') NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE component_equivalent DROP FOREIGN KEY FK_3F637CB5C35E566A');
        $this->addSql('ALTER TABLE component_equivalent DROP FOREIGN KEY FK_3F637CB5F8BD700D');
        $this->addSql('DROP TABLE component_equivalent');
        $this->addSql('ALTER TABLE parameter CHANGE kind kind VARCHAR(255) NOT NULL');
    }
}
