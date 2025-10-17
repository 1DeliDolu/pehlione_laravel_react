
<br>




## Cache

<br>




### Introduction

Uygulamanız tarafından gerçekleştirilen bazı veri alma veya işleme görevleri CPU açısından yoğun olabilir veya tamamlanması birkaç saniye sürebilir. Böyle durumlarda, alınan veriyi belirli bir süre için cache'lemek yaygın bir yaklaşımdır, böylece aynı veriye yapılan sonraki isteklerde hızlıca erişilebilir. Cache'lenen veriler genellikle Memcached veya Redis gibi çok hızlı veri depolama sistemlerinde saklanır.

Neyse ki, Laravel çeşitli cache backend'leri için ifade gücü yüksek ve birleşik bir API sağlar; bu sayede bu sistemlerin son derece hızlı veri erişiminden yararlanabilir ve web uygulamanızın hızını artırabilirsiniz.

<br>




### Configuration

Uygulamanızın cache yapılandırma dosyası `config/cache.php` konumunda bulunur. Bu dosyada, uygulamanız genelinde varsayılan olarak hangi cache store’un kullanılacağını belirleyebilirsiniz. Laravel, kutudan çıktığı haliyle Memcached, Redis, DynamoDB ve ilişkisel veritabanları gibi popüler cache backend'lerini destekler. Ayrıca, dosya tabanlı bir cache driver’ı da mevcuttur; `array` ve `null` cache driver’ları ise otomatik testleriniz için uygun geçici cache çözümleri sağlar.

Cache yapılandırma dosyasında gözden geçirebileceğiniz çeşitli diğer seçenekler de bulunur. Varsayılan olarak, Laravel `database` cache driver’ını kullanacak şekilde yapılandırılmıştır; bu driver, serialize edilmiş cache objelerini uygulamanızın veritabanında saklar.

<br>




### Driver Prerequisites

<br>




#### Database

`database` cache driver’ını kullanırken cache verilerini içeren bir veritabanı tablosuna ihtiyacınız olacaktır. Bu genellikle Laravel’in varsayılan `0001_01_01_000001_create_cache_table.php` migration dosyasına dahildir; ancak, uygulamanız bu migration’a sahip değilse, aşağıdaki Artisan komutu ile oluşturabilirsiniz:

```bash
php artisan make:cache-table

php artisan migrate
````

<br>




#### Memcached

`Memcached` driver’ını kullanmak için Memcached PECL paketinin kurulu olması gerekir. Tüm Memcached sunucularınızı `config/cache.php` dosyasında listeleyebilirsiniz. Bu dosya, başlangıç için zaten bir `memcached.servers` girişi içerir:

```php
'memcached' => [
    'servers' => [
        [
            'host' => env('MEMCACHED_HOST', '127.0.0.1'),
            'port' => env('MEMCACHED_PORT', 11211),
            'weight' => 100,
        ],
    ],
],
```

Gerekirse, `host` seçeneğini bir UNIX socket yoluna ayarlayabilirsiniz. Bu durumda `port` seçeneğini `0` olarak belirtmelisiniz:

```php
'memcached' => [
    'servers' => [
        [
            'host' => '/var/run/memcached/memcached.sock',
            'port' => 0,
            'weight' => 100
        ],
    ],
],
```

<br>




#### Redis

Laravel ile Redis cache kullanmadan önce, PECL üzerinden PhpRedis PHP eklentisini kurmanız veya Composer üzerinden `predis/predis` paketini (~2.0) yüklemeniz gerekir. Laravel Sail bu eklentiyi zaten içerir. Ayrıca, Laravel Cloud ve Laravel Forge gibi resmi Laravel platformlarında PhpRedis varsayılan olarak yüklüdür.

Redis’in yapılandırılması hakkında daha fazla bilgi için Laravel’in Redis dokümantasyon sayfasına bakın.

<br>




#### DynamoDB

`DynamoDB` cache driver’ını kullanmadan önce, tüm cache verilerini saklayacak bir DynamoDB tablosu oluşturmanız gerekir. Bu tablo genellikle `cache` olarak adlandırılır; ancak tablo adı, `config/cache.php` dosyasındaki `stores.dynamodb.table` yapılandırma değeriyle uyumlu olmalıdır. Tablo adı ayrıca `DYNAMODB_CACHE_TABLE` ortam değişkeni aracılığıyla da ayarlanabilir.

Bu tablonun ayrıca bir `string` türünde `partition key` alanına sahip olması gerekir ve bu alanın adı, yapılandırma dosyanızdaki `stores.dynamodb.attributes.key` değerine karşılık gelmelidir. Varsayılan olarak bu anahtarın adı `key`’dir.

DynamoDB genellikle süresi dolmuş öğeleri proaktif olarak silmez. Bu nedenle, tablo üzerinde Time to Live (TTL) özelliğini etkinleştirmelisiniz. TTL ayarlarını yapılandırırken `expires_at` alanını TTL öznitelik adı olarak ayarlayın.

Ardından, Laravel uygulamanızın DynamoDB ile iletişim kurabilmesi için AWS SDK’sını yükleyin:

```bash
composer require aws/aws-sdk-php
```

Ayrıca, DynamoDB cache store yapılandırma seçenekleri için değerlerin sağlandığından emin olun. Genellikle bu seçenekler (`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` vb.) `.env` dosyasında tanımlanmalıdır:

```php
'dynamodb' => [
    'driver' => 'dynamodb',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
    'endpoint' => env('DYNAMODB_ENDPOINT'),
],
```

<br>




#### MongoDB

Eğer MongoDB kullanıyorsanız, `mongodb/laravel-mongodb` resmi paketi tarafından sağlanan bir `mongodb` cache driver’ı mevcuttur ve bu driver, bir MongoDB veritabanı bağlantısı kullanılarak yapılandırılabilir. MongoDB, süresi dolmuş cache öğelerini otomatik olarak silmek için TTL index’lerini destekler.

MongoDB yapılandırması hakkında daha fazla bilgi için MongoDB Cache ve Locks dokümantasyonuna bakın.

<br>




### Cache Usage

<br>




#### Obtaining a Cache Instance

Bir cache store örneği almak için `Cache` facade’ını kullanabilirsiniz; bu dokümantasyon boyunca da bu şekilde yapacağız. `Cache` facade’ı, Laravel cache contract’larının temel implementasyonlarına kolay ve kısa erişim sağlar:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function index(): array
    {
        $value = Cache::get('key');

        return [
            // ...
        ];
    }
}
```

<br>




#### Accessing Multiple Cache Stores

`Cache` facade’ını kullanarak, `store` metoduna bir parametre geçerek farklı cache store’lara erişebilirsiniz. Bu parametre, `config/cache.php` dosyanızdaki `stores` dizisinde tanımlı store anahtarlarından biri olmalıdır:

```php
$value = Cache::store('file')->get('foo');

Cache::store('redis')->put('bar', 'baz', 600); // 10 dakika
```

<br>




#### Retrieving Items From the Cache

`Cache` facade’ının `get` metodu cache’ten bir öğe almak için kullanılır. Eğer öğe mevcut değilse `null` döner. İsterseniz, öğe mevcut değilse döndürülmesini istediğiniz varsayılan değeri ikinci argüman olarak belirtebilirsiniz:

```php
$value = Cache::get('key');

$value = Cache::get('key', 'default');
```

Varsayılan değer olarak bir `closure` da geçebilirsiniz. Belirtilen öğe cache’te yoksa, bu closure çalıştırılır ve sonucu döndürülür. Bu sayede varsayılan değerleri veritabanı veya başka bir dış servis üzerinden dinamik olarak çekebilirsiniz:

```php
$value = Cache::get('key', function () {
    return DB::table(/* ... */)->get();
});
```

<br>




#### Determining Item Existence

Bir öğenin cache’te mevcut olup olmadığını kontrol etmek için `has` metodunu kullanabilirsiniz. Bu metod, öğe mevcut olsa bile değeri `null` ise `false` döndürür:

```php
if (Cache::has('key')) {
    // ...
}
```

<br>




#### Incrementing / Decrementing Values

`increment` ve `decrement` metodları, cache’teki tam sayı değerlerini artırmak veya azaltmak için kullanılır. Her iki metod da artırma veya azaltma miktarını belirten isteğe bağlı ikinci bir argüman alır:

```php
Cache::add('key', 0, now()->addHours(4));

Cache::increment('key');
Cache::increment('key', $amount);
Cache::decrement('key');
Cache::decrement('key', $amount);
```

<br>




#### Retrieve and Store

