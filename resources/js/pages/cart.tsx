import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import ShopLayout from '@/layouts/shop-layout';
import { Head, router } from '@inertiajs/react';
import { Minus, Plus, ShoppingCart, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface CartItem {
    id: number;
    product_id: number;
    product_name: string;
    product_price: string;
    quantity: number;
    subtotal: string;
}

interface Cart {
    id: number;
    items: CartItem[];
    total: string;
}

interface CartProps {
    cart: Cart;
}

export default function Cart({ cart }: CartProps) {
    const [quantities, setQuantities] = useState<Record<number, number>>(
        cart.items.reduce(
            (acc, item) => {
                acc[item.id] = item.quantity;
                return acc;
            },
            {} as Record<number, number>,
        ),
    );

    const handleQuantityChange = (itemId: number, newQuantity: number) => {
        if (newQuantity < 1) return;
        setQuantities((prev) => ({ ...prev, [itemId]: newQuantity }));
    };

    const handleUpdateQuantity = (itemId: number) => {
        router.patch(cart().update({ cartItem: itemId }).url, {
            quantity: quantities[itemId],
        });
    };

    const handleRemove = (itemId: number) => {
        if (
            confirm('Are you sure you want to remove this item from your cart?')
        ) {
            router.delete(cart().remove({ cartItem: itemId }).url);
        }
    };

    return (
        <ShopLayout>
            <Head title="Shopping Cart" />
            <div className="mx-auto max-w-7xl px-4 py-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold">Shopping Cart</h1>
                    <p className="mt-2 text-muted-foreground">
                        Review and manage your cart items
                    </p>
                </div>

                {cart.items.length === 0 ? (
                    <Card>
                        <CardContent className="py-12 text-center">
                            <ShoppingCart className="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                            <p className="text-lg font-medium">
                                Your cart is empty
                            </p>
                            <p className="mt-2 text-muted-foreground">
                                Add some products to get started!
                            </p>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="grid gap-6 lg:grid-cols-3">
                        <div className="lg:col-span-2">
                            <div className="space-y-4">
                                {cart.items.map((item) => (
                                    <Card key={item.id}>
                                        <CardHeader>
                                            <div className="flex items-start justify-between">
                                                <CardTitle className="text-lg">
                                                    {item.product_name}
                                                </CardTitle>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    onClick={() =>
                                                        handleRemove(item.id)
                                                    }
                                                >
                                                    <Trash2 className="h-4 w-4 text-destructive" />
                                                </Button>
                                            </div>
                                        </CardHeader>
                                        <CardContent>
                                            <div className="flex items-center justify-between">
                                                <div>
                                                    <p className="text-sm text-muted-foreground">
                                                        Price: $
                                                        {parseFloat(
                                                            item.product_price,
                                                        ).toFixed(2)}
                                                    </p>
                                                    <p className="mt-1 text-lg font-semibold">
                                                        Subtotal: $
                                                        {parseFloat(
                                                            item.subtotal,
                                                        ).toFixed(2)}
                                                    </p>
                                                </div>
                                                <div className="flex items-center space-x-2">
                                                    <Button
                                                        variant="outline"
                                                        size="icon"
                                                        onClick={() =>
                                                            handleQuantityChange(
                                                                item.id,
                                                                quantities[
                                                                    item.id
                                                                ] - 1,
                                                            )
                                                        }
                                                        disabled={
                                                            quantities[
                                                                item.id
                                                            ] <= 1
                                                        }
                                                    >
                                                        <Minus className="h-4 w-4" />
                                                    </Button>
                                                    <Input
                                                        type="number"
                                                        min="1"
                                                        value={
                                                            quantities[item.id]
                                                        }
                                                        onChange={(e) =>
                                                            handleQuantityChange(
                                                                item.id,
                                                                parseInt(
                                                                    e.target
                                                                        .value,
                                                                ) || 1,
                                                            )
                                                        }
                                                        onBlur={() =>
                                                            handleUpdateQuantity(
                                                                item.id,
                                                            )
                                                        }
                                                        className="w-20 text-center"
                                                    />
                                                    <Button
                                                        variant="outline"
                                                        size="icon"
                                                        onClick={() =>
                                                            handleQuantityChange(
                                                                item.id,
                                                                quantities[
                                                                    item.id
                                                                ] + 1,
                                                            )
                                                        }
                                                    >
                                                        <Plus className="h-4 w-4" />
                                                    </Button>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                ))}
                            </div>
                        </div>

                        <div className="lg:col-span-1">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Order Summary</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div className="flex justify-between text-sm">
                                            <span className="text-muted-foreground">
                                                Items ({cart.items.length})
                                            </span>
                                            <span>
                                                $
                                                {cart.items
                                                    .reduce(
                                                        (sum, item) =>
                                                            sum +
                                                            parseFloat(
                                                                item.subtotal,
                                                            ),
                                                        0,
                                                    )
                                                    .toFixed(2)}
                                            </span>
                                        </div>
                                        <div className="border-t pt-4">
                                            <div className="flex justify-between text-lg font-bold">
                                                <span>Total</span>
                                                <span>
                                                    $
                                                    {parseFloat(
                                                        cart.total,
                                                    ).toFixed(2)}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                                <CardFooter>
                                    <Button className="w-full" size="lg">
                                        Proceed to Checkout
                                    </Button>
                                </CardFooter>
                            </Card>
                        </div>
                    </div>
                )}
            </div>
        </ShopLayout>
    );
}
