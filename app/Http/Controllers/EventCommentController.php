<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventCommentController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse|JsonResponse
    {
        $request->validate(['body' => ['required', 'string', 'max:500']]);

        $comment = EventComment::create([
            'event_id' => $event->id,
            'user_id'  => auth()->id(),
            'body'     => $request->body,
        ]);

        $comment->load('user');

        if ($request->wantsJson()) {
            return response()->json([
                'comment' => [
                    'id'         => $comment->id,
                    'body'       => $comment->body,
                    'username'   => $comment->user->username ?? $comment->user->name,
                    'delete_url' => route('events.comments.destroy', $comment),
                ],
            ]);
        }

        return back()->with('status', 'Comment added.');
    }

    public function destroy(EventComment $comment): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->isAdmin() || $comment->user_id === auth()->id(), 403);

        $comment->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('status', 'Comment removed.');
    }
}
