<script src="{{ asset( 'modules/nsprintadapter/js/jquery.js' ) }}"></script>
<script>

document.addEventListener( 'DOMContentLoaded', () => {
    nsHooks.addAction( 'ns-settings-loaded', 'ns-pa-settings', ( instance ) => {
        setTimeout(() => {
            const url   =   document.querySelector( '#ns_pa_server_address' ).value;

            if ( url.length > 0 ) {
                $.ajax( `${url}/api/printers`, {
                    success: ( result ) => {
                        instance.form.tabs.general.fields.forEach( field => {
                            if ( field.name === 'ns_pa_printer' ) {
                                field.options   =   result.map( printer => {
                                    return {
                                        label: printer.name,
                                        value: printer.driverName
                                    }
                                })
                            }
                        })
                    }
                });
            }
        }, 100 );
    })
});
</script>