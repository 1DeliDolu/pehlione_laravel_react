# Hata Yönetimi

## Giriş

Yeni bir Laravel projesi başlattığınızda, hata ve istisna (exception) yönetimi zaten sizin için yapılandırılmıştır; ancak, uygulamanızın `bootstrap/app.php` dosyasında `withExceptions` metodunu kullanarak, istisnaların nasıl raporlanacağını ve işlendiğini yönetebilirsiniz.

`withExceptions` closure’ına sağlanan `$exceptions` nesnesi, uygulamanızdaki istisna yönetiminden sorumlu olan `Illuminate\Foundation\Configuration\Exceptions` sınıfının bir örneğidir. Bu dokümantasyonda bu nesneyi daha derinlemesine inceleyeceğiz.

---

## Yapılandırma

`config/app.php` yapılandırma dosyanızdaki `debug` seçeneği, bir hataya ilişkin kullanıcıya ne kadar bilgi gösterileceğini belirler. Varsayılan olarak, bu seçenek `.env` dosyanızda saklanan `APP_DEBUG` ortam değişkeninin değerine göre ayarlanır.

Yerel geliştirme sırasında `APP_DEBUG` değişkenini `true` olarak ayarlamalısınız. Üretim ortamında ise bu değer her zaman `false` olmalıdır. Eğer üretimde bu değer `true` olarak ayarlanırsa, uygulamanızın son kullanıcılarına hassas yapılandırma bilgilerini ifşa etme riskiyle karşılaşırsınız.

---

## İstisnaları Yönetme

### İstisnaları Raporlama

Laravel’de istisna raporlama, istisnaların loglanması veya Sentry ya da Flare gibi harici bir servise gönderilmesi için kullanılır. Varsayılan olarak, istisnalar loglama yapılandırmanıza göre loglanır; ancak istisnaları istediğiniz şekilde raporlayabilirsiniz.

Belirli türdeki istisnaları farklı şekillerde raporlamak isterseniz, `bootstrap/app.php` dosyanızda `report` istisna metodunu kullanabilirsiniz. Laravel, closure’ın type-hint’ine bakarak hangi tür istisnayı raporlayacağını belirler:

```php
use App\Exceptions\InvalidOrderException;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->report(function (InvalidOrderException $e) {
        // ...
    });
})
```

Bir `report` callback’i tanımladığınızda, Laravel yine de istisnayı varsayılan loglama yapılandırmasına göre loglar. Ancak, istisnanın varsayılan loglama yığınına iletilmesini durdurmak isterseniz, `stop` metodunu kullanabilir veya callback’ten `false` döndürebilirsiniz:

```php
use App\Exceptions\InvalidOrderException;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->report(function (InvalidOrderException $e) {
        // ...
    })->stop();
 
    $exceptions->report(function (InvalidOrderException $e) {
        return false;
    });
})
```

Belirli bir istisna için özel raporlama davranışı oluşturmak isterseniz, **reportable exceptions** özelliğini de kullanabilirsiniz.

---

## Global Log Bağlamı

Eğer mevcutsa, Laravel otomatik olarak geçerli kullanıcının kimliğini her istisna log mesajına bağlamsal veri olarak ekler. Uygulamanızın `bootstrap/app.php` dosyasında `context` metodunu kullanarak kendi global bağlamsal verinizi tanımlayabilirsiniz. Bu bilgiler, uygulamanız tarafından yazılan her istisna log mesajına dahil edilecektir:

```php
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->context(fn () => [
        'foo' => 'bar',
    ]);
})
```

---

## İstisna Log Bağlamı

Her log mesajına bağlam eklemek yararlı olsa da, bazen belirli bir istisna için loglarınıza dahil etmek istediğiniz özel bağlam verileri olabilir. Uygulamanızdaki bir istisna sınıfına `context` metodu tanımlayarak, bu istisna ile ilgili ek verileri belirtebilirsiniz:

```php
<?php
 
namespace App\Exceptions;
 
use Exception;
 
class InvalidOrderException extends Exception
{
    /**
     * İstisnanın bağlam bilgilerini döndür.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return ['order_id' => $this->orderId];
    }
}
```

---

## `report` Helper’ı

Bazen bir istisnayı raporlamak isteyebilir, ancak mevcut isteği işlemeye devam etmek isteyebilirsiniz. `report` helper fonksiyonu, kullanıcıya hata sayfası göstermeden istisnayı hızlıca raporlamanıza olanak tanır:

```php
public function isValid(string $value): bool
{
    try {
        // Değeri doğrula...
    } catch (Throwable $e) {
        report($e);
 
        return false;
    }
}
```

---

## Raporlanan İstisnaların Yinelenmesini Önleme

Uygulamanız genelinde `report` fonksiyonunu sıkça kullanıyorsanız, aynı istisnayı birden fazla kez raporlayarak loglarda yinelenen girdiler oluşturabilirsiniz.

Bir istisna örneğinin yalnızca bir kez raporlanmasını sağlamak için, `bootstrap/app.php` dosyanızda `dontReportDuplicates` metodunu çağırabilirsiniz:

```php
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->dontReportDuplicates();
})
```

Artık aynı istisna örneğiyle `report` çağrıldığında, yalnızca ilk çağrı raporlanacaktır:

```php
$original = new RuntimeException('Whoops!');
 
report($original); // raporlandı
 
try {
    throw $original;
} catch (Throwable $caught) {
    report($caught); // yok sayıldı
}
 
report($original); // yok sayıldı
report($caught); // yok sayıldı
```

---

## İstisna Log Seviyeleri

Uygulamanız loglarına mesaj yazarken, bu mesajlar belirli bir **log seviyesi** ile yazılır. Bu seviye, mesajın önemini veya ciddiyetini belirtir.

Yukarıda belirtildiği gibi, özel bir `report` callback’i kaydettiğinizde bile, Laravel istisnayı varsayılan log yapılandırmasına göre loglar; ancak bazen belirli istisnalar için log seviyesini değiştirmek isteyebilirsiniz.

Bunu gerçekleştirmek için, `bootstrap/app.php` dosyanızda `level` metodunu kullanabilirsiniz. Bu metod, ilk parametre olarak istisna türünü, ikinci parametre olarak log seviyesini alır:

```php
use PDOException;
use Psr\Log\LogLevel;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->level(PDOException::class, LogLevel::CRITICAL);
})
```
# Türüne Göre İstisnaları Yoksayma

Uygulamanızı geliştirirken, bazı istisna türlerini hiçbir zaman raporlamak istemeyebilirsiniz. Bu istisnaları yoksaymak için, uygulamanızın `bootstrap/app.php` dosyasında `dontReport` metodunu kullanabilirsiniz. Bu metoda verilen herhangi bir sınıf asla raporlanmaz; ancak, yine de özel render (görselleştirme) mantığına sahip olabilir:

```php
use App\Exceptions\InvalidOrderException;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->dontReport([
        InvalidOrderException::class,
    ]);
})
```

Alternatif olarak, bir istisna sınıfını `Illuminate\Contracts\Debug\ShouldntReport` arayüzü ile “işaretleyebilirsiniz”. Bir istisna bu arayüzle işaretlendiğinde, Laravel’in istisna işleyicisi tarafından asla raporlanmaz:

```php
<?php
 
namespace App\Exceptions;
 
use Exception;
use Illuminate\Contracts\Debug\ShouldntReport;
 
class PodcastProcessingException extends Exception implements ShouldntReport
{
    //
}
```

Belirli bir türdeki istisnanın ne zaman yoksayılacağını daha ayrıntılı kontrol etmek isterseniz, `dontReportWhen` metoduna bir closure sağlayabilirsiniz:

```php
use App\Exceptions\InvalidOrderException;
use Throwable;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->dontReportWhen(function (Throwable $e) {
        return $e instanceof PodcastProcessingException &&
               $e->reason() === 'Subscription expired';
    });
})
```

Laravel dahili olarak bazı hata türlerini zaten sizin için yoksayar, örneğin 404 HTTP hatalarından veya geçersiz CSRF token’ları nedeniyle oluşturulan 419 HTTP yanıtlarından kaynaklanan istisnalar.
Laravel’in belirli bir istisna türünü yoksamayı bırakmasını istiyorsanız, `stopIgnoring` metodunu kullanabilirsiniz:

```php
use Symfony\Component\HttpKernel\Exception\HttpException;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->stopIgnoring(HttpException::class);
})
```

---

## İstisnaları Render Etme

Varsayılan olarak, Laravel istisna işleyicisi istisnaları sizin için bir HTTP yanıtına dönüştürür. Ancak, belirli türdeki istisnalar için özel render closure’ları kaydedebilirsiniz.
Bunu `bootstrap/app.php` dosyanızda `render` metodu ile gerçekleştirebilirsiniz.

`render` metoduna geçirilen closure, `Illuminate\Http\Response` örneğini döndürmelidir. Bu yanıt `response` helper’ı aracılığıyla oluşturulabilir. Laravel, closure’ın type-hint’ine bakarak hangi tür istisnayı render edeceğini belirler:

