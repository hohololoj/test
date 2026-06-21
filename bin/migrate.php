<?php

require __DIR__ . '/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

$connection = DriverManager::getConnection([
	'driver' => 'pdo_sqlite',
	'path' => __DIR__ . '/../var/data.db',
]);

$migrationsDir = __DIR__ . '/../migrations';
$files = glob($migrationsDir . '/*.sql');
sort($files);

foreach ($files as $file) {
    $sql = file_get_contents($file);
    $connection->executeStatement($sql);
    echo basename($file) . " - done\n";
}

echo "Migration successful\n";
