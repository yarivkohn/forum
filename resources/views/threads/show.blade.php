@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <article>
                            <h4><a href="#">{{ $thread->creator->name }}</a> posted: {{ $thread->title }} </h4>
                            <hr>
                            <div class="body">{{ $thread->body }}</div>
                        </article>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                @foreach ($thread->replies AS $reply)
                    @include('threads.reply')
                @endforeach
            </div>
        </div>
        <br>
        <div class="row justify-content-center">
            @if (auth()->check())

                <div class="col-md-8">
                    <form method="POST" action="{{ $thread->path().'/replies' }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                                <textarea name="body" class="form-control"
                                          placeholder="Have something to say?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-default">Post</button>
                    </form>
                </div>

            @else

            @endif
        </div>
    </div>
@endsection