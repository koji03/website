<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ActionFlag;
use App\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class AutoRestart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AutoRestart';

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

        //オートリスタートフラグがtrueのアカウントを取得して各フラグをfalseにする。
        //リミットに現在時刻を入れる。 この時刻から１５分経過すればそれぞれのアクションができるようになる。twitterapicontrollerで判定している。
        $action_flags = ActionFlag::where('auto_restart_flag',1)->get();
        foreach($action_flags as $action_flag){
            DB::beginTransaction();
            try{
                $action_flag->fill([
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
                $account_id = $action_flag->account_id;
                $account = Account::where('id',$account_id)->first();
                $account->fill([
                    'follow_limit'=>$follow_limit,
                    'unfollow_limit'=>$unfollow_limit,
                    'like_limit'=>$like_limit,
                    'follow_target_limit'=>$follow_target_limit,
                    'unfollow_target_limit'=>$unfollow_target_limit
                    ]);
                $account->save();
                DB::commit();
            }catch(\Exception $e){
                Log::debug($e);
                DB::rollBack();
                return false;
            }
        }
    }
}
