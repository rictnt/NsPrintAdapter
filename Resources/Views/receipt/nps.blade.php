<{{ '?xml version="1.0" encoding="UTF-8"?' }}>
<document>
    <align mode="center">
        <bold>
            <text-line size="3:3">
            @if ( ns()->option->get( 'ns_pa_logotype' ) === 'image' )
                {{ ns()->option->get( 'ns_pa_logoshortcode' ) }}
            @else
                {{ ns()->option->get( 'ns_store_rectangle_logo', ns()->option->get( 'ns_store_name' ) ) }}
            @endif
            </text-line>
        </bold>
    </align>
    <line-feed></line-feed>
    <align mode="left">
        <?php foreach( $printService->buildingLines( 
            ns()->option->get( 'ns_pa_left_column', '' ),
            ns()->option->get( 'ns_pa_right_column', '' ),
        ) as $line ):?>
        <text-line><?php echo $printService->nexting( $line );?></text-line>
        <?php endforeach;?>
    </align>
    <line-feed></line-feed>
    <text>
        <text-line>{{ __( 'Products' ) }}</text-line>
        @foreach( $order->products as $product )
        <text-line><?php echo $printService->nexting([], '-');?></text-line>
        <text-line>
        <?php echo $printService->nexting([
            $product->name . ' (x' . $product->quantity . ')',
            ns()->currency->define( $product->total_price )
        ]);
        ?></text-line>
        @endforeach
    </text>
    <line-feed></line-feed>
    <text>
        <text-line><?php echo $printService->nexting([], '*');?></text-line>   
    </text>
    <bold>
        <text-line>
        <?php echo $printService->nexting([
            __( 'Sub Total' ),
            ns()->currency->define( $order->total )
        ]);?></text-line>
        <text-line><?php echo $printService->nexting([], '-');?></text-line>
        <text-line><?php echo $printService->nexting([
            __( 'Discount' ),
            ns()->currency->define( $order->discount )
        ]);?></text-line>
        
        <text-line><?php echo $printService->nexting([], '-');?></text-line>

        <text-line><?php echo $printService->nexting([
            __( 'Total' ),
            ns()->currency->define( $order->total )
        ]);?></text-line>
        <text-line><?php echo $printService->nexting([], '-');?></text-line>

        @foreach( $order->payments as $payment )
        <text-line><?php echo $printService->nexting([
            $payments[ $payment->identifier ],
            ns()->currency->define( $payment->amount )
        ]);?></text-line>
        @endforeach        
        
        <text-line><?php echo $printService->nexting([], '-');?></text-line>

        <text-line><?php echo $printService->nexting([
            __( 'Tendered' ),
            ns()->currency->define( $order->tendered )
        ]);?></text-line>
        <text-line><?php echo $printService->nexting([], '-');?></text-line>

    </bold>
    <align mode="center">
        <text-line size="2:2"><?php echo $printService->nexting([
            __( 'Change' ),
            ns()->currency->define( $order->total - $order->tendered )
        ]);?></text-line>
    </align>
    <text>
        <text-line><?php echo $printService->nexting([], '-');?></text-line>
    </text>
    <line-feed></line-feed>
    <align mode="center">
        <text-line>{{ $order->note }}</text-line>
    </align>
    <line-feed></line-feed>
    <align mode="center">
        <text-line>{{ ns()->option->get( 'ns_pa_receipt_footer' ) }}</text-line>
    </align>
    <paper-cut></paper-cut>
</document>