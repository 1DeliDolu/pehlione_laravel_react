
<br>


## Introduction

Web uygulamanızı geliştirirken, yüklenen bir CSV dosyasını ayrıştırma ve kaydetme gibi bazı görevler, tipik bir web isteği sırasında gerçekleştirilemeyecek kadar uzun sürebilir. Neyse ki, Laravel arka planda işlenebilecek kuyruklu (queued) işler oluşturmayı kolaylaştırır. Zaman alıcı görevleri bir kuyruğa taşıyarak, uygulamanız web isteklerine son derece hızlı yanıt verebilir ve müşterilerinize daha iyi bir kullanıcı deneyimi sunabilir.

Laravel kuyrukları, Amazon SQS, Redis veya ilişkisel bir veritabanı gibi çeşitli kuyruk backend’leri arasında birleşik bir kuyruk API’si sağlar.

Laravel’in kuyruk yapılandırma seçenekleri, uygulamanızın `config/queue.php` yapılandırma dosyasında saklanır. Bu dosyada, veritabanı, Amazon SQS, Redis ve Beanstalkd sürücülerini içeren framework’te bulunan her bir kuyruk sürücüsü için bağlantı yapılandırmaları bulacaksınız. Ayrıca, işleri hemen çalıştıran senkron bir sürücü (geliştirme veya test sırasında kullanmak için) ve işleri tamamen yok sayan null sürücüsü de mevcuttur.

**Laravel Horizon**, Redis tabanlı kuyruklarınız için güzel bir dashboard ve yapılandırma sistemidir. Daha fazla bilgi için Horizon dokümantasyonuna göz atabilirsiniz.

<br>


## Connections vs. Queues

Laravel kuyruklarına başlamadan önce, "connections" (bağlantılar) ve "queues" (kuyruklar) arasındaki farkı anlamak önemlidir. `config/queue.php` dosyasında bir `connections` yapılandırma dizisi bulunur. Bu seçenek, Amazon SQS, Beanstalk veya Redis gibi arka uç kuyruk servislerine olan bağlantıları tanımlar. Ancak, herhangi bir kuyruk bağlantısı birden fazla "queue" (kuyruk) içerebilir; bunlar farklı iş yığınları veya kümeleri olarak düşünülebilir.

Her bağlantı yapılandırma örneğinde bir `queue` niteliği bulunduğuna dikkat edin. Bu, belirli bir bağlantıya gönderilen işlerin varsayılan olarak yerleştirileceği kuyruktur. Yani, bir işi açıkça hangi kuyruğa gönderileceğini belirtmeden dispatch ederseniz, iş bu `queue` niteliğinde tanımlı kuyruğa yerleştirilecektir:

```php
use App\Jobs\ProcessPodcast;

// Bu iş varsayılan bağlantının varsayılan kuyruğuna gönderilir...
ProcessPodcast::dispatch();

// Bu iş varsayılan bağlantının "emails" kuyruğuna gönderilir...
ProcessPodcast::dispatch()->onQueue('emails');
````

Bazı uygulamalar, işleri birden fazla kuyruğa göndermek zorunda kalmadan tek bir basit kuyruğu tercih edebilir. Ancak, işleri birden fazla kuyruğa göndermek, işlemleri önceliklendirmek veya bölümlere ayırmak isteyen uygulamalar için oldukça yararlıdır; çünkü Laravel kuyruk işçisi, hangi kuyruğun hangi öncelikle işleneceğini belirlemenize olanak tanır. Örneğin, yüksek öncelikli bir kuyruğa (`high`) gönderdiğiniz işler için, bu kuyruğa öncelik veren bir işçi çalıştırabilirsiniz:

```bash
php artisan queue:work --queue=high,default
```

<br>


## Driver Notes and Prerequisites

### Database

Veritabanı kuyruk sürücüsünü kullanmak için işleri tutacak bir tabloya ihtiyacınız vardır. Bu genellikle Laravel’in varsayılan `0001_01_01_000002_create_jobs_table.php` migration dosyasında bulunur; ancak, uygulamanızda bu migration yoksa, aşağıdaki Artisan komutunu kullanarak oluşturabilirsiniz:

```bash
php artisan make:queue-table

php artisan migrate
```

### Redis

Redis kuyruk sürücüsünü kullanmak için, `config/database.php` dosyasında bir Redis bağlantısı yapılandırmalısınız.

Redis kuyruk sürücüsü `serializer` ve `compression` seçeneklerini desteklemez.

#### Redis Cluster

Redis kuyruk bağlantınız bir Redis Cluster kullanıyorsa, kuyruk adlarınız bir **key hash tag** içermelidir. Bu, belirli bir kuyruk için tüm Redis anahtarlarının aynı hash slot içinde bulunmasını sağlamak için gereklidir:

```php
'redis' => [
    'driver' => 'redis',
    'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
    'queue' => env('REDIS_QUEUE', '{default}'),
    'retry_after' => env('REDIS_QUEUE_RETRY_AFTER', 90),
    'block_for' => null,
    'after_commit' => false,
],
```

#### Blocking

Redis kuyruğunu kullanırken, `block_for` yapılandırma seçeneğini kullanarak, sürücünün bir iş kullanılabilir olana kadar ne kadar süre beklemesi gerektiğini belirtebilirsiniz. Bu değeri kuyruk yükünüze göre ayarlamak, sürekli Redis veritabanını sorgulamaktan daha verimli olabilir. Örneğin, sürücünün bir iş için 5 saniye beklemesini belirtmek isteyebilirsiniz:

```php
'redis' => [
    'driver' => 'redis',
    'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
    'queue' => env('REDIS_QUEUE', 'default'),
    'retry_after' => env('REDIS_QUEUE_RETRY_AFTER', 90),
    'block_for' => 5,
    'after_commit' => false,
],
```

`block_for` değerini 0 yapmak, kuyruk işçilerinin bir iş mevcut olana kadar **süresiz olarak** beklemesine neden olur. Bu, SIGTERM gibi sinyallerin bir sonraki iş işlenene kadar yakalanmasını da engeller.

### Other Driver Prerequisites

Aşağıdaki bağımlılıklar, ilgili kuyruk sürücüleri için gereklidir. Bu bağımlılıkları Composer aracılığıyla kurabilirsiniz:

* **Amazon SQS:** `aws/aws-sdk-php ~3.0`
* **Beanstalkd:** `pda/pheanstalk ~5.0`
* **Redis:** `predis/predis ~2.0` veya `phpredis` PHP eklentisi
* **MongoDB:** `mongodb/laravel-mongodb`

<br>


## Creating Jobs

### Generating Job Classes

Varsayılan olarak, uygulamanızdaki tüm kuyruklanabilir işler `app/Jobs` dizininde saklanır. Eğer bu dizin mevcut değilse, aşağıdaki komutu çalıştırdığınızda otomatik olarak oluşturulur:

```bash
php artisan make:job ProcessPodcast
```

Oluşturulan sınıf, Laravel’e bu işin kuyrukta çalıştırılması gerektiğini bildiren `Illuminate\Contracts\Queue\ShouldQueue` arayüzünü uygular.

İş şablonları (job stubs), **stub publishing** kullanılarak özelleştirilebilir.

<br>


### Class Structure

Job sınıfları oldukça basittir ve genellikle yalnızca bir `handle` metoduna sahiptir. Bu metot, iş kuyruğu tarafından işlendiğinde çağrılır. Örneğin, bir podcast yükleme servisini yönettiğimizi ve yüklenen podcast dosyalarını yayınlanmadan önce işlememiz gerektiğini varsayalım:

```php
<?php

namespace App\Jobs;

use App\Models\Podcast;
use App\Services\AudioProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPodcast implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Podcast $podcast,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AudioProcessor $processor): void
    {
        // Process uploaded podcast...
    }
}
```

Bu örnekte, bir Eloquent modelini doğrudan işin kurucusuna (constructor) iletebildiğimize dikkat edin. Job sınıfının kullandığı `Queueable` trait’i sayesinde, Eloquent modelleri ve ilişkili verileri işlenirken düzgün bir şekilde serileştirilir ve yeniden oluşturulur.

Eğer bir queued job bir Eloquent modelini constructor’da kabul ediyorsa, yalnızca modelin kimliği kuyruğa serileştirilir. İş işlendiğinde, kuyruk sistemi modelin tam örneğini ve ilişkilerini otomatik olarak veritabanından yeniden yükler. Bu yöntem, daha küçük iş yüklerinin kuyruk sürücüsüne gönderilmesini sağlar.

<br>


### handle Method Dependency Injection

`handle` metodu, iş kuyruk tarafından işlendiğinde çağrılır. Bu metoda bağımlılıkları type-hint ederek otomatik olarak enjekte ettirebilirsiniz. Laravel’in service container’ı bu bağımlılıkları otomatik olarak sağlar.

Container’ın bağımlılıkları nasıl enjekte ettiğini tamamen kontrol etmek isterseniz, `bindMethod` metodunu kullanabilirsiniz. Bu metot, job ve container’ı alan bir callback kabul eder. Genellikle bu çağrı, `App\Providers\AppServiceProvider` içindeki `boot` metoduna eklenmelidir:

```php
use App\Jobs\ProcessPodcast;
use App\Services\AudioProcessor;
use Illuminate\Contracts\Foundation\Application;

$this->app->bindMethod([ProcessPodcast::class, 'handle'], function (ProcessPodcast $job, Application $app) {
    return $job->handle($app->make(AudioProcessor::class));
});
```

Ham binary veriler (örneğin bir resim dosyasının içeriği gibi), kuyruğa gönderilmeden önce `base64_encode` fonksiyonundan geçirilmelidir; aksi halde iş doğru şekilde JSON’a serileştirilemeyebilir.

<br>


## Queued Relationships

Tüm yüklenmiş Eloquent ilişkileri bir iş kuyruğuna serileştirildiği için, serileştirilmiş iş verisi oldukça büyük olabilir. Ayrıca, iş deseralize edilip ilişkiler yeniden yüklendiğinde, daha önce uygulanmış ilişki kısıtlamaları korunmaz. Bu nedenle, belirli bir ilişkinin yalnızca bir alt kümesiyle çalışmak istiyorsanız, bu kısıtlamaları iş içinde yeniden uygulamalısınız.

Ya da ilişkilerin serileştirilmesini önlemek için, `withoutRelations` metodunu kullanabilirsiniz:

```php
public function __construct(Podcast $podcast)
{
    $this->podcast = $podcast->withoutRelations();
}
```

PHP constructor property promotion kullanıyorsanız, `#[WithoutRelations]` attribute’unu kullanabilirsiniz:

```php
use Illuminate\Queue\Attributes\WithoutRelations;

public function __construct(
    #[WithoutRelations]
    public Podcast $podcast,
) {}
```

Tüm modellerin ilişkileri olmadan serileştirilmesini istiyorsanız, attribute’u sınıfın tamamına uygulayabilirsiniz:

```php
#[WithoutRelations]
class ProcessPodcast implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Podcast $podcast,
        public DistributionPlatform $platform,
    ) {}
}
```

Bir iş, tek bir model yerine bir model koleksiyonu alıyorsa, bu koleksiyon içindeki modellerin ilişkileri yeniden yüklenmeyecektir. Bu, büyük veri kümelerinde gereksiz kaynak kullanımını önlemek için yapılır.

---

(Bu noktadan itibaren metin **“Unique Jobs”**, **“Encrypted Jobs”**, ve **“Job Middleware”** başlıklarıyla devam eder — dilersen oradan çeviriye devam edebilirim.)

```
```

<br>


## Skipping Jobs

`Skip` middleware’ı, işin kendi mantığını değiştirmeden bir job’un atlanmasını veya silinmesini sağlar.  
`Skip::when` metodu, verilen koşul **true** dönerse işi siler;  
`Skip::unless` metodu ise koşul **false** dönerse işi siler:

```php
use Illuminate\Queue\Middleware\Skip;

/**
 * Get the middleware the job should pass through.
 */
public function middleware(): array
{
    return [
        Skip::when($condition),
    ];
}
````

Daha karmaşık koşullar için `when` ve `unless` metodlarına bir Closure (kapanış) da geçebilirsiniz:

```php
use Illuminate\Queue\Middleware\Skip;

