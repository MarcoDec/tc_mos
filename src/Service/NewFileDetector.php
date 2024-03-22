<?php
namespace App\Service;

use App\Entity\Event\NewFileDetectedEvent;
use App\Entity\It\SFTPConnection;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class NewFileDetector
{
    private SFTPConnection $sftp;
    private EventDispatcherInterface $dispatcher;

    public function __construct(SFTPConnection $sftp, EventDispatcherInterface $dispatcher)
    {
        $this->sftp = $sftp;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @throws Exception
     */
    public function detectNewFiles(string $directory): array
    {
        // Récupération de la liste précédente des fichiers (à partir d'une base de données ou d'un fichier local)
        $previousFiles = $this->loadFileList($directory);
        // Récupération de la liste actuelle des fichiers
        $currentFiles = iterator_to_array($this->sftp->getIterator($directory));
        // Détection des nouveaux fichiers
        $newFiles = array_diff($currentFiles, $previousFiles);
        // Traitement des nouveaux fichiers
        foreach ($newFiles as $newFile) {
            $event = new NewFileDetectedEvent($newFile);
            $this->dispatcher->dispatch($event);
        }
        // Mise à jour de la liste précédente des fichiers
        $this->saveFileList($directory, $currentFiles);
        return $newFiles;
    }

    private function loadFileList(string $directory): array
    {
        $filename = $this->getFilename($directory);
        if (!file_exists($filename)) {
            return [];
        }
        return json_decode(file_get_contents($filename), true) ?? [];
    }
    private function saveFileList(string $directory, array $fileList): void
    {
        $filename = $this->getFilename($directory);
        file_put_contents($filename, json_encode($fileList));
    }
    private function getFilename(string $directory): string
    {
        $path = str_replace('/', '_', $directory);
        $filename = "/edi/$path.txt";
        return realpath(__DIR__.'/../../..').$filename;
    }
}
