<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the user's cart.
     */
    public function index()
    {
        $user = auth()->user();
        $cart = $user->cart;

        if (! $cart) {
            $cart = Cart::create(['user_id' => $user->id]);
        }

        $cart->load(['cartItems.product']);

        return Inertia::render('cart', [
            'cart' => [
                'id' => $cart->id,
                'items' => $cart->cartItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'product_price' => $item->product->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                    ];
                }),
                'total' => $cart->total,
            ],
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $user = auth()->user();
        $cart = $user->cart;

        if (! $cart) {
            $cart = Cart::create(['user_id' => $user->id]);
        }

        $product = Product::findOrFail($request->product_id);

        // Check stock availability
        $quantity = $request->quantity ?? 1;
        if ($product->stock_quantity < $quantity) {
            return back()->withErrors([
                'quantity' => 'Insufficient stock available.',
            ]);
        }

        // Check if product already exists in cart
        $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if ($product->stock_quantity < $newQuantity) {
                return back()->withErrors([
                    'quantity' => 'Insufficient stock available.',
                ]);
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cart->cartItems()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Ensure the cart item belongs to the authenticated user's cart
        if ($cartItem->cart->user_id !== auth()->id()) {
            abort(403);
        }

        $product = $cartItem->product;

        // Check stock availability
        if ($product->stock_quantity < $request->quantity) {
            return back()->withErrors([
                'quantity' => 'Insufficient stock available.',
            ]);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(CartItem $cartItem)
    {
        // Ensure the cart item belongs to the authenticated user's cart
        if ($cartItem->cart->user_id !== auth()->id()) {
            abort(403);
        }

        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }
}
