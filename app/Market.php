<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
   protected $table = 'markets';

   protected $primaryKey = 'mid';     

   public function shares(){
	  return $this->hasMany('App\Share', 'atStockMarket', 'market_index');
   }  
 
   public function index(){
   	  return $this->hasOne('App\Index', 'belongsToMarket', 'market_index');	
   }

   public function currency(){
   		return $this->belongsTo('App\Currency', 'allowed_currency');
   }   
}
