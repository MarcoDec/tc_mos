<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Hr\Employee\Employee;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class Version20221103170649 extends AbstractMigration {
    private UserPasswordHasherInterface $hasher;

    public function setHasher(UserPasswordHasherInterface $hasher): void {
        $this->hasher = $hasher;
    }

    public function up(Schema $schema): void {
        $this->addSql(<<<'SQL'
CREATE TABLE `employee` (
    `id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `deleted` BOOLEAN DEFAULT 0 NOT NULL,
    `password` CHAR(60) DEFAULT NULL COMMENT '(DC2Type:char)',
    `username` VARCHAR(20) DEFAULT NULL
)
SQL);
        ($user = new Employee())
            ->setUsername('super')
            ->setPassword($this->hasher->hashPassword($user, 'super'));
        $this->addSql(sprintf(
            'INSERT INTO `employee` (`password`, `username`) VALUES (%s, %s)',
            $this->platform->quoteStringLiteral((string) $user->getPassword()),
            $this->platform->quoteStringLiteral((string) $user->getUsername())
        ));
    }
}
