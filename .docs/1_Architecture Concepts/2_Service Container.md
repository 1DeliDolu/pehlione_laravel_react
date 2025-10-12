````markdown
Service Container  
Giriş  
Laravel service container, sınıf bağımlılıklarını yönetmek ve bağımlılık enjeksiyonu (dependency injection) gerçekleştirmek için güçlü bir araçtır. Bağımlılık enjeksiyonu, temel olarak şu anlama gelir: sınıf bağımlılıkları, sınıfa constructor veya bazı durumlarda “setter” metotları aracılığıyla “enjeksiyon” yapılır.  

Basit bir örneğe bakalım:  

```php
<?php
 
namespace App\Http\Controllers;
 
use App\Services\AppleMusic;
use Illuminate\View\View;
 
class PodcastController extends Controller
{
    /**
     * Yeni bir controller örneği oluştur.
     */
    public function __construct(
        protected AppleMusic $apple,
    ) {}
 
    /**
     * Verilen podcast hakkında bilgi göster.
     */
    public function show(string $id): View
    {
        return view('podcasts.show', [
            'podcast' => $this->apple->findPodcast($id)
        ]);
    }
}
````

Bu örnekte, `PodcastController`, Apple Music gibi bir veri kaynağından podcast’leri almak zorundadır. Bu yüzden podcast’leri alabilen bir servisi enjeksiyon yoluyla sınıfa dahil ederiz. Servis enjeksiyonla sağlandığı için, uygulamamızı test ederken `AppleMusic` servisinin “mock” (sahte) bir versiyonunu kolayca oluşturabilir veya kullanabiliriz.

Laravel service container’ı derinlemesine anlamak, güçlü ve büyük uygulamalar oluşturmak için olduğu kadar Laravel çekirdeğine katkıda bulunmak için de çok önemlidir.

Zero Configuration Resolution
Bir sınıfın bağımlılığı yoksa veya yalnızca başka somut (concrete) sınıflara bağlıysa (interface’lere değil), container o sınıfın nasıl çözümleneceğini bilmek zorunda değildir. Örneğin, aşağıdaki kodu `routes/web.php` dosyanıza koyabilirsiniz:

```php
<?php
 
class Service
{
    // ...
}
 
Route::get('/', function (Service $service) {
    dd($service::class);
});
```

Bu örnekte, uygulamanızın `/` rotasına istek gönderildiğinde, `Service` sınıfı otomatik olarak çözümlenir ve rota işleyicisine enjekte edilir. Bu devrim niteliğindedir. Bu, uygulamanızı geliştirirken şişkin yapılandırma dosyalarıyla uğraşmadan bağımlılık enjeksiyonunun tüm avantajlarından yararlanabileceğiniz anlamına gelir.

Neyse ki, Laravel uygulaması oluştururken yazacağınız birçok sınıf, container aracılığıyla otomatik olarak bağımlılıklarını alır — bunlara controller’lar, event listener’lar, middleware’ler ve daha fazlası dahildir. Ayrıca, queued job’ların `handle` metodu içinde de bağımlılıkları type-hint yoluyla belirtebilirsiniz. Otomatik ve sıfır yapılandırmalı bağımlılık enjeksiyonunun gücünü bir kez tattığınızda, onsuz geliştirme yapmak neredeyse imkânsız gelir.

Container Ne Zaman Kullanılmalı
Sıfır yapılandırmalı çözümleme sayesinde, genellikle rotalarda, controller’larda, event listener’larda ve başka yerlerde bağımlılıkları type-hint yaparak container ile manuel olarak etkileşime girmeden kullanırsınız. Örneğin, mevcut isteğe kolayca erişebilmek için rota tanımınızda `Illuminate\Http\Request` nesnesini type-hint olarak belirtebilirsiniz. Container ile doğrudan etkileşime girmesek bile, arka planda bu bağımlılıkların enjeksiyonunu container yönetir:

```php
use Illuminate\Http\Request;
 
