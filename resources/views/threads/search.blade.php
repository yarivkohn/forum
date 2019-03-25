@extends('layouts.app')

@section('content')
    <div class="container">
        <ais-index
                app-id="{{ config('scout.algolia.id') }}"
                api-key="{{ config('scout.algolia.key') }}"
                index-name="threads"
                query="{{ request('q') }}">
            <div class="row">
                <div class="col-md-8">
                    <ais-results>
                        <template scope="{result}">
                            <li>
                                <a :href="result.path" v-text="result.title"></a>
                                <ais-highlight :result="result" attribute-name="title"></ais-highlight>
                            </li>
                        </template>
                    </ais-results>
                </div>

                <div class="col-md-4">
                    <div>
                        <div class="card">
                            <div class="card-header">Search</div>
                            <div class="card-body">
                                <ais-search-box>
                                    <ais-input placeholder="Search for thread..." :autofocus="autofocus" class="form-control"></ais-input>
                                </ais-search-box>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="card">
                            <div class="card-header">Filter</div>
                            <div class="card-body">
                                    <ais-refinement-list attribute-name="channel.name"></ais-refinement-list>
                            </div>
                        </div>
                    </div>

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
        </ais-index>
    </div>
@endsection