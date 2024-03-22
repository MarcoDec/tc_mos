<?php
namespace App\Service;

use App\Entity\Event\NewFileDetectedEvent;
use App\Entity\It\SFTPConnection;
use Exception;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class NewFileDetector
{
    private SFTPConnection $sftp;
    private bool $isLogged = false;
    private EventDispatcherInterface $dispatcher;

    public function __construct(SFTPConnection $sftp, EventDispatcherInterface $dispatcher)
    {
        $this->sftp = $sftp;
        $this->dispatcher = $dispatcher;
        $this->isLogged = false;
    }

    /**
     * @throws Exception
     */
    public function login(OutputInterface $output): void
    {
        $output->writeln('Connexion au serveur SFTP');
        $this->sftp->login( $_ENV['SFTP_USER'],  $_ENV['SFTP_PASSWORD']);
        $output->writeln('Connexion réussie');
        $this->isLogged = true;
    }

    /**
     * @throws Exception
     */
    public function detectNewFiles(string $directory, OutputInterface $output): array
    {
        $output->writeln(sprintf('NewFileDetector:detectNewFiles(%s)', $directory));
        if (!$this->isLogged) {
            $this->login($output);
        }
        $remoteFiles = $this->sftp->getIterator($directory, $output);
        $previousFiles = $this->loadFileList($directory, $output);
        $output->writeln(sprintf('Fichiers précédents: %s', implode(', ', $previousFiles)));
        $output->writeln(sprintf('Fichiers actuels: %s', implode(', ', iterator_to_array($remoteFiles))));
        $currentFiles = iterator_to_array($remoteFiles);
        $newFiles = array_diff($currentFiles, $previousFiles);
        foreach ($newFiles as $newFile) {
            $output->writeln(sprintf('Nouveau fichier détecté: %s', $newFile));
            $event = new NewFileDetectedEvent($newFile);
            $this->dispatcher->dispatch($event);
        }
        $this->saveFileList($directory, $currentFiles);
        return $newFiles;
    }

    private function loadFileList(string $directory, OutputInterface $output): array
    {
        $filename = $this->getFilename($directory);
        if (!file_exists($filename)) {
            return [];
        }
        return json_decode(file_get_contents($filename), true) ?? [];
    }
    private function saveFileList(string $directory, array $fileList): void
    {
        if (!is_dir('/var/www/html/TConcept-GPAO/edi/')) {
            mkdir('/var/www/html/TConcept-GPAO/edi/', 0777, true);
        }
        $filename = $this->getFilename($directory);
        file_put_contents($filename, json_encode($fileList));
    }
    private function getFilename(string $directory): string
    {
        $path = str_replace('/', '_', $directory);
        $filename = "/TConcept-GPAO/edi/$path.txt";
        return realpath(__DIR__.'/../../..').$filename;
    }
}
