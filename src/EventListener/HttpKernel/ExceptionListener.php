<?php

namespace App\EventListener\HttpKernel;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class ExceptionListener {
    /**
     * @param int[] $exceptionToStatus
     */
    public function __construct(private array $exceptionToStatus) {
    }

    public function __invoke(ExceptionEvent $event): void {
        $exceptionClass = get_class($event->getThrowable());
        $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        foreach ($this->exceptionToStatus as $class => $status) {
            if (is_a($exceptionClass, $class, true)) {
                $statusCode = $status;
                break;
            }
        }
        $event->setResponse(new JsonResponse(null, $statusCode));
    }
}
