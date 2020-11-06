<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowedList extends Model
{
    protected $fillable = ['followed_id','account_id','created_at'];
    public function account(){
        return $this->belongsTo('App\Account');
    }
}
