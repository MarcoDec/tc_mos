<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
#[
    AsCommand(
        name: 'app:check-constraints',
        description: 'Checks constraints for a given class.'
    )
]
class CheckConstraintsCommand extends Command
{
//    protected static $defaultName = 'app:check-constraints';

    private MetadataFactoryInterface $metadataFactory;

    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        parent::__construct();
        $this->metadataFactory = $metadataFactory;
    }

    protected function configure()
    {
        $this->setDescription('Checks constraints for a given class.')
            ->addArgument('class', InputArgument::REQUIRED, 'The class to check.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $class = $input->getArgument('class');

        if (!$this->metadataFactory->hasMetadataFor($class)) {
            $output->writeln(sprintf('No validation metadata found for class "%s"', $class));
            return Command::FAILURE;
        }

        $metadata = $this->metadataFactory->getMetadataFor($class);

        foreach ($metadata->getConstraints() as $constraint) {
            $output->writeln(get_class($constraint));
        }

        return Command::SUCCESS;
    }
}
