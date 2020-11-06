<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\ActionFlag;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;


//エラー発生時、手動でリスタートするときに使う。
class Restart extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
     //それぞれのフラグをfalseにする。 
     //各種リミットに現在時刻を入れる。 TwitterApiControllerで現在時刻＋15分になったら開始するようになっている。
    public function __invoke(Request $request)
    {
        $twitter_screen_name = $request->twitter_screen_name;
        $flags = $request->flags;
        DB::beginTransaction();
        try{
            $twitter_screen_name = $request->twitter_screen_name;
            $account = Account::where('twitter_screen_name',$twitter_screen_name)->first();
            $account_id = $account->id;
            $action_flag = ActionFlag::where('account_id',$account_id)->first();
            $action_flag->fill(
                [
                    'error_flag'=>false,
                    'mail_flag'=>false,
                    'auto_restart_flag'=>false
                ]);
            $action_flag->save();
            $follow_limit = new Carbon;
            $unfollow_limit = new Carbon;
            $like_limit = new Carbon;
            $follow_target_limit = new Carbon;
            $unfollow_target_limit = new Carbon;
            $account->fill(
                [
                    'follow_limit'=>$follow_limit,
                    'unfollow_limit'=>$unfollow_limit,
                    'like_limit'=>$like_limit,
                    'follow_target_limit'=>$follow_target_limit,
                    'unfollow_target_limit'=>$unfollow_target_limit
                ]);
            $account->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::debug($e);
            return false;
        }
    }
}
