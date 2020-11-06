<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\TargetListFollower;
use App\TargetList;
use App\ActionFlag;
use App\User;
use App\Account;
use App\LikeWord;
use App\FollowedList;
use App\UnfollowedList;
use App\UnfollowUser;
use App\UnfollowNextId;
use App\SearchWord;
use Carbon\Carbon;
use App\Http\Requests\AutoFollowRequest;
use DateTime;
use App\Exceptions\ApiErrorException;


class TwitterApiController extends Controller
{
    
    private $error_handling; //エラーチェック、発生時に使う。
    private $home_controller;
    public function __construct()
    {
        $this->error_handling = app()->make('App\Http\Controllers\ErrorHandlingController');
        $this->home_controller = app()->make('App\Http\Controllers\HomeController');
    }

    //twitter認証画面に移動させる
    public function twitter()
    {
        $twitter = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret')
        );
        # 認証用のrequest_tokenを取得
        # このとき認証後、遷移する画面のURLを渡す
        $token = $twitter->oauth('oauth/request_token', array(
            'oauth_callback' => config('twitter.callback_url')
        ));

        # 認証画面で認証を行うためSessionに入れる
        session(array(
            'oauth_token' => $token['oauth_token'],
            'oauth_token_secret' => $token['oauth_token_secret'],
        ));

