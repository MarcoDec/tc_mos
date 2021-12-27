<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Workflow\Registry;

final class EntityDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private EntityManagerInterface $em, private Registry $registry) {
    }

    /**
     * @param mixed   $data
     * @param mixed[] $context
     */
    public function persist($data, array $context = []): object|null {
        $returnObject = null;
        $this->em->detach($data);

        switch ($context['item_operation_name']) {
            case 'clone':
                $returnObject = clone $data;
                $this->em->persist($returnObject);
                $this->em->flush();
                break;
            case 'upgrade':
                /** @var class-string $className */
                $className = $context['identifiers']['id'][0];
                /** @var mixed $parent */
                $parent = $this->em->getRepository($className)->findOneBy(['id' => $data->getId()]);
                $returnObject = clone $data;

                if (null !== $parent) {
                    $parent->addChild($returnObject);
                    $this->em->persist($returnObject);
                    $this->em->flush();
                }
                break;
            case 'promote':
                /** @var class-string $className */
                $className = $context['identifiers']['id'][0];
                /** @var mixed $returnObject */
                $returnObject = $this->em->getRepository($className)->findOneBy(['id' => $data->getId()]);

                if (null !== $returnObject) {
                    $workflow = $this->registry->get($returnObject);

                    if ($workflow->can($returnObject, $data->getPlace())) {
                        $workflow->apply($returnObject, $data->getPlace());

                        $this->em->persist($returnObject);
                        $this->em->flush();
                    } else {
                        throw new Exception("This entity cannot be promoted to '{$data->getPlace()}'");
                    }
                }
                break;
            default:
                break;
        }

        // The clone is return as result
        return $returnObject;
    }

    /**
     * @param mixed   $data
     * @param mixed[] $context
     */
    public function remove($data, array $context = []): void {
        // call your persistence layer to delete $data
    }

    /**
     * @param object  $data
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return isset($context['item_operation_name']) && in_array($context['item_operation_name'], ['clone', 'upgrade', 'promote']);
    }
}
