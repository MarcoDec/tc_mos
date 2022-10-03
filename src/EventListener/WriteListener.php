<?php

namespace App\EventListener;

use App\Entity\Purchase\Component\Attribute;
use App\Entity\Purchase\Component\Component;
use App\Entity\Purchase\Component\Family;
use App\Repository\Purchase\Component\ComponentAttributeRepository;
use Symfony\Component\HttpKernel\Event\ViewEvent;

final class WriteListener {
    public function __construct(private readonly ComponentAttributeRepository $repo) {
    }

    public function onPost(ViewEvent $event): void {
        $request = $event->getRequest();
        if (in_array($request->attributes->get('_api_resource_class'), [Attribute::class, Component::class, Family::class])) {
            $this->repo->links();
        }
    }
}
