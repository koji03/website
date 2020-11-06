<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PassResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $content;
    public function __construct($content)
    {
        //
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->from('kuotan19@gmail.com','KamiTTer') // 送信元
        ->subject('[KamiTTer]パスワード変更申請を受け付けました') // メールタイトル
        ->view('mail.PassResetMail') // メール本文のテンプレート
        ->with(['content' => $this->content]);  // withでセットしたデータをviewへ渡す
  }
}
