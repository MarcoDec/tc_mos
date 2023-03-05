<?php

namespace App\EventListener\Security;

use App\Entity\Api\Token;
use App\Security\SecurityTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListener {
    use SecurityTrait {
        __construct as private constructSecurity;
    }

    public function __construct(Security $security, private readonly EntityManagerInterface $em) {
        $this->constructSecurity($security);
    }

    public function __invoke(LogoutEvent $event): void {
        $this->em->beginTransaction();
        $this->em->getRepository(Token::class)->disconnect($this->getUser());
        $this->em->commit();
        $event->setResponse(new Response(null, Response::HTTP_NO_CONTENT));
    }
}
