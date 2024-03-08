<?php
namespace App\DataPersister\Production;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Production\Engine\CounterPart\CounterPart;
use App\Entity\Production\Engine\CounterPart\Group;
use App\Entity\Production\Engine\Engine;
use App\Entity\Production\Engine\Tool\Tool;
use App\Entity\Production\Engine\Workstation\Workstation;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class EngineDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private readonly EntityManagerInterface $em, private RequestStack $requestStack)
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Engine
            && (
                (isset($context['collection_operation_name'])
                    && $context['collection_operation_name'] === 'post')
                || (isset($context['item_operation_name'])
                    && $context['item_operation_name'] === 'patch')
            );
    }

    public function persist($data, array $context = []): void
    {
        /** @var Engine $data */
        if ($data->getGroup() !== null) {
            if ($data instanceof Workstation) {
                $workStationGroup = $this->em->getRepository(\App\Entity\Production\Engine\Workstation\Group::class)->find($data->getGroup()->getId());
                $data->setCode($workStationGroup->getCode() . '-' . $data->getId());
            } elseif ($data instanceof Tool) {
                $toolGroup = $this->em->getRepository(\App\Entity\Production\Engine\Tool\Group::class)->find($data->getGroup()->getId());
                $data->setCode($toolGroup->getCode() . '-' . $data->getId());
            } else if ($data instanceof CounterPart) {
                $counterPartGroup = $this->em->getRepository(\App\Entity\Production\Engine\CounterPart\Group::class)->find($data->getGroup()->getId());
                $data->setCode($counterPartGroup->getCode() . '-' . $data->getId());
            }
        }
        $this->em->persist($data);
        $this->em->flush();
        $this->em->refresh($data);
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}