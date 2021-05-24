<?php
use App\Models\Order;
use App\Classes\Hook;
use App\Services\OrdersService;

$orderService   =   app()->make( OrdersService::class );
?>

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
            $orderService->orderTemplateMapping( 'ns_pa_left_column', $order ),
            $orderService->orderTemplateMapping( 'ns_pa_right_column', $order ),
        ) as $line ):?>
        <text-line><?php echo $printService->nexting( $line );?></text-line>
        <?php endforeach;?>
    </align>
    <line-feed></line-feed>
    <text>
        <text-line>{{ __m( 'Products', 'NsPrintAdapter' ) }}</text-line>
        @foreach( $order->products as $product )
        <text-line><?php echo $printService->nexting([], '-');?></text-line>
        <text-line>
        <?php echo $printService->nexting([
            $product->name . ' (x' . $product->quantity . ')',
            ns()->currency->define( $product->total_price )
        ]);
        ?></text-line>
        <?php echo Hook::filter( 'ns-pa-receipt-after-product', '', $product );?>
        @endforeach
    </text>
    <line-feed></line-feed>
    <text>
        <text-line><?php echo $printService->nexting([], '*');?></text-line>   
    </text>
    <bold>
        <text-line>
        <?php echo $printService->nexting([
            __m( 'Sub Total', 'NsPrintAdapter' ),
            ns()->currency->define( $order->total )
        ]);?></text-line>

        @if ( $order->discount > 0 )
        <text-line><?php echo $printService->nexting([], '-');?></text-line>        
        <text-line><?php echo $printService->nexting([
            __m( 'Discount', 'NsPrintAdapter' ),
            ns()->currency->define( $order->discount )
        ]);?></text-line>
        @endif
        

        @if ( $order->tax_value > 0 )
        <text-line><?php echo $printService->nexting([], '-');?></text-line>
        <text-line><?php echo $printService->nexting([
            __m( 'Taxes', 'NsPrintAdapter' ),
            ns()->currency->define( $order->tax_value )
        ]);?></text-line>
        @endif

        <text-line><?php echo $printService->nexting([], '-');?></text-line>

        <text-line><?php echo $printService->nexting([
            __m( 'Total', 'NsPrintAdapter' ),
            ns()->currency->define( $order->total )
        ]);?></text-line>

        @if ( ns()->option->get( 'ns_pa_payment_summary', 'yes' ) === 'yes' )
            @foreach( $order->payments as $payment )
            <text-line><?php echo $printService->nexting([], '-');?></text-line>
            <text-line><?php echo $printService->nexting([
                $payments[ $payment->identifier ],
                ns()->currency->define( $payment->value )
            ]);?></text-line>
            @endforeach        
        @endif
        
        <text-line><?php echo $printService->nexting([], '-');?></text-line>

        @if ( $order->tendered > 0 )
        <text-line><?php echo $printService->nexting([
            __m( 'Tendered', 'NsPrintAdapter' ),
            ns()->currency->define( $order->tendered )
        ]);?></text-line>
        <text-line><?php echo $printService->nexting([], '-');?></text-line>
        @endif

    </bold>
    <align mode="center">
        <text-line size="1:2"><?php echo $printService->nexting([
            $order->payment_status === Order::PAYMENT_PAID ? __m( 'Change', 'NsPrintAdapter' ) : __m( 'Due', 'NsPrintAdapter' ),
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