<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\User;
use App\Account;
use App\ActionFlag;

//メールに使う内容
class Message{
    const TITLE = [
        'title1'=> '[KamiTTer]自動機能を停止しました。',
        'title2'=> '[KamiTTer]API制限を受けました。',
        'title3'=> '[KamiTTer]ツイッターアカウントが凍結しました。',
        'title4'=> '[KamiTTer]ご利用のTwitterアカウントがロックされました。',
        'title5'=> '[KamiTTer]サーバーエラーが起きました。'
    ];
    const SUB_TITLE = [
        'sub1' => '自動機能使用中にAPIエラーが起きました。',
        'sub2' => '自動機能を使用中に予期せぬエラーが発生しました。',
        'sub3' => 'ターゲットリストに鍵アカウントもしくは存在しないアカウントがあります。',
        'sub4' => 'ツイッターアカウントが凍結しました。',
        'sub5' => 'ツイッターアカウントがロックしました。',
        'sub6' => 'サーバーエラーが起きました。',
    ];

    const TEXT = [
        'text1' => '制限解除後に自動で再開されるので、必要な措置はありません。',
        'text2' => '予期せぬエラーが発生しました。'."\n".
        '自動機能の設定を確認してください。(ターゲットリストに存在しないアカウントが登録されていないかなど)'
        ."\n".
        '自動機能が停止されているので再開する場合は自動機能の設定から手動で起動してください。'."\n".
        '何度もこのエラーが起こる場合はフォロー、アンフォロー、いいねのいずれかに制限が掛かっている可能性があります。'."\n".
        'その場合はそれらの設定をoffにして試すか数日開けてから再度自動機能をお試しください。',
        'text3' => 'ターゲットリストに鍵アカウントもしくは存在しないアカウントがあり自動機能を停止しました。'."\n".
        '自動機能の設定からそれらのアカウントの登録を解除した後手動で再開してください。',
        'text5' => 'ツイッターアカウントが凍結されたので自動機能を停止しました。'."\n".
        '凍結解除後、自動機能の設定から手動で再開させてください。',
        'text6' => '自動機能中にツイッターアカウントがロックされました。'."\n".
        'ロックを解除後、自動機能の設定から手動で自動機能を再開してください。',
        'text7' => '自動機能が停止しました。'."\n".
        '再開する場合は自動機能の設定から手動で行って下さい。'
    ];
}

//apiエラー、DBエラーのチェックをする。
//エラーがある場合はメールを送信する。
class ErrorHandlingController extends Controller
{
    private $send_mail;
    private $action_flag;
    private $Key_Account = 'Not authorized.';
    private $Resolving_timed_out = 'Resolving timed out';
    private $User_not_found = 50;
    private $Rate_limit_exceeded = 88;
    private $You_have_already_favorited = 139; 
    private $User_has_been_suspended = 63;
    private $account_is_temporarily_locked = 326;

    //エラーが発生したことが判明したときに使う。
    //メール送信とフラグを変更する(エラーフラグをtrueにする)ためにコントローラーを取得。
    //エラーが発生したアカウントのツイッター名を取得。
    public function preparation($account_id){
        $this->send_mail = app()->make('App\Http\Controllers\ApiErrorMailController');
        $this->action_flag = app()->make('App\Http\Controllers\ActionFlagController');
        $twitter_screen_name = Account::where('id',$account_id)->select('twitter_screen_name')->get();
        $twitter = (string)$twitter_screen_name[0]->twitter_screen_name;
        $name = '@'.$twitter;
        return $name;
    }

    //エラーが発生したらfalseを返し、そうでないならtrueを返す。
    public function errorHandling($account_id,$response){
        $false = false;
        $true = true;
        $response = (array)$response;
        if(!empty($response['messages'])){
            Log::debug($response);
            $name = $this->preparation($account_id);
            if(strpos($response['messages'],$this->Resolving_timed_out)!== false){
                $this->action_flag->autoRestart($account_id);
                $title = Message::TITLE["title1"].$name;
                $sub_title = Message::SUB_TITLE['sub1'];
                $text = Message::TEXT['text1'];
                $this->send_mail->mail($account_id,$title,$sub_title,$text);
                return !!$false;
            }else{
                $this->action_flag->stopAction($account_id);
                $title = Message::TITLE["title1"].$name;
                $sub_title = Message::SUB_TITLE["sub2"];
                $text = Message::TEXT['text2'];
                $this->send_mail->mail($account_id,$title,$sub_title,$text);
                return !!$false;
            }
        }
        if(!empty($response['error'])){
            Log::debug($response);
            $name = $this->preparation($account_id);
            $this->action_flag->stopAction($account_id);
            if($response['error']=== $this->Key_Account){
                $title = Message::TITLE["title1"].$name;
                $sub_title = Message::SUB_TITLE["sub3"];
                $text = Message::TEXT['text3'];
                $this->send_mail->mail($account_id,$title,$sub_title,$text);
                return !!$false;
            }else{
                $title = Message::TITLE["title1"].$name;
                $sub_title = Message::SUB_TITLE["sub2"];
                $text = Message::TEXT['text2'];
                $this->send_mail->mail($account_id,$title,$sub_title,$text);
                return !!$false;
            }
        }
        if(!empty($response['errors'])){
            Log::debug($response);
            $name = $this->preparation($account_id);
            if($response['errors'][0]->code === $this->Rate_limit_exceeded){
                $this->action_flag->autoRestart($account_id);
                $title = Message::TITLE["title2"].$name;
                $sub_title = Message::SUB_TITLE["sub1"];
                $text = Message::TEXT['text1'];
                $this->send_mail->mail($account_id,$title,$sub_title,$text);
                return !!$false;
            }else if($response['errors'][0]->code === $this->You_have_already_favorited){
                return !!$false;
            }else if($response['errors'][0]->code === $this->User_has_been_suspended){
                $this->action_flag->stopAction($account_id);
                $title = Message::TITLE["title3"].$name;
                $sub_title = Message::SUB_TITLE["sub4"];
                $text = Message::TEXT['text5'];
                $this->send_mail->mail($account_id,$title,$sub_title,$text);
                return !!$false;
            }else if($response['errors'][0]->code === $this->account_is_temporarily_locked){
                $this->action_flag->stopAction($account_id);
                $title = Message::TITLE["title4"].$name;
                $sub_title = Message::SUB_TITLE["sub5"];
                $text = Message::TEXT['text6'];
                $this->send_mail->mail($account_id,$title,$sub_title,$text);
                return !!$false;
            }else if($response['errors'][0]->code === $this->User_not_found){
                $this->action_flag->stopAction($account_id);
                $title = Message::TITLE["title1"].$name;
                $sub_title = Message::SUB_TITLE["sub3"];
                $text = Message::TEXT['text3'];
                $this->send_mail->mail($account_id,$title,$sub_title,$text);
                return !!$false;
            }else{
                $this->action_flag->stopAction($account_id);
                $title = Message::TITLE["title1"].$name;
                $sub_title = Message::SUB_TITLE["sub2"];
                $text = Message::TEXT['text2'];
                $this->send_mail->mail($account_id,$title,$sub_title,$text);
                return !!$false;
            }
        }
        return !!$true;
    }
    public function dbError($account_id){
        $this->action_flag->stopAction($account_id);
        $title = Message::TITLE["title5"];
        $sub_title = Message::SUB_TITLE["title6"];
        $text =Message::TEXT['text7'];
        $this->send_mail->mail($account_id,$title,$sub_title,$text);
    }
}
