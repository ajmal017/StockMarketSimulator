<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Share;
use Session;



class UpdateShareController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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

    public function activate_all_shares(){
    	Share::where('status', '=',  1)
    				->update(['status' => 1]);
    	Session::flash('success', '全部激活');
    	return redirect('/home')->withTitle('');
    }

    public function sync_time(){
    	DB::table('shares')->where('status', 1)
    		->update(['created_at' => DB::raw('updated_at')]);
    	Session::flash('success', '同步完成');
    	return redirect('/home')->withTitle('');    		
    }

    public function initialize_us_share(){
        //$page = 'http://vip.stock.finance.sina.com.cn/usstock/ustotal.php';
        $page = 'http://proj.yucheung.com/usstocklist2.htm';
        $rules = array(
            'record' => ["a[title]", 'title'],
         ); 
        //列表选择器
        $rang = '';

        //采集
        $data = array();
        $temp = \QL\QueryList::Query($page,$rules)->data;
        //$data = $this->encode_char(\QL\QueryList::Query($page,$rules)->data); 
 
        //dd($temp);
       
       //process query data
        if (is_array($temp)){
            $walk = 0;
            foreach($temp as $t){
                $str_arr = explode(",", $t['record']);
                $data[$walk]['stock_id'] = $str_arr[0];
                $count = count($str_arr);
                if ($count == 3){                    
                    $data[$walk]['eng_name'] = $str_arr[1];
                    $data[$walk]['name'] = $str_arr[2];
                }
                
                else if ($count > 3){
                    $temp2 = array();
                    for($i = 1; $i < $count - 1; $i++ )
                        $temp2[] = $str_arr[$i];
                    $data[$walk]['eng_name'] = implode(' ', $temp2);
                    $data[$walk]['name'] = $str_arr[$count - 1];
                }
                
                $walk++;    
            }
        }
        //dd($data);    
        $us_stocks = ['nas', 'nys'];
        $us_stock = '';
        //inject into table shares
        foreach($data as $d){
            $us_stock = $us_stocks[array_rand($us_stocks)]; //randomly pick a stock market
            DB::table('shares')->insert(                [
                    'stock_index' => $d['stock_id'],
                    'name' => $d['name'],
                    'eng_name' => $d['eng_name'],
                    'status' => 1,
                    'atStockMarket' => $us_stock,                     
                ]
            );            

        }
        return 'Insert ' . count($data) . ' records! ';    
    }


}
