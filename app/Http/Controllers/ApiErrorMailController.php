<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\RateLimitMail;
use App\Mail\SuspendedMail;
use App\Mail\LockedMail;
use App\Mail\CompletedMail;
use App\Mail\ServerErrorMail;
use App\Mail\ApiError;
use App\ActionFlag;
use App\Account;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


//送信先のemailを取得し、メールを送る。
class ApiErrorMailController extends Controller
{

    public function mail($account_id,$title,$sub_title,$text)
    {   
        $action_flag = ActionFlag::where('account_id',$account_id)->first();
        if($action_flag->mail_flag === 1){
            return false;
        }else{
            $account = Account::where('id',$account_id)->select(['user_id','twitter_screen_name'])->get();
            $email = $this->getEmail($account);
            try{
                Mail::to($email)->send(new ApiError($text,$title,$sub_title));
                $action_flag->mail_flag = true;
                $action_flag->save();
                DB::commit();
            }catch(\Exception $e){
                Log::debug($e);
                return false;
            }
        }
    }
    public function getEmail($account){
        $id = $account[0]['user_id'];
        $user = User::where('id',$id)->select('email')->get();
        $email = $user[0]['email'];
        return $email;
    }
}
