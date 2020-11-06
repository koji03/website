<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnfollowNextId extends Model
{
    protected $fillable = ['account_id','timeline_next_id','timeline_flag','lookup_next_id','lookup_flag'];
    protected $visible =[
        'timeline_next_id','timeline_flag','lookup_next_id','lookup_flag'
    ];
    public function account(){
        return $this->belongsTo('App\Account');
    }
}
