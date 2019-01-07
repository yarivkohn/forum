{{-- Editing the question --}}
<div class="card" v-if="editing" v-cloak>
    <div class="card-header">
        <div class="level">
            <input class="form-control" type="text" v-model="form.title">

        </div>
    </div>
    <div class="card-body">
        <div class="form-group">
            <wysiwyg v-model="form.body" :value="form.body" name="body"></wysiwyg>
            {{--<textarea class="form-control" rows="10" v-model="form.body"></textarea>--}}
        </div>
    </div>
    <div class="card-footer">
        <div class="level">
            <button class="btn btn-sm btn-primary mr-1" @click="update">Update</button>
            <button class="btn btn-sm" @click="resetForm">Cancel</button>
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
            <span class="flex" v-text="title">
                <a href="/profile/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a> posted:
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="body" v-html="body"></div>
    </div>
    <div class="card-footer" v-if="authorized('owns', thread)">
        <button class="btn btn-sm" @click="editing=true">Edit</button>
    </div>
</div>