Route::get('/', function (Request $request) {
    // ...
});
```

Birçok durumda, otomatik bağımlılık enjeksiyonu ve facadeler sayesinde, Laravel uygulamalarını hiçbir şeyi manuel olarak container’a bağlamadan veya çözümlemeden geliştirebilirsiniz. Peki, container ile manuel olarak ne zaman etkileşime geçmeniz gerekir? İki durumu inceleyelim.

Birincisi, bir interface uygulayan bir sınıf yazarsanız ve bu interface’i bir rota veya sınıf constructor’ında type-hint olarak belirtmek isterseniz, container’a bu interface’in nasıl çözümleneceğini bildirmeniz gerekir.

İkincisi, diğer Laravel geliştiricileriyle paylaşmayı planladığınız bir Laravel paketi yazıyorsanız, paketinizin servislerini container’a bağlamanız gerekebilir.

````markdown
Binding  
Binding Temelleri  
Basit Binding’ler  
Service container binding’lerinin neredeyse tamamı service provider’lar içinde kaydedilir, bu yüzden örneklerin çoğu container’ın bu bağlamda nasıl kullanıldığını gösterecektir.  

Bir service provider içinde container’a her zaman `$this->app` özelliği aracılığıyla erişebilirsiniz. `bind` metodunu kullanarak bir binding kaydedebiliriz; bu metoda kaydetmek istediğimiz sınıf veya interface adını, ayrıca bu sınıfın bir örneğini döndüren bir closure’ı geçiririz:  

```php
use App\Services\Transistor;
use App\Services\PodcastParser;
use Illuminate\Contracts\Foundation\Application;
 
$this->app->bind(Transistor::class, function (Application $app) {
    return new Transistor($app->make(PodcastParser::class));
});
````

Dikkat ederseniz, çözücüye (resolver) container’ın kendisini argüman olarak alıyoruz. Böylece, oluşturduğumuz nesnenin alt bağımlılıklarını çözümlemek için container’ı kullanabiliriz.

Belirtildiği gibi, genellikle container ile service provider’lar içinde etkileşime geçersiniz; ancak, provider dışında container ile çalışmak isterseniz, bunu `App` facade’ı aracılığıyla da yapabilirsiniz:

```php
use App\Services\Transistor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\App;
 
App::bind(Transistor::class, function (Application $app) {
    // ...
});
```

`bindIf` metodunu, yalnızca verilen tür için daha önce bir binding kaydedilmemişse yeni bir binding oluşturmak için kullanabilirsiniz:

```php
$this->app->bindIf(Transistor::class, function (Application $app) {
    return new Transistor($app->make(PodcastParser::class));
});
```

Kolaylık olması açısından, kaydetmek istediğiniz sınıf veya interface adını ayrı bir argüman olarak vermeden, Laravel’in bunu closure’ın dönüş türünden (return type) otomatik olarak anlamasına izin verebilirsiniz:

```php
App::bind(function (Application $app): Transistor {
    return new Transistor($app->make(PodcastParser::class));
});
```

Bağımlılığı olmayan sınıfları container’a manuel olarak bind etmenize gerek yoktur. Container bu nesneleri reflection kullanarak otomatik olarak çözümleyebilir.

### Bir Singleton Binding

`singleton` metodu, bir sınıf veya interface’i container’a yalnızca bir kez çözümlenecek şekilde bağlar. Bir singleton binding çözümlendikten sonra, container’a yapılan sonraki tüm çağrılarda aynı nesne örneği döndürülür:

```php
use App\Services\Transistor;
use App\Services\PodcastParser;
use Illuminate\Contracts\Foundation\Application;
 
$this->app->singleton(Transistor::class, function (Application $app) {
    return new Transistor($app->make(PodcastParser::class));
});
```

`singletonIf` metodunu kullanarak, yalnızca verilen tür için daha önce bir binding kaydedilmemişse singleton binding oluşturabilirsiniz:

```php
$this->app->singletonIf(Transistor::class, function (Application $app) {
    return new Transistor($app->make(PodcastParser::class));
});
```

### Singleton Attribute

