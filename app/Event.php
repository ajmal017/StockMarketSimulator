<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'type', 'event', 'bywhom', 'created_at', 'updated_at',
    ];   

    protected $table = 'events';

    protected $primaryKey = 'eid';


    public function investor(){
        return $this->belongsTo('App\Investor', 'bywhom');
    }    
}
