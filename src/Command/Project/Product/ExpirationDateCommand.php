<?php

namespace App\Command\Project\Product;

use App\Attributes\CronJob;
use App\Repository\Project\Product\ProductRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method static string getDefaultName()
 */
#[CronJob('@daily')]
final class ExpirationDateCommand extends AsCommand {
    protected static $defaultDescription = 'CRON de désactivation des produits expirés.';
    protected static $defaultName = 'gpao:expiration:date';

    public function __construct(private readonly ProductRepository $repository) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $this->repository->expires();
        return self::SUCCESS;
    }
}
