<?php

namespace App\EventListener\Security;

use App\Entity\Hr\Employee\Employee;
use App\Repository\Api\TokenRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListener {
    public function __construct(private Security $security, private TokenRepository $tokenRepo) {
    }

    public function __invoke(LogoutEvent $event): void {
        /** @var Employee $user */
        $user = $this->security->getUser();
        $this->tokenRepo->disconnect($user);
        $event->setResponse(new Response(null, Response::HTTP_NO_CONTENT));
    }
}
