<?php
namespace App\Services;

use App\Tools\MarketTimeZone;
use Cache;
use DateTime;
use DateTimeZone;

//use Config;

class MarketRankService {
   	protected $local_times, $open_times;

	protected $config  = array();
	protected $market = array();
	protected $index = array();
	protected $currency = array();
	protected $rankUpdatedEvery = 60;

	//use MarketTimeZone;

	public function __construct(){
		//$this->middleware('cache');
		$this->config = Cache::get('global_config');
		$this->market = Cache::get('market');
		$this->index = Cache::get('index');
		$this->currency = Cache::get('currency');
		$this->extra_share_config = Cache::get('extra_share_config');
		//dd($this->extra_share_config);
      	$this->local_times = $this->get_local_time();

      	//dd($this->local_times);

        //$this->open_times = config('share.MarketOpenTime');	
        $this->open_times = $this->extra_share_config['MarketOpenTime'];
        //$this->location = config('share.MarketLocationMap');
        $this->location = $this->extra_share_config['MarketLocationMap'];
       // $this->rankUpdatedEvery = config('share.RankUpdatedEvery');	
        $this->rankUpdatedEvery = $this->extra_share_config['RankUpdatedEvery'];
        //dd($this->open_times);
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


}

?>