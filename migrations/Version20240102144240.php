<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240102144240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modifie le type de colonne timeCard de string en integer et ajoute/supprime la fonction get_next_timecard et Ajoute et supprime une fonction pour obtenir le dernier timeCard + 1 dans la table Employee';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE employee MODIFY time_card INT;');
        $this->addSql("
            CREATE FUNCTION get_next_timecard()
            RETURNS INTEGER
            DETERMINISTIC
            BEGIN
                DECLARE next_timeCard INTEGER;
                SELECT COALESCE(MAX(time_card), 0) + 1 INTO next_timeCard FROM employee;
                RETURN next_timeCard;
            END;
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP FUNCTION IF EXISTS get_next_timecard;');
        $this->addSql('ALTER TABLE employee MODIFY time_card VARCHAR(255);');
    }
}
