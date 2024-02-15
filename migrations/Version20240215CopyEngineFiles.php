<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Filesystem\Filesystem;
use DirectoryIterator;

final class Version20240215CopyEngineFiles extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Copie les fichiers des machines de l\'ancien dossier vers le nouveau en se basant sur les IDs.';
    }

    public function up(Schema $schema) : void
    {
        $this->copyEmployeeFiles();
    }

    public function down(Schema $schema) : void
    {
        $this->write('La migration ne peut pas être annulée.');
    }
    private function getProjectDir(): string
    {
        // Remplacez __DIR__ par le répertoire approprié si votre structure de répertoire est différente
        return dirname(__DIR__, 1);
    }
    private function copyEmployeeFiles(): void
    {
        $filesystem = new Filesystem();

        // Ici, utilisez la connexion à la base de données de Doctrine
        $conn = $this->connection;

        $stmt = $conn->executeQuery('SELECT id, code FROM engine');

        // On récupère la variable d'environnement DESKTOP_HOST
        $DESKTOP_HOST = $_ENV['DESKTOP_HOST'];

        while ($engine = $stmt->fetchAssociative()) {
            $parts = explode('-', $engine['code']);
            $engineOldId = $parts[1];
            $engineId = $engine['id'];
            $oldPath = $this->getProjectDir() . '/migrations-files/engine/' . $engineOldId;
            $newPath = $this->getProjectDir() . '/public/uploads/Engine/' . $engineId;
            $this->write("Copie des fichiers de l'ancien ID " . $engineOldId . " vers le nouveau ID " . $engineId . ". de la machine " . $engine['code'] . ".");
            $this->write("Ancien chemin : " . $oldPath);
            $this->write("Nouveau chemin : " . $newPath);
            if ($filesystem->exists($oldPath)) {
                if (!$filesystem->exists($newPath)) {
                    $filesystem->mkdir($newPath);
                }

                $files = new DirectoryIterator($oldPath);
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $fileName = $file->getFilename();
                        $filesystem->copy($file->getRealPath(), $newPath . '/' . $fileName);
                        // On ajoute pour chaque fichier un élément dans la table engine_attachment
                        $conn->insert('engine_attachment', [
                            'engine_id' => $engineId,
                            'category' => 'doc',
                            'url' => "http://$DESKTOP_HOST/uploads/Engine/$engineId/$fileName"
                        ]);
                    }
                }
            } else {
                $this->write("Le répertoire pour l'ancien ID " . $engineOldId . " n'existe pas.");
            }
        }
    }
}
