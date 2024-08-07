<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Hr\Employee\Employee;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215090225 extends AbstractMigration implements ContainerAwareInterface
{
    private ?EntityManagerInterface $entityManager = null;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
    }

    public function getDescription(): string
    {
        return 'Migre les fichiers images (photos) des employés et met à jour les URLs dans la base de données.';
    }

    public function up(Schema $schema): void
    {
        $jsonFilePath =  __DIR__ . '/../migrations-data/exportjson_table_attachment.json';
        if (!file_exists($jsonFilePath)) {
            $this->write('Fichier de données non trouvé.');
            return;
        }
        $data = json_decode(file_get_contents($jsonFilePath), true);
        if ($data === null && json_last_error() != JSON_ERROR_NONE) {
            $this->write('Erreur lors de la lecture du fichier de données JSON.');
            return;
        }

        $destinationFolder = __DIR__ . '/../public/uploads/hr-employee-employee/';
        //Si le dossier cible n'existe pas on le crée
        if (!file_exists($destinationFolder)) {
            mkdir($destinationFolder, 0777, true);
        }
        foreach ($data as $item) {
            if ($item['id_employee'] !== null && $item['is_pic'] == '1') {
                $oldId = $item['id_employee'];
                $filename = basename($item['filename']);
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $sourcePath = __DIR__ . "/../migrations-files/employee/{$oldId}/{$filename}";
//                $this->write(sprintf('Fichier pour employé source %d : %s', $oldId, $sourcePath));
                if (file_exists($sourcePath)) {
                    $employee = $this->entityManager->getRepository(Employee::class)->findOneBy(['oldId' => $oldId]);

                    if ($employee) {
                        $newFilename = "{$employee->getId()}.{$extension}";
                        $destinationPath = $destinationFolder . $newFilename;
                        //Récupère la variable d'environnement pour le domaine
                        $domain = $_ENV['DESKTOP_HOST'];
                        if (copy($sourcePath, $destinationPath)) {
                            $employee->setFilePath("http://{$domain}/uploads/hr-employee-employee/{$newFilename}");
                            $this->entityManager->flush();
//                            $this->write(sprintf('Fichier pour employé ID %d migré et mis à jour.', $employee->getId()));
                        } else {
                            $this->write(sprintf('Erreur lors de la copie du fichier pour l\'employé ID %d.', $employee->getId()));
                        }
                    } else {
//                        $this->write(sprintf('Employé source %d non trouvé.', $oldId));
                    }
                } else {
                    $this->write(sprintf('Fichier pour employé source %d non trouvé.', $sourcePath));
                }
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->write('Impossible de revenir en arrière.');
    }
}
