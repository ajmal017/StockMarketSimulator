<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    protected $fillable = [
        'avator', 'medals', 'belongsTo',
    ];   

    protected $table = 'profile';

    protected $primaryKey = 'pid';


    public function investor(){
        return $this->belongsTo('App\Investor', 'belongsTo');
    }      

}
