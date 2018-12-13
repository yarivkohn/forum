@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-8 offset-1">
                <div class="card">
                    <div class="card-header">
                        <div class="level">
                        <span class="flex">
                            <a href="/profile/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a> posted: {{ $thread->title }}
                        </span>
                            <form method="POST" action=" {{ $thread->path() }}">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-link">Delete Thread</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="body">{{ $thread->body }}</div>
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
                            <a href="#"> {{ $thread->creator->name }}</a>, and currently
                            has {{ $thread->replies_count }} {{ str_plural('comment', $thread->replies_count) }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection