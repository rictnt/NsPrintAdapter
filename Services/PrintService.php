<?php
namespace Modules\NsPrintAdapter\Services;

use Illuminate\Support\Facades\View;
use App\Models\Order;
use App\Models\Register;
use App\Services\Options;

class PrintService
{
    public function getOrderReceipt( Order $order )
    {
        $printerName        =   ns()->option->get( 'ns_pa_printer' );
        $printerAddress     =   ns()->option->get( 'ns_pa_server_address' );
        $registerId         =   request()->query( 'cash-register' );

        if ( ! empty( $registerId ) ) {
            $cashRegister   =   Register::find( $registerId );

            if ( ! $cashRegister instanceof Register ) {
                throw new NotFoundException( __( 'Unable to find the requested cash register.' ) );
            }

            $printerName    =   $cashRegister->printer_name;
            $printerAddress =   $cashRegister->printer_address;
        }

        /**
         * @var Options;
         */
        $options            =   app()->make( Options::class );

        if ( empty( $printerName ) || empty( $printerAddress ) ) {
            if ( empty( $printerId ) ) {
                throw new NotAllowedException( __( 'Unable to retreive the receipt if no printer name and address is defined.' ) );
            } else {
                throw new NotAllowedException( __( 'Unable to retreive the receipt if no printer name and address is defined for the opened cash register.' ) );
            }
        }

        return [
            'content'       =>  ( string ) View::make( 'NsPrintAdapter::receipt.nps', [
                'order'         =>  $order,
                'printService'  =>  $this,
                'payments'      =>  collect( config( 'nexopos.pos.payments' ) )
                    ->mapWithKeys( fn( $payment ) => [ $payment[ 'identifier' ] => $payment[ 'label' ] ])
                    ->toArray(),
            ]),
            'printer'       =>  $printerName,
            'address'       =>  $printerAddress,
        ];
    }

    public function nexting( $values, $replacement = ' ', $limit = null, $ratio = 1 ) {
        if ( $limit == null ) {
            $limit  =   ns()->option->get( 'ns_pa_characters_limit', 48 );
        }
    
        $length         =   0;
        $countString    =   count( $values );
    
        foreach( $values as $val ) {
            $length     +=  ( count( preg_split( '//u', $val, null, PREG_SPLIT_NO_EMPTY ) ) * $ratio );
            // $length     +=  ( count( str_split( $val ) ) * $ratio );
        }
    
        $fill    =   '';
        for( $i = 0; $i < $limit - $length; $i++ ) {
            $fill    .=  $replacement;
        }
    
        if ( count( $values ) == 0 ) {
            return $fill;
        }
    
        $spaceBetweenValues     =   floor( $length / count( $values ) );
    
        $finalString    =   '';
        foreach( $values as $index => $value ) {
            if ( $index == $countString - 1 ) {
                $finalString   .=  $value;
            } else {
                $finalString    .=  $value . $fill;
            }
        }
    
        return $finalString;
    }
    
    public function buildingLines( $col1, $col2 ) {
        $col1_lines     =   preg_split ('/$\R?^/m', $col1);
        $col2_lines     =   preg_split ('/$\R?^/m', $col2);
        $finalBuild     =   [];
        
        /**
         * We would like to use the hight table number
         */
        for( $i = 0; $i < ( count( $col1_lines ) > count( $col2_lines ) ? count( $col1_lines ) : count( $col2_lines ) ); $i++ ) {
            $finalBuild[]   =   [ trim( @$col1_lines[$i] ), trim( @$col2_lines[$i] ) ];
        }
    
        return $finalBuild;
    }
    
    /** 
     * Text to EsCText
     * @param string
     * @return string[][]
     */
    public function textToEsc( $string )
    {
        $col1_lines     =   preg_split ('/$\R?^/m', $string);
        $finalBuild     =   [];
    
        /**
         * We would like to use the hight table number
         */
        for( $i = 0; $i < count( $col1_lines ) ; $i++ ) {
            $finalBuild[]   =   trim( @$col1_lines[$i] );
        }
    
        return $finalBuild;
    }
    
    public function __fill( $char = '-', $maxLetter ) {
    
        $finalString    =   '';
    
        for( $i = 0; $i < $maxLetter; $i++ ) {
            $finalString    .=  $char;
        }
    
        return $finalString;
    }
    
    /**
     * Populate a line with a string and fill 
     * with the place holder
     * @param string string to fill
     * @param int maxium letter
     * @param array config
     * @return string;
     */
    public function __populate( $string, $max, $config = [
        'align' =>  'left',
        'fill'  =>  ' '
    ]) {
        extract( $config );
        $strLen         =   strlen( $string );
        $toPopulate     =   $max - $strLen;
        return $string . $this->__fill( $fill, $toPopulate );
    }
    
    /**
     * Check if a string or an array
     * of string will overflow the provided with
     * @param array of row
     * @param int width per column
     * @param int max letter
     * @return int maximum row overflow
     */
    public function __willOverFlow( $row, $widthPerColumn, $maxLetter ) {
        /**
         * let's check if string
         * will overflow
         */
        $maximumRowOverflow     =   0;
    
        foreach( $row as $__index => $col ) {
            
            if( is_array( $col ) ) {
                $col    =   $this->__getRealColString( compact( 'col', '__index', 'widthPerColumn', 'maxLetter' ) );
            }
    
            /**
             * Make the placeholder length 
             * per column automatic
             */
            $placeholderLengthPerColumn     =   floor( ( $widthPerColumn[ $__index ] * $maxLetter ) / 100 );
            $maximumLines                   =   round( strlen( $col ) / $placeholderLengthPerColumn );
    
            
            /**
             * Reassign the maxium line only
             * if it's greater
             */
            $maximumRowOverflow     =   $maximumLines > $maximumRowOverflow ? $maximumLines : $maximumRowOverflow; 
        }
        
        return $maximumRowOverflow;
    }
    
