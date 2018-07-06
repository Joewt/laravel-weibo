@extends('layouts.default')
@section('title','home')
@section('content')
    <div class="jumbotron">
        <h1>Hello larabel</h1>
        <p class="lead">
            您现在看到的是weibo的示例首页 <a href="https://joewt.com">my blog</a>
        </p>
        <p>一切将从这里开始</p>
        <p><a class="btn btn-lg btn-success" role="button" href="{{ route('signup') }}">现在注册</a></p>
    </div>
@stop