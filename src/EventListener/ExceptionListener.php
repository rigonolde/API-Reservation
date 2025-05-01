<?php

// src/EventListener/ExceptionListener.php
namespace App\EventListener;

use App\Exception\ReservationTimeAlreadyTaken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        $statusCode = match (true) {
            $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
            $exception instanceof ReservationTimeAlreadyTaken =>Response::HTTP_CONFLICT,
            default => Response::HTTP_INTERNAL_SERVER_ERROR
        };

        // sends the modified response object to the event
        $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], $statusCode));
    }
}