<?php

namespace App\DataPersister;

use App\Entity\Mos\Connecteur;
use App\Entity\Mos\Accessoire;
use App\Entity\Mos\Voie;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Psr\Log\LoggerInterface;

final class ConnecteurDeleteDataPersister implements ContextAwareDataPersisterInterface {
    
    private $logger;

    public function __construct(private readonly EntityManagerInterface $em, LoggerInterface $deleted_connecteur) {
        $this->logger = $deleted_connecteur;
    }

    /**
     * @param WorkflowInterface $data
     * @param mixed[]           $context
     */
    public function persist($data, array $context = []): void {
    }

    /**
     * @param WorkflowInterface $data
     * @param mixed[]           $context
     */
    public function remove($data, array $context = []): void {
        $this->em->beginTransaction();

        // Suppression d'un Connecteur
        if ($data instanceof Connecteur) {

            foreach ($data->getAccessoires() as $accessoire) { // Parcours les accessoires dans la liste accessoires
                
                $this->logger->info("Update Accessoire.connecteur {$data->getId()} => null pour l'Accessoire à l'id = {$accessoire->getId()}");
                $accessoire->setConnecteur(null); // Efface la référence connecteur
                
                $temp = is_null($accessoire->getTargetedConnecteur())? "null" : $accessoire->getTargetedConnecteur()->getId();
                $this->logger->info("Update Accessoire.targetedConnecteur {$temp} => null pour l'Accessoire à l'id = {$accessoire->getId()}");
                $accessoire->setTargetedConnecteur(null); // Efface la référence targetedConnecteur
 
                $this->em->persist($accessoire);
                $this->em->flush();
                $this->logger->info("Delete de l'Accessoire à l'id = {$accessoire->getId()}");
                $this->em->remove($accessoire); // Supprime l'accessoire
            }
            
            $repositoryAccessoire = $this->em->getRepository(Accessoire::class);
            $criteria = new Criteria();
            $criteria->where(Criteria::expr()->eq('targetedConnecteur', $data));
            $accessoires = $repositoryAccessoire->matching($criteria); // Récupère les accessoires dont targetedConnecteur correspond au connecteur $data
            
            // Efface les références targetedConnecteur pour les accessoires référençant le connecteur $data
            foreach ($accessoires as $accessoire) {
                $this->logger->info("Update Accessoire.targetedConnecteur {$data->getId()} => null pour l'Accessoire à l'id = {$accessoire->getId()}");
                $accessoire->setTargetedConnecteur(null);
            }

            foreach ($data->getVoies() as $voie) { // Parcours les voies associées
                $this->logger->info("Update Voie.connecteur {$data->getId()} => null pour la Voie à l'id = {$voie->getId()}");
                $voie->setConnecteur(null); // Efface la référence connecteur
                
                $this->em->persist($voie);
                $this->em->flush();
                $this->logger->info("Delete de la Voie à l'id = {$voie->getId()}");
                $this->em->remove($voie); // Supprime la voie
            }
            
            $this->logger->info("Delete du Connecteur à l'id = {$data->getId()}");
            $this->em->remove($data); // Supprime le Connecteur
            $this->em->flush();
        }
        // Suppression d'un Accessoire
        else if ($data instanceof Accessoire) {
            
            $data->setConnecteur(null); // Efface la référence connecteur
            $data->setTargetedConnecteur(null); // Efface la référence targetedConnecteur
            $this->em->persist($data);
            $this->em->flush();
            $this->em->remove($data); // Supprime l'accessoire
            $this->em->flush();
        }
        // Suppression d'une Voie
        else if ($data instanceof Voie) {
            
            $connecteur = $data->getConnecteur(); // Récupère le connecteur référencé
            
            $repositoryVoie = $this->em->getRepository(Voie::class);
            $criteria = new Criteria();
            $criteria->where(Criteria::expr()->gt('num', $data->getNum()))
            ->andWhere(Criteria::expr()->eq('connecteur', $connecteur));
            $voies = $repositoryVoie->matching($criteria); // Récupère les voies dont le num est suppérieur au num de voie
            
            // Reconfigure de num si besoin
            foreach($voies as $voie) {
                $num = $voie->getNum();
                $voie->setNum($num-1);
                $this->em->persist($voie);
                $this->em->flush();
            }

            $connecteur->removeVoie($data); // Supprime la voie de la liste des voies du connecteur
            $this->em->persist($connecteur);
            $this->em->flush();

            // Configure un tri croissant sur num
            $sortedVoies = $connecteur->getVoies()->matching($criteria); // Trie les voies

            $this->em->remove($data); // Supprime la voie
            $this->em->flush();
        }
        else throw new BadRequestHttpException('Cette ressource n\'est pas à un statut supprimable.');
        
        $this->em->commit();
    }

    /**
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return ($data instanceof Connecteur || $data instanceof Accessoire || $data instanceof Voie)
            && isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'delete';
    }
}
