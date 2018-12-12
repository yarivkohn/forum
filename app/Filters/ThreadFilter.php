<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/6/18
 * Time: 3:23 PM
 */

namespace App\Filters;

class ThreadFilter extends AbstractFilters
{
    protected $filters = ['by', 'popular'];

    /**
     * Filter Threads by username
     *
     * @param $username
     * @return mixed
     */
    protected function by($username)
    {
        $user = \App\User::where('name', $username)->firstOrFail();
        return $this->builder->where('user_id', $user->id);
    }

    /**
     * Filter query results according to most popular threads
     * @return mixed
     */
    protected function popular()
    {
        $this->builder->getQuery()->orders = [];
        return $this->builder->orderBy('replies_count', 'desc');
    }
}