<?php
$path = __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) {
    echo "NO FILE\n";
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $path);
    $pdo->exec("DROP TABLE IF EXISTS modules;");
    $pdo->exec("DROP TABLE IF EXISTS saved_words;");
    echo "Dropped modules and saved_words if they existed.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
