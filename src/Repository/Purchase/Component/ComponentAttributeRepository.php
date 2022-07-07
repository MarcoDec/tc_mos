<?php

namespace App\Repository\Purchase\Component;

use App\Entity\Purchase\Component\ComponentAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ComponentAttribute>
 *
 * @method ComponentAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComponentAttribute|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method ComponentAttribute[]    findAll()
 * @method ComponentAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ComponentAttributeRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ComponentAttribute::class);
    }

    public function links(): void {
        $this->_em->getConnection()->executeQuery(<<<'SQL'
INSERT INTO `component_attribute` (`component_id`, `attribute_id`, `measure_code`)
SELECT `c`.`id`, `a`.`id`, `u`.`code`
FROM `component` `c`
INNER JOIN `component_family` `f` ON `c`.`family_id` = `f`.`id`
INNER JOIN `attribute_family` `af` ON `f`.`parent_id` = `af`.`family_id`
INNER JOIN `attribute` `a` ON `af`.`attribute_id` = `a`.`id`
LEFT JOIN `unit` `u` ON `a`.`unit_id` = `u`.`id`
ON DUPLICATE KEY UPDATE `deleted` = 0, `measure_code` = `u`.`code`
SQL);
        $this->_em->getConnection()->executeQuery(<<<'SQL'
UPDATE `component_attribute` `ca`
LEFT JOIN `component` `c` ON `ca`.`component_id` = `c`.`id`
LEFT JOIN `component_family` `f` ON `c`.`family_id` = `f`.`id`
LEFT JOIN `attribute` `a` ON `ca`.`attribute_id` = `a`.`id`
LEFT JOIN `attribute_family` `af` ON `a`.`id` = `af`.`attribute_id` AND `f`.`parent_id` = `af`.`family_id`
SET `ca`.`deleted` = 1
WHERE `af`.`attribute_id` IS NULL OR `af`.`family_id` IS NULL
SQL);
    }
}