```php
use App\Exceptions\InvalidOrderException;
use Illuminate\Http\Request;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (InvalidOrderException $e, Request $request) {
        return response()->view('errors.invalid-order', status: 500);
    });
})
```

`render` metodunu ayrıca Laravel veya Symfony’de yerleşik olan `NotFoundHttpException` gibi istisnalar için render davranışını geçersiz kılmak amacıyla da kullanabilirsiniz.
Eğer `render` metoduna verilen closure herhangi bir değer döndürmezse, Laravel’in varsayılan render işlemi uygulanır:

```php
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (NotFoundHttpException $e, Request $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'message' => 'Record not found.'
            ], 404);
        }
    });
})
```

---

## İstisnaları JSON Olarak Render Etme

Bir istisna render edilirken, Laravel otomatik olarak isteğin `Accept` başlığına göre yanıtın HTML mi yoksa JSON olarak mı render edileceğini belirler.
Laravel’in HTML veya JSON istisna yanıtlarını nasıl seçeceğini özelleştirmek isterseniz, `shouldRenderJsonWhen` metodunu kullanabilirsiniz:

```php
use Illuminate\Http\Request;
use Throwable;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
        if ($request->is('admin/*')) {
            return true;
        }
 
        return $request->expectsJson();
    });
})
```

---

## İstisna Yanıtını Özelleştirme

Nadir durumlarda, Laravel’in istisna işleyicisinin oluşturduğu HTTP yanıtını tamamen özelleştirmeniz gerekebilir.
Bunu gerçekleştirmek için, `respond` metodunu kullanarak bir yanıt özelleştirme closure’ı kaydedebilirsiniz:

```php
use Symfony\Component\HttpFoundation\Response;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->respond(function (Response $response) {
        if ($response->getStatusCode() === 419) {
            return back()->with([
                'message' => 'The page expired, please try again.',
            ]);
        }
 
        return $response;
    });
})
```

---

## Raporlanabilir ve Render Edilebilir İstisnalar

Uygulamanızın `bootstrap/app.php` dosyasında özel raporlama ve render davranışları tanımlamak yerine, bunları doğrudan istisna sınıfınızın içinde `report` ve `render` metodlarıyla tanımlayabilirsiniz.
Bu metodlar mevcutsa, framework tarafından otomatik olarak çağrılır:

```php
<?php
 
namespace App\Exceptions;
 
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
 
class InvalidOrderException extends Exception
{
    /**
     * İstisnayı raporla.
     */
    public function report(): void
    {
        // ...
    }
 
    /**
     * İstisnayı bir HTTP yanıtı olarak render et.
     */
    public function render(Request $request): Response
    {
        return response(/* ... */);
    }
}
```

Eğer istisnanız zaten render edilebilir bir istisnayı (örneğin bir Laravel veya Symfony istisnasını) genişletiyorsa, istisnanın varsayılan HTTP yanıtını render etmek için `render` metodundan `false` döndürebilirsiniz:

```php
/**
 * İstisnayı bir HTTP yanıtı olarak render et.
 */
public function render(Request $request): Response|bool
{
    if (/** İstisnanın özel render edilmesi gerekip gerekmediğini belirle */) {
        return response(/* ... */);
    }
 
    return false;
}
```

Eğer istisnanız yalnızca belirli koşullarda gerekli olan özel raporlama mantığı içeriyorsa, Laravel’e istisnayı bazen varsayılan yapılandırmayla raporlamasını söylemek için `report` metodundan `false` döndürebilirsiniz:

```php
/**
 * İstisnayı raporla.
 */
public function report(): bool
{
    if (/** İstisnanın özel raporlama gerektirip gerektirmediğini belirle */) {
        // ...
        return true;
    }
 
    return false;
}
```

`report` metodunda ihtiyaç duyulan bağımlılıkları type-hint olarak belirtebilirsiniz; Laravel’in servis container’ı bu bağımlılıkları otomatik olarak metoda enjekte edecektir.

# Raporlanan İstisnaları Sınırlama (Throttling)

Eğer uygulamanız çok fazla sayıda istisna raporluyorsa, loglanan veya harici hata izleme servisine gönderilen istisna sayısını sınırlamak isteyebilirsiniz.

Rastgele bir örnekleme oranı belirlemek için, uygulamanızın `bootstrap/app.php` dosyasında `throttle` metodunu kullanabilirsiniz. Bu metod, bir `Lottery` örneği döndüren bir closure alır:

