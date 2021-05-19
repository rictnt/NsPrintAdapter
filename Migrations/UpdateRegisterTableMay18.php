<?php
/**
 * Table Migration
 * @package  4.2.1
**/

namespace Modules\NsPrintAdapter\Migrations;

use App\Classes\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRegisterTableMay18 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        if ( Schema::hasTable( 'nexopos_registers' ) ) {
            Schema::table( 'nexopos_registers', function( Blueprint $table ) {
                if ( ! Schema::hasColumn( 'nexopos_registers', 'printer_name' ) ) {
                    $table->string( 'printer_name' )->nullable();
                }
                if ( ! Schema::hasColumn( 'nexopos_registers', 'printer_address' ) ) {
                    $table->string( 'printer_address' )->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        if ( Schema::hasTable( 'nexopos_registers' ) ) {
            Schema::table( 'nexopos_registers', function( Blueprint $table ) {
                if ( ! Schema::hasColumn( 'nexopos_registers', 'printer_name' ) ) {
                    $table->dropColumn( 'printer_name' );
                }
                if ( ! Schema::hasColumn( 'nexopos_registers', 'printer_address' ) ) {
                    $table->dropColumn( 'printer_address' );
                }
            });
        }
    }
}
