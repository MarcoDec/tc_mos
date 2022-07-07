<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Embeddable\Hr\Employee\Roles;
use App\Entity\Hr\Employee\Employee;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Illuminate\Support\Collection;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class Version20220704090555 extends AbstractMigration {
    public function postUp(Schema $schema): void {
        $this->upPhoneNumbers('out_trainer', 'tel');
        $this->upPhoneNumbers('society', 'phone');

        $attributes = $this->connection->executeQuery('SELECT `id`, `attribut_id_family` FROM `attribute` WHERE `attribut_id_family` IS NOT NULL');
        /** @var Collection<int, string> $insert */
        $insert = new Collection();
        while ($attribute = $attributes->fetchAssociative()) {
            /** @var array{attribut_id_family:string, id: int} $attribute */
            $insert = $insert->merge(
                collect(explode('#', $attribute['attribut_id_family']))
                    ->map(static fn (string $id): string => "({$attribute['id']}, $id)")
            );
        }
        $this->connection->executeQuery("INSERT INTO `attribute_family` (`attribute_id`, `family_id`) VALUES {$insert->join(',')}");
        $this->connection->executeQuery('ALTER TABLE `attribute` DROP `attribut_id_family`');
        $this->postUpComponentAttributes();
    }

    public function up(Schema $schema): void {
        // rank 3
        $this->upComponentAttributes();
        // old_id
        $this->addSql('ALTER TABLE `attribute` DROP `old_id`');
        $this->addSql('ALTER TABLE `component` DROP `old_id`');
        $this->addSql('ALTER TABLE `component_family` DROP `old_subfamily_id`');
        $this->addSql('ALTER TABLE `invoice_time_due` DROP `old_id`');
        $this->addSql('ALTER TABLE `product` DROP `old_id`');
    }

    private function alterTable(string $table, string $comment): void {
        /** @phpstan-ignore-next-line */
        $this->addSql("ALTER TABLE `$table` COMMENT {$this->connection->quote($comment)}");
        $this->addSql("ALTER TABLE `$table` DEFAULT CHARACTER SET utf8mb4");
        $this->addSql("ALTER TABLE `$table` CHARACTER SET utf8mb4");
        $this->addSql("ALTER TABLE `$table` DEFAULT COLLATE `utf8mb4_unicode_ci`");
        $this->addSql("ALTER TABLE `$table` COLLATE `utf8mb4_unicode_ci`");
        $this->addSql("ALTER TABLE `$table` ENGINE = InnoDB");
    }

    private function postUpComponentAttributes(): void {
        /** @var Collection<int, array{attribute: int, family: int, unit: string}> $definitions */
        $definitions = collect($this->connection->executeQuery(<<<'SQL'
SELECT `a`.`id` as `attribute`, `f`.`id` as `family`, `u`.`code` as `unit`
FROM `attribute` `a`
LEFT JOIN `unit` `u` ON `a`.`unit_id` = `u`.`id`
INNER JOIN `attribute_family` `af` ON `a`.`id` = `af`.`attribute_id`
INNER JOIN `component_family` `f` ON `af`.`family_id` = `f`.`id`
SQL)->fetchAllAssociative());
        /** @var Collection<int, int> $attributes */
        $attributes = new Collection();
        foreach ($definitions as $definition) {
            $attributes->push($definition['attribute']);
        }
        $attributes = $attributes->unique()->sort();
        if ($attributes->isEmpty()) {
            $this->connection->executeQuery('DELETE FROM `component_attribute`');
        } else {
            $this->connection->executeQuery(
                sql: 'DELETE FROM `component_attribute` WHERE `attribute_id` NOT IN (:attributes)',
                params: ['attributes' => $attributes->all()],
                types: ['attributes' => Connection::PARAM_INT_ARRAY]
            );
        }
        /** @var Collection<int, Collection<int, array{attribute: int, family: int, unit: string}>> $families */
        $families = $definitions->mapToGroups(static fn (array $definition): array => [$definition['family'] => $definition]);
        /** @var array{id: int, family: int, parent: int}[] $components */
        $components = $this->connection->executeQuery(<<<'SQL'
SELECT `c`.`id`, `c`.`family_id` as `family`, `f`.`parent_id` as `parent`
FROM `component` `c`
INNER JOIN `component_family` `f` ON `c`.`family_id` = `f`.`id`
SQL)->fetchAllAssociative();
        foreach ($components as $component) {
            $family = $families->get($component['family']) ?? $families->get($component['parent']);
            if (empty($family)) {
                $this->connection->executeQuery(
                    sql: 'DELETE FROM `component_attribute` WHERE `component_id` = :component',
                    params: ['component' => $component['id']]
                );
            } else {
                /** @var Collection<int, array{attribute_id: int, id: int}> $componentAttributes */
                $componentAttributes = collect($this->connection->executeQuery(
                    sql: 'SELECT `id`, `attribute_id` FROM `component_attribute` WHERE `component_id` = :component',
                    params: ['component' => $component['id']]
                )->fetchAllAssociative());
                /** @var Collection<int, int> $familyAttributes */
                $familyAttributes = new Collection();
                foreach ($family as $attribute) {
                    $compAttr = $componentAttributes->first(static fn (array $compAttr): bool => $compAttr['attribute_id'] === $attribute['attribute']);
                    if (empty($compAttr)) {
                        $this->connection->executeQuery(
                            sql: 'INSERT INTO `component_attribute` (`component_id`, `attribute_id`, `measure_code`) VALUES (:component, :attribute, :unit)',
                            params: ['attribute' => $attribute['attribute'], 'component' => $component['id'], 'unit' => $attribute['unit']]
                        );
                    } else {
                        $this->connection->executeQuery(
                            sql: 'UPDATE `component_attribute` SET `measure_code` = :unit WHERE `id` = :id',
                            params: ['id' => $compAttr['id'], 'unit' => $attribute['unit']]
                        );
                    }
                    $familyAttributes->push($attribute['attribute']);
                }
                $familyAttributes = $familyAttributes->unique()->sort();
                if ($familyAttributes->isEmpty()) {
                    $this->connection->executeQuery(
                        sql: 'DELETE FROM `component_attribute` WHERE `component_id` = :component',
                        params: ['component' => $component['id']]
                    );
                } else {
                    $this->connection->executeQuery(
                        sql: 'DELETE FROM `component_attribute` WHERE `attribute_id` NOT IN (:attributes) AND `component_id` = :component',
                        params: ['attributes' => $familyAttributes->all(), 'component' => $component['id']],
                        types: ['attributes' => Connection::PARAM_INT_ARRAY]
                    );
                }
            }
        }
        $this->connection->executeQuery(<<<'SQL'
CREATE TABLE `component_attribute_copy` (
  `id` int UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `component_id` int UNSIGNED NOT NULL,
  `attribute_id` int UNSIGNED NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_id` int UNSIGNED DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `measure_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `measure_denominator` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `measure_value` double NOT NULL DEFAULT '0',
  UNIQUE KEY `UNIQ_248373AAB6E62EFAE2ABAFFF` (`attribute_id`, `component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Caractéristique d''un composant';
SQL);
        $this->connection->executeQuery(<<<'SQL'
INSERT INTO `component_attribute_copy` (
    `component_id`,
    `attribute_id`,
    `value`,
    `color_id`,
    `deleted`,
    `measure_code`,
    `measure_denominator`,
    `measure_value`
) SELECT
    `component_id`,
    `attribute_id`,
    `value`,
    `color_id`,
    `deleted`,
    `measure_code`,
    `measure_denominator`,
    `measure_value`
FROM `component_attribute`
SQL);
        $this->connection->executeQuery('DROP TABLE `component_attribute`');
        $this->connection->executeQuery('RENAME TABLE `component_attribute_copy` TO `component_attribute`');
        $this->connection->executeQuery(<<<'SQL'
ALTER TABLE `component_attribute`
  ADD CONSTRAINT `IDX_248373AA7ADA1FB5` FOREIGN KEY (`color_id`) REFERENCES `color` (`id`),
  ADD CONSTRAINT `IDX_248373AAB6E62EFA` FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`id`),
  ADD CONSTRAINT `IDX_248373AAE2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`);
SQL);
    }

    private function upComponentAttributes(): void {
        $this->addSql('RENAME TABLE `component_attribut` TO `component_attribute`');
        $this->alterTable('component_attribute', 'Caractéristique d\'un composant');
        $this->addSql(<<<'SQL'
ALTER TABLE `component_attribute`
    ADD `color_id` INT UNSIGNED DEFAULT NULL,
    ADD `deleted` TINYINT(1) DEFAULT 0 NOT NULL,
    ADD `id` INT UNSIGNED AUTO_INCREMENT NOT NULL,
    ADD `measure_code` VARCHAR(6) DEFAULT NULL,
    ADD `measure_denominator` VARCHAR(6) DEFAULT NULL,
    ADD `measure_value` DOUBLE PRECISION DEFAULT '0' NOT NULL,
    CHANGE `id_attribut` `attribute_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `id_component` `component_id` INT UNSIGNED DEFAULT NULL,
    CHANGE `valeur_attribut` `value` VARCHAR(255) DEFAULT NULL,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`id`)
SQL);
        $this->addSql(<<<'SQL'
UPDATE `component_attribute` SET
`component_attribute`.`attribute_id` = (SELECT `attribute`.`id` FROM `attribute` WHERE `attribute`.`old_id` = `component_attribute`.`attribute_id`),
`component_attribute`.`component_id` = (SELECT `component`.`id` FROM `component` WHERE `component`.`old_id` = `component_attribute`.`component_id`)
SQL);
        $this->addSql('DELETE FROM `component_attribute` WHERE `attribute_id` IS NULL OR `component_id` IS NULL OR `value` IS NULL');
        $this->addSql(<<<'SQL'
ALTER TABLE `component_attribute`
    CHANGE `attribute_id` `attribute_id` INT UNSIGNED NOT NULL,
    CHANGE `component_id` `component_id` INT UNSIGNED NOT NULL,
    ADD CONSTRAINT `IDX_248373AAB6E62EFA` FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`id`),
    ADD CONSTRAINT `IDX_248373AA7ADA1FB5` FOREIGN KEY (`color_id`) REFERENCES `color` (`id`),
    ADD CONSTRAINT `IDX_248373AAE2ABAFFF` FOREIGN KEY (`component_id`) REFERENCES `component` (`id`)
SQL);
    }

    private function upPhoneNumbers(string $table, string $phoneProp): void {
        $items = $this->connection->executeQuery(<<<SQL
SELECT `id`, `$phoneProp`, `address_country`
FROM `$table`
WHERE `$phoneProp` IS NOT NULL
SQL);
        $util = PhoneNumberUtil::getInstance();
        while ($item = $items->fetchAssociative()) {
            /** @var string[] $item */
            $phone = null;
            try {
                $phone = $util->parse($item[$phoneProp], $item['address_country']);
            } catch (NumberParseException) {
            }
            $this->connection->prepare("UPDATE `$table` SET `$phoneProp` = :phone WHERE `id` = :id")
                ->executeQuery([
                    'id' => $item['id'],
                    'phone' => !empty($phone) && $util->isValidNumber($phone)
                        ? $util->format($phone, PhoneNumberFormat::INTERNATIONAL)
                        : null
                ]);
        }
        $this->connection->executeQuery("ALTER TABLE `$table` CHANGE `$phoneProp` `address_phone_number` VARCHAR(18) DEFAULT NULL");
    }
}
