import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { UserMenuContent } from '@/components/user-menu-content';
import { useInitials } from '@/hooks/use-initials';
import { home, login, register } from '@/routes';
import cart from '@/routes/cart';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ShoppingCart } from 'lucide-react';
import AppLogo from './app-logo';

export function ShopNavbar() {
    const page = usePage<SharedData>();
    const { auth, cartItemCount } = page.props;
    const getInitials = useInitials();

    return (
        <nav className="border-b border-sidebar-border/80 bg-background">
            <div className="mx-auto flex h-16 items-center justify-between px-4 md:max-w-7xl">
                {/* Logo */}
                <Link
                    href={home()}
                    prefetch
                    className="flex items-center space-x-2"
                >
                    <AppLogo />
                </Link>

                {/* Navigation Items */}
                <div className="flex items-center space-x-4">
                    {auth.user && (
                        <Link href={cart.index().url} prefetch>
                            <Button
                                variant="ghost"
                                size="icon"
                                className="relative"
                            >
                                <ShoppingCart className="h-5 w-5" />
                                {cartItemCount > 0 && (
                                    <span className="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground">
                                        {cartItemCount > 99
                                            ? '99+'
                                            : cartItemCount}
                                    </span>
                                )}
                            </Button>
                        </Link>
                    )}

                    {auth.user ? (
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button
                                    variant="ghost"
                                    className="relative h-10 w-10 rounded-full"
                                >
                                    <Avatar className="h-10 w-10">
                                        <AvatarImage
                                            src={auth.user.avatar_url as string}
                                            alt={auth.user.name}
                                        />
                                        <AvatarFallback>
                                            {getInitials(auth.user.name)}
                                        </AvatarFallback>
                                    </Avatar>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent
                                className="w-56"
                                align="end"
                                forceMount
                            >
                                <UserMenuContent user={auth.user} />
                            </DropdownMenuContent>
                        </DropdownMenu>
                    ) : (
                        <div className="flex items-center space-x-2">
                            <Link href={login()}>
                                <Button variant="ghost">Log in</Button>
                            </Link>
                            <Link href={register()}>
                                <Button>Register</Button>
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </nav>
    );
}