/**
 * Get the middleware the job should pass through.
 */
public function middleware(): array
{
    return [
        Skip::when(function (): bool {
            return $this->shouldSkip();
        }),
    ];
}
```

<br>


## Dispatching Jobs

Bir job sınıfı oluşturduktan sonra, onu `dispatch` metodu ile kuyruklayabilirsiniz.
`dispatch` metoduna geçirilen argümanlar, job’un constructor’ına aktarılır:

```php
<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPodcast;
use App\Models\Podcast;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    /**
     * Store a new podcast.
     */
    public function store(Request $request): RedirectResponse
    {
        $podcast = Podcast::create(/* ... */);

        // ...

        ProcessPodcast::dispatch($podcast);

        return redirect('/podcasts');
    }
}
```

Koşullu olarak job dispatch etmek istiyorsanız, `dispatchIf` ve `dispatchUnless` metodlarını kullanabilirsiniz:

```php
ProcessPodcast::dispatchIf($accountActive, $podcast);

ProcessPodcast::dispatchUnless($accountSuspended, $podcast);
```

Yeni Laravel uygulamalarında, varsayılan kuyruk sürücüsü **database** sürücüsüdür.
Farklı bir sürücü belirtmek için `config/queue.php` dosyasını düzenleyebilirsiniz.

<br>


## Delayed Dispatching

Bir job’un hemen işlenmemesini istiyorsanız, `delay` metodunu kullanabilirsiniz.
Örneğin, job’un 10 dakika sonra işlenmesini belirtelim:

```php
<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPodcast;
use App\Models\Podcast;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $podcast = Podcast::create(/* ... */);

        ProcessPodcast::dispatch($podcast)
            ->delay(now()->addMinutes(10));

        return redirect('/podcasts');
    }
}
```

Bazı job’larda varsayılan bir gecikme süresi tanımlı olabilir.
Bu gecikmeyi atlamak için `withoutDelay` metodunu kullanabilirsiniz:

```php
ProcessPodcast::dispatch($podcast)->withoutDelay();
```

> **Not:** Amazon SQS, maksimum 15 dakikalık bir gecikme süresini destekler.

<br>


## Dispatching After the Response is Sent to the Browser

`dispatchAfterResponse` metodu, web sunucunuz FastCGI kullanıyorsa, bir job’un HTTP yanıtı gönderildikten sonra dispatch edilmesini sağlar.
Bu, kullanıcıya uygulamayı hemen kullanma olanağı tanır.
Bu yöntem genellikle **e-posta gönderimi** gibi kısa (1 saniyelik) işlemler için uygundur.

```php
use App\Jobs\SendNotification;

SendNotification::dispatchAfterResponse();
```

Bir Closure’ı da dispatch edip, `afterResponse` metodunu zincirleyerek kullanabilirsiniz:

```php
use App\Mail\WelcomeMessage;
use Illuminate\Support\Facades\Mail;

dispatch(function () {
    Mail::to('taylor@example.com')->send(new WelcomeMessage);
})->afterResponse();
```

<br>


## Synchronous Dispatching

Bir job’u **anında (senkron)** çalıştırmak isterseniz, `dispatchSync` metodunu kullanabilirsiniz.
Bu durumda job kuyruklanmaz, mevcut işlemde hemen çalıştırılır:

```php
<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPodcast;
use App\Models\Podcast;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $podcast = Podcast::create(/* ... */);

        ProcessPodcast::dispatchSync($podcast);

        return redirect('/podcasts');
    }
}
```

<br>


## Jobs & Database Transactions

Bir veritabanı transaction’ı içinde job dispatch etmek mümkündür; ancak dikkat edilmelidir.
Çünkü job, transaction commit edilmeden önce çalıştırılabilir.
Bu durumda, veritabanına yapılan değişiklikler henüz görünmez olabilir.

Bu sorunu çözmek için, kuyruk bağlantı yapılandırmasında `after_commit` seçeneğini etkinleştirebilirsiniz:

```php
'redis' => [
    'driver' => 'redis',
    // ...
    'after_commit' => true,
],
```

`after_commit` **true** olduğunda, job transaction commit edildikten sonra çalıştırılır.
Eğer transaction rollback olursa, job **iptal edilir**.

Bu ayar ayrıca kuyruklu event listener’lar, mail, notification ve broadcast olayları için de geçerlidir.

<br>


### Specifying Commit Dispatch Behavior Inline

Eğer `after_commit` global olarak etkin değilse, belirli bir job için `afterCommit` metodunu kullanabilirsiniz:

```php
use App\Jobs\ProcessPodcast;

ProcessPodcast::dispatch($podcast)->afterCommit();
```

Tam tersi şekilde, `after_commit` aktifken job’u hemen göndermek isterseniz:

```php
ProcessPodcast::dispatch($podcast)->beforeCommit();
```

<br>


## Job Chaining

**Job chaining**, bir job tamamlandığında sırayla diğer job’ların çalıştırılmasını sağlar.
Eğer zincirdeki bir job başarısız olursa, kalan job’lar çalışmaz.

```php
use App\Jobs\OptimizePodcast;
use App\Jobs\ProcessPodcast;
use App\Jobs\ReleasePodcast;
use Illuminate\Support\Facades\Bus;

Bus::chain([
    new ProcessPodcast,
    new OptimizePodcast,
    new ReleasePodcast,
])->dispatch();
```

Closure’lar da zincire eklenebilir:

```php
Bus::chain([
    new ProcessPodcast,
    new OptimizePodcast,
    function () {
        Podcast::update(/* ... */);
    },
])->dispatch();
```

> `$this->delete()` metodunu çağırmak, zincirin devam etmesini engellemez.
> Zincir, yalnızca bir job başarısız olursa durur.

<br>


### Chain Connection and Queue

Zincir içindeki job’lar için kullanılacak **connection** ve **queue**’yu belirtebilirsiniz:

```php
Bus::chain([
    new ProcessPodcast,
    new OptimizePodcast,
    new ReleasePodcast,
])->onConnection('redis')->onQueue('podcasts')->dispatch();
```

<br>


### Adding Jobs to the Chain

Bir zincir içindeki job’dan, zincire başka job’lar ekleyebilirsiniz:

```php
public function handle(): void
{
    $this->prependToChain(new TranscribePodcast); // Hemen sonraya ekle
    $this->appendToChain(new TranscribePodcast);  // Zincirin sonuna ekle
}
```

<br>


### Chain Failures

Zincirdeki bir job başarısız olduğunda tetiklenecek bir callback tanımlayabilirsiniz:

```php
use Illuminate\Support\Facades\Bus;
use Throwable;

