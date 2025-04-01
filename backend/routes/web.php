<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    broadcast(new \App\Events\MyEvent('selam'));
});





