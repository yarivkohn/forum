@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <tasks-list :data-project="{{ $project }}"></tasks-list>
        </div>
    </div>
@endsection
