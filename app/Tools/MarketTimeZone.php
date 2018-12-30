<?
namespace App\Tools;
use DateTime;
use DateTimeZone;

trait MarketTimeZone{
	
	public function get_local_time(){
		$ret = array();
		$timezones = config('share.GlobalTimezone');
		//dd($timezones);
		//$marketzones = array();
		$market_time_zones = config('share.MarketTimezone');
		//dd($market_time_zones);
		foreach($market_time_zones as $mid => $mval){
			$timezone = $timezones[$mval];
			$new_date_time = $this->make_date_time_now($timezone, 'Y-m-d H:i:s');
			//dd($new_date_time);
			/*
			$date_time = new DateTime('NOW');
			$date_time->setTimezone(new DateTimeZone($timezone));
			$new_date_time = new DateTime($date_time->format('Y-m-d H:i:s'));
			*/
			$ret[$mid] = $new_date_time->getTimestamp();
		}
		//dd($ret);
		return $ret;
	}

	public function make_date_time_now($timezone, $format){
		$date_time = new DateTime('NOW');
		$date_time->setTimezone(new DateTimeZone($timezone));
		$new_date_time = new DateTime($date_time->format($format));
		return $new_date_time;
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

	public function countdown($market, $open_markets, $open_times){
		$ret = 0;
		if(in_array($market, $open_markets))
			return $ret;
		else{
			//dd('i am here');
			$timezones = config('share.GlobalTimezone');
			$market_time_zones = config('share.MarketTimezone');

			$timezone = $timezones[$market_time_zones[$market]];
			$now_date = $this->make_date_time_now($timezone, 'Y-m-d H:i:s');

			$times = array();

			$open_str = $open_times[$market];
			$now = date('Hi', $now_date->getTimestamp());
			$nowweekday = date('D', $now_date->getTimestamp());

			if($nowweekday != 'Sat' && $nowweekday != 'Sun'){
				$temp = explode(',', $open_str);
				$times_comp = array();
				foreach($temp as $t){
					$temp2 = explode('-', $t);
					$times_comp[] = $temp2[0];
					$times_comp[] = $temp2[1];
					$temp3 = explode(':', $temp2[0]);
					$times[] = $temp3[0] . $temp3[1];
					$temp4 = explode(':', $temp2[1]);
					$times[] = $temp4[0] . $temp4[1];	
				}
				if(count($times) == 2){
					 if($now < $times[0]) {
					 	$open = date('Y-m-d', $now_date->getTimestamp()) . ' ' . $times_comp[0];
					 	$open_date = new DateTime($open);
					 }
					 elseif($now > $times[1]){
					 	$open = date('Y-m-d', $now_date->getTimestamp()) . ' ' . $times_comp[0];
					 	$open_date = new DateTime($open);
					 	$open_date->modify('+1 day');
					}
					
					//$diff = $now_date->diff($open_date)	;
					//$ret = $diff->s;
					$ret = $open_date->getTimestamp() - $now_date->getTimestamp();
				}

				elseif (count($times) == 4){
					//dd($times);
					if($now < $times[0]){
					 	$open = date('Y-m-d', $now_date->getTimestamp()) . ' ' . $times_comp[0];
					 	$open_date = new DateTime($open);
					 	//dd($open);
					 	//dd($open_date->format('Y-m-d H:i:s'));
					}
					elseif ($now > $times[1] && $now < $times[2]){
					 	$open = date('Y-m-d') . ' ' . $times_comp[2];
					 	$open_date = new DateTime($open);
					}
					elseif ($now > $times[3]){
					 	$open = date('Y-m-d', $now_date->getTimestamp()) . ' ' . $times_comp[0];
					 	$open_date = new DateTime($open);
					 	$open_date->modify('+1 day');
					 	//dd($open_date->format('Y-m-d H:i:s'));
					}
					//$diff = $now_date->diff($open_date);
					$ret = $open_date->getTimestamp() - $now_date->getTimestamp();
					//dd($ret);
				}
			}	
		}
		return $ret;
	}

	protected function ntp_time($host) {
	  
	  // Create a socket and connect to NTP server
	  $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	  socket_connect($sock, $host, 123);
	  
	  // Send request
	  $msg = "\010" . str_repeat("\0", 47);
	  socket_send($sock, $msg, strlen($msg), 0);
	  
	  // Receive response and close socket
	  socket_recv($sock, $recv, 48, MSG_WAITALL);
	  socket_close($sock);

	  // Interpret response
	  $data = unpack('N12', $recv);
	  $timestamp = sprintf('%u', $data[9]);
	  
	  // NTP is number of seconds since 0000 UT on 1 January 1900
	  // Unix time is seconds since 0000 UT on 1 January 1970
	  $timestamp -= 2208988800;
	  
	  return $timestamp;
	}
	
}	