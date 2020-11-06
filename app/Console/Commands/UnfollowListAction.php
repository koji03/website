<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ActionFlag;
use Illuminate\Support\Facades\Log;

class UnfollowListAction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UnfollowListAction';

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
        $action_start = ActionFlag::where('unfollow_target_flag', 1)->where('error_flag','!=', 1)->orWhereNull('error_flag')->select('account_id')->get();
        $api_controller = app()->make('App\Http\Controllers\TwitterApiController');
        if(!empty($action_start)){
            foreach($action_start as $value){
                $api_controller->createUnfollowList($value);
            }
        }
    }
}
