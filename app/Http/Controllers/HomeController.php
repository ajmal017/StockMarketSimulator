<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tools\MarketTimeZone;
use Cache;
use Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    use MarketTimeZone;

    protected $local_times, $open_times;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('cache');
        $this->local_times = $this->get_local_time();
        $this->open_times = config('share.MarketOpenTime');        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $configs = Cache::get('global_config');
        $markets = Cache::get('market');
        $index = Cache::get('index');

        $open_markets = $this->opened_markets($this->open_times, $this->local_times);
        
        
        return view('pages.home')->withConfig($configs)->withMarket($markets)->withIndex($index)->with('open_markets', $open_markets)->withTitle('');
    }
}
