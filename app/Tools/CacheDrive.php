<?php
namespace App\Tools;
use Illuminate\Support\Facades\DB;
use App\Investor;
use App\Market;
use App\Index;
use App\Config;
use App\Share;
use App\Currency;
use Session;
use Cache;
use Response;
use Carbon\Carbon;

trait CacheDrive{
	
	public function getGlobalConfig(){
    	$configs = array();

    	if (!Cache::has('global_config')){
    		$temp = Config::all();
    		if(!empty($temp)){
	    		foreach($temp as $cid => $rec)
	    			$configs[$rec->attribute] = $rec->value;

	    		if (!empty($configs))
	    			Cache::forever('global_config', $configs);
    		}
    	}
    	else
    		$configs = Cache::get('global_config');

    	return $configs;
	} 	

	public function getMarket(){
		$markets = array();
    	if (!Cache::has('market')){
    		$temp = Market::all();
    		if(!empty($temp)){
	    		foreach($temp as $mid => $rec){
	    			$markets[$rec->market_index]['market_index'] = $rec->market_index;
	    			$markets[$rec->market_index]['name'] = $rec->name;
	    			$markets[$rec->market_index]['query_url_head'] = $rec->query_url_head;
	    			$markets[$rec->market_index]['rank_up_url'] = $rec->rank_up_url;
	    			$markets[$rec->market_index]['rank_down_url'] = $rec->rank_down_url;
	    			$markets[$rec->market_index]['status'] = $rec->status;
	    			$markets[$rec->market_index]['allowed_currency'] = $rec->allowed_currency;
	    		}

	    		if (!empty($markets))
	    			Cache::forever('market', $markets);
    		}
    	}
    	else
    		$markets = Cache::get('market');

    	return $markets;
	}

	public function getIndex(){
		$indice = array();
    	if (!Cache::has('index')){
    		$temp = Index::all();
    		if(!empty($temp)){
	    		foreach($temp as $iid => $rec){
	    			$indice[$rec->index_index]['index_index'] = $rec->index_index;
	    			$indice[$rec->index_index]['name'] = $rec->name;
	    			$indice[$rec->index_index]['index_query_url'] = $rec->index_query_url;
	    			$indice[$rec->index_index]['min_chart_url'] = $rec->min_chart_url;
	    			$indice[$rec->index_index]['belongsToMarket'] = $rec->belongsToMarket;
	    			//$indice[$rec->market_index]['status'] = $rec->status;
	    			$indice[$rec->index_index]['status'] = $rec->status;
	    		}

	    		if (!empty($indice))
	    			Cache::forever('index', $indice);
    		}
    	}
    	else
    		$indice = Cache::get('index');

    	return $indice;
	}

	public function getCurrency(){
		$currency = array();
    	if (!Cache::has('currency')){
    		$temp = Currency::all();
    		if(!empty($temp)){
	    		foreach($temp as $cid => $rec){
	    			$currency[$rec->currency_index]['currency_index'] = $rec->currency_index;
	    			$currency[$rec->currency_index]['name'] = $rec->name;
	    			$currency[$rec->currency_index]['rate_query_url'] = $rec->rate_query_url;
	    			$currency[$rec->currency_index]['status'] = $rec->status;
	    			
	    		}

	    		if (!empty($currency))
	    			Cache::forever('currency', $currency);
    		}
    	}
    	else
    		$currency = Cache::get('currency');

    	return $currency;		
	}	

	public function updateConfig(array $configs_arr){
		//update config table
		$configs = Config::all();

		foreach($configs as $theconfig){
			$key = $theconfig->attribute;
			$theconfig->value = $configs_arr[$key];
			$theconfig->save();
		}	
		//update cache
		Cache::forever('global_config', $configs_arr);

	}


	public function addConfig(array $configs_arr){
		//insert into config table + add into cache
		$configs = array();
		if (Cache::has('global_config'))
			$configs = Cache::get('global_config');
		
		foreach($configs_arr as $attr => $val){	
			if ($attr != '_token'){		
				$config = new Config;
				$config->attribute = $attr;
				$config->value = $val;
				$config->save();
				$configs[$attr] = $val;
			}		
		}

		//add into cache
		Cache::forever('global_config', $configs);
	}


