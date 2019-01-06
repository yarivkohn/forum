{{-- Editing the question --}}
<div class="card" v-if="editing" v-cloak>
    <div class="card-header">
        <div class="level">
                <input class="form-control" type="text" value="{{ $thread->title }}">

        </div>
    </div>
    <div class="card-body">
        <div class="form-group">
            <textarea class="form-control" rows="10">{{ $thread->body }}</textarea>
        </div>
    </div>
    <div class="card-footer">
    <div class="level">
        <button class="btn btn-sm btn-primary" @click="update">Update</button>
        <button class="btn btn-sm" @click="editing=false">Cancel</button>
        @can ('update', $thread)
            <form method="POST" action=" {{ $thread->path() }}" class="ml-a">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="submit" class="btn btn-link">Delete Thread</button>
            </form>
        @endcan
    </div>
    </div>
</div>

{{-- Viewing the question --}}
<div class="card" v-else>
    <div class="card-header">
        <div class="level">
            <img src="{{ asset($thread->creator->avatar()) }}" width="25" height="25" class="mr-2"
                 alt="{{ $thread->creator->name }}">
            <span class="flex">
                            <a href="/profile/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a> posted: {{ $thread->title }}
                        </span>
        </div>
    </div>
    <div class="card-body">
        <div class="body">{{ $thread->body }}</div>
    </div>
    <div class="card-footer">
        <button class="btn btn-sm" @click="editing=true">Edit</button>
    </div>
</div>