Bus::chain([
    new ProcessPodcast,
    new OptimizePodcast,
    new ReleasePodcast,
])->catch(function (Throwable $e) {
    // Bir job başarısız oldu...
})->dispatch();
```

> Zincir callback’leri serialize edilip daha sonra çalıştırılır, bu yüzden `$this` kullanılmamalıdır.

<br>


## Customizing the Queue and Connection

### Dispatching to a Particular Queue

İşleri farklı kuyruklara göndererek önceliklendirme yapabilirsiniz:

```php
ProcessPodcast::dispatch($podcast)->onQueue('processing');
```

Ayrıca `onQueue` metodunu job’un constructor’ında da kullanabilirsiniz:

```php
public function __construct()
{
    $this->onQueue('processing');
}
```

### Dispatching to a Particular Connection

Uygulamanız birden fazla kuyruk bağlantısı kullanıyorsa:

```php
ProcessPodcast::dispatch($podcast)->onConnection('sqs');
```

Her iki metodu zincirleyebilirsiniz:

```php
ProcessPodcast::dispatch($podcast)
    ->onConnection('sqs')
    ->onQueue('processing');
```

<br>


## Specifying Max Job Attempts / Timeout Values

### Max Attempts

Bir job’un kaç kez denenebileceğini belirlemek için `tries` kullanılır.
Varsayılan olarak job yalnızca **bir kez** denenir.

```bash
php artisan queue:work --tries=3
```

Job sınıfında manuel olarak da tanımlanabilir:

```php
public $tries = 5;
```

Ya da dinamik olarak bir metotla:

```php
public function tries(): int
{
    return 5;
}
```

### Time Based Attempts

Kaç kez değil, **ne kadar süre boyunca** denenebileceğini `retryUntil` ile belirtebilirsiniz:

```php
use DateTime;

public function retryUntil(): DateTime
{
    return now()->addMinutes(10);
}
```

### Max Exceptions

Bazı durumlarda job’un belirli sayıda **exception** sonrası başarısız olmasını isteyebilirsiniz:

```php
public $tries = 25;
public $maxExceptions = 3;
```

### Timeout

Bir job’un azami çalışma süresini saniye cinsinden belirleyebilirsiniz:

```php
public $timeout = 120;
```

```bash
php artisan queue:work --timeout=30
```

> PCNTL PHP uzantısı gereklidir.
> `timeout` değeri her zaman `retry_after` değerinden küçük olmalıdır.

Timeout’ta job’un başarısız sayılmasını isterseniz:

```php
public $failOnTimeout = true;
```

<br>


## SQS FIFO and Fair Queues

Amazon SQS **FIFO** kuyrukları, işlerin tam sırayla işlenmesini sağlar.
`onGroup` metodu ile mesaj grubu belirtebilirsiniz:

```php
ProcessOrder::dispatch($order)
    ->onGroup("customer-{$order->customer_id}");
```

Mesaj tekilleştirmesi (deduplication) için `deduplicationId` metodu tanımlanabilir:

```php
public function deduplicationId(): string
{
    return "renewal-{$this->subscription->id}";
}
```

Aynı şekilde mail, notification ve listener’larda da `messageGroup` ve `deduplicationId` tanımlanabilir.

<br>


## Queue Failover

`failover` sürücüsü, birincil kuyruk bağlantısı başarısız olursa, işi sıradaki diğer bağlantıya gönderir.
Yüksek kullanılabilirlik için idealdir.

```php
'failover' => [
    'driver' => 'failover',
    'connections' => [
        'database',
        'sync',
    ],
],
```

`.env` dosyasında:

```
QUEUE_CONNECTION=failover
```

Başarısız olduğunda `Illuminate\Queue\Events\QueueFailedOver` olayı tetiklenir.

<br>


## Error Handling

Bir job çalışırken exception oluşursa, job otomatik olarak kuyruğa geri bırakılır ve tekrar denenir.
Deneme sayısı `--tries` seçeneği veya job üzerindeki `$tries` özelliğiyle belirlenir.

<br>


### Manually Releasing a Job

Job’u manuel olarak kuyruğa geri göndermek için `release` metodunu kullanın:

```php
$this->release(); // hemen
$this->release(10); // 10 saniye sonra
$this->release(now()->addSeconds(10)); // tarih olarak
```

### Manually Failing a Job

Bir job’u manuel olarak başarısız işaretlemek için:

```php
$this->fail();
$this->fail($exception);
$this->fail('Something went wrong.');
```

<br>


## Failing Jobs on Specific Exceptions

`FailOnException` middleware’ı, belirli hatalar oluştuğunda job’un tekrar denenmesini engeller:

```php
use Illuminate\Queue\Middleware\FailOnException;
use Illuminate\Auth\Access\AuthorizationException;

public function middleware(): array
{
    return [
        new FailOnException([AuthorizationException::class])
    ];
}
```

<br>


## Job Batching

**Job batching**, bir grup job’u aynı anda çalıştırmanıza ve tamamlandığında işlem yapmanıza olanak tanır.
Öncelikle batch bilgilerini tutacak tabloyu oluşturun:

```bash
php artisan make:queue-batches-table
php artisan migrate
```

Job sınıfında `Batchable` trait kullanılmalıdır:

```php
use Illuminate\Bus\Batchable;

class ImportCsv implements ShouldQueue
{
    use Batchable, Queueable;

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        // Import işlemi...
    }
}
```

Batch’i `Bus::batch` ile dispatch edebilirsiniz:

```php
use App\Jobs\ImportCsv;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

$batch = Bus::batch([
    new ImportCsv(1, 100),
    new ImportCsv(101, 200),
])
->then(fn (Batch $batch) => /* tamamlandı */)
->catch(fn (Batch $batch, Throwable $e) => /* hata */)
->finally(fn (Batch $batch) => /* bitti */)
->dispatch();

return $batch->id;
```

> Batch callback’lerinde `$this` kullanılmamalıdır.
> Ayrıca batch job’ları transaction içinde çalıştığından, **implicit commit** tetikleyen sorgular kullanılmamalıdır.

```
```

<br>


## Naming Batches

Laravel Horizon ve Laravel Telescope gibi araçlar, batch’lere isim verilmişse daha kullanıcı dostu hata ayıklama bilgileri sunabilir.  
Bir batch’e isim vermek için `name` metodunu kullanabilirsiniz:

```php
$batch = Bus::batch([
    // ...
])->then(function (Batch $batch) {
    // Tüm işler başarıyla tamamlandı...
})->name('Import CSV')->dispatch();
````

<br>


## Batch Connection and Queue

Batch içindeki job’ların hangi connection ve queue üzerinde çalışacağını belirlemek için `onConnection` ve `onQueue` metodlarını kullanabilirsiniz.
Tüm batched job’lar aynı connection ve queue üzerinde çalışmalıdır:

```php
$batch = Bus::batch([
    // ...
])->then(function (Batch $batch) {
    // Tüm işler başarıyla tamamlandı...
})->onConnection('redis')->onQueue('imports')->dispatch();
```

<br>


## Chains and Batches

Bir batch içinde zincirlenmiş (chained) job’lar tanımlayabilirsiniz.
Aşağıdaki örnekte iki farklı job zinciri paralel olarak çalıştırılır ve ikisi de tamamlandığında callback çalışır:

```php
use App\Jobs\ReleasePodcast;
use App\Jobs\SendPodcastReleaseNotification;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

Bus::batch([
    [
        new ReleasePodcast(1),
        new SendPodcastReleaseNotification(1),
    ],
    [
        new ReleasePodcast(2),
        new SendPodcastReleaseNotification(2),
    ],
])->then(function (Batch $batch) {
    // Tüm işler başarıyla tamamlandı...
})->dispatch();
```

Tersine, bir **chain** içinde **batch** tanımlayarak, örneğin önce yayın job’larını sonra bildirim job’larını çalıştırabilirsiniz:

```php
use App\Jobs\FlushPodcastCache;
use App\Jobs\ReleasePodcast;
use App\Jobs\SendPodcastReleaseNotification;
use Illuminate\Support\Facades\Bus;

Bus::chain([
    new FlushPodcastCache,
    Bus::batch([
        new ReleasePodcast(1),
        new ReleasePodcast(2),
    ]),
    Bus::batch([
        new SendPodcastReleaseNotification(1),
        new SendPodcastReleaseNotification(2),
    ]),
])->dispatch();
```

<br>


## Adding Jobs to Batches

Bazı durumlarda, bir batch içindeki job’dan batch’e yeni job’lar eklemek faydalı olabilir.
Örneğin, binlerce job’u web isteği sırasında dispatch etmek yerine, “yükleyici” job’lar oluşturup batch’i aşamalı olarak doldurabilirsiniz:

```php
$batch = Bus::batch([
    new LoadImportBatch,
    new LoadImportBatch,
    new LoadImportBatch,
])->then(function (Batch $batch) {
    // Tüm işler başarıyla tamamlandı...
})->name('Import Contacts')->dispatch();
```

Bu durumda, `LoadImportBatch` job’u kendi batch’ine yeni job’lar ekleyebilir:

```php
use App\Jobs\ImportContacts;
use Illuminate\Support\Collection;

/**
 * Execute the job.
 */
public function handle(): void
{
    if ($this->batch()->cancelled()) {
        return;
    }

    $this->batch()->add(Collection::times(1000, function () {
        return new ImportContacts;
    }));
}
```

> Job’lar yalnızca aynı batch içinden batch’e eklenebilir.

<br>


## Inspecting Batches

`Illuminate\Bus\Batch` örneği, batch hakkında çeşitli bilgiler sağlar:

```php
$batch->id;             // Batch’in UUID’si
$batch->name;           // Batch ismi (varsa)
$batch->totalJobs;      // Toplam job sayısı
$batch->pendingJobs;    // Henüz işlenmemiş job sayısı
$batch->failedJobs;     // Başarısız job sayısı
$batch->processedJobs(); // İşlenen job sayısı
$batch->progress();     // Tamamlanma yüzdesi (0–100)
$batch->finished();     // Batch tamamlandı mı?
$batch->cancel();       // Batch’i iptal et
$batch->cancelled();    // Batch iptal edildi mi?
```

<br>


## Returning Batches From Routes

`Illuminate\Bus\Batch` nesneleri **JSON olarak döndürülebilir**, bu da batch ilerleme durumunu kolayca göstermenizi sağlar.
Batch ID’si ile batch’i bulmak için `Bus::findBatch` metodunu kullanın:

```php
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Route;

Route::get('/batch/{batchId}', function (string $batchId) {
    return Bus::findBatch($batchId);
});
```

<br>


## Cancelling Batches

Bir batch’in çalışmasını iptal etmek için `cancel` metodunu kullanabilirsiniz:

```php
public function handle(): void
{
    if ($this->user->exceedsImportLimit()) {
        $this->batch()->cancel();
        return;
    }

    if ($this->batch()->cancelled()) {
        return;
    }
}
```

Alternatif olarak, `SkipIfBatchCancelled` middleware’ını kullanabilirsiniz.
Bu middleware, batch iptal edildiyse job’un işlenmesini engeller:

```php
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

public function middleware(): array
{
    return [new SkipIfBatchCancelled];
}
```

<br>


## Batch Failures

Bir batch içindeki job başarısız olduğunda, `catch` callback’i (tanımlanmışsa) çalıştırılır.
Bu callback yalnızca **ilk** başarısız job için çağrılır.

### Allowing Failures

Varsayılan olarak, batch’teki bir job başarısız olursa, batch “cancelled” olarak işaretlenir.
Bu davranışı devre dışı bırakmak için `allowFailures` metodunu kullanabilirsiniz:

```php
$batch = Bus::batch([
    // ...
])->then(function (Batch $batch) {
    // Tüm işler başarıyla tamamlandı...
})->allowFailures()->dispatch();
```

İsterseniz `allowFailures` metoduna bir Closure da geçebilirsiniz:

```php
$batch = Bus::batch([
    // ...
])->allowFailures(function (Batch $batch, $exception) {
    // Her job hatasında çalışır...
})->dispatch();
```

<br>


## Retrying Failed Batch Jobs

Belirli bir batch’in başarısız job’larını yeniden denemek için `queue:retry-batch` komutunu kullanabilirsiniz:

```bash
php artisan queue:retry-batch 32dbc76c-4f82-4749-b610-a639fe0099b5
```

<br>


## Pruning Batches

`job_batches` tablosu hızla büyüyebilir. Bu yüzden, `queue:prune-batches` komutunu günlük olarak çalıştırmanız önerilir:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:prune-batches')->daily();
```

Varsayılan olarak, 24 saatten eski batch’ler silinir.
Saklama süresini `--hours` ile belirleyebilirsiniz:

```php
Schedule::command('queue:prune-batches --hours=48')->daily();
```

Başarısız veya iptal edilmiş batch’leri de temizlemek için `--unfinished` veya `--cancelled` parametrelerini kullanın:

```php
Schedule::command('queue:prune-batches --hours=48 --unfinished=72')->daily();
Schedule::command('queue:prune-batches --hours=48 --cancelled=72')->daily();
```

<br>


## Storing Batches in DynamoDB

Batch meta verilerini **DynamoDB**’de saklamak da mümkündür.
Bunun için `job_batches` adında bir DynamoDB tablosu oluşturun.

### DynamoDB Batch Table Configuration

Tablo aşağıdaki anahtarlara sahip olmalıdır:

* **Partition key:** `application` (string)
* **Sort key:** `id` (string)

İsteğe bağlı olarak `ttl` özniteliğini ekleyerek otomatik temizlik sağlayabilirsiniz.

### DynamoDB Configuration

Öncelikle AWS SDK’yı yükleyin:

```bash
composer require aws/aws-sdk-php
```

Ardından `config/queue.php` dosyasında `queue.batching.driver` değerini `dynamodb` olarak ayarlayın:

```php
'batching' => [
    'driver' => env('QUEUE_BATCHING_DRIVER', 'dynamodb'),
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'table' => 'job_batches',
],
```

### Pruning Batches in DynamoDB

DynamoDB’de saklanan batch’ler için Laravel’in normal prune komutları çalışmaz.
Bunun yerine DynamoDB’nin **TTL** özelliğini kullanabilirsiniz.

```php
'batching' => [
    'driver' => env('QUEUE_FAILED_DRIVER', 'dynamodb'),
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'table' => 'job_batches',
    'ttl_attribute' => 'ttl',
    'ttl' => 60 * 60 * 24 * 7, // 7 gün...
],
```

<br>


## Queueing Closures

Bir job sınıfı yerine doğrudan bir **Closure** da kuyruklayabilirsiniz.
Closure içeriği güvenlik için imzalanır:

```php
use App\Models\Podcast;

$podcast = Podcast::find(1);

dispatch(function () use ($podcast) {
    $podcast->publish();
});
```

Closure’a bir isim atamak için `name` metodunu kullanabilirsiniz:

```php
dispatch(function () {
    // ...
})->name('Publish Podcast');
```

Başarısız durumda çalışacak bir callback tanımlamak için `catch` metodunu kullanın:

```php
use Throwable;

dispatch(function () use ($podcast) {
    $podcast->publish();
})->catch(function (Throwable $e) {
    // Bu job başarısız oldu...
});
```

> `catch` callback’lerinde `$this` değişkeni kullanılmamalıdır.

<br>


## Running the Queue Worker

### The queue:work Command

Kuyruk işleyicisini başlatmak için:

```bash
php artisan queue:work
```

İşleyiciyi arka planda sürekli çalıştırmak için **Supervisor** gibi bir process manager kullanın.
Daha ayrıntılı çıktı için `-v` ekleyebilirsiniz:

```bash
php artisan queue:work -v
```

Kod değişiklikleri sonrasında worker’ları yeniden başlatmayı unutmayın:

```bash
php artisan queue:restart
```

Alternatif olarak, **queue:listen** komutu kod değişikliklerini otomatik algılar ancak daha yavaştır:

```bash
php artisan queue:listen
```

<br>


### Running Multiple Queue Workers

Aynı anda birden fazla worker çalıştırarak job’ları paralel işleyebilirsiniz.
Supervisor’da `numprocs` değeri ile bu sayıyı ayarlayabilirsiniz.

<br>


### Specifying the Connection and Queue

```bash
php artisan queue:work redis
php artisan queue:work redis --queue=emails
```

<br>


### Processing a Specified Number of Jobs

Bir worker’ın yalnızca belirli sayıda job işlemesini isterseniz:

```bash
php artisan queue:work --max-jobs=1000
php artisan queue:work --once
```

Tüm job’lar bittikten sonra çıkmak için:

```bash
php artisan queue:work --stop-when-empty
```

Belirli bir süre çalışıp çıkması için:

```bash
php artisan queue:work --max-time=3600
```

Boşta bekleme süresini belirlemek için:

```bash
php artisan queue:work --sleep=3
```

<br>


### Maintenance Mode and Queues

Bakım modundayken job’lar işlenmez.
Zorlamak için `--force` kullanabilirsiniz:

```bash
php artisan queue:work --force
```

<br>


### Queue Priorities

Bazı job’lara öncelik vermek için:

```php
dispatch((new Job)->onQueue('high'));
```

```bash
php artisan queue:work --queue=high,low
```

<br>


### Queue Workers and Deployment

Worker’lar uzun süreli süreçlerdir; kod değişikliklerinden haberdar olmazlar.
Dağıtım sırasında yeniden başlatılmaları gerekir:

```bash
php artisan queue:restart
```

Bu komut, çalışan job’ları kaybetmeden worker’ları yeniden başlatır.

<br>


## Job Expirations and Timeouts

### Job Expiration

`config/queue.php` dosyasındaki `retry_after` değeri, bir job’un en fazla kaç saniye çalışabileceğini belirtir.

```php
'retry_after' => 90,
```

SQS bu değeri AWS konsolundaki **Visibility Timeout** üzerinden yönetir.

<br>


### Worker Timeouts

`queue:work` komutunda `--timeout` parametresi bulunur:

```bash
php artisan queue:work --timeout=60
```

> `--timeout` değeri, `retry_after` değerinden birkaç saniye **daha kısa** olmalıdır.

<br>


## Supervisor Configuration

### Installing Supervisor

Ubuntu’da kurulum:

```bash
sudo apt-get install supervisor
```

<br>


### Configuring Supervisor

`/etc/supervisor/conf.d/laravel-worker.conf` dosyasını oluşturun:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/forge/app.com/artisan queue:work sqs --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=forge
numprocs=8
redirect_stderr=true
stdout_logfile=/home/forge/app.com/worker.log
stopwaitsecs=3600
```

