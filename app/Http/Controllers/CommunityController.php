<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function index(): View
    {
        $posts = CommunityPost::with('user')
            ->withCount('comments')
            ->latest()
            ->paginate(20);

        return view('community.index', compact('posts'));
    }

    public function show(CommunityPost $post): View
    {
        $post->load('user');
        $comments = $post->comments()->with('user')->oldest()->get();

        return view('community.show', compact('post', 'comments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['body' => ['required', 'string', 'max:2000']]);

        CommunityPost::create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        return back()->with('status', 'Post shared with the community!');
    }

    public function destroy(CommunityPost $post): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin() || $post->user_id === auth()->id(), 403);

        $post->delete();

        return back()->with('status', 'Post removed.');
    }
}
