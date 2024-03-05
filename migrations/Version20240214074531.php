<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214074531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Mise à jour des entités Employee et Skill pour ajout oldId et changement du champ type en kind dans l\'entité Skill';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee CHANGE old_id old_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE skill DROP FOREIGN KEY IDX_5E3DE477C54C8C93');
        $this->addSql('DROP INDEX IDX_5E3DE477C54C8C93 ON skill');
        $this->addSql('ALTER TABLE skill CHANGE type_id kind_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE skill ADD CONSTRAINT FK_5E3DE47730602CA9 FOREIGN KEY (kind_id) REFERENCES skill_type (id)');
        $this->addSql('CREATE INDEX IDX_5E3DE47730602CA9 ON skill (kind_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee CHANGE old_id old_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE skill DROP FOREIGN KEY FK_5E3DE47730602CA9');
        $this->addSql('DROP INDEX IDX_5E3DE47730602CA9 ON skill');
        $this->addSql('ALTER TABLE skill CHANGE kind_id type_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE skill ADD CONSTRAINT IDX_5E3DE477C54C8C93 FOREIGN KEY (type_id) REFERENCES skill_type (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5E3DE477C54C8C93 ON skill (type_id)');
    }
}
