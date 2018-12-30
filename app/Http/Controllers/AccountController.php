<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Tools\CacheDrive;
use App\Tools\MarketTimeZone;
use App\Share;
use App\Market;
use App\Index;
use App\Investor;
use App\Avator;
use App\Profile;
use Session;
use Cache;
use Response;

class AccountController extends Controller
{
	use CacheDrive;
    use MarketTimeZone;

    protected $config  = array();
    protected $market = array();
    protected $index = array();
    protected $currency = array();
    protected $local_times, $open_times, $market_platform, $market_platform_price_query, $fullcode_prefix;

    public function __construct(){
    	$this->middleware('auth');
    	$this->middleware('cache');
        $this->config = Cache::get('global_config');
        $this->market = Cache::get('market');
        $this->index = Cache::get('index');
        $this->currency = Cache::get('currency'); 
        $this->local_times = $this->get_local_time();
        $this->open_times = config('share.MarketOpenTime');
        $this->market_platform = config('share.market_platform');
        $this->market_platform_price_query = config('share.market_platform_price_query');
        $this->fullcode_prefix = config('share.fullcode_prefix');                	 
    }
    
    public function myaccount(){
    	$myid = Session::get('iid');

    	$myself = Investor::find($myid);
    	$asset['account'] = $myself->coins;
        $avator="";
        if(Session::get('iid'))
    	   $avator = $myself->avator()->first();
    	$shares = array();

        $myshares = $myself->shares()->wherePivot('amount', '>', 0)->get();

        foreach($myshares as $share){
            $shares[$share->sid] = $share->pivot->toArray();
            //$shares[$share->sid] = $share->toArray();
            $shares[$share->sid]['name'] = $share->name;
            $shares[$share->sid]['atStockMarket'] = $themarket = $share->atStockMarket;
            $shares[$share->sid]['code'] = $share->stock_index;
            $shares[$share->sid]['fullcode'] = $this->fullcode_prefix[$themarket] . $share->stock_index;
            
            foreach($this->market_platform as $platform => $markets){
                if(in_array($themarket, $markets)){
                    $shares[$share->sid]['platform'] = $platform;
                    break;
                }    
            }
                
        }
        /*
        $shares = $myself->shares()->get()->filter(function($share_index, $share){
            return $share->pivot->amount > 0;
        });
        */
        //dd($shares);
        /*
    	foreach($myself->shares()-> as $share){
    		$shares[$share->stock_index] = $share->toArray();
    	}
        */
        $open_markets = $this->opened_markets($this->open_times, $this->local_times);
        
        return view('account.myaccount')->withMyself($myself)->withAvator($avator)->withShares($shares)->withMarket($this->market)->withIndex($this->index)->withCurrency($this->currency)->withConfig($this->config)->with('open_markets', $open_markets)->withTitle(' | 股票账号');   	
    }

    public function exchange($cid, Request $request){
    	$ret = array();
    	//dd($request);
    	//dd($newcoins);
 		if ($request->ajax()){
	    	$myself = Investor::find(Session::get('iid'));
	    	$old = $myself->bind_to;
	    	$rate = $this->get_exchange_rate($old, $cid);
	    	$newcoins = round(floatval($myself->coins) * floatval($rate), 2);
	    	$myself->coins = $newcoins;
	    	$myself->bind_to = $cid;
	    	$myself->save();
	    	$ret['newcoins'] = $newcoins;
 		}
 		else
 			$ret['failure'] = 'ajax failed';
    	return Response::json($ret);
    }

    public function getMyInfo(){
        $ret = array();
        $myid = Session::get('iid');
        $ret['personal'] = Investor::find($myid)->toArray();
        $myshares = Investor::find($myid)->shares()->wherePivot('amount', '>', 0)->get();
        $shares = array();

        if(count($myshares) > 0){
            //$ret['shares'] = $myshares->toArray();
            //$shares = $myshares->toArray();
            foreach($myshares as $share){
                $shares[$share->sid] = $share->pivot->toArray();
                $shares[$share->sid]['name'] = $share->name;
                $shares[$share->sid]['atStockMarket'] = $themarket = $share->atStockMarket;
                $shares[$share->sid]['code'] = $share->stock_index;
                $shares[$share->sid]['fullcode'] = $this->fullcode_prefix[$themarket] . $share->stock_index;

                foreach($this->market_platform as $platform => $markets){
                    if(in_array($themarket, $markets)){
                        $shares[$share->sid]['platform'] = $platform;
                        break;
                    }    
                }                                
            }
            $ret['shares'] = $shares;
        }

        return json_encode($ret);
    }

    public function getQueryHeadByPlatform(){
        $ret = config('share.market_platform_price_query');
        return json_encode($ret);
    }
    /*
    public function get_update_query(){
        $myid = Session::get('iid');
        $ret = '';
        if($myid){
            $myshares = Investor::find($myid)->shares()->wherePivot('amount', '>', 0)->get();

        }
        return $ret;
    }
    */

    protected function get_exchange_rate($from, $to){
    	$url = $this->currency[$to]['rate_query_url'] . time() . 'list=fx_s' . $from . $to;
    	$str = mb_convert_encoding(file_get_contents($url), 'UTF-8', 'GBK');
    	$data = explode(',', $str);
    	return $data[8];
    }

}
