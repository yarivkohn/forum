@extends('layouts.app')
@section('header')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Create new thread</div>
                    <div class="card-body">
                        <form method="POST" action="/threads">
                            {{ csrf_field() }}
                            <div class="from-group">
                                <label for="channel">Choose a channel</label>
                                <select id="channel" name="channel_id" class="form-control">
                                    <option>Choose one</option>
                                    @foreach($channels as $channel)
                                        <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}> {{ $channel->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" id="title" name="title"
                                       value="{{ old('title') }}">
                            </div>
                            <div class="form-group">
                                <label for="body">Body:</label>
                                <textarea id="body" name="body" class="form-control"
                                          rows="8">{{ old('body') }}</textarea>
                            </div>
                            <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="6LdGS4YUAAAAAFhbkPVMeIXznr2ELEz4_xql6PJg"></div>
                            </div>

                            <button type="submit" class="btn btn-primary">Publish</button>
                        </form>
                        @if(count($errors))
                            <ul class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection