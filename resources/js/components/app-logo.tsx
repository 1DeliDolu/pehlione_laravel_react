import { Link, type LinkProps } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import { home } from '@/routes';
import AppLogoIcon from './app-logo-icon';

type RouteLike = string | { url: string };

interface AppLogoProps extends Omit<LinkProps, 'href'> {
    href?: RouteLike;
}

const resolveHref = (target: RouteLike) =>
    typeof target === 'string' ? target : target.url;

export default function AppLogo({
    href = home(),
    className,
    prefetch = true,
    ...rest
}: AppLogoProps) {
    const destination = resolveHref(href);

    return (
        <Link
            {...rest}
            href={destination}
            prefetch={prefetch}
            className={cn('flex items-center space-x-2', className)}
        >
            <div className="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
                <AppLogoIcon className="size-5 fill-current text-white dark:text-black" />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate font-semibold leading-tight">
                    PehliONE
                </span>
            </div>
        </Link>
    );
}
