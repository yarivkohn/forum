@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @include('threads._list')
                {{ $threads->render() }}
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Search</div>
                    <div class="card-body">
                        <form action="/threads/search" method="GET">
                            <div class="form-group">
                                <input type="text" name="q" placeholder="Search for something..." class="form-control">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-sm btn-primary" type="submit">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div>
                    @if($trending)
                        <div class="card">
                            <div class="card-header">Trending threads</div>
                            <div class="card-body">
                                <ul class="list-group">
                                    @foreach($trending as $thread)
                                        <li class="list-group-item">
                                            <a href="{{ $thread->path }}">{{ $thread->title }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection