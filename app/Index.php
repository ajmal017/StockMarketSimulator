<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Index extends Model
{
   protected $table = 'markets_index';

   protected $primaryKey = 'inid';

   public function market(){
   		return $this->belongsTo('App\Market', 'belongsToMarket');
   }     
}
