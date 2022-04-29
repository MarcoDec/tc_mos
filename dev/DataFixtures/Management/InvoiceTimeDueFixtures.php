<?php

namespace App\DataFixtures\Management;

use App\Entity\Management\InvoiceTimeDue;
use App\Repository\Management\InvoiceTimeDueRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class InvoiceTimeDueFixtures extends Fixture {
    public function load(ObjectManager $manager): void {
        /** @var InvoiceTimeDueRepository $repo */
        $repo = $manager->getRepository(InvoiceTimeDue::class);
        $repo->unify();
    }
}
