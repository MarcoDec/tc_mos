<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CloneDataPersister implements ContextAwareDataPersisterInterface {
    final public function __construct(
        protected readonly EntityManagerInterface $em,
        private readonly ResourceMetadataFactoryInterface $metadataFactory,
        private readonly RequestStack $requestStack,
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @param Entity  $data
     * @param mixed[] $context
     */
    final public function persist($data, array $context = []): Entity {
        $this->em->detach($data);
        $this->validate($clone = clone $data);
        $this->em->persist($clone);
        $this->update($data, $clone);
        $this->em->flush();
        return $clone;
    }

    /**
     * @param Entity  $data
     * @param mixed[] $context
     */
    final public function remove($data, array $context = []): void {
    }

    /**
     * @param mixed   $data
     * @param mixed[] $context
     */
    public function supports($data, array $context = []): bool {
        return $data instanceof Entity
            && isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'clone';
    }

    /**
     * @param Entity $data
     * @param Entity $clone
     */
    protected function update($data, $clone): void {
    }

    private function validate(Entity $data): void {
        if (empty($request = $this->requestStack->getCurrentRequest())) {
            return;
        }
        $attributes = RequestAttributesExtractor::extractAttributes($request);
        $this->validator->validate(
            data: $data,
            context: ['groups' => $this->metadataFactory->create($attributes['resource_class'])->getOperationAttribute(
                attributes: $attributes,
                key: 'validation_groups',
                resourceFallback: true
            )]
        );
    }
}
