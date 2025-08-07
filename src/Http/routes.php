<?php

use DagaSmart\Notice\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::resource('notice', Controllers\NoticeController::class);

Route::post('notice_quick_edit', [Controllers\NoticeController::class, 'quickEdit']);
