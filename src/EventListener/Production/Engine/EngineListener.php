<?php

namespace App\EventListener\Production\Engine;

use App\Entity\Production\Engine\Engine;
use App\Security\SecurityTrait;
use Doctrine\ORM\Event\LifecycleEventArgs;

final class EngineListener {
    use SecurityTrait;

    public function postPersist(Engine $engine, LifecycleEventArgs $event): void {
        $engine
            ->setCompany($this->getUser()->getCompany())
            ->setCode("{$engine->getGroup()?->getCode()}-{$engine->getId()}");
        $event->getEntityManager()->flush();
    }
}
