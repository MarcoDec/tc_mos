<?php
namespace App\Command;

use App\Service\NewFileDetector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetectNewFilesCommand extends Command
{
    private $detector;

    protected static $defaultName = 'app:detect-new-files';

    public function __construct(NewFileDetector $detector)
    {
        parent::__construct();
        $this->detector = $detector;
    }

    protected function configure()
    {
        $this->setDescription('Detect new files in the remote directory.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->detector->detectNewFiles('/test/orders/old');
        $output->writeln('New files detected.');

        return Command::SUCCESS;
    }
}
