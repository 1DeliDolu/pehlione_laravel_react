import { type ReactNode } from 'react';
import { SiteNavbar } from '@/components/site-navbar';

interface SiteLayoutProps {
    children: ReactNode;
}

export default function SiteLayout({ children }: SiteLayoutProps) {
    return (
        <div className="min-h-screen bg-neutral-50 text-neutral-900 antialiased dark:bg-neutral-900 dark:text-neutral-100">
            <SiteNavbar />
            <main className="mx-auto flex w-full max-w-6xl flex-1 flex-col px-4 pb-16 pt-10 sm:px-6 lg:px-8">
                {children}
            </main>
        </div>
    );
}
