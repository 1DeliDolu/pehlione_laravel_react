
Middleware  
Giriş  
Middleware, uygulamanıza giren HTTP isteklerini incelemek ve filtrelemek için kullanışlı bir mekanizma sağlar. Örneğin, Laravel, uygulamanızın kullanıcısının kimliğini doğrulayan bir middleware içerir. Eğer kullanıcı kimliği doğrulanmamışsa, middleware kullanıcıyı uygulamanızın giriş ekranına yönlendirir. Ancak, kullanıcı kimliği doğrulanmışsa, middleware isteğin uygulamaya daha ileriye gitmesine izin verir.

Kimlik doğrulamanın yanı sıra çeşitli görevleri gerçekleştirmek için ek middleware’ler yazılabilir. Örneğin, bir logging middleware, uygulamanıza gelen tüm istekleri kaydedebilir. Laravel, kimlik doğrulama ve CSRF koruması için çeşitli middleware’ler içerir; ancak, kullanıcı tanımlı tüm middleware’ler genellikle uygulamanızın app/Http/Middleware dizininde bulunur.

Middleware Tanımlama  
Yeni bir middleware oluşturmak için make:middleware Artisan komutunu kullanın:

php artisan make:middleware EnsureTokenIsValid  

Bu komut, app/Http/Middleware dizininizde yeni bir EnsureTokenIsValid sınıfı oluşturur. Bu middleware’de, yalnızca verilen token belirli bir değere eşleştiğinde route’a erişime izin vereceğiz. Aksi halde, kullanıcıyı /home URI’sine yönlendireceğiz:

```php
<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->input('token') !== 'my-secret-token') {
            return redirect('/home');
        }
 
        return $next($request);
    }
}
````

Gördüğünüz gibi, verilen token gizli token ile eşleşmezse, middleware istemciye bir HTTP yönlendirmesi döndürür; aksi takdirde, istek uygulamaya daha ileriye iletilir. İsteği uygulamaya daha derin iletmek için (middleware’in “geçmesine” izin vermek için) $next callback’ini $request ile çağırmanız gerekir.

Middleware’leri, HTTP isteklerinin uygulamanıza ulaşmadan önce geçmesi gereken bir dizi “katman” olarak düşünmek en iyisidir. Her katman isteği inceleyebilir ve hatta tamamen reddedebilir.

Tüm middleware’ler service container aracılığıyla çözülür, bu nedenle bir middleware’in constructor’ına ihtiyaç duyduğunuz bağımlılıkları type-hint olarak ekleyebilirsiniz.

Middleware ve Response’lar
Elbette, bir middleware isteği uygulamaya iletmeden önce veya sonra bazı görevleri gerçekleştirebilir. Örneğin, aşağıdaki middleware, istek uygulama tarafından işlenmeden önce bir işlem yapar:

```php
<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class BeforeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // İşlem yap
 
        return $next($request);
    }
}
```

Ancak bu middleware, işlemini istek uygulama tarafından işlendiğinde sonra gerçekleştirir:

```php
<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class AfterMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
 
        // İşlem yap
 
        return $response;
    }
}
```

Middleware Kaydetme
Global Middleware
Bir middleware’in uygulamanıza yapılan her HTTP isteğinde çalışmasını istiyorsanız, uygulamanızın bootstrap/app.php dosyasındaki global middleware listesine ekleyebilirsiniz:

```php
use App\Http\Middleware\EnsureTokenIsValid;
 
->withMiddleware(function (Middleware $middleware): void {
     $middleware->append(EnsureTokenIsValid::class);
})
```

withMiddleware closure’ına sağlanan $middleware nesnesi, Illuminate\Foundation\Configuration\Middleware örneğidir ve uygulamanızın rotalarına atanan middleware’leri yönetmekten sorumludur. append yöntemi, middleware’i global middleware listesinin sonuna ekler. Middleware’i listenin başına eklemek istiyorsanız prepend yöntemini kullanmalısınız.

Laravel’in Varsayılan Global Middleware’lerini Manuel Yönetme
Laravel’in global middleware yığınını manuel olarak yönetmek istiyorsanız, use metoduna Laravel’in varsayılan global middleware yığınını sağlayabilirsiniz. Ardından, varsayılan middleware yığınını gerektiği gibi ayarlayabilirsiniz:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->use([
        \Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class,
        // \Illuminate\Http\Middleware\TrustHosts::class,
        \Illuminate\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ]);
})
```

Rotalara Middleware Atama
Belirli rotalara middleware atamak istiyorsanız, route tanımlarken middleware metodunu çağırabilirsiniz:

```php
use App\Http\Middleware\EnsureTokenIsValid;
 
Route::get('/profile', function () {
    // ...
})->middleware(EnsureTokenIsValid::class);
```

Bir route’a birden fazla middleware atamak için, middleware metoduna bir dizi olarak geçebilirsiniz:

```php
Route::get('/', function () {
    // ...
})->middleware([First::class, Second::class]);
```

Middleware Hariç Tutma
Bir grup rotaya middleware atarken, bazen bu middleware’in belirli bir rota için uygulanmamasını isteyebilirsiniz. Bunu withoutMiddleware metodu ile gerçekleştirebilirsiniz:

```php
use App\Http\Middleware\EnsureTokenIsValid;
 
Route::middleware([EnsureTokenIsValid::class])->group(function () {
    Route::get('/', function () {
        // ...
    });
 
    Route::get('/profile', function () {
        // ...
    })->withoutMiddleware([EnsureTokenIsValid::class]);
});
```

Ayrıca belirli bir middleware setini bir grup rotadan tamamen hariç tutabilirsiniz:

```php
use App\Http\Middleware\EnsureTokenIsValid;
 
Route::withoutMiddleware([EnsureTokenIsValid::class])->group(function () {
    Route::get('/profile', function () {
        // ...
    });
});
```

withoutMiddleware metodu yalnızca route middleware’lerini kaldırabilir ve global middleware’ler için geçerli değildir.

Middleware Grupları
Bazen birkaç middleware’i tek bir anahtar altında gruplamak isteyebilirsiniz. Bu, rotalara atamayı kolaylaştırır. Bunu bootstrap/app.php dosyanızda appendToGroup metodunu kullanarak yapabilirsiniz:

```php
use App\Http\Middleware\First;
use App\Http\Middleware\Second;
 
->withMiddleware(function (Middleware $middleware): void {
    $middleware->appendToGroup('group-name', [
        First::class,
        Second::class,
    ]);
 
    $middleware->prependToGroup('group-name', [
        First::class,
        Second::class,
    ]);
})
```

Middleware grupları, bireysel middleware’lerde olduğu gibi rotalara atanabilir:

```php
Route::get('/', function () {
    // ...
})->middleware('group-name');
 
Route::middleware(['group-name'])->group(function () {
    // ...
});
```

Laravel’in Varsayılan Middleware Grupları
Laravel, web ve api olmak üzere iki önceden tanımlı middleware grubu içerir. Bu gruplar, web ve API rotalarınız için uygulanacak yaygın middleware’leri içerir. Laravel bu middleware gruplarını routes/web.php ve routes/api.php dosyalarına otomatik olarak uygular.

**web Middleware Grubu**

* Illuminate\Cookie\Middleware\EncryptCookies
* Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse
* Illuminate\Session\Middleware\StartSession
* Illuminate\View\Middleware\ShareErrorsFromSession
* Illuminate\Foundation\Http\Middleware\ValidateCsrfToken
* Illuminate\Routing\Middleware\SubstituteBindings

**api Middleware Grubu**

* Illuminate\Routing\Middleware\SubstituteBindings

Bu gruplara middleware eklemek veya önüne eklemek istiyorsanız web ve api metodlarını kullanabilirsiniz:

```php
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Middleware\EnsureUserIsSubscribed;
 
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        EnsureUserIsSubscribed::class,
    ]);
 
    $middleware->api(prepend: [
        EnsureTokenIsValid::class,
    ]);
})
```

Bir middleware’i tamamen değiştirmek veya kaldırmak da mümkündür:

```php
use App\Http\Middleware\StartCustomSession;
use Illuminate\Session\Middleware\StartSession;
 
$middleware->web(replace: [
    StartSession::class => StartCustomSession::class,
]);

$middleware->web(remove: [
    StartSession::class,
]);
```

Middleware Takma Adları (Aliases)
Middleware’lere takma adlar atayabilirsiniz. Bu, uzun sınıf adlarına sahip middleware’ler için oldukça kullanışlıdır:

```php
use App\Http\Middleware\EnsureUserIsSubscribed;
 
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'subscribed' => EnsureUserIsSubscribed::class
    ]);
})
```

Bu alias tanımlandıktan sonra, route middleware atamalarında kullanılabilir:

```php
Route::get('/profile', function () {
    // ...
})->middleware('subscribed');
```

Laravel’in bazı varsayılan middleware’leri zaten alias olarak tanımlanmıştır, örneğin `auth` middleware’i `Illuminate\Auth\Middleware\Authenticate` sınıfına karşılık gelir.

**Varsayılan Middleware Alias’ları:**

| Alias            | Middleware                                                                                                  |
| ---------------- | ----------------------------------------------------------------------------------------------------------- |
| auth             | Illuminate\Auth\Middleware\Authenticate                                                                     |
| auth.basic       | Illuminate\Auth\Middleware\AuthenticateWithBasicAuth                                                        |
| auth.session     | Illuminate\Session\Middleware\AuthenticateSession                                                           |
| cache.headers    | Illuminate\Http\Middleware\SetCacheHeaders                                                                  |
| can              | Illuminate\Auth\Middleware\Authorize                                                                        |
| guest            | Illuminate\Auth\Middleware\RedirectIfAuthenticated                                                          |
| password.confirm | Illuminate\Auth\Middleware\RequirePassword                                                                  |
| precognitive     | Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests                                            |
| signed           | Illuminate\Routing\Middleware\ValidateSignature                                                             |
| subscribed       | \Spark\Http\Middleware\VerifyBillableIsSubscribed                                                           |
| throttle         | Illuminate\Routing\Middleware\ThrottleRequests veya Illuminate\Routing\Middleware\ThrottleRequestsWithRedis |
| verified         | Illuminate\Auth\Middleware\EnsureEmailIsVerified                                                            |

Middleware Parametreleri
Middleware’ler ek parametreler de alabilir. Örneğin, kimliği doğrulanmış kullanıcının belirli bir “role”e sahip olup olmadığını kontrol eden bir EnsureUserHasRole middleware oluşturabilirsiniz:

```php
<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()->hasRole($role)) {
            // Yönlendir...
        }
 
        return $next($request);
    }
}
```

Middleware parametreleri, middleware adı ve parametreleri “:” ile ayırarak route tanımlarken belirtilebilir:

```php
use App\Http\Middleware\EnsureUserHasRole;
 
Route::put('/post/{id}', function (string $id) {
    // ...
})->middleware(EnsureUserHasRole::class.':editor');
```

Birden fazla parametre, virgül ile ayrılabilir:

```php
Route::put('/post/{id}', function (string $id) {
    // ...
})->middleware(EnsureUserHasRole::class.':editor,publisher');
```


Terminable Middleware  
Bazen bir middleware, HTTP yanıtı tarayıcıya gönderildikten sonra bazı işlemler yapmak isteyebilir. Eğer middleware’inizde bir terminate metodu tanımlarsanız ve web sunucunuz FastCGI kullanıyorsa, terminate metodu yanıt tarayıcıya gönderildikten sonra otomatik olarak çağrılacaktır:

```php
<?php
 
namespace Illuminate\Session\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class TerminatingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
 
    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        // ...
    }
}
````

terminate metodu hem request’i hem de response’u almalıdır. Bir terminable middleware tanımladıktan sonra, bunu uygulamanızın bootstrap/app.php dosyasındaki route veya global middleware listesine eklemelisiniz.

Laravel, middleware’in terminate metodunu çağırırken, middleware’in yeni bir örneğini service container üzerinden çözer. handle ve terminate metodları çağrıldığında aynı middleware örneğini kullanmak istiyorsanız, middleware’i container’a singleton olarak kaydedin. Genellikle bu işlem AppServiceProvider’ın register metodunda yapılmalıdır:

```php
use App\Http\Middleware\TerminatingMiddleware;
 
/**
 * Register any application services.
 */
public function register(): void
{
    $this->app->singleton(TerminatingMiddleware::class);
}
```

