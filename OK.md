# Symfony\Component\Mailer\Exception\UnexpectedResponseException - Internal Server Error
Expected response code "354" but got code "550", with message "550 5.7.0 Too many emails per second. Please upgrade your plan https://mailtrap.io/billing/plans/testing".

PHP 8.4.13
Laravel 12.33.0
pehlione_laravel_react.test

## Stack Trace

0 - vendor\symfony\mailer\Transport\Smtp\SmtpTransport.php:342
1 - vendor\symfony\mailer\Transport\Smtp\SmtpTransport.php:198
2 - vendor\symfony\mailer\Transport\Smtp\EsmtpTransport.php:150
3 - vendor\symfony\mailer\Transport\Smtp\SmtpTransport.php:220
4 - vendor\symfony\mailer\Transport\AbstractTransport.php:69
5 - vendor\symfony\mailer\Transport\Smtp\SmtpTransport.php:138
6 - vendor\laravel\framework\src\Illuminate\Mail\Mailer.php:584
7 - vendor\laravel\framework\src\Illuminate\Mail\Mailer.php:331
8 - vendor\laravel\framework\src\Illuminate\Mail\Mailable.php:207
9 - vendor\laravel\framework\src\Illuminate\Support\Traits\Localizable.php:19
10 - vendor\laravel\framework\src\Illuminate\Mail\Mailable.php:200
11 - vendor\laravel\framework\src\Illuminate\Mail\Mailer.php:353
12 - vendor\laravel\framework\src\Illuminate\Mail\Mailer.php:300
13 - vendor\laravel\framework\src\Illuminate\Mail\PendingMail.php:123
14 - app\Actions\Checkout\FinalizeCheckout.php:167
15 - vendor\laravel\framework\src\Illuminate\Database\DatabaseTransactionsManager.php:211
16 - vendor\laravel\framework\src\Illuminate\Database\Concerns\ManagesTransactions.php:351
17 - vendor\laravel\framework\src\Illuminate\Database\DatabaseManager.php:489
18 - vendor\laravel\framework\src\Illuminate\Support\Facades\Facade.php:363
19 - app\Actions\Checkout\FinalizeCheckout.php:147
20 - app\Http\Controllers\CheckoutController.php:149
21 - vendor\laravel\framework\src\Illuminate\Routing\ControllerDispatcher.php:46
22 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:265
23 - vendor\laravel\framework\src\Illuminate\Routing\Route.php:211
24 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:822
25 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
26 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\EnsureEmailIsVerified.php:41
27 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
28 - vendor\laravel\framework\src\Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets.php:32
29 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
30 - vendor\inertiajs\inertia-laravel\src\Middleware.php:96
31 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
32 - app\Http\Middleware\HandleAppearance.php:21
33 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
34 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:50
35 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
36 - vendor\laravel\framework\src\Illuminate\Auth\Middleware\Authenticate.php:63
37 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
38 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken.php:87
39 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
40 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
41 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
42 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
43 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
44 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
45 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
46 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
47 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
48 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
49 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
50 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
51 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
52 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
53 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
54 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
55 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
56 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
57 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
58 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
59 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
60 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
61 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
62 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
63 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
64 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:109
65 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
66 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:48
67 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
68 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
69 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
70 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
71 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
72 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:26
73 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
74 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
75 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
76 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
77 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1220
78 - public\index.php:20
79 - C:\Program Files\Herd\resources\app.asar.unpacked\resources\valet\server.php:139

## Request

POST /checkout

## Headers

