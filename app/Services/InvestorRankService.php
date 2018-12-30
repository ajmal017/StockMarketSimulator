<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Tools\MarketTimeZone;

use Carbon\Carbon;
use App\Investor;
use App\Market;
use App\Index;
use App\Config;
use App\Share;
use App\Currency;
use Cache;
use DateTime;
use DateTimeZone;

class InvestorRankService {

    protected $config  = array();
    protected $market = array();
    protected $index = array();
    protected $currency = array();
    protected $extra_share_config = array();
    //protected $local_times, $open_times;
 	protected $local_times, $open_times, $market_platform, $market_platform_price_query, $fullcode_prefix, $active_shares, $no_of_active_shares, $active_shares_count, $make_rank_date, $make_rank_offset, $chunk_unit;
 	
    //private $marketrank_service;
 	protected $hasFailed;

 	protected $open_markets = array();

 public function __construct(){
    	//$this->middleware('auth');
    	 $this->config = Cache::get('global_config');
        $this->market = Cache::get('market');
        $this->index = Cache::get('index');
        $this->currency = Cache::get('currency'); 
        $this->extra_share_config = Cache::get('extra_share_config');
        //dd($this->extra_share_config);
        //dd($this->currency);
        //dd(config('share'));
        $this->open_times = $this->extra_share_config['MarketOpenTime'];
        //$this->location = config('share.MarketLocationMap');
        $this->location = $this->extra_share_config['MarketLocationMap'];
       // $this->rankUpdatedEvery = config('share.RankUpdatedEvery');	
        $this->rankUpdatedEvery = $this->extra_share_config['RankUpdatedEvery'];

        $this->local_times = $this->get_local_time();

        $this->open_markets = $this->opened_markets($this->open_times, $this->local_times);

       	$this->market_platform  = $this->extra_share_config['market_platform'];
       	$this->market_platform_price_query = $this->extra_share_config['market_platform_price_query'];
       	$this->fullcode_prefix = $this->extra_share_config['fullcode_prefix'];
        //$this->open_times = config('share.MarketOpenTime');
        //dd($this->open_times);
        //$this->market_platform = config('share.market_platform');
        //$this->market_platform_price_query = config('share.market_platform_price_query');
        //$this->fullcode_prefix = config('share.fullcode_prefix');
        $this->active_shares = $this->get_active_shares();
         //dd($this->active_shares);
        $this->no_of_active_shares = $this->get_active_shares_no();

        $this->active_shares_count = $this->get_active_shares_count();

        $this->make_rank_date = $this->get_make_rank_date();
        $this->make_price_date = $this->get_make_price_date();
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
        $this->hasFailed = true;
        $this->rank_top_no = 10;
    }

	public function update_all_active_shares($overwrite=false){
		if($overwrite){
			$old_active = $this->get_active_shares();
		}


		//dd($data);
        $temp = DB::table('shares')->leftJoin('have_shares', 'shares.sid', '=', 'have_shares.sid')
                                ->where('have_shares.amount', '>', 0)
                                ->select(DB::raw('shares.sid AS sid, shares.stock_index AS stock_index, shares.name AS name, shares.atStockMarket AS market, have_shares.amount AS amount'))
                                ->get();

        //dd($temp);
        foreach($temp as $t){
        	if(!isset($data['shares_count'][$t->market][$t->stock_index]))
        		$data['shares_count'][$t->market][$t->stock_index] = 0;
            $data['active_shares'][$t->market][$t->stock_index]['name'] = $t->name;

            if($overwrite && isset($old_active[$t->market][$t->stock_index]['newprice'])){
            	$data['active_shares'][$t->market][$t->stock_index]['newprice'] = $old_active[$t->market][$t->stock_index]['newprice'];
            	$data['active_shares'][$t->market][$t->stock_index]['updatedate'] = $old_active[$t->market][$t->stock_index]['updatedate'];
            }
            //$data[$t->market][$t->stock_index]['amount'] = $t->amount;
            $data['shares_count'][$t->market][$t->stock_index]++;
        } 
        foreach($data['active_shares'] as $m => $mdata)
            $data['no_of_shares'][$m] = count($mdata);		


        Cache::forever('active_shares', $data['active_shares']);
        Cache::forever('no_of_active_shares', $data['no_of_shares']);
        Cache::forever('active_shares_count', $data['shares_count']);

        //dd(Cache::get('active_shares'));
	}


	public function get_active_shares($market=null){
		if(!$market)
			return Cache::get('active_shares');
		else{
			$ret = Cache::get('active_shares');
			if(isset($ret[$market]))
				return $ret[$market];
			else
				return null;
		}
	}

	public function get_active_shares_no($market=null){
		if(!$market)
			return Cache::get('no_of_active_shares');
		else{
			$ret = Cache::get('no_of_active_shares');
			if(isset($ret[$market]))
				return $ret[$market];
			else
				return null;
		}
	}

	public function get_active_shares_count($market=null){
		if(!$market)
			return Cache::get('active_shares_count');
		else{
			$ret = Cache::get('active_shares_count');
			if(isset($ret[$market]))
				return $ret[$market];
			else
				return null;
		}		
	}

	public function get_make_rank_date($market = null){

		$local_times = $this->get_local_time();
		//dd($local_times);
		if($market == null){
			if(Cache::has('make_rank_date')){
				return Cache::get('make_rank_date');
			}
			else{
				$yesterday = array();			

				foreach ($local_times as $mid => $t){
					$thedate = date('Y-m-d H:i:s', $t);
					$yesterday[$mid] = Carbon::parse($thedate)->subDays(1)->toDateString();
				}		
				//dd($yesterday);
				return $yesterday;
			}
		}
		else{
			if(Cache::has('make_rank_date')){
				$dates = Cache::get('make_rank_date');
				return $dates[$market];
			}
			else{
				$thedate = date('Y-m-d H:i:s', $local_times[$market]);
				return Carbon::parse($thedate)->subDays(1)->toDateString();

			}
		}

	}

	public function update_make_rank_date($market, $date=null){
		$ret = $this->get_make_rank_offset();
		if($date == null)
			$ret[$market] = Carbon::now()->toDateString();
		else
			$ret[$market] = $date;

		Cache::forever('make_rank_date', $ret);
	}


	public function get_make_price_date($market = null){

		$local_times = $this->get_local_time();
		//dd($local_times);
		if($market == null){
			if(Cache::has('make_price_date')){
				return Cache::get('make_price_date');
			}
			else{
				$yesterday = array();			

				foreach ($local_times as $mid => $t){
					$thedate = date('Y-m-d H:i:s', $t);
					$yesterday[$mid] = Carbon::parse($thedate)->subDays(1)->toDateString();
				}		
				//dd($yesterday);
				return $yesterday;
			}
		}
		else{
			if(Cache::has('make_price_date')){
				$dates = Cache::get('make_price_date');
				return $dates[$market];
			}
			else{
				$thedate = date('Y-m-d H:i:s', $local_times[$market]);
				return Carbon::parse($thedate)->subDays(1)->toDateString();

			}
		}

	}

	public function update_make_price_date($market, $date=null){
		$ret = $this->get_make_rank_offset();
		if($date == null)
			$ret[$market] = Carbon::now()->toDateString();
		else
			$ret[$market] = $date;

		Cache::forever('make_price_date', $ret);
	}

	public function get_make_rank_offset($market = null){
		$local_times = $this->get_local_time();

		if($market == null){
			if(Cache::has('make_rank_offset'))
				return Cache::get('make_rank_offset');
			else{
				$offsets = array();
				foreach($local_times as $mid =>$t)
					$offsets[$mid] = -1;
				return $offsets;
			}	
		}
		else{
			if(Cache::has('make_rank_offset')){
				$offsets = Cache::get('make_rank_offset');
				if(isset($offsets[$market]))
					return $offsets[$market];
				else
					return -1;
			}
			else{
				return -1;
			}
		}	
	}

	public function update_make_rank_offset($market, $offset=null){
		$ret = $this->get_make_rank_offset();
		if(!is_array($ret))
			$ret = array();
		//dd($ret);
		if($offset == null)
			$ret[$market] = -1;
		else
			$ret[$market] = $offset;
		Cache::forever('make_rank_offset', $ret);

	}


	public function convert_into_url($market, $codes){
		$full_codes = array();
		if(is_array($codes)){
			foreach($codes as $code)
				$full_codes[] = $this->get_fullcode($market, strtolower($code));
		}
		else
			$full_codes[] = $this->get_fullcode($market, strtolower($codes));

		//dd($full_codes);
		if(!empty($full_codes)){

			if($market == 'us')
				$market = 'nas';
			elseif ($market == 'china')
				$market = 'sh';

			return $this->market[$market]['query_url_head'] . implode(',', $full_codes);
		}
		else
			return false;
	}


	public function get_fullcode($market, $code){
		//$prefix = $this->('share.fullcode_prefix.' . $market);
		return $this->extra_share_config['fullcode_prefix'][$market] . $code;
	}

