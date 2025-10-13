<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(Request $request): Response
    {
        $cart = $this->activeCart($request->user());

        $cart->load(['items.product']);

        return Inertia::render('cart/index', [
            'cart' => [
                'id' => $cart->id,
                'status' => $cart->status,
                'subtotal' => (float) $cart->subtotal,
                'currency' => $cart->currency,
                'items' => $cart->items->map(function (CartItem $item) {
                    return [
                        'id' => $item->id,
                        'product' => [
                            'id' => $item->product_id,
                            'name' => $item->name,
                            'slug' => $item->product?->slug,
                            'thumbnail' => $item->product?->images[0] ?? '/products/placeholders/placeholder-1.png',
                        ],
                        'sku' => $item->sku,
                        'size' => $item->size !== '' ? $item->size : null,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'total_price' => (float) $item->total_price,
                        'attribute_tags' => $item->attribute_tags ?? [],
                    ];
                }),
            ],
        ]);
    }

    public function store(StoreCartItemRequest $request): RedirectResponse
    {
        $user = $request->user();
        $product = Product::where('slug', $request->validated('product_slug'))
            ->firstOrFail();

        $size = $request->validated('size');
        $normalizedSize = $size ?? '';
        $quantity = (int) $request->validated('quantity');

        $availableSizes = $product->available_sizes ?? [];
        if (! empty($availableSizes) && ! $size) {
            return back()->withErrors([
                'size' => 'Please choose a size before adding to cart.',
            ])->onlyInput('size');
        }

        if (! empty($availableSizes) && ! in_array($size, $availableSizes, true)) {
            return back()->withErrors([
                'size' => 'Please choose a valid size option.',
            ])->onlyInput('size');
        }

        $cart = $this->activeCart($user);

        DB::transaction(function () use ($cart, $product, $size, $normalizedSize, $quantity) {
            $item = $cart->items()->where([
                'product_id' => $product->id,
                'size' => $normalizedSize,
            ])->first();

            if (! $item) {
                $item = $cart->items()->create([
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'size' => $normalizedSize,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $product->price * $quantity,
                    'attribute_tags' => $product->attribute_tags,
                    'metadata' => [
                        'available_sizes' => $product->available_sizes,
                    ],
                ]);
            } else {
                $item->quantity += $quantity;
                $item->total_price = $item->quantity * $item->unit_price;
                $item->save();
            }

            $cart->recalculateSubtotal();
        });

        return redirect()->back()->with('success', 'Product added to cart.');
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $this->authorizeItem($request, $item);

        DB::transaction(function () use ($item, $request) {
            $item->quantity = (int) $request->input('quantity');
            $item->total_price = $item->quantity * $item->unit_price;
            $item->save();

            $item->cart->recalculateSubtotal();
        });

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, CartItem $item): RedirectResponse
    {
        $this->authorizeItem($request, $item);

        DB::transaction(function () use ($item) {
            $cart = $item->cart;
            $item->delete();
            $cart->refresh();
            $cart->recalculateSubtotal();
        });

        return back()->with('success', 'Item removed from cart.');
    }

    private function activeCart($user): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['currency' => 'EUR']
        );
    }

    private function authorizeItem(Request $request, CartItem $item): void
    {
        if ($item->cart->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($item->cart->status !== 'active') {
            abort(400, 'Cart is no longer editable.');
        }
    }
}
