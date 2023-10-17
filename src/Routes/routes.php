<?php

use App\Http\Controllers\HomeController;
use G4T\MockInterface\Controllers\MockController;
use Illuminate\Support\Facades\Route;

Route::get("/api/routes", [MockController::class,"getRoutes"]);


$home = new MockController;
$functions = $home->getRoutes();
foreach ($functions as $route => $interface) {
    if (is_array($interface) && isset($interface['method'])) {
        Route::{$interface['method']}($route, [MockController::class, 'index'])->middleware('api');
    } else {
        foreach ($interface as $nested_route) {
            Route::{$nested_route['method']}($route, [MockController::class, 'index'])->middleware('api');
        }
    }
}
