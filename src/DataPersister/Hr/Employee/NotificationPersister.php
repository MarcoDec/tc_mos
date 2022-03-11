<?php

namespace App\DataPersister\Hr\Employee;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Hr\Employee\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class NotificationPersister implements DataPersisterInterface {
    public function __construct(private readonly EntityManagerInterface $em, private readonly RequestStack $requests) {
    }

    /**
     * @param Notification $data
     */
    public function persist($data): void {
        $data->setRead(true);
        $this->em->flush();
    }

    public function remove($data): void {
    }

    public function supports($data): bool {
        $request = $this->requests->getCurrentRequest();
        return !empty($request)
            && $request->isMethod(Request::METHOD_PATCH)
            && $data instanceof Notification;
    }
}
