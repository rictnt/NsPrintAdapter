<?php
namespace Modules\NsPrintAdapter\Providers;

use App\Classes\Hook;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        Hook::addFilter( 'ns-dashboard-menus', function( $menus ) {
            
            return $menus;
        });
    }

    public function boot()
    {

    }
}