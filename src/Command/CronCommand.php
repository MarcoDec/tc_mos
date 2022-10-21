<?php

namespace App\Command;

use App\Attributes\CronJob as CronJobAttribute;
use App\Collection;
use App\Entity\CronJob;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LazyCommand;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method static string getDefaultName()
 */
final class CronCommand extends AbstractCommand {
    final public const OPTION_SCAN = 'scan';

    protected static $defaultDescription = 'Lance les CRON.';
    protected static $defaultName = 'gpao:cron';

    public function __construct(private readonly EntityManagerInterface $em) {
        parent::__construct();
    }

    protected function configure(): void {
        $this->addOption(self::OPTION_SCAN, null, InputOption::VALUE_NONE, 'Analyse les commandes et créer les CRON en base de données.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        if (!$input->getOption(self::OPTION_SCAN)) {
            $this->runJobs($output);
        } else {
            $this->scan();
        }
        $this->em->flush();
        return self::SUCCESS;
    }

    /**
     * @return CronJob[]
     */
    private function getEntities(): array {
        return $this->em->getRepository(CronJob::class)->findAll();
    }

    /**
     * @return array<string, array{command: Command, cron: CronJobAttribute}>
     */
    private function getJobs(): array {
        return Collection::collect($this->getApplication()->all('gpao'))
            ->mapWithKeys(static function (Command $command): array {
                if (empty($name = $command->getName())) {
                    throw new LogicException('Undefined command name.');
                }

                $refl = new ReflectionClass($command instanceof LazyCommand ? $command->getCommand() : $command);
                $attributes = $refl->getAttributes(CronJobAttribute::class);
                if (empty($attributes)) {
                    return [];
                }

                if (count($attributes) > 1) {
                    throw new LogicException('CronJob attributes use more than one.');
                }

                return [$name => [
                    'command' => $command,
                    'cron' => $attributes[0]->newInstance()
                ]];
            })
            ->all();
    }

    private function runJobs(OutputInterface $output): void {
        $entities = $this->getEntities();
        foreach ($this->getJobs() as $name => $job) {
            $entity = $entities[$name];
            if ($entity->isDue()) {
                /** @var Command $command */
                $command = $job['command'];
                $command->run(new ArrayInput(['command' => $name]), $output);
                $entity->run();
            }
        }
    }

    private function scan(): void {
        $entities = $this->getEntities();
        foreach ($this->getJobs() as $name => $job) {
            /** @var CronJobAttribute $attribute */
            $attribute = $job['cron'];
            if (isset($entities[$name])) {
                $entity = $entities[$name]->setPeriod($attribute->getPeriod());
            } else {
                $entity = new CronJob($name, $attribute->getPeriod());
            }
            $this->em->persist($entity);
        }
    }
}
