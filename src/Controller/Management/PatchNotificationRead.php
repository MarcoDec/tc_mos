<?php

namespace App\Controller\Management;

use App\Entity\Management\Notification;
use App\Repository\Management\NotificationRepository;
use Exception;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PatchNotificationRead {
    public function __construct(private NotificationRepository $repo) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(int $id): Notification {
        /** @var Notification $notification */
        $notification = $this->repo->find((string) $id);
        $notification->setRead(true);
        $this->repo->persist($notification);
        return $notification;
    }
}
