<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
   protected $table = 'currency';

   protected $primaryKey = 'cid';     

   public function investors(){
   	  return $this->hasMany('App\Investor', 'bind_to', 'currency_index');	
   }

   public function markets(){
   	 return $this->hasMany('App\Market', 'allowed_currency', 'currency_index');

   }
     
}
