<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Tools\MarketTimeZone;
use App\Share;
use App\Investor;
use App\Market;
use App\Index;
use App\Currency;
use Session;
use Cache;


class MarketController extends Controller
{
	//protected $page = 'http://vip.stock.finance.sina.com.cn/q/go.php/vInvestConsult/kind/lhb/index.phtml';
	/*
	protected $page_up_from_sh = 'http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=1&num=10&sort=changepercent&asc=0&node=sh_a&symbol=';

	protected $page_down_from_sh = 'http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=1&num=10&sort=changepercent&asc=1&node=sh_a&symbol=';

	protected $page_up_from_sz = 'http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=1&num=10&sort=changepercent&asc=0&node=sz_a&symbol=';
	
	protected $page_down_from_sz = 'http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=1&num=10&sort=changepercent&asc=1&node=sz_a&symbol=';

	protected $us_stock_url_head = "http://stock.finance.sina.com.cn/usstock/api/jsonp.php/IO.XSRV2.CallbackList['";
	
	protected $us_stock_url_mid = "']/US_CategoryService.getList?page=1&num=10&sort=chg&";

	protected $up_token = 'asc=0&';
	protected $down_token = 'asc=1&';

	protected $ny_token = 'market=N&';
	protected $nas_token = 'market=O&';
	*/
    use MarketTimeZone;

    protected $local_times, $open_times;

	protected $config  = array();
	protected $market = array();
	protected $index = array();
	protected $currency = array();
	protected $rankUpdatedEvery = 60;

	public function __construct(){
		$this->middleware('cache');
		$this->config = Cache::get('global_config');
		$this->market = Cache::get('market');
		$this->index = Cache::get('index');
		$this->currency = Cache::get('currency');
      	$this->local_times = $this->get_local_time();
        $this->open_times = config('share.MarketOpenTime');	
        $this->location = config('share.MarketLocationMap');
        $this->rankUpdatedEvery = config('share.RankUpdatedEvery');	
	}	



	protected function randomString($length = 6) {
		$str = "";
		$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
		$max = count($characters) - 1;
		for ($i = 0; $i < $length; $i++) {
			$rand = mt_rand(0, $max);
			$str .= $characters[$rand];
		}
		return $str;
	}

	protected function encode(){
		$length = rand(5, 15);

		return $this->randomString($length);
	}

	//for sina chinese market json
	protected function fix_sina_json($str){
		//remove ticktime
		$pattern = '/ticktime:\"(20|21|22|23|[0-1]?\d):[0-5]?\d:[0-5]?\d\",/';
		$str = preg_replace($pattern, '', $str);

	   //if(preg_match('/\w:/', $str)){  	   		
	        $str = preg_replace('/(\w+):/is', '"$1":', $str);  
	    //} 		

	    return $str;
	}

	//for sina hk market json
	protected function fix_sina_hk_json($str){
		//remove ticktime
		$pattern = '/ticktime:\"[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\s(20|21|22|23|[0-1]?\d):[0-5]?\d:[0-5]?\d\",|\\\\\'/';
		$str = preg_replace($pattern, '', $str);

	   //if(preg_match('/\w:/', $str)){  	   		
	        $str = preg_replace('/(\w+):/is', '"$1":', $str);  
	    //} 		
	    //dd($str);    
	    return $str;
	}

	//for sina us market_json
	protected function fix_sina_us_json($str){
		$pattern = "/(IO\.XSRV2\.CallbackList\[\')" . "(\w+)" . "\'\]\(\(\{count:\"(\d+)\",data:|"  . "\}\)\);$"  . '/';
		$str = preg_replace($pattern, '', $str);

		return $this->fix_sina_json($str);
	}	
		
