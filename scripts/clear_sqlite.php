<?php
$path = __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) {
    echo "NO FILE\n";
    exit(1);
}
$pdo = new PDO('sqlite:' . $path);
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    echo "Dropping $table\n";
    $pdo->exec("DROP TABLE IF EXISTS $table");
}
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
echo "Remaining: " . implode(', ', $tables) . "\n";
