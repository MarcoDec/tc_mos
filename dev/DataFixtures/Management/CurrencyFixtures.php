<?php

namespace App\DataFixtures\Management;

use App\Entity\Management\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Intl\Currencies;

final class CurrencyFixtures extends Fixture {
    private const ACTIVE_CURRENCIES = ['CHF', 'MDL', 'RUB', 'TND', 'USD', 'VND'];

    public function load(ObjectManager $manager): void {
        $manager->persist($parent = (new Currency())->setActive(true)->setCode('EUR'));
        foreach (Currencies::getCurrencyCodes() as $code) {
            $manager->persist(
                (new Currency())
                    ->setActive(in_array($code, self::ACTIVE_CURRENCIES))
                    ->setCode($code)
                    ->setParent($parent)
            );
        }
        $manager->flush();
    }
}