        # 認証画面へ移動させる
        ## 毎回認証をさせたい場合： 'oauth/authorize'
        ## 再認証が不要な場合： 'oauth/authenticate'
        $url = $twitter->url('oauth/authorize', array(
            'oauth_token' => $token['oauth_token']
        ));
        return redirect($url);
    }
    //認証画面から戻ってきた時。 DBのAccountにツイッターアカウント情報を保存
    public function twitterCallback(Request $request)
    {
        if(isset($request->denied)){
            return redirect('/kamitter/home');
        }
        $oauth_token = session('oauth_token');
        $oauth_token_secret = session('oauth_token_secret');
        # request_tokenが不正な値だった場合エラー
        if ($request->has('oauth_token') && $oauth_token !== $request->oauth_token) {
            return Redirect::to('/');
        }
        # request_tokenからaccess_tokenを取得
        $twitter = new TwitterOAuth(
            $oauth_token,
            $oauth_token_secret
        );

        $token = $twitter->oauth('oauth/access_token', array(
            'oauth_verifier' => $request->oauth_verifier,
            'oauth_token' => $request->oauth_token,
        ));
        $oauth_token = $token['oauth_token'];
        $oauth_token_secret = $token['oauth_token_secret'];
        # access_tokenを用いればユーザー情報へアクセスできるため、それを用いてTwitterOAuthをinstance化
        $twitter_user = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            $oauth_token,
            $oauth_token_secret
        );
        # プロフィール取得
        $twitter_user_info = $twitter_user->get('account/verify_credentials');
        $twitter_user_id = $twitter_user_info->id;
        $twitter_screen_name = $twitter_user_info->screen_name;

        $account = new Account;
        $id = Auth::id();
        $user = User::find($id);

        //保存したツイッターアカウントが１０こ以上ならばキャンセル
        $count = Account::where('user_id',$id)->count();
        if($count>10){
            return redirect('/kamitter/home');
        }
        $twitter = Account::where('twitter_user_id',$twitter_user_id)->first();
       
        if($twitter)
       {    //既に登録済みのツイッターアカウントの場合は更新をする
           $twitter->fill(['oauth_token'=>$oauth_token,'oauth_token_secret'=>$oauth_token_secret,'twitter_user_id'=>$twitter_user_id,
           'twitter_screen_name'=>$twitter_screen_name]);
            $user->accounts()->save($twitter);
       }
       else{
           //そうでない場合は新規作成をする
            $account->fill(['oauth_token'=> $oauth_token,'oauth_token_secret'=>$oauth_token_secret,'twitter_user_id'=>$twitter_user_id,
            'twitter_screen_name'=>$twitter_screen_name]);
            $user->accounts()->save($account);
       }
        return redirect('/kamitter/home');
    }


    /**
     * 
     * 
     * 
     * 
     * ターゲットリスト
     * 対象のアカウントからフォロワーを全て取得する。
     * followers/listでプロフィールを取得しfilterListでdescription（プロフィール)とid_strとfollowing(自分がフォローしているか)を取得する。
     * それらのデータをDBに保存する。
     * 
     * 
     * 
     */
    
    public function createFolowerTargetList($value){
        $account_id = $value['account_id'];
        try{
            $account = Account::with(['target_lists','followed_lists','unfollowed_lists'])->where('id',$account_id)->first();
            $search_words = SearchWord::where('account_id',$account_id)->get();
        }catch(\Exception $e){
            Log::debug($e);
            $this->error_handling->dbError($account_id);
            return false;
        }
        $followTargetLimit = $this->followTargetLimit($account);
        if($followTargetLimit === false){
            return false;
        }
        $word_list = ['normal'=>[],'or'=>[],'ng'=>[]];
        foreach($search_words as $word){
            switch ($word->type_word_id){
                case 1:
                    $word_list['normal'][] = $word->word;
                break;
                case 2:
                    $word_list['or'][] = $word->word;
                break;
                case 3:
                    $word_list['ng'][] = $word->word;
                break;
            }
        }
        $unfollowed_lists = $account->unfollowed_lists;
        $my_twitter_user_id = $account->twitter_user_id;
        $followed_lists = $account->followed_lists;
        $oauth_token = $account->oauth_token;
        $oauth_token_secret = $account->oauth_token_secret;
        if(!isset($account->target_lists[0])){
            $actionflag = ActionFlag::where('account_id',$account_id)->first();
            $actionflag->fill(['follow_target_flag'=>false,'follow_flag'=>true]);
            $actionflag->save();
            return false;
        }
        $target_list = $account->target_lists[0];
        $target_lists_id = $target_list->id;
        $target_account_name = $target_list['target'];
        $connection = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            $oauth_token,
            $oauth_token_secret
        );
        $rate_limit = $this->rateLimit($account_id,$oauth_token,$oauth_token_secret,'followers');
        $list_remaining = $rate_limit['resources']->followers->{'/followers/list'}->remaining;
        //カーソルが保存されている場合はそのカーソルを設定する
        $next_cursor_str = $target_list->next_cursor_str;
        $cursor = (isset($next_cursor_str))?$next_cursor_str :'-1';
        $params = [
            'cursor' => $cursor,
            'count' => '200',
            'skip_status' => 'false',
            'screen_name' => $target_account_name,
        ];
        //ターゲットリストのユーザーのフォロワー数を調べる。
        //ターゲットのフォロワーを全て取得する。
        $target_followers = []; //ターゲットのフォロワー
        $target_list_followers = [];
        $count = 0;
        do {
            $response = $connection->get('followers/list', $params);
            //エラーではない場合
            if(isset($response->users)){
                $target_followers = array_merge($target_followers, $response->users);
                $params['cursor'] = $response->next_cursor_str;
            }
            $count +=1;
            //apiの回数分followers/listを実行するかapiエラーにかかった場合
            if ($count >= $list_remaining || !isset($response->users)){
                $target_list->next_cursor_str = $params['cursor']; //続きからするためにカーソルを保存する。
                $target_list->save();
                //DBに途中まで作成したフォロワーリストを保存する
                //その際にフィルターをして必要な情報だけを保存する。
                $filterList = $this->filterList($target_followers);
                $filterWord = $this->filterWord($filterList,$word_list);
                $followerTargetList = $this->excludeUser($filterWord ,$followed_lists,$oauth_token,$oauth_token_secret,$my_twitter_user_id,$unfollowed_lists,$account_id);
                if($followerTargetList===false){
                    return false;
                }
                foreach($followerTargetList as $target){
                    array_push($target_list_followers,
                    ['target_lists_id'=>$target_lists_id,
                    'description'=>$target['description'],
                    'id_str' => $target['id_str'],
                    'following' => $target['following'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString()]);
                }
                try{
                    DB::table('target_list_followers')->insert($target_list_followers);
                }catch(\Exception $e){
                    Log::debug($e);
                    $this->error_handling->dbError($account_id);
                    return false;
                }
                if($count >= $list_remaining){
                    return false;
                }else{
                    $response = (array)$response;
                    $this->error_handling->errorHandling($account_id,$response);
                    return false;
                }
            }
        } while ($params['cursor']); 
        //cursorが０になったらループが終わる。
        $target_list->next_cursor_str = $params['cursor']; //cursorを保存する。
        $target_list->save();
        //取得した全てのフォロワーからフォロワーターゲットリストを作る。
        $filterList = $this->filterList($target_followers);
        $filterWord = $this->filterWord($filterList,$word_list);
        $followerTargetList = $this->excludeUser($filterWord ,$followed_lists,$oauth_token,$oauth_token_secret,$my_twitter_user_id,$unfollowed_lists,$account_id);
        if($followerTargetList===false){
            return false;
        }
        //フォロワーターゲットリストをDBに保存する。
        foreach($followerTargetList as $target){
            array_push($target_list_followers,
            ['target_lists_id'=>$target_lists_id,
            'description'=>$target['description'],
            'id_str' => $target['id_str'],
            'following' => $target['following'],
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()]);
        }
        try{
            DB::table('target_list_followers')->insert($target_list_followers);
            $actionflag = ActionFlag::where('account_id',$account_id)->first();
            $actionflag->fill(['follow_target_flag'=>false,'follow_flag'=>true]) ;
            $actionflag->save();
        }catch(\Exception $e){
            Log::debug($e);
            $this->error_handling->dbError($account_id);
            return false;
        }
        
    }

    /**
     * フォローしているユーザー
     * アンフォローしたユーザー
     * 30日以内にフォローしたユーザー
     * フォローリクエストを送ったユーザー
     * を除外する。
     */
    public function excludeUser($target_list,$followed_lists,$oauth_token,$oauth_token_secret,$my_twitter_user_id,$unfollowed_lists,$account_id){
        $follower_target_list = [];
        $unfollow_user_id = [];

        for($i=0; $i<count($unfollowed_lists);$i++){
            $unfollow_user_id[] = $unfollowed_lists[$i]['unfollowed_id'];
        }
        $connection = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            $oauth_token,
            $oauth_token_secret
        );
        //30日以内にフォローした人だけを入れる。
        $three_month_users =[];
        $now = new DateTime();
        for($i=0; $i<count($followed_lists); $i++){
            $create = new DateTime($followed_lists[$i]->created_at);
            $diff = $create->diff($now);
            if($diff->format('%d')<30){
                array_push($three_month_users,$followed_lists[$i]->followed_id);
            }
        }
        //フォローリクエストを送った相手
        $outgoing_id_list;
        $outgoing = (array)$connection->get('friendships/outgoing', ['stringify_ids' => true]);
        //エラー処理
        $error = $this->error_handling->errorHandling($account_id,$outgoing);
        if($error === false){
            return false;
        }else{
            if(isset($outgoing['ids'])){
                $outgoing_id_list = $outgoing['ids'];
            }else{
                $outgoing_id_list = [];
            }
        }
        //フォロー処理
        foreach ($target_list as $user) {
            if(
                $user['following'] == false && //フォローしているユーザー入れない
                $user['id_str'] !== $my_twitter_user_id &&  //自分自身のIDが含まれている場合は除外する
                (!in_array($user['id_str'], $outgoing_id_list))&& //フォローリクエストしてるユーザーを除外
                (!in_array($user['id_str'], $unfollow_user_id))&& //アンフォローしたユーザーを除外
                (!in_array($user['id_str'], $three_month_users))&& //３0日いないにフォローしたユーザーを除外
                preg_match("/[ぁ-ん]+|[ァ-ヴー]+/u",$user['description']) //日本語が含まれているユーザーである
                )
            {
                array_push($follower_target_list,$user);
            }
        }
        return $follower_target_list;
    }
    //descriptionとid_strとfollowingだけにする
    public function filterList($target_followers){
        $filterList = [];
        for($i =0; $i<count($target_followers);$i++){

            (array)$follow_value = array_filter
            (
                (array)$target_followers[$i],
                function($key1)
                {
                    return $key1 === 'description' || $key1 === 'id_str' || $key1 === 'following';
                },
                ARRAY_FILTER_USE_KEY
            );
            array_push($filterList,$follow_value);
        }
        return $filterList;
    }
    //プロフィールに含まれているキーワードからユーザーを絞り込む
    public function filterWord($filterList,$word_list){
        $ng_list = $word_list['ng'];
        $normal_list = $word_list['normal'];
        $or_list = $word_list['or'];

        $ng_filter = []; //ngワード対象外のユーザーを入れる。
        if(!empty($ng_list)){
            foreach($filterList as $user){
                foreach($ng_list as $ng){
                    //ユーザーのプロフィールにngワードが含まれているかを調べる
                    if(strpos($user['description'],$ng) !==false){
                        continue 2;
                    }
                }
                //1つも含まれていないときng_filterにユーザーを入れる。含まれていたらNGユーザーなので入れない。
                array_push($ng_filter,$user);
             }
        }else{
            //ngワードがない場合はフィルターをかける必要がないので全ていれる
            $ng_filter =array_merge($ng_filter,$filterList);
        }
        //ng_filterからさらに絞り込む
        $normal_filter = [];
        if(!empty($normal_list)){
            foreach($ng_filter as $user){
                foreach($normal_list as $normal){
                    if(strpos($user['description'],$normal) ===false){
                        //１つでも含まれていない場合はスキップ
                        continue 2;
                    }
                }
                //含まれている場合はユーザーを入れる
                array_push($normal_filter,$user);
            }
        }else{
            $normal_filter =array_merge($normal_filter,$ng_filter);
        }
        //normal_filterからさらに絞り込む
        $or_filter = [];
        if(!empty($or_list)){
            foreach($normal_filter as $user){
                foreach($or_list as $or){
                    if(strpos($user['description'],$or) !==false){
                        //１つでも含まれていたらいれる.
                        array_push($or_filter,$user);
                        continue 2;
                    }
                }
            }
        }else{
            $or_filter =array_merge($or_filter,$normal_filter);
        }
        //全てのフィルターにかけて残ったユーザーを返す、
        return $or_filter;
    }
    /**
     * 
     * 
     * 
     * 
     * アカウントフォロー
     * 
     * 
     * 
     * 
     */
    public function follow($value){
        $account_id = $value['account_id'];
        $follow_times = 15;
        $max_follow_times = 1000;
        //フォロー人数1000超えている場合は終わる。
        $follow_count = $this->home_controller->getFollowCountToday($account_id);
        if($follow_count >$max_follow_times){
            $this->endFollow($account_id);
            return false;
        }
        $account = Account::where('id',$account_id)->with(['target_list_followers'])->first();
        //前にフォローしたときから１５分経過していない場合は処理を終わる。
        $followLimit = $this->followLimit($account);
        if($followLimit === false){
            return false;
        }
        $target_list_followers = $account->target_list_followers;
        $oauth_token = $account->oauth_token;
        $oauth_token_secret = $account->oauth_token_secret;
        $connection = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            $oauth_token,
            $oauth_token_secret
        );
       
        //follow_listから15人を抽出する。
        $follow_list = [];
        foreach($target_list_followers as $follower){
            $follow_list [] = $follower->id_str;
        }
        $users_id_array = array_splice($follow_list,0,$follow_times);
        $users_id_comma = implode(',', $users_id_array);
        //フォローする十五人分のリストを作る。
        $list = (array)$connection->get('friendships/lookup',['user_id' => $users_id_comma]);
        $error = $this->error_handling->errorHandling($account_id,$list);
        if($error === false){
            return false;
        }

        //フォロー回数
        //基本15回で15回未満の場合があればその数値に設定する。
        if($max_follow_times - $follow_count <$follow_times){
            $num = $max_follow_times - $follow_count;
            $count = (count($list)>$num)? $num : count($list);
        }else{
            $count = (count($list)>$follow_times)? $follow_times : count($list);
        }
        for($i=0; $i<$count;$i++){
            $user = (array)$list[$i];
            //フォローしていないかをチェックする。
            if(!in_array('following',$user['connections'],true)){
                DB::beginTransaction();
                try{
                    $followed_list = new FollowedList;
                    $followed_list->fill(['followed_id'=>$user['id_str']]);
                    $account->followed_lists()->save($followed_list);
        
                    //ターゲットリストからフォローしたユーザーを削除。 
                    TargetListFollower::where('id_str',$user['id_str'])->delete();
                    $response = (array)$connection->post('friendships/create',['user_id'=>$user['id_str']]);
                    //エラーがあればDBに保存しないようにする。
                    $error = $this->error_handling->errorHandling($account_id,$response);
                    if($error === false){
                        throw new ApiErrorException();
                    }else{
                        DB::commit();
                    }
                }
                catch(ApiErrorException $e) {
                    //apiエラー
                    DB::rollBack();
                    return false;
                }catch(\Exception $e){
                    Log::debug($e);
                    $this->error_handling->dbError($account_id);
                    return false;
                }
            }
        }
        
        if(empty($follow_list)){
            DB::beginTransaction();
            try{
                $target_list = new TargetList;
                $action_flag = ActionFlag::where('account_id',$account_id)->first();
                $list = $target_list::where('account_id',$account_id)->first();
                if(isset($list)){
                    $target_list_id = $list->id;
                    $target_list::where('id',$target_list_id)->delete();
                }
                if(!DB::table('target_lists')->where('account_id', $account_id)->exists()){
                    if($action_flag->follow_target_flag === 1 && $action_flag->unfollow_flag === 0){
                        //フォロー数が5000以上か調べる。
                        $this->unfollowStartCheck($account,$action_flag,$account_id);
                    }
                    $this->endFollow($account_id);
                }else{
                    $action_flag->follow_target_flag = true;
                    $action_flag->follow_flag = false;
                    $action_flag->save();
                }
                DB::commit();
            }catch(\Exception $e){
                DB::rollBack();
                    Log::debug($e);
                    $this->error_handling->dbError($account_id);
                    return false;
            }
        }
        return;
    }
    
    /**
     * 
     * 
     * 
     * 
     * アンフォロー用のリストを作成する。
     * 
     * 
     * 
     * 
     */
    
    public function createUnfollowList($value){
        $account_id = $value['account_id'];
        $inactive_day = 15;
        $account = Account::with(['unfollow_next_ids','followed_lists','unfollow_setting','unfollowed_lists','unfollow_users'])->where('id',$account_id)->first();
        $action_flag = ActionFlag::where('account_id',$account_id)->first();
        //フォロー数が5000以上か調べる。
        $unfollowStartCheck = $this->unfollowStartCheck($account,$action_flag,$account_id);
        if($unfollowStartCheck === false){
             return false;
        }
        $unfollowTargetLimit = $this->unfollowTargetLimit($account);
        if($unfollowTargetLimit === false){
            return false;
        }
        $followed_lists = $account->followed_lists;
        $unfollow_next_ids = $account->unfollow_next_ids[0];
        $timeline_flag;
        $lookup_flag;
        $timeline_next_id;
        $lookup_next_id;
        $unfollow_users;
        $unfollowed_lists;
        (isset($unfollow_next_ids->timeline_flag))?$timeline_flag = $unfollow_next_ids->timeline_flag : $timeline_flag = 1;
        (isset($unfollow_next_ids->lookup_flag))?$lookup_flag = $unfollow_next_ids->lookup_flag : $lookup_flag = 1;
        (isset($unfollow_next_ids->timeline_next_id))?$timeline_next_id = $unfollow_next_ids->timeline_next_id : $timeline_next_id = null;
        (isset($unfollow_next_ids->lookup_next_id))?$lookup_next_id = $unfollow_next_ids->lookup_next_id : $lookup_next_id = null;
        (isset($account->unfollow_users))?$unfollow_users = $account->unfollow_users : $unfollow_users = [];
        (isset($account->unfollowed_lists))?$unfollowed_lists = $account->unfollowed_lists : $unfollowed_lists = [];

        $followed_setting_days = $account->unfollow_setting['days'];
        $oauth_token = $account->oauth_token;
        $oauth_token_secret = $account->oauth_token_secret;
        $connection = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            $oauth_token,
            $oauth_token_secret
        );
        
        $rate_limit = $this->rateLimit($account_id,$oauth_token,$oauth_token_secret,'statuses,friendships');
        $user_timeline_remaining = $rate_limit['resources']->statuses->{'/statuses/user_timeline'}->remaining;
        $friendships_lookup_remaining = $rate_limit['resources']->friendships->{'/friendships/lookup'}->remaining;

        //フォローしてからfollowed_setting_days日以上経過してる人だけにする。
        $elapsed_days_user_list =[];
        $now = Carbon::now();
        for($i=0; $i<count($followed_lists); $i++){
            $create = Carbon::parse($followed_lists[$i]->created_at);
            if($create->diffInDays($now)>=$followed_setting_days){ 
                array_push($elapsed_days_user_list,$followed_lists[$i]->followed_id);
            }
        }
       
        //アンフォローした人のリスト
        $unfollowed_list = [];
        foreach($unfollowed_lists as $user){
            $unfollowed_list[] = $user->unfollowed_id;
        }
        $next_id = UnfollowNextId::where('account_id',$account_id)->first();
        if($timeline_flag !== 0){
             //日付比較用
            $today = new DateTime('r');
            //アンフォローをする人リスト
            $unfollowed_array_list = [];
            foreach($unfollow_users as $user){
                $unfollowed_array_list[] = $user['id_str'];
            }
            //followed_setting_days日経過したユーザーリストから既にアンフォローしているユーザーを除外する、
            $check_users = array_diff($elapsed_days_user_list,$unfollowed_list);
            //既にチェック済みのユーザーを除外する。
            $check_users = array_diff($check_users,$unfollowed_array_list);
            //途中で中断したとき用のリストから除外する。

            $check_users = $this->filterCheckedUser($timeline_next_id,$check_users);
            /**
             * 15日以上非アクティブの人をunfollow_user_listに格納する
             */
            $unfollow_user_list = [];
            foreach($check_users as $id){
                //中断
                if($user_timeline_remaining <=0){
                    try{
                        if(!empty($unfollow_user_list)){
                            DB::table('unfollow_users')->insert($unfollow_user_list);
                        }
                        $next_id->timeline_next_id = $id;
                        $next_id->save();
                        return false;
                    }catch(\Exception $e){
                        Log::debug($e);
                        $this->error_handling->dbError($account_id);
                        return false;
                    }
                }
                $params = [
                    'user_id' => $id,
                    'count' => '1',
                    'trim_user' => 'true',
                    'tweet_mode' => 'extended',
                    'include_rts' => 'true',
                    'exclude_replies' => 'false'
                ];
                //ターゲットのタイムラインの最新の投稿を取得する。
                $response = (array)$connection->get('statuses/user_timeline', $params);
                if(isset($response['error'])&&$response['error']==='Not authorized.'){
                    //鍵アカウントのとき
                    array_push($unfollow_user_list,
                    ['account_id'=>$account_id,
                    'id_str'=>$id,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString()]);
                }else if(isset($response['error'])||isset($response['errors'])||isset($response['messages'])){
                    try{
                        if(!empty($unfollow_user_list)){
                            DB::table('unfollow_users')->insert($unfollow_user_list);
                            $next_id->timeline_next_id = $id;
                            $next_id->save();
                            $error = $this->error_handling->errorHandling($account_id,$response);
                        }
                        return false;
                    }catch(\Exception $e){
                        $this->error_handling->dbError($account_id);
                        return false;
                    }
                    return false;
                }
                else{
                    //取得した投稿の日付と現在の日付を比較して15日以上非アクティブならunfollow_user_listに格納する
                    //オブジェクトをarrayに変換している。
                    $response2 = (array)$response[0];
                    $created_at = (array)$response2['created_at'];
                    $create_day = new DateTime((string)$created_at[0]);
                    //日付比較 非アクティブ何日か　基本は15日
                    if($create_day->diff($today)->format('%d')>=$inactive_day){
                        array_push($unfollow_user_list,
                        ['account_id'=>$account_id,
                        'id_str'=>$id,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString()]);
                    }
                }
                $user_timeline_remaining -=1;
            }
            try{
                if(!empty($unfollow_user_list)){
                    DB::table('unfollow_users')->insert($unfollow_user_list);
                }
                $next_id->timeline_flag = false;
                $next_id->save();
            }catch(\Exception $e){
                Log::debug($e);
                $this->error_handling->dbError($account_id);
                return false;
            }
        }
        if($lookup_flag !== 0){
            $UnfollowUser = UnfollowUser::where('account_id',$account_id)->get();
            $UnfollowUser_id_list=[];
            foreach($UnfollowUser as $user){
                $UnfollowUser_list []=$user['id_str'];
            }
            //days日経過したユーザーリストから既にアンフォローしているユーザーを除外する、
            $check_users2 = array_diff($elapsed_days_user_list,$unfollowed_list);
            //アンフォローリストにあるユーザーを除外する。
            $check_users2 = array_diff($check_users2,$UnfollowUser_id_list);
            //続きから始めるよう
            $check_users2 = $this->filterCheckedUser($lookup_next_id,$check_users2);
            //followとunfollowでfriendships_lookup_remainingを使うので常に２回は使えるように残しておくためにマイナス２する。
            $friendships_lookup_remaining = $friendships_lookup_remaining-2;
            //０以下になった場合は処理を中断する。
            if($friendships_lookup_remaining <=0){
                return false;
            }
            /**
             * フォロー返しをしていないユーザーをunfollow_user_listに格納する。
             */
            $unfollow_user_list = [];
            for($i=0;$i<$friendships_lookup_remaining;$i++){ 
                $check_users2_list = array_splice($check_users2,0,100);
                $users_id = implode(',', $check_users2_list);
                $response = (array)$connection->get('friendships/lookup',['user_id' => $users_id]);
                $error = $this->error_handling->errorHandling($account_id,$response);
                if($error === false){
                    if(!empty($unfollow_user_list)){
                        DB::table('unfollow_users')->insert($unfollow_user_list);
                    }
                    $next_id->lookup_next_id = $check_users2_list[0];
                    $next_id->save();
                    return false;
                }
                //フォローされていないかを調べる。
                for($c=0; $c<count($response);$c++){
                    $user = (array)$response[$c];
                    if(!in_array('followed_by',$user['connections'],true)){
                        array_push($unfollow_user_list,
                        ['account_id'=>$account_id,
                        'id_str'=>$user['id_str'],
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString()]);
                    }
                }
                //空になったらおわる。
                if(empty($check_users2)){
                    break;
                }
                //中断。
                if($i >= $friendships_lookup_remaining-1){
                    try{
                        if(!empty($unfollow_user_list)){
                            DB::table('unfollow_users')->insert($unfollow_user_list);
                        }
                        $id = array_slice($check_users2,0,1);
                        $next_id->lookup_next_id = $id[0];
                        $next_id->save();
                        return false;
                    }catch(\Exception $e){
                        Log::debug($e);
                        $this->error_handling->dbError($account_id);
                        return false;
                    }
                    return false;
                }
            }
            try{
                if(!empty($unfollow_user_list)){
                    DB::table('unfollow_users')->insert($unfollow_user_list);
                }
                $next_id->lookup_flag = false;
                $next_id->save();
            }catch(\Exception $e){
                Log::debug($e);
                $this->error_handling->dbError($account_id);
                return false;
            }
        }
       
        DB::beginTransaction();
        try{
            $action_flag->fill(['unfollow_target_flag'=>false,'unfollow_flag'=>true]);
            $action_flag->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::debug($e);
            $this->error_handling->dbError($account_id);
            return false;
        }
        return false;
    }
    /**
     * 
     * 
     * 
     * 
     * アンフォロー
     * セッションに保存されたアンフォローリストを元にアンフォローする。
     * 
     * 
     * 
     */
    public function unfollow($value){
        $account_id = $value['account_id'];
        $account = Account::where('id',$account_id)->with(['unfollow_setting','unfollow_users'])->first();
        $unfollow_today_count = $this->home_controller->getUnfollowCountToday($account_id);
        $setting_unfollow_count = $account->unfollow_setting['people'];
        $unfollow_num = 15;
        //アンフォロー人数がsetting_unfollow_count人超えている場合は終わる。
        if($unfollow_today_count >=$setting_unfollow_count){
            $this->endUnfollow($account_id);
            return false;
        }
        $unfollow_user_list = $account->unfollow_users;
        $oauth_token = $account->oauth_token;
        $oauth_token_secret = $account->oauth_token_secret;
        $connection = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            $oauth_token,
            $oauth_token_secret
        );

        //15分いないにアンフォローをしたかを確認
        $unfollowLimit = $this->unfollowLimit($account);
        if($unfollowLimit === false){
            false;
        }

        $unfollow_array_list = [];
        foreach($unfollow_user_list as $follower){
            $unfollow_array_list [] = $follower->id_str;
        }
        //アンフォロワーリストから15人までフォローしているかを調べる
        $user_array = array_splice($unfollow_array_list,0,$unfollow_num);
        $user_ids = implode(',', $user_array);
        $user_list = (array)$connection->get('friendships/lookup',['user_id' => $user_ids]);
        $error = $this->error_handling->errorHandling($account_id,$user_list);
        if($error === false){
            return false;
        }
        //最大で15回までアンフォローをする。
        //1000人を超えそうな場合は1000人で終わるようにする。
        //アンフォロー数が設定した上限の回数を超えないようにする.
        $count = 0;
        if($setting_unfollow_count - $unfollow_today_count < $unfollow_num){
            $num = $setting_unfollow_count - $unfollow_today_count;
            $count = (count($user_list)>$num)? $num : count($user_list);
        }else{
            $count = (count($user_list)>$unfollow_num)? $unfollow_num : count($user_list);
        }

        $unfollow_users = new UnfollowUser;
        foreach($user_list as $user){
            if(in_array('following',$user->connections,true)){
                $count -=1;
                DB::beginTransaction();
                try{
                    $unfollowed_list = new UnfollowedList;
                    $unfollowed_list->fill(['unfollowed_id'=>$user->id_str]);
                    $account->unfollowed_lists()->save($unfollowed_list);
                    //アンフォローターゲットリストからフォローしたユーザーを削除。 
                    $unfollow_users::where('id_str',$user->id_str)->delete();
                    $response = (array)$connection->post('friendships/destroy', ['user_id' => $user->id_str]);
                    $error = $this->error_handling->errorHandling($account_id,$response);
                    if($error === false){
                        throw new ApiErrorException();
                    }else{
                        DB::commit();
                    }
                }
                catch(ApiErrorException $e) {
                    DB::rollBack();
                    return $response;
                }catch(\Exception $e){
                    DB::rollBack();
                    throw $e;
                }
            }else{
                $unfollow_users::where('id_str',$user->id_str)->delete();
                DB::commit();
            }
            if($count <= 0){
            break;
            }
        }
        if(empty($unfollow_array_list)){
            $this->endUnfollow($account_id);
        }
    }

    

    /**
     * 
     * 
     * 
     * 
     * いいね
     * 自動いいねは規約違反なので短い時間にいいねを大量にしないようにして
     * BANにならないように気をつけている
     * 
     * 
     * 
     */
    public function likeTweet($value){
        $account_id = $value['account_id'];
        try{
            $account = Account::where('id',$account_id)->first();
            $like_word = LikeWord::where('account_id',$account_id)->get();
        }catch(\Exception $e){
            Log::debug($e);
            $this->error_handling->dbError($account_id);
            return false;
        }
        $like_word_list = ['normal'=>[],'or'=>[],'ng'=>[]];
        foreach($like_word as $word){
            switch ($word->type_word_id){
                case 1:
                    $like_word_list['normal'][] = $word->word;
                break;
                case 2:
                    $like_word_list['or'][] = $word->word;
                break;
                case 3:
                    $like_word_list['ng'][] = $word->word;
                break;
            }
        }
        $oauth_token = $account->oauth_token;
        $oauth_token_secret = $account->oauth_token_secret;
        $connection = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            $oauth_token,
            $oauth_token_secret
        );
        $rate_limit = $this->rateLimit($account_id,$oauth_token,$oauth_token_secret,'statuses');
        $remaining_home_timeline = $rate_limit['resources']->statuses->{'/statuses/home_timeline'}->remaining;
        //likeした時間が１５分いないかを調べる。
        $like_limit = $account->like_limit;
        $now = new Carbon();
        if(isset($like_limit)){
            $like_limit = new Carbon($like_limit);
            if($like_limit->diffInSeconds($now) < 15*60){
                $this->endLike($account_id);
                return false;
            }
        }
        $account->like_limit = new Carbon();
        $account->save();
        $params = [
            'count' => '200',
            'trim_user' => 'false',
            'tweet_mode' => 'extended',
            'exclude_replies' => 'true'
        ];
        //タイムラインにあるツイートを可能な限り格納する。
        $home_timeline = [];
        for($i=0; $i<$remaining_home_timeline;$i++){
            $response = (array)$connection->get('statuses/home_timeline', $params);
            $this->error_handling->errorHandling($account_id,$response);
            $home_timeline = array_merge($home_timeline,$response);
            //取得したタイムラインの一番古いツイートのidを取得する。
            $value = count($response)-1;
            if($value <=0){
            break;
            }
            $response2 = (array)$response[$value];
            $tweet_id = $response2['id_str'];
            $params['max_id'] = $tweet_id;
            //max_idに一番古いツイートのidを入れることでこれより過去のツイートを取得する。
        }

        //先ほど取得したタイムラインから必要な情報だけを取り出したツイートリストを作る。
        $tweet_list = [];
        for($i=0; $i<count($home_timeline);$i++){
            $tweet = (array)$home_timeline[$i];
            $id_str = $tweet['id_str'];
            $full_text = $tweet['full_text'];
            $favorited = $tweet['favorited'];
            $tweet_list[] = ['id_str' => $id_str, 'description' => $full_text,'favorited' => $favorited];
        }


        //フィルターにかけてlikeするツイートに絞る。
        $list = $this->filterWord($tweet_list,$like_word_list);
        $favorites=[];
        $count = 0;
        //ツイートにいいねをする。　
        //最大で10ツイートいいねします。
        //自動いいねはツイッターの規約違反なのでいいねをする数を少なめに設定して誤魔化しています。
        foreach($list as $val){
            $count +=1;
            if($count > 10){
            break;
            }
            if($val['favorited']){
                continue;
            }
            $params = [
                'id' => $val['id_str'],
                'include_entities' => 'false',
            ];
            $rand_sleep_time = rand(50000,100000);
            usleep($rand_sleep_time);
            $favorites = (array)$connection->post('favorites/create', $params);
            if(!isset($favorites['id_str'])){
                if($favorites['errors'][0]['code'] === 139){
                    //既にlikeしているとき
                    continue;
                }
                else{
                    $this->error_handling->errorHandling($account_id,$favorites);
                    return false;
                }

            }
        }
        $this->endLike($account_id);
    }
    
    /**
     * 
     * 
     * 
     * 
     * その他
     * 
     * 
     * 
     * 
     */

     //apiのレート制限をチェックする。
    public function rateLimit($account_id,$oauth_token,$oauth_token_secret,$param){
        $connection = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
            $oauth_token,
            $oauth_token_secret
        );
        $params['resources'] = $param;
        $response = (array)$connection->get('application/rate_limit_status', $params);
        $this->error_handling->errorHandling($account_id,$response);
        return $response;
    }

    //ツイッターアカウントのフォロー数を調べる
    public function followCount($account){
        $twitter_user_id = $account->twitter_user_id;
        $connection = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret'),
        );
        $params = [
            'user_id' => $twitter_user_id,
        ];
        $friends_count = (array)$connection->get('users/show', $params);
        $error = $this->error_handling->errorHandling($account->id,$friends_count);
        if($error === false){
            return false;
        }
        $friends_count = $friends_count['friends_count'];
        return $friends_count;
    }

    /**
     * それぞれ前回から15分経過しているかを調べる
     * 経過している場合は現在時刻をDBに保存する。
     * true false　を返す。
     */

    public function followTargetLimit($account){
        $now = new Carbon();
        if(isset($account->follow_target_limit)){
            $follow_target_limit = new Carbon($account->follow_target_limit);
            if($follow_target_limit->diffInSeconds($now) < 15*60){
                return false;
            }
        }
        //現在の時間を記録する。        
        $account->follow_target_limit = new Carbon();
        $account->save();
        return true;
    }
    public function followLimit($account){
        $now = new Carbon();
        if(isset($account->follow_limit)){
            $follow_limit = new Carbon($account->follow_limit);
            if($follow_limit->diffInSeconds($now) < 15*60){
                return false;
            }
        }
        //フォローを開始するので現在の時間を記録する。        
        $account->follow_limit = new Carbon();
        $account->save();
        return true;
    }
    public function unfollowTargetLimit($account){
        $now = new Carbon();
        if(isset($account->unfollow_target_limit)){
            $unfollow_target_limit = new Carbon($account->unfollow_target_limit);
            if($unfollow_target_limit->diffInSeconds($now) < 15*60){
                return false;
            }
        }
        //フォローを開始するので現在の時間を記録する。        
        $account->unfollow_target_limit = new Carbon();
        $account->save();
        return true;
    }
    public function unfollowLimit($account){
        $now = new Carbon();
        if(isset($account->unfollow_limit)){
            $unfollow_limit = new Carbon($account->unfollow_limit);
            if($unfollow_limit->diffInSeconds($now) < 15*60){
                return false;
            }
        }
        //フォローを開始するので現在の時間を記録する。        
        $account->unfollow_limit = new Carbon();
        $account->save();
        return true;
    }

    //アンフォローターゲットリスト作成で使う、
    //既にチェック済みのユーザーを除外する。
    public function filterCheckedUser($next_id,$check_users){
        if(isset($next_id)){
            $check_users = array_unique($check_users);
            $check_users = array_values($check_users);
            $index = array_search($next_id,$check_users);
            array_splice($check_users,0,$index);
        }
        return $check_users;
    }

    /**
     * フォロー、いいね、アンフォローのアクションが終わったときに使う。
     * それぞれのend_flagをtrueにする。
     */
    public function endUnfollow($account_id){
        DB::beginTransaction();
        try{
            $next_id =UnfollowNextId::where('account_id',$account_id)->first();
            $action_flag = ActionFlag::where('account_id',$account_id)->first();
            $next_id->fill([
                'timeline_next_id'=>null,
                'timeline_flag'=>null,
                'lookup_next_id'=>null,
                'lookup_flag'=>null]);
            $next_id->save();
            $action_flag->fill([
                'unfollow_end_flag'=>true,
                'unfollow_flag'=>false
            ]);
            $action_flag->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::debug($e);
            $this->error_handling->dbError($account_id);
            return false;
        }
        $this->endCheck($account_id);
    }
    public function endLike($account_id){
        //アンフォロー完了
        DB::beginTransaction();
        try{
            $action_flag = ActionFlag::where('account_id',$account_id)->first();
            $action_flag->fill([
                'like_end_flag'=>true,
                ]);
            $action_flag->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::debug($e);
            $this->error_handling->dbError($account_id);
            return false;
        }
        $this->endCheck($account_id);
    }
    public function endFollow($account_id){
        DB::beginTransaction();
        try{
            $action_flag = ActionFlag::where('account_id',$account_id)->first();
            $action_flag->follow_end_flag = true;
            $action_flag->follow_flag = false;
            $action_flag->save();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::debug($e);
            $this->error_handling->dbError($account_id);
            return false;
        }
        $this->endCheck($account_id);
    }

    //全てのend_flagがtrueかを調べる。
    //全てtrueの場合はメールを送る。
    public function endCheck($account_id){
        $action_flag = ActionFlag::where('account_id',$account_id)->first();
        $follow_end_flag = $action_flag->follow_end_flag;
        $unfollow_end_flag = $action_flag->unfollow_end_flag;
        $like_end_flag = $action_flag->like_end_flag;
        if($follow_end_flag === 1 && $unfollow_end_flag === 1 && $like_end_flag=== 1){
            DB::beginTransaction();
            //メール送信
            try{
                $action_flag->fill([
                    'follow_flag'=>false,
                    'follow_target_flag'=>false,
                    'unfollow_flag'=>false,
                    'unfollow_target_flag'=>false,
                    'like_flag'=>false,
                    'follow_end_flag'=>false,
                    'unfollow_end_flag'=>false,
                    'like_end_flag'=>false,
                    'mail_flag'=>false,
                    'auto_restart_flag'=>false,
                    'error_flag'=>false
                ]);
                $action_flag->save();
                $send_mail = $this->send_mail = app()->make('App\Http\Controllers\ApiErrorMailController');
                $title = '[KamiTTer]自動機能が完了しました。';
                $sub_title = '自動機能が完了しました。';
                $text = '全ての自動機能が完了しました。';
                $send_mail->mail($account_id,$title,$sub_title,$text);
                DB::commit();
            }catch(\Exception $e){
                DB::rollBack();
                Log::debug($e);
                $this->error_handling->dbError($account_id);
                return false;
            }
            
        }
    }

    //フォロー数が5000以上か調べる。
    // true false　を返す。
    public function unfollowStartCheck($account,$action_flag,$account_id){
        $followCount = $this->followCount($account);
        if($followCount < 5000){
            try{
                $action_flag->fill(['unfollow_end_flag'=>true]);
                $action_flag->save();
                $this->endCheck($account_id);
            }catch(\Exception $e){
                DB::rollBack();
                Log::debug($e);
                $this->error_handling->dbError($account_id);
                return false;
            }
            return false;
        }else if($followCount >=5000){
            try{
                $action_flag = ActionFlag::where('account_id',$account_id)->first();
                if($action_flag->unfollow_end_flag === 1){
                    $action_flag->fill(['unfollow_end_flag'=>false]);
                    $action_flag->save();
                }
                return true;
            }catch(\Exception $e){
                DB::rollBack();
                Log::debug($e);
                $this->error_handling->dbError($account_id);
                return false;
            }
        }
        return true;
    }

}