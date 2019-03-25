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
    public static function bootFavoritable()
    {
        static::deleting(function($model){
            $model->favorites->each->delete();
        });
    }

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
// In laravel the following line will produce a SQL query that will delete the relevant favorite
// The problem is that this will not go through the model, hence will not trigger the 'delete' listener
// and the activities will not be deleted.
//        $this->favorites()->where($attributes)->delete();

// The solution goes as follow:
// We will get the collection and will delete the favorites one by one
        $this->favorites()
            ->where($attributes)
            ->get()
            ->each(function($favorite) {
                $favorite->delete();
            });
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