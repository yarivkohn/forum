<?php

namespace App;

use App\Providers\ThreadReceivedNewReply;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder;
use Laravel\Scout\Searchable;


class Thread extends Model
{
    use RecordActivity, Searchable;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

//   Here we can cast all of our  returned eloquent data for custom format
//   Usually helps to avoid different results from different MySql versions
//
//    protected $casts = [
//        'locked' => 'boolean'
//    ];

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
        static::created(function($thread){
            $thread->update([
                'slug' => $thread->title,
            ]);
        });
// This is now being replaced with the global param $with
//        static::addGlobalScope('creator', function($builder){
//            $builder->with('creator');
//        });
    }

    public static function search($query = '', $callback = null)
    {
        return new Builder(new static, $query, $callback);
    }


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
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
// option #4
        $this->notifySubscribers($reply);
// option #3
//        event(new ThreadHasNewReply($this, $reply));
//Option #2
//        $this->subscriptions
//            ->where('user_id', '!=', $reply->user_id)
//            ->each
//            ->notify($reply);

// Option #1
//        $this->subscriptions->filter(function ($subscription) use ($reply) {
//            return $subscription->user_id != $reply->user_id;
//        })
//            ->each->notify($reply); // This is a replacement for the following each function.
// Option #1b
//            ->each(function ($subscription) use ($reply) {
//                $subscription->notify($reply);
//            });
        event(new ThreadReceivedNewReply($reply));
        return $reply;
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
// We abandoned this approach since there is an internal lock function on laravel query builder and we
// don't want to take a chance of overriding it
//    public function lock()
//    {
//        $this->update([
//            'locked' => true,
//        ]);
//    }
//
//    public function unlock()
//    {
//        $this->update([
//            'locked' => false,
//        ]);
//    }

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

    public function hasUpdatesFor($user)
    {
        $key = $user->visitedThreadCacheKey($this);
        try {
            return $this->updated_at > cache($key);
        } catch (\Exception $e) {
            return false;
        }
    }

//    public function visits()
//    {
////        return new Visits($this);
//    }
    public function toSearchableArray()
    {
        return $this->toArray() + ['path' => $this->path()];
    }


    /**
     * @param $reply
     */
    private function notifySubscribers($reply): void
    {
        $this->subscriptions
            ->where('user_id', '!=', $reply->user_id)
            ->each
            ->notify($reply);
    }

    /**
     * Replace the default sql load by key
     * e.g: by default we use the id for loading modules,
     * using this function we tell Laravel to load by the return key (here by slug)
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);
        $original = $slug;
        if (static::whereSlug($slug)->exists()) {
            $slug .= "-{$this->id}";
            //            $slug = $this->incrementSlug($slug);
        }
        $this->attributes['slug'] = $slug;
    }

//    /**
//     * @param $slug
//     * @return string
//     */
//    private function incrementSlug($slug)
//    {
//        $original = $slug;
//        $count = 2;
//        while(static::whereSlug($slug)->exists()) {
//            $slug = "{$original}-". $count++;
//        }
//        return $slug;
////        $maxSlug = static::whereTitle($this->title)->latest('id')->value('slug');
//        if(is_numeric($maxSlug[-1])){
//         return preg_replace_callback('/(\d+)$/', function($matches){
//                return ++$matches[1];
//            }, $maxSlug);
//        }
//        return "{$slug}-2";
//    }
    public function markBestReply($reply)
    {
        $this->update(['best_reply_id' => $reply->id]);
    }
}
