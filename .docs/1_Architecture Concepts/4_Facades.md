# Facadeler

## Giriş

Laravel dokümantasyonu boyunca, Laravel'in özellikleriyle "facade"lar aracılığıyla etkileşime giren kod örnekleri göreceksiniz. Facade'lar, uygulamanın servis container'ında bulunan sınıflara "statik" bir arayüz sağlar. Laravel, neredeyse tüm özelliklerine erişim sağlayan birçok facade ile birlikte gelir.

Laravel facadeleri, servis container’ındaki temel sınıflara “statik proxy” olarak hizmet eder; bu da geleneksel statik metotlara kıyasla daha kısa, ifade gücü yüksek bir sözdizimi ve daha fazla test edilebilirlik ile esneklik sağlar. Facade’lerin nasıl çalıştığını tamamen anlamasanız da sorun değil — sadece devam edin ve Laravel’i öğrenmeye devam edin.

Tüm Laravel facadeleri **Illuminate\Support\Facades** namespace’i altında tanımlanmıştır. Bu nedenle bir facade’a şu şekilde kolayca erişebiliriz:

```php
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
 
Route::get('/cache', function () {
    return Cache::get('key');
});
```

Laravel dokümantasyonu boyunca, birçok örnekte framework'ün çeşitli özelliklerini göstermek için facade'lar kullanılacaktır.

---

## Helper Fonksiyonları

Facadeleri tamamlamak için Laravel, yaygın Laravel özellikleriyle etkileşimi daha da kolaylaştıran çeşitli global **"helper" fonksiyonları** sunar. Etkileşime girebileceğiniz yaygın helper fonksiyonlardan bazıları **view**, **response**, **url**, **config** ve daha fazlasıdır. Laravel tarafından sunulan her helper fonksiyon, ilgili özelliğin dokümantasyonunda açıklanmıştır; ancak, bunların tam listesi özel helper dokümantasyonunda bulunabilir.

Örneğin, bir JSON yanıtı oluşturmak için **Illuminate\Support\Facades\Response** facade’ini kullanmak yerine basitçe **response** fonksiyonunu kullanabiliriz. Helper fonksiyonları global olarak mevcut olduğundan, bunları kullanmak için herhangi bir sınıfı içe aktarmanız gerekmez:

```php
use Illuminate\Support\Facades\Response;
 
Route::get('/users', function () {
    return Response::json([
        // ...
    ]);
});
 
Route::get('/users', function () {
    return response()->json([
        // ...
    ]);
});
```

---

## Facade Ne Zaman Kullanılır

Facadelerin birçok avantajı vardır. Laravel'in özelliklerini, manuel olarak yapılandırılması veya inject edilmesi gereken uzun sınıf isimlerini hatırlamadan kullanmanızı sağlayan kısa ve akılda kalıcı bir sözdizimi sunarlar. Ayrıca, PHP'nin dinamik metot kullanımından dolayı test etmeleri de kolaydır.

Ancak, facade kullanırken dikkat edilmesi gereken bazı noktalar vardır. Facadelerin birincil tehlikesi, sınıfın **“scope creep”** yaşamasıdır. Facadeler bu kadar kolay kullanılabildiğinden ve injection gerektirmediğinden, bir sınıfın büyüyüp birçok facade kullanmaya başlaması kolaydır. Dependency injection kullanıldığında, büyük bir constructor, sınıfınızın büyüdüğüne dair görsel bir geri bildirim sağlar. Bu nedenle facade kullanırken, sınıfınızın boyutuna dikkat edin ve sorumluluk alanının dar kalmasını sağlayın. Sınıfınız çok büyüyorsa, onu daha küçük sınıflara bölmeyi düşünün.

---

## Facadeler vs. Dependency Injection

Dependency injection’ın temel avantajlarından biri, inject edilen sınıfın implementasyonlarını değiştirme yeteneğidir. Bu, test sırasında özellikle kullanışlıdır çünkü bir mock veya stub inject edip, çeşitli metotların çağrıldığını doğrulayabilirsiniz.

Genellikle, gerçekten statik bir sınıf metodunu mock’lamak veya stub’lamak mümkün değildir. Ancak, facade’ler dinamik metotları kullanarak metot çağrılarını servis container’ından çözümlenen nesnelere yönlendirdiği için, aslında facade’leri inject edilmiş sınıf örnekleri gibi test edebiliriz. Örneğin, aşağıdaki route’u ele alalım:

```php
use Illuminate\Support\Facades\Cache;
 
Route::get('/cache', function () {
    return Cache::get('key');
});
```

Laravel'in facade test yöntemlerini kullanarak, **Cache::get** metodunun beklenen argümanla çağrıldığını doğrulamak için aşağıdaki testi yazabiliriz:

### Pest

### PHPUnit

```php
use Illuminate\Support\Facades\Cache;
 
test('basic example', function () {
    Cache::shouldReceive('get')
        ->with('key')
        ->andReturn('value');
 
    $response = $this->get('/cache');
 
    $response->assertSee('value');
});
```

---

## Facadeler vs. Helper Fonksiyonları

Facadelerin yanı sıra, Laravel ayrıca view oluşturma, event tetikleme, job dispatch etme veya HTTP yanıtı gönderme gibi yaygın görevleri gerçekleştirebilen çeşitli **"helper" fonksiyonları** içerir. Bu helper fonksiyonların çoğu, ilgili bir facade ile aynı işlevi gerçekleştirir. Örneğin, şu iki çağrı birbirine eşdeğerdir:

```php
return Illuminate\Support\Facades\View::make('profile');
 
return view('profile');
```

Facadeler ile helper fonksiyonlar arasında hiçbir pratik fark yoktur. Helper fonksiyonları kullanırken, bunları aynı şekilde test edebilirsiniz. Örneğin, aşağıdaki route’u ele alalım:

```php
Route::get('/cache', function () {
    return cache('key');
});
```

**cache** helper’ı, Cache facade’ının altında bulunan sınıfın **get** metodunu çağıracaktır. Yani helper fonksiyonunu kullansak bile, aşağıdaki testi yazarak metodun beklenen argümanla çağrıldığını doğrulayabiliriz:

```php
use Illuminate\Support\Facades\Cache;
 
/**
 * A basic functional test example.
 */
public function test_basic_example(): void
{
    Cache::shouldReceive('get')
        ->with('key')
        ->andReturn('value');
 
    $response = $this->get('/cache');
 
    $response->assertSee('value');
}
```
# Facadeler Nasıl Çalışır

Bir Laravel uygulamasında, bir **facade**, container’daki bir nesneye erişim sağlayan bir sınıftır. Bu mekanizmanın çalışmasını sağlayan yapı, **Facade** sınıfında bulunur. Laravel’in facadeleri ve oluşturduğunuz tüm özel facadeler, **Illuminate\Support\Facades\Facade** temel sınıfını genişletir.

**Facade** temel sınıfı, **__callStatic()** sihirli metodunu kullanarak, facade üzerinden yapılan çağrıları container’dan çözümlenen bir nesneye yönlendirir. Aşağıdaki örnekte, Laravel’in cache sistemine bir çağrı yapılmaktadır. Bu koda bakarak, Cache sınıfında statik bir `get` metodunun çağrıldığını varsayabilirsiniz:

```php
<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
 
class UserController extends Controller
{
    /**
     * Show the profile for the given user.
     */
    public function showProfile(string $id): View
    {
        $user = Cache::get('user:'.$id);
 
        return view('profile', ['user' => $user]);
    }
}
```

Dosyanın en üstünde **Cache** facade’ini “import” ettiğimize dikkat edin. Bu facade, **Illuminate\Contracts\Cache\Factory** arayüzünün temel implementasyonuna erişim sağlamak için bir proxy görevi görür. Facade üzerinden yaptığımız tüm çağrılar, Laravel’in cache servisine ait temel örneğe iletilir.

Eğer **Illuminate\Support\Facades\Cache** sınıfına bakarsak, aslında burada statik bir `get` metodu bulunmadığını görürüz:

