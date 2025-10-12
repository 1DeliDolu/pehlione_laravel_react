import { Icon } from '@/components/icon';
import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { type ComponentPropsWithoutRef } from 'react';

const toHrefString = (href: NavItem['href']) =>
    typeof href === 'string' ? href : href.url;

const isExternalHref = (href: NavItem['href']) =>
    /^(https?:)?\/\//.test(toHrefString(href));

const resolveHref = (href: NavItem['href']) => toHrefString(href);

export function NavFooter({
    items,
    className,
    ...props
}: ComponentPropsWithoutRef<typeof SidebarGroup> & {
    items: NavItem[];
}) {
    return (
        <SidebarGroup
            {...props}
            className={`group-data-[collapsible=icon]:p-0 ${className || ''}`}
        >
            <SidebarGroupContent>
                <SidebarMenu>
                    {items.map((item) => {
                        const hrefValue = resolveHref(item.href);
                        const isExternal = isExternalHref(item.href);
                        const content = (
                            <>
                                {item.icon && (
                                    <Icon iconNode={item.icon} className="h-5 w-5" />
                                )}
                                <span>{item.title}</span>
                            </>
                        );

                        return (
                            <SidebarMenuItem key={item.title}>
                                <SidebarMenuButton
                                    asChild
                                    className="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                                >
                                    {isExternal ? (
                                        <a
                                            href={hrefValue}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="flex items-center gap-2"
                                        >
                                            {content}
                                        </a>
                                    ) : (
                                        <Link
                                            href={hrefValue}
                                            prefetch
                                            className="flex items-center gap-2"
                                        >
                                            {content}
                                        </Link>
                                    )}
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        );
                    })}
                </SidebarMenu>
            </SidebarGroupContent>
        </SidebarGroup>
    );
}
