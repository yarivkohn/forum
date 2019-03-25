<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/30/18
 * Time: 4:20 PM
 */

namespace App;


use Illuminate\Support\Facades\Redis;

class Visits
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function record()
    {
        Redis::incr($this->cacheKey());
        return $this;
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
        return $this;
    }

    public function count()
    {
        return Redis::get($this->cacheKey()) ?? 0;
    }

    private function cacheKey()
    {
        return "threads.{$this->model->id}.visits";
    }
}