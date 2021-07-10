<script src="{{ asset( 'modules/nsprintadapter/js/jquery.js' ) }}"></script>
<script>
const nsPrintAdapterOptions     =   {
    'printer_name'      :   '{{ ns()->option->get( "ns_pa_printer" ) }}',
    'server_address'    :   '{{ ns()->option->get( "ns_pa_server_address" ) }}'
};

class nsPaPrint {
    constructor() {

        /**
         * Will handle print job if
         * the print gateway is nps_legacy
         */
        nsHooks.addFilter( 'ns-order-custom-print', 'ns-pa.catch-printing', ({ order_id, gateway, printed }) => {
            if ( gateway === 'nps_legacy' ) {
                this.print( order_id );
                printed     =   true;  
            }

            return { order_id, gateway, printed };
        });
    }
    
    print( order_id ) {
        const registerId    =   POS.get( 'register' ) ? POS.get( 'register' ).id : null;
        nsHttpClient.get( `/api/nexopos/v4/ns-printadapter/${order_id}/print-data?cash-register=${registerId}` )
            .subscribe( (result ) => {
                const { printer, content, address }  =   result;

                const data  =   new FormData;

                data.set( 'printer', printer );
                data.set( 'content', content );
                
                const oReq = new XMLHttpRequest();

                oReq.addEventListener( "load", ( e ) => {
                    nsSnackBar.success( 'The print job has been submitted.' )
                        .subscribe();
                });
                oReq.addEventListener( 'error', () => {
                    return nsSnackBar.error( __( 'An unexpected error has occured while printing.' ) ).subscribe();
                });
                oReq.open( "POST",  `${address}/api/print` );
                oReq.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
                oReq.send(JSON.stringify({ printer, content }));

            }, ( error ) => {
                nsSnackBar.error( error.message || __( 'An unexpected error has occured while retreiving the receipt.' ) ).subscribe();
            });
    }
}

let nsPaPrintObject;
document.addEventListener( 'DOMContentLoaded', () => {
    nsPaPrintObject     =   new nsPaPrint;
})
</script>