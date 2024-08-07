<?php
namespace App\DataPersister\Production;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Production\Manufacturing\Order as ManufacturingOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

// L'objectif de ce datapersister est de générer la date de livraison de l'of en fonction de la date de début de fabrication et du délai de fabrication
class ManufacturerOrderDeliveryDateDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private EntityManagerInterface $em, private RequestStack $requestStack)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        return
            $data instanceof ManufacturingOrder
            && (
                (
                    isset($context['collection_operation_name'])
                    && $context['collection_operation_name'] === 'post'
                )
                || (
                    isset($context['item_operation_name'])
                    && $context['item_operation_name'] === 'patch'
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function persist($data, array $context = [])
    {
        if (isset($context['collection_operation_name'])
            && $context['collection_operation_name'] === 'post') {
            //à la création de l'of, on génère le numéro de l'of en fonction du site de fabrication (on prend le dernier code et on y ajoute 1)
            /** @var ManufacturingOrder $data */
            $site = $data->getManufacturingCompany();
            $allManufacturingOrders = $this->em->getRepository(ManufacturingOrder::class)->findBy(['manufacturingCompany' => $site, 'deleted' => false]);
            // On transforme les ref en int pour pouvoir les trier
            $allManufacturingOrders = array_map(function ($manufacturingOrder) {
                return intval($manufacturingOrder->getRef());
            }, $allManufacturingOrders);
            // On trie les ref par ordre décroissant
            rsort($allManufacturingOrders);
            // On prend le premier élément du tableau trié
            $lastManufacturingOrderCode = $allManufacturingOrders[0] ?? 0;
            $data->setRef($lastManufacturingOrderCode + 1);
        }
        //Dans tous les cas, on génère la date de livraison de l'of en fonction de la date de début de fabrication et du délai de fabrication
        /** @var ManufacturingOrder $data */
        $startDate = $data->getManufacturingDate();
        $manufacturedProduct = $data->getProduct();
        $quantity = $data->getQuantityRequested()->getValue();
        // On calcule le délai de fabrication en heure
        $manufacturingDelay = $quantity * ($manufacturedProduct->getCostingAutoDuration()->getValue()+$manufacturedProduct->getCostingManualDuration()->getValue());
        // On récupère le temps travaillé par jour
        $workingTimePerDay = 8; // 8 heures par jour
        // On calcule le délai de fabrication en jour
        $manufacturingLeadTime = ceil($manufacturingDelay / $workingTimePerDay);
        // On doit également ajouter le temps de remise en production si le dernier of a été fabriqué il y a plus de 3 mois
        $lastManufacturingOrder = $this->em->getRepository(ManufacturingOrder::class)->findOneBy(['product' => $manufacturedProduct], ['manufacturingDate' => 'DESC']);
        if ($lastManufacturingOrder) {
            $lastDeliveryDate = $lastManufacturingOrder->getDeliveryDate();
            $interval = $startDate->diff($lastDeliveryDate);
            $months = $interval->m + $interval->y * 12;
            if ($months > 3) {
                $productionDelay = $manufacturedProduct->getProductionDelay()->getValue(); // en jours
                $manufacturingLeadTime += $productionDelay;
            }
        }

        // On ajoute le délai de fabrication à la date de début de fabrication
        $deliveryDate = $startDate->add(new \DateInterval('P' . $manufacturingLeadTime . 'D'));
        $data->setDeliveryDate($deliveryDate);
        $this->em->persist($data);
        $this->em->flush();
        $this->em->refresh($data);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}