<?php

namespace App\DataPersister\Purchase\Component;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ComponentPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private readonly EntityManagerInterface $em, private RequestStack $requestStack) {
    }

    public function supports($data, array $context = []): bool
    {
        dump(['data' => $data, 'context' => $context, 'request' => $this->requestStack->getCurrentRequest()]);
        return $data instanceof Component
            && (
            (isset($context['collection_operation_name'])
            && $context['collection_operation_name'] === 'post')
            || (isset($context['item_operation_name'])
            && $context['item_operation_name'] === 'patch')
            );
    }
    public function persist($data, array $context = []): void
    {
        /** @var Component $data */
        if ($data->getFamily() !== null && str_contains($this->requestStack->getCurrentRequest()->getPathInfo(), 'admin') === true) {
            dump('original data', $data);
            $family = $this->em->getRepository(Family::class)->find($data->getFamily()->getId());
            $data->setCode($family->getCode() . '-' . $data->getId());
            $data->setCustomsCode($family->getCustomsCode());
            dump('data before persist & flush', $data);
            $this->em->persist($data);
            $this->em->flush();
            $this->em->refresh($data);
        } else {
            dump('data', $data);
        }
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}