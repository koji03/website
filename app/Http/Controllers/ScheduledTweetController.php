<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Account;
use App\ScheduledTweet;
use Carbon\Carbon;
use App\Photo;
use DateTime;
use Illuminate\Support\Facades\Storage;

/**
 * 予約ツイートをDBに保存する。
 */
class ScheduledTweetController extends Controller
{
    /**
     * ツイートをDBに保存します。　画像のデータはaws3　名前だけはDBにも保存します。
     */
    public function saveScheduled(Request $request){
        for($i=0; $i<4;$i++){
            $name = "photo{$i}";
            if($request->$name === 'undefined'||!is_object($request->$name)){
                break;
            }
            $validatedData = $this->validate($request,
            [
                $name => "file|image|max:10240|mimes:jpeg,png,jpg,svg,webp",
            ],
            [
                'photo0.file' => '1枚目の画像がアップロードされていません。',
                'photo0.uploaded' => '1枚目の画像のアップロードに失敗しました。',
                'photo0.max' => '1枚目の画像には10MB以下の画像を指定してください。',
                'photo0.image' => '1枚目には画像ファイルを指定してください。',
                'photo0.mimes' => '投稿できない拡張子があります。またgifは投稿することができません。',
    
                'photo1.file' => '2枚目の画像がアップロードされていません。',
                'photo1.uploaded' => '2枚目の画像のアップロードに失敗しました。',
                'photo1.max' => '2枚目の画像には10MB以下の画像を指定してください。',
                'photo1.image' => '2枚目には画像ファイルを指定してください。',
                'photo1.mimes' => '投稿できない拡張子があります。またgifは投稿することができません。',

    
                'photo2.file' => '3枚目の画像がアップロードされていません。',
                'photo2.uploaded' => '3枚目の画像のアップロードに失敗しました。',
                'photo2.max' => '3枚目の画像には10MB以下の画像を指定してください。',
                'photo2.image' => '3枚目には画像ファイルを指定してください。',
                'photo2.mimes' => '3枚目に投稿できない拡張子があります。またgifは投稿することができません。',

                
    
                'photo3.file' => '4枚目の画像がアップロードされていません。',
                'photo3.uploaded' => '4枚目の画像のアップロードに失敗しました。',
                'photo3.max' => '4枚目の画像には10MB以下の画像を指定してください。',
                'photo3.image' => '4枚目には画像ファイルを指定してください。',
                'photo3.mimes' => '4枚目に投稿できない拡張子があります。またgifは投稿することができません。',


            ]);
        }
        $twetter_screen_name = $request->twetter_screen_name;
        $scheduledTweetsId = $request->scheduledTweetsId;
        $account = Account::where('twitter_screen_name',$twetter_screen_name)->first();
        $tweet_text = $request->tweetText;
        $year = $request->year;
        $month= $request->month+1;
        $day = $request->day;
        
        $hours = $request->hours;  
        $minutes = $request->minutes;
        $seconds = $request->seconds;
        $setting_date = $year.'-'.$month.'-'.$day.' '.$hours.':'.$minutes.':'.$seconds;
        $date = new Carbon($setting_date);
        $scheduled_tweet = new ScheduledTweet;

        $scheduled_tweet_id;
        $photo_name_list2 = [];
        DB::beginTransaction();
        try{
            /**
             * scheduledTweetsIdがある場合はツイートの更新
             */
            if(isset($scheduledTweetsId)){
                $scheduled_tweet->where('id',$scheduledTweetsId)->update(['scheduled_date'=>$date,'tweet_text'=>$tweet_text]);
                $scheduled_tweet_id = $scheduled_tweet->where('id',$scheduledTweetsId)->first()->id;
                $scheduled_tweet = ScheduledTweet::where('id',$scheduled_tweet_id)->first();
            }else{
                $scheduled_tweet->fill(['scheduled_date'=>$date,'tweet_text'=>$tweet_text]);
                $account->scheduled_tweets()->save($scheduled_tweet);
                $scheduled_tweet_id = $scheduled_tweet->id;
                $scheduled_tweet = ScheduledTweet::where('id',$scheduled_tweet_id)->first();
            }
            //画像は最大で４枚までなので4回ループさせます。
            for($i=0; $i<4;$i++){
                $name = "photo{$i}";
                if($request->$name === 'undefined'){
                    break;
                }
                /**
                 * オブジェクトの場合は新規画像、もしくはツイートの更新（画像を変更した変更先の画像）なのでawsS3とDBに画像の情報を保存します。
                 * オブジェクトでない場合はawsS3にある画像なのでツイート内容の更新（画像を変更しなかった場合）になります。
                 * photo_name_list2にこれらの画像の名前を格納します。
                 * photo_name_list2には画像は新規画像、画像を変更した変更先の画像、画像を変更しなかった時の画像
                 * が格納されることになります。
                 */
                if(is_object($request->$name)){
                    $photo = new Photo();
                    $extension = $request->$name->extension();
                    // インスタンス生成時に割り振られたランダムなID値と
                    // 本来の拡張子を組み合わせてファイル名とする
                    $photo->filename = $photo->id . '.' . $extension;
                    Storage::cloud()->putFileAs('', $request->$name, $photo->filename, 'public');
                    $scheduled_tweet->photos()->save($photo);
                    $photo_name_list2 [] = $photo->filename;
                }else{
                    $photo_name_list2 [] = $request->$name;
                }
            }
            DB::commit();
        }catch (\Exception $exception) {
            DB::rollBack();
            for($i=0; $i<4;$i++){
                $name = "photo{$i}";
                if($request->$name === 'undefined'){
                    break;
                }
                Storage::cloud()->delete($photo->$name);
            }
            throw $exception;
        }

        /**
         * 
         * scheduledTweetsIdがある場合はツイートの更新
         * DBとawsS3の画像を削除するかの判定と削除。
         * 
         * dbから画像の情報を取得
         * photo_name_listにそれらの画像の名前を格納。
         * 
         * photo_name_listとphoto_name_list2を比較して
         * photo_name_list2に含まれていない画像はツイートの更新(画像を変更した時の変更前の画像)になるので
         * 削除をします。
         * 
         */
        if(isset($scheduledTweetsId)){
        DB::beginTransaction();
            try{
                $photo_list = Photo::where('scheduled_tweet_id',$scheduled_tweet_id)->get();
                $photo_name_list = [];
                foreach($photo_list as $photo){
                    $photo_name_list [] = $photo->filename;
                }
                $del_array = array_diff($photo_name_list,$photo_name_list2);
                foreach($del_array as $val){
                    $photo = new Photo();
                    $photo::where('filename',$val)->delete();
                }
                foreach($del_array as $val){
                    Storage::cloud()->delete($val);
                }
                DB::commit();
            }catch (\Exception $exception) {
                DB::rollBack();
                throw $exception;
            }
        }
        
       
    }

