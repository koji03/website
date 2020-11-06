<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnfollowedList extends Model
{
    protected $fillable = ['unfollowed_id','account_id'];
    protected $visible =[
        'unfollowed_id'
    ];

    public function account(){
        return $this->belongsTo('App\Account');
    }
}
