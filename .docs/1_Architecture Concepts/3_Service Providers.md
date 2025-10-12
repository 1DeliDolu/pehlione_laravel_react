## Servis Sağlayıcılar

### Giriş

Servis sağlayıcılar, tüm Laravel uygulama başlatma işlemlerinin merkezi yeridir. Kendi uygulamanızın yanı sıra Laravel’in çekirdek servislerinin tamamı servis sağlayıcılar aracılığıyla başlatılır.

Peki, “başlatma” (bootstrapped) derken neyi kastediyoruz? Genel olarak, servis container bağlamalarını, olay dinleyicilerini, middleware’leri ve hatta route’ları kaydetmeyi kastediyoruz. Servis sağlayıcılar, uygulamanızı yapılandırmak için merkezi bir yerdir.

Laravel, posta gönderici, kuyruk, önbellek ve diğerleri gibi çekirdek servislerini başlatmak için dahili olarak onlarca servis sağlayıcı kullanır. Bu sağlayıcıların birçoğu “ertelenmiş” (deferred) sağlayıcılardır, yani her istekte yüklenmezler, yalnızca sundukları servislere gerçekten ihtiyaç duyulduğunda yüklenirler.

Tüm kullanıcı tanımlı servis sağlayıcılar **bootstrap/providers.php** dosyasında kaydedilir. Aşağıdaki belgede, kendi servis sağlayıcılarınızı nasıl yazacağınızı ve bunları Laravel uygulamanıza nasıl kaydedeceğinizi öğreneceksiniz.

Laravel’in istekleri nasıl ele aldığı ve dahili olarak nasıl çalıştığı hakkında daha fazla bilgi edinmek istiyorsanız, **Laravel istek yaşam döngüsü** belgelerine göz atın.

---

### Servis Sağlayıcı Yazma

Tüm servis sağlayıcılar **Illuminate\Support\ServiceProvider** sınıfını genişletir. Çoğu servis sağlayıcı bir **register** ve bir **boot** metodu içerir. **register** metodunda yalnızca şeyleri servis container’a bağlamalısınız. Bu metod içinde olay dinleyicileri, route’lar veya başka herhangi bir işlevsellik kaydetmeye çalışmamalısınız.

**Artisan CLI**, yeni bir sağlayıcıyı **make:provider** komutu aracılığıyla oluşturabilir. Laravel, yeni sağlayıcınızı uygulamanızın **bootstrap/providers.php** dosyasına otomatik olarak kaydeder:

```bash
php artisan make:provider RiakServiceProvider
```

---

### Register Metodu

Daha önce belirtildiği gibi, **register** metodunda yalnızca şeyleri servis container’a bağlamalısınız. Bu metodda olay dinleyicileri, route’lar veya başka işlevler kaydetmeye çalışmamalısınız. Aksi takdirde, henüz yüklenmemiş bir servis sağlayıcının sağladığı bir servisi yanlışlıkla kullanabilirsiniz.

Temel bir servis sağlayıcıya bakalım. Herhangi bir servis sağlayıcı metodunun içinde, servis container’a erişim sağlayan **$app** özelliğine her zaman erişiminiz vardır:

```php
<?php

namespace App\Providers;

use App\Services\Riak\Connection;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class RiakServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Connection::class, function (Application $app) {
            return new Connection(config('riak'));
        });
    }
}
```

Bu servis sağlayıcı yalnızca bir **register** metodu tanımlar ve bu metodu kullanarak **App\Services\Riak\Connection**’ın bir implementasyonunu servis container’da tanımlar.
Laravel’in **service container**’ı hakkında henüz bilgi sahibi değilseniz, onun belgelerine göz atın.

---

### Bindings ve Singletons Özellikleri

Servis sağlayıcınız birçok basit binding kaydediyorsa, her container bağlamasını manuel olarak kaydetmek yerine **bindings** ve **singletons** özelliklerini kullanmak isteyebilirsiniz. Servis sağlayıcı framework tarafından yüklendiğinde, bu özellikleri otomatik olarak kontrol eder ve binding’leri kaydeder:

```php
<?php

namespace App\Providers;

use App\Contracts\DowntimeNotifier;
use App\Contracts\ServerProvider;
use App\Services\DigitalOceanServerProvider;
use App\Services\PingdomDowntimeNotifier;
use App\Services\ServerToolsProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        ServerProvider::class => DigitalOceanServerProvider::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        DowntimeNotifier::class => PingdomDowntimeNotifier::class,
        ServerProvider::class => ServerToolsProvider::class,
    ];
}
```

---

### Boot Metodu

Peki, servis sağlayıcımız içinde bir **view composer** kaydetmemiz gerekirse ne olacak? Bu işlem **boot** metodu içinde yapılmalıdır. Bu metod, diğer tüm servis sağlayıcılar kaydedildikten sonra çağrılır, yani framework tarafından kaydedilen tüm diğer servislere erişiminiz olur:

```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('view', function () {
            // ...
        });
    }
}
```

---

### Boot Metodu Bağımlılık Enjeksiyonu

Servis sağlayıcınızın **boot** metodu için bağımlılıkları **type-hint** olarak tanımlayabilirsiniz. Servis container, ihtiyacınız olan bağımlılıkları otomatik olarak enjekte edecektir:

```php
use Illuminate\Contracts\Routing\ResponseFactory;

/**
 * Bootstrap any application services.
 */
public function boot(ResponseFactory $response): void
{
    $response->macro('serialized', function (mixed $value) {
        // ...
    });
}
```
## Sağlayıcıların Kaydedilmesi

Tüm servis sağlayıcılar **bootstrap/providers.php** yapılandırma dosyasında kaydedilir. Bu dosya, uygulamanızın servis sağlayıcılarının sınıf adlarını içeren bir dizi döndürür:

```php
<?php
 
return [
    App\Providers\AppServiceProvider::class,
];
```

**make:provider** Artisan komutunu çağırdığınızda, Laravel oluşturulan sağlayıcıyı otomatik olarak **bootstrap/providers.php** dosyasına ekleyecektir. Ancak, sağlayıcı sınıfını manuel olarak oluşturduysanız, sağlayıcı sınıfını diziye manuel olarak eklemelisiniz:

```php
<?php
 
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ComposerServiceProvider::class, 
];
```

---

## Ertelenmiş Sağlayıcılar (Deferred Providers)

Sağlayıcınız yalnızca servis container’a binding kaydediyorsa, kayıt işlemini, bu binding’lerden biri gerçekten ihtiyaç duyulana kadar ertelemeyi tercih edebilirsiniz. Böyle bir sağlayıcının yüklenmesini ertelemek, her istekte dosya sisteminden yüklenmediği için uygulamanızın performansını artıracaktır.

Laravel, ertelenmiş servis sağlayıcılar tarafından sağlanan tüm servislerin bir listesini, sağlayıcının sınıf adıyla birlikte derler ve saklar. Daha sonra, yalnızca bu servislerden birini çözmeye çalıştığınızda Laravel servis sağlayıcıyı yükler.

Bir sağlayıcının yüklenmesini ertelemek için **\Illuminate\Contracts\Support\DeferrableProvider** arayüzünü uygulayın ve bir **provides** metodu tanımlayın. **provides** metodu, sağlayıcı tarafından kaydedilen servis container binding’lerini döndürmelidir:

```php
<?php
 
namespace App\Providers;
 
use App\Services\Riak\Connection;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
 
class RiakServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Connection::class, function (Application $app) {
            return new Connection($app['config']['riak']);
        });
    }
 
    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [Connection::class];
    }
}
```
