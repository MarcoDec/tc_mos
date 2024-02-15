<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Hr\Employee\Employee;
use App\Entity\Production\Engine\CounterPart\CounterPart;
use App\Entity\Production\Engine\Engine;
use App\Entity\Production\Engine\Tool\Tool;
use App\Entity\Production\Engine\Workstation\Workstation;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215134730 extends AbstractMigration implements ContainerAwareInterface
{
    private ?EntityManagerInterface $entityManager = null;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
    }

    public function getDescription(): string
    {
        return 'Migre les fichiers images (photos) des machines et met à jour les URLs dans la base de données.';
    }

    public function up(Schema $schema): void
    {
        $jsonFilePath =  __DIR__ . '/../migrations-data/exportjson_table_attachment.json';
        $data = json_decode(file_get_contents($jsonFilePath), true);
        foreach ($data as $item) {
            if ($item['id_engine'] !== null && $item['is_pic'] == '1') {
                $oldId = $item['id_engine'];
                $filename = basename($item['filename']);
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $sourcePath = __DIR__ . "/../migrations-files/engine/{$oldId}/{$filename}";
                $this->write(sprintf('Fichier pour la machine source %d : %s', $oldId, $sourcePath));
                if (file_exists($sourcePath)) {
                    $pattern = '%-' . $oldId; // Prépare le pattern pour la recherche SQL LIKE
                    $query = $this->entityManager->createQueryBuilder()
                        ->select('e')
                        ->from(Engine::class, 'e')
                        ->where('e.code LIKE :pattern')
                        ->setParameter('pattern', $pattern)
                        ->getQuery();
                    $engine = $query->getOneOrNullResult();
//                    $engine = $this->entityManager->getRepository(Engine::class)->findOneBy(['oldId' => $oldId]);
                    if ($engine) {
                        $basePath = '/uploads/production-engine-engine-engine/';
                        if ($engine instanceof Tool) {
                            //Récupère le chemin du fichier
                            $basePath = '/uploads/production-engine-tool-tool/';
                        } else if ($engine instanceof Workstation) {
                            $basePath = '/uploads/production-engine-workstation-workstation/';
                        } else if ($engine instanceof CounterPart) {
                            $basePath = '/uploads/production-engine-counter-part-counter-part/';
                        }
                        $destinationFolder = __DIR__ . "/../public{$basePath}";
                        $this->write(sprintf('Dossier de destination : %s', $destinationFolder));
                        $newFilename = "{$engine->getId()}.{$extension}";
                        $this->write(sprintf('Nouveau nom de fichier : %s', $newFilename));
                        $destinationPath = $destinationFolder . $newFilename;
                        $this->write(sprintf('Nouveau chemin : %s', $destinationPath));
                        //Récupère la variable d'environnement pour le domaine
                        $domain = $_ENV['DESKTOP_HOST'];
                        if (copy($sourcePath, $destinationPath)) {
                            $engine->setFilePath("http://{$domain}{$basePath}{$newFilename}");
                            $this->entityManager->flush();
//                            $this->write(sprintf('Fichier pour la machine ID %d migré et mis à jour.', $engine->getId()));
                        } else {
                            $this->write(sprintf('Erreur lors de la copie du fichier pour la machine ID %d.', $engine->getId()));
                        }
                    } else {
//                        $this->write(sprintf('Machine source %d non trouvé.', $oldId));
                    }
                } else {
                    $this->write(sprintf('Fichier pour la machine source %d non trouvé.', $sourcePath));
                }
            }
        }

    }

    public function down(Schema $schema): void
    {
        $this->write('Impossible de revenir en arrière.');
    }
}
