
## Events

<br>



### Introduction

<br>

 

Laravel'ın **events** özelliği, uygulamanız içinde gerçekleşen çeşitli olaylara abone olmanızı ve bunları dinlemenizi sağlayan basit bir **observer pattern** (gözlemci deseni) uygulaması sunar. Event sınıfları genellikle `app/Events` dizininde, bunların **listeners** (dinleyicileri) ise `app/Listeners` dizininde saklanır. Uygulamanızda bu dizinleri görmüyorsanız endişelenmeyin; `Artisan` konsol komutlarını kullanarak event ve listener oluşturduğunuzda Laravel bu dizinleri sizin için oluşturacaktır.

Events, uygulamanızın farklı bileşenlerini birbirinden ayırmak için harika bir yöntemdir, çünkü tek bir event birbirinden bağımsız birden fazla listener tarafından dinlenebilir. Örneğin, bir sipariş gönderildiğinde kullanıcıya bir **Slack** bildirimi göndermek isteyebilirsiniz. Sipariş işleme kodunuzu Slack bildirimi koduna bağlamak yerine, bir `App\Events\OrderShipped` event’i tetikleyebilir ve bu event’i dinleyen bir listener Slack bildirimi gönderebilir.

<br>



### Generating Events and Listeners

Event ve listener’ları hızlıca oluşturmak için `make:event` ve `make:listener` Artisan komutlarını kullanabilirsiniz:

```bash
php artisan make:event PodcastProcessed
 
php artisan make:listener SendPodcastNotification --event=PodcastProcessed
````

Kolaylık olması açısından, bu komutları argüman belirtmeden de çalıştırabilirsiniz. Bu durumda Laravel, sizden sınıf adını ve listener oluştururken hangi event’i dinleyeceğini isteyecektir:

```bash
php artisan make:event
 
php artisan make:listener
```

<br>




### Registering Events and Listeners

#### Event Discovery

Varsayılan olarak Laravel, uygulamanızın `Listeners` dizinini tarayarak event listener’ları otomatik olarak bulur ve kaydeder. Laravel, `handle` veya `__invoke` ile başlayan herhangi bir listener metodunu, imzasında type-hint olarak belirtilen event için otomatik olarak dinleyici olarak kaydeder:

```php
use App\Events\PodcastProcessed;
 
class SendPodcastNotification
{
    /**
     * Handle the event.
     */
    public function handle(PodcastProcessed $event): void
    {
        // ...
    }
}
```

Birden fazla event’i dinlemek için PHP’nin **union types** özelliğini kullanabilirsiniz:

```php
/**
 * Handle the event.
 */
public function handle(PodcastProcessed|PodcastPublished $event): void
{
    // ...
}
```

Listener’larınızı farklı bir dizinde veya birden fazla dizinde tutmak istiyorsanız, `bootstrap/app.php` dosyanızda `withEvents` metodunu kullanarak Laravel’e bu dizinleri taramasını söyleyebilirsiniz:

```php
->withEvents(discover: [
    __DIR__.'/../app/Domain/Orders/Listeners',
])
```

Benzer birden fazla dizini taramak için `*` karakterini joker karakter (wildcard) olarak kullanabilirsiniz:

```php
->withEvents(discover: [
    __DIR__.'/../app/Domain/*/Listeners',
])
```

Uygulamanızda kayıtlı tüm listener’ları listelemek için:

```bash
php artisan event:list
```

<br>




### Event Discovery in Production

Uygulamanızın performansını artırmak için, `optimize` veya `event:cache` Artisan komutlarını kullanarak tüm listener’larınızın bir manifest’ini önbelleğe alabilirsiniz. Bu komut genellikle uygulamanızın dağıtım sürecinin bir parçası olarak çalıştırılmalıdır. Manifest, event kayıt sürecini hızlandırmak için framework tarafından kullanılır. Event önbelleğini temizlemek için:

```bash
php artisan event:clear
```

<br>




### Manually Registering Events

`Event` facade’ını kullanarak event’leri ve bunlara karşılık gelen listener’ları manuel olarak `AppServiceProvider` sınıfının `boot` metodunda kaydedebilirsiniz:

```php
use App\Domain\Orders\Events\PodcastProcessed;
use App\Domain\Orders\Listeners\SendPodcastNotification;
use Illuminate\Support\Facades\Event;
 
