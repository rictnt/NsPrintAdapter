<?php
namespace Modules\NsPrintAdapter\Events;

use Illuminate\Support\Facades\View;

/**
 * Register Events
**/
class NsPrintAdapterEvent
{
    public function __construct()
    {
        //
    }

    public static function getFooter( $output )
    {
        $output->addView( 'NsPrintAdapter::pos.footer' );
    }
}