<?php

namespace App\Command;

use App\Attributes\CronJob;
use App\Repository\CurrencyRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[CronJob('@daily')]
final class CurrencyRateCommand extends Command {
    public function __construct(private HttpClientInterface $client, private CurrencyRepository $currencyRepo) {
        parent::__construct('gpao:currency:rate');
    }

    protected function configure(): void {
        $this->setDescription('CRON de mise à jour des taux de change des devises.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->currencyRepo->updateRates($this->getRates());
        return self::SUCCESS;
    }

    /**
     * @return array{code: string, rate: float}[]
     */
    private function getRates(): array {
        return $this->client->request('GET', 'https://www.floatrates.com/daily/eur.json')->toArray();
    }
}