	public function get_code($market, $fullcode){
		$prefix = $this->extra_share_config['fullcode_prefix'][$market];
		$pattern = '/' . $prefix . '/';
		$ret = preg_replace($pattern, '', $fullcode);
		return strtoupper($ret);
	}

	public function opened_markets($open_times, $local_times){
		$ret = array();
		foreach($open_times as $mid => $mdata){
			$times = array();
			$now = date('Hi', $local_times[$mid]);
			$nowweekday = date('D', $local_times[$mid]);
		
			if($nowweekday != 'Sat' && $nowweekday != 'Sun'){
				$temp = explode(',', $mdata);
				foreach($temp as $t){
					$temp2 = explode('-', $t);
					$temp3 = explode(':', $temp2[0]);
					$times[] = $temp3[0] . $temp3[1];
					$temp3 = explode(':', $temp2[1]);
					$times[] = $temp3[0] . $temp3[1];	
				}
				if(count($times) == 2 && $now >= $times[0] && $now <= $times[1])
					$ret[] = $mid;
				elseif(count($times) == 4 && ( ($now >= $times[0] && $now <= $times[1]) || ($now >= $times[2] && $now <= $times[3]) ))
					$ret[] = $mid;
			}

		}
		return $ret;
	}

	public function get_local_time(){
		$ret = array();
		//$timezones = config('share.GlobalTimezone');
		$timezones = $this->extra_share_config['GlobalTimezone'];
		//$marketzones = array();
		//$market_time_zones = config('share.MarketTimezone');
		$market_time_zones = $this->extra_share_config['MarketTimezone'];

		//$test = config('share');
		//dd($test);
		foreach($market_time_zones as $mid => $mval){
			$timezone = $timezones[$mval];
			$new_date_time = $this->make_date_time_now($timezone, 'Y-m-d H:i:s');

			$ret[$mid] = $new_date_time->getTimestamp();
		}
		return $ret;
	}
	
	public function make_date_time_now($timezone, $format){
		$date_time = new DateTime('NOW');
		$date_time->setTimezone(new DateTimeZone($timezone));
		$new_date_time = new DateTime($date_time->format($format));
		return $new_date_time;
	}	


	public function get_shares_prices($query_url, $market, $platform='sina'){
		$ret = array();
		$failed = array();
		//dd($query_url);
		if($platform == 'sina'){
			$json_content = mb_convert_encoding(file_get_contents($query_url), 'UTF-8', 'GBK');

			$pattern = "/(\r\n|\r|\n)/";
			$json_content = preg_replace($pattern, '\n', $json_content);


			//dd($json_content);

			$temp2 = explode("\\n", $json_content);

			//dd($temp2);
		

			foreach($temp2 as $t){
				if($t){
			    	$temp3 = explode("=", $t);
			    	$res = $temp3[1];
			    	$id_arr = explode(' ', $temp3[0]);
			    	if($res && isset($id_arr[1]) && $id_arr[1]){
				    	$fullid = substr($id_arr[1],  7);
				    	//dd('fullid: ' . $fullid);
				    	$id = $this->get_code($market, $fullid);
				    	//dd($id);
			    		$pattern = "/\"|\";" .  '/';
			    		$res = preg_replace($pattern, '', $res);
			    		$temp = explode(",", $res);
			    		//dd($temp);
			    		$newprice = 0;
			    		if(!$temp){
			    			$this->hasFailed = true;
			    			return $failed;
			    		}
			    		else{
			    			$this->hasFailed = false;
				    		if($market == 'sh' || $market == 'sz')
				    			$newprice = floatval($temp[3]);
				    		else if ($market == 'ny' || $market == 'nas' || $market == 'us'){
				    			$newprice = floatval($temp[1]);
				    		}

				    		else if ($market == 'hk')
				    			$newprice = floatval($temp[6]);
				    		$ret[$id] = round($newprice, 2);
				    		//dd($ret[$id]);
				    	}
		    		}
	    		}
    		}			
		}
		//dd($ret);
		return $ret;
	}

	
	public function process_price($market){
		if(!empty($this->active_shares) && isset($this->active_shares[$market])) {
      		$max_tries = 5;
      		$current_date_at_local = DateTime::createFromFormat('Y-m-d', date('Y-m-d', $this->local_times[$market]));
      		//$date = DateTime::createFromFormat($format, '2009-02-15');
      		$make_price_date = $this->get_make_price_date();



      		if(isset($make_price_date[$market]))
      			$last_update_date_at_local = DateTime::createFromFormat('Y-m-d', $make_price_date[$market]);


      		//dd($current_date_at_local->format('Y-m-d H:i:s'));
      		//dd($last_update_date_at_local->format('Y-m-d H:i:s'));

      		//$current_date_at_local->diff($last_update_date_at_local)->days < 1
      		//dd($current_date_at_local->diff($last_update_date_at_local)->days);
      		if(isset($make_price_date[$market]) && $current_date_at_local->diff($last_update_date_at_local)->days < 1)
      			return false;


      		//dd($this->active_shares[$market]);
      		//dd(ksort($this->active_shares[$market]));
      		ksort($this->active_shares[$market]);
        	$share_codes = array_keys($this->active_shares[$market]);
        	$length = count($share_codes);
            //dd($share_codes); 	
        	//dd($this->active_shares);

            $start = $this->get_make_rank_offset($market) + 1;

            $newoffset = $start + $this->chunk_unit - 1;
            


            $chunk_shares = array_splice($share_codes, $start, $this->chunk_unit);
            //dd($chunk_shares);

			$query_url = $this->convert_into_url($market, $chunk_shares);
			
			//dd($query_url);

			$count = 0;
			while($this->hasFailed && $count <= $max_tries) {
				$ret = $this->get_shares_prices($query_url, $market);
				if($this->hasFailed)
					$count++;
			}

			if(!empty($ret)) {
				//$timezones = $this->extra_share_config['GlobalTimezone'];
				//$market_time_zones = $this->extra_share_config['MarketTimezone'];				
				foreach($ret as $code => $newprice){
					//$timezone = $timezones[$market_time_zones[$market]];
					$this->active_shares[$market][$code]['newprice'] = $newprice;
					$this->active_shares[$market][$code]['updatedate'] = date('Y-m-d', $current_date_at_local->getTimestamp());
				}
				Cache::forever('active_shares', $this->active_shares);
				//$this->update_make_rank_offset($market, $newoffset);
			}
			$this->update_make_rank_offset($market, $newoffset);

			if($newoffset >= $length - 1){
				$this->update_make_price_date($market, date('Y-m-d', $this->local_times[$market]));
				$this->update_make_rank_offset($market);
			}
		}	
	}

