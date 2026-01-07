import { ShopNavbar } from '@/components/shop-navbar';
import { type PropsWithChildren } from 'react';

export default function ShopLayout({ children }: PropsWithChildren) {
    return (
        <div className="flex min-h-screen w-full flex-col">
            <ShopNavbar />
            <main className="flex-1">{children}</main>
        </div>
    );
}

