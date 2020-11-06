<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>
        <meta name="description" content="{{ '誰でも簡単にツイッターの自動フォロー、自動いいね、自動アンフォローができます。フォロー数を増やしたい場合や好きな話題にいいねをしたいときに便利です。' }}">
        <meta name="keywords" content="ツイッター,自動フォロー,自動アンフォロー,自動いいね,簡単,手軽,">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Lato:400,700|Noto+Sans+JP:400,700" rel="stylesheet">
        <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@1,500&display=swap" rel="stylesheet">
        <!-- css -->
        @if(app('env')=='local')
            <link href="{{ asset('css/app.css') }}" rel="stylesheet">
            <script src="{{ mix('js/app.js') }}" defer></script>    
        @else
            <link href="{{ asset('css/app.css') }}" rel="stylesheet">
            <script src="{{ mix('js/app.js') }}" defer></script> 
        @endif
    </head>
    <body>
        <div id="app"></div>
    </body>
    
</html>
