<?php
namespace App\Tools;
use App\Investor;
use App\Market;
use App\Index;
use App\Config;
use App\Share;
use App\Currency;
use Session;
use Cache;
use Response;

trait ShareTools{	

	public function get_share($query_url, $market, $platform='sina'){
		$float_range = 0.02; //可更改

		$ret = array();
		if($platform == 'sina'){
	    	$json_content = mb_convert_encoding(file_get_contents($query_url), 'UTF-8', 'GBK');
	    	$temp = explode("=", $json_content);
	    	$res = $temp[1];
    		$pattern = "/\"|\";" .  '/';
    		$res = preg_replace($pattern, '', $res);
    		$temp = explode(",", $res);


			$ret['name'] = '';	    	
			$ret['opening_price'] = 0; //今日开盘价
			$ret['closing_price'] = 0; //昨日收盘价
			$ret['current_price'] = 0; //当前价格
			$ret['highest_price'] = 0; //今日最高价
			$ret['lowest_price'] = 0; //今日最低价
			$ret['bid_price'] = 0; //竞买价=买1
			$ret['asked_price'] = 0; //竞卖价=卖1
			$ret['trade_volume'] = 0; //成交量
			$ret['volume_unit'] = 0;
			$ret['unit_in_word'] = '';
			$ret['trade_amount'] = 0; //成交额

			$ret['buying_list_volume1'] = 0; //买1量
			$ret['buying_list_price1'] = 0; //买1价
			$ret['buying_list_volume2'] = 0; //买2量
			$ret['buying_list_price2'] = 0; //买2价
			$ret['buying_list_volume3'] = 0; //买3量
			$ret['buying_list_price3'] = 0; //买3价
			$ret['buying_list_volume4'] = 0; //买4量
			$ret['buying_list_price4'] = 0; //买4价
			$ret['buying_list_volume4'] = 0; //买5量
			$ret['buying_list_price4'] = 0; //买5价	

			$ret['selling_list_volume1'] = 0; //卖1量
			$ret['selling_list_price1'] = 0; //卖1价
			$ret['selling_list_volume2'] = 0; //卖2量
			$ret['selling_list_price2'] = 0; //卖2价
			$ret['selling_list_volume3'] = 0; //卖3量
			$ret['selling_list_price3'] = 0; //卖3价
			$ret['selling_list_volume4'] = 0; //卖4量
			$ret['selling_list_price4'] = 0; //卖4价
			$ret['selling_list_volume5'] = 0; //卖5量
			$ret['selling_list_price5'] = 0; //卖5价

			$ret['date'] = ''; //日期
			$ret['time'] = ''; //时间

			$unit_in_word = config('share.unit_in_word');
			$volume_unit = config('share.volume_unit');

			$ret['volume_unit'] = 1;
			$ret['unit_in_word'] = '';
    		
    		if ($market == 'sh' || $market == 'sz'){
				$ret['name'] = $temp[0];	    	
				$ret['opening_price'] = $temp[1]; //今日开盘价
				$ret['closing_price'] = $temp[2]; //昨日收盘价
				$ret['current_price'] = $temp[3]; //当前价格
				$ret['highest_price'] = $temp[4]; //今日最高价
				$ret['lowest_price'] = $temp[5]; //今日最低价
				$ret['bid_price'] = $temp[6]; //竞买价=买1
				$ret['asked_price'] = $temp[7]; //竞卖价=卖1
				$ret['trade_volume'] = $temp[8]; //成交量
				//$ret['volume_unit'] = 100;
				$ret['volume_unit'] = intval($volume_unit[$market]);
				$ret['unit_in_word'] = $unit_in_word[$market];
				$ret['trade_amount'] = $temp[9]; //成交额

				$ret['buying_list_volume1'] = $temp[10]; //买1量
				$ret['buying_list_price1'] = $temp[11]; //买1价
				$ret['buying_list_volume2'] = $temp[12]; //买2量
				$ret['buying_list_price2'] = $temp[13]; //买2价
				$ret['buying_list_volume3'] = $temp[14]; //买3量
				$ret['buying_list_price3'] = $temp[15]; //买3价
				$ret['buying_list_volume4'] = $temp[16]; //买4量
				$ret['buying_list_price4'] = $temp[17]; //买4价
				$ret['buying_list_volume4'] = $temp[18]; //买5量
				$ret['buying_list_price4'] = $temp[19]; //买5价	

				$ret['selling_list_volume1'] = $temp[20]; //卖1量
				$ret['selling_list_price1'] = $temp[21]; //卖1价
				$ret['selling_list_volume2'] = $temp[22]; //卖2量
				$ret['selling_list_price2'] = $temp[23]; //卖2价
				$ret['selling_list_volume3'] = $temp[24]; //卖3量
				$ret['selling_list_price3'] = $temp[25]; //卖3价
				$ret['selling_list_volume4'] = $temp[26]; //卖4量
				$ret['selling_list_price4'] = $temp[27]; //卖4价
				$ret['selling_list_volume5'] = $temp[28]; //卖5量
				$ret['selling_list_price5'] = $temp[29]; //卖5价

				$ret['date'] = $temp[30]; //日期
				$ret['time'] = $temp[31]; //时间																									 
    		}

    		elseif($market == 'nas' || $market == 'ny' || $market == 'us'){
				$ret['name'] = $temp[0];	    	
				$ret['opening_price'] = $temp[5]; //今日开盘价
				$ret['closing_price'] = $temp[26]; //昨日收盘价
				$ret['current_price'] = $temp[1]; //当前价格
				$ret['highest_price'] = $temp[6]; //今日最高价
				$ret['lowest_price'] = $temp[7]; //今日最低价
				//$ret['bid_price'] = $temp[6]; //竞买价=买1
				$ret['bid_price'] = $this->float_rand(floatval($ret['current_price']) * (1 - $float_range), floatval($ret['current_price']));
					
				//$ret['asked_price'] = $temp[7]; //竞卖价=卖1
				$ret['asked_price'] = $this->float_rand(floatval($ret['current_price']), floatval($ret['current_price']) * (1 + $float_range));
				$ret['trade_volume'] = $temp[10]; //成交量
				//$ret['volume_unit'] = 100;
				//$ret['unit_in_word'] = '手';
				
				$ret['buying_list_volume1'] = mt_rand(100, 20000); //买1量
				$ret['buying_list_price1'] = $ret['bid_price']; //买1价


				$ret['selling_list_volume1'] = mt_rand(100, 20000); //卖1量
				$ret['selling_list_price1'] = $ret['asked_price']; //卖1价

				$times = explode(' ', $temp[3]);

				$ret['date'] = $times[0]; //日期
				$ret['time'] = $times[1]; //时间				    			
    		}

    		if ($market == 'hk'){
				$ret['name'] = $temp[1]; //中文名
				$ret['ename'] = $temp[0]; //英文名	    	
				$ret['opening_price'] = $temp[2]; //今日开盘价
				$ret['closing_price'] = $temp[3]; //昨日收盘价
				$ret['current_price'] = $temp[6]; //当前价格
				$ret['highest_price'] = $temp[4]; //今日最高价
				$ret['lowest_price'] = $temp[5]; //今日最低价
				//$ret['bid_price'] = $temp[6]; //竞买价=买1
				$ret['bid_price'] = $temp[9];
					
				//$ret['asked_price'] = $temp[7]; //竞卖价=卖1
				$ret['asked_price'] = $temp[10];
				$ret['trade_volume'] = $temp[12]; //成交量
				$ret['trade_amount'] = $temp[11]; //成交额
				//$ret['volume_unit'] = 100;
				//$ret['unit_in_word'] = '手';
				
				$ret['buying_list_volume1'] = mt_rand(100, 20000); //买1量
				$ret['buying_list_price1'] = $ret['bid_price']; //买1价


				$ret['selling_list_volume1'] = mt_rand(100, 20000); //卖1量
				$ret['selling_list_price1'] = $ret['asked_price']; //卖1价

				//$times = explode(' ', $temp[3]);

				$ret['date'] = $temp[17]; //日期
				$ret['time'] = $temp[18]; //时间	    			
    		}	
    		return $ret;
		}
	}

	
	public function get_platform($market){
		$search = config('share.market_platform');
		//dd($market);
		//dd($search);
		$ret = '';
		foreach($search as $p => $pdata){
			if (in_array($market, $pdata))
				return $p;
		}
		return $ret;
	}

