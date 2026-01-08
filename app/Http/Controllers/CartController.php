<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the user's cart.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Clear relationship cache to ensure fresh data
        $user->unsetRelation('cart');
        $cart = $user->cart;

        if (! $cart) {
            $cart = Cart::create(['user_id' => $user->id]);
        }

        // Refresh cart items to get latest data
        $cart->unsetRelation('cartItems');
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
                        'stock_quantity' => $item->product->stock_quantity,
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

        return back()->with('success', 'Product added to cart.');
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

        // Clear relationship cache to ensure fresh data on next request
        $cartItem->cart->unsetRelation('cartItems');
        auth()->user()->unsetRelation('cart');

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

    /**
     * Clear all items from the cart.
     */
    public function clear()
    {
        $user = auth()->user();
        
        // Clear relationship cache to ensure fresh data
        $user->unsetRelation('cart');
        $cart = $user->cart;

        if ($cart) {
            $cart->cartItems()->delete();
        }

        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }

    /**
     * Checkout and create order from cart.
     */
    public function checkout()
    {
        $user = auth()->user();
        $cart = $user->cart;

        if (! $cart || $cart->cartItems->isEmpty()) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Your cart is empty.',
            ]);
        }

        $cart->load(['cartItems.product']);

        // Validate stock availability
        foreach ($cart->cartItems as $cartItem) {
            if ($cartItem->product->stock_quantity < $cartItem->quantity) {
                return redirect()->route('cart.index')->withErrors([
                    'stock' => "Insufficient stock for {$cartItem->product->name}. Only {$cartItem->product->stock_quantity} available.",
                ]);
            }
        }

        // Create order and order items, and reduce stock
        DB::transaction(function () use ($cart, $user) {
            $order = Order::create([
                'user_id' => $user->id,
            ]);

            foreach ($cart->cartItems as $cartItem) {
                $order->orderItems()->create([
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);

                // Reduce stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Clear cart
            $cart->cartItems()->delete();
        });

        return redirect()->route('cart.index')->with('success', 'Order placed successfully!');
    }
}
