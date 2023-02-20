<?php

declare(strict_types=1);

namespace App\State\Hr\Employee\Notification;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Hr\Employee\Notification;
use Doctrine\ORM\EntityManagerInterface;

class RemoveProcessor implements ProcessorInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param Notification $data
     * @param mixed[]      $uriVariables
     * @param mixed[]      $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Notification {
        return $this->em->getRepository(Notification::class)->remove($data);
    }
}
