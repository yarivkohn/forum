<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path', 'confirmation_token'
    ];

    protected $casts = [
        'confirmed' => 'boolean'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email'
    ];

    /**
     * Make eloquent use name for Type hinting and
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'name';
    }

    public function threads()
    {
        return $this->hasMany('App\Thread')->latest();
    }

    public function activity()
    {
        return $this->hasMany('App\Activity');
    }

    public function visitedThreadCacheKey($thread)
    {
       return sprintf('users.%s.visits.%s', $this->id, $thread->id);
    }

    public function read($thread)
    {
        // Simulate that the user visited the thread
        cache()->forever($this->visitedThreadCacheKey($thread), Carbon::now());
    }

    public function lastReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }

    public function avatar()
    {
        return ($this->avatar_path)?: 'images/default.png';
    }

    public function confirm()
    {
        $this->confirmed = true;
        $this->save();
    }
}
