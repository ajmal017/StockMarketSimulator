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
use App\Tools\ShowTools;
use App\Investor;
use App\Market;
use App\Index;
use App\Config;
use App\Share;
use App\Currency;
use App\Avator;
use Session;
use Cache;
use Response;
use App\Http\Controllers\EventController;

class TransactionController extends Controller{

	use CacheDrive;	
	use ShareTools;
	use MarketTimeZone;
	use ShowTools;

    //protected $config  = array();
    //protected $market = array();
    //protected $index = array();
    //protected $currency = array();
    protected $predata = array();
    protected $location;

    public function __construct(){
    	$this->middleware('auth');
    	$this->middleware('cache');
        $this->predata['config'] = Cache::get('global_config');
        $this->predata['market'] = Cache::get('market');
        $this->predata['index'] = Cache::get('index');
        $this->predata['currency'] = Cache::get('currency'); 
        $this->predata['fullcode_prefix'] =  config('share.fullcode_prefix');
        $this->predata['show_share_chart'] = config('share.show_share_chart');
        $this->predata['has_bar_chart'] = config('share.has_bar_chart');
        $this->predata['share_chart_platform'] = config('share.share_chart_platform');
        $this->predata['share_chart_url_head'] = config('share.share_chart_url_head');
        $this->predata['unit_in_word'] = config('share.unit_in_word');
        $this->predata['volume_unit'] = config('share.volume_unit');
        $this->predata['local_times'] = $this->get_local_time();
        $this->predata['open_times'] = config('share.MarketOpenTime');
        $this->predata['trading_with_delay'] = config('share.trading_with_delay');

        $this->location = config('share.MarketLocationMap');
    }

    public function showtradeform($thismarket, $code, Request $request){
    	  
    	 // dd($request);	
   		//$themarket = Market::where('market_index', $thismarket)->first();  

        if($thismarket == 'us'){
            $tempmarket = 'nas'; //NASDAQ和NYSE的参数基本一样
        }
        else
            $tempmarket = $thismarket;

    	$themarket = $this->predata['market'][$tempmarket];

        //if($this->location[$thismarket] == 'us')
            //$code = strtoupper($code);
   		$share = Share::where('stock_index', strtoupper($code))->where('atStockMarket', $thismarket)->first();
        //else
            //$share = Share::where('stock_index', $code)->first();

   		if (!$share){
   			$share = new Share;
   			$platform = $this->get_platform($thismarket);

            //dd($platform);
			if ($platform == 'sina')
				$query_url = $themarket['query_url_head'] . strtolower($this->get_fullcode($thismarket, $code));

            //dd($query_url);
			$info = $this->get_share($query_url, $tempmarket, $platform);
			$share->stock_index = strtoupper($code);
			$share->name = $info['name'];
			$share->atStockMarket = $thismarket;
			$share->status = 1;
			$share->save();
   		}

   		//$sid = $share->sid;

    	$title = ' | ' . $share->name . '交易';
    	$this->predata['share'] = $share->toArray();
    	$this->predata['thismarket'] = $tempmarket;
    	$this->predata['code'] = strtolower($code);
    	$this->predata['myself'] = $request->user()->toArray();

    	$open_markets = $this->opened_markets($this->predata['open_times'], $this->predata['local_times']);
    	//$countdown = $this->countdown($thismarket, $open_markets, $this->predata['open_times']);
    	$this->predata['open_markets'] = $open_markets;

    	$avator = Investor::find(Session::get('iid'))->avator()->first();
    	$this->predata['avator_url'] = '/avator/' . $avator->belongsTo . '/' . base64_encode($avator->filename);

    	$myinfo = Investor::find(Session::get('iid'));

    	$myshares = $myinfo->shares()->get();
    	$sharecount = 0;
    	$sharearr = array();
    	if(!empty($myshares)){
    		foreach($myshares as $theshare){
    			if(intval($theshare->pivot->amount) > 0){
    				$sharearr[] = $theshare->sid;
    				$sharecount++;
    			}
    		}
    		$this->predata['myshares_count'] = $sharecount;    		
    		$this->predata['myshares'] = $sharearr;

    	}

    	if($myinfo && $myinfo->shares()->wherePivot('sid', $share->sid)->exists()){
    		$thisshare = $myinfo->shares()->wherePivot('sid', $share->sid)->first();  		

    		if(intval($thisshare->pivot->amount) > 0 && $thisshare->pivot->buying_at){
    			$this->predata['thisshare'] = $thisshare->pivot;
    			if(isset($this->predata['trading_with_delay'][$thismarket]) && $thisshare->pivot->buying_at){
    				$addhours = intval($this->predata['trading_with_delay'][$thismarket]);
    				$expired = Carbon::parse($thisshare->pivot->buying_at)->addHours($addhours); 
    				$now = Carbon::createFromTimestamp($this->predata['local_times'][$thismarket]);
    				if($now->lt($expired)){
    					$this->predata['countdown'] = $now->diffInSeconds($expired);
    				}
    				else{
    					$this->predata['countdown'] = 0;
    				}
                }
  			}  				
    	}
    	//$trading_at = Carbon::createFromTimestamp($this->predata['local_times'][$thismarket])->toDateTimeString();
    	//dd($trading_at);
    	$this->predata['csrf_token'] = csrf_token();

        $avator="";
        if(Session::get('iid'))
            $avator = Investor::find(Session::get('iid'))->avator()->first();

    	return view('transaction.showtradeform')->withTitle($title)->withMarket($this->predata['market'])->withIndex($this->predata['index'])->withConfig($this->predata['config'])->withCurrency($this->predata['currency'])->withPredata(json_encode($this->predata))->with('open_markets', $open_markets)->withAvator($avator);
    }