Alternatif olarak, bir interface veya sınıfı `#[Singleton]` attribute’u ile işaretleyerek, container’a bunun yalnızca bir kez çözümlenmesi gerektiğini belirtebilirsiniz:

```php
<?php
 
namespace App\Services;
 
use Illuminate\Container\Attributes\Singleton;
 
#[Singleton]
class Transistor
{
    // ...
}
```

### Scoped Singleton Binding

`scoped` metodu, bir sınıf veya interface’i belirli bir Laravel istek / job yaşam döngüsü içinde yalnızca bir kez çözümlenecek şekilde container’a bağlar. Bu metod `singleton` metoduna benzerdir, ancak `scoped` ile kaydedilen örnekler, Laravel uygulaması yeni bir “yaşam döngüsü” başlattığında (örneğin Laravel Octane bir isteği işlerken veya bir queue worker yeni bir job işlerken) sıfırlanır:

```php
use App\Services\Transistor;
use App\Services\PodcastParser;
use Illuminate\Contracts\Foundation\Application;
 
$this->app->scoped(Transistor::class, function (Application $app) {
    return new Transistor($app->make(PodcastParser::class));
});
```

`scopedIf` metodunu kullanarak, yalnızca verilen tür için daha önce bir binding kaydedilmemişse scoped binding oluşturabilirsiniz:

```php
$this->app->scopedIf(Transistor::class, function (Application $app) {
    return new Transistor($app->make(PodcastParser::class));
});
```

### Scoped Attribute

Alternatif olarak, bir interface veya sınıfı `#[Scoped]` attribute’u ile işaretleyerek, container’a bunun yalnızca bir Laravel istek / job yaşam döngüsü boyunca bir kez çözümlenmesi gerektiğini belirtebilirsiniz:

```php
<?php
 
namespace App\Services;
 
use Illuminate\Container\Attributes\Scoped;
 
#[Scoped]
class Transistor
{
    // ...
}
```

### Instance Binding

Var olan bir nesne örneğini container’a `instance` metodu ile bağlayabilirsiniz. Verilen örnek, container’a yapılan sonraki tüm çağrılarda döndürülür:

```php
use App\Services\Transistor;
use App\Services\PodcastParser;
 
$service = new Transistor(new PodcastParser);
 
$this->app->instance(Transistor::class, $service);
```

### Interface’leri Implementasyonlara Bağlama

Service container’ın en güçlü özelliklerinden biri, bir interface’i belirli bir implementasyona bağlayabilmesidir. Örneğin, `EventPusher` adında bir interface’imiz ve `RedisEventPusher` adlı bir implementasyonumuz olduğunu varsayalım. `RedisEventPusher` implementasyonunu container’a şu şekilde kaydedebiliriz:

```php
use App\Contracts\EventPusher;
use App\Services\RedisEventPusher;
 
$this->app->bind(EventPusher::class, RedisEventPusher::class);
```

Bu ifade, container’a `EventPusher` implementasyonu gerektiğinde `RedisEventPusher` sınıfını enjekte etmesini söyler. Artık container tarafından çözümlenen bir sınıfın constructor’ında `EventPusher` interface’ini type-hint olarak belirtebiliriz. Unutmayın, controller’lar, event listener’lar, middleware’ler ve Laravel uygulamalarındaki birçok sınıf her zaman container aracılığıyla çözülür:

```php
use App\Contracts\EventPusher;
 
public function __construct(
    protected EventPusher $pusher,
) {}
```

### Bind Attribute

Laravel ayrıca kolaylık sağlamak için bir `Bind` attribute’u da sunar. Bu attribute’u herhangi bir interface’e uygulayarak, bu interface istendiğinde hangi implementasyonun otomatik olarak enjekte edileceğini Laravel’e bildirebilirsiniz. `Bind` attribute’unu kullanırken, service provider’larda ek bir kayıt işlemi yapmanıza gerek yoktur.

Ayrıca, bir interface’e birden fazla `Bind` attribute’u ekleyerek, belirli environment’lar için farklı implementasyonlar belirleyebilirsiniz:

```php
<?php
 
namespace App\Contracts;
 
use App\Services\FakeEventPusher;
use App\Services\RedisEventPusher;
use Illuminate\Container\Attributes\Bind;
 
#[Bind(RedisEventPusher::class)]
#[Bind(FakeEventPusher::class, environments: ['local', 'testing'])]
interface EventPusher
{
    // ...
}
```

Ayrıca, `Singleton` ve `Scoped` attribute’ları da ekleyerek, container binding’lerinin bir kez veya her istek / job yaşam döngüsü başına bir kez çözülmesi gerektiğini belirtebilirsiniz:

```php
use App\Services\RedisEventPusher;
use Illuminate\Container\Attributes\Bind;
use Illuminate\Container\Attributes\Singleton;
 
#[Bind(RedisEventPusher::class)]
#[Singleton]
interface EventPusher
{
    // ...
}
```

### Contextual Binding

Bazen aynı interface’i kullanan iki sınıf olabilir, ancak her birine farklı implementasyonlar enjekte etmek isteyebilirsiniz. Örneğin, iki controller `Illuminate\Contracts\Filesystem\Filesystem` contract’ının farklı implementasyonlarına ihtiyaç duyabilir. Laravel, bu davranışı tanımlamak için basit ve anlaşılır bir arayüz sunar:

```php
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\VideoController;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
 
$this->app->when(PhotoController::class)
    ->needs(Filesystem::class)
    ->give(function () {
        return Storage::disk('local');
    });
 
$this->app->when([VideoController::class, UploadController::class])
    ->needs(Filesystem::class)
    ->give(function () {
        return Storage::disk('s3');
    });
```

### Contextual Attributes

Contextual binding genellikle sürücü veya yapılandırma değerlerinin enjekte edilmesi için kullanıldığından, Laravel bu tür değerleri manuel olarak tanımlamak zorunda kalmadan enjekte etmenizi sağlayan çeşitli contextual binding attribute’ları sunar.

Örneğin, `Storage` attribute’u belirli bir depolama diskini enjekte etmek için kullanılabilir:

```php
<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Container\Attributes\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
 
class PhotoController extends Controller
{
    public function __construct(
        #[Storage('local')] protected Filesystem $filesystem
    ) {
        // ...
    }
}
```

`Storage` attribute’una ek olarak, Laravel `Auth`, `Cache`, `Config`, `Context`, `DB`, `Give`, `Log`, `RouteParameter` ve `Tag` attribute’larını da sunar:

```php
<?php
 
namespace App\Http\Controllers;
 
use App\Contracts\UserRepository;
use App\Models\Photo;
use App\Repositories\DatabaseRepository;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Container\Attributes\Cache;
use Illuminate\Container\Attributes\Config;
use Illuminate\Container\Attributes\Context;
use Illuminate\Container\Attributes\DB;
use Illuminate\Container\Attributes\Give;
use Illuminate\Container\Attributes\Log;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Container\Attributes\Tag;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Connection;
use Psr\Log\LoggerInterface;
 
class PhotoController extends Controller
{
    public function __construct(
        #[Auth('web')] protected Guard $auth,
        #[Cache('redis')] protected Repository $cache,
        #[Config('app.timezone')] protected string $timezone,
        #[Context('uuid')] protected string $uuid,
        #[Context('ulid', hidden: true)] protected string $ulid,
        #[DB('mysql')] protected Connection $connection,
        #[Give(DatabaseRepository::class)] protected UserRepository $users,
        #[Log('daily')] protected LoggerInterface $log,
        #[RouteParameter('photo')] protected Photo $photo,
        #[Tag('reports')] protected iterable $reports,
    ) {
        // ...
    }
}
```

Ayrıca Laravel, mevcut kimliği doğrulanmış kullanıcıyı bir route veya sınıfa enjekte etmek için `CurrentUser` attribute’unu sağlar:

```php
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
 
Route::get('/user', function (#[CurrentUser] User $user) {
    return $user;
})->middleware('auth');
```

### Özel Attribute’lar Tanımlama

