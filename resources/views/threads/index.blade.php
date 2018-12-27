@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @include('threads._list')
            </div>
        </div>
        <div class="row justify-content-center">
            {{ $threads->render() }}
        </div>
    </div>
@endsection