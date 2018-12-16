<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 12/13/18
 * Time: 10:07 PM
 */

namespace App;


use ReflectionClass;

trait RecordActivity
{

    /**
     * This is a Magical function.
     * The naming convention is boot followed by Trait name.
     * The function will auto-run every time the trait loads.
     */
    public static function bootRecordActivity()
    {
        if(auth()->guest()){
            return;
        }
        foreach(static::getRecordEvents() as $event) {
            static::$event(function($model){
                $model->recordActivity($model);
            });
        }

    }

    /**
     * If we need to listen to different/other/more events (i.e deleted)
     * it is possible to override this function in the relevant model.
     *
     * @return array
     */
    public static function getRecordEvents()
    {
        return ['created'];
    }

    protected function recordActivity($event)
    {
        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
        ]);

//        Activity::create([
//            'user_id' => auth()->id(),
//            'type' => $this->getActivityType($event),
//            'subject_id' => $this->id,
//            'subject_type' => get_class($this),
//        ]);
    }

    public function activity()
    {
        return $this->morphMany('App\Activity', 'subject');
    }

    /**
     * @param $event
     * @return string
     * @throws \ReflectionException
     */
    protected function getActivityType($event): string
    {
        $type = strtolower((new ReflectionClass($event))->getShortName());
        return "created_{$type}";
    }

}