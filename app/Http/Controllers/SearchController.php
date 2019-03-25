<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Trending;

class SearchController extends Controller
{
    public function show(Trending $trending)
    {
        if(request()->expectsJson()){
            $search = request('q');
            return Thread::search($search)->paginate(25);
        }


        return view('threads.search', [
            'trending' => $trending->get()
        ]); 
    }
}
