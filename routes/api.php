<?php

use Illuminate\Support\Facades\Route;

Route::get('/stock/health', function () {
    return response()->json(['package' => 'commerce-stock', 'status' => 'ok']);
});
