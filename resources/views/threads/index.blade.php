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
                    <div class="card-header">Trending threads</div>
                    <div class="card-body">
                        <ol>
                            <li>Polina</li>
                            <li>Ella</li>
                            <li>Yariv</li>
                            <li>Evyatar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection