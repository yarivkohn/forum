@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="page-header">
                    <h1>
                        {{ $profileUser->name }}
                        <small>(Member since: {{ $profileUser->created_at->diffForHumans() }})</small>
                    </h1>
                    @can('update', $profileUser)
                        <form method="POST" action="{{ route('avatar', $profileUser) }}" enctype="multipart/form-data">
                            <input type="file" name="avatar">
                            <button type="submit" class="btn btn-primary">Add Avatar</button>
                            {{ csrf_field() }}
                        </form>
                    @endcan
                    <img src="/storage/{{ $profileUser->avatar_path }}" width="50" height="50" alt="{{ $profileUser->name }}">
                </div>

                @forelse($activities as  $date => $activityGroup)
                    <h3> {{ $date  }} </h3>
                    @foreach($activityGroup as $activity)
                        @if(view()->exists("profiles.activities.$activity->type"))
                            @include ("profiles.activities.$activity->type")
                        @endif
                    @endforeach
                @empty
                    <p>There are currently no activities for this user</p>
                @endforelse
                {{--{{ $threads->links() }}--}}
            </div>
        </div>
    </div>


@endsection