Bazen cache’ten bir öğe almak, ama aynı zamanda öğe yoksa bir varsayılan değer kaydetmek isteyebilirsiniz. Örneğin, tüm kullanıcıları cache’ten almak veya yoksa veritabanından çekip cache’e eklemek isteyebilirsiniz. Bunu `Cache::remember` metodu ile yapabilirsiniz:

```php
$value = Cache::remember('users', $seconds, function () {
    return DB::table('users')->get();
});
```

Öğe cache’te yoksa, `remember` metoduna geçirilen closure çalıştırılır ve sonucu cache’e kaydedilir.

Cache öğesini süresiz olarak saklamak için `rememberForever` metodunu kullanabilirsiniz:

```php
$value = Cache::rememberForever('users', function () {
    return DB::table('users')->get();
});
```

<br>




#### Stale While Revalidate

`Cache::remember` metodunu kullanırken, bazı kullanıcılar cache süresi dolduğunda yavaş yanıt süreleriyle karşılaşabilir. Bazı veri türleri için, cache’lenmiş değer arka planda yeniden hesaplanırken kısmen eski verilerin sunulmasına izin vermek faydalıdır. Bu yaklaşım "stale-while-revalidate" deseni olarak bilinir ve `Cache::flexible` metodu bu deseni uygular.

`flexible` metodu, cache’in ne kadar süreyle "taze" sayılacağını ve ne zaman "bayat" hale geleceğini belirten bir dizi alır. Dizideki ilk değer cache’in taze olduğu süreyi, ikinci değer ise yeniden hesaplama gerekmeden önce bayat verinin ne kadar süreyle sunulabileceğini belirtir.

```php
$value = Cache::flexible('users', [5, 10], function () {
    return DB::table('users')->get();
});
```

<br>




#### Retrieve and Delete

Cache’ten bir öğeyi aldıktan sonra onu silmek isterseniz, `pull` metodunu kullanabilirsiniz. Bu metod, `get` metoduna benzer şekilde, öğe mevcut değilse `null` döndürür:

```php
$value = Cache::pull('key');

$value = Cache::pull('key', 'default');
```


<br>




## Storing Items in the Cache

<br>




### Cache’e Veri Kaydetme

Cache’e veri kaydetmek için `Cache` facade’ının `put` metodunu kullanabilirsiniz:

```php
Cache::put('key', 'value', $seconds = 10);
````

Eğer `put` metoduna saklama süresi belirtilmezse, öğe süresiz olarak saklanır:

```php
Cache::put('key', 'value');
```

Saniye sayısı yerine, öğenin süresinin dolacağı zamanı temsil eden bir `DateTime` örneği de geçebilirsiniz:

```php
Cache::put('key', 'value', now()->addMinutes(10));
```

<br>




### Store if Not Present

`add` metodu, öğe cache’te zaten yoksa ekleme işlemini yapar. Öğe başarıyla eklendiyse `true`, zaten mevcutsa `false` döner. `add` metodu atomik bir işlemdir:

```php
Cache::add('key', 'value', $seconds);
```

<br>




### Storing Items Forever

Bir öğeyi kalıcı olarak cache’e kaydetmek için `forever` metodunu kullanabilirsiniz. Bu öğeler zaman aşımına uğramaz; dolayısıyla `forget` metodu ile manuel olarak silinmeleri gerekir:

```php
Cache::forever('key', 'value');
```

Eğer `Memcached` driver’ını kullanıyorsanız, “süresiz” saklanan öğeler cache kapasitesi dolduğunda silinebilir.

<br>




### Removing Items From the Cache

Cache’ten öğeleri silmek için `forget` metodunu kullanabilirsiniz:

```php
Cache::forget('key');
```

Ayrıca, sıfır veya negatif bir süre değeri belirterek de öğeyi hemen silmeyi sağlayabilirsiniz:

```php
Cache::put('key', 'value', 0);

Cache::put('key', 'value', -5);
```

Tüm cache’i temizlemek için `flush` metodunu kullanabilirsiniz:

```php
Cache::flush();
```

> ⚠️ `flush` metodu, yapılandırılmış cache “prefix”’inizi dikkate almaz ve cache’teki **tüm** girişleri kaldırır. Bu, başka uygulamalarla paylaşılan bir cache temizlenirken dikkat edilmesi gereken bir durumdur.

<br>




### Cache Memoization

Laravel’in “memo” cache driver’ı, tek bir istek veya job çalışması süresince çözülmüş cache değerlerini bellekte geçici olarak saklamanıza olanak tanır. Bu, aynı işlem içinde tekrarlanan cache erişimlerini önleyerek performansı önemli ölçüde artırır.

Memoize edilmiş cache’i kullanmak için `memo` metodunu çağırabilirsiniz:

```php
use Illuminate\Support\Facades\Cache;

