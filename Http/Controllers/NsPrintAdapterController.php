<?php 
namespace Modules\NsPrintAdapter\Http\Controllers;

use App\Http\Controllers\DashboardController;
use Modules\NsPrintAdapter\Settings\PrintAdapterSettings;
use Modules\NsPrintAdapter\Services\PrintService;
use App\Classes\Hook;
use App\Classes\Output;
use App\Models\Order;

class NsPrintAdapterController extends DashboardController
{
    public function getSettingsPage()
    {
        Hook::addAction( 'ns-dashboard-footer', function( Output $output ) {
            $output->addView( 'NsPrintAdapter::footer' );
        });

        return PrintAdapterSettings::renderForm();
    }

    public function getReceipt( Order $order )
    {
        $printService   =   app()->make( PrintService::class );
        return $printService->getOrderReceipt( $order );
    }
}