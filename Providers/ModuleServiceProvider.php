<?php
namespace Modules\NsPrintAdapter\Providers;

use App\Classes\Hook;
use Modules\NsPrintAdapter\Settings\PrintAdapterSettings;
use Modules\NsPrintAdapter\Events\NsPrintAdapterEvent;
use Modules\NsPrintAdapter\Services\PrintService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        Hook::addFilter( 'ns-dashboard-menus', function( $menus ) {
            if ( isset( $menus[ 'settings' ] ) ) {
                $menus[ 'settings' ][ 'childrens' ][ 'ns-adapter' ]    =   [
                    'label'     =>      __m( 'Ns Print Adapter', 'NsPrintAdapter' ),
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

        Hook::addAction( 'ns-dashboard-pos-footer', [ NsPrintAdapterEvent::class, 'getFooter' ] );

        $this->app->singleton( PrintService::class, fn() => new PrintService );
    }

    public function boot()
    {

    }
}