<?php 
namespace Modules\NsPrintAdapter\Http\Controllers;

use App\Http\Controllers\DashboardController;
use Modules\NsPrintAdapter\Settings\PrintAdapterSettings;
use App\Classes\Hook;
use App\Classes\Output;

class NsPrintAdapterController extends DashboardController
{
    public function getSettingsPage()
    {
        Hook::addAction( 'ns-dashboard-footer', function( Output $output ) {
            $output->addView( 'NsPrintAdapter::footer' );
        });

        return PrintAdapterSettings::renderForm();
    }
}