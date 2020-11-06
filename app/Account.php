<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['id','user_id','oauth_token','oauth_token_secret','twitter_user_id','twitter_screen_name','follow_limit','unfollow_limit','like_limit','follow_target_limit','unfollow_target_limit'];

    protected $visible =[
        'twitter_screen_name','oauth_token','oauth_token_secret','target_lists','id','unfollow_setting','unfollowed_lists','follow_limit','unfollow_limit','like_limit','search_words','target_list_followers','user_id','followed_lists','unfollow_users','unfollow_next_ids',
        'action_flags','unfollow_target_limit','follow_target_limit'
    ];
    
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function unfollow_setting(){
        return $this->hasOne('App\UnfollowSetting');
    }
    public function target_lists(){
        return $this->hasMany('App\TargetList');
    }
    public function search_words(){
        return $this->hasMany('App\SearchWord');
    }
    public function like_words(){
        return $this->hasMany('App\LikeWord');
    }
    public function followed_lists(){
        return $this->hasMany('App\FollowedList');
    }
    public function unfollowed_lists(){
        return $this->hasMany('App\UnfollowedList');
    }
    public function scheduled_tweets(){
        return $this->hasMany('App\ScheduledTweet');
    }
    public function target_list_followers(){
        return $this->hasManyThrough('App\TargetListFollower', 'App\TargetList','account_id','target_lists_id','id','id');
    }
    public function unfollow_users(){
        return $this->hasMany('App\UnfollowUser');
    }
    public function unfollow_next_ids(){
        return $this->hasMany('App\UnfollowNextId');
    }
    public function action_flags(){
        return $this->hasMany('App\ActionFlag');
    }
}
