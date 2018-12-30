<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Investor extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bind_to', 'level', 'coins',
    ];

    protected $guarded = [
        'name', 'password', 'email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $table = 'investors';

    protected $primaryKey = 'iid'; 

    public function isAdmin(){
        return Auth::user()->is_admin;
    }

    public function shares(){

        return $this->belongsToMany('App\Share', 'have_shares', 'iid', 'sid')->withPivot('amount', 'buying_price', 'selling_price', 'by_currency', 'buying_at', 'selling_at')->withTimestamps();
    } 

    public function currency(){
        return $this->belongsTo('App\Currency', 'bind_to');
    } 

    public function profile(){
        return $this->hasOne('App\Profile', 'belongsTo', 'iid');
    } 
    
    public function avator(){
        return $this->hasOne('App\Avator', 'belongsTo', 'iid');
    }   

    public function events(){
        return $this->hasMany('App\Event', 'bywhom', 'iid');
    }
}