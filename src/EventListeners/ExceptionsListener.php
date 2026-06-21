<?php

namespace App\EventListeners;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionsListener{
    
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Внутренняя ошибка сервера';

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage() ?: $message;
        }

        $this->logger->error($exception->getMessage(), [
            'exception' => $exception::class,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        $responseData = [
            'status' => 'error',
            'message' => $message,
            'code' => $statusCode,
        ];

        if ($_ENV['APP_DEBUG'] ?? false) {
            $responseData['detail'] = $exception->getMessage();
        }

        $response = new JsonResponse($responseData, $statusCode);
        $event->setResponse($response);
    }
}