	protected function process_sina_json($market, $type){
		$rank_type = 'rank_' . $type . '_url'; 
		$temp_url = $this->market[$market][$rank_type];
		if($this->location[$market] == 'china' || $this->location[$market] == 'hk')
		//if ($market == 'sh' || $market == 'sz')			
			//$page = $this->{'page_' . $type . '_from_' . $market};
			$page = $temp_url;

		elseif ($this->location[$market] == 'us'){
		//else if ($market == 'ny' || $market == 'nas'){
			//$page = $this->us_stock_url_head . $this->encode() . $this->us_stock_url_mid . $this->{$market . '_token'} . $this->{$type . '_token'} . 'id=';
			//http://stock.finance.sina.com.cn/usstock/api/jsonp.php/IO.XSRV2.CallbackList['|']/US_CategoryService.getList?page=1&num=10&sort=chg&|asc=0&|market=N&

			$temps = explode("|", $temp_url);
			$page = $temps[0] . $this->encode() . $temps[1] . $temps[2] . $temps[3];
		}

		//elseif ($this->location[$market] == 'hk'){
			//$page = $temp_url;
		//}	

		//dd($page);
		$json_content = mb_convert_encoding(file_get_contents($page), 'UTF-8', 'GBK');

		//dd($json_content);
		$r_data = array();
		if($this->location[$market] == 'china')
		//if ($market == 'sh' || $market == 'sz')
			$json_content = $this->fix_sina_json($json_content);

		else if ($this->location[$market] == 'us')
		//else if ($market == 'ny' || $market == 'nas')
			$json_content = $this->fix_sina_us_json($json_content);

		else if ($this->location[$market] == 'hk')
			$json_content = $this->fix_sina_hk_json($json_content);

		//dd($json_content);
		$t_data = json_decode($json_content, true);
		//dd($t_data);

		if($this->location[$market] == 'china'){
		//if ($market == 'sh' || $market == 'sz'){
			foreach($t_data as $i => $t){
				$r_data[$i]['stock_id'] = $t['code'];
				$r_data[$i]['stock_name'] = $t['name'];
				//$r_data[$i]['stock_name_limit'] = str_limit($d['stock_name'], $limit = 10, $end = '...');
				$r_data[$i]['trading_price'] = $t['trade'];
				$r_data[$i]['change_percent'] = $t['changepercent'];
				$r_data[$i]['change_price'] = $t['pricechange'];
				$r_data[$i]['volume'] = round(intval($t['volume'])/1000000, 2);
				$r_data[$i]['amount'] = round(intval($t['amount'])/1000000, 2);
			}
		}
		elseif ($this->location[$market] == 'us'){
		//elseif ($market == 'ny' || $market == 'nas'){
			$max = count($t_data);
			//pick top 10
			if ($max > 0){
				if ($max >= 10)
					$max = 10;

				for ($i = 0; $i < $max; $i++){
					$r_data[$i]['stock_id'] = $t_data[$i]['symbol'];
					$r_data[$i]['stock_name'] = $t_data[$i]['cname'];
					$r_data[$i]['trading_price'] = $t_data[$i]['price'];				
					$r_data[$i]['change_percent'] = $t_data[$i]['chg'];
					$r_data[$i]['change_price'] = $t_data[$i]['diff'];
					$r_data[$i]['volume'] = round(intval($t_data[$i]['volume'])/1000000, 2);
					$r_data[$i]['amount'] = round(intval($t_data[$i]['mktcap'])/1000000, 2);		
				}	
			}

		}
		elseif ($this->location[$market] == 'hk'){
			foreach($t_data as $i => $t){
				$r_data[$i]['stock_id'] = $t_data[$i]['symbol'];
				$r_data[$i]['stock_name'] = $t_data[$i]['name'];
				$r_data[$i]['trading_price'] = $t_data[$i]['lasttrade'];				
				$r_data[$i]['change_percent'] = $t_data[$i]['changepercent'];
				$r_data[$i]['change_price'] = $t_data[$i]['pricechange'];
				$r_data[$i]['volume'] = round(intval($t_data[$i]['volume'])/1000000, 2);
				$r_data[$i]['amount'] = round(intval($t_data[$i]['amount'])/1000000, 2);				
			}	
		}
		//dd($r_data);
		return $r_data;					
	}	

