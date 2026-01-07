import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import ShopLayout from '@/layouts/shop-layout';
import { login } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { ShoppingCart } from 'lucide-react';

interface Product {
    id: number;
    name: string;
    price: string;
    stock_quantity: number;
}

interface HomeProps {
    products: Product[];
}

export default function Home({ products }: HomeProps) {
    const { auth } = usePage<SharedData>().props;

    return (
        <ShopLayout>
            <Head title="Products" />
            <div className="mx-auto max-w-7xl px-4 py-8">
                {products.length === 0 ? (
                    <div className="py-12 text-center">
                        <p className="text-muted-foreground">
                            No products available at the moment.
                        </p>
                    </div>
                ) : (
                    <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        {products.map((product) => (
                            <Card key={product.id} className="flex flex-col">
                                <CardHeader>
                                    <CardTitle className="line-clamp-2">
                                        {product.name}
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="flex-1">
                                    <div className="space-y-2">
                                        <p className="text-2xl font-bold">
                                            $
                                            {parseFloat(product.price).toFixed(
                                                2,
                                            )}
                                        </p>
                                        <p className="text-sm text-muted-foreground">
                                            Stock: {product.stock_quantity}
                                        </p>
                                        {product.stock_quantity === 0 && (
                                            <p className="text-sm font-medium text-destructive">
                                                Out of Stock
                                            </p>
                                        )}
                                    </div>
                                </CardContent>
                                <CardFooter>
                                    {auth.user ? (
                                        <Button
                                            className="w-full"
                                            disabled={
                                                product.stock_quantity === 0
                                            }
                                            onClick={() => {
                                                router.post('/cart/add', {
                                                    product_id: product.id,
                                                    quantity: 1,
                                                });
                                            }}
                                        >
                                            <ShoppingCart className="mr-2 h-4 w-4" />
                                            Add to Cart
                                        </Button>
                                    ) : (
                                        <Link href={login()} className="w-full">
                                            <Button
                                                className="w-full"
                                                variant="outline"
                                            >
                                                Log in to Add to Cart
                                            </Button>
                                        </Link>
                                    )}
                                </CardFooter>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </ShopLayout>
    );
}
