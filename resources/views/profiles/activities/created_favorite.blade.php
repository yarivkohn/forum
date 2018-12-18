@component('profiles.activities.activity');
@slot('heading')
    {{ $profileUser->name }} favorited a <a href="{{ $activity->subject->favorited->path() }}">reply</a>
    {{--<a href="/">{{ $activity->subject->favorited->title }}</a>--}}
@endslot
@slot('body')
    {{ $activity->subject->favorited->body }}
@endslot
@endcomponent