```php
class Cache extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'cache';
    }
}
```

Bunun yerine, **Cache** facade’i **Facade** temel sınıfını genişletir ve **getFacadeAccessor()** metodunu tanımlar. Bu metodun görevi, bir servis container binding’inin adını döndürmektir. Bir kullanıcı, Cache facade’i üzerindeki herhangi bir statik metodu çağırdığında, Laravel **service container** üzerinden `cache` binding’ini çözümler ve istenen metodu (bu örnekte `get`) o nesne üzerinde çalıştırır.

---

## Gerçek Zamanlı Facadeler

Gerçek zamanlı (real-time) facadeler kullanarak, uygulamanızdaki herhangi bir sınıfı sanki bir facade’mış gibi kullanabilirsiniz. Bunun nasıl kullanılabileceğini göstermek için önce gerçek zamanlı facadelerin **kullanılmadığı** bir örneğe bakalım.

Örneğin, **Podcast** modelimizin bir `publish` metodu olduğunu varsayalım. Ancak, podcast’i yayımlamak için bir **Publisher** örneğini inject etmemiz gerekiyor:

```php
<?php
 
namespace App\Models;
 
use App\Contracts\Publisher;
use Illuminate\Database\Eloquent\Model;
 
class Podcast extends Model
{
    /**
     * Publish the podcast.
     */
    public function publish(Publisher $publisher): void
    {
        $this->update(['publishing' => now()]);
 
        $publisher->publish($this);
    }
}
```

Metoda bir publisher implementasyonu inject etmek, bu metodu izole şekilde test etmemizi kolaylaştırır çünkü inject edilen publisher’ı mock’layabiliriz. Ancak, `publish` metodunu her çağırdığımızda bir **Publisher** örneği geçmemiz gerekir.

Gerçek zamanlı facadeler kullanarak, aynı test edilebilirliği koruyabiliriz ancak **Publisher** örneğini açıkça iletmemize gerek kalmaz. Bir gerçek zamanlı facade oluşturmak için, import edilen sınıfın namespace’inin başına **Facades** önekini ekleyin:

```php
<?php
 
namespace App\Models;
 
use App\Contracts\Publisher; 
use Facades\App\Contracts\Publisher; 
use Illuminate\Database\Eloquent\Model;
 
class Podcast extends Model
{
    /**
     * Publish the podcast.
     */
    public function publish(Publisher $publisher): void 
    public function publish(): void 
    {
        $this->update(['publishing' => now()]);
 
        $publisher->publish($this); 
        Publisher::publish($this); 
    }
}
```

Gerçek zamanlı facade kullanıldığında, publisher implementasyonu service container’dan, **Facades** önekinden sonraki arayüz veya sınıf adının kısmı kullanılarak çözümlenir.

Test sırasında, Laravel’in yerleşik facade test yardımcılarını kullanarak bu metot çağrısını mock’layabiliriz:

### Pest

### PHPUnit

```php
<?php
 
use App\Models\Podcast;
use Facades\App\Contracts\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
 
pest()->use(RefreshDatabase::class);
 
test('podcast can be published', function () {
    $podcast = Podcast::factory()->create();
 
    Publisher::shouldReceive('publish')->once()->with($podcast);
 
    $podcast->publish();
});
```
# Facade Sınıf Referansı

Aşağıda her bir facade ve onun temel aldığı sınıfı bulabilirsiniz. Bu liste, belirli bir facade kökü için API dokümantasyonuna hızlıca göz atmak adına yararlı bir araçtır. Uygun olduğu durumlarda, **service container binding key** de eklenmiştir.

