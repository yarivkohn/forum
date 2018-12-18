<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/12/18
 * Time: 5:25 PM
 */

namespace App;


trait Favoritable
{
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];
        if(!$this->favorites()->where($attributes)->exists()) {
            $this->favorites()->create($attributes); // Since this is a polymorphic relationship aloquent will auto complete the favorited id and type
        }
    }

    public function unfavorite()
    {
        $attributes = ['user_id' => auth()->id()];
        $this->favorites()->where($attributes)->delete();
    }

    public function isFavorited()
    {
        return ($this->favorites->where('user_id', auth()->id())->count()) > 0;
    }

    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

}