* **cookie**: XSRF-TOKEN=eyJpdiI6IlpBcCsrT0FYLzRrVzdNdE5wdFh6ZUE9PSIsInZhbHVlIjoiZHN4UENub0tXbDVLcTU1UjVRUHprVlpzMVJMVDNqMHNxekEveEJaNG5NYnJVeDJYcmxqVXhhdFJBeUhka1VSckQ0RHZ5YW5uOUEydVRncE02S1o2Q2R4elZqMHc2NitvVnZrYUJ1NjI1a2JLTk1DQkVNRm9HOHZNclhPamlNOWsiLCJtYWMiOiI3MTk2Njk3MTkyMmEwYzM1OTM5NWFlMzliMzRiNWZkMjA5ODI1YjQ0OWRmYTg5M2JmODE2YTBkZTZiYTg2ZGYwIiwidGFnIjoiIn0%3D; pehlione_session=eyJpdiI6IjJQYTduMWdFRVEwQlo5aForN0FjZmc9PSIsInZhbHVlIjoiT2dzbTdLenducFgyY1pLNmltMUNFaFN3QzZ1aVA5Znh3L21iTk1XaXFRRzNaZFBBYUxZRGJ6aSt5T1U1a213RzlGaUFtdkxpanBtSHZjKzBISWlBeDRkaHp6ZDhNRVQ4b0JwZjh4R1hlRzRNd1VIUEs5VURXMitZWU1CN3BZazMiLCJtYWMiOiJjNzM1NjVmNDI5ZTFmZjg4MWRmMTkzOTRmNTI4NzY2YmY3MjVmN2M1MDM4YmE3N2Q4OGRjMzVjYmE5YTBiNmUyIiwidGFnIjoiIn0%3D
* **accept-language**: de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7
* **accept-encoding**: gzip, deflate
* **referer**: http://pehlione_laravel_react.test/checkout
* **origin**: http://pehlione_laravel_react.test
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36
* **x-inertia**: true
* **content-type**: application/json
* **accept**: text/html, application/xhtml+xml
* **x-requested-with**: XMLHttpRequest
* **x-inertia-version**: 774a34d7dfcaf5fa6cd7c586ede750ef
* **x-xsrf-token**: eyJpdiI6IlpBcCsrT0FYLzRrVzdNdE5wdFh6ZUE9PSIsInZhbHVlIjoiZHN4UENub0tXbDVLcTU1UjVRUHprVlpzMVJMVDNqMHNxekEveEJaNG5NYnJVeDJYcmxqVXhhdFJBeUhka1VSckQ0RHZ5YW5uOUEydVRncE02S1o2Q2R4elZqMHc2NitvVnZrYUJ1NjI1a2JLTk1DQkVNRm9HOHZNclhPamlNOWsiLCJtYWMiOiI3MTk2Njk3MTkyMmEwYzM1OTM5NWFlMzliMzRiNWZkMjA5ODI1YjQ0OWRmYTg5M2JmODE2YTBkZTZiYTg2ZGYwIiwidGFnIjoiIn0=
* **content-length**: 383
* **connection**: keep-alive
* **host**: pehlione_laravel_react.test

## Route Context

controller: App\Http\Controllers\CheckoutController@store
route name: checkout.store
middleware: web, auth, verified

## Route Parameters

No route parameter data available.

## Database Queries

