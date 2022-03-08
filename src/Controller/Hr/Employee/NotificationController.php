<?php

namespace App\Controller\Hr\Employee;

use App\Entity\Hr\Employee\Notification;
use App\Repository\Hr\Employee\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class NotificationController {
    public function __construct(private NotificationRepository $repo) {
    }

    #[Route(
        path: '/api/notifications/category/all',
        name: 'api_notifications_delete_collection',
        defaults: [
            '_api_collection_operation_name' => 'delete',
            '_api_resource_class' => Notification::class
        ],
        methods: 'DELETE'
    )]
    public function delete(): void {
        $this->repo->delete();
    }

    /**
     * @return Notification[]
     */
    #[Route(
        path: '/api/notifications/category/read-all',
        name: 'api_notifications_patch_collection',
        defaults: [
            '_api_collection_operation_name' => 'patch',
            '_api_resource_class' => Notification::class
        ],
        methods: 'PATCH'
    )]
    public function read(Request $request): array {
        $request->attributes->set('data', $data = $this->repo->read());
        return $data;
    }
}