	public function addOneConfig($attr, $val){
		$configs = array();
		if (Cache::has('global_config'))
			$configs = Cache::get('global_config');		
		
		$config = new Config;
		$config->attribute = $attr;
		$config->value = $val;
		$config->save();
		$configs[$attr] = $val;

		Cache::forever('global_config', $configs);
	}

	public function addDefaultMarketIndex(){
		$q_head = "http://hq.sinajs.cn/list=";

		$page_up_from_sh = 'http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=1&num=10&sort=changepercent&asc=0&node=sh_a&symbol=';

		$page_down_from_sh = 'http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=1&num=10&sort=changepercent&asc=1&node=sh_a&symbol=';

		$page_up_from_sz = 'http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=1&num=10&sort=changepercent&asc=0&node=sz_a&symbol=';
		
		$page_down_from_sz = 'http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=1&num=10&sort=changepercent&asc=1&node=sz_a&symbol=';

		$us_stock_url_head = "http://stock.finance.sina.com.cn/usstock/api/jsonp.php/IO.XSRV2.CallbackList['";
		
		$us_stock_url_mid = "']/US_CategoryService.getList?page=1&num=10&sort=chg&";


		$up_token = 'asc=0&';
		$down_token = 'asc=1&';

		$ny_token = 'market=N&';
		$nas_token = 'market=O&';		

		if(Market::all()->isEmpty() == true){
			$sh_market = new Market;
			$sz_market = new Market;
			$ny_market = new Market;
			$nas_market = new Market;

			$markets = array();

			$sh_market->market_index = $markets['sh']['market_index'] = 'sh';			
			$sh_market->name = $markets['sh']['name'] ='上证';
			$sh_market->query_url_head = $markets['sh']['query_url_head'] = $q_head . 'sh';
			$sh_market->rank_up_url = $markets['sh']['rank_up_url'] = $page_up_from_sh;
			$sh_market->rank_down_url = $markets['sh']['rank_down_url'] = $page_down_from_sh;


			$sh_market->save();

			$sz_market->market_index = $markets['sz']['market_index'] = 'sz';
			$sz_market->name = $markets['sz']['name'] = '深证';
			$sz_market->query_url_head = $markets['sz']['query_url_head'] = $q_head . 'sz';
			$sz_market->rank_up_url = $markets['sz']['rank_up_url'] = $page_up_from_sz;
			$sz_market->rank_down_url = $markets['sz']['rank_down_url'] = $page_down_from_sz;

			$sz_market->save();

			$ny_market->market_index = $markets['ny']['market_index'] = 'ny';
			$ny_market->name = $markets['ny']['name'] = 'NYSE';
			$ny_market->query_url_head = $markets['ny']['query_url_head'] = $q_head . 'gb_';
			$ny_market->rank_up_url =  $markets['ny']['rank_up_url'] = $us_stock_url_head . '|' . $us_stock_url_mid . '|' . $up_token . '|' . $ny_token;
			$ny_market->rank_down_url =  $markets['ny']['rank_down_url'] = $us_stock_url_head . '|' . $us_stock_url_mid . '|' . $down_token . '|' . $ny_token;

			$ny_market->save();

			$nas_market->market_index = $markets['nas']['market_index'] = 'nas';
			$nas_market->name = $markets['nas']['name'] = 'NASDAQ';
			$nas_market->query_url_head = $markets['nas']['query_url_head'] = $q_head . 'gb_';
			$nas_market->rank_up_url = $markets['nas']['rank_up_url'] = $us_stock_url_head . '|' . $us_stock_url_mid . '|' . $up_token . '|' . $nas_token;
			$nas_market->rank_down_url = $markets['nas']['rank_down_url'] = $us_stock_url_head . '|' . $us_stock_url_mid . '|' . $down_token . '|' . $nas_token;

			$nas_market->save();

			Cache::forever('market', $markets);
		}

		if(Index::all()->isEmpty() == true){
			$sh_index = new Index;
			$sz_index = new Index;
			$dow_index = new Index;
			$nas_index = new Index;

			$indice = array();

			$sh_index->index_index = $indice['sh']['index_index'] = 'sh';
			$sh_index->name = $indice['sh']['name'] = '上证指数';
			$sh_index->index_query_url = $indice['sh']['index_query_url'] = $q_head . 's_sh000001';
			$sh_index->min_chart_url = $indice['sh']['min_chart_url'] = "http://image.sinajs.cn/newchart/min/n/sh000001.gif";
			$sh_index->belongsToMarket = $indice['sh']['belongsToMarket'] = 'sh';

			$sh_index->save();

			$sz_index->index_index = $indice['sz']['index_index'] = 'sz';
			$sz_index->name = $indice['sz']['name'] = '深证指数';
			$sz_index->index_query_url = $indice['sz']['index_query_url'] = $q_head . 's_sz399001';
			$sz_index->min_chart_url = $indice['sz']['min_chart_url'] = "http://image.sinajs.cn/newchart/min/n/sz399001.gif";
			$sz_index->belongsToMarket = $indice['sz']['belongsToMarket'] = 'sz';

			$sz_index->save();

			$dow_index->index_index = $indice['dow']['index_index'] = 'dow';
			$dow_index->name = $indice['dow']['name'] = 'Dow Jones指数';
			$dow_index->index_query_url = $indice['dow']['index_query_url'] = $q_head . 'gb_dji';
			$dow_index->min_chart_url = $indice['dow']['min_chart_url'] = "http://image.sinajs.cn/newchart/usstock/min_idx_py/dji.gif";
			$dow_index->belongsToMarket = $indice['dow']['belongsToMarket'] = 'ny';

			$dow_index->save();

			$nas_index->index_index = $indice['nas']['index_index'] = 'nas';
			$nas_index->name = $indice['nas']['name'] = 'NASDAQ指数';
			$nas_index->index_query_url = $indice['nas']['index_query_url'] = $q_head . 'gb_ixic';
			$nas_index->min_chart_url = $indice['nas']['min_chart_url'] = "http://image.sinajs.cn/newchart/usstock/min_idx_py/ixic.gif";
			$nas_index->belongsToMarket = $indice['nas']['belongsToMarket'] = 'nas';

			$nas_index->save();

			Cache::forever('index', $indice);
		}
		
	}


