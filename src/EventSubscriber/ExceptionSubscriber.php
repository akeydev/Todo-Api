<?php

namespace App\EventSubscriber;

use ApiPlatform\Validator\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof NotFoundHttpException) {
            $message = $exception->getMessage() ?: 'Resource not found';
            $response = new JsonResponse([
            'error' => $message,
            ], 404);
            $event->setResponse($response);
        }

        if ($exception instanceof ValidationException){
            $violations = $exception->getConstraintViolationList();
            foreach ($violations as $violation) {
                $errors[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            $response = new JsonResponse([
                'status' => 422,
                'errors' => $errors,
            ], 422);

            $event->setResponse($response);   
        }
   }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
