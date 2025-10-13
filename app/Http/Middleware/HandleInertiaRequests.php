<?php

namespace App\Http\Middleware;

use App\Models\WarehouseNotification;
use App\Models\MailLog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $user = $request->user();

        $cartSummary = [
            'items' => 0,
        ];

        $warehouseAlerts = [
            'pending' => 0,
        ];

        $mailAlerts = [
            'unread' => 0,
        ];

        if ($user && \Schema::hasTable('carts')) {
            $cart = $user->carts()
                ->where('status', 'active')
                ->withCount('items')
                ->first();

            if ($cart) {
                $cartSummary['items'] = $cart->items_count;
            }
        }

        if (Schema::hasTable('warehouse_notifications')) {
            $warehouseAlerts['pending'] = WarehouseNotification::where('status', 'pending')->count();
        }

        if (Schema::hasTable('mail_logs')) {
            $mailAlerts['unread'] = MailLog::whereNull('read_at')->whereNull('deleted_at')->count();
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'cartSummary' => $cartSummary,
            'warehouseAlerts' => $warehouseAlerts,
            'mailAlerts' => $mailAlerts,
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