    /**
     * Get Real row string, including extrat fields
     * @return string
     */
    public function __getRealColString( $data )
    {
        extract( $data );
    
        $resultString   =   '';
        foreach( $col as $colString ) {
            $result     =   $this->__populate( $colString, floor( ( $widthPerColumn[ $__index ] * $maxLetter ) / 100 ), [
                'align'     =>  'left',
                'fill'      =>  isset( $fillWith ) ? $fillWith : ' ',
            ]);
    
            $resultString    .=      $result;
        }
        return $resultString;
    }
    
    /**
     * render lines
     * @return string;
     */
    public function __renderLines( $data )
    {
        extract( $data );
        
        $colString  =   isset( $col ) ? $col : $colString;
    
        /**
         * Make the placeholder length 
         * per column automatic
         */
        $placeholderLengthPerColumn     =   floor( ( $widthPerColumn[ $__index ] * $maxLetter ) / 100 );
        
        if( strlen( $colString ) > $placeholderLengthPerColumn ) {
            $rawStr     =   ( substr( $colString, $rowId * ( $placeholderLengthPerColumn - 1 ), $placeholderLengthPerColumn - 1 ) );
            $rawStr     .=  ' '; // to add a space between the text and the next column
        } else if( $rowId === 0 && strlen( $colString ) <= $placeholderLengthPerColumn ) {
            $rawStr     =   trim( $colString );
        } else {
            $rawStr     =   '';
        }
    
        $str            =    $this->__populate( $rawStr, $placeholderLengthPerColumn, [
            'align'     =>  'left',
            'fill'      =>  ' '
        ]);
        return $str;
    }
    
    /**
     * Create toEscTable
     * @param array
     * @return string
     */
    public function toEscTable( $rawTable, $config = [
        'bodyLines'     =>  true,
        'maxLetter'     =>  150,
        'fillWith'      =>  ' ',
    ]) 
    {
        extract( $config );
    
        $totalColumns                   =   count( $rawTable[0] );
        $placeholderLengthPerColumn     =   ceil( $maxLetter / $totalColumns );
        $finalString                    =   '';
        $widthPerColumn                 =   [];
    
        foreach( $rawTable as $index => $row ) {
    
            
            /**
             * first row is the header
             */
            if( $index === 0 ) {
                
                $finalString    .=  $this->__fill( '-', $maxLetter ) . "\r\n";
    
                $totalStringPerCol  =   array_map( function( $col ) {
                    return strlen( $col[ 'title' ] );
                }, $row );
    
                $totalUsedString    =   array_sum( $totalStringPerCol );
                $maxDefinedWidth    =   0;
                $totalAutoWidth     =   0;
    
                foreach( $row as $__index => $col ) {
    
                    /**
                     * Save defined width 
                     * or count auto columns
                     */
                    if( is_numeric( @$col[ 'width' ] ) ) {
                        $maxDefinedWidth    +=  $col[ 'width' ];
                    } else {
                        $totalAutoWidth++;
                    }
                }
    
                /**
                 * let's calculate the auto
                 * width for columns
                 */
                $availableAutoWidth     =   100 - $maxDefinedWidth;
                $autoWidth              =   $totalAutoWidth === 0 ? 0 : floor( $availableAutoWidth / $totalAutoWidth );
    
                foreach( $row as $__index => $col ) {
                    if( $col[ 'width' ] === 'auto' ) {
                        $widthPerColumn[]   =   $autoWidth;
                    } else {
                        $widthPerColumn[]   =   floatval( $col[ 'width' ] );
                    }
                }
                
                foreach( $row as $__index => $col ) {
                    $str            =    $this->__populate( $col[ 'title' ], ( $widthPerColumn[ $__index ] * $maxLetter ) / 100, [
                        'align'     =>  @$col[ 'align' ] ?: 'left',
                        'fill'      =>  $fillWith
                    ]);
    
                    $finalString    .=    $str;
                }
    
                $finalString .=  "\r\n";
    
                $finalString    .=  $this->__fill( '-', $maxLetter ) . "\r\n";
                
            } else {
                
                /**
                 * let's check if string
                 * will overflow
                 */
                $maximumRowOverflow     =   $this->__willOverFlow( $row, $widthPerColumn, $maxLetter );
    
                /**
                 * According to the defined overflow
                 * let's populate the row
                 */
                for( $rowId = 0; $rowId <= $maximumRowOverflow; $rowId++ ) {
                    
                    $rendered   =   false;
    
                    foreach( $row as $__index => $col ) {
    
                        /**
                         * let's render each column and make sure 
                         * a column with an array is also rendered
                         */
                        if( is_array( $col ) ) {
                            $col    =   $this->__getRealColString( compact( 'col', 'widthPerColumn', 'maxLetter', 'fillWith', '__index' ) );
                        }
    
                        $rendered   =   __renderLines( compact( 'widthPerColumn', 'maxLetter', 'col', '__index', 'rowId', 'finalString' ) );
    
                        if( $rendered !== false ) {
                            $finalString    .=  $rendered ;
                        }
                    }
        
                    if( $rendered ) {
                        $finalString .= "\r\n";
                    }
                }
    
                if( $bodyLines ) {
                    $finalString    .=  $this->__fill( '-', $maxLetter ) . "\r\n";
                }
    
            }
    
            /**
             * if were closing the table
             * checking the last index
             */
            if( $index == count( $rawTable ) - 1 ) {
                $finalString    .=  $this->__fill( '-', $maxLetter ) . "\r\n";
            }
        }
    
        return $finalString;
    }
}