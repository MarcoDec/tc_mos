<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231128081221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE preparation ADD emb_blocker_state ENUM(\'blocked\', \'disabled\', \'enabled\') DEFAULT \'enabled\' NOT NULL COMMENT \'(DC2Type:blocker_state)\', ADD emb_state_state ENUM(\'asked\', \'agreed\', \'delivered\', \'rejected\') DEFAULT \'asked\' NOT NULL COMMENT \'(DC2Type:preparation_state)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE preparation DROP emb_blocker_state, DROP emb_state_state');
    }
}