    /**
     * 予約ツイートをDBから取得する。
     */
    public function getScheduledList(Request $request){
        $twetter_screen_name = $request->twitter_screen_name;
        $account_id = Account::where('twitter_screen_name',$twetter_screen_name)->first()->id;
        $scheduled_tweet = ScheduledTweet::where('account_id',$account_id)->get();
        $scheduled_tweet_list =[];
        foreach($scheduled_tweet as $value){
            $id = $value->id;
            $tweet_text = $value->tweet_text;
            $scheduled_date = $value->scheduled_date;
            $date = new Carbon($scheduled_date);
            $scheduled_tweet_list[] =['id'=>$id,'tweet_text'=>$tweet_text,'scheduled_date'=>$date->format("Y/m/d H:i:s")];
        }
        return $scheduled_tweet_list;
    }

    /**
     * 予約ツイートを削除する
     */
    public function deleteScheduled(Request $request){
        $id = $request->id;
        $scheduled_tweet = ScheduledTweet::where('id',$id)->with(['photos'])->first();
        $scheduled_tweet->delete();
        foreach($scheduled_tweet->photos as $val){
            Storage::cloud()->delete($val->filename);
        }
    }

    public function index(Request $request)
    {
        $scheduled_tweet_id = $request->scheduled_tweet_id;
        $photos = Photo::where('scheduled_tweet_id',$scheduled_tweet_id)->get();
        $list = [];
        foreach($photos as $photo){
           $list [] = $photo;
        }
        return $list;
    }
}
