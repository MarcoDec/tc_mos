<?php
namespace App\Service;

use App\Entity\It\SFTPConnection;
use App\Event\NewFileDetectedEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class NewFileDetector
{
    private $sftp;
    private $dispatcher;

    public function __construct(SFTPConnection $sftp, EventDispatcherInterface $dispatcher)
    {
        $this->sftp = $sftp;
        $this->dispatcher = $dispatcher;
    }

    public function detectNewFiles(string $directory): void
    {
        // Récupération de la liste précédente des fichiers (à partir d'une base de données ou d'un fichier local)
        $previousFiles = [''];

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
        $previousFiles = $currentFiles;
    }
}
