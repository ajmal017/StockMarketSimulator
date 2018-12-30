<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Tools\CacheDrive;
use App\Tools\ShareTools;
use App\Tools\MarketTimeZone;
use App\Investor;
use App\Market;
use App\Index;
use App\Config;
use App\Share;
use App\Currency;
use App\Event;
use Session;
use Cache;
use Response;

class EventController extends Controller
{
    protected $config  = array();
    protected $market = array();
    protected $index = array();
    protected $currency = array();

    //protected $lastevents = array();
    protected $shownum = 10;

    public function __construct($iid = null){
    	//$this->middleware('auth');
    	//$this->middleware('cache');
        $this->config = Cache::get('global_config');
        $this->market = Cache::get('market');
        $this->index = Cache::get('index');
        $this->currency = Cache::get('currency');   		

    }

    public static function show_events($iid = null){
  		if(!$iid){
			return Event::orderBy('created_at', 'DESC')->where('type', 1)->take($this->shownum)->get();
  		}
  		else{
  			return Event::orderBy('created_at', 'DESC')->where('bywhom', $iid)->get();
		}
    	
    }

    public static function create_event($arr){
    	$event = Event::create($arr);
    	//return $event;
    	//$event->fill($arr);
    	return $event;
    }
}