public function boot(): void
{
    Event::listen(
        PodcastProcessed::class,
        SendPodcastNotification::class,
    );
}
```

Tüm kayıtlı listener’ları listelemek için yine:

```bash
php artisan event:list
```

<br>




### Closure Listeners

Genellikle listener’lar sınıf olarak tanımlanır; ancak closure (anonim fonksiyon) olarak da tanımlanabilirler. Bu işlemi `AppServiceProvider` sınıfınızın `boot` metodunda yapabilirsiniz:

```php
use App\Events\PodcastProcessed;
use Illuminate\Support\Facades\Event;
 
public function boot(): void
{
    Event::listen(function (PodcastProcessed $event) {
        // ...
    });
}
```

<br>




### Queueable Anonymous Event Listeners

Closure tabanlı event listener’ları kuyruğa almak isterseniz, `Illuminate\Events\queueable` fonksiyonuna sarabilirsiniz:

```php
use App\Events\PodcastProcessed;
use function Illuminate\Events\queueable;
use Illuminate\Support\Facades\Event;
 
public function boot(): void
{
    Event::listen(queueable(function (PodcastProcessed $event) {
        // ...
    }));
}
```

Kuyruklanan bu listener’lar için `onConnection`, `onQueue` ve `delay` metodlarını kullanarak özelleştirme yapabilirsiniz:

```php
Event::listen(queueable(function (PodcastProcessed $event) {
    // ...
})->onConnection('redis')->onQueue('podcasts')->delay(now()->addSeconds(10)));
```

Anonim kuyruğa alınmış listener hatalarını yönetmek için `catch` metoduna bir closure tanımlayabilirsiniz:

```php
use App\Events\PodcastProcessed;
use function Illuminate\Events\queueable;
use Illuminate\Support\Facades\Event;
use Throwable;
 
Event::listen(queueable(function (PodcastProcessed $event) {
    // ...
})->catch(function (PodcastProcessed $event, Throwable $e) {
    // Kuyruklanan listener başarısız oldu...
}));
```

<br>




### Wildcard Event Listeners

Birden fazla event’i aynı listener üzerinde yakalamak için `*` karakterini kullanabilirsiniz. Wildcard listener’lar, event adını ilk parametre, event verisini ise ikinci parametre olarak alır:

```php
Event::listen('event.*', function (string $eventName, array $data) {
    // ...
});
```

<br>




### Defining Events

Bir event sınıfı, olayla ilgili verileri tutan bir veri konteyneridir. Örneğin, bir `App\Events\OrderShipped` event’i bir **Eloquent ORM** nesnesi alabilir:

```php
<?php
 
namespace App\Events;
 
use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
 
class OrderShipped
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public function __construct(
        public Order $order,
    ) {}
}
```

Bu sınıf herhangi bir mantık içermez; yalnızca satın alınan `App\Models\Order` örneğini taşır. `SerializesModels` trait’i, event nesnesi PHP’nin `serialize` fonksiyonu ile serileştirildiğinde Eloquent modellerini düzgün şekilde serileştirmeyi sağlar (örneğin kuyruklu listener’larda).

<br>




### Defining Listeners

Örnek event’imiz için listener’a bakalım. Event listener’ları `handle` metodunda event örneğini alır. `make:listener` komutu `--event` seçeneğiyle çağrıldığında, uygun event sınıfını otomatik olarak import eder ve `handle` metodunda type-hint olarak tanımlar:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderShipped;
 
class SendShipmentNotification
{
    public function __construct() {}
 
    public function handle(OrderShipped $event): void
    {
        // $event->order üzerinden erişim...
    }
}
```

