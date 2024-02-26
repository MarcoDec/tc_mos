<?php

namespace App\Command;

use App\Attributes\CronJob;
use App\Repository\Project\Product\ProductRepository;
use App\Repository\Purchase\Component\ComponentRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** @method static string getDefaultName() */
#[AsCommand(name: 'gpao:expiration:date', description: 'CRON de désactivation des produits expirés.'), CronJob('@daily')]
final class ExpirationDateCommand extends Command {
    public function __construct(
        private readonly ComponentRepository $componentRepo,
        private readonly ProductRepository $productRepo
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->componentRepo->expires();
        $this->productRepo->expires();
        return self::SUCCESS;
    }
}
