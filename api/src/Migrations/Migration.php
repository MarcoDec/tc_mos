<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\UnicodeString;

abstract class Migration extends AbstractMigration {
    /** @var array<int, string> */
    private const EMPTY = [];

    /** @var Collection<int, string> */
    private Collection $queries;

    public function __construct(Connection $connection, LoggerInterface $logger) {
        parent::__construct($connection, $logger);
        $this->queries = new Collection(self::EMPTY);
    }

    private static function getVersion(): string {
        $matches = [];
        preg_match('/Version\d+/', static::class, $matches);
        return $matches[0];
    }

    abstract protected function defineQueries(): void;

    public function up(Schema $schema): void {
        $this->defineQueries();
        $procedure = self::getVersion();
        $this->addSql("CREATE PROCEDURE $procedure() BEGIN {$this->getQueries()}; END");
        $this->addSql("CALL $procedure");
        $this->addSql("DROP PROCEDURE $procedure");
    }

    protected function push(string $query): void {
        $this->queries = $this->queries->push($query);
    }

    private function getQueries(): string {
        return (new UnicodeString($this->queries->implode(';')))
            ->replaceMatches('/\s+/', ' ')
            ->replace('( ', '(')
            ->replace(' )', ')')
            ->toString();
    }
}