	public function update_config_cache(){
		
		$configs = Config::all();
		$config = array();
		foreach($configs as $theconfig){
			$attr = $theconfig->attribute;
			$config[$attr] = $theconfig->value;
		}				
		Cache::forever('global_config', $config);
	}

	public function update_market_cache(){
		$markets = Market::all();
		$market = array();
		foreach($markets as $themarket){
			$mid = $themarket->market_index;
			$market[$mid]['market_index'] = $themarket->market_index;
			$market[$mid]['name'] = $themarket->name;
			$market[$mid]['query_url_head'] = $themarket->query_url_head;
			$market[$mid]['rank_up_url'] = $themarket->rank_up_url;
			$market[$mid]['rank_down_url'] = $themarket->rank_down_url;
			$market[$mid]['status'] = $themarket->status;
			$market[$mid]['allowed_currency'] = $themarket->allowed_currency;
		}
		Cache::forever('market', $market);	
	}

	public function update_index_cache(){
		$indice = Index::all();
		$index = array();
		foreach($indice as $theindex){
			$mid = $theindex->index_index;
			$index[$mid]['index_index'] = $theindex->index_index;
			$index[$mid]['name'] = $theindex->name;
			$index[$mid]['index_query_url'] = $theindex->index_query_url;
			$index[$mid]['min_chart_url'] = $theindex->min_chart_url;
			$index[$mid]['belongsToMarket'] = $theindex->belongsToMarket;
			$index[$mid]['status'] = $theindex->status;
		}
		Cache::forever('index', $index);	
	}


	public function update_currency_cache(){	
		$currencies = Currency::all();
		$currency = array();

		foreach($currencies as $thecurrency){
			$cid = $thecurrency->currency_index;
			$currency[$cid]['currency_index'] = $thecurrency->currency_index;
			$currency[$cid]['name'] = $thecurrency->name;
			$currency[$cid]['rate_query_url'] = $thecurrency->rate_query_url;
			$currency[$cid]['status'] = $thecurrency->status;			
		}
 		
    	Cache::forever('currency', $currency);
	}

	public function convert_share_config_into_cache(){
		$share_config = config('share');
		Cache::forever('extra_share_config', $share_config);
	}

