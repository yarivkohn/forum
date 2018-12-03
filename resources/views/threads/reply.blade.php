<div class="card">
    <div class="card-header">{{ $reply->owner->name }} said {{ $reply->created_at->diffForHumans() }}...</div>
    <div class="card-body">
        <article>
            <div class="body">{{ $reply->body }}</div>
        </article>
    </div>
</div>