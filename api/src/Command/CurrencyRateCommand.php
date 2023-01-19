<?php

declare(strict_types=1);

namespace App\Command;

use App\Attribute\CronJob;
use App\Repository\Management\Unit\CurrencyRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'gpao:currency:rate', description: 'CRON de mise à jour des taux de change des devises.'), CronJob('@daily')]
class CurrencyRateCommand extends Command {
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly CurrencyRepository $currencyRepo,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->currencyRepo->updateRates($this->getRates());
        return self::SUCCESS;
    }

    /** @return array{code: string, rate: float}[] */
    private function getRates(): array {
        return $this->client->request('GET', 'https://www.floatrates.com/daily/eur.json')->toArray();
    }
}