Kendi contextual attribute’larınızı oluşturmak için `Illuminate\Contracts\Container\ContextualAttribute` contract’ını uygulayabilirsiniz. Container, attribute’unuzun `resolve` metodunu çağırır ve bu metod, sınıfa enjekte edilmesi gereken değeri çözümlemelidir. Aşağıdaki örnekte, Laravel’in yerleşik `Config` attribute’unu yeniden uygulayacağız:

```php
<?php
 
namespace App\Attributes;
 
use Attribute;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;
 
#[Attribute(Attribute::TARGET_PARAMETER)]
class Config implements ContextualAttribute
{
    public function __construct(public string $key, public mixed $default = null)
    {
    }
 
    public static function resolve(self $attribute, Container $container)
    {
        return $container->make('config')->get($attribute->key, $attribute->default);
    }
}
```

### Primitif Değerleri Binding Etme

Bazen bir sınıf, bazı sınıfları enjekte ederken aynı zamanda bir tamsayı gibi basit (primitive) bir değere de ihtiyaç duyabilir. Contextual binding kullanarak bu tür değerleri kolayca enjekte edebilirsiniz:

```php
use App\Http\Controllers\UserController;
 
$this->app->when(UserController::class)
    ->needs('$variableName')
    ->give($value);
```

Bazen bir sınıf, etiketlenmiş (tagged) örneklerin bir dizisine bağımlı olabilir. `giveTagged` metodunu kullanarak, belirli bir etikete sahip tüm container binding’lerini kolayca enjekte edebilirsiniz:

```php
$this->app->when(ReportAggregator::class)
    ->needs('$reports')
    ->giveTagged('reports');
```

Uygulamanızın yapılandırma dosyalarından bir değeri enjekte etmeniz gerekiyorsa, `giveConfig` metodunu kullanabilirsiniz:

```php
$this->app->when(ReportAggregator::class)
    ->needs('$timezone')
    ->giveConfig('app.timezone');
```

````markdown
Binding Typed Variadics  
Bazen, bir sınıf variadic bir constructor argümanı kullanarak typed objelerden oluşan bir dizi alabilir:  

```php
<?php
 
use App\Models\Filter;
use App\Services\Logger;
 
class Firewall
{
    /**
     * Filtre örnekleri.
     *
     * @var array
     */
    protected $filters;
 
    /**
     * Yeni bir sınıf örneği oluştur.
     */
    public function __construct(
        protected Logger $logger,
        Filter ...$filters,
    ) {
        $this->filters = $filters;
    }
}
````

Contextual binding kullanarak, bu bağımlılığı çözümlemek için `give` metoduna, `Filter` örneklerini döndüren bir closure sağlayabilirsiniz:

```php
$this->app->when(Firewall::class)
    ->needs(Filter::class)
    ->give(function (Application $app) {
          return [
              $app->make(NullFilter::class),
              $app->make(ProfanityFilter::class),
              $app->make(TooLongFilter::class),
          ];
    });
```

Kolaylık olması açısından, `Firewall` sınıfı `Filter` örneklerine ihtiyaç duyduğunda container tarafından çözümlenecek sınıf adlarının bir dizisini de doğrudan verebilirsiniz:

```php
$this->app->when(Firewall::class)
    ->needs(Filter::class)
    ->give([
        NullFilter::class,
        ProfanityFilter::class,
        TooLongFilter::class,
    ]);
```

### Variadic Tag Dependencies

Bazen bir sınıf, belirli bir sınıfla type-hint edilmiş bir variadic bağımlılığa sahip olabilir (`Report ...$reports`). `needs` ve `giveTagged` metotlarını kullanarak, belirli bir bağımlılık için aynı etiketle container’a kaydedilen tüm binding’leri kolayca enjekte edebilirsiniz:

```php
$this->app->when(ReportAggregator::class)
    ->needs(Report::class)
    ->giveTagged('reports');
