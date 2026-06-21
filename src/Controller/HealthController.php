<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HealthController extends AbstractController
{
    #[Route('api/health', name: 'api_health', methods: ['GET'])]
    public function check(
        Connection $connection,
        HttpClientInterface $httpClient,
    ): JsonResponse {
        $checks = [];
        $healthy = true;

        try {
            $connection->executeQuery('SELECT 1');
            $checks['database'] = 'ok';
        } catch (\Throwable) {
            $checks['database'] = 'unavailable';
            $healthy = false;
        }

        try {
            $response = $httpClient->request('GET', $_ENV['AI_ENDPOINT'] . '/v1/models', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_ENV['AI_API_KEY'],
                ],
                'timeout' => 3,
            ]);
            
            if ($response->getStatusCode() === 200) {
                $checks['ai_service'] = 'ok';
            } else {
                $checks['ai_service'] = 'degraded';
                $healthy = false;
            }
        } catch (\Throwable) {
            $checks['ai_service'] = 'unavailable';
            $healthy = false;
        }

        return $this->json([
            'status' => $healthy ? 'healthy' : 'degraded',
            'checks' => $checks,
            'timestamp' => time(),
        ], $healthy ? 200 : 503);
    }
}