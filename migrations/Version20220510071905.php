<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Hr\Employee\Employee;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class Version20220510071905 extends AbstractMigration {
    private UserPasswordHasherInterface $hasher;

    public function __construct(Connection $connection, LoggerInterface $logger) {
        parent::__construct($connection, $logger);
    }

    public function down(Schema $schema): void {
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE employee');
    }

    public function getDescription(): string {
        return '';
    }

    public function setHasher(UserPasswordHasherInterface $hasher): void {
        $this->hasher = $hasher;
    }

    public function up(Schema $schema): void {
        ($user = (new Employee()))
            ->setPassword($this->hasher->hashPassword($user, 'super'))
            ->addRole(Roles::ROLE_ACCOUNTING_ADMIN)
            ->addRole(Roles::ROLE_HR_ADMIN)
            ->addRole(Roles::ROLE_IT_ADMIN)
            ->addRole(Roles::ROLE_LEVEL_DIRECTOR)
            ->addRole(Roles::ROLE_LOGISTICS_ADMIN)
            ->addRole(Roles::ROLE_MAINTENANCE_ADMIN)
            ->addRole(Roles::ROLE_MANAGEMENT_ADMIN)
            ->addRole(Roles::ROLE_PRODUCTION_ADMIN)
            ->addRole(Roles::ROLE_PROJECT_ADMIN)
            ->addRole(Roles::ROLE_PURCHASE_ADMIN)
            ->addRole(Roles::ROLE_QUALITY_ADMIN)
            ->addRole(Roles::ROLE_SELLING_ADMIN);
        $this->addSql(
            <<<'SQL'
CREATE TABLE employee (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    deleted TINYINT(1) DEFAULT 0 NOT NULL,
    emb_roles_roles TEXT CHARACTER SET ascii NOT NULL COMMENT '(DC2Type:simple_array)',
    name VARCHAR(30) NOT NULL,
    password CHAR(60) CHARACTER SET ascii NOT NULL,
    username VARCHAR(20) CHARACTER SET ascii NOT NULL
)
SQL
        );
        $this->addSql(sprintf(
            'INSERT INTO employee (name, password, username, emb_roles_roles) VALUES (\'Super\', %s, \'super\', %s)',
            /** @phpstan-ignore-next-line */
            $this->connection->quote($user->getPassword()),
            /** @phpstan-ignore-next-line */
            $this->connection->quote(implode(',', $user->getRoles()))
        ));
        $this->addSql(
            <<<'SQL'
CREATE TABLE token (
    id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    employee_id INT UNSIGNED NOT NULL,
    expire_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    token CHAR(120) CHARACTER SET ascii NOT NULL,
    CONSTRAINT token_employee_id_employee_id FOREIGN KEY (employee_id) REFERENCES employee (id)
)
SQL
        );
    }
}
