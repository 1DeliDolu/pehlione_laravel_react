import { Head } from '@inertiajs/react';
import SiteLayout from '@/layouts/site-layout';

const contactChannels: { label: string; email: string; phone: string; description: string }[] = [
    { label: 'Service', email: 'service@pehlione.com', phone: '+49 30 1234 5670', description: 'Customer support and order inquiries.' },
    { label: 'Kunden', email: 'kunden@pehlione.com', phone: '+49 30 1234 5671', description: 'German-speaking customer care.' },
    { label: 'Marketing', email: 'marketing@pehlione.com', phone: '+49 30 1234 5672', description: 'Campaign requests and collaborations.' },
    { label: 'Lager', email: 'lager@pehlione.com', phone: '+49 30 1234 5673', description: 'Warehouse coordination and stock alerts.' },
    { label: 'Vertrieb', email: 'vertrieb@pehlione.com', phone: '+49 30 1234 5674', description: 'Wholesale and partner opportunities.' },
    { label: 'Admin', email: 'admin@pehlione.com', phone: '+49 30 1234 5679', description: 'Escalations and platform administration.' },
];

export default function Connection() {
    return (
        <SiteLayout>
            <Head title="Connection" />
            <section className="space-y-8">
                <div className="space-y-4">
                    <h1 className="text-3xl font-semibold tracking-tight">Connect with Our Team</h1>
                    <p className="max-w-3xl text-neutral-600 dark:text-neutral-300">
                        Reach the right department faster. Use the contacts below or the in-app messaging channel once authentication is in place. Every request is routed through Laravel notifications so the correct role receives updates in real time.
                    </p>
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                    {contactChannels.map((channel) => (
                        <div
                            key={channel.email}
                            className="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition hover:border-amber-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-amber-400"
                        >
                            <p className="text-sm font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">
                                {channel.label}
                            </p>
                            <p className="mt-1 text-lg font-medium text-neutral-900 dark:text-neutral-100">
                                {channel.email}
                            </p>
                            <p className="mt-1 text-lg font-medium text-neutral-900 dark:text-neutral-100">
                                {channel.phone}
                            </p>
                            <p className="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
                                {channel.description}
                            </p>
                        </div>
                    ))}
                </div>
            </section>
        </SiteLayout>
    );
}
