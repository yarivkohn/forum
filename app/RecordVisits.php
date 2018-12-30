<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/30/18
 * Time: 11:15 AM
 */

namespace App;


use Illuminate\Support\Facades\Redis;

trait RecordVisits
{
    public function recordVisits()
    {
        Redis::incr($this->visitCacheKey());
        return $this;
    }

    public function visits()
    {
        return Redis::get($this->visitCacheKey()) ?? 0;
    }

    public function resetVisits()
    {
        Redis::del($this->visitCacheKey());
        return $this;
    }

    /**
     * @return string
     */
    private function visitCacheKey(): string
    {
        return "threads.{$this->id}.visits";
    }

}