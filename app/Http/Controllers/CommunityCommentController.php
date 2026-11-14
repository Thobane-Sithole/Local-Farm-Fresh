<?php

namespace App\Http\Controllers;

use App\Models\CommunityComment;
use App\Models\CommunityPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommunityCommentController extends Controller
{
    public function store(Request $request, CommunityPost $post): RedirectResponse
    {
        $request->validate(['body' => ['required', 'string', 'max:1000']]);

        CommunityComment::create([
            'community_post_id' => $post->id,
            'user_id'           => auth()->id(),
            'body'              => $request->body,
        ]);

        return back()->with('status', 'Comment added.');
    }

    public function destroy(CommunityComment $comment): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin() || $comment->user_id === auth()->id(), 403);

        $comment->delete();

        return back()->with('status', 'Comment removed.');
    }
}