```

---

### Tagging

Bazen belirli bir “kategoriye” ait tüm binding’leri çözümlemeniz gerekebilir. Örneğin, birçok farklı `Report` interface implementasyonu alan bir rapor analizörü (report analyzer) oluşturduğunuzu varsayalım. `Report` implementasyonlarını kaydettikten sonra, `tag` metodunu kullanarak onlara bir etiket atayabilirsiniz:

```php
$this->app->bind(CpuReport::class, function () {
    // ...
});
 
$this->app->bind(MemoryReport::class, function () {
    // ...
});
 
$this->app->tag([CpuReport::class, MemoryReport::class], 'reports');
```

Servisler etiketlendikten sonra, container’ın `tagged` metodu aracılığıyla hepsini kolayca çözümleyebilirsiniz:

```php
$this->app->bind(ReportAnalyzer::class, function (Application $app) {
    return new ReportAnalyzer($app->tagged('reports'));
});
```

---

### Extending Bindings

`extend` metodu, çözümlenen servisleri değiştirmeye (modify) olanak tanır. Örneğin, bir servis çözümlendiğinde, servisi dekore etmek veya yapılandırmak için ek kod çalıştırabilirsiniz.
`extend` metodu iki argüman alır: genişletmek istediğiniz servis sınıfı ve değiştirilmiş servisi döndürmesi gereken bir closure. Closure, çözümlenen servisi ve container örneğini alır:

```php
$this->app->extend(Service::class, function (Service $service, Application $app) {
    return new DecoratedService($service);
});
```

---

### Resolving

#### `make` Metodu

Container’dan bir sınıf örneğini çözümlemek için `make` metodunu kullanabilirsiniz. `make` metodu, çözümlemek istediğiniz sınıf veya interface adını alır:

```php
use App\Services\Transistor;
 
$transistor = $this->app->make(Transistor::class);
```

Bazı sınıf bağımlılıkları container aracılığıyla çözümlenemiyorsa, bunları `makeWith` metoduna bir associative array olarak geçerek manuel olarak enjekte edebilirsiniz.
Örneğin, `Transistor` servisi tarafından istenen `$id` constructor argümanını elle sağlayabiliriz:

```php
use App\Services\Transistor;
 
$transistor = $this->app->makeWith(Transistor::class, ['id' => 1]);
```

`bound` metodu, bir sınıfın veya interface’in container’da açıkça kaydedilip kaydedilmediğini belirlemek için kullanılabilir:

```php
if ($this->app->bound(Transistor::class)) {
    // ...
}
```

Eğer kodunuzun `$app` değişkenine erişimi olmayan bir bölümündeyseniz (örneğin bir service provider dışında), container’dan bir sınıf örneğini çözümlemek için `App` facade’ını veya `app` helper’ını kullanabilirsiniz:

```php
use App\Services\Transistor;
use Illuminate\Support\Facades\App;
 
$transistor = App::make(Transistor::class);
 
$transistor = app(Transistor::class);
```

Container örneğinin kendisini çözülmekte olan bir sınıfa enjekte etmek isterseniz, sınıfın constructor’ında `Illuminate\Container\Container` sınıfını type-hint olarak belirtebilirsiniz:

```php
use Illuminate\Container\Container;
 
public function __construct(
    protected Container $container,
) {}
```

---

### Automatic Injection

Alternatif olarak ve önemli biçimde, container tarafından çözümlenen bir sınıfın constructor’ında (controller, event listener, middleware vb.) bağımlılıkları doğrudan type-hint olarak belirtebilirsiniz. Ayrıca, queued job’ların `handle` metodunda da bağımlılıkları type-hint edebilirsiniz.
Pratikte, nesnelerinizin çoğu container tarafından bu şekilde çözümlenmelidir.

Örneğin, bir controller’ın constructor’ında uygulamanızda tanımlı bir servisi type-hint olarak belirtebilirsiniz. Servis otomatik olarak çözülür ve sınıfa enjekte edilir:

```php
<?php
 
namespace App\Http\Controllers;
 
use App\Services\AppleMusic;
 
class PodcastController extends Controller
{
    /**
     * Yeni bir controller örneği oluştur.
     */
    public function __construct(
        protected AppleMusic $apple,
    ) {}
 
