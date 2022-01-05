<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Workflow\Registry;

final class PromoteDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(private EntityManagerInterface $em, private Registry $registry) {
    }

    /**
     * @param Entity  $data
     * @param mixed[] $context
     */
    public function persist($data, array $context = []): Entity {
        $this->em->detach($data);
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
        return $data;
    }

    /**
     * @param Entity  $data
     * @param mixed[] $context
     */
    public function remove($data, array $context = []): void {
    }

    /**
     * @param mixed   $data
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return $data instanceof Entity
            && isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'promote'
            && $this->registry->has($data);
    }
}
