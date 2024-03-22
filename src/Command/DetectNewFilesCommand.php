<?php
namespace App\Command;

use App\Service\NewFileDetector;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DetectNewFilesCommand extends Command
{
    private NewFileDetector $detector;

    protected static $defaultName = 'app:detect-new-files';

    public function __construct(NewFileDetector $detector)
    {
        parent::__construct();
        $this->detector = $detector;
    }

    protected function configure(): void
    {
        $this->setDescription('Detect new files in the remote directory.');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directories = [
            '/test/orders/old'//,
//            '/path/to/remote/directory2',
//            '/path/to/remote/directory3',
        ];

        $newFilesDetected = false;

        foreach ($directories as $directory) {
            $newFiles = $this->detector->detectNewFiles($directory);
            if (!empty($newFiles)) {
                $newFilesDetected = true;
                $output->writeln(sprintf('New files detected in %s: %s', $directory, implode(', ', $newFiles)));
            }
        }

        if (!$newFilesDetected) {
            $output->writeln('No new files detected.');
        }

        return $newFilesDetected ? 1 : 0;
    }
}