	public function get_ranks($market = null){
		if(!$market){
			return Cache::get('investor_rank');
		}
		else{
			$ret = Cache::get('investor_rank');
			return $ret[$market];
		}
	}

	public function process_rank($market){
		$ret = array();

		//$investors = Investor::with('shares')->get();
		//$temp = 
		//$investors = Investor::with('shares')->where('shares.atStockMarket', 'us')->wherePivot('amount', '>', 0)->get();

		$investors = Investor::with(['shares' => function($query) use (&$market){	
										//dd($market);									
										$query->where('atStockMarket', $market)->wherePivot('amount', '>', 0);
								}])->get();
		//dd($investors);

		$active_shares = $this->active_shares;

		//dd($active_shares);
		//dd($this->get_active_shares());

		foreach($investors as $investor){
			$iid = $investor->iid;

			$hisshares = $investor->shares;
			//dd($hisshares);
			$profit = 0;
			$asset = 0;
			$old_asset = 0;
			foreach($hisshares as $share){
				//dd($share);
				$sid = $share->stock_index;
				//dd($sid);
				$volume = intval($share->pivot->amount);
				$bought_price = floatval($share->pivot->buying_price);
				if(isset($active_shares[$market][$sid]['newprice']))
					$new_price = floatval($active_shares[$market][$sid]['newprice']);
				else
					$new_price = 0;

				$old_asset += $bought_price * $volume;
				$asset += $new_price * $volume;
				$profit += ($new_price - $bought_price) * $volume;
			}

			$ret['rank'][$iid]['coins'] = round(floatval($investor->coins), 2);
			$ret['rank'][$iid]['stockvalue'] = round($asset, 2);
			$ret['rank'][$iid]['old_stockvalue'] = round($old_asset, 2);
			$ret['rank'][$iid]['profit'] = round($profit, 2);
			$ret['rank'][$iid]['asset'] = $ret['rank'][$iid]['coins'] + $ret['rank'][$iid]['stockvalue'];
			$ret['rank'][$iid]['no_of_shares'] = count($hisshares);



			$ret['info'][$iid]['shares'] = $hisshares;


		}
		//dd($ret);
		$collection = collect($ret['rank'])->sortByDesc('asset')->slice(0, $this->rank_top_no);



		$ret['rank'] = $collection->all();

		//if(isset($ret['rank']) && !empty($ret['rank']))
		//dd($ret);
		return $ret;
	}

