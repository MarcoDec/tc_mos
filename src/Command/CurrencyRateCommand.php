<?php

namespace App\Command;

use App\Attributes\CronJob;
use App\Doctrine\Repository\CurrencyRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @method static string getDefaultName()
 */
#[CronJob('@daily')]
final class CurrencyRateCommand extends Command {
    protected static $defaultDescription = 'CRON de mise à jour des taux de change des devises.';
    protected static $defaultName = 'gpao:currency:rate';

    public function __construct(private HttpClientInterface $client, private CurrencyRepository $currencyRepo) {
        parent::__construct();
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
