<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/example-api-call', function () {
    $client = new \GuzzleHttp\Client();
    $response = $client->get('https://canvas.instructure.com:443/api/v1/courses?enrollment_type=teacher');

    $body = $response->getBody();
    $data = json_decode($body);

    return view('example', ['data' => $data]);
});