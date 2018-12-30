<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Share;
use Session;
use Cache;

class NewsController extends Controller
{
    public function update_news_cache($market='china'){
    	if ($market = 'china'){
    		$page = 'http://finance.sina.com.cn/stock/';
			$rules = array(
					'news_title' => ['a', 'text'],
					'news_link' => ['a', 'href'],

		        ); 
		        //列表选择器
		        $rang = '.hdline';
		        //采集
		        $data = \QL\QueryList::Query($page,$rules,$rang, 'UTF-8','GB2312',true)->data;



    	}	

    }	
}