Listener’lar, constructor’larında ihtiyaç duydukları bağımlılıkları type-hint olarak tanımlayabilir. Laravel’in **service container** yapısı sayesinde bu bağımlılıklar otomatik olarak enjekte edilir.

<br>




### Stopping The Propagation Of An Event

Bazı durumlarda, bir event’in diğer listener’lara iletilmesini durdurmak isteyebilirsiniz. Bunu listener’ın `handle` metodundan `false` döndürerek yapabilirsiniz.

<br>




### Queued Event Listeners

Listener’ınız e-posta gönderme veya HTTP isteği yapma gibi yavaş işlemler yapacaksa, onu kuyruğa almak mantıklı olabilir. Bunun için öncelikle **queue** yapılandırmasını tamamlamalı ve bir **queue worker** başlatmalısınız.

Bir listener’ın kuyruklanacağını belirtmek için sınıfa `ShouldQueue` arayüzünü ekleyin. `make:listener` komutuyla oluşturulan listener’lar zaten bu arayüzü içe aktarır:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderShipped;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class SendShipmentNotification implements ShouldQueue
{
    // ...
}
```

Artık bu listener tarafından dinlenen event tetiklendiğinde, listener Laravel’in kuyruk sistemi tarafından otomatik olarak kuyruğa alınır.

<br>




### Customizing The Queue Connection, Name, & Delay

Bir listener’ın kuyruk bağlantısını, adını veya gecikme süresini özelleştirmek için aşağıdaki özellikleri tanımlayabilirsiniz:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderShipped;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class SendShipmentNotification implements ShouldQueue
{
    public $connection = 'sqs';
    public $queue = 'listeners';
    public $delay = 60;
}
```

Bunları çalışma zamanında dinamik olarak belirlemek isterseniz `viaConnection`, `viaQueue` veya `withDelay` metodlarını tanımlayabilirsiniz:

```php
public function viaConnection(): string
{
    return 'sqs';
}
 
public function viaQueue(): string
{
    return 'listeners';
}
 
public function withDelay(OrderShipped $event): int
{
    return $event->highPriority ? 0 : 60;
}
```

<br>




### Conditionally Queueing Listeners

Bazı durumlarda listener’ın kuyruğa alınıp alınmayacağına çalışma zamanında karar vermek isteyebilirsiniz. Bunun için listener’a `shouldQueue` metodu ekleyebilirsiniz. Bu metod `false` dönerse listener kuyruğa alınmaz:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class RewardGiftCard implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        // ...
    }
 
    public function shouldQueue(OrderCreated $event): bool
    {
        return $event->order->subtotal >= 5000;
    }
}
```

<br>




## Manually Interacting With the Queue

Listener’ın kuyruğa alınmış job’unun `delete` ve `release` metodlarına manuel olarak erişmeniz gerekirse, bunu `Illuminate\Queue\InteractsWithQueue` trait’ini kullanarak yapabilirsiniz. Bu trait, oluşturulan listener’larda varsayılan olarak dahil edilir ve bu metodlara erişim sağlar:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderShipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
 
class SendShipmentNotification implements ShouldQueue
{
    use InteractsWithQueue;
 
    public function handle(OrderShipped $event): void
    {
        if ($condition) {
            $this->release(30);
        }
    }
}
````

<br>




### Queued Event Listeners and Database Transactions

Kuyruklu listener’lar veritabanı işlemleri (transactions) içinde tetiklendiğinde, listener kuyruğa veritabanı işlemi commit edilmeden önce alınabilir. Bu durumda, işlem içindeki model güncellemeleri veya yeni kayıtlar henüz veritabanına yansımamış olabilir. Listener bu modellere bağımlıysa, beklenmeyen hatalar ortaya çıkabilir.

Eğer kuyruğunuzun `after_commit` yapılandırma seçeneği `false` olarak ayarlanmışsa, belirli bir listener’ın yalnızca tüm veritabanı işlemleri tamamlandıktan sonra tetiklenmesini sağlamak için listener sınıfında `ShouldQueueAfterCommit` arayüzünü uygulayabilirsiniz:

```php
<?php
 
namespace App\Listeners;
 
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Queue\InteractsWithQueue;
 
class SendShipmentNotification implements ShouldQueueAfterCommit
{
    use InteractsWithQueue;
}
```

Bu konu hakkında daha fazla bilgi için **queued jobs** ve **database transactions** belgelerine göz atabilirsiniz.

<br>




### Queued Listener Middleware

Kuyruklu listener’lar, **job middleware** kullanabilir. Job middleware, listener’ın çalıştırılması etrafında özel mantık tanımlamanıza izin verir ve listener içindeki gereksiz kodu azaltır. Middleware oluşturduktan sonra, listener’ın `middleware` metodundan döndürerek listener’a ekleyebilirsiniz:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderShipped;
use App\Jobs\Middleware\RateLimited;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class SendShipmentNotification implements ShouldQueue
{
    public function handle(OrderShipped $event): void
    {
        // Event işleme...
    }
 
    public function middleware(OrderShipped $event): array
    {
        return [new RateLimited];
    }
}
```

<br>




### Encrypted Queued Listeners

Laravel, kuyruklu listener verilerini şifreleyerek gizlilik ve bütünlük sağlar. Bunun için listener sınıfına `ShouldBeEncrypted` arayüzünü eklemeniz yeterlidir. Laravel, bu interface’i tanıdığında listener’ı kuyruğa eklemeden önce otomatik olarak şifreler:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderShipped;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class SendShipmentNotification implements ShouldQueue, ShouldBeEncrypted
{
    // ...
}
```

<br>




### Handling Failed Jobs

Kuyruklu listener’lar bazen başarısız olabilir. Listener maksimum deneme sayısını aştığında `failed` metodu çağrılır. Bu metod, event örneğini ve hataya neden olan `Throwable` nesnesini alır:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderShipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Throwable;
 
class SendShipmentNotification implements ShouldQueue
{
    use InteractsWithQueue;
 
    public function handle(OrderShipped $event): void
    {
        // ...
    }
 
    public function failed(OrderShipped $event, Throwable $exception): void
    {
        // ...
    }
}
```

<br>




### Specifying Queued Listener Maximum Attempts

Bir listener hata veriyorsa, sonsuz şekilde yeniden denenmesini istemezsiniz. Laravel, listener’ın kaç kez veya ne kadar süreyle yeniden denenebileceğini belirlemenize olanak tanır.

Listener sınıfınızda `tries` özelliğini tanımlayarak deneme sayısını belirleyebilirsiniz:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderShipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
 
class SendShipmentNotification implements ShouldQueue
{
    use InteractsWithQueue;
 
    public $tries = 5;
}
```

Alternatif olarak, listener’ın belirli bir süre sonra yeniden denenmemesini sağlamak için `retryUntil` metodunu tanımlayabilirsiniz:

```php
use DateTime;
 
public function retryUntil(): DateTime
{
    return now()->addMinutes(5);
}
```

Hem `retryUntil` hem de `tries` tanımlıysa, Laravel `retryUntil` metoduna öncelik verir.

<br>




### Specifying Queued Listener Backoff

Listener bir hata aldığında yeniden denemeden önce kaç saniye beklenmesi gerektiğini belirlemek için `backoff` özelliğini tanımlayabilirsiniz:

```php
public $backoff = 3;
```

Daha karmaşık bir bekleme süresi belirlemek isterseniz `backoff` metodunu kullanabilirsiniz:

```php
public function backoff(OrderShipped $event): int
{
    return 3;
}
```

Exponential (üstel) backoff ayarlamak için bir dizi döndürebilirsiniz:

```php
public function backoff(OrderShipped $event): array
{
    return [1, 5, 10];
}
```

<br>




### Specifying Queued Listener Max Exceptions

Bir listener’ın çok sayıda denemesi olabilir, ancak belirli sayıda işlenmemiş istisnadan sonra başarısız sayılmasını isteyebilirsiniz. Bunun için `maxExceptions` özelliğini tanımlayabilirsiniz:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderShipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
 
class SendShipmentNotification implements ShouldQueue
{
    use InteractsWithQueue;
 
    public $tries = 25;
    public $maxExceptions = 3;
 
    public function handle(OrderShipped $event): void
    {
        // ...
    }
}
```

