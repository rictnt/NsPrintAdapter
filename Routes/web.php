<?php

use Modules\NsPrintAdapter\Http\Controllers\NsPrintAdapterController;
use App\Http\Middleware\Authenticate;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::middleware([
    SubstituteBindings::class,
    Authenticate::class
])->group( function() {
    Route::get( '/dashboard/print-adapter/settings', [ NsPrintAdapterController::class, 'getSettingsPage' ]);
});