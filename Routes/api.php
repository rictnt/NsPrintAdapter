<?php
use Modules\NsPrintAdapter\Http\Controllers\NsPrintAdapterController;
use App\Http\Middleware\Authenticate;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::middleware([
    SubstituteBindings::class
])->group( function() {
    Route::get( '/nexopos/v4/ns-printadapter/{order}/print-data/', [ NsPrintAdapterController::class, 'getReceipt' ]);
});