<?php

use Gustavomendes\PrometheusLaravel\Controllers\MetricController;
use Illuminate\Support\Facades\Route;

Route::get('/metrics', MetricController::class);