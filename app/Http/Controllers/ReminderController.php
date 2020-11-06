<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\PassResetMail;
use App\Http\Requests\FindEmailRequest;
use App\Http\Requests\PasswordResetRequest;
use App\User;

class ReminderController extends Controller
{
    public function reminder(FindEmailRequest $request)
    {
        $email = $request->email;
        //URL生成 1時間で期限がきれる。
         $url = URL::temporarySignedRoute('password', now()->addSeconds(3600),['reset' => 'reset']);
         Mail::to($request->email)->send(new PassResetMail($url));
         
         //sessionにメールアドレスを入れる
         session()->put(['email' => $email]);

         return response()->json();

    }

    public function reset(PasswordResetRequest $request){
        //パスワードバリデーションチェック
        //sessionからemail取得 sessionを削除
        $email = session()->pull('email', 'default');
        //emailからuserを見つけてパスワードをupdate
        $user = User::where('email',$email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json();
    }


}


