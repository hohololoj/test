<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

class FeedbackRepository
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function save(array $data): int
    {
        $this->connection->insert('Feedback', [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'comment' => $data['comment'],
            'sentiment' => $data['sentiment'] ?? null,
            'type' => $data['type'] ?? null,
            'created_at' => time(),
        ]);

        return (int) $this->connection->lastInsertId();
    }
}