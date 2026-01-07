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
import { formatCurrency } from '@/lib/utils';
import cartRoutes from '@/routes/cart';
import { Head, router } from '@inertiajs/react';
import { Minus, Plus, ShoppingCart, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface CartItem {
    id: number;
    product_id: number;
    product_name: string;
    product_price: string;
    stock_quantity: number;
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
    const [previousQuantities, setPreviousQuantities] = useState<
        Record<number, number>
    >(
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

    const handleUpdateQuantity = (itemId: number, newQuantity?: number) => {
        const quantity =
            newQuantity !== undefined
                ? newQuantity
                : parseInt(String(quantities[itemId]), 10);

        if (isNaN(quantity) || quantity < 1) {
            console.error('Invalid quantity:', quantity);
            // Reset to previous valid quantity
            setQuantities((prev) => ({
                ...prev,
                [itemId]: previousQuantities[itemId],
            }));
            return;
        }

        // Find the cart item to check stock
        const cartItem = cart.items.find((item) => item.id === itemId);
        if (cartItem && quantity > cartItem.stock_quantity) {
            // Reset to previous valid quantity
            setQuantities((prev) => ({
                ...prev,
                [itemId]: previousQuantities[itemId],
            }));
            alert(`Only ${cartItem.stock_quantity} items available in stock.`);
            return;
        }

        const url = `/cart/${itemId}`;

        router.patch(
            url,
            {
                quantity: quantity,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Update previous quantity on success
                    setPreviousQuantities((prev) => ({
                        ...prev,
                        [itemId]: quantity,
                    }));
                    // Force reload to get fresh data
                    router.reload({ only: ['cart'] });
                },
                onError: (errors) => {
                    console.error('Error updating cart:', errors);
                    // Reset to previous valid quantity on error
                    setQuantities((prev) => ({
                        ...prev,
                        [itemId]: previousQuantities[itemId],
                    }));
                    const errorMessage =
                        errors.quantity ||
                        'Failed to update quantity. Please try again.';
                    alert(errorMessage);
                },
            },
        );
    };

    const handleRemove = (itemId: number) => {
        if (
            confirm('Are you sure you want to remove this item from your cart?')
        ) {
            router.delete(cartRoutes.remove({ cartItem: itemId }).url);
        }
    };

    const handleClearCart = () => {
        if (
            confirm(
                'Are you sure you want to clear your cart? This will remove all items.',
            )
        ) {
            router.delete('/cart', {
                onSuccess: () => {
                    // Reset quantities state
                    setQuantities({});
                    setPreviousQuantities({});
                },
            });
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
                                                        Price:{' '}
                                                        {formatCurrency(
                                                            item.product_price,
                                                        )}
                                                    </p>
                                                    <p className="mt-1 text-lg font-semibold">
                                                        Subtotal:{' '}
                                                        {formatCurrency(
                                                            item.subtotal,
                                                        )}
                                                    </p>
                                                </div>
                                                <div className="flex items-center space-x-2">
                                                    <Button
                                                        variant="outline"
                                                        size="icon"
                                                        onClick={() => {
                                                            const newQuantity =
                                                                quantities[
                                                                    item.id
                                                                ] - 1;
                                                            if (
                                                                newQuantity >= 1
                                                            ) {
                                                                handleQuantityChange(
                                                                    item.id,
                                                                    newQuantity,
                                                                );
                                                                handleUpdateQuantity(
                                                                    item.id,
                                                                    newQuantity,
                                                                );
                                                            }
                                                        }}
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
                                                        onClick={() => {
                                                            const newQuantity =
                                                                quantities[
                                                                    item.id
                                                                ] + 1;
                                                            // Check stock before allowing increase
                                                            if (
                                                                newQuantity >
                                                                item.stock_quantity
                                                            ) {
                                                                alert(
                                                                    `Only ${item.stock_quantity} items available in stock.`,
                                                                );
                                                                return;
                                                            }
                                                            handleQuantityChange(
                                                                item.id,
                                                                newQuantity,
                                                            );
                                                            handleUpdateQuantity(
                                                                item.id,
                                                                newQuantity,
                                                            );
                                                        }}
                                                        disabled={
                                                            quantities[
                                                                item.id
                                                            ] >=
                                                            item.stock_quantity
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
                                                Items (
                                                {cart.items.reduce(
                                                    (sum, item) =>
                                                        sum + item.quantity,
                                                    0,
                                                )}
                                                )
                                            </span>
                                            <span>
                                                {formatCurrency(
                                                    cart.items.reduce(
                                                        (sum, item) =>
                                                            sum +
                                                            parseFloat(
                                                                item.subtotal,
                                                            ),
                                                        0,
                                                    ),
                                                )}
                                            </span>
                                        </div>
                                        <div className="border-t pt-4">
                                            <div className="flex justify-between text-lg font-bold">
                                                <span>Total</span>
                                                <span>
                                                    {formatCurrency(cart.total)}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                                <CardFooter className="flex flex-col space-y-2">
                                    <Button
                                        variant="outline"
                                        className="w-full"
                                        onClick={handleClearCart}
                                    >
                                        Clear Cart
                                    </Button>
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
