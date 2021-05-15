<?php
namespace Modules\NsPrintAdapter\Settings;

use App\Services\SettingsPage;
use App\Services\Helper;
use App\Services\Options;

class PrintAdapterSettings extends SettingsPage
{
    protected $form        =   [];
    protected $identifier  =   'ns.pa-settings';
    protected $labels      =   [];

    public function __construct()
    {
        $options            =   app()->make( Options::class );

        $this->labels       =    [
            'title'         =>  __m( 'Print Adapter Settings', 'NsPrintAdapter' ),
            'description'   =>  __m( 'Provides settings for print adapter.', 'NsPrintAdapter' ),
        ];

        $this->form         =   [
            'tabs'      =>  [
                'general'   =>  [
                    'label'     =>  __m( 'General', 'NsPrintAdapter' ),
                    'fields'    =>  [
                        [
                            'type'          =>  'switch',
                            'label'         =>  __m( 'Print Enabled', 'NsPrintAdapter' ),
                            'value'         =>  $options->get( 'ns_pa_enabled' ),
                            'description'   =>  __m( 'Printer ', 'NsPrintAdapter' ),
                            'options'       =>  Helper::kvToJsOptions([
                                'yes'       =>  __m( 'Yes', 'NsPrintAdapter' ),
                                'no'        =>  __m( 'No', 'NsPrintAdapter' ),
                            ]),
                            'name'          =>  'ns_pa_enabled',
                        ],  [
                            'type'          =>  'select',
                            'label'         =>  __m( 'Logo Type', 'NsPrintAdapter' ),
                            'value'         =>  $options->get( 'ns_pa_logotype' ),
                            'description'   =>  __m( 'Define what is the logo type.', 'NsPrintAdapter' ),
                            'options'       =>  Helper::kvToJsOptions([
                                'image'     =>  __m( 'Image (using shortcode)', 'NsPrintAdapter' ),
                                'text'      =>  __m( 'Use Store Name', 'NsPrintAdapter' ),
                            ]),
                            'name'          =>  'ns_pa_logotype',
                        ], [
                            'type'          =>  'text',
                            'label'         =>  __m( 'Logo Shortcode', 'NsPrintAdapter' ),
                            'value'         =>  $options->get( 'ns_pa_logoshortcode' ),
                            'description'   =>  __m( 'If the Logo type is a shortcode, provide the shortcode here.', 'NsPrintAdapter' ),
                            'name'          =>  'ns_pa_logoshortcode',
                        ], [
                            'type'          =>  'text',
                            'label'         =>  __m( 'NPS Address', 'NsPrintAdapter' ),
                            'value'         =>  $options->get( 'ns_pa_server_address' ),
                            'description'   =>  __m( 'Provide the local Nexo Print Server 3.x address', 'NsPrintAdapter' ),
                            'name'          =>  'ns_pa_server_address',
                        ], [
                            'type'          =>  'text',
                            'label'         =>  __m( 'Character Limit', 'NsPrintAdapter' ),
                            'value'         =>  $options->get( 'ns_pa_characters_limit' ),
                            'description'   =>  __m( 'Define the maximum allowed characters. Default (48)', 'NsPrintAdapter' ),
                            'name'          =>  'ns_pa_characters_limit',
                        ], [
                            'type'          =>  'select',
                            'label'         =>  __m( 'Default Printer', 'NsPrintAdapter' ),
                            'value'         =>  $options->get( 'ns_pa_printer' ),
                            'description'   =>  __m( 'Printer ', 'NsPrintAdapter' ),
                            'options'       =>  [],
                            'name'          =>  'ns_pa_printer',
                        ], [
                            'type'          =>  'textarea',
                            'label'         =>  __m( 'Left Column', 'NsPrintAdapter' ),
                            'value'         =>  $options->get( 'ns_pa_left_column' ),
                            'description'   =>  __m( 'Define the header for the left column', 'NsPrintAdapter' ),
                            'name'          =>  'ns_pa_left_column',
                        ], [
                            'type'          =>  'textarea',
                            'label'         =>  __m( 'Right Column', 'NsPrintAdapter' ),
                            'value'         =>  $options->get( 'ns_pa_right_column' ),
                            'description'   =>  __m( 'Define the header for the right column', 'NsPrintAdapter' ),
                            'name'          =>  'ns_pa_right_column',
                        ], [
                            'type'          =>  'textarea',
                            'label'         =>  __m( 'Receipt Footer', 'NsPrintAdapter' ),
                            'value'         =>  $options->get( 'ns_pa_receipt_footer' ),
                            'description'   =>  __m( 'This will always displays at the bottom of the receipt.', 'NsPrintAdapter' ),
                            'name'          =>  'ns_pa_receipt_footer',
                        ],
                    ]
                ]
            ]
        ];
    }
}