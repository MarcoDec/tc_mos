<?php

namespace App\Command;

use App\Attributes\CronJob;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CronCommand extends AbstractCommand {
    public function __construct() {
        parent::__construct('gpao:cron');
    }

    protected function configure(): void {
        $this->setDescription('Lance les CRON.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        foreach ($this->getCommands() as $command) {
            $refl = new ReflectionClass($command);
            $attributes = $refl->getAttributes(CronJob::class);
            if (empty($attributes)) {
                continue;
            }

            if (count($attributes) > 1) {
                throw new LogicException('CronJob attributes use more than one.');
            }

            $command->run(new ArrayInput(['command' => $command->getName()]), $output);
        }
        return self::SUCCESS;
    }

    /**
     * @return Command[]
     */
    private function getCommands(): array {
        return $this->getApplication()->all('gpao');
    }
}
