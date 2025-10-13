import { useState, type ReactNode } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { Mail, Menu, ShoppingCart, X } from 'lucide-react';
import { login, register } from '@/routes';
import cartRoutes from '@/routes/cart';
import { cn } from '@/lib/utils';
import type { SharedData } from '@/types';

type RouteLike = string | { url: string };

interface NavItem {
    label: string;
    href: RouteLike;
    onlyAuth?: boolean;
    onlyGuest?: boolean;
    badge?: string | number;
    icon?: ReactNode;
    hideLabelOnDesktop?: boolean;
}

const primaryLinks: NavItem[] = [
    { label: 'Home', href: '/' },
    { label: 'About', href: '/about' },
    { label: 'Connection', href: '/connection' },
    { label: 'Products', href: '/products' },
];

const accountLinks: NavItem[] = [
    { label: 'Login', href: login(), onlyGuest: true },
    { label: 'Register', href: register(), onlyGuest: true },
    { label: 'Dashboard', href: '/dashboard', onlyAuth: true },
];

const resolveHref = (target: RouteLike) =>
    typeof target === 'string' ? target : target.url;

export function SiteNavbar() {
    const page = usePage<SharedData>();
    const user = page.props.auth.user as SharedData['auth']['user'] | null;
    const isAuthenticated = Boolean(user);
    const rawCart = page.props.cartSummary?.items ?? 0;
    const parsedCart = typeof rawCart === 'number' ? rawCart : parseInt(String(rawCart), 10);
    const cartCount = Number.isFinite(parsedCart) && parsedCart > 0 ? parsedCart : 0;
    const cartBadge = cartCount > 9 ? '9+' : String(cartCount);
    const mailAlertsCount = Number(page.props.mailAlerts?.unread ?? 0);
    const mailBadge = mailAlertsCount > 9 ? '9+' : String(mailAlertsCount);
    const [mobileOpen, setMobileOpen] = useState(false);

    const visibleAccountLinks = accountLinks.filter((item) => {
        if (item.onlyAuth) {
            return isAuthenticated;
        }
        if (item.onlyGuest) {
            return !isAuthenticated;
        }
        return true;
    });

    if (isAuthenticated) {
        visibleAccountLinks.push({
            label: 'Mail',
            href: '/dashboard/mail',
            onlyAuth: true,
            badge: mailAlertsCount > 0 ? mailBadge : undefined,
            icon: <Mail className="h-4 w-4" />,
            hideLabelOnDesktop: true,
        });

        visibleAccountLinks.push({
            label: 'Cart',
            href: cartRoutes.index(),
            onlyAuth: true,
            badge: cartBadge,
            icon: <ShoppingCart className="h-4 w-4" />, 
            hideLabelOnDesktop: true,
        });
    }

    const combinedLinks: NavItem[] = [...primaryLinks, ...visibleAccountLinks];

    const renderLink = (item: NavItem, key: string, showLabel = true) => {
        const href = resolveHref(item.href);
        const isActive =
            href === '/' ? page.url === '/' : page.url.startsWith(href);

        const isIconOnly = Boolean(item.icon) && !showLabel;

        const linkClasses = isIconOnly
            ? cn(
                  'relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-300 text-neutral-700 transition hover:border-amber-500 hover:text-amber-600 dark:border-neutral-700 dark:text-neutral-200',
                  isActive && 'border-amber-500 text-amber-600 dark:border-amber-400',
              )
            : cn(
                  'rounded-full px-4 py-2 text-sm font-medium transition-colors',
                  isActive
                      ? 'bg-neutral-900 text-white shadow-sm dark:bg-neutral-100 dark:text-neutral-900'
                      : 'text-neutral-700 hover:bg-neutral-200/70 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800/60 dark:hover:text-neutral-50',
              );

        const badgeClasses = isIconOnly
            ? 'absolute -top-1 -right-1 inline-flex min-w-[1.1rem] items-center justify-center rounded-full bg-amber-600 px-1 text-[0.65rem] font-semibold leading-none text-white dark:bg-amber-500'
            : 'inline-flex min-w-5 items-center justify-center rounded-full bg-amber-600 px-1.5 text-[0.65rem] font-semibold leading-none text-white dark:bg-amber-500';

        return (
            <Link
                key={key}
                href={href}
                prefetch
                className={linkClasses}
                onClick={() => setMobileOpen(false)}
            >
                {isIconOnly ? (
                    <>
                        {item.icon}
                        {item.badge !== undefined && (
                            <span className={badgeClasses}>{item.badge}</span>
                        )}
                    </>
                ) : (
                    <span className="inline-flex items-center gap-2">
                        {item.icon}
                        {showLabel && item.label && <span>{item.label}</span>}
                        {item.badge !== undefined && (
                            <span className={badgeClasses}>{item.badge}</span>
                        )}
                    </span>
                )}
            </Link>
        );
    };

    return (
        <header className="sticky top-0 z-40 border-b border-neutral-200/70 bg-white/90 backdrop-blur dark:border-neutral-800 dark:bg-neutral-950/90">
            <div className="mx-auto flex h-16 w-full max-w-6xl items-center px-4 sm:px-6 lg:px-8">
                <Link
                    href="/"
                    prefetch
                    className="text-lg font-semibold tracking-tight text-neutral-900 transition-colors hover:text-amber-600 dark:text-neutral-100 dark:hover:text-amber-400"
                >
                    Pehli
                    <span className="text-amber-600 dark:text-amber-400">ONE</span>
                </Link>

                <button
                    type="button"
                    className="ml-auto inline-flex size-9 items-center justify-center rounded-full border border-neutral-300 text-neutral-700 transition hover:bg-neutral-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-500 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800 md:hidden"
                    aria-expanded={mobileOpen}
                    aria-controls="site-mobile-nav"
                    aria-label="Toggle navigation menu"
                    onClick={() => setMobileOpen((open) => !open)}
                >
                    {mobileOpen ? <X className="size-5" /> : <Menu className="size-5" />}
                </button>

                <nav className="ml-auto hidden items-center gap-3 md:flex">
                    {primaryLinks.map((item, index) =>
                        renderLink(item, `primary-${index}`)
                    )}
                    <div className="ml-2 flex items-center gap-2">
                        {visibleAccountLinks.map((item, index) =>
                            renderLink(
                                item,
                                `account-${index}`,
                                !item.hideLabelOnDesktop,
                            )
                        )}
                    </div>
                </nav>
            </div>

            {mobileOpen && (
                <nav
                    id="site-mobile-nav"
                    className="border-t border-neutral-200/70 bg-white px-4 pb-4 pt-3 dark:border-neutral-800 dark:bg-neutral-950 md:hidden"
                >
                    <div className="flex flex-col gap-2">
                        {combinedLinks.map((item, index) =>
                            renderLink(item, `mobile-${index}`, true)
                        )}
                    </div>
                </nav>
            )}
        </header>
    );
}
