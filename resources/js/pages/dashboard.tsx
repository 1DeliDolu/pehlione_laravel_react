import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { CalendarCheck2, CheckCircle2, PackageCheck } from 'lucide-react';

type ShipmentUpdate = {
    id: number;
    subject: string;
    sentAt: string | null;
    orderId: number | null;
    statusMessage?: string | null;
    packageStatus?: {
        approved?: boolean;
        approvedAt?: string | null;
        dispatched?: boolean;
        dispatchedAt?: string | null;
    };
    trackingNumber?: string | null;
    estimatedDeliveryAt?: string | null;
};

type PageProps = SharedData & {
    shipments: ShipmentUpdate[];
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const formatDate = (value?: string | null) => {
    if (!value) {
        return null;
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return null;
    }

    return date.toLocaleString();
};

export default function Dashboard() {
    const { props } = usePage<PageProps>();
    const shipments = props.shipments ?? [];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 py-8">
                <Card>
                    <CardHeader>
                        <CardTitle>Shipment updates</CardTitle>
                        <CardDescription>
                            Track approval and dispatch progress for your latest orders.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {shipments.length === 0 ? (
                            <div className="rounded-lg border border-dashed border-neutral-200 p-6 text-sm text-muted-foreground dark:border-neutral-800">
                                You don&apos;t have any shipment notifications yet. As soon as a package is approved and handed to the carrier, you&apos;ll see the details here.
                            </div>
                        ) : (
                            shipments.map((shipment) => {
                                const approvedAt = formatDate(shipment.packageStatus?.approvedAt);
                                const dispatchedAt = formatDate(shipment.packageStatus?.dispatchedAt);
                                const sentAt = formatDate(shipment.sentAt);
                                const estimatedDelivery = formatDate(shipment.estimatedDeliveryAt);

                                return (
                                    <div
                                        key={shipment.id}
                                        className="rounded-lg border border-neutral-200 p-4 shadow-sm dark:border-neutral-800"
                                    >
                                        <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div className="space-y-2">
                                                <div className="flex flex-wrap items-center gap-2">
                                                    <span className="text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                                                        {shipment.orderId ? `Order #${shipment.orderId}` : shipment.subject}
                                                    </span>
                                                    {sentAt && (
                                                        <Badge variant="outline" className="text-xs font-normal">
                                                            {sentAt}
                                                        </Badge>
                                                    )}
                                                </div>
                                                <p className="text-sm text-muted-foreground">
                                                    {shipment.statusMessage ?? shipment.subject}
                                                </p>
                                            </div>
                                            {shipment.trackingNumber && (
                                                <Badge variant="secondary" className="text-xs">
                                                    Tracking #{shipment.trackingNumber}
                                                </Badge>
                                            )}
                                        </div>

                                        <div className="mt-4 grid gap-3 sm:grid-cols-2">
                                            <div className="flex items-start gap-2">
                                                <CheckCircle2
                                                    className={`mt-0.5 h-4 w-4 ${
                                                        shipment.packageStatus?.approved
                                                            ? 'text-emerald-500'
                                                            : 'text-muted-foreground'
                                                    }`}
                                                />
                                                <div>
                                                    <div className="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                                        Package approved
                                                    </div>
                                                    <div className="text-xs text-muted-foreground">
                                                        {approvedAt ?? 'Awaiting update'}
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="flex items-start gap-2">
                                                <PackageCheck
                                                    className={`mt-0.5 h-4 w-4 ${
                                                        shipment.packageStatus?.dispatched
                                                            ? 'text-emerald-500'
                                                            : 'text-muted-foreground'
                                                    }`}
                                                />
                                                <div>
                                                    <div className="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                                        Handed to carrier
                                                    </div>
                                                    <div className="text-xs text-muted-foreground">
                                                        {dispatchedAt ?? 'Awaiting update'}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {estimatedDelivery && (
                                            <div className="mt-4 rounded-md border border-amber-200/60 bg-amber-50 px-3 py-2 text-xs text-amber-700 dark:border-amber-500/40 dark:bg-amber-500/10 dark:text-amber-200">
                                                Estimated delivery: {estimatedDelivery}
                                            </div>
                                        )}

                                        {sentAt && (
                                            <div className="mt-4 flex items-center gap-2 text-xs text-muted-foreground">
                                                <CalendarCheck2 className="h-4 w-4" />
                                                <span>Notification sent {sentAt}</span>
                                            </div>
                                        )}
                                    </div>
                                );
                            })
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
