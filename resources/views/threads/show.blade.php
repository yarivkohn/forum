@extends('layouts.app')

@section('header')
    <link rel="stylesheet" href="/css/vendor/jquery.atwho.css">
@endsection

@section('content')
    <thread-view inline-template :data-replies-count="{{ $thread->replies_count }}" :data-locked="{{ $thread->locked }}">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-1">
                    <div class="card">
                        <div class="card-header">
                            <div class="level">
                                <img src="{{ asset($thread->creator->avatar()) }}" width="25" height="25" class="mr-2"
                                     alt="{{ $thread->creator->name }}">
                                <span class="flex">
                            <a href="/profile/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a> posted: {{ $thread->title }}
                        </span>
                                @can ('update', $thread)
                                    <form method="POST" action=" {{ $thread->path() }}">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-link">Delete Thread</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="body">{{ $thread->body }}</div>
                        </div>
                    </div>
                    <replies @removed="--repliesCount" @added="++repliesCount"></replies>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <p>
                                This thread was published {{ $thread->created_at->diffForHumans() }} by
                                <a href="#"> {{ $thread->creator->name }}</a>, and currently
                                has <span
                                        v-text="repliesCount"></span> {{ str_plural('comment', $thread->replies_count) }}
                                .
                            </p>
                            <p>
                                <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}" v-if="signedIn"></subscribe-button>
                                <button class="btn btn-defualt" v-if="authorized('isAdmin') && !locked" @click="lockMe">Lock</button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection