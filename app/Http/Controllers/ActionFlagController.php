<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\ActionFlag;
use Carbon\Carbon;
use App\UnfollowNextId;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ActionFlagController extends Controller
{

    //どのアクションを実行するかのフラグをセットする。
    public function setStartFlag(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        $flags = $request->flags;
        $follow_flag = $flags['follow'];
        $unfollow_flag = $flags['unfollow'];
        $like_flag = $flags['like'];

        ActionFlag::updateOrCreate(
            [
                'account_id'=>$account_id
            ],
            [
                'follow_target_flag'=>$follow_flag,
                'unfollow_target_flag'=>$unfollow_flag,
                'like_flag'=>$like_flag,
                'follow_flag'=>false,
                'unfollow_flag'=>false,
                'follow_end_flag'=>!$follow_flag,
                'unfollow_end_flag'=>!$unfollow_flag,
                'like_end_flag'=>!$like_flag,
                'mail_flag'=>false,
                'auto_restart_flag'=>false,
                'error_flag'=>false
            ]
        );
        UnfollowNextId::updateOrCreate([
            'account_id'=>$account_id
        ],
        [
            'timeline_flag'=>null,
            'timeline_next_id'=>null,
            'lookup_next_id'=>null,
            'lookup_flag'=>null
        ]);
        return ;
    }
    
    //error_flagをtrueにする。　trueだと全てのアクションを実行しなくなる。
    public function stopAction($account_id){
        try{
            $action_flag = ActionFlag::where('account_id',$account_id)->first();
            ActionFlag::where('account_id',$account_id)->update(['error_flag'=>true]);
            DB::commit();
            return;
        }catch(\Exception $e){
            Log::debug($e);
            return false;
        }
    }

    //自動で再開させるフラグをtrueにする。自動再開するエラーのとき用
    public function autoRestart($account_id){
        try{
            $action_flag = ActionFlag::where('account_id',$account_id)->first();
            $action_flag->auto_restart_flag = true;
            $action_flag->error_flag = true;
            $action_flag->save();
            DB::commit();
            return;
        }catch(\Exception $e){
            Log::debug($e);
            return false;
        }
        
    }

    //全てのアクションのフラグを取得する。
    //空の場合はnullを入れる。
    public function getActionFlag(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        $action_flag = ActionFlag::where('account_id',$account_id)->first();
        if(isset($action_flag->follow_flag)){
            $follow_flag = $action_flag->follow_flag;
        }else{
            $follow_flag = null;
        }
        if(isset($action_flag->follow_target_flag)){
            $follow_target_flag = $action_flag->follow_target_flag;
        }else{
            $follow_target_flag = null;
        }
        if(isset($action_flag->unfollow_flag)){
            $unfollow_flag = $action_flag->unfollow_flag;
        }else{
            $unfollow_flag = null;
        }
        if(isset($action_flag->unfollow_target_flag)){
            $unfollow_target_flag = $action_flag->unfollow_target_flag;
        }else{
            $unfollow_target_flag = null;
        }
        if(isset($action_flag->like_flag)){
            $like_flag = $action_flag->like_flag;
        }else{
            $like_flag = null;
        }
        if(isset($action_flag->error_flag)){
            $error_flag = $action_flag->error_flag;
        }else{
            $error_flag = null;
        }
        if(isset($action_flag->auto_restart_flag)){
            $auto_restart_flag = $action_flag->auto_restart_flag;
        }else{
            $auto_restart_flag = null;
        }
        return response()->json([
            'auto_restart_flag' => $auto_restart_flag,
            'error_flag' => $error_flag,
            'follow_flag' => $follow_flag, 
            'follow_target_flag' => $follow_target_flag,
            'unfollow_flag' => $unfollow_flag, 
            'unfollow_target_flag' => $unfollow_target_flag,
            'like_flag' => $like_flag,
        ]);
    }

    //エラーフラグとツイッター名、オートリスタートフラグを取得する。
    public function getErrorFlag(){
        $user_id = Auth::id();
        $account = Account::where('user_id',$user_id)->with('action_flags')->get();
        $errorFlags = [];
        foreach($account as $value){
            $twitter_screen_name = $value->twitter_screen_name;
            if(isset($value->action_flags[0]) && isset($value->action_flags[0]->error_flag)){
                $error_flag = $value->action_flags[0]->error_flag;
            }else{
                $error_flag = 0;
            }
            if(isset($value->action_flags[0]) && isset($value->action_flags[0]->auto_restart_flag)){
                $auto_restart_flag = $value->action_flags[0]->auto_restart_flag;
            }else{
                $auto_restart_flag = 0;
            }
            $errorFlags[] = [
                'twitter_screen_name'=>$twitter_screen_name,
                'error_flag'=>$error_flag,
                'auto_restart_flag'=>$auto_restart_flag];
        }
        return $errorFlags;
    }
}
