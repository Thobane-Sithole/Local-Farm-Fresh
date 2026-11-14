<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsernameController extends Controller
{
    public function edit(): View
    {
        return view('account.username');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'alpha_dash',
                'unique:users,username,' . auth()->id(),
            ],
        ]);

        auth()->user()->update(['username' => $request->username]);

        $intended = session()->pull('url.intended', route('community.index'));

        return redirect($intended)->with('status', 'Username set! Welcome to the community.');
    }
}
