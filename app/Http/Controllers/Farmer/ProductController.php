<?php

namespace App\Http\Controllers\Farmer;

use App\Enums\ProductUnit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\StoreProductRequest;
use App\Http\Requests\Farmer\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $products) {}

    public function index(Request $request): View
    {
        $farmer = $request->user()->farmerProfile;
        abort_if($farmer === null, 403);

        $products = Product::ownedBy($farmer)
            ->with('primaryImage', 'category')
            ->withTrashed()
            ->latest()
            ->paginate(20);

        return view('farmer.products.index', compact('farmer', 'products'));
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('farmer.products.create', [
            'categories' => Category::active()->pluck('name', 'id'),
            'units' => ProductUnit::cases(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->authorize('create', Product::class);

        $product = $this->products->create(
            $request->user()->farmerProfile,
            $request->safe()->except('image'),
            $request->file('image'),
        );

        return redirect()
            ->route('farmer.products.edit', $product)
            ->with('status', 'Product added.');
    }

    public function edit(Request $request, Product $product): View
    {
        $this->authorize('update', $product);

        return view('farmer.products.edit', [
            'product' => $product->load('primaryImage'),
            'categories' => Category::active()->pluck('name', 'id'),
            'units' => ProductUnit::cases(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $this->products->update(
            $product,
            $request->safe()->except('image'),
            $request->file('image'),
        );

        return redirect()
            ->route('farmer.products.edit', $product)
            ->with('status', 'Product updated.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $this->products->delete($product);

        return redirect()
            ->route('farmer.products.index')
            ->with('status', 'Product deleted.');
    }
}
