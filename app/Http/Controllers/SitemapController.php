<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\Event;
use App\Models\FarmerProfile;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $products = Product::whereNull('removed_at')->get(['slug', 'updated_at']);
        $farmers  = FarmerProfile::where('is_verified', true)->get(['slug', 'updated_at']);
        $events   = Event::where('starts_at', '>=', now()->subMonths(6))->get(['id', 'updated_at']);
        $posts    = CommunityPost::latest()->limit(200)->get(['id', 'updated_at']);

        $xml = view('sitemap', compact('products', 'farmers', 'events', 'posts'));

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