Bu durumda listener, en fazla 25 kez yeniden denenir ancak 3 istisnadan sonra başarısız olur.

<br>




### Specifying Queued Listener Timeout

Listener’ınızın ne kadar süreceğini yaklaşık olarak biliyorsanız, bir **timeout** değeri tanımlayabilirsiniz. Listener belirtilen süreden daha uzun çalışırsa worker hata verir:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderShipped;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class SendShipmentNotification implements ShouldQueue
{
    public $timeout = 120;
}
```

Timeout durumunda listener’ın başarısız sayılmasını istiyorsanız `failOnTimeout` özelliğini `true` olarak belirleyin:

```php
public $failOnTimeout = true;
```

<br>




### Dispatching Events

Bir event’i tetiklemek için, event sınıfı üzerinde `dispatch` metodunu çağırabilirsiniz. Bu metod, `Illuminate\Foundation\Events\Dispatchable` trait’i tarafından sağlanır:

```php
<?php
 
namespace App\Http\Controllers;
 
use App\Events\OrderShipped;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
 
class OrderShipmentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $order = Order::findOrFail($request->order_id);
 
        // Sipariş gönderim işlemleri...
 
        OrderShipped::dispatch($order);
 
        return redirect('/orders');
    }
}
```

Event’i koşullu olarak tetiklemek için `dispatchIf` veya `dispatchUnless` metodlarını kullanabilirsiniz:

```php
OrderShipped::dispatchIf($condition, $order);
OrderShipped::dispatchUnless($condition, $order);
```

<br>




### Dispatching Events After Database Transactions

Bazı durumlarda, event’in yalnızca veritabanı işlemi tamamlandıktan sonra tetiklenmesini isteyebilirsiniz. Bunun için event sınıfında `ShouldDispatchAfterCommit` arayüzünü uygulayın:

```php
<?php
 
namespace App\Events;
 
use App\Models\Order;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
 
class OrderShipped implements ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public function __construct(public Order $order) {}
}
```

<br>




### Deferring Events

**Deferred events**, model event’lerinin ve listener’ların belirli bir kod bloğu tamamlanana kadar ertelenmesini sağlar. Bu, ilişkili tüm kayıtların oluşturulduğundan emin olmanız gerektiğinde özellikle faydalıdır.

```php
use App\Models\User;
use Illuminate\Support\Facades\Event;
 
Event::defer(function () {
    $user = User::create(['name' => 'Victoria Otwell']);
    $user->posts()->create(['title' => 'My first post!']);
});
```

Closure içinde tetiklenen tüm event’ler, closure tamamlandıktan sonra gönderilecektir. Eğer closure içinde bir istisna oluşursa, event’ler tetiklenmez.

Belirli event’leri ertelemek için, `defer` metoduna ikinci argüman olarak event dizisini geçebilirsiniz:

```php
Event::defer(function () {
    $user = User::create(['name' => 'Victoria Otwell']);
    $user->posts()->create(['title' => 'My first post!']);
}, ['eloquent.created: '.User::class]);
```

<br>




## Event Subscribers

### Writing Event Subscribers

Event subscribers, tek bir sınıf içinde birden fazla event’e abone olmanıza olanak tanır. Subscriber sınıfı bir `subscribe` metodu tanımlamalıdır ve bu metod bir event dispatcher örneği alır:

```php
<?php
 
