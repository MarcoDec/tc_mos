<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Workflow\Registry;

final class PromoteDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Registry $registry,
        private readonly RequestStack $stack
    ) {
    }

    /**
     * @param Entity  $data
     * @param mixed[] $context
     */
    public function persist($data, array $context = []): Entity {
        if (!is_string($transition = $this->getCurrentRequest()->attributes->get('transition')) || empty($transition)) {
            throw new BadRequestException('Missing "transition" parameter.');
        }
        $workflow = $this->registry->get($data);
        if ($workflow->can($data, $transition)) {
            $workflow->apply($data, $transition);
            $this->em->flush();
        } else {
            throw new BadRequestException("Transition \"$transition\" can't be applied.");
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

    private function getCurrentRequest(): Request {
        if (empty($request = $this->stack->getCurrentRequest())) {
            throw new Exception('Can\'t access to current request.');
        }
        return $request;
    }
}
