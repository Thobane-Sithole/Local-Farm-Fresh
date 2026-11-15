<?php

namespace App\Http\Controllers;

use App\Models\CommunityComment;
use App\Models\CommunityPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommunityCommentController extends Controller
{
    public function store(Request $request, CommunityPost $post): RedirectResponse|JsonResponse
    {
        $request->validate(['body' => ['required', 'string', 'max:1000']]);

        $comment = CommunityComment::create([
            'community_post_id' => $post->id,
            'user_id'           => auth()->id(),
            'body'              => $request->body,
        ]);

        $comment->load('user');

        if ($request->wantsJson()) {
            return response()->json([
                'comment' => [
                    'id'         => $comment->id,
                    'body'       => $comment->body,
                    'username'   => $comment->user->username ?? $comment->user->name,
                    'delete_url' => route('community.comments.destroy', $comment),
                    'is_own'     => true,
                ],
            ]);
        }

        return back()->with('status', 'Comment added.');
    }

    public function destroy(CommunityComment $comment): RedirectResponse|JsonResponse
    {
        abort_unless(auth()->user()->isAdmin() || $comment->user_id === auth()->id(), 403);

        $comment->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('status', 'Comment removed.');
    }
}
