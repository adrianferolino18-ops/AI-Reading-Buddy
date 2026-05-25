<?php
$path = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $path);
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
echo "tables: " . implode(', ', $tables) . "\n";
$info = $pdo->query("PRAGMA table_info('modules')")->fetchAll(PDO::FETCH_ASSOC);
if ($info) {
    echo "modules cols:\n";
    foreach ($info as $col) {
        echo $col['cid'] . ' ' . $col['name'] . ' ' . $col['type'] . '\n';
    }
} else {
    echo "modules table not found\n";
}
echo "\n";
$info = $pdo->query("PRAGMA table_info('saved_words')")->fetchAll(PDO::FETCH_ASSOC);
if ($info) {
    echo "saved_words cols:\n";
    foreach ($info as $col) {
        echo $col['cid'] . ' ' . $col['name'] . ' ' . $col['type'] . '\n';
    }
} else {
    echo "saved_words table not found\n";
}
