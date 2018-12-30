<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    protected $primaryKey = 'sid';
    protected $fillable = ['stock_index', 'name', 'status', 'atStockMarket'];

    protected $table = 'shares';

    public function investors(){
        return $this->belongsToMany('App\Investor', 'have_shares', 'sid', 'iid')->withPivot('amount', 'buying_price', 'selling_price', 'by_currency', 'buying_at', 'selling_at')->withTimestamps();
    }

    public function market(){
    	return $this->belongsTo('App\Market', 'atStockMarket');
    }    
}


