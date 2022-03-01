<?php

namespace App\DataFixtures\Management;

use App\Doctrine\Entity\Management\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Intl\Currencies;

final class CurrencyFixtures extends Fixture {
    private const ACTIVE_CURRENCIES = ['CHF', 'EUR', 'MDL', 'RUB', 'USD', 'VND'];

    public function load(ObjectManager $manager): void {
        foreach (Currencies::getCurrencyCodes() as $code) {
            $manager->persist(
                (new Currency())
                    ->setActive(in_array($code, self::ACTIVE_CURRENCIES))
                    ->setCode($code)
            );
        }
        $manager->flush();
    }
}
