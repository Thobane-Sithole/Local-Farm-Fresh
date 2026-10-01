<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * /dashboard sends each role to its own home. Breeze's login, register and
 * email-verification flows all redirect here, so they work unchanged.
 */
class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        return redirect()->to($request->user()->homeRoute());
    }
}
