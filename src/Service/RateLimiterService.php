<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

class RateLimiterService{
	public function __construct(
		private Connection $connection,
		private int $limit = 5,
		private int $timeout_s = 60
	){}

	public function check(string $ip){
		$now = time();
		$border = $now - $this->timeout_s;

		$this->connection->executeStatement(
			"DELETE FROM RateLimit WHERE requested_at <= ?",
			[$border]
		);

		$cRequests = $this->connection->fetchOne(
			"SELECT COUNT(*) FROM RateLimit WHERE ip = ? AND requested_at > ?",
			[$ip, $border]
		);

		if($cRequests >= $this->limit){
			return false;
		}

		$this->connection->insert('RateLimit', [
			'ip' => $ip,
			'requested_at' => $now
		]);

		return true;
	}
}