<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tools\MarketTimeZone;
use Carbon\Carbon;
use App\Event;
use App\Investor;
use Session;
use Cache;
use Response;

class PagesController extends Controller
{
    use MarketTimeZone;

    protected $local_times, $open_times;

    public function __construct(){
    	$this->middleware('cache');
        $this->local_times = $this->get_local_time();
        $this->open_times = config('share.MarketOpenTime');
    }	
    public function index(){
        $configs = Cache::get('global_config');
        $markets = Cache::get('market');
        $index = Cache::get('index');
        $avator = [];
        if(Session::get('iid'))
            $avator = Investor::find(Session::get('iid'))->avator()->first();
        $open_markets = $this->opened_markets($this->open_times, $this->local_times);
        $events = Event::orderBy('updated_at', 'desc')->where('type', 1)->take(5)->get();
        $last_updated = '';

        if(count($events) > 0){
            $last_updated = Event::orderBy('updated_at', 'desc')->where('type', 1)->max('updated_at');
            $events = $events->each(function($event, $k){
                $event->event = stripcslashes($event->event);
            });
        }
        
        //dd($this->local_times);
        /**
        $test = array();
        foreach($this->local_times as $mid => $mdata)
            $test[$mid] = date('Y-m-d H:i:s', $mdata);
        dd($test);
        **/
        //dd($open_markets);

        return view('pages.home')->withTitle('')->withConfig($configs)->withMarket($markets)->withIndex($index)->with('open_markets', $open_markets)->withEvents($events)->with('lastupdated', $last_updated)->withAvator($avator);    	
    }

    public function get_last_events($last_updated){
        $ret = array();
        $last_updated = base64_decode($last_updated);
        $ret['receive_time'] = $last_updated;
        //$events = Event::orderBy('updated_at', 'desc')->where('type', 1)->where('updated_at', '>', $last_updated)->get();
        $events = Event::where('updated_at', '>', $last_updated)->where('type', 1)->orderBy('updated_at', 'desc')->get();
        if(count($events) > 0){
            $ret['last_updated'] = Event::orderBy('updated_at', 'desc')->where('type', 1)->max('updated_at');
            $events = $events->each(function($event, $k){
                $event->event = stripcslashes($event->event);
            });
            $ret['events'] = $events->toArray();
        }
        //$ret['last_updated'] =  Carbon::now()->toDateTimeString();        
        
        //return Response::json($last_updated);
        return Response::json($ret);
    }	
}