| **Facade**            | **Sınıf**                                       | **Service Container Binding** |
| --------------------- | ----------------------------------------------- | ----------------------------- |
| App                   | Illuminate\Foundation\Application               | app                           |
| Artisan               | Illuminate\Contracts\Console\Kernel             | artisan                       |
| Auth (Instance)       | Illuminate\Contracts\Auth\Guard                 | auth.driver                   |
| Auth                  | Illuminate\Auth\AuthManager                     | auth                          |
| Blade                 | Illuminate\View\Compilers\BladeCompiler         | blade.compiler                |
| Broadcast (Instance)  | Illuminate\Contracts\Broadcasting\Broadcaster   |                               |
| Broadcast             | Illuminate\Contracts\Broadcasting\Factory       |                               |
| Bus                   | Illuminate\Contracts\Bus\Dispatcher             |                               |
| Cache (Instance)      | Illuminate\Cache\Repository                     | cache.store                   |
| Cache                 | Illuminate\Cache\CacheManager                   | cache                         |
| Config                | Illuminate\Config\Repository                    | config                        |
| Context               | Illuminate\Log\Context\Repository               |                               |
| Cookie                | Illuminate\Cookie\CookieJar                     | cookie                        |
| Crypt                 | Illuminate\Encryption\Encrypter                 | encrypter                     |
| Date                  | Illuminate\Support\DateFactory                  | date                          |
| DB (Instance)         | Illuminate\Database\Connection                  | db.connection                 |
| DB                    | Illuminate\Database\DatabaseManager             | db                            |
| Event                 | Illuminate\Events\Dispatcher                    | events                        |
| Exceptions (Instance) | Illuminate\Contracts\Debug\ExceptionHandler     |                               |
| Exceptions            | Illuminate\Foundation\Exceptions\Handler        |                               |
| File                  | Illuminate\Filesystem\Filesystem                | files                         |
| Gate                  | Illuminate\Contracts\Auth\Access\Gate           |                               |
| Hash                  | Illuminate\Contracts\Hashing\Hasher             | hash                          |
| Http                  | Illuminate\Http\Client\Factory                  |                               |
| Lang                  | Illuminate\Translation\Translator               | translator                    |
| Log                   | Illuminate\Log\LogManager                       | log                           |
| Mail                  | Illuminate\Mail\Mailer                          | mailer                        |
| Notification          | Illuminate\Notifications\ChannelManager         |                               |
| Password (Instance)   | Illuminate\Auth\Passwords\PasswordBroker        | auth.password.broker          |
| Password              | Illuminate\Auth\Passwords\PasswordBrokerManager | auth.password                 |
| Pipeline (Instance)   | Illuminate\Pipeline\Pipeline                    |                               |
| Process               | Illuminate\Process\Factory                      |                               |
| Queue (Base Class)    | Illuminate\Queue\Queue                          |                               |
| Queue (Instance)      | Illuminate\Contracts\Queue\Queue                | queue.connection              |
| Queue                 | Illuminate\Queue\QueueManager                   | queue                         |
| RateLimiter           | Illuminate\Cache\RateLimiter                    |                               |
| Redirect              | Illuminate\Routing\Redirector                   | redirect                      |
| Redis (Instance)      | Illuminate\Redis\Connections\Connection         | redis.connection              |
| Redis                 | Illuminate\Redis\RedisManager                   | redis                         |
| Request               | Illuminate\Http\Request                         | request                       |
| Response (Instance)   | Illuminate\Http\Response                        |                               |
| Response              | Illuminate\Contracts\Routing\ResponseFactory    |                               |
| Route                 | Illuminate\Routing\Router                       | router                        |
| Schedule              | Illuminate\Console\Scheduling\Schedule          |                               |
| Schema                | Illuminate\Database\Schema\Builder              |                               |
| Session (Instance)    | Illuminate\Session\Store                        | session.store                 |
| Session               | Illuminate\Session\SessionManager               | session                       |
| Storage (Instance)    | Illuminate\Contracts\Filesystem\Filesystem      | filesystem.disk               |
| Storage               | Illuminate\Filesystem\FilesystemManager         | filesystem                    |
| URL                   | Illuminate\Routing\UrlGenerator                 | url                           |
| Validator (Instance)  | Illuminate\Validation\Validator                 |                               |
| Validator             | Illuminate\Validation\Factory                   | validator                     |
| View (Instance)       | Illuminate\View\View                            |                               |
| View                  | Illuminate\View\Factory                         | view                          |
| Vite                  | Illuminate\Foundation\Vite                      |                               |