	public function get_fullcode($market, $code){
		$prefix = config('share.fullcode_prefix.' . $market);
		return $prefix . $code;
	}

	protected function float_rand($min=0,$max=1,$mul=1000000, $round=2){
	    if ($min>$max) return false;
	    return round(mt_rand($min*$mul,$max*$mul)/$mul, $round);
	}

	protected function number_shorten($number, $precision = 2, $divisors = null) {

	    // Setup default $divisors if not provided
	    if (!isset($divisors)) {
	        $divisors = array(
	            pow(1000, 0) => '', // 1000^0 == 1
	            pow(1000, 1) => 'K', // Thousand
	            pow(1000, 2) => 'M', // Million
	            pow(1000, 3) => 'B', // Billion
	            pow(1000, 4) => 'T', // Trillion
	            pow(1000, 5) => 'Qa', // Quadrillion
	            pow(1000, 6) => 'Qi', // Quintillion
	        );    
	    }

	    // Loop through each $divisor and find the
	    // lowest amount that matches
	    foreach ($divisors as $divisor => $shorthand) {
	        if (abs($number) < ($divisor * 1000)) {
	            // We found a match!
	            break;
	        }
	    }

	    // We found our match, or there were no matches.
	    // Either way, use the last defined value for $divisor.
	    return number_format($number / $divisor, $precision) . $shorthand;
	}	

}

?>