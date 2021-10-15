<?php

namespace App\EventListener\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListener {
    public function __invoke(LogoutEvent $event): void {
        dump('event');
        $event->setResponse(new Response(null, Response::HTTP_NO_CONTENT));
    }
}
