<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class LoggerService{
	public function __construct(
        private LoggerInterface $logger,
    ){}

    public function logRequest(Request $request, ?Response $response = null): void
    {
        $context = [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'ip' => $request->getClientIp(),
            'user_agent' => $request->headers->get('User-Agent'),
            'body' => $request->getContent(),
        ];

        if ($response) {
            $context['status'] = $response->getStatusCode();
            $context['response'] = $response->getContent();
        }

        $this->logger->info('API Request', $context);
    }
}