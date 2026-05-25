<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Module;
use App\Models\SavedWord;

try {
    $m = Module::create(['title' => 'Smoke Test Module', 'body_text' => 'Smoke test body']);
    $sw = SavedWord::create(['module_id' => $m->id, 'word' => 'smoke', 'definition' => 'a test', 'context' => 'ctx']);

    echo 'module_id: ' . $m->id . PHP_EOL;
    echo 'saved_word_id: ' . $sw->id . PHP_EOL;
    echo 'saved_count: ' . $m->savedWords()->count() . PHP_EOL;
} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
