<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class MetricsController extends AbstractController{
	
    #[Route('/api/metrics', name: 'api_metrics', methods: ['GET'])]
    public function index(Connection $connection): JsonResponse{

        $total = $connection->fetchOne('SELECT COUNT(*) FROM Feedback');


        $bySentiment = $connection->fetchAllAssociative(
            'SELECT sentiment, COUNT(*) as count FROM Feedback WHERE sentiment IS NOT NULL GROUP BY sentiment'
        );

        $byType = $connection->fetchAllAssociative(
            'SELECT type, COUNT(*) as count FROM Feedback WHERE type IS NOT NULL GROUP BY type'
        );

        $lastWeek = $connection->fetchOne(
            'SELECT COUNT(*) FROM Feedback WHERE created_at > ?',
            [time() - 7 * 24 * 60 * 60]
        );

        return $this->json([
            'total' => $total,
            'last_week' => $lastWeek,
            'by_sentiment' => $bySentiment,
            'by_type' => $byType,
        ]);
    }
}