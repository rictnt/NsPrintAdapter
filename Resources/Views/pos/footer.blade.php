<script src="{{ asset( 'modules/nsprintadapter/js/jquery.js' ) }}"></script>
<script>
const nsPrintAdapterOptions     =   {
    'printer_name'      :   '{{ ns()->option->get( "ns_pa_printer" ) }}',
    'server_address'    :   '{{ ns()->option->get( "ns_pa_server_address" ) }}'
};

class nsPaPrint {
    constructor() {
        nsHooks.addAction( 'ns-order-submit-successful', 'ns-pa.catch-order', ( result ) => {
            this.print( result.data.order.id );
        });

        nsHooks.addAction( 'ns-pos-pending-orders-refreshed', 'ns-pa.order-refreshed', ( orders ) => {
            setTimeout(() => {
                $( '.buttons-container' ).prepend( 
                    `<button class="print-button text-white bg-indigo-400 outline-none px-2 py-1"><i class="las la-print"></i> {{ __( 'Print' ) }}</button>`
                );

                $( '.print-button' ).bind( 'click', function() {
                    const orderID   =   $(this).closest( '[data-order-id]' ).data( 'order-id' );
                    nsPaPrintObject.print( orderID );
                });
            }, 100 );
        })
    }
    
    print( order_id ) {
        nsHttpClient.get( `/api/nexopos/v4/ns-print-adapter/receipt/${order_id}` )
            .subscribe( result => {
                const { printer, content, address }  =   result;

                $.ajax( `${address}/api/print`, {
                    data        :   { printer, content },
                    type        :   'POST',
                    dataType 	:   'json',
                    success     :   ( result ) => {
                        nsSnackBar.success( 'The print job has been submitted.' )
                            .subscribe();
                    }
                });
            })  
    }
}

let nsPaPrintObject;
document.addEventListener( 'DOMContentLoaded', () => {
    nsPaPrintObject     =   new nsPaPrint;
})
</script>