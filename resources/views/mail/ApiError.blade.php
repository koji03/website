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
<h1>{{$sub_title}}</h1>

<p>{{$text}}</p>
<p>
  --------------------------------------------------
  このメールは送信専用です。返信をしないでください。
  URL:<a href="{{ url('/') }}">{{url('/')}}</a>
</p>
</div>
</body>
</html>