$value = Cache::memo()->get('key');
```

`memo` metodu, isteğe bağlı olarak bir cache store adı alabilir. Bu, memoize edilen driver’ın hangi cache store’u sarmalayacağını belirtir:

```php
// Varsayılan cache store...
$value = Cache::memo()->get('key');

// Redis cache store kullanımı...
$value = Cache::memo('redis')->get('key');
```

Belirli bir anahtar için yapılan ilk `get` çağrısı değeri cache’ten alır; ancak aynı istek veya job süresince yapılan sonraki çağrılar bellekteki değeri döndürür:

```php
// Cache’e erişim yapılır...
$value = Cache::memo()->get('key');

// Cache’e erişim yapılmaz, bellekteki değer döner...
$value = Cache::memo()->get('key');
```

Cache değerini değiştiren metodlar (`put`, `increment`, `remember` vb.) çağrıldığında, memoize edilmiş cache değeri otomatik olarak unutulur ve işlem gerçek cache store’a aktarılır:

```php
Cache::memo()->put('name', 'Taylor'); // Cache'e yazar...
Cache::memo()->get('name');           // Cache'ten alır...
Cache::memo()->get('name');           // Bellekten döner...

Cache::memo()->put('name', 'Tim');    // Bellekteki değeri unutur, yenisini yazar...
Cache::memo()->get('name');           // Yeniden cache'e erişir...
```

<br>




### The Cache Helper

`Cache` facade’ı yerine, global `cache` fonksiyonunu da kullanabilirsiniz. Bu fonksiyon, cache üzerinden veri almak ve saklamak için kısa bir alternatif sağlar.

`cache` fonksiyonu yalnızca bir string argümanla çağrıldığında, belirtilen anahtarın değerini döndürür:

```php
$value = cache('key');
```

Eğer bir anahtar/değer dizisi ve süre parametresi sağlarsanız, belirtilen sürede cache’e kaydeder:

```php
cache(['key' => 'value'], $seconds);

cache(['key' => 'value'], now()->addMinutes(10));
```

Argümansız çağrıldığında ise, `Illuminate\Contracts\Cache\Factory` implementasyonunun bir örneğini döndürür. Bu sayede, diğer cache metodlarını doğrudan çağırabilirsiniz:

```php
cache()->remember('users', $seconds, function () {
    return DB::table('users')->get();
});
```

Global `cache` fonksiyonunu test ederken, `Cache::shouldReceive` metodunu `facade` testlerinde olduğu gibi kullanabilirsiniz.

<br>




### Cache Tags

> `file`, `dynamodb` veya `database` cache driver’ları kullanıldığında cache etiketleri desteklenmez.

<br>




#### Storing Tagged Cache Items

Cache tag’leri, ilişkili cache öğelerini etiketlemenize ve daha sonra belirli bir etikete sahip tüm cache değerlerini topluca temizlemenize olanak tanır. Etiketlenmiş bir cache’e erişmek için `tags` metoduna bir etiket dizisi geçebilirsiniz:

```php
use Illuminate\Support\Facades\Cache;

Cache::tags(['people', 'artists'])->put('John', $john, $seconds);
Cache::tags(['people', 'authors'])->put('Anne', $anne, $seconds);
```

<br>




#### Accessing Tagged Cache Items

Etiketli olarak kaydedilen öğelere, yalnızca aynı etiketlerle erişebilirsiniz. Bir öğeyi almak için `tags` metoduna etiketleri geçin ve ardından `get` metodunu çağırın:

```php
$john = Cache::tags(['people', 'artists'])->get('John');

$anne = Cache::tags(['people', 'authors'])->get('Anne');
```

<br>




#### Removing Tagged Cache Items

Belirli bir etiket veya etiket listesine sahip tüm öğeleri temizlemek için `flush` metodunu kullanabilirsiniz. Aşağıdaki örnekte, `people` veya `authors` etiketine sahip tüm cache öğeleri silinecektir (hem Anne hem de John silinir):

```php
Cache::tags(['people', 'authors'])->flush();
```

Buna karşılık, yalnızca `authors` etiketiyle etiketlenmiş öğeleri silmek isterseniz (sadece Anne silinir):

```php
Cache::tags('authors')->flush();
```

<br>




### Atomic Locks

Bu özelliği kullanabilmek için uygulamanızın varsayılan cache driver’ı olarak `memcached`, `redis`, `dynamodb`, `database`, `file` veya `array` driver’larından birini kullanması gerekir. Ayrıca, tüm sunucuların aynı merkezi cache sunucusuyla iletişim kurması gerekir.

<br>




#### Managing Locks

Atomic lock’lar, race condition’lardan endişe etmeden dağıtılmış kilitleri yönetmenizi sağlar. Örneğin, Laravel Cloud, aynı anda yalnızca bir uzak görevin çalışmasını sağlamak için atomic lock’lar kullanır. `Cache::lock` metoduyla kilit oluşturabilir ve yönetebilirsiniz:

```php
use Illuminate\Support\Facades\Cache;

$lock = Cache::lock('foo', 10);

if ($lock->get()) {
    // 10 saniyelik kilit alındı...

    $lock->release();
}
```

`get` metodu bir `closure` da alabilir. Bu durumda closure çalıştıktan sonra Laravel kilidi otomatik olarak serbest bırakır:

```php
Cache::lock('foo', 10)->get(function () {
    // 10 saniyelik kilit alındı ve otomatik olarak serbest bırakıldı...
});
```

Kilit mevcut değilse, Laravel’in belirli bir süre boyunca beklemesini isteyebilirsiniz. Belirtilen süre içinde kilit alınamazsa, `Illuminate\Contracts\Cache\LockTimeoutException` fırlatılır:

```php
use Illuminate\Contracts\Cache\LockTimeoutException;

$lock = Cache::lock('foo', 10);

try {
    $lock->block(5);

    // En fazla 5 saniye bekledikten sonra kilit alındı...
} catch (LockTimeoutException $e) {
    // Kilit alınamadı...
} finally {
    $lock->release();
}
```

Yukarıdaki örnek, `block` metoduna bir `closure` geçilerek basitleştirilebilir. Laravel, belirtilen süre boyunca kilidi almaya çalışır ve closure çalıştıktan sonra kilidi otomatik olarak serbest bırakır:

```php
Cache::lock('foo', 10)->block(5, function () {
    // En fazla 5 saniye bekledikten sonra 10 saniyelik kilit alındı...
});
```


<br>




## Managing Locks Across Processes

<br>




### İşlemler Arasında Kilitleri Yönetme

Bazen bir işlemde bir kilit almak ve başka bir işlemde bu kilidi serbest bırakmak isteyebilirsiniz. Örneğin, bir web isteği sırasında bir kilit alıp, bu istek tarafından tetiklenen bir **queued job**’ın sonunda bu kilidi serbest bırakmak isteyebilirsiniz. Bu senaryoda, kilidin kapsamlı "owner token" değerini ilgili job’a aktarmalısınız, böylece job bu token’ı kullanarak kilidi yeniden oluşturabilir.

Aşağıdaki örnekte, bir kilit başarıyla alındığında bir **queued job** başlatıyoruz. Ayrıca, kilidin `owner()` metodu ile alınan token değerini job’a aktarıyoruz:

```php
$podcast = Podcast::find($id);

$lock = Cache::lock('processing', 120);

if ($lock->get()) {
    ProcessPodcast::dispatch($podcast, $lock->owner());
}
````

Uygulamanızın `ProcessPodcast` job’ı içinde, bu owner token’ı kullanarak kilidi yeniden oluşturabilir ve serbest bırakabilirsiniz:

```php
Cache::restoreLock('processing', $this->owner)->release();
```

Eğer bir kilidi mevcut sahibine bakmaksızın serbest bırakmak istiyorsanız, `forceRelease` metodunu kullanabilirsiniz:

```php
Cache::lock('processing')->forceRelease();
```

<br>




## Adding Custom Cache Drivers

<br>




### Writing the Driver

Özel bir cache driver’ı oluşturmak için önce `Illuminate\Contracts\Cache\Store` arayüzünü (contract) uygulamanız gerekir. Örneğin, bir **MongoDB** cache implementasyonu şu şekilde olabilir:

