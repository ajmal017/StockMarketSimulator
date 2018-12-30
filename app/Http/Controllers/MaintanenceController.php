<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Share;
use App\Market;
use App\Index;
use Session;
use Cache;

class MaintanenceController extends Controller
{
    public function correct_us_shares_market(){

		$max = 15;
		$total = DB::table('shares')->where('atStockMarket', '=', 'dow')
									->orWhere('atStockMarket', '=', 'nas')
									->count();

		
		$pages = ceil($total / $max);

		for ($i = 1; $i < ($pages + 1); $i++) {
		    $offset = (($i - 1)  * $max);
		    $start = ($offset == 0 ? 0 : ($offset + 1));
		    $us_shares = DB::table('shares')
		    	->where('atStockMarket', '=', 'dow')
		    	->orWhere('atStockMarket', '=', 'nas')
		    	->skip($start)
		    	->take($max)->get();
		    $temp = array();
		    foreach($us_shares as $s)
		    	$temp[] = 'gb_' . $s->stock_index;
		    
		    $q_str = implode(',', $temp);	
		} 					
    }	
}
