@extends('layouts.default')
@section('title','home')
@section('content')

    @if (Auth::check())
    <div class="row">
        <div class="col-md-8">
            <section class="status_form">
                @include('shared._status_form')
            </section>
            <h3>微博列表</h3>
            @include('shared._feed')
        </div>
        <aside class="col-md-4">
            <section class="user_info">
                @include('shared._user_info', ['user' => Auth::user()])
            </section>
        </aside>
    </div>
    @else
    <div class="jumbotron">
        <h1>Hello larabel</h1>
        <p class="lead">
            您现在看到的是weibo的示例首页 <a href="https://joewt.com">my blog</a>
        </p>
        <p>一切将从这里开始</p>
        <p><a class="btn btn-lg btn-success" role="button" href="{{ route('signup') }}">现在注册</a></p>
    </div>
    @endif
@stop