* mysql - select * from `sessions` where `id` = 'BYPmtBkC0dKad6tLHuBqRnPw3fm377zCTMdXdBoS' limit 1 (16.59 ms)
* mysql - select * from `users` where `id` = 5 limit 1 (0.75 ms)
* mysql - select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'carts' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists` (1.46 ms)
* mysql - select `carts`.*, (select count(*) from `cart_items` where `carts`.`id` = `cart_items`.`cart_id`) as `items_count` from `carts` where `carts`.`user_id` = 5 and `carts`.`user_id` is not null and `status` = 'active' limit 1 (1 ms)
* mysql - select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'warehouse_notifications' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists` (0.98 ms)
* mysql - select count(*) as aggregate from `warehouse_notifications` where `status` = 'pending' (0.49 ms)
* mysql - select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'mail_logs' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists` (1.6 ms)
* mysql - select count(*) as aggregate from `mail_logs` where `read_at` is null and `deleted_at` is null and `mail_logs`.`deleted_at` is null (0.88 ms)
* mysql - select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'addresses' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists` (1.24 ms)
* mysql - select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'discount_codes' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists` (0.86 ms)
* mysql - select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'discount_redemptions' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists` (0.85 ms)
* mysql - select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'orders' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists` (0.78 ms)
* mysql - select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'order_items' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists` (0.94 ms)
* mysql - select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'order_payments' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists` (0.85 ms)
* mysql - select exists (select 1 from information_schema.tables where table_schema = schema() and table_name = 'warehouse_notifications' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists` (0.87 ms)
* mysql - select * from `carts` where (`user_id` = 5 and `status` = 'active') limit 1 (0.54 ms)
* mysql - select * from `cart_items` where `cart_items`.`cart_id` in (4) (0.48 ms)
* mysql - select * from `products` where `products`.`id` in (3) (0.82 ms)
* mysql - insert into `addresses` (`label`, `first_name`, `last_name`, `company`, `line1`, `line2`, `postal_code`, `city`, `state`, `country`, `phone`, `is_default_shipping`, `is_default_billing`, `user_id`, `updated_at`, `created_at`) values (NULL, 'Mustafa', 'Özdemir', NULL, 'Am Richtsberg 20', NULL, '35037', 'Marburg', 'Hessen', 'GE', NULL, 1, 1, 5, '2025-10-13 21:08:29', '2025-10-13 21:08:29') (5.35 ms)
* mysql - update `addresses` set `is_default_shipping` = 0, `addresses`.`updated_at` = '2025-10-13 21:08:29' where `addresses`.`user_id` = 5 and `addresses`.`user_id` is not null and `addresses`.`id` != 2 and `is_default_shipping` = 1 (0.57 ms)
* mysql - update `addresses` set `is_default_billing` = 0, `addresses`.`updated_at` = '2025-10-13 21:08:29' where `addresses`.`user_id` = 5 and `addresses`.`user_id` is not null and `addresses`.`id` != 2 and `is_default_billing` = 1 (0.49 ms)
* mysql - insert into `orders` (`cart_id`, `discount_code_id`, `status`, `payment_status`, `payment_method`, `currency`, `subtotal`, `discount_total`, `shipping_total`, `tax_total`, `total`, `shipping_address`, `billing_address`, `shipping_method`, `notes`, `placed_at`, `paid_at`, `user_id`, `updated_at`, `created_at`) values (4, NULL, 'processing', 'paid', 'paypal', 'EUR', 1599, 0, 29.9, 0, 1628.9, '{"label":null,"first_name":"Mustafa","last_name":"\u00d6zdemir","company":null,"line1":"Am Richtsberg 20","line2":null,"postal_code":"35037","city":"Marburg","state":"Hessen","country":"GE","phone":null}', '{"label":null,"first_name":"Mustafa","last_name":"\u00d6zdemir","company":null,"line1":"Am Richtsberg 20","line2":null,"postal_code":"35037","city":"Marburg","state":"Hessen","country":"GE","phone":null}', 'Standard', NULL, '2025-10-13 21:08:29', '2025-10-13 21:08:29', 5, '2025-10-13 21:08:29', '2025-10-13 21:08:29') (0.78 ms)
* mysql - insert into `order_items` (`product_id`, `name`, `sku`, `size`, `quantity`, `unit_price`, `total_price`, `attribute_tags`, `metadata`, `order_id`, `updated_at`, `created_at`) values (3, 'Lumen Arc OLED TV 55"', 'ELC-LUM-55', '55"', 1, '1599.00', '1599.00', '["eco-friendly","energy-efficient","smart-enabled"]', '{"cart_item_id":17}', 4, '2025-10-13 21:08:29', '2025-10-13 21:08:29') (0.69 ms)
* mysql - insert into `order_payments` (`method`, `amount`, `currency`, `status`, `reference`, `payload`, `processed_at`, `order_id`, `updated_at`, `created_at`) values ('paypal', 1628.9, 'EUR', 'captured', 'SIM-68ED6A4D1AEA9', '{"simulated":true,"method":"paypal"}', '2025-10-13 21:08:29', 4, '2025-10-13 21:08:29', '2025-10-13 21:08:29') (0.65 ms)
* mysql - insert into `warehouse_notifications` (`order_id`, `status`, `message`, `metadata`, `updated_at`, `created_at`) values (4, 'pending', 'Order #4 ready for fulfilment. Shipping to Mustafa Özdemir, 35037 Marburg (GE).', '{"payment_method":"paypal","total":1628.9,"alert_email":"lager@pehlione.com"}', '2025-10-13 21:08:29', '2025-10-13 21:08:29') (0.53 ms)
* mysql - update `carts` set `status` = 'submitted', `carts`.`updated_at` = '2025-10-13 21:08:29' where `id` = 4 (0.51 ms)
* mysql - select * from `order_items` where `order_items`.`order_id` in (4) (0.56 ms)
* mysql - select * from `orders` where `id` = 4 limit 1 (0.7 ms)
* mysql - select * from `order_items` where `order_items`.`order_id` in (4) (0.44 ms)
* mysql - select * from `users` where `users`.`id` in (5) (0.4 ms)
* mysql - insert into `mail_logs` (`direction`, `status`, `sent_at`, `subject`, `to_email`, `to_name`, `context`, `related_type`, `related_id`, `updated_at`, `created_at`) values ('outgoing', 'sent', '2025-10-13 21:08:31', 'Order #4 confirmation', 'lager@pehlione.com', 'Lager', '{"order_id":4,"type":"customer_confirmation"}', 'App\Models\Order', 4, '2025-10-13 21:08:31', '2025-10-13 21:08:31') (12.24 ms)
* mysql - select * from `orders` where `id` = 4 limit 1 (0.92 ms)
* mysql - select * from `order_items` where `order_items`.`order_id` in (4) (0.72 ms)
* mysql - select * from `users` where `users`.`id` in (5) (0.55 ms)
* mysql - select * from `users` where `users`.`id` = 5 limit 1 (0.61 ms)
