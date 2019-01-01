<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/30/18
 * Time: 10:21 AM
 */

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    /**
     * @return array
     */
    public function get()
    {
        return array_map('json_decode', Redis::zrevrange($this->cacheKey(), 0, 4));
    }

    /**
     * @param $thread
     */
    public function push($thread)
    {
        Redis::zincrby($this->cacheKey(), 1, json_encode([
            'title' => $thread->title,
            'path' => $thread->path()
        ]));
    }

    /**
     * @return string
     */
    public function cacheKey()
    {
        return app()->environment('testing')? 'testing_trending_threads' : 'trending_threads';
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
    }
}