<?php

use Modules\NsPrintAdapter\Http\Controllers\NsPrintAdapterController;
use App\Http\Middleware\Authenticate;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

Route::prefix( 'dashboard' )->group( function() {
    Route::middleware([
        SubstituteBindings::class,
        Authenticate::class
    ])->group( function() {
        include_once( dirname( __FILE__ ) . '/multistore.php' );
    });
});