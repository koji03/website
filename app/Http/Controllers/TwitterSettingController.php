<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TargetList;
use App\Account;
use App\SearchWord;
use App\LikeWord;
use App\TypeWord;
use App\UnfollowSetting;
use App\UnfollowUser;
use App\UnfollowNextId;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TwitterSettingController extends Controller
{
    private $twitter_screen_name;
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    //ターゲットリストを保存する。
    public function registerUserTargetList(Request $request){
        $target_id = $request->target_id;
        $twitter_screen_name = $request->twitter_screen_name;
        $account = Account::where('twitter_screen_name',$twitter_screen_name)->first();
        $targetList = new TargetList;
        $targetList->fill(['target'=>$target_id]);
        $account->target_lists()->save($targetList);
        return response()->json();
    }
    //ターゲットリストを取得する
    public function getTargetList(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $account = Account::with('target_lists')->where('twitter_screen_name',$twitter_screen_name)->first();
       return $account->target_lists;
    }
    public function deleteUserFromTargetList(Request $request){
        $target_id = $request->target_id;
        TargetList::where('target',$target_id)->delete();
        return response()->json();
    }
    //サーチ用キーワードをDBに保存する。
    public function saveWordList(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $word_list = $request->word_list;
        $account = Account::where('twitter_screen_name',$twitter_screen_name)->first();

        foreach ($word_list as $type => $list){
            if(empty($list[0])){
                continue;
            }
            //or ng normalが$typeに入る
            $search_word = new SearchWord;
            $type_word = TypeWord::where('word_type',$type)->first();
            foreach($list as $word){
                $type_word_id=$type_word->id;
                $search_word->fill(['word' => $word,'type_word_id'=>$type_word_id]);
                $account->search_words()->save($search_word);
            }
        }
        return response()->json();
    }

    //フォロワーサーチワードを連想配列で取得 
    public function getSearchWordList(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        //それぞれの値を取得
        $normal_words = SearchWord::where('account_id',$account_id)->where('type_word_id',1)->pluck('word');
        $or_words = SearchWord::where('account_id',$account_id)->where('type_word_id',2)->pluck('word');
        $ng_words = SearchWord::where('account_id',$account_id)->where('type_word_id',3)->pluck('word');
        //取得した値を格納していく
        $or_list = ['or'=>[]];
        foreach($or_words as $or){
            $or_list['or'][] = $or;
        }
        $normal_list = ['normal'=>[]];
        foreach($normal_words as $normal){
            $normal_list['normal'][] = $normal;
        }
        $ng_list = ['ng'=>[]];
        foreach($ng_words as $ng){
            $ng_list['ng'][] = $ng;
        }

        return [$or_list,$normal_list,$ng_list];
    }
    //フォロワーサーチワードを削除する
    public function deleteSerachWord(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $word_type = $request->type;
        $word = $request->word;
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        $type_word_id = TypeWord::where('word_type',$word_type)->first()->id;
        SearchWord::where('account_id',$account_id)->where('type_word_id',$type_word_id)->where('word',$word)->delete();

        return response()->json();
    }

    //いいね用ワードを保存する。
    public function saveLikeWordList(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $word_list = $request->word_list;
        $account = Account::where('twitter_screen_name',$twitter_screen_name)->first();
        
        foreach ($word_list as $type => $list){
            if(empty($list[0])){
                continue;
            }
            $like_word = new LikeWord;
            $type_word = TypeWord::where('word_type',$type)->first();
            foreach($list as $word){
                $type_word_id=$type_word->id;
                $like_word->fill(['word' => $word,'type_word_id'=>$type_word_id]);
                $account->like_words()->save($like_word);
            }
        }
    }

    //いいね用ワードを削除する。
    public function deleteLikeWord(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $word_type = $request->type;
        $word = $request->word;
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        $type_word_id = TypeWord::where('word_type',$word_type)->first()->id;
        LikeWord::where('account_id',$account_id)->where('type_word_id',$type_word_id)->where('word',$word)->delete();

        return response()->json();
    }

    //いいね用ワードを取得する。
    public function getLikeWordList(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        //それぞれの値を取得
        $normal_words = LikeWord::where('account_id',$account_id)->where('type_word_id',1)->pluck('word');
        $or_words = LikeWord::where('account_id',$account_id)->where('type_word_id',2)->pluck('word');
        $ng_words = LikeWord::where('account_id',$account_id)->where('type_word_id',3)->pluck('word');

        //取得した値を格納していく
        $or_list = ['or'=>[]];
        foreach($or_words as $or){
            $or_list['or'][] = $or;
        }
        $normal_list = ['normal'=>[]];
        foreach($normal_words as $normal){
            $normal_list['normal'][] = $normal;
        }
        $ng_list = ['ng'=>[]];
        foreach($ng_words as $ng){
            $ng_list['ng'][] = $ng;
        }

        return [$or_list,$normal_list,$ng_list];
    }

    //経過日数とアンフォロー上限人数を保存
    public function saveUnfollowSetting(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $days = $request->number['days'];
        $people = $request->number['people'];
        $account_id = Account::where('twitter_screen_name',$twitter_screen_name)->first()->id;
        
        //既に値がある場合はアップデート。それ以外は新規
        DB::beginTransaction();
        try{
            UnfollowSetting::updateOrCreate(
                ['account_id'=>$account_id],
                ['days'=>$days,
                'people'=>$people,
                ]
            );
            UnfollowNextId::updateOrCreate(
                ['account_id'=>$account_id],
                ['timeline_next_id'=>null,
                'timeline_flag'=>null,
                'lookup_next_id'=>null,
                'lookup_flag'=>null
                ]
            );
            UnfollowUser::where('account_id',$account_id)->delete();
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            Log::debug($e);
            return false;
        }
        return response()->json();
    }

    //アンフォローの設定を取得する。
    public function getUnfollowSetting(Request $request){
        $twitter_screen_name = $request->twitter_screen_name;
        $account = Account::with('unfollow_setting')->where('twitter_screen_name',$twitter_screen_name)->first();
        $unfollow_setting = $account->unfollow_setting;
        return $unfollow_setting;
    }
}
