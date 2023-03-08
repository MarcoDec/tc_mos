<?php

declare(strict_types=1);

namespace App\State\Hr\Employee\Notification;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Hr\Employee\Notification;
use Doctrine\ORM\EntityManagerInterface;

/** @implements ProviderInterface<Notification> */
class NotificationProvider implements ProviderInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param  mixed[]                                $uriVariables
     * @param  mixed[]                                $context
     * @return Notification[]|Paginator<Notification>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|Paginator {
        return $this->em->getRepository(Notification::class)->provideCollection($operation, $uriVariables, $context);
    }
}
