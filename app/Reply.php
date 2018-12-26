<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{

    use Favoritable, RecordActivity;

    protected $guarded = [];

    protected $with = ['owner', 'favorites', ];

    // Evert time this model is cast to array/json it will auto add this attribute
    // The attribute naming convention is get_FEATURE_NAME_Attribute where in our case _FEATURE_NAME_ is FavoritesCount
    // Function getFavoritesCountAttribute is located in the favoritable trait.
    protected $appends = ['favoritesCount', 'isFavorited'];

    protected static function boot()
    {
        parent::boot();
        static::created(function($reply){
            $reply->thread->increment('replies_count');
        });
        static::deleted(function($reply){
            $reply->thread->decrement('replies_count');
        });
    }


    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    public function wasJustPublished()
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

}
