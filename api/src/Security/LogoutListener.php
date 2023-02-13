<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener {
    public function __invoke(LogoutEvent $event): void {
        $event->setResponse(new JsonResponse(status: JsonResponse::HTTP_NO_CONTENT));
    }
}
