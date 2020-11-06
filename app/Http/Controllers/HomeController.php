<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Account;
use App\User;
use App\UnfollowedList;
use App\FollowedList;
use App\ActionFlag;
use Carbon\Carbon;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    //登録しているTwitterのアカウントを全て取得する
    public function getTwitterIdList()
    {
        $id = Auth::id();
        $account_list = Account::get()->where('user_id',$id);
        return $account_list;
    }

    //アカウントリストから選択されたアカウントを一つ削除する
    public function deleteAccountList(Request $request){
        $account = new Account;
        $twitter_screen_name = $request->twitter_screen_name;
        $response = $account->where('twitter_screen_name',$twitter_screen_name)->delete();
        return $response;
    }

    //24時間以内にフォローした人の人数
    public function getFollowCountToday($account_id){
        $dt = new Carbon();
        $today = Carbon::now();
        $yesterday = $dt->subDay();
        $count = FollowedList::where('account_id',$account_id)->whereBetween('created_at',[$yesterday,$today])->count();
        return $count;
    }
    public function getFollowCount(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        $dt = new Carbon();
        $today = Carbon::now();
        $yesterday = $dt->subDay();
        $count = FollowedList::where('account_id',$account_id)->whereBetween('created_at',[$yesterday,$today])->count();
        return $count;
    }
    public function getUnfollowCount(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        $dt = new Carbon();
        $today = Carbon::now();
        $yesterday = $dt->subDay();
        $count = UnfollowedList::where('account_id',$account_id)->whereBetween('created_at',[$yesterday,$today])->count();
        return $count;
    }
    
    //24時間以内にアンフォローした人の人数
    public function getUnfollowCountToday($account_id){
        $dt = new Carbon();
        $today = Carbon::now();
        $yesterday = $dt->subDay();
        $count = UnfollowedList::where('account_id',$account_id)->whereBetween('created_at',[$yesterday,$today])->count();
        return $count;
    }

    //フォローした人の累計
    public function totalFollowCount($twitter_screen_name){
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        $dt = new Carbon();
        $today = Carbon::now();
        $yesterday = $dt->subDay();
        $count = FollowedList::where('account_id',$account_id)->count();
        return $count;
    }

    //アンフォローした人の累計
    public function totalUnfollowCount($twitter_screen_name){
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        $dt = new Carbon();
        $today = Carbon::now();
        $yesterday = $dt->subDay();
        $count = UnfollowedList::where('account_id',$account_id)->count();
        return $count;
    }

    //自分以外のツイッターアカウントをgetパラメータに入力していないかを確認
    public function parameCheck(Request $request){
        $user_id = Auth::id();
        $twitter_screen_name = $request->twitter_screen_name;
        $response = DB::table('accounts')->where('user_id', $user_id)->where('twitter_screen_name',$twitter_screen_name)->exists();
        if($response){
            return abort(200);
        }else{
            return abort(404);
        }
    }

    
}
