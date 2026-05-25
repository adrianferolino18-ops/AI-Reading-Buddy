<?php
$path = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $path);
$modules = $pdo->query('SELECT count(*) FROM modules')->fetchColumn();
$savedWords = $pdo->query('SELECT count(*) FROM saved_words')->fetchColumn();
echo "modules=$modules\n";
echo "saved_words=$savedWords\n";