	public function add_active_shares($market, $shares){
		$active_shares = array();
		$active_shares_count = array();
		$test_msg = 'not yet';

		if(Cache::has('active_shares'))
			$active_shares = Cache::get('active_shares');

		if(Cache::has('active_shares_count'))
			$active_shares_count = Cache::get('active_shares_count');

		if(is_array($shares)){
			foreach($shares as $share){
				if (!isset($active_shares[$market])){
					$active_shares[$market][$share->stock_index]['name'] = $share->name;					
				}
				if(isset($active_shares[$market]) && !isset($active_shares[$market][$share->stock_index]))
					$active_shares[$market][$share->stock_index]['name'] = $share->name;

				if(!isset($active_shares_count[$market][$share->stock_index]))
					$active_shares_count[$market][$share->stock_index] = 0;

				$active_shares_count[$market][$share->stock_index]++;
			}
		}
		else{
			if (!isset($active_shares[$market])){
				$active_shares[$market][$shares->stock_index]['name'] = $shares->name;					
			}
			elseif(isset($active_shares[$market]) && !isset($active_shares[$market][$shares->stock_index])){
				$test_msg = 'pass elseif part!';
				$active_shares[$market][$shares->stock_index]['name'] = $shares->name;	
			}

			//return $active_shares;

			if(!isset($active_shares_count[$market][$shares->stock_index]))
				$active_shares_count[$market][$shares->stock_index] = 0;
			
			//return $active_shares;
			$active_shares_count[$market][$shares->stock_index]++;	


		}

		Cache::forever('active_shares', $active_shares);
		//return $active_shares;
		$this->update_active_shares_no($market);
		//return $active_shares;
		Cache::forever('active_shares_count', $active_shares_count);
		return $test_msg;
	}

	public function delete_active_shares($market, $share){
		$active_shares = array();
		$active_shares_count = array();
		if(Cache::has('active_shares'))
			$active_shares = Cache::get('active_shares');

		if(Cache::has('active_shares_count'))
			$active_shares_count = Cache::get('active_shares_count');

		if(!empty($active_shares)){
			if(isset($active_shares[$market][$share->stock_index]) && isset($active_shares_count[$market][$share->stock_index])){
				if(intval($active_shares_count[$market][$share->stock_index]) == 1)
					unset($active_shares[$market][$share->stock_index]);
				$active_shares_count[$market][$share->stock_index]--;
			}
			Cache::forever('active_shares', $active_shares);
			$this->update_active_shares_no($market);
			Cache::forever('active_shares_count', $active_shares_count);
		}			
	}

	public function update_all_active_shares(){
		$data = array();
        $temp = DB::table('shares')->leftJoin('have_shares', 'shares.sid', '=', 'have_shares.sid')
                                ->where('have_shares.amount', '>', 0)
                                ->select(DB::raw('shares.sid AS sid, shares.stock_index AS stock_index, shares.name AS name, shares.atStockMarket AS market, have_shares.amount AS amount'))
                                ->get();

        //dd($temp);
        foreach($temp as $t){
        	if(!isset($data['shares_count'][$t->market][$t->stock_index]))
        		$data['shares_count'][$t->market][$t->stock_index] = 0;
            $data['active_shares'][$t->market][$t->stock_index]['name'] = $t->name;
            //$data[$t->market][$t->stock_index]['amount'] = $t->amount;
            $data['shares_count'][$t->market][$t->stock_index]++;
        } 
        foreach($data['active_shares'] as $m => $mdata)
            $data['no_of_shares'][$m] = count($mdata);		

        Cache::forever('active_shares', $data['active_shares']);
        Cache::forever('no_of_active_shares', $data['no_of_shares']);
        Cache::forever('active_shares_count', $data['shares_count']);
	}

	public function update_active_shares_no($market){
		$active_shares = array();
		$active_shares_no = array();
		if(Cache::has('active_shares'))
			$active_shares = Cache::get('active_shares');

		if(Cache::has('no_of_active_shares'))
			$active_shares_no = Cache::get('no_of_active_shares');

		if(!isset($active_shares[$market]))
			$active_shares_no[$market] = 0;
		else
			$active_shares_no[$market] = count($active_shares[$market]);

		Cache::forever('no_of_active_shares', $active_shares_no);
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
		if($date == null)
			$date = Carbon::now()->toDateString();

		Cache::forever('make_rank_date', $date);
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
			}	
		}
		else{
			if(Cache::has('make_rank_offset')){
				$offsets = Cache::get('make_rank_offset');
				return $offsets[$market];
			}
			else{
				return -1;
			}
		}	
	}

	public function update_make_rank_offset($market, $offset=null){
		if($offset = null)
			$offset = -1;
		Cache::forever('make_rank_offset', $offset);
	}

	public function convert_into_url($market, $codes){
		$full_codes = array();
		if(is_array($codes)){
			foreach($codes as $code)
				$full_codes[] = $this->get_fullcode($market, strtolower($code));
		}
		else
			$full_codes[] = $this->get_fullcode($market, strtolower($codes));

		if(!empty($full_codes))
			return $this->market[$market]['query_url_head'] . implode(',', $full_codes);
		else
			return false;
	}


