<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Filesystem\Filesystem;
use DirectoryIterator;

final class Version20240213CopyEmployeeFiles extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Copie les fichiers des employés de l\'ancien dossier vers le nouveau en se basant sur les IDs.';
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

        $stmt = $conn->executeQuery('SELECT id, old_id FROM employee');

        // On récupère la variable d'environnement DESKTOP_HOST
        $DESKTOP_HOST = $_ENV['DESKTOP_HOST'];

        while ($employee = $stmt->fetchAssociative()) {
            $employeeId = $employee['id'];
            $oldPath = $this->getProjectDir() . '/migrations-files/employee/' . $employee['old_id'];
            $newPath = $this->getProjectDir() . '/public/uploads/Employee/' . $employeeId;

            if ($filesystem->exists($oldPath)) {
                if (!$filesystem->exists($newPath)) {
                    $filesystem->mkdir($newPath);
                }

                $files = new DirectoryIterator($oldPath);
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $fileName = $file->getFilename();
                        $filesystem->copy($file->getRealPath(), $newPath . '/' . $fileName);
                        // On ajoute pour chaque fichier un élément dans la table employee_attachment
                        $conn->insert('employee_attachment', [
                            'employee_id' => $employee['id'],
                            'url' => "http://$DESKTOP_HOST/uploads/Employee/$employeeId/$fileName"
                        ]);
                    }
                }
            } else {
                $this->write("Le répertoire pour l'ancien ID " . $employee['old_id'] . " n'existe pas.");
            }
        }
    }
}
