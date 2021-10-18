<?php

namespace App\EventListener\HttpKernel;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ExceptionListener {
    /**
     * @param int[] $exceptionToStatus
     */
    public function __construct(private array $exceptionToStatus) {
    }

    public function __invoke(ExceptionEvent $event): void {
        $exception = $event->getThrowable();
        $exceptionClass = get_class($exception);
        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : Response::HTTP_UNPROCESSABLE_ENTITY;
        foreach ($this->exceptionToStatus as $class => $status) {
            if (is_a($exceptionClass, $class, true)) {
                $statusCode = $status;
                break;
            }
        }
        if (!in_array($statusCode, [Response::HTTP_BAD_REQUEST, Response::HTTP_UNPROCESSABLE_ENTITY])) {
            $event->setResponse(new Response(null, $statusCode));
        }
    }
}
