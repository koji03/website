<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TargetList extends Model
{
    protected $fillable = ['target','account_id','id'];
    protected $visible =[
        'target','next_cursor_str'
    ];
    public function account(){
        return $this->belongsTo('App\Account');
    }
    public function target_list_followers(){
        return $this->hasMany('App\TargetListFollower');
    }
}