	public function is_new_price_ready($market){
		//$current_date_at_local = DateTime::createFromFormat('Y-m-d', date('Y-m-d', $this->local_times[$market]));
		$current_date_at_local = date('Y-m-d', $this->local_times[$market]);
		$make_price_date = $this->get_make_price_date();
		if($current_date_at_local == $make_price_date[$market])
			return true;
		else
			return false;
	}
	
	//public function make_date_at_market($market){

	//}

	public function is_time_for_rank($market){
		$current_date_at_local = DateTime::createFromFormat('Y-m-d', date('Y-m-d', $this->local_times[$market]));
		$last_update_date_at_local = DateTime::createFromFormat('Y-m-d', $this->get_make_rank_date($market));

		if($current_date_at_local->diff($last_update_date_at_local)->days > 0)
			return true;
		else
			return false;
	}

	/********************************Implementation****************************************/

    public function make_american_new_price(){
    	//Cache::forget('')
    	//if(!empty($this->active_shares) && isset($this->active_shares['us']) && !in_array('us', $this->open_markets))
			$this->process_price('us');
   			//dd($this->active_shares);
			//dd($this->get_active_shares());
		//dd($this->get_make_rank_offset());
		//dd($this->get_make_price_date());

    }

    public function make_chinese_new_price(){
        if(!empty($this->active_shares)){
            if(isset($this->active_shares['sh'])  && !in_array('sh', $this->open_markets)){
            	$this->process_price('sh');
            }

            if(isset($this->active_shares['sz'])  && !in_array('sz', $this->open_markets)){
            	$this->process_price('sz');
            }
        }

    }

    public function make_hk_new_price(){
        if(!empty($this->active_shares) && !in_array('hk', $this->open_markets) && isset($this->active_shares['hk'])) {
        	$this->process_price('hk');
        }

    }



    public function make_american_rank(){
    	if($this->is_new_price_ready('us') && $this->is_time_for_rank('us')){    		
    		$ret = $this->process_rank('us');
    		
    		if(Cache::has('investor_rank'))
    			$ranks = Cache::get('investor_rank');
    		else
    			$ranks = array();
    		
    		if(isset($ret['rank']) && !empty($ret['rank'])){
    			//dd($ret['rank']);
    			$ranks['us'] = $ret['rank'];
    			Cache::forever('investor_rank', $ranks);

    			//Cache::forever('investor_rank_date.us', date('Y-m-d', $this->local_times['us']));
    			$this->update_make_rank_date('us', date('Y-m-d', $this->local_times['us']));
    			//dd(Cache::get('investor_rank'));
    			//dd(Cache::get('make_rank_date'));
    		}
    	}
    }

    public function make_chinese_rank(){
    	if($this->is_new_price_ready('sh')  && $this->is_time_for_rank('sh')){
    		$ret = $this->process_rank('sh');
    		if(Cache::has('investor_rank'))
    			$ranks = Cache::get('investor_rank');
    		else
    			$ranks = array();

    		if(isset($ret['rank']) && !empty($ret['rank'])){
    			$ranks['sh'] = $ret['rank'];
    			Cache::forever('investor_rank', $ranks);
    			//Cache::forever('investor_rank_date.sh', date('Y-m-d', $this->local_times['sh']));
    			$this->update_make_rank_date('sh', date('Y-m-d', $this->local_times['sh']));
    		}
    	}
    	if($this->is_new_price_ready('sz') && $this->is_time_for_rank('sz')){
    		$ret = $this->process_rank('sz');
    		if(isset($ret['rank']) && !empty($ret['rank'])){
    			$ranks['sz'] = $ret['rank'];
    			Cache::forever('investor_rank', $ranks);    			
    			//Cache::forever('investor_rank.sz', $ret['rank']);
    			//Cache::forever('investor_rank_date.sz', date('Y-m-d', $this->local_times['sz']));
    			$this->update_make_rank_date('sz', date('Y-m-d', $this->local_times['sz']));
    		}
    	}    	
    }

    public function make_hk_rank(){
    	if($this->is_new_price_ready('hk') && $this->is_time_for_rank('hk')){
    		$ret = $this->process_rank('hk');
    		if(Cache::has('investor_rank'))
    			$ranks = Cache::get('investor_rank');
    		else
    			$ranks = array();

    		if(isset($ret['rank']) && !empty($ret['rank'])){
    			$ranks['hk'] = $ret['rank'];
    			Cache::forever('investor_rank', $ranks);    	    			
    			//Cache::forever('investor_rank.hk', $ret['rank']);
    			//Cache::forever('investor_rank_date.hk', date('Y-m-d', $this->local_times['hk']));
    			$this->update_make_rank_date('hk', date('Y-m-d', $this->local_times['hk']));
    		}
    	}
    }
}

?>