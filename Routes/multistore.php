<?php

use Modules\NsPrintAdapter\Http\Controllers\NsPrintAdapterController;
use Illuminate\Support\Facades\Route;

Route::get( '/print-adapter/settings', [ NsPrintAdapterController::class, 'getSettingsPage' ]);