    /**
     * Verilen podcast hakkında bilgi göster.
     */
    public function show(string $id): Podcast
    {
        return $this->apple->findPodcast($id);
    }
}
```
````markdown
Method Invocation and Injection  
Bazen, bir nesne örneği üzerindeki bir metodu çağırmak isterken, container’ın bu metodun bağımlılıklarını otomatik olarak enjekte etmesini isteyebilirsiniz. Örneğin, aşağıdaki sınıfa bakalım:  

```php
<?php
 
namespace App;
 
use App\Services\AppleMusic;
 
class PodcastStats
{
    /**
     * Yeni bir podcast istatistik raporu oluştur.
     */
    public function generate(AppleMusic $apple): array
    {
        return [
            // ...
        ];
    }
}
````

`generate` metodunu container aracılığıyla şu şekilde çağırabilirsiniz:

```php
use App\PodcastStats;
use Illuminate\Support\Facades\App;
 
$stats = App::call([new PodcastStats, 'generate']);
```

`call` metodu herhangi bir PHP callable’ını kabul eder. Container’ın `call` metodu, bağımlılıkları otomatik olarak enjekte ederken bir closure’ı çağırmak için bile kullanılabilir:

```php
use App\Services\AppleMusic;
use Illuminate\Support\Facades\App;
 
$result = App::call(function (AppleMusic $apple) {
    // ...
});
```

---

### Container Events

Service container, her nesne çözümlendiğinde bir event tetikler. Bu olayı `resolving` metodu ile dinleyebilirsiniz:

```php
use App\Services\Transistor;
use Illuminate\Contracts\Foundation\Application;
 
$this->app->resolving(Transistor::class, function (Transistor $transistor, Application $app) {
    // Container "Transistor" türündeki nesneleri çözümlerken çağrılır...
});
 
$this->app->resolving(function (mixed $object, Application $app) {
    // Container herhangi bir türdeki nesneyi çözümlerken çağrılır...
});
```

Görüldüğü gibi, çözümlenen nesne callback’e geçirilir; böylece, nesne tüketicisine verilmeden önce üzerine ek özellikler atayabilirsiniz.

---

### Rebinding

`rebinding` metodu, bir servisin container’a yeniden bağlandığı (yani ilk kayıttan sonra yeniden kaydedildiği veya üzerine yazıldığı) durumlarda dinleme yapmanıza olanak tanır.
Bu, belirli bir binding her güncellendiğinde bağımlılıkları güncellemek veya davranışı değiştirmek istediğinizde yararlı olabilir:

```php
use App\Contracts\PodcastPublisher;
use App\Services\SpotifyPublisher;
use App\Services\TransistorPublisher;
use Illuminate\Contracts\Foundation\Application;
 
$this->app->bind(PodcastPublisher::class, SpotifyPublisher::class);
 
$this->app->rebinding(
    PodcastPublisher::class,
    function (Application $app, PodcastPublisher $newInstance) {
        //
    },
);
 
// Yeni binding rebinding callback’ini tetikleyecektir...
$this->app->bind(PodcastPublisher::class, TransistorPublisher::class);
```

---

### PSR-11

Laravel’in service container’ı PSR-11 arayüzünü (interface) uygular. Bu nedenle, Laravel container örneğini almak için PSR-11 container arayüzünü type-hint olarak kullanabilirsiniz:

```php
use App\Services\Transistor;
use Psr\Container\ContainerInterface;
 
Route::get('/', function (ContainerInterface $container) {
    $service = $container->get(Transistor::class);
 
    // ...
});
```

Eğer verilen tanımlayıcı (identifier) çözümlenemiyorsa, bir exception fırlatılır.
Tanımlayıcı hiç kaydedilmemişse, exception `Psr\Container\NotFoundExceptionInterface` türünde olur.
Tanımlayıcı kaydedilmiş ancak çözümlenememişse, `Psr\Container\ContainerExceptionInterface` türünde bir exception fırlatılır.





