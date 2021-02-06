<?php
namespace Modules\NsPrintAdapter\Providers;

use App\Classes\Hook;
use Modules\NsPrintAdapter\Settings\PrintAdapterSettings;
use Modules\NsPrintAdapter\Events\NsPrintAdapterEvent;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        Hook::addFilter( 'ns-dashboard-menus', function( $menus ) {
            if ( isset( $menus[ 'settings' ] ) ) {
                $menus[ 'settings' ][ 'childrens' ][ 'ns-adapter' ]    =   [
                    'label'     =>      __( 'Ns Print Adapter' ),
                    'href'      =>      ns()->url( '/dashboard/print-adapter/settings' ),
                ];
            }
            return $menus;
        });

        Hook::addFilter( 'ns.settings', function( $class, $identifier ) {
            if ( $identifier === 'ns.pa-settings' ) {
                return new PrintAdapterSettings;
            }
            return $class;
        }, 10, 2 );

        Hook::addAction( 'ns-dashboard-footer', [ NsPrintAdapterEvent::class, 'getFooter' ] );
    }

    public function boot()
    {

    }
}