	protected static function encode_char($arr, $source='UTF-8'){
		$ret = array();	
		foreach($arr as $k => $v){	
			if (is_array($v)){
				$ret[$k] = Self::encode_char($v, $source);
			}
			else{
				$ret[$k] = mb_convert_encoding($v, $source, "GBK");	
			}	
		}
		return $ret;
	}

	//这个函数每日定期更新一次
	public function update_market_cache($market='china'){
		if ($market == 'china'){

			$data['up']['sh'] = $this->process_sina_json('sh', 'up');
			$data['down']['sh'] = $this->process_sina_json('sh', 'down');
			$data['up']['sz'] = $this->process_sina_json('sz', 'up');
			$data['down']['sz'] = $this->process_sina_json('sz', 'down');


	        $data['date'] = date('Y-m-d H:m');

	        //dd($data);

	        //Cache::forget('chinese_market_rank');
	        if($data['up'] && $data['down'])
	        //Cache::put('chinese_market_rank', $data, $this->rankUpdatedEvery);
	        	Cache::forever('chinese_market_rank', $data);
	        
        }

        else if ($market == 'america'){
 			$data['up']['ny'] = $this->process_sina_json('ny', 'up');
			$data['down']['ny'] = $this->process_sina_json('ny', 'down');
			$data['up']['nas'] = $this->process_sina_json('nas', 'up');
			$data['down']['nas'] = $this->process_sina_json('nas', 'down');

	        $data['date'] = date('Y-m-d H:m');  
	        //Cache::forget('american_market_rank');
	        if($data['up'] && $data['down'])
	       	 //Cache::put('american_market_rank', $data, $this->rankUpdatedEvery);
	         Cache::forever('american_market_rank', $data);
        }	
 
 		else if ($market == 'hk'){
 			$data['up']['hk'] = $this->process_sina_json('hk', 'up');
 			$data['down']['hk'] = $this->process_sina_json('hk', 'down');
 			$data['date'] = date('Y-m-d H:m');

 			if($data['up'] && $data['down'])
 				//Cache::put('hk_market_rank', $data, $this->rankUpdatedEvery);
 				Cache::forever('hk_market_rank', $data);
 		}
        return $data;
	}

	public function update_market_all_cache(){
		$ret = array();
		$ret[] = $this->update_market_cache('china');
		$ret[] = $this->update_market_cache('america');
		$ret[] = $this->update_market_cache('hk');
		//dd($ret);
		return $ret;
	}	

