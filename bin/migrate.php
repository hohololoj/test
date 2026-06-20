<?php

require __DIR__ . "/../vendor/autoload.php";

use Doctrine\DBAL\DriverManager;

$connection = DriverManager::getConnection([
	'driver' => 'pdo_sqlite',
	'path' => __DIR__ . '/../var/data.db'
]);

$sql = file_get_contents(__DIR__ . '/../migrations/001_create_ratelimit.sql');
$connection->executeStatement($sql);

echo "Migration successful\n";