```php
use Illuminate\Support\Lottery;
use Throwable;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->throttle(function (Throwable $e) {
        return Lottery::odds(1, 1000);
    });
})
```

Belirli bir istisna türüne göre koşullu örnekleme yapmak da mümkündür. Sadece belirli bir istisna sınıfının örneklerini örneklemek istiyorsanız, yalnızca o sınıf için bir `Lottery` örneği döndürebilirsiniz:

```php
use App\Exceptions\ApiMonitoringException;
use Illuminate\Support\Lottery;
use Throwable;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->throttle(function (Throwable $e) {
        if ($e instanceof ApiMonitoringException) {
            return Lottery::odds(1, 1000);
        }
    });
})
```

İstisnaların loglanmasını veya harici hata izleme servisine gönderilmesini **oran sınırlamak** (rate limit) istiyorsanız, bir `Lottery` yerine bir `Limit` örneği döndürebilirsiniz. Bu, özellikle üçüncü taraf bir servis çöktüğünde loglarınızın birden fazla istisna ile dolmasını önlemek için yararlıdır:

```php
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Cache\RateLimiting\Limit;
use Throwable;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->throttle(function (Throwable $e) {
        if ($e instanceof BroadcastException) {
            return Limit::perMinute(300);
        }
    });
})
```

Varsayılan olarak, limitler istisnanın sınıf adını oran sınırlama anahtarı olarak kullanır. Bunu özelleştirmek için `Limit` üzerindeki `by` metodunu kullanarak kendi anahtarınızı belirleyebilirsiniz:

```php
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Cache\RateLimiting\Limit;
use Throwable;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->throttle(function (Throwable $e) {
        if ($e instanceof BroadcastException) {
            return Limit::perMinute(300)->by($e->getMessage());
        }
    });
})
```

Elbette, farklı istisnalar için `Lottery` ve `Limit` örneklerini karışık şekilde de döndürebilirsiniz:

```php
use App\Exceptions\ApiMonitoringException;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Lottery;
use Throwable;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->throttle(function (Throwable $e) {
        return match (true) {
            $e instanceof BroadcastException => Limit::perMinute(300),
            $e instanceof ApiMonitoringException => Lottery::odds(1, 1000),
            default => Limit::none(),
        };
    });
})
```

---

## HTTP İstisnaları

Bazı istisnalar, sunucudan gelen HTTP hata kodlarını temsil eder. Örneğin, bu bir **“sayfa bulunamadı” (404)** hatası, **“yetkisiz erişim” (401)** hatası veya bir geliştirici tarafından oluşturulan **500 hata kodu** olabilir.
Uygulamanızın herhangi bir yerinden bu tür bir yanıt oluşturmak için `abort` helper’ını kullanabilirsiniz:

```php
abort(404);
```

---

## Özel HTTP Hata Sayfaları

Laravel, çeşitli HTTP durum kodları için özel hata sayfaları oluşturmayı kolaylaştırır.
Örneğin, **404 HTTP hata kodu** için hata sayfasını özelleştirmek istiyorsanız, `resources/views/errors/404.blade.php` adlı bir view dosyası oluşturun.
Bu view, uygulamanız tarafından oluşturulan tüm 404 hataları için render edilir.

Bu dizindeki view dosyalarının adları, karşılık geldikleri HTTP durum koduyla aynı olmalıdır.
`abort` fonksiyonu tarafından oluşturulan `Symfony\Component\HttpKernel\Exception\HttpException` örneği, view’a `$exception` değişkeni olarak aktarılır:

```html
<h2>{{ $exception->getMessage() }}</h2>
```

Laravel’in varsayılan hata sayfa şablonlarını yayımlamak (publish) için `vendor:publish` Artisan komutunu kullanabilirsiniz.
Şablonlar yayımlandıktan sonra, bunları istediğiniz gibi özelleştirebilirsiniz:

```bash
php artisan vendor:publish --tag=laravel-errors
```

---

## Yedek (Fallback) HTTP Hata Sayfaları

Belirli bir HTTP durum kodu için sayfa bulunmadığında, genel bir “fallback” hata sayfası tanımlayabilirsiniz.
Bunu yapmak için, uygulamanızın `resources/views/errors` dizinine bir **4xx.blade.php** ve **5xx.blade.php** şablonu ekleyin.

Bu fallback sayfalar, belirli durum kodu için özel bir sayfa yoksa render edilir.
Ancak, Laravel’in 404, 500 ve 503 durum kodları için dahili özel sayfaları vardır; bu yüzden bu durum kodlarını özelleştirmek için her biri için ayrı bir özel sayfa tanımlamalısınız.
