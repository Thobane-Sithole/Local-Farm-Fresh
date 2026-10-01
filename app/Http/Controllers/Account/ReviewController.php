<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Notifications\NewReviewFarmer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    public function create(Order $order): \Illuminate\View\View
    {
        Gate::authorize('create', [Review::class, $order]);

        return view('account.reviews.create', compact('order'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('create', [Review::class, $order]);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body'   => ['nullable', 'string', 'max:1000'],
        ]);

        $review = Review::create([
            'order_id'          => $order->id,
            'customer_id'       => $request->user()->id,
            'farmer_profile_id' => $order->farmer_profile_id,
            'rating'            => $data['rating'],
            'body'              => $data['body'] ?? null,
        ]);

        $order->farmerProfile->user->notify(new NewReviewFarmer($review));

        return redirect()->route('account.orders.show', $order->order_number)
            ->with('success', 'Thank you for your review!');
    }
}
