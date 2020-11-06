<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ScheduledTweet;
use App\Account;
use Carbon\Carbon;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ScheduledTweetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:scheduledTweet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {       
        $dt = new Carbon();
        $today = $dt->addMinutes(10);
        $dt2 = new Carbon();
        $yesterday = $dt2->subMinutes(10);
        $data = ScheduledTweet::whereBetween('scheduled_date',[$yesterday,$today])->orderBy('scheduled_date', 'asc')->with(['photos'])->get();
        $flag = false;
        if(isset($data)){
            ScheduledTweet::whereBetween('scheduled_date',[$yesterday,$today])->delete();
            foreach($data as $value){
                $scheduled_date = $value->scheduled_date;
                //現在時刻と比べて過去の場合はすぐにツイート
                if(new Carbon($scheduled_date) >= Carbon::now() && $flag === false){
                    $now = Carbon::now();
                    $now->minute = ceil(($now->minute/10))*10;
                    $now->second = 0;
                    $diff = $now->diffInSeconds(Carbon::now());
                    $flag = true;
                    sleep($diff);
                }
                $text = $value->tweet_text;
                $id = $value->account_id;
                $photos = $value->photos;
                $account = Account::where('id',$id)->first();
                $oauth_token = $account->oauth_token;
                $oauth_token_secret = $account->oauth_token_secret;
                $connection = new TwitterOAuth(
                    config('twitter.consumer_key'),
                    config('twitter.consumer_secret'),
                    $oauth_token,
                    $oauth_token_secret
                );
                $media_list=[];
                if(isset($photos)){
                    foreach($photos as $photo){
                        $s3_file = Storage::cloud()->get($photo->filename); 
                        $s3 = Storage::disk('public'); 
                        $s3->put($photo->filename, $s3_file); 
                        $path = Storage::disk('public')->path($photo->filename);
                        $photo_response = (array)$connection->upload('media/upload', ['media'=>$path]);
                        $s3->delete($photo->filename);
                        $media_list[] = $photo_response['media_id_string'];
                    }
                }
                $medias = implode(",",$media_list);
                $params = [
                    'status' => $text,
                    'trim_user' => true,
                    'media_ids' => $medias
                ];
                $response = (array)$connection->POST('statuses/update', $params);
                if(!empty($response['errors'])){
                    continue;
                }
                if(isset($photos)){
                    foreach($photos as $photo){
                        Storage::cloud()->delete($photo->filename);
                    }
                }
            }
        }
    }
}
