<!DOCTYPE html>
<html lang="ja">
<style>
  p {
    white-space:pre-line; 
    word-wrap:break-word;
  }
  .padding{
    padding: 15px;
    box-sizing: border-box;
  }

</style>
<body>
<div class="padding">
  <div>
    <p>パスワードの変更申請を受け付けました。</p>
    <p>下記のURLから、パスワード変更手続きをしてください。</p>
    <a href="{{ $content }}">{{ $content }}</a>
</div>
<div>
    <p>このアドレスの有効期限は一時間です。</p>
</div>
<div>
    <p>このメールに覚えがない場合、ほかの方がメールアドレスを間違えて入力した可能性があります。
        パスワードが変更されることはありません。
    </p>
</div>
<p>
  --------------------------------------------------
  このメールは送信専用です。返信をしないでください。
  URL:<a href="{{ url('/') }}">{{url('/')}}</a>
</p>
</div>

</body>
</html>