/*******************************************************************************/

	//arr: type->1.全部资产  2. 现金资产  3. 股市资产  4. 期货资产
	public function get_asset_rank($arr){
		extract($arr);

	}

	public function update_asset_rank(){

		$chunk_size = 10; //changable
		$ret = array();

		$market_platform = config('share.market_platform');
		$market_platform_price_query = config('share.market_platform_price_query');
		$fullcode_prefix = config('share.fullcode_prefix');   
		//现金
		//$rank_by_coins = Investor::orderBy('coins', 'desc')->take(15)->get(); 
		$ret['all'] = Investor::orderBy('coins', 'desc')->take(15)->get(); 
		//$ret['all'] = $rank_by_coins->toArray()->keyBy('');
		//foreach($rank_by_coins as $d){
			//$ret['all'][$d->iid] = $d;
		//}


		//股市
		$sid_arr = array();
		$fullsid_arr = array();
		$fullsid_chunks = array();
		$query_url_batch = array();
		$json_content = array();
		$new_prices = array();

		$investors = Investor::with('shares')->get()->each(function($investor, $index){
			global $sid_arr;
			$investor->asset = 0;
			foreach($investor->shares as $share){
				if($share->atStockMarket == 'us')
					$themarket = 'nas';
				else
					$themarket = $share->atStockMarket;
				if(intval($share->amount) > 0 && !in_array($share->sid, $sid_arr[$themarket]))
					$sid_arr[$themarket][] = $share->sid;
					$fullsid_arr[$themarket][] = $fullcode_prefix[$themarket] . strtolower($share->sid); 
			}
		});		

		foreach($fullsid_arr as $mid => $sdata){
			$fullsid_chunks[$mid] = array_chunk($sdata, $chunk_size);
		}
		foreach($fullsid_chunks as $mid => $sdata){
			$thisplatform = $this->get_platform($mid);
			$query_prefix = $market_platform_price_query[$thisplatform];
			if($query_prefix){
				foreach($sdata as $chunk_data)
					if(!empty($chunk_data))
						$query_url_batch[$mid][] = $query_prefix . implode(',', $chunk_data); 
			} 
		}

		dd($query_url_batch);

		//get updated price
		if(count($query_rul_batch) > 0){
			foreach($query_url_batch as $mid => $urls){
				foreach($urls as $url){
					$json_content[$mid][] = mb_convert_encoding(file_get_contents($url), 'UTF-8', 'GBK');

				}
			}
			if(!empty($json_content)){
				foreach($json_content as $mid => $contents){
					foreach($contents as $j){
						$temp2 = explode("\n", $j);
						foreach($temp2 as $fragment){
					    	$temp = explode("=", $fragment);
					    	$temp3 = explode(" ", $temp[0]);
					    	$pattern = "/^hq_str_/";
					    	$id_fragment = preg_replace($pattern, '', $temp3[1]);
					    	if($mid == 'sh' || $mid == 'sz' || $mid =="hk")
					    		$code = preg_replace("/^.{2}/", '', $id_fragment);

					    	else
					    		$code = preg_replace("/^.{3}/", '', $id_fragment);

					    	$res = $temp[1];
				    		$pattern = "/\"|\";" .  '/';
				    		$res = preg_replace($pattern, '', $res);
				    		$data = explode(",", $res);
				    		if($mid == 'sh' || $mid == 'sz')
				    			$new_prices[$mid][$code] = floatval($data[3]);
				    		else if ($mid == 'nas')
				    			$new_prices[$mid][$code] = floatval($data[1]);
				    		else if ($mid == 'hk')
				    			$new_prices[$mid][$code] = floatval($data[6]);	
			    		}			    		
					}
				}
			}
		}

	}  	
}	

