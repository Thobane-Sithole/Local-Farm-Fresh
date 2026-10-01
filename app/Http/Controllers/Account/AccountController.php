<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $recentOrders = $user->orders()
            ->with('farmerProfile')
            ->latest('placed_at')
            ->limit(3)
            ->get();

        return view('account.index', compact('user', 'recentOrders'));
    }
}
