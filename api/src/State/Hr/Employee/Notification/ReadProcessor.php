<?php

declare(strict_types=1);

namespace App\State\Hr\Employee\Notification;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Hr\Employee\Notification;
use Doctrine\ORM\EntityManagerInterface;

class ReadProcessor implements ProcessorInterface {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    /**
     * @param Notification $data
     * @param mixed[]      $uriVariables
     * @param mixed[]      $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Notification {
        $this->em->beginTransaction();
        $data->setRead(true);
        $this->em->flush();
        $this->em->commit();
        return $data;
    }
}
