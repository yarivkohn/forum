@extends('layouts.app')
@section('content')
    <div class="container">
        <ais-index
                app-id="{{ config('scout.algolia.id') }}"
                api-key="{{ config('scout.algolia.key') }}"
                index-name="threads">
            <ais-search-box></ais-search-box>
            <ais-results>
                <template slot-scope="{ result }">
                    <p>
                        <a href="#">
                            <ais-highlight :result="result" attribute-name="title"></ais-highlight>
                        </a>
                    </p>
                </template>
            </ais-results>
        </ais-index>
    </div>
@endsection