    public function ajax_getshare($thismarket, $code){
    	$themarket = $this->predata['market'][$thismarket];
    	$platform = $this->get_platform($thismarket);
    	$ret = '';
    	if($platform == 'sina')
    		$query_url = $themarket['query_url_head'] . $this->get_fullcode($thismarket, $code);

    	if($query_url)
    		$ret = $this->get_share($query_url, $thismarket, $platform); 

    	return Response::json($ret);
    }

    //key_word should be encoded
    public function searchshares($location, $key_word){
    	$token = time();
    	if($location == 'china'){
	    	
	    	$url = "http://suggest3.sinajs.cn/suggest/type=11,12,13,14,15&key=" . $key_word . "&name=suggestdata_" . $token;
	    	$json_content = mb_convert_encoding(file_get_contents($url), 'UTF-8', 'GBK');
	    	$temp = explode("=", $json_content);
	    	$res = $temp[1];

	    	$pattern = "/\"|\";" .  '/';
	    	$res = preg_replace($pattern, '', $res);
	    	

	    	$temp = explode(";", $res);
	    	
	    	array_pop($temp);

	    	$ret = array();
	    	foreach($temp as $t){
	    		if ($t){
	    			$temp2 = explode(',', $t);
	    			//dd($temp2[3]);
	    			$index = $temp2[3];
	    			//dd($index);
	    			$ret[$index]['name']  = $temp2[4];
	    			$ret[$index]['code'] = $temp2[2];
	    			$ret[$index]['fullcode'] = $temp2[3];
	    		}
	    		
	    	}
    	}
    	else if($location == 'us'){
    		$url = "http://suggest3.sinajs.cn/suggest/type=41,42&key=" . $key_word . "&name=suggestdata_" . $token;
	    	$json_content = mb_convert_encoding(file_get_contents($url), 'UTF-8', 'GBK');
	    	$temp = explode("=", $json_content);
	    	$res = $temp[1];

	    	$pattern = "/\"|\";" .  '/';
	    	$res = preg_replace($pattern, '', $res);    		
	    	$temp = explode(";", $res);
	    	
	    	array_pop($temp);

	    	$ret = array();
	    	foreach($temp as $t){
	    		if ($t){
	    			$temp2 = explode(',', $t);
	    			//dd($temp2[3]);
	    			$index = $temp2[2];
	    			//dd($index);
	    			$ret[$index]['name']  = $temp2[4];
	    			//$ret[$index]['cname'] = $temp2[4];
	    			$ret[$index]['code'] = $temp2[2];
	    			$ret[$index]['fullcode'] = 'us' . $temp2[2];
	    		}
	    		
	    	}

	    	
    	}
    	//dd(json_encode($ret));
    	return Response::json($ret);
    }

    public function get_openmarkets(){
    	$ret = $this->opened_markets($this->predata['open_times'], $this->predata['local_times']);
    	return Response::json($ret);
    }

   public function tradeshare($tradetype, Request $request){
    	$accepted_type = ['buy', 'sell'];
    	$ret = array();
    	$ret['data'] = array();
    	$ret['errors'] = '';
    	    	
    	if(!in_array($tradetype, $accepted_type)){
    		$ret['errors'] = 'Not accepted trade type: ' . $tradetype;
    		return Response::json($ret);
    	}
    	
    	$data = $request->all();
    	
		$validator = Validator::make($data, [
			'sid' => 'required|integer',
			'iid' => 'required|integer',
			'thismarket' => 'required',			
			'amount' => 'required|integer',
			'price' => 'required|numeric',
		]);    	

		if($validator->fails()){
			$errors = implode(', ', $validator->errors()->all());
			$ret['errors'] = $errors;
			return Response::json($ret);
		}

		$myinfo = Investor::find($data['iid']);
		$hasShare = false;
		$soldOut = true;

        $themarket = $data['thismarket'];

        if($themarket == 'nas' || $themarket == 'ny')
            $themarket = 'us';

		//$data['status']['hasShare'] = $hasShare;
		//$data['status']['soldOut'] = $soldOut;
		//return Response::json($data);

		if($myinfo->shares()->wherePivot('sid', $data['sid'])->exists()){
			$hasShare = true;
			$myshare = $myinfo->shares()->wherePivot('sid', $data['sid'])->first();
			if(intval($myshare->pivot->amount) > 0)
				$soldOut = false;
		}
		else{
			//$myshare = new Share;
            $myshare = Share::where('sid', $data['sid'])->first();
        }



		if($myinfo->bind_to != $this->predata['market'][$data['thismarket']]['allowed_currency']){
			$ret['errors'] = '绑定货币不符';
			return Response::json($ret);
		}

		if(floatval($data['price']) == 0){
			$ret['errors'] = '竞买价/竞卖价为0';
			return Response::json($ret);
		}	
		elseif (intval($data['amount']) == 0){
			$ret['errors'] = '竞买量/竞卖量为0';
			return Response::json($ret);   			
		}

		//$trading_at = Carbon::now()->toDateTimeString();
		$trading_at = Carbon::createFromTimestamp($this->predata['local_times'][$data['thismarket']])->toDateTimeString();
		//$data['status']['hasShare'] = $hasShare;
		//$data['status']['soldOut'] = $soldOut;
		//$data['status']['sharename'] = $myshare->name;
		//$data['status']['shareamount'] = $myshare->pivot->amount;
		//return Response::json($data);
    	if($tradetype == 'buy'){
			$spend = floatval($data['price']) * intval($data['amount']);
			if($spend > floatval($myinfo->coins)){
				$ret['errors'] = '交易额超出持有现金';
				return Response::json($ret);
			}	

    		$left = round(floatval($myinfo->coins) - $spend, 2);
    		$myinfo->coins = $left;

    		//$data['extra']['spend'] = $spend;
    		//$data['extra']['left'] = $left;
    		//return Response::json($data);

    		$myinfo->save();
   		

    		if(!$hasShare){    			
    			$p_data = [
    				'amount' => intval($data['amount']),
 					'buying_price' => floatval($data['price']),
 					'selling_price' => 0,
 					'by_currency' => $myinfo->bind_to,
 					'buying_at' => $trading_at,
    			];
                //return Response::json($data);

    			$myinfo->shares()->attach($data['sid'], $p_data);

                //return Response::json($data);

   				$p_data['username'] = Session::get('username');
                //return Response::json($data);
    			$p_data['sharename'] = $myshare->name;
                //return Response::json($data);    			
    			$e_msg = addslashes($this->show_trading_msg($p_data, $tradetype, null));
                //return Response::json($data);
    			$event = [
    				'type' => 1,
    				'event' => $e_msg,
    				'bywhom' => $data['iid'],
    			];

    			EventController::create_event($event);

    			//$data['extra']['spend'] = $spend;
    			//$data['extra']['left'] = $left;
    			//return Response::json($data);
    			$ret['data']['left'] = floatval($myinfo->coins);
    			$ret['data']['amount'] = intval($data['amount']);
    			$ret['data']['totalamount'] = intval($data['amount']);
    			$ret['data']['price'] = floatval($data['price']);
    			$ret['data']['spend'] = $spend;
    			$ret['data']['sharecount'] = 1;
    			$ret['data']['buying_at'] = $trading_at;
    		}
    		else{
    			$amount = intval($myshare->pivot->amount) + intval($data['amount']);
    			$buying_price = ($spend + intval($myshare->pivot->amount) * floatval($myshare->pivot->buying_price)) / $amount;
    			$buying_price = round($buying_price, 2);

    			/*
    			$data['extra']['myshare_amount'] = $myshare->amount;
    			$data['extra']['amount'] = $amount;
    			$data['extra']['buying_price'] = $buying_price;
    			$data['extra']['spend'] = $spend;
    			$data['extra']['left'] = $left;
    			return Response::json($data);    			
    			*/
    			$p_data = [
    				'amount' => $amount,
    				'buying_price' => $buying_price,  
    				'buying_at' => $trading_at,
    			];

    			if(intval($myshare->pivot->amount) <= 0)
    				$ret['data']['sharecount'] = 1;
    			else
    				$ret['data']['sharecount'] = 0;

    			$myinfo->shares()->updateExistingPivot($data['sid'], $p_data);    			
    			
    			$p_data['username'] = Session::get('username');
    			
    			$p_data['sharename'] = $myshare->name;
    			$p_data['amount'] = $data['amount'];
    			
    			$e_msg = addslashes($this->show_trading_msg($p_data, $tradetype, null));
    			
    			$event = [
    				'type' => 1,
    				'event' => $e_msg,
    				'bywhom' => $data['iid'],
    			];
    			
    			EventController::create_event($event);

    			$ret['data']['left'] = floatval($myinfo->coins);
    			$ret['data']['amount'] = intval($data['amount']);
    			$ret['data']['totalamount'] = $amount;
    			$ret['data']['price'] = $buying_price;
    			$ret['data']['spend'] = $spend;
    			$ret['data']['buying_at'] = $trading_at;    			   			
    		}

           $ret['data']['test']['thismarket'] = $data['thismarket'];
           $ret['data']['test']['myshare'] = $myshare->toArray();

           $ret['data']['test']['posttest'] = $this->add_active_shares($themarket, $myshare);
    	}

    	
    	elseif ($tradetype == 'sell'){
    		if(!$hasShare || $soldOut){
    			$ret['errors'] = '没有持有股票，无法卖出';
    			return Response::json($ret);
    		}	

    		$selling_at = Carbon::now()->toDateTimeString();

  			if( intval($data['amount']) >= intval($myshare->pivot->amount) ){
	    		$gain = floatval($data['price']) * intval($myshare->pivot->amount);
	    		$net_gain = round($gain -  floatval($myshare->pivot->buying_price) * intval($myshare->pivot->amount), 2);
    			$p_data = [
    				'amount' => 0,
    				'selling_price' => floatval($data['price']), 
    				'selling_at' => $trading_at,     				   				
    			]; 
	    		$myinfo->coins = round(floatval($myinfo->coins) + $gain, 2);
	    		//$data['extra']['spend'] = $spend;
	    		//$data['extra']['left'] = $left;
	    		//return Response::json($data);
	    		$myinfo->save();    			   			
    			$myinfo->shares()->updateExistingPivot($data['sid'], $p_data);

    			$p_data['username'] = Session::get('username');
    			$p_data['sharename'] = $myshare->name;
    			$p_data['amount'] = $data['amount'];
    			$e_msg = addslashes($this->show_trading_msg($p_data, $tradetype, 1));
    			$event = [
    				'type' => 1,
    				'event' => $e_msg,
    				'bywhom' => $data['iid'],
    			];

    			EventController::create_event($event);

    			$ret['data']['gain'] = round($gain, 2);
    			$ret['data']['net_gain'] = $net_gain;
    			$ret['data']['left'] = floatval($myinfo->coins);
    			$ret['data']['amount'] = intval($myshare->pivot->amount);
    			$ret['data']['totalamount'] = 0;
    			$ret['data']['price'] = floatval($data['price']);
    			$ret['data']['sharecount'] = -1;
    			$ret['data']['selling_at'] = $trading_at;

                $this->delete_active_shares($themarket, $myshare);
    		} 
 
    		else{
                
	    		$gain = floatval($data['price']) * intval($data['amount']);
	    		$net_gain = round($gain -  floatval($myshare->pivot->buying_price) * intval($data['amount']), 2);    			
    			$left_amount = intval($myshare->pivot->amount) - intval($data['amount']);
    			$p_data = [
    				'amount' => $left_amount,
    				'selling_price' => floatval($data['price']),
    				'selling_at' => $trading_at,      				   				
    			]; 

	    		$myinfo->coins = round(floatval($myinfo->coins) + $gain, 2);
	    		//$data['extra']['spend'] = $spend;
	    		//$data['extra']['left'] = $left;
	    		//return Response::json($data);
	    		$myinfo->save();   

    			$myinfo->shares()->updateExistingPivot($data['sid'], $p_data);  

   				$p_data['username'] = Session::get('username');
    			$p_data['sharename'] = $myshare->name;
    			$p_data['amount'] = $data['amount'];
    			$e_msg = addslashes($this->show_trading_msg($p_data, $tradetype, null));
    			$event = [
    				'type' => 1,
    				'event' => $e_msg,
    				'bywhom' => $data['iid'],
    			];

    			EventController::create_event($event);

    			$ret['data']['gain'] = $gain;
    			$ret['data']['net_gain'] = $net_gain;
    			$ret['data']['left'] = floatval($myinfo->coins);
    			$ret['data']['amount'] = intval($data['amount']);
    			$ret['data']['totalamount'] = $left_amount;
    			$ret['data']['price'] = floatval($data['price']);    			
   				$ret['data']['sharecount'] = 0;
   				$ret['data']['selling_at'] = $trading_at;
    		}
   		

    	}
		
    	return Response::json($ret);    		
    }			
 
}
