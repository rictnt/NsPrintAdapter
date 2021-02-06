<script src="{{ asset( 'modules/nsprintadapter/js/jquery.js' ) }}"></script>
<script>
const nsPrintAdapterOptions     =   {
    'printer_name'      :   '{{ ns()->option->get( "ns_pa_printer" ) }}',
    'server_address'    :   '{{ ns()->option->get( "ns_pa_server_address" ) }}'
};

document.addEventListener( 'DOMContentLoaded', () => {
    nsHooks.addAction( 'ns-order-submit-successful', 'ns-pa.catch-order', ( result ) => {
        console.log( result );
        nsHttpClient.get( `/api/nexopos/v4/ns-print-adapter/receipt/${result.data.order.id}` )
            .subscribe( result => {
                const { printer, content, address }  =   result;

                $.ajax( `${address}/api/print`, {
                    data        :   { printer, content },
                    type        :   'POST',
                    dataType 	:   'json',
                    success     :   ( result ) => {
                        console.log( result );
                    }
                });
            })        
    })
})
</script>