<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActionFlag;
use App\Account;


//手動でアクションを終わらせるときに使う。
class stopAction extends Controller
{
    public function __invoke(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        try{
            ActionFlag::updateOrCreate(
                [
                    'account_id'=>$account_id
                ],
                [
                    'follow_target_flag'=>false,
                    'unfollow_target_flag'=>false,
                    'like_flag'=>false,
                    'follow_flag'=>false,
                    'unfollow_flag'=>false,
                    'follow_end_flag'=>true,
                    'unfollow_end_flag'=>true,
                    'like_end_flag'=>true,
                    'mail_flag'=>true,
                    'auto_restart_flag'=>false,
                    'error_flag'=>false
                ]
            );
            return;
        }catch(\Exception $e){
            Log::debug($e);
            return false;
        }
    }
}
