<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApiError extends Mailable
{
    use Queueable, SerializesModels;

    protected $title;
    protected $sub_title;
    protected $text;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($text,$title,$sub_title)
    {
        $this->title = $title;
        $this->sub_title = $sub_title;
        $this->text = $text;
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
        ->view('mail.ApiError') // メール本文のテンプレート
        ->subject($this->title)
        ->with([
            'text' => $this->text,
            'sub_title' => $this->sub_title,
          ]);
    }
}