namespace App\Listeners;
 
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;
 
class UserEventSubscriber
{
    public function handleUserLogin(Login $event): void {}
    public function handleUserLogout(Logout $event): void {}
 
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(Login::class, [UserEventSubscriber::class, 'handleUserLogin']);
        $events->listen(Logout::class, [UserEventSubscriber::class, 'handleUserLogout']);
    }
}
```

Alternatif olarak, `subscribe` metodundan bir dizi döndürerek event ve metod isimlerini belirtebilirsiniz:

```php
public function subscribe(Dispatcher $events): array
{
    return [
        Login::class => 'handleUserLogin',
        Logout::class => 'handleUserLogout',
    ];
}
```

<br>




### Registering Event Subscribers

Subscriber’lar, Laravel’in event discovery mekanizması ile otomatik olarak kaydedilebilir. Aksi halde, `Event` facade’ının `subscribe` metodunu kullanarak manuel olarak kaydedebilirsiniz. Bu işlem genellikle `AppServiceProvider` içinde yapılır:

```php
<?php
 
namespace App\Providers;
 
use App\Listeners\UserEventSubscriber;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
 
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::subscribe(UserEventSubscriber::class);
    }
}
```

<br>




## Testing

Event’leri test ederken, listener’ların çalıştırılmasını engellemek isteyebilirsiniz. Böylece yalnızca event’in tetiklenip tetiklenmediğini test edebilirsiniz. `Event::fake()` metodu, listener’ların çalıştırılmasını durdurur ve ardından `assertDispatched`, `assertNotDispatched` ve `assertNothingDispatched` metodlarıyla kontrol yapabilirsiniz:

```php
use App\Events\OrderFailedToShip;
use App\Events\OrderShipped;
use Illuminate\Support\Facades\Event;

test('orders can be shipped', function () {
    Event::fake();
 
    // Sipariş gönderimi...
 
    Event::assertDispatched(OrderShipped::class);
    Event::assertDispatched(OrderShipped::class, 2);
    Event::assertDispatchedOnce(OrderShipped::class);
    Event::assertNotDispatched(OrderFailedToShip::class);
    Event::assertNothingDispatched();
});
```

Belirli koşullara göre event’in tetiklenip tetiklenmediğini test etmek için closure kullanabilirsiniz:

```php
Event::assertDispatched(function (OrderShipped $event) use ($order) {
    return $event->order->id === $order->id;
});
```

Bir listener’ın belirli bir event’i dinleyip dinlemediğini test etmek için:

```php
Event::assertListening(
    OrderShipped::class,
    SendShipmentNotification::class
);
```

> Not: `Event::fake()` çağrısından sonra listener’lar çalıştırılmaz, bu nedenle model event’lerine bağımlı factory’leriniz varsa `Event::fake()` metodunu onları kullandıktan sonra çağırın.

<br>



### Faking a Subset of Events

Belirli event’ler için listener’ları sahte hale getirmek isterseniz:

```php
Event::fake([
    OrderCreated::class,
]);
```

Tüm event’leri sahte hale getirip, belirli event’leri hariç tutmak için:

```php
Event::fake()->except([
    OrderCreated::class,
]);
```

<br>






### Scoped Event Fakes

Testinizin yalnızca bir bölümünde event listener’larını sahte hale getirmek için `fakeFor` metodunu kullanabilirsiniz:

```php
use App\Events\OrderCreated;
use App\Models\Order;
use Illuminate\Support\Facades\Event;
 
test('orders can be processed', function () {
    $order = Event::fakeFor(function () {
        $order = Order::factory()->create();
        Event::assertDispatched(OrderCreated::class);
        return $order;
    });
 
    $order->update([
        // ...
    ]);
});
```

Laravel, yazılım oluşturmanın, dağıtmanın ve izlemenin en verimli yoludur.

