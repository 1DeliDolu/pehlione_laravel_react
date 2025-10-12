import { Head } from '@inertiajs/react';
import SiteLayout from '@/layouts/site-layout';

export default function About() {
    return (
        <SiteLayout>
            <Head title="About" />
            <section className="space-y-6">
                <h1 className="text-3xl font-semibold tracking-tight">About PehliONE</h1>
                <p className="max-w-3xl text-neutral-600 dark:text-neutral-300">
                    PehliONE is designed as a full-stack commerce accelerator for growing brands. With Laravel 12 powering services and Fortify handling authentication, our application gives teams reliable tooling for catalogue management, fulfilment, and customer engagement.
                </p>
                <div className="grid gap-6 md:grid-cols-3">
                    <div className="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <h2 className="text-lg font-semibold">Admin Operations</h2>
                        <p className="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
                            Control products, categories, and messaging pipelines from a unified dashboard with queued workers for heavy tasks.
                        </p>
                    </div>
                    <div className="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <h2 className="text-lg font-semibold">Employee Collaboration</h2>
                        <p className="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
                            Dedicated access surfaces order insights, assigned conversations, and quick tools for customer support teams.
                        </p>
                    </div>
                    <div className="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <h2 className="text-lg font-semibold">Customer Experience</h2>
                        <p className="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
                            Inertia + React deliver responsive browsing, while tailored notifications keep customers updated from cart to delivery.
                        </p>
                    </div>
                </div>
            </section>
        </SiteLayout>
    );
}
