<?php
namespace Modules\NsPrintAdapter\Providers;

use App\Classes\Hook;
use App\Classes\Output;
use Modules\NsPrintAdapter\Settings\PrintAdapterSettings;
use Modules\NsPrintAdapter\Events\NsPrintAdapterEvent;
use Modules\NsPrintAdapter\Services\PrintService;
use Illuminate\Support\ServiceProvider;
use App\Crud\RegisterCrud;
use App\Services\ModulesService;
use Illuminate\Support\Facades\Event;
use Modules\NsMultiStore\Events\MultiStoreWebRoutesLoadedEvent;

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

        Hook::addFilter( 'ns-crud-form-footer', function( Output $output, $namespace ) {
            if ( $namespace === 'ns.registers' ) {
                return $output->addView( 'NsPrintAdapter::cash-registers.footer' );
            }
            return $output;
        }, 10, 2 );

        Hook::addFilter( 'ns.settings', function( $class, $identifier ) {
            if ( $identifier === 'ns.pa-settings' ) {
                return new PrintAdapterSettings;
            }
            return $class;
        }, 10, 2 );

        Event::listen( MultiStoreApiRoutesLoadedEvent::class, fn() => ModulesService::loadModuleFile( 'NsPrintAdapter', 'Routes/api' ) );
        Event::listen( MultiStoreWebRoutesLoadedEvent::class, fn() => ModulesService::loadModuleFile( 'NsPrintAdapter', 'Routes/multistore' ) );

        /**
         * This will filter the POS options
         * to provide new options.
         */
        Hook::addFilter( 'ns-pos-options', function( $options ) {
            $options[ 'ns_pa_printing_gateway' ]   =   ns()->option->get( 'ns_pa_printing_gateway', 'default' );
            return $options;
        });

        /**
         * This will overwrite the printing fields
         * to add a new printing option (Nexo Print Server 2.x)
         */
        Hook::addFilter( 'ns-printing-settings-fields', function( $fields ) {
            foreach( $fields as &$field ) {
                if ( $field[ 'name' ] === 'ns_pos_printing_gateway' ) {
                    $field[ 'options' ]     =   [
                        ...$field[ 'options' ],
                        [
                            'value' =>  'nps_legacy',
                            'label' =>  __( 'Nexo Print Server (2x)' )
                        ]
                    ];
                }
            }

            return $fields;
        });

        Hook::addFilter( 'ns.crud.form', function( $form, $namespace, $data ) {
            /**
             * @param Register $model
             */
            extract( $data );

            if ( $namespace === 'ns.registers' ) {
                $fields     =   [
                    [
                        'type'  =>  'select',
                        'name'  =>  'printer_name',
                        'label' =>  __( 'Printer' ),
                        'options'   =>  [],
                        'description'   =>  __( 'Select the printer used for the cash register.' ),
                        'value' =>  $model->printer_name ?? '',
                    ], [
                        'type'  =>  'text',
                        'name'  =>  'printer_address',
                        'label' =>  __( 'NPS Address' ),
                        'description'   =>  __( 'Set the address for Nexo Print Server.' ),
                        'value' =>  $model->printer_address ?? '',
                    ], 
                ];
                
                array_push( $form[ 'tabs' ][ 'general' ][ 'fields' ], ...$fields );
            }

            return $form;
        }, 10, 3 );

        Hook::addAction( 'ns-dashboard-pos-footer', [ NsPrintAdapterEvent::class, 'getFooter' ] );

        $this->app->singleton( PrintService::class, fn() => new PrintService );
    }

    public function boot()
    {

    }
}