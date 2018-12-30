<?php
namespace App\Tools;

trait ShowTools{

	public function show_trading_msg($data, $type, $extra){		
		$s_m = '<span class="event-sharename">' . $data['sharename'] . '</span>';
		$su_m = '';
		//return true;
		if($type == 'buy'){
			$a_m = '<span class="event-buy">购买</span>';
			$b_m = '<span class="event-buyingprice">' . $data['buying_price'] . '</span>';
			$su_m = ' ' . $data['amount'] . '股';
			//return $s_m . $a_m . $b_m;
		}
		else if ($type == 'sell'){
			if(!$extra){
				$a_m = '<span class="event-sell">卖出</span>';
				$su_m = ' ' . $data['amount'] . '股'; 
			}
			else{
				$a_m = '<span class="event-sell">清仓</span> (' . $data['amount'] . '股)';
				
			}

			$b_m = '<span class="event-sellingprice">' . $data['selling_price'] . '</span>';			
		}
		
		return $data['username'] . '以价格' . $b_m . $a_m . $s_m . $su_m; 
		//return '以价格' . $b_m . $a_m . $s_m . $su_m; 
	}

	public function show_admin_msg($data, $type, $extra=null){
		if($type == 'updateinvestor'){
			$a_m = '<span class="event-admin-updateperson">修改</span>'; 
			$i_m = '<span class="event-admin-investor">' . $data['investor'] . '</span>';
			return $data['admin'] . $a_m . '投资者' . $i_m . '参数';
		}

		else if ($type == 'baninvestor'){
			$a_m = '<span class="event-admin-banperson">禁止</span>'; 
			$i_m = '<span class="event-admin-investor">' . $data['investor'] . '</span>';
			return $data['admin'] . $a_m  . '投资者' . $i_m; 			
		}

		else if ($type == 'updatemarket'){
			$a_m = '<span class="event-admin-updateproperty">修改</span>'; 
			$m_m = '<span class="event-admin-property">' . $data['market'] . '</span>';
			return $data['admin'] . $a_m . $m_m; 
		}

		else if ($type == 'addmarket'){
			$a_m = '<span class="event-admin-addproperty">添加</span>'; 
			$m_m = '<span class="event-admin-property">' . $data['market'] . '</span>';
			return $data['admin'] . $a_m . $m_m; 
		}

		else if ($type == 'updateindex'){
			$a_m = '<span class="event-admin-updateproperty">修改</span>'; 
			$i_m = '<span class="event-admin-property">' . $data['index'] . '</span>';
			return $data['admin'] . $a_m . $i_m; 			
		}

		else if ($type == 'addindex'){
			$a_m = '<span class="event-admin-updateproperty">添加</span>'; 
			$i_m = '<span class="event-admin-property">' . $data['index'] . '</span>';
			return $data['admin'] . $a_m . $i_m; 				
		}

		else if ($type == 'updatecurrency'){
			$a_m = '<span class="event-admin-updateproperty">修改</span>'; 
			$i_m = '<span class="event-admin-property">' . $data['currency'] . '</span>';
			return $data['admin'] . $a_m . $i_m; 			
		}

		else if ($type == 'addcurrency'){
			$a_m = '<span class="event-admin-updateproperty">添加</span>'; 
			$i_m = '<span class="event-admin-property">' . $data['currency'] . '</span>';
			return $data['admin'] . $a_m . $i_m; 				
		}

		else if ($type == 'updateshare'){
			$a_m = '<span class="event-admin-updateproperty">修改</span>'; 
			$o_m = '<span class="event-admin-investor">' . $data['investor'] . '</span>';
			$s_m = '<span class="event-admin-property">' . $data['share'] . '</span>';
			return $data['admin'] . $a_m . $o_m . '所持有的股票' . $s_m; 			
		}

		else if ($type == 'sellshare'){
			$a_m = '<span class="event-admin-sellshare">强制卖出</span>'; 
			$o_m = '<span class="event-admin-investor">' . $data['investor'] . '</span>';
			$s_m = '<span class="event-admin-property">' . $data['share'] . '</span>';
			if(!$extra)
				$am_m = ' ' . $data['amount'] . '股';
			else
				$am_m = ' <span class="event-admin-notice">已经清仓!</span>'; 
			return $data['admin'] . $a_m . $o_m . '所持有的股票' . $s_m; 			
		}

		else if ($type == 'updateconfig'){
			$a_m = '<span class="event-admin-updateconfig">修改</span>'; 
			$c_m = '<span class="event-admin-property">' . '全局参数' . '</span>';
			return $data['admin'] . $a_m . $c_m; 				
		}								
	}

}	