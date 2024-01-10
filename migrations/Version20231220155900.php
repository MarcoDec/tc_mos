<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231220155900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'SinglePrinterMobileUnit ajout champ booléen localPrint pour signifier une impression locale';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE single_printer_mobile_unit ADD local_print TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE single_printer_mobile_unit DROP local_print');
    }
}
