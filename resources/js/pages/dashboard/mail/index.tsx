import { Head, Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { dashboard } from '@/routes';
import type { BreadcrumbItem, PaginatedResponse, SharedData } from '@/types';

interface MailLogItem {
    id: number;
    direction: string;
    status: string;
    subject: string;
    to_email: string;
    to_name?: string | null;
    sent_at?: string | null;
    read_at?: string | null;
    deleted_at?: string | null;
    context?: Record<string, unknown> | null;
}

interface PageProps extends SharedData {
    logs: PaginatedResponse<MailLogItem>;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Mail centre',
        href: '/dashboard/mail',
    },
];

export default function MailIndex() {
    const { props } = usePage<PageProps>();
    const { logs } = props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Mail centre" />
            <div className="flex w-full flex-col gap-6 px-4 py-8">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-semibold tracking-tight">Mail centre</h1>
                        <p className="text-sm text-neutral-600 dark:text-neutral-300">
                            Review recent notifications sent by the system. Use the actions to mark messages as read or remove them from the list.
                        </p>
                    </div>
                    <Badge variant="secondary" className="text-xs uppercase tracking-[0.2em]">
                        Total {logs.total}
                    </Badge>
                </div>

                <div className="space-y-4">
                    {logs.data.map((log) => (
                        <Card key={log.id} className="border-neutral-200 dark:border-neutral-800">
                            <CardHeader className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <CardTitle className="text-base font-semibold text-neutral-900 dark:text-neutral-100">
                                        {log.subject}
                                    </CardTitle>
                                    <p className="text-sm text-neutral-600 dark:text-neutral-300">
                                        To: {log.to_name ? `${log.to_name} <${log.to_email}>` : log.to_email}
                                    </p>
                                </div>
                                <div className="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-400">
                                    <span>{log.direction}</span>
                                    <span>&middot;</span>
                                    <span className={log.read_at ? '' : 'text-amber-600 dark:text-amber-400'}>
                                        {log.read_at ? 'Read' : 'Unread'}
                                    </span>
                                    {log.deleted_at && (
                                        <Badge variant="destructive" className="uppercase">
                                            Deleted
                                        </Badge>
                                    )}
                                </div>
                            </CardHeader>
                            <CardContent className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div className="space-y-1 text-sm text-neutral-600 dark:text-neutral-300">
                                    <p>
                                        Sent: {log.sent_at ? new Date(log.sent_at).toLocaleString() : 'Pending'}
                                    </p>
                                    <p>Status: {log.status}</p>
                                    {log.context?.order_id && (
                                        <p>
                                            Order ID: <Link href={`/checkout/confirmation/${log.context.order_id}`} className="text-amber-600 hover:underline dark:text-amber-400">#{log.context.order_id}</Link>
                                        </p>
                                    )}
                                </div>
                                <div className="flex flex-wrap gap-2">
                                    {!log.read_at && !log.deleted_at && (
                                        <Button
                                            asChild
                                            variant="outline"
                                            size="sm"
                                            className="border-neutral-300 text-neutral-700 hover:border-amber-500 hover:text-amber-600 dark:border-neutral-700 dark:text-neutral-200"
                                        >
                                            <Link href={`/dashboard/mail/${log.id}/read`} method="post" as="button" preserveScroll>
                                                Mark as read
                                            </Link>
                                        </Button>
                                    )}
                                    {!log.deleted_at && (
                                        <Button asChild variant="ghost" size="sm" className="text-red-600 hover:text-white hover:bg-red-500">
                                            <Link href={`/dashboard/mail/${log.id}`} method="delete" as="button" preserveScroll>
                                                Remove
                                            </Link>
                                        </Button>
                                    )}
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                <div className="mt-6 flex items-center justify-between text-sm text-neutral-600 dark:text-neutral-300">
                    <span>
                        Showing {logs.from ?? 0}–{logs.to ?? 0} of {logs.total}
                    </span>
                    <div className="flex gap-2">
                        {logs.prev_page_url && (
                            <Link href={logs.prev_page_url} className="rounded border border-neutral-200 px-3 py-1 hover:bg-neutral-100 dark:border-neutral-700 dark:hover:bg-neutral-800">
                                Previous
                            </Link>
                        )}
                        {logs.next_page_url && (
                            <Link href={logs.next_page_url} className="rounded border border-neutral-200 px-3 py-1 hover:bg-neutral-100 dark:border-neutral-700 dark:hover:bg-neutral-800">
                                Next
                            </Link>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
