<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnfollowSetting extends Model
{
    protected $fillable = ['days','people','account_id'];
    protected $visible =[
        'days','people'
    ];
    public function account(){
        return $this->hasOne('App\Account');
    }
    protected $attributes = [
        'days' => 7,
        'people' => 1000,
    ];
}