Ardından Supervisor’ı yeniden yükleyip başlatın:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start "laravel-worker:*"
```

<br>


## Dealing With Failed Jobs

Job’lar başarısız olduğunda, Laravel bunları `failed_jobs` tablosuna kaydeder.
Migration yoksa oluşturmak için:

```bash
php artisan make:queue-failed-table
php artisan migrate
```

Worker çalıştırırken maksimum deneme sayısını belirlemek için:

```bash
php artisan queue:work redis --tries=3
```

Geri deneme süresini belirlemek için:

```bash
php artisan queue:work redis --tries=3 --backoff=3
```

Job içinde özel `backoff` tanımlayabilirsiniz:

```php
public $backoff = 3;

public function backoff(): array
{
    return [1, 5, 10];
}
```

<br>


### Cleaning Up After Failed Jobs

Bir job başarısız olduğunda yapılacak işlemleri `failed` metodunda tanımlayabilirsiniz:

```php
public function failed(?Throwable $exception): void
{
    // Kullanıcıya bildirim gönder, logla vs...
}
```

> `failed` metodu çağrıldığında job yeniden instantiate edilir.

<br>


### Retrying Failed Jobs

Başarısız job’ları listelemek:

```bash
php artisan queue:failed
```

Tek bir job’u yeniden denemek:

```bash
php artisan queue:retry <job-id>
```

Tüm job’ları yeniden denemek:

```bash
php artisan queue:retry all
```

Job’ları silmek:

```bash
php artisan queue:forget <job-id>
php artisan queue:flush
```

<br>


### Ignoring Missing Models

Job kuyruktayken ilişkili model silinmişse, hata oluşabilir.
Bunu önlemek için:

```php
public $deleteWhenMissingModels = true;
```

<br>


### Pruning Failed Jobs

Eski başarısız job kayıtlarını silmek için:

```bash
php artisan queue:prune-failed --hours=48
```

<br>


## Storing Failed Jobs in DynamoDB

Başarısız job’ları DynamoDB’de saklamak da mümkündür.

Tablo anahtarları:

* **Partition key:** `application`
* **Sort key:** `uuid`

AWS SDK kurulumu:

```bash
composer require aws/aws-sdk-php
```

`config/queue.php` ayarları:

```php
'failed' => [
    'driver' => env('QUEUE_FAILED_DRIVER', 'dynamodb'),
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'table' => 'failed_jobs',
],
```

<br>


### Disabling Failed Job Storage

Başarısız job’ların hiç saklanmamasını istiyorsanız:

```
QUEUE_FAILED_DRIVER=null
```

<br>


### Failed Job Events

Job başarısız olduğunda olay dinleyicisi eklemek için:

```php
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;

Queue::failing(function (JobFailed $event) {
    // $event->connectionName
    // $event->job
    // $event->exception
});
```


<br>


## Clearing Jobs From Queues

Horizon kullanıyorsanız, kuyruktaki job’ları temizlemek için `queue:clear` yerine `horizon:clear` komutunu kullanmalısınız.

Varsayılan connection’daki varsayılan kuyruğu temizlemek için:

```bash
php artisan queue:clear
````

Belirli bir connection ve queue’yu temizlemek isterseniz:

```bash
php artisan queue:clear redis --queue=emails
```

Job’ları kuyruklardan temizleme özelliği yalnızca **SQS**, **Redis** ve **database** queue sürücüleri için geçerlidir.
SQS için mesaj silme işlemi 60 saniyeye kadar sürebileceği için, bu süre içinde gönderilen job’lar da silinebilir.

<br>


## Monitoring Your Queues

Kuyruğunuza aniden çok fazla job gelirse, kuyruk işleyiciniz bunaltılabilir ve job’ların tamamlanması uzun sürebilir.
Laravel, kuyruk boyutu belirlediğiniz eşiği aştığında sizi uyarabilir.

Başlamak için, `queue:monitor` komutunu her dakika çalışacak şekilde zamanlamalısınız.
Bu komut, izlemek istediğiniz kuyrukların adlarını ve job sayısı eşiğini alır:

```bash
php artisan queue:monitor redis:default,redis:deployments --max=100
```

Bu komutu zamanlamak, bildirim oluşturmak için tek başına yeterli değildir.
Kuyruk boyutu belirtilen eşiği aştığında, `Illuminate\Queue\Events\QueueBusy` olayı tetiklenir.
Bu olayı dinleyerek kendinize veya ekibinize bildirim gönderebilirsiniz:

```php
use App\Notifications\QueueHasLongWaitTime;
use Illuminate\Queue\Events\QueueBusy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Event::listen(function (QueueBusy $event) {
        Notification::route('mail', 'dev@example.com')
            ->notify(new QueueHasLongWaitTime(
                $event->connection,
                $event->queue,
                $event->size
            ));
    });
}
```

<br>


## Testing

Kodunuz job dispatch ediyorsa, test sırasında bu job’ların gerçekten çalışmasını istemeyebilirsiniz.
Job’un kodunu ayrı olarak test edebilir, dispatch işlemini ise sahte (fake) kuyruk üzerinden doğrulayabilirsiniz.

`Queue` facade’ının `fake` metodunu kullanarak job’ların gerçekten kuyruğa eklenmesini engelleyebilirsiniz.
Ardından job’ların kuyruğa eklenip eklenmediğini assert edebilirsiniz.

### Örnek (Pest / PHPUnit)

