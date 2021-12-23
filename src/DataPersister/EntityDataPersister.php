<?php

namespace App\DataPersister;

use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\Workflow\Registry;

final class EntityDataPersister implements ContextAwareDataPersisterInterface
{

    public function __construct(private EntityManagerInterface $em, private Registry $registry) {}

    public function supports($data, array $context = []): bool
    {
        return isset($context['item_operation_name']) && in_array($context['item_operation_name'], ['clone', 'upgrade', 'promote']);
    }

    public function persist($data, array $context = [])
    {
        $returnObject = null;
         $this->em->detach($data);

        switch($context['item_operation_name']) {
            case 'clone':
                $returnObject = clone $data;
                $this->em->persist($returnObject);
                $this->em->flush();
                break;
            case 'upgrade':
                $parent = $this->em->getRepository(''.$context['identifiers']['id'][0].'')->findOneById($data->getId());
                $returnObject = clone $data;
                $parent->addChild($returnObject);

                $this->em->persist($returnObject);
                $this->em->flush();
                break;
            case 'promote':
                $returnObject = $this->em->getRepository(''.$context['identifiers']['id'][0].'')->findOneById($data->getId());

                $workflow = $this->registry->get($returnObject);

                if ($workflow->can($returnObject, $data->getPlace())) {
                    $workflow->apply($returnObject, $data->getPlace());
                    $this->em->persist($returnObject);
                    $this->em->flush();
                } else {
                    throw new \Exception("This entity cannot be promoted to '{$data->getPlace()}'");
                }
                break;
            default:
                break;
        }

        // The clone is return as result
        return $returnObject;
    }

    public function remove($data, array $context = [])
    {
        // call your persistence layer to delete $data
    }
}