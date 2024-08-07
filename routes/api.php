<?php

use Ferdiunal\NovaTranslations\Http\Controllers\AllTranslateController;
use Ferdiunal\NovaTranslations\Http\Controllers\TranslateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Route::get('/', function (Request $request) {
//     //
// });

Route::post('/translate/{resource}/{resourceId}', TranslateController::class);
Route::post('/all-translate/{resource}/{resourceId}', AllTranslateController::class);
