<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Collection;
use App\Entity\Management\Parameter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\UnicodeString;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230226104447 extends AbstractMigration
{
   /** @var Collection<int, string> */
   private readonly Collection $queries;

   public function __construct(Connection $connection, LoggerInterface $logger) {
      parent::__construct($connection, $logger);
      $this->queries = new Collection();
   }

    public function getDescription(): string
    {
        return 'Migration chargeant les paramètres ainsi que la structure des Attachments';
    }

   private static function trim(string $str): string {
      return (new UnicodeString($str))
         ->replaceMatches('/\s+/', ' ')
         ->replace('( ', '(')
         ->replace(' )', ')')
         ->toString();
   }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE employee_attachment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, employee_id INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, category VARCHAR(255) NOT NULL DEFAULT "doc", expiration_date DATE DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_E188696F8C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parameter (id INT UNSIGNED AUTO_INCREMENT NOT NULL, link INT UNSIGNED DEFAULT NULL, deleted TINYINT(1) DEFAULT 0 NOT NULL, name VARCHAR(255) NOT NULL, target VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, kind ENUM(\'ARRAY\', \'SELECT_MULTIPLE_LINK\', \'INTEGER\') NOT NULL, value VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_2A97911036AC99F1 (link), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employee_attachment ADD CONSTRAINT FK_E188696F8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE parameter ADD CONSTRAINT FK_2A97911036AC99F1 FOREIGN KEY (link) REFERENCES parameter (id)');

        $this->parameterUp();
        foreach ($this->queries as $query) {
           $this->addSql($query);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee_attachment DROP FOREIGN KEY FK_E188696F8C03F15C');
        $this->addSql('ALTER TABLE parameter DROP FOREIGN KEY FK_2A97911036AC99F1');
        $this->addSql('DROP TABLE employee_attachment');
        $this->addSql('DROP TABLE parameter');
    }

   private function addQuery(string $query): void {
      $this->queries->push(self::trim($query));
   }

   public function parameterUp() {
      $filename = __DIR__."/../migrations-data/parameter.json";
      $file = file_get_contents($filename);
      if (!$file) {
         throw new InvalidArgumentException("$filename not found.");
      }
      /** @var array<int, array<string, mixed>> $decoded */
      $decoded = json_decode($file, true);
      Collection::collect($decoded)
         ->map(function($parameter) {
         $currentParameter= (new Parameter())
            ->setValue($parameter['value'])
            ->setName($parameter['name'])
            ->setKind($parameter['type'])
            ->setDescription($parameter['description'])
            ->setTarget("")
         ;
         $this->generateParameter($currentParameter,$parameter['process']);
      });
   }
   /**
    * @param Parameter $parameter
    * @param string $process
    */
   private function generateParameter(Parameter $parameter, string $process): void {
      $deleted = 'FALSE';
      $name = $parameter->getName();
      $target = $parameter->getTarget();
      $type = $parameter->getKind();
      $value = $parameter->getValue();
      $description = $parameter->getDescription();
      $this->addQuery(sprintf(
         <<<SQL
INSERT INTO `parameter` (`deleted`, `name`, `target`, `kind`, `value`, `type`, `description` )
VALUES ($deleted, '$name', '$target', '$type', '$value', '$process',"$description")
SQL
      ));
   }
}
