<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduledTweet extends Model
{
    protected $fillable = ['id','scheduled_date','account_id','tweet_text'];

    public function Account(){
        return $this->belongsTo('App\Account');
    }

    public function photos()
    {
        return $this->hasMany('App\Photo');
    }
}
