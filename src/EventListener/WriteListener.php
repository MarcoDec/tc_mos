<?php

namespace App\EventListener;

use App\Entity\Interfaces\WorkflowInterface;
use App\Entity\Purchase\Component\Family;
use App\Repository\Purchase\Component\ComponentAttributeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class WriteListener {
    public function __construct(private readonly ComponentAttributeRepository $repo) {
    }

    public function onPost(ViewEvent $event): void {
        $request = $event->getRequest();
        if (
            ($request->isMethod(Request::METHOD_PATCH) && $request->attributes->get('_api_resource_class') === Family::class)
            || ($request->isMethod(Request::METHOD_POST) && $request->attributes->get('_api_collection_operation_name') === 'post')
        ) {
            $this->repo->links();
        }
    }

    public function onPre(ViewEvent $event): void {
        $request = $event->getRequest();
        if ($request->isMethod(Request::METHOD_POST) || $request->isMethod(Request::METHOD_PATCH)) {
            if ($request->attributes->get('_api_item_operation_name') === 'clone') {
                return;
            }
            $data = $request->attributes->get('data');
            if ($data instanceof WorkflowInterface && $data->isFrozen()) {
                throw new BadRequestHttpException('Cette ressource n\'est pas à un statut modifiable.');
            }
        }
    }
}
