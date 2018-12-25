<?php

namespace App;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;


class Thread extends Model
{
    use RecordActivity;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

    protected static function boot()
    {
        parent::boot();
        //This eager loading was replace with a new Db column in the threads table
//        static::addGlobalScope('replyCount', function($builder){
//            $builder->withCount('replies');
//        });

//        With this implementation we are deleting all off the replies as instance w/o triggering any events
//        static::deleting(function($thread){
//            $thread->replies()->delete();
//        });
//      With this implementation we are deleting the replies one by one and therefore triggering events per deletion
        static::deleting(function ($thread) {
            $thread->replies->each(function ($reply) {
                $reply->delete();
            });
        });

// This is now being replaced with the global param $with
//        static::addGlobalScope('creator', function($builder){
//            $builder->with('creator');
//        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    public function replies()
    {
        return $this->hasMany('App\Reply');
    }

//    public function getReplyCountAttribute()
//    {
//        return $this->replies()->count();
//    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);
        //Prepare notifications for all subscribers
        $this->subscriptions->filter(function ($subscription) use ($reply) {
            return $subscription->user_id != $reply->user_id;
        })
//            ->each->notify($reply); // This is a replacement for the following each function.

            ->each(function ($subscription) use ($reply) {
                $subscription->notify($reply);
            });
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function subscribe($userId = null)
    {
        $this->subscriptions()
            ->create([
                'user_id' => $userId ? $userId : auth()->id(),
            ]);
        return $this;
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ? $userId : auth()->id())
            ->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscriptions::class);
    }

    /**
     * Custom Eloquent attribute
     * Whenever calling $thread->isSubscribedTo, this function will fire
     *
     * @return bool
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where(['user_id' => auth()->id()])
            ->exists();
    }
}
