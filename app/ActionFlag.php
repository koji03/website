<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionFlag extends Model
{
    protected $fillable = [
        'account_id','follow_flag','unfollow_flag','like_flag','follow_target_flag','unfollow_target_flag','error_flag','follow_end_flag','unfollow_end_flag','like_end_flag','mail_flag','auto_restart_flag'
        ];
    protected $visible = [
        'account_id','follow_flag','unfollow_flag','like_flag','follow_target_flag','unfollow_target_flag','error_flag','follow_end_flag','unfollow_end_flag','like_end_flag','mail_flag','auto_restart_flag'
    ];

    public function account(){
        return $this->belongsTo('App\Account');
    }
}
