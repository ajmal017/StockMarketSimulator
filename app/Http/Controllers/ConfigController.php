<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Cache;
class ConfigController extends Controller
{
    public function getAllConfig(){
    	$ret = array();
    	$ret['market'] = Cache::get('market');
    	$ret['index'] = Cache::get('index');
    	$ret['config'] = Cache::get('global_config');
    	$ret['currency'] = Cache::get('currency');
    	$ret['extra'] = config('share');
    	//dd($ret);
    	return json_encode($ret);
    }	
}