```php
use App\Jobs\AnotherJob;
use App\Jobs\ShipOrder;
use Illuminate\Support\Facades\Queue;

test('orders can be shipped', function () {
    Queue::fake();

    // Sipariş gönderimini gerçekleştir...

    // Hiç job eklenmediğini doğrula...
    Queue::assertNothingPushed();

    // Belirli bir kuyruğa job eklendiğini doğrula...
    Queue::assertPushedOn('queue-name', ShipOrder::class);

    // Job’un iki kez eklendiğini doğrula...
    Queue::assertPushed(ShipOrder::class, 2);

    // Belirli bir job’un eklenmediğini doğrula...
    Queue::assertNotPushed(AnotherJob::class);

    // Closure job’un eklendiğini doğrula...
    Queue::assertClosurePushed();

    // Closure job’un eklenmediğini doğrula...
    Queue::assertClosureNotPushed();

    // Toplam job sayısını doğrula...
    Queue::assertCount(3);
});
```

`assertPushed`, `assertNotPushed`, `assertClosurePushed` veya `assertClosureNotPushed` metodlarına Closure geçerek job’un belirli bir koşulu sağladığını doğrulayabilirsiniz:

```php
use Illuminate\Queue\CallQueuedClosure;

Queue::assertPushed(function (ShipOrder $job) use ($order) {
    return $job->order->id === $order->id;
});

Queue::assertClosurePushed(function (CallQueuedClosure $job) {
    return $job->name === 'validate-order';
});
```

<br>


## Faking a Subset of Jobs

Sadece belirli job’ları sahte hale getirmek istiyorsanız, `fake` metoduna bu job’ların sınıf adlarını geçebilirsiniz:

```php
Queue::fake([
    ShipOrder::class,
]);
```

Tersine, belirli job’lar hariç diğer tüm job’ları sahte hale getirmek için `except` metodunu kullanabilirsiniz:

```php
Queue::fake()->except([
    ShipOrder::class,
]);
```

<br>


## Testing Job Chains

Job zincirlerini (chains) test etmek için `Bus` facade’ının `fake` ve `assertChained` metodlarını kullanabilirsiniz:

```php
use App\Jobs\RecordShipment;
use App\Jobs\ShipOrder;
use App\Jobs\UpdateInventory;
use Illuminate\Support\Facades\Bus;

Bus::fake();

// ...

Bus::assertChained([
    ShipOrder::class,
    RecordShipment::class,
    UpdateInventory::class
]);
```

Job örnekleriyle de test yapabilirsiniz:

```php
Bus::assertChained([
    new ShipOrder,
    new RecordShipment,
    new UpdateInventory,
]);
```

Bir job’un zincirsiz (chain olmadan) dispatch edildiğini doğrulamak için:

```php
Bus::assertDispatchedWithoutChain(ShipOrder::class);
```

<br>


## Testing Chain Modifications

Bir job zincire yeni job ekliyor veya prepend ediyorsa, `assertHasChain` metoduyla zincirin beklenen durumda olduğunu doğrulayabilirsiniz:

```php
$job = new ProcessPodcast;

$job->handle();

$job->assertHasChain([
    new TranscribePodcast,
    new OptimizePodcast,
    new ReleasePodcast,
]);
```

Zincirin boş olduğunu doğrulamak için:

```php
$job->assertDoesntHaveChain();
```

<br>


## Testing Chained Batches

Bir zincir içinde batch varsa, `Bus::chainedBatch` ile batch’in beklenen şekilde oluşturulduğunu doğrulayabilirsiniz:

```php
use App\Jobs\ShipOrder;
use App\Jobs\UpdateInventory;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;

Bus::assertChained([
    new ShipOrder,
    Bus::chainedBatch(function (PendingBatch $batch) {
        return $batch->jobs->count() === 3;
    }),
    new UpdateInventory,
]);
```

<br>


## Testing Job Batches

`Bus::assertBatched` metodu, belirli bir batch’in dispatch edildiğini doğrular:

```php
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;

Bus::fake();

// ...

Bus::assertBatched(function (PendingBatch $batch) {
    return $batch->name == 'Import CSV' &&
           $batch->jobs->count() === 10;
});
```

Diğer yardımcı doğrulama metodları:

```php
Bus::assertBatchCount(3);
Bus::assertNothingBatched();
```

<br>


## Testing Job / Batch Interaction

Bir job’un batch ile etkileşimini test etmek için `withFakeBatch` metodunu kullanabilirsiniz:

```php
[$job, $batch] = (new ShipOrder)->withFakeBatch();

$job->handle();

$this->assertTrue($batch->cancelled());
$this->assertEmpty($batch->added);
```

<br>


## Testing Job / Queue Interactions

Bir job’un kendi kendini yeniden kuyruğa almasını veya silmesini test etmek için `withFakeQueueInteractions` metodunu kullanabilirsiniz:

```php
use App\Exceptions\CorruptedAudioException;
use App\Jobs\ProcessPodcast;

$job = (new ProcessPodcast)->withFakeQueueInteractions();

$job->handle();

$job->assertReleased(delay: 30);
$job->assertDeleted();
$job->assertNotDeleted();
$job->assertFailed();
$job->assertFailedWith(CorruptedAudioException::class);
$job->assertNotFailed();
```

<br>


## Job Events

`Queue` facade’ı, job işlenmeden önce veya sonra çalışacak callback’ler tanımlamanıza izin verir.
Bunlar genellikle loglama veya metrik toplama için kullanılır.
Bu işlemleri genellikle bir service provider’ın `boot` metodunda tanımlarsınız:

```php
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

Queue::before(function (JobProcessing $event) {
    // $event->connectionName
    // $event->job
    // $event->job->payload()
});

Queue::after(function (JobProcessed $event) {
    // $event->connectionName
    // $event->job
    // $event->job->payload()
});
```

Ayrıca, `looping` metodu, worker bir job’ı almadan önce çalışacak callback tanımlamanızı sağlar.
Bu, örneğin başarısız bir job’tan kalan açık transaction’ları geri almak için kullanılabilir:

```php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

Queue::looping(function () {
    while (DB::transactionLevel() > 0) {
        DB::rollBack();
    }
});
```

<br>


Laravel, yazılım geliştirme, dağıtım ve izleme süreçlerini en verimli şekilde yönetmenin yoludur.
```
