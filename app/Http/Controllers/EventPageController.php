<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class EventPageController extends Controller
{
    public function show(Event $event): View
    {
        $comments = $event->comments()->with('user')->oldest()->get();

        return view('events.show', compact('event', 'comments'));
    }
}