```php
<?php

namespace App\Extensions;

use Illuminate\Contracts\Cache\Store;

class MongoStore implements Store
{
    public function get($key) {}
    public function many(array $keys) {}
    public function put($key, $value, $seconds) {}
    public function putMany(array $values, $seconds) {}
    public function increment($key, $value = 1) {}
    public function decrement($key, $value = 1) {}
    public function forever($key, $value) {}
    public function forget($key) {}
    public function flush() {}
    public function getPrefix() {}
}
```

Bu metodların her birini bir **MongoDB** bağlantısı kullanarak uygulamanız gerekir. Her metodun nasıl uygulanacağına dair bir örnek görmek için Laravel kaynak kodundaki `Illuminate\Cache\MemcachedStore` sınıfına göz atabilirsiniz.

Implementasyonu tamamladıktan sonra, özel driver’ınızı `Cache` facade’ının `extend` metodu ile kaydedebilirsiniz:

```php
Cache::extend('mongo', function (Application $app) {
    return Cache::repository(new MongoStore);
});
```

Bu özel cache driver kodunu nereye yerleştireceğinizi merak ediyorsanız, `app` dizini altında bir `Extensions` namespace’i oluşturabilirsiniz. Ancak, Laravel’in katı bir uygulama yapısı olmadığını unutmayın; uygulamanızı kendi tercihlerinize göre organize edebilirsiniz.

<br>




### Registering the Driver

Özel cache driver’ınızı Laravel’e kaydetmek için `Cache` facade’ının `extend` metodunu kullanacağız. Diğer service provider’lar kendi `boot` metotlarında cache verilerini okumaya çalışabileceğinden, özel driver’ımızı bir **booting callback** içinde kaydetmemiz gerekir. Bu callback, uygulamanın tüm service provider’larının `register` metotları çağrıldıktan **ancak** `boot` metotları çağrılmadan hemen önce çalışır.

Bu işlemi, uygulamanızın `App\Providers\AppServiceProvider` sınıfındaki `register` metodu içerisinde yapabilirsiniz:

```php
<?php

namespace App\Providers;

use App\Extensions\MongoStore;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->booting(function () {
            Cache::extend('mongo', function (Application $app) {
                return Cache::repository(new MongoStore);
            });
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ...
    }
}
```

`extend` metoduna geçirilen ilk argüman, driver’ın adıdır. Bu ad, `config/cache.php` dosyasındaki `driver` seçeneğine karşılık gelir. İkinci argüman ise, bir `Illuminate\Cache\Repository` örneği döndürmesi gereken bir **closure**’dır. Bu closure’a `$app` parametresi geçirilir; bu parametre, servis container’ının bir örneğidir.

Genişletmeniz (extension) kaydedildikten sonra, `.env` dosyasındaki `CACHE_STORE` değişkenini veya `config/cache.php` dosyasındaki varsayılan `store` seçeneğini, oluşturduğunuz özel driver adını kullanacak şekilde güncelleyin.

<br>




## Events

Her cache işlemi sırasında belirli bir kodu çalıştırmak istiyorsanız, cache tarafından yayınlanan çeşitli event’leri dinleyebilirsiniz:

| Event Name                                 |
| ------------------------------------------ |
| Illuminate\Cache\Events\CacheFlushed       |
| Illuminate\Cache\Events\CacheFlushing      |
| Illuminate\Cache\Events\CacheHit           |
| Illuminate\Cache\Events\CacheMissed        |
| Illuminate\Cache\Events\ForgettingKey      |
| Illuminate\Cache\Events\KeyForgetFailed    |
| Illuminate\Cache\Events\KeyForgotten       |
| Illuminate\Cache\Events\KeyWriteFailed     |
| Illuminate\Cache\Events\KeyWritten         |
| Illuminate\Cache\Events\RetrievingKey      |
| Illuminate\Cache\Events\RetrievingManyKeys |
| Illuminate\Cache\Events\WritingKey         |
| Illuminate\Cache\Events\WritingManyKeys    |

Performansı artırmak için, belirli bir cache store için `config/cache.php` dosyasında `events` yapılandırma seçeneğini `false` olarak ayarlayarak cache event’lerini devre dışı bırakabilirsiniz:

```php
'database' => [
    'driver' => 'database',
    // ...
    'events' => false,
],
```
