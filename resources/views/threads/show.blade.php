@extends('layouts.app')

@section('header')
    <link rel="stylesheet" href="/css/vendor/jquery.atwho.css">
@endsection

@section('content')
    <thread-view inline-template :thread="{{ $thread }}">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-1">
                    @include('threads._question')
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
                                <button class="btn btn-defualt"
                                        v-if="authorized('isAdmin')"
                                        @click="toggleLockState"
                                        v-text="locked? 'Unlock' : 'Lock' "></button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection