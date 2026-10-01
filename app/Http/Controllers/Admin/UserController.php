<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('farmerProfile')->latest();

        if ($request->filled('role') && $role = UserRole::tryFrom($request->role)) {
            $query->where('role', $role);
        }

        if ($request->filled('q')) {
            $search = '%'.$request->q.'%';
            $query->where(fn ($q) => $q->where('name', 'like', $search)->orWhere('email', 'like', $search));
        }

        $users = $query->paginate(25)->withQueryString();
        $roles  = UserRole::cases();
        $counts = [
            'all'      => User::count(),
            'customer' => User::where('role', UserRole::Customer)->count(),
            'farmer'   => User::where('role', UserRole::Farmer)->count(),
            'admin'    => User::where('role', UserRole::Admin)->count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'counts'));
    }

    public function edit(User $user): View
    {
        $user->load('farmerProfile');

        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($request->filled('role') && $user->id !== auth()->id()) {
            $user->forceFill(['role' => UserRole::from($request->role)])->save();
        }

        return redirect()->route('admin.users.edit', $user)->with('status', 'User updated.');
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $user->suspend($request->input('reason'));

        return back()->with('status', "{$user->name} has been suspended.");
    }

    public function reinstate(User $user): RedirectResponse
    {
        $user->reinstate();

        return back()->with('status', "{$user->name} has been reinstated.");
    }
}
