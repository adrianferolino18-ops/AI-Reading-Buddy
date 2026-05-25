<?php
// scripts/run_define.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Bootstrap the application and facades for CLI execution
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
Illuminate\Support\Facades\Facade::setFacadeApplication($app);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// Register a simple validate macro so controller validation works in CLI context
if (!Illuminate\Http\Request::hasMacro('validate')) {
    Illuminate\Http\Request::macro('validate', function ($rules) {
        $validator = Illuminate\Support\Facades\Validator::make($this->all(), $rules);
        if ($validator->fails()) {
            throw new Illuminate\Validation\ValidationException($validator);
        }
        return $validator->validated();
    });
}

// Build a POST request payload
$payload = [
    'word' => $argv[1] ?? 'testword',
    'module_id' => $argv[2] ?? 1,
    'context' => $argv[3] ?? 'example context'
];

$request = Request::create('/', 'POST', $payload);

// Directly call the controller method to bypass web middleware (CSRF etc.)
$controller = new App\Http\Controllers\WordController();
$response = $controller->defineAndStoreWord($request);

if ($response instanceof Illuminate\Http\JsonResponse) {
    echo $response->getContent();
} elseif (is_string($response)) {
    echo $response;
} else {
    // fallback
    var_export($response);
}
