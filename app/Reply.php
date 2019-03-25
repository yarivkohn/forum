<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Purify;


class Reply extends Model
{

    use Favoritable, RecordActivity;

    protected $guarded = [];

    protected $with = ['owner', 'favorites', ];

    // Evert time this model is cast to array/json it will auto add this attribute
    // The attribute naming convention is get_FEATURE_NAME_Attribute where in our case _FEATURE_NAME_ is FavoritesCount
    // Function getFavoritesCountAttribute is located in the favoritable trait.
    protected $appends = ['favoritesCount', 'isFavorited', 'isBest'];

    protected static function boot()
    {
        parent::boot();
        static::created(function($reply){
            $reply->thread->increment('replies_count');
        });
        static::deleted(function($reply){
            $reply->thread->decrement('replies_count');
//            if ($reply->isBest()){
//                $reply->thread->update([
//                    'best_reply_id' => null,
//                ]);
//            }
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

    public function mentionedUsers()
    {
        // Inspect the reply body for mentioned users
        // And the FOREACH mentioned user - notify them.
        preg_match_all( '/\@([\w\-]+)/', $this->body, $matches);
        return $matches[1];
    }

    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace('/\@([\w\-]+)/', '<a href="/profile/$1">$0</a>', $body);
    }

    /**
     * Return true if this reply was marked as the best one (for a given thread)
     * @return bool
     */
    public function isBest()
    {
        return ($this->thread->best_reply_id == $this->id);
    }

    public function getIsBestAttribute()
    {
        return $this->isBest();
    }

    public function getBodyAttribute($body)
    {
        return Purify::clean($body);
    }
}
