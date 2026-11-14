<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventCommentController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $request->validate(['body' => ['required', 'string', 'max:500']]);

        EventComment::create([
            'event_id' => $event->id,
            'user_id'  => auth()->id(),
            'body'     => $request->body,
        ]);

        return back()->with('status', 'Comment added.');
    }

    public function destroy(EventComment $comment): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin() || $comment->user_id === auth()->id(), 403);

        $comment->delete();

        return back()->with('status', 'Comment removed.');
    }
}
