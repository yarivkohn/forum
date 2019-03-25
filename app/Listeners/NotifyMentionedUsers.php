<?php

namespace App\Listeners;

use App\Notifications\YouWereMwntioned;
use App\Providers\ThreadReceivedNewReply;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {

//        collect($event->reply->mentionedUsers()) //Creates a collection form the given value
//            ->map(function($name){
//                return User::where('name', $name)->first(); //Will return either user or null
//            })
//            ->filter() //When calling filter w/o any arguments it will strip all null values from the given collection
        // This call to User::whereIn replaces the above lines with cleaner code and less SQL queries.
        User::whereIn('name', $event->reply->mentionedUsers())->get()
            ->each(function($user) use($event) {
                $user->notify(new YouWereMwntioned($event->reply));
            });

    }
}
