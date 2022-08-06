<?php

namespace App\EventListener\Security;

use App\Repository\Api\TokenRepository;
use App\Security\SecurityTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListener {
    use SecurityTrait {
        __construct as private constructSecurity;
    }

    public function __construct(Security $security, private readonly TokenRepository $tokenRepo) {
        $this->constructSecurity($security);
    }

    public function __invoke(LogoutEvent $event): void {
        $this->tokenRepo->disconnect($this->getUser());
        $event->setResponse(new Response(null, Response::HTTP_NO_CONTENT));
    }
}
