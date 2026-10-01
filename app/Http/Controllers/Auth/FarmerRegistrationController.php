<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Province;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\FarmerRegistrationRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FarmerRegistrationController extends Controller
{
    public function create(): View
    {
        return view('auth.farmer-register', [
            'provinces' => Province::options(),
        ]);
    }

    public function store(FarmerRegistrationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // User + profile are created together or not at all.
        $user = DB::transaction(function () use ($data) {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'],
            ]);
            $user->role = UserRole::Farmer;
            $user->save();

            $user->farmerProfile()->create([
                'farm_name' => $data['farm_name'],
                'province' => $data['province'],
                'municipality' => $data['municipality'],
                'town' => $data['town'] ?? null,
                'farm_address' => $data['farm_address'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()
            ->route('farmer.dashboard')
            ->with('status', 'Welcome to Local-Farm-Fresh! Add your first product to start selling.');
    }
}
