@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-8 offset-1">
                <div class="card">
                    <div class="card-body">
                        <article>
                            <h4><a href="#">{{ $thread->creator->name }}</a> posted: {{ $thread->title }} </h4>
                            <hr>
                            <div class="body">{{ $thread->body }}</div>
                        </article>
                    </div>
                </div>

                @foreach ($replies AS $reply)
                    @include('threads.reply')
                @endforeach

                {{ $replies->links() }}

                @if (auth()->check())

                    <form method="POST" action="{{ $thread->path().'/replies' }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <textarea name="body" class="form-control" placeholder="Have something to say?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-default">Post</button>
                    </form>
                @else
                    <p>Please sign in to participate in this thread</p>
                @endif
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                       <p>
                           This thread was published {{ $thread->created_at->diffForHumans() }} by
                           <a href="#"> {{ $thread->creator->name }}</a>, and currently has {{ $thread->replies_count }} {{ str_plural('comment', $thread->replies_count) }}.
                       </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection