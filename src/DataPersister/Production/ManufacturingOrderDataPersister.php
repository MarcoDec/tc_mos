<?php
namespace App\DataPersister\Production;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Production\Manufacturing\Order as ManufacturingOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * L'objectif de ce datapersister est de générer le numéro de l'of en fonction du site de fabrication (on prend le dernier code et on y ajoute 1)
 */
class ManufacturingOrderDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private EntityManagerInterface $em, private RequestStack $requestStack)
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof ManufacturingOrder
            && (
            (isset($context['collection_operation_name'])
                && $context['collection_operation_name'] === 'post')
            );
    }

    public function persist($data, array $context = []): void
    {
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
        $this->em->persist($data);
        $this->em->flush();
        $this->em->refresh($data);
    }

    public function remove($data, array $context = [])
    {

    }
}