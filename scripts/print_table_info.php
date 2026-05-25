<?php
$path = __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) {
    echo "NO FILE\n";
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $path);
    $res = $pdo->query("PRAGMA table_info('modules')")->fetchAll(PDO::FETCH_ASSOC);
    if (!$res) {
        echo "(no columns)\n";
    } else {
        foreach ($res as $r) echo $r['cid'] . ' ' . $r['name'] . ' ' . $r['type'] . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
