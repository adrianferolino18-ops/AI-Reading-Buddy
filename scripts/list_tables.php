<?php
$path = __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) {
    echo "NO FILE\n";
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $path);
    $res = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    if (!$res) {
        echo "(no tables)\n";
    } else {
        foreach ($res as $t) echo $t . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
