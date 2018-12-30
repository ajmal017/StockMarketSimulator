<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Tools\MarketTimeZone;
use App\Tools\CacheDrive;
use App\Tools\ShareTools;
use App\Tools\ShowTools;
use Carbon\Carbon;
use App\Investor;
use App\Market;
use App\Index;
use App\Config;
use App\Share;
use App\Currency;
use App\Services\MarketRankService;
use Session;
use Cache;
use Response;
//use Carbon\Carbon;


class RankController extends Controller
{
	use CacheDrive;	
	use ShareTools;
    use MarketTimeZone;

    protected $config  = array();
    protected $market = array();
    protected $index = array();
    protected $currency = array();
    protected $extra_share_config = array();
    //protected $local_times, $open_times;
 	protected $local_times, $open_times, $market_platform, $market_platform_price_query, $fullcode_prefix, $active_shares, $no_of_active_shares, $active_shares_count, $make_rank_date, $make_rank_offset, $chunk_unit;
 	
    private $marketrank_service;

   //public function __construct(MarketRankService $marketrank_service){
   public function __construct(){
    	//$this->middleware('auth');
    	$this->middleware('cache');
        $this->config = Cache::get('global_config');
        $this->market = Cache::get('market');
        $this->index = Cache::get('index');
        $this->currency = Cache::get('currency'); 
        $this->extra_share_config = Cache::get('extra_share_config');
        //dd($this->extra_share_config);
        //dd($this->currency);
        //dd(config('share'));
        $this->local_times = $this->get_local_time();

        $this->open_times = config('share.MarketOpenTime');
        //dd($this->open_times);
        $this->market_platform = config('share.market_platform');
        $this->market_platform_price_query = config('share.market_platform_price_query');
        $this->fullcode_prefix = config('share.fullcode_prefix');
        $this->active_shares = $this->get_active_shares();
        // dd($this->active_shares);
        $this->no_of_active_shares = $this->get_active_shares_no();

        $this->active_shares_count = $this->get_active_shares_count();

        $this->make_rank_date = $this->get_make_rank_date();
        //dd($this->active_shares);
        $this->make_rank_offset = $this->get_make_rank_offset();
        //dd($this->active_shares);
        $this->chunk_unit = 10;
        /*
        $temp = DB::table('shares')->leftJoin('have_shares', 'shares.sid', '=', 'have_shares.sid')
                                ->where('have_shares.amount', '>', 0)
                                ->select(DB::raw('shares.sid AS sid, shares.stock_index AS stock_index, shares.name AS name, shares.atStockMarket AS market, have_shares.amount AS amount'))
                                ->get();
        */
        //$this->active_shares = Cache::get('')
        //dd($this->no_of_shares);
        //dd($this->active_shares); 
        //dd($this->no_of_active_shares);
        //dd($this->active_shares_count);
        //dd($this->get_active_shares());
        //dd($this->get_active_shares_no());                      
        //dd($this->get_active_shares_count());

        //$this->marketrank_service = $marketrank_service;
        //dd('got it!');
        //dd($this->active_shares);
    }

    public function make_american_rank(){

        if(!empty($this->active_shares) && isset($this->active_shares['us'])){
            $chunk_shares = collect($this->active_shares['us'])->chunk($this->chunk_unit)->toArray();
            foreach($chunk_shares as $chunk_share){
                
            }    
        }
            
    }

    public function make_chinese_rank(){
        if(!empty($this->active_shares)){
            if(isset($this->active_shares['sh'])){

            }

            if(isset($this->active_shares['sz'])){

            }
        }

    }

    public function make_hk_rank(){
        if(!empty($this->active_shares) && isset($this->active_shares['hk'])){

        }

    }

    public function getrank(){
        dd($this->active_shares); 
        //dd($this->no_of_active_shares);
        //dd($this->active_shares_count);
    }

    //test
    public function test_add_active_shares(){
        $myid = Session::get('iid');
        if($myid){
            $myself = Investor::find($myid);
            $myshares = $myself->shares()->wherePivot('amount', '>', 0)->first();    
            $mymarket = $myshares->atStockMarket;
            $this->add_active_shares($mymarket, $myshares);
        }
    }
}