    public function showmarket($market = 'sh'){
    	$current_route	= Route::currentRouteName();

    	$showtype = '';
    	//dd($current_route);
    	if ($current_route == 'showtrademarket')
    		$showtype = 'trade';

    	$json_content = '';
    	$ret = $r_data = array();

 		$value_up_class = 'us-value-up'; //国际惯用

 		if($this->location[$market] == 'china'){
    	//if ($market == 'sh' || $market == 'sz'){
    		//$img_url_head = "http://image.sinajs.cn/newchart/min/n/";
    		$value_up_class = 'value-up';
    		if (Cache::has('chinese_market_rank'))
    			$market_rank = Cache::get('chinese_market_rank');
    		else
    			$market_rank = $this->update_market_cache('china');
    		/**
			$market_rank = Cache::remember('chinese_market_rank',60, function() {
			    return $this->update_market_cache('china');
			});
			**/
		}

		else if ($this->location[$market] == 'us'){
    	//else if ($market == 'ny' || $market == 'nas'){
    		//$value_up_class = 'us-value-up';
    		//$img_url_head = "http://image.sinajs.cn/newchart/usstock/min_idx_py/";
    		if (Cache::has('american_market_rank'))
    			$market_rank = Cache::get('american_market_rank');
    		else
    			$market_rank = $this->update_market_cache('america');    		
    		/**
			$market_rank = Cache::remember('american_market_rank',60, function() {
			    return $this->update_market_cache('america');
			});
			**/

			//dd($market_rank);
		}		

		else if ($this->location[$market] == 'hk'){
    		if (Cache::has('hk_market_rank'))
    			$market_rank = Cache::get('hk_market_rank');
    		else
    			$market_rank = $this->update_market_cache('hk');   
		}

		$ret['market_rank'] = $market_rank;
		
		//dd($this->index);
		$ret['img_url'] = '';
		foreach($this->index as $index_index => $idata){
			if ($idata['belongsToMarket'] == $market)
				$ret['img_url'] = $idata['min_chart_url'] . '?' . time();

		}	

		//$ret['market_prefix'] = $market_prefix[$market];
		//$ret['img_url'] = $img_url_head . $market_img_url[$market] . '?' . time();
		//$ret['img_url'] = $this->index[$market]['min_chart_url'] . '?' . time();
		$ret['market_prefix'] = $this->market[$market]['name']; 
		$title = ' | ' . $this->market[$market]['name'] . '资讯';
		//$title = ' | ' . $market_prefix[$market] . '资讯';

		//dd($ret);
		//dd($value_up_class);
		$open_markets = $this->opened_markets($this->open_times, $this->local_times);
		$countdown = $this->countdown($market, $open_markets, $this->open_times);

		//get this index code
		$thisindex = Index::where('belongsToMarket', $market)->first()->index_index;
		
		$avator="";
		if(Session::get('iid'))
			$avator = Investor::find(Session::get('iid'))->avator()->first();
		//dd($countdown);
		//dd($open_markets);
		return view('pages.marketshow')->withTitle($title)->withData($ret)->with('thismarket', $market)->with('thisindex', $thisindex)->withMarket($this->market)->withIndex($this->index)->withConfig($this->config)->withCurrency($this->currency)->withShowtype($showtype)->with('value_up_class', $value_up_class)->with('open_markets', $open_markets)->withCountdown($countdown)->withLocation($this->location[$market])->withAvator($avator);
		

    }	

    public function get_info($market, $type){
    	$ret = array();
    	if($this->location[$market] == 'china'){
    	//if ($market == 'sh' || $market == 'sz'){
    		if (Cache::has('chinese_market_rank'))
    			$data = Cache::get('chinese_market_rank');
    		else
    			$data = $this->update_market_cache('china');

	    	if (!empty($data)){
	    		if ($type == 'up')
	    			$ret['data'] = $data['up'][$market];
	    		else if ($type == 'down')
	    			$ret['data'] = $data['down'][$market];
	    		$ret['date'] = $data['date'];
	    	}	
   		}

   		else if ($this->location[$market] == 'us'){
    	//else if ($market == 'ny' || $market == 'nas'){
	   		if (Cache::has('american_market_rank'))
	    			$data = Cache::get('american_market_rank');
	    	else
	    			$data = $this->update_market_cache('america');

	    	if (!empty($data)){
	    		if ($type == 'up'){
	    			$ret['data'] = $data['up'][$market];
	    		}
	    		else if ($type == 'down'){
	    			$ret['data'] = $data['down'][$market];
	    		}
	    		$ret['date'] = $data['date'];
	    		//$ret['data']['stock_name'] = str_limit($ret['data']['stock_name'], 10);

	    	}	
   		}

   		else if ($this->location[$market] == 'hk'){
	   		if (Cache::has('hk_market_rank'))
	    			$data = Cache::get('hk_market_rank');
	    	else
	    			$data = $this->update_market_cache('hk');

	    	if (!empty($data)){
	    		if ($type == 'up'){
	    			$ret['data'] = $data['up'][$market];
	    		}
	    		else if ($type == 'down'){
	    			$ret['data'] = $data['down'][$market];
	    		}
	    		$ret['date'] = $data['date'];

	    	}	
   		}
   		//将名称缩短
   		foreach($ret['data'] as $i => $v){
   			$ret['data'][$i]['stock_name_limit'] = str_limit($v['stock_name'], $limit = 10, $end = '...');
   		}
   		//dd($ret);
   		//dd(json_encode($ret));
   		return json_encode($ret);
    }


    public function get_fullcode_prefix(){
    	return json_encode($this->fullcode_prefix);
    }


}