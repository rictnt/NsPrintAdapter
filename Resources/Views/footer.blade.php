<script>
document.addEventListener( 'DOMContentLoaded', () => {
    nsHooks.addAction( 'ns-settings-change-tab', 'ns-pa-settings', ({ tab, instance }) => {
        setTimeout(() => {
            const input   =   document.querySelector( '#ns_pa_server_address' );

            if ( input ) {
                const url   =   input.value;

                if ( url.length > 0 ) {
                    var oReq = new XMLHttpRequest();
                    oReq.addEventListener("load", ( e ) => {
                        const result    =   JSON.parse( e.target.responseText );
                        instance.form.tabs.general.fields.forEach( field => {
                            if ( field.name === 'ns_pa_printer' ) {
                                field.options   =   result.map( printer => {
                                    return {
                                        label: printer.name,
                                        value: printer.name
                                    }
                                })
                            }
                        })
                    });
                    oReq.open("GET",  `${url}/api/printers` );
                    oReq.send();
                }
            }

        }, 100 );
    })
});
</script>