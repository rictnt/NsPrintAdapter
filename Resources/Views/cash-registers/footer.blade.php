<script src="{{ asset( 'modules/nsprintadapter/js/jquery.js' ) }}"></script>
<script>
const nsLoadPrinters      =   ({ url, instance }) => {
    $.ajax( `${url}/api/printers`, {
        success: ( result ) => {
            instance.form.tabs.general.fields.forEach( field => {
                if ( field.name === 'printer_name' ) {
                    field.options   =   result.map( printer => {
                        return {
                            label: printer.name,
                            value: printer.name
                        }
                    })
                }
            });
        },
        error: () => {
            nsSnackBar.error( __( 'An error has occured while loading the printer. Make sure the server URL is correct.' ) ).subscribe();
        }
    });
}

document.addEventListener( 'DOMContentLoaded', () => {
    nsHooks.addAction( 'ns-crud-form-loaded', 'ns-registers', ( instance ) => {
        setTimeout(() => {
            nsLoadPrinters({ instance, url : document.querySelector( '#printer_address' ).value });
            document.querySelector( '#printer_address' ).addEventListener( 'change', ( e ) => {
                const url   =  e.target.value;
                if ( url.length > 0 ) {
                    nsLoadPrinters({ url, instance });
                }
            })
        }, 100 );
    })
});
</script>