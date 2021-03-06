<reply :attributes="{{ $reply }}" inline-template v-cloak>
    <div id="reply-{{ $reply->id }}" class="card">
        <div class="card-header">
            <div class="level">
                <h5 class="flex">
                    <a href="/profile/{{ $reply->owner->name }}">{{ $reply->owner->name }}</a>
                    said {{ $reply->created_at->diffForHumans() }}...
                </h5>
                @if(Auth::check())
                    <div>
                        <favorite :reply="{{ $reply }}"></favorite>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" v-model="body"></textarea>
                    <button class="btn btn-sm btn-primary" @click="update">Update</button>
                    <button class="btn btn-sm btn-link" @click="editing=false">Cancel</button>
                </div>

            </div>
            <div v-else class="body">@{{ body }}</div>
        </div>

        @can('update', $reply)
            <div class="card-footer level">
                <button class="btn btn-sm btn-info mr-1" @click="editing=true">Edit</button>
                <button class="btn btn-sm btn-danger" @click="destroy">Delete</button>
            </div>
        @endcan
    </div>
</reply>