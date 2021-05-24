<?php
namespace Modules\NsPrintAdapter;

use Illuminate\Support\Facades\Event;
use App\Services\Module;

class NsPrintAdapterModule extends Module
{
    public function __construct()
    {
        parent::__construct( __FILE__ );
    }
}