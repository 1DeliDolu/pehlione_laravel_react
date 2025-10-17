# HTTP Client



<br>




## Giriş

Laravel, diğer web uygulamalarıyla iletişim kurmak için hızlı bir şekilde HTTP istekleri yapmanı sağlayan, Guzzle HTTP client etrafında ifade gücü yüksek ve minimal bir API sunar. Laravel’in Guzzle için oluşturduğu sarmalayıcı (wrapper), en yaygın kullanım senaryolarına ve harika bir geliştirici deneyimine odaklanır.

<br>




## İstek Gönderme

İstek göndermek için `Http` facade’ı tarafından sağlanan `head`, `get`, `post`, `put`, `patch` ve `delete` metotlarını kullanabilirsin. Öncelikle, başka bir URL’ye temel bir GET isteği nasıl yapılır bakalım:

```php
use Illuminate\Support\Facades\Http;
 
$response = Http::get('http://example.com');
````

`get` metodu, yanıtı incelemek için çeşitli metotlar sunan `Illuminate\Http\Client\Response` sınıfının bir örneğini döndürür:

```php
$response->body() : string;
$response->json($key = null, $default = null) : mixed;
$response->object() : object;
$response->collect($key = null) : Illuminate\Support\Collection;
$response->resource() : resource;
$response->status() : int;
$response->successful() : bool;
$response->redirect(): bool;
$response->failed() : bool;
$response->clientError() : bool;
$response->header($header) : string;
$response->headers() : array;
```

`Illuminate\Http\Client\Response` nesnesi ayrıca PHP’nin `ArrayAccess` arayüzünü uygular, bu da JSON yanıt verilerine doğrudan erişmeni sağlar:

```php
return Http::get('http://example.com/users/1')['name'];
```

Yukarıdaki yanıtlara ek olarak, belirli bir HTTP durum kodunu kontrol etmek için aşağıdaki metotlar kullanılabilir:

```php
$response->ok() : bool;                  // 200 OK
$response->created() : bool;             // 201 Created
$response->accepted() : bool;            // 202 Accepted
$response->noContent() : bool;           // 204 No Content
$response->movedPermanently() : bool;    // 301 Moved Permanently
$response->found() : bool;               // 302 Found
$response->badRequest() : bool;          // 400 Bad Request
$response->unauthorized() : bool;        // 401 Unauthorized
$response->paymentRequired() : bool;     // 402 Payment Required
$response->forbidden() : bool;           // 403 Forbidden
$response->notFound() : bool;            // 404 Not Found
$response->requestTimeout() : bool;      // 408 Request Timeout
$response->conflict() : bool;            // 409 Conflict
$response->unprocessableEntity() : bool; // 422 Unprocessable Entity
$response->tooManyRequests() : bool;     // 429 Too Many Requests
$response->serverError() : bool;         // 500 Internal Server Error
```

<br>




## URI Şablonları

HTTP client, URI template spesifikasyonunu kullanarak istek URL’leri oluşturmanı da sağlar. Genişletilecek URL parametrelerini tanımlamak için `withUrlParameters` metodunu kullanabilirsin:

```php
Http::withUrlParameters([
    'endpoint' => 'https://laravel.com',
    'page' => 'docs',
    'version' => '12.x',
    'topic' => 'validation',
])->get('{+endpoint}/{page}/{version}/{topic}');
```

<br>




## İstekleri Görüntüleme (Dumping Requests)

Gönderilmeden önce giden isteği görmek ve betiğin çalışmasını durdurmak istersen, isteğinin başına `dd` metodunu ekleyebilirsin:

```php
return Http::dd()->get('http://example.com');
```

<br>




## İstek Verileri (Request Data)

Elbette, `POST`, `PUT` ve `PATCH` istekleri yaparken isteğe ek veri göndermek yaygındır. Bu metotlar ikinci argüman olarak bir dizi alır. Varsayılan olarak, veriler `application/json` içerik tipiyle gönderilir:

```php
use Illuminate\Support\Facades\Http;
 
$response = Http::post('http://example.com/users', [
    'name' => 'Steve',
    'role' => 'Network Administrator',
]);
```

<br>




## GET İstekleri ve Sorgu Parametreleri

GET istekleri yaparken, sorgu dizisini (query string) doğrudan URL’ye ekleyebilir veya `get` metoduna ikinci argüman olarak bir anahtar/değer dizisi verebilirsin:

```php
$response = Http::get('http://example.com/users', [
    'name' => 'Taylor',
    'page' => 1,
]);
```

Alternatif olarak `withQueryParameters` metodu kullanılabilir:

```php
Http::retry(3, 100)->withQueryParameters([
    'name' => 'Taylor',
    'page' => 1,
])->get('http://example.com/users');
```

<br>




## Form URL Kodlu İstekler Gönderme

Verileri `application/x-www-form-urlencoded` içerik tipiyle göndermek istersen, isteğini yapmadan önce `asForm` metodunu çağırmalısın:

```php
$response = Http::asForm()->post('http://example.com/users', [
    'name' => 'Sara',
    'role' => 'Privacy Consultant',
]);
```

<br>




## Ham (Raw) İstek Gövdesi Gönderme

Ham bir istek gövdesi göndermek istersen, `withBody` metodunu kullanabilirsin. İçerik tipi metodun ikinci argümanında belirtilir:

```php
$response = Http::withBody(
    base64_encode($photo), 'image/jpeg'
)->post('http://example.com/photo');
```

<br>




## Çok Parçalı (Multi-Part) İstekler

Dosyaları çok parçalı istekler olarak göndermek istersen, isteği yapmadan önce `attach` metodunu kullanmalısın. Bu metod dosya adını ve içeriğini kabul eder. Gerekirse, üçüncü argüman dosya adı, dördüncü argüman ise dosya ile ilişkili başlıklar (headers) olarak kullanılabilir:

```php
$response = Http::attach(
    'attachment', file_get_contents('photo.jpg'), 'photo.jpg', ['Content-Type' => 'image/jpeg']
)->post('http://example.com/attachments');
```

Dosya içeriği yerine bir stream kaynağı da gönderebilirsin:

```php
$photo = fopen('photo.jpg', 'r');
 
$response = Http::attach(
    'attachment', $photo, 'photo.jpg'
)->post('http://example.com/attachments');
```

<br>




## Başlıklar (Headers)

İsteklere başlık eklemek için `withHeaders` metodunu kullanabilirsin. Bu metod bir anahtar/değer dizisi alır:

```php
$response = Http::withHeaders([
    'X-First' => 'foo',
    'X-Second' => 'bar'
])->post('http://example.com/users', [
    'name' => 'Taylor',
]);
```

Uygulamanın yanıt olarak hangi içerik tipini beklediğini belirtmek için `accept` metodunu kullanabilirsin:

```php
$response = Http::accept('application/json')->get('http://example.com/users');
```

Kolaylık olması açısından, `application/json` beklediğini belirtmek için `acceptJson` metodunu da kullanabilirsin:

```php
$response = Http::acceptJson()->get('http://example.com/users');
```

`withHeaders` metodu, yeni başlıkları mevcut başlıklarla birleştirir. Tüm başlıkları tamamen değiştirmek istersen, `replaceHeaders` metodunu kullanabilirsin:

```php
$response = Http::withHeaders([
    'X-Original' => 'foo',
])->replaceHeaders([
    'X-Replacement' => 'bar',
])->post('http://example.com/users', [
    'name' => 'Taylor',
]);
```

<br>




## Kimlik Doğrulama (Authentication)

Temel veya özet kimlik doğrulama bilgilerini sırasıyla `withBasicAuth` ve `withDigestAuth` metotlarıyla belirtebilirsin:

```php
// Basic authentication...
$response = Http::withBasicAuth('taylor@laravel.com', 'secret')->post(/* ... */);
 
// Digest authentication...
$response = Http::withDigestAuth('taylor@laravel.com', 'secret')->post(/* ... */);
```

<br>




## Bearer Token’lar

İsteğin `Authorization` başlığına hızlıca bir bearer token eklemek istersen, `withToken` metodunu kullanabilirsin:

```php
$response = Http::withToken('token')->post(/* ... */);
```

<br>




## Zaman Aşımı (Timeout)

`timeout` metodu, bir yanıt için beklenilecek maksimum saniye sayısını belirtmek için kullanılır. Varsayılan olarak HTTP client 30 saniye sonra zaman aşımına uğrar:

```php
$response = Http::timeout(3)->get(/* ... */);
```

Belirtilen süre aşılırsa, bir `Illuminate\Http\Client\ConnectionException` örneği fırlatılır.

Sunucuya bağlanmaya çalışırken beklenilecek maksimum saniye sayısını belirtmek için `connectTimeout` metodunu kullanabilirsin. Varsayılan değer 10 saniyedir:

```php
$response = Http::connectTimeout(3)->get(/* ... */);
```



<br>




## Yeniden Denemeler (Retries)

HTTP client’in, istemci veya sunucu hatası oluştuğunda isteği otomatik olarak yeniden denemesini istiyorsan, `retry` metodunu kullanabilirsin. `retry` metodu, isteğin kaç kez yeniden denenmesi gerektiğini ve denemeler arasında Laravel’in kaç milisaniye beklemesi gerektiğini belirtir:

```php
$response = Http::retry(3, 100)->post(/* ... */);
````

Denemeler arasındaki bekleme süresini manuel olarak hesaplamak istersen, `retry` metoduna ikinci argüman olarak bir closure verebilirsin:

```php
use Exception;
 
$response = Http::retry(3, function (int $attempt, Exception $exception) {
    return $attempt * 100;
})->post(/* ... */);
```

Kolaylık olması için, `retry` metodunun ilk argümanı olarak bir dizi de verebilirsin. Bu dizi, denemeler arasındaki bekleme sürelerini belirler:

```php
$response = Http::retry([100, 200])->post(/* ... */);
```

Gerekirse, `retry` metoduna üçüncü bir argüman geçebilirsin. Bu argüman, yeniden denemelerin gerçekten yapılıp yapılmayacağını belirleyen bir callable’dır. Örneğin, isteğin yalnızca `ConnectionException` oluştuğunda yeniden denenmesini isteyebilirsin:

```php
use Exception;
use Illuminate\Http\Client\PendingRequest;
 
$response = Http::retry(3, 100, function (Exception $exception, PendingRequest $request) {
    return $exception instanceof ConnectionException;
})->post(/* ... */);
```

Bir istek başarısız olduğunda, yeni bir deneme yapılmadan önce isteği değiştirmek isteyebilirsin. Bu, `retry` metoduna sağladığın callable içindeki `request` argümanını değiştirerek yapılabilir. Örneğin, ilk denemede kimlik doğrulama hatası alınırsa, isteği yeni bir token ile yeniden denemek isteyebilirsin:

```php
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
 
$response = Http::withToken($this->getToken())->retry(2, 0, function (Exception $exception, PendingRequest $request) {
    if (! $exception instanceof RequestException || $exception->response->status() !== 401) {
        return false;
    }
 
    $request->withToken($this->getNewToken());
 
    return true;
})->post(/* ... */);
```

Tüm denemeler başarısız olursa, bir `Illuminate\Http\Client\RequestException` fırlatılır. Bu davranışı devre dışı bırakmak istersen, `throw` argümanını `false` olarak belirtebilirsin. Bu durumda, tüm denemeler tamamlandıktan sonra son alınan yanıt döndürülür:

```php
$response = Http::retry(3, 100, throw: false)->post(/* ... */);
```

Ancak, tüm istekler bağlantı hatası nedeniyle başarısız olursa, `throw` argümanı `false` olsa bile bir `Illuminate\Http\Client\ConnectionException` fırlatılır.

<br>




## Hata Yönetimi (Error Handling)

Guzzle’ın varsayılan davranışının aksine, Laravel’in HTTP client sarmalayıcısı istemci veya sunucu hatalarında (400 ve 500 seviyesindeki yanıtlar) istisna fırlatmaz. Bu tür hataların oluşup oluşmadığını aşağıdaki metotlarla belirleyebilirsin:

```php
// Durum kodu 200 <= x < 300 aralığında mı?
$response->successful();
 
// Durum kodu 400 veya daha büyük mü?
$response->failed();
 
// 400 seviyesinde bir hata var mı?
$response->clientError();
 
// 500 seviyesinde bir hata var mı?
$response->serverError();
 
// Hata oluştuysa anında callback çalıştır...
$response->onError(callable $callback);
```

<br>




## İstisna Fırlatma (Throwing Exceptions)

Eğer bir yanıt örneğin varsa ve durum kodu bir istemci veya sunucu hatası gösteriyorsa, bir `Illuminate\Http\Client\RequestException` fırlatmak için `throw` veya `throwIf` metodunu kullanabilirsin:

```php
use Illuminate\Http\Client\Response;
 
$response = Http::post(/* ... */);
 
// Hata varsa istisna fırlat...
$response->throw();
 
// Belirli bir koşul doğruysa istisna fırlat...
$response->throwIf($condition);
 
// Closure true dönerse istisna fırlat...
$response->throwIf(fn (Response $response) => true);
 
// Koşul false ise istisna fırlat...
$response->throwUnless($condition);
 
// Closure false dönerse istisna fırlat...
$response->throwUnless(fn (Response $response) => false);
 
// Belirli bir durum kodu varsa istisna fırlat...
$response->throwIfStatus(403);
 
// Belirli bir durum kodu yoksa istisna fırlat...
$response->throwUnlessStatus(200);
 
return $response['user']['id'];
```

`Illuminate\Http\Client\RequestException` nesnesinin, dönen yanıtı incelemeni sağlayan genel bir `$response` özelliği vardır.

`throw` metodu hata oluşmazsa yanıt örneğini döndürür, bu sayede zincirleme işlemler yapabilirsin:

```php
return Http::post(/* ... */)->throw()->json();
```

İstisna fırlatılmadan önce ek bir mantık çalıştırmak istersen, `throw` metoduna bir closure geçebilirsin. Closure çalıştırıldıktan sonra istisna otomatik olarak fırlatılır, yani closure içinden tekrar `throw` etmen gerekmez:

```php
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\RequestException;
 
return Http::post(/* ... */)->throw(function (Response $response, RequestException $e) {
    // ...
})->json();
```

Varsayılan olarak, `RequestException` mesajları loglanırken veya raporlanırken 120 karaktere kadar kısaltılır. Bu davranışı özelleştirmek veya devre dışı bırakmak için, `bootstrap/app.php` dosyanda `truncateRequestExceptionsAt` veya `dontTruncateRequestExceptions` metotlarını kullanabilirsin:

```php
use Illuminate\Foundation\Configuration\Exceptions;
 
->withExceptions(function (Exceptions $exceptions): void {
    // İstisna mesajlarını 240 karaktere kadar kısalt...
    $exceptions->truncateRequestExceptionsAt(240);
 
    // Kısaltmayı devre dışı bırak...
    $exceptions->dontTruncateRequestExceptions();
})
```

Alternatif olarak, bu davranışı istek bazında özelleştirmek için `truncateExceptionsAt` metodunu kullanabilirsin:

```php
return Http::truncateExceptionsAt(240)->post(/* ... */);
```

<br>




## Guzzle Middleware

Laravel’in HTTP client’i Guzzle üzerine kurulu olduğundan, giden isteği veya gelen yanıtı değiştirmek için Guzzle Middleware kullanabilirsin. Giden isteği değiştirmek için `withRequestMiddleware` metodunu kullanarak bir middleware kaydedebilirsin:

```php
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;
 
$response = Http::withRequestMiddleware(
    function (RequestInterface $request) {
        return $request->withHeader('X-Example', 'Value');
    }
)->get('http://example.com');
```

Benzer şekilde, gelen HTTP yanıtını incelemek için `withResponseMiddleware` metodunu kullanabilirsin:

```php
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;
 
$response = Http::withResponseMiddleware(
    function (ResponseInterface $response) {
        $header = $response->getHeader('X-Example');
 
        // ...
 
        return $response;
    }
)->get('http://example.com');
```

<br>




## Global Middleware

Bazen, her giden isteğe veya gelen yanıta uygulanacak bir middleware kaydetmek isteyebilirsin. Bunu yapmak için `globalRequestMiddleware` ve `globalResponseMiddleware` metotlarını kullanabilirsin. Bu metotlar genellikle uygulamanın `AppServiceProvider` sınıfındaki `boot` metodunda çağrılmalıdır:

```php
use Illuminate\Support\Facades.Http;
 
Http::globalRequestMiddleware(fn ($request) => $request->withHeader(
    'User-Agent', 'Example Application/1.0'
));
 
Http::globalResponseMiddleware(fn ($response) => $response->withHeader(
    'X-Finished-At', now()->toDateTimeString()
));
```

<br>




## Guzzle Seçenekleri (Guzzle Options)

Giden isteğe ek Guzzle seçenekleri belirtmek için `withOptions` metodunu kullanabilirsin. Bu metod, anahtar/değer çiftlerinden oluşan bir dizi alır:

```php
$response = Http::withOptions([
    'debug' => true,
])->get('http://example.com/users');
```

### Global Seçenekler

Tüm giden istekler için varsayılan seçenekleri yapılandırmak istersen, `globalOptions` metodunu kullanabilirsin. Bu metod da genellikle `AppServiceProvider` içindeki `boot` metodunda çağrılır:

```php
use Illuminate\Support\Facades\Http;
 
/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Http::globalOptions([
        'allow_redirects' => false,
    ]);
}
```

<br>




## Eşzamanlı (Concurrent) İstekler

Bazen, birden fazla HTTP isteğini aynı anda göndermek isteyebilirsin. Yani isteklerin ardışık olarak değil, eşzamanlı olarak gönderilmesini istersin. Bu, yavaş çalışan HTTP API’lerle etkileşimde büyük performans kazançları sağlayabilir.

<br>




### İstek Havuzu (Request Pooling)

Bunu `pool` metodu ile yapabilirsin. `pool` metodu, bir `Illuminate\Http\Client\Pool` örneği alan bir closure kabul eder ve havuza kolayca istek eklemeni sağlar:

```php
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
 
$responses = Http::pool(fn (Pool $pool) => [
    $pool->get('http://localhost/first'),
    $pool->get('http://localhost/second'),
    $pool->get('http://localhost/third'),
]);
 
return $responses[0]->ok() &&
       $responses[1]->ok() &&
       $responses[2]->ok();
```

Her yanıt, havuza eklendiği sıraya göre erişilebilir. Eğer istekleri adlandırmak istersen, `as` metodunu kullanabilir ve yanıtları isimleriyle alabilirsin:

```php
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
 
$responses = Http::pool(fn (Pool $pool) => [
    $pool->as('first')->get('http://localhost/first'),
    $pool->as('second')->get('http://localhost/second'),
    $pool->as('third')->get('http://localhost/third'),
]);
 
return $responses['first']->ok();
```

<br>




### Eşzamanlı İstekleri Özelleştirme

`pool` metodu, `withHeaders` veya `middleware` gibi diğer HTTP client metotlarıyla zincirlenemez. Eğer havuzdaki isteklere özel başlıklar veya middleware eklemek istersen, bunları her bir isteğin üzerinde yapılandırmalısın:

```php
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
 
$headers = [
    'X-Example' => 'example',
];
 
$responses = Http::pool(fn (Pool $pool) => [
    $pool->withHeaders($headers)->get('http://laravel.test/test'),
    $pool->withHeaders($headers)->get('http://laravel.test/test'),
    $pool->withHeaders($headers)->get('http://laravel.test/test'),
]);
```

<br>




## İstek Gruplama (Request Batching)

Eşzamanlı isteklerle çalışmanın bir diğer yolu `batch` metodunu kullanmaktır. `pool` metoduna benzer şekilde, bu metod bir `Illuminate\Http\Client\Batch` örneği alan bir closure kabul eder. Ancak, `batch` ayrıca tamamlanma callback’leri tanımlamana da olanak tanır:

```php
use Illuminate\Http\Client\Batch;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
 
$responses = Http::batch(fn (Batch $batch) => [
    $batch->get('http://localhost/first'),
    $batch->get('http://localhost/second'),
    $batch->get('http://localhost/third'),
])->before(function (Batch $batch) {
    // Batch oluşturuldu ama henüz başlatılmadı...
})->progress(function (Batch $batch, int|string $key, Response $response) {
    // Bireysel bir istek başarıyla tamamlandı...
})->then(function (Batch $batch, array $results) {
    // Tüm istekler başarıyla tamamlandı...
})->catch(function (Batch $batch, int|string $key, Response|RequestException|ConnectionException $response) {
    // İlk batch isteği başarısız oldu...
})->finally(function (Batch $batch, array $results) {
    // Batch tamamlandı...
})->send();
```

Tıpkı `pool` metodunda olduğu gibi, istekleri adlandırmak için `as` metodunu kullanabilirsin:

```php
$responses = Http::batch(fn (Batch $batch) => [
    $batch->as('first')->get('http://localhost/first'),
    $batch->as('second')->get('http://localhost/second'),
    $batch->as('third')->get('http://localhost/third'),
])->send();
```

`send` metodu çağrıldıktan sonra batch’e yeni istek ekleyemezsin. Aksi halde bir `Illuminate\Http\Client\BatchInProgressException` fırlatılır.

<br>




### Batch’leri İnceleme

`batch` tamamlama callback’lerine verilen `Illuminate\Http\Client\Batch` örneği, batch ile ilgili etkileşimde bulunmanı ve inceleme yapmanı sağlayan çeşitli özellikler ve metotlara sahiptir:

```php
$batch->totalRequests;      // Batch’e atanmış toplam istek sayısı
$batch->pendingRequests;    // Henüz işlenmemiş istek sayısı
$batch->failedRequests;     // Başarısız olan istek sayısı
$batch->processedRequests(); // Şimdiye kadar işlenen istek sayısı
$batch->finished();          // Batch tamamlandı mı?
$batch->hasFailures();       // Batch içinde hata var mı?
```

<br>




### Batch’leri Erteleme (Deferring Batches)

`defer` metodu çağrıldığında, batch hemen yürütülmez. Bunun yerine Laravel, uygulamanın HTTP yanıtı kullanıcıya gönderildikten sonra batch’i çalıştırır. Bu, uygulamanın daha hızlı ve duyarlı hissettirmesini sağlar:

```php
use Illuminate\Http\Client\Batch;
use Illuminate\Support\Facades.Http;
 
$responses = Http::batch(fn (Batch $batch) => [
    $batch->get('http://localhost/first'),
    $batch->get('http://localhost/second'),
    $batch->get('http://localhost/third'),
])->then(function (Batch $batch, array $results) {
    // Tüm istekler başarıyla tamamlandı...
})->defer();
```

<br>




## Makrolar (Macros)

Laravel HTTP client, uygulama genelinde servislerle etkileşimde bulunurken ortak istek yolları ve başlıkları yapılandırmak için akıcı, ifadeli bir mekanizma olan “macro” tanımlamana olanak tanır. Başlamak için, makronu uygulamanın `App\Providers\AppServiceProvider` sınıfındaki `boot` metodunda tanımlayabilirsin:

```php
use Illuminate\Support\Facades\Http;
 
/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Http::macro('github', function () {
        return Http::withHeaders([
            'X-Example' => 'example',
        ])->baseUrl('https://github.com');
    });
}
```

Makro yapılandırıldıktan sonra, uygulamanın herhangi bir yerinden belirlenen yapılandırmayla bir istek başlatmak için çağırabilirsin:

```php
$response = Http::github()->get('/');
```

```
```


<br>




## Test Etme (Testing)

Laravel’ın birçok servisi, testleri kolay ve ifadeli bir şekilde yazmanı sağlar ve Laravel’in HTTP client’i de bu konuda bir istisna değildir. `Http` facade’ının `fake` metodu, HTTP client’e yapılan isteklerde sahte (stub / dummy) yanıtlar döndürmesini söylemeni sağlar.

<br>




## Sahte Yanıtlar (Faking Responses)

Örneğin, tüm isteklere boş ve 200 durum koduna sahip yanıtlar döndürülmesini istiyorsan, `fake` metodunu argümansız olarak çağırabilirsin:

```php
use Illuminate\Support\Facades\Http;
 
Http::fake();
 
$response = Http::post(/* ... */);
````

<br>




## Belirli URL’leri Sahteleme (Faking Specific URLs)

Alternatif olarak, `fake` metoduna bir dizi geçebilirsin. Dizinin anahtarları sahteleyeceğin URL desenlerini, değerleri ise bu desenlere ait yanıtları temsil eder. `*` karakteri joker (wildcard) olarak kullanılabilir. Bu endpoint’ler için sahte yanıtlar oluşturmak adına `Http` facade’ının `response` metodunu kullanabilirsin:

```php
Http::fake([
    // GitHub endpoint’leri için sahte JSON yanıt...
    'github.com/*' => Http::response(['foo' => 'bar'], 200, $headers),
 
    // Google endpoint’leri için sahte string yanıt...
    'google.com/*' => Http::response('Hello World', 200, $headers),
]);
```

Sahte olarak tanımlanmamış URL’lere yapılan istekler gerçekten yürütülür. Tüm eşleşmeyen URL’leri yakalayacak bir varsayılan desen belirtmek istersen, tek bir `*` karakteri kullanabilirsin:

```php
Http::fake([
    'github.com/*' => Http::response(['foo' => 'bar'], 200, ['Headers']),
    '*' => Http::response('Hello World', 200, ['Headers']),
]);
```

Kolaylık olması için, sadece string, dizi veya integer vererek basit string, JSON veya boş yanıtlar da oluşturabilirsin:

```php
Http::fake([
    'google.com/*' => 'Hello World',
    'github.com/*' => ['foo' => 'bar'],
    'chatgpt.com/*' => 200,
]);
```

<br>




## Sahte İstisnalar (Faking Exceptions)

Bazen HTTP client’in bir `Illuminate\Http\Client\ConnectionException` hatasıyla karşılaştığında uygulamanın davranışını test etmek isteyebilirsin. Bunun için `failedConnection` metodunu kullanabilirsin:

```php
Http::fake([
    'github.com/*' => Http::failedConnection(),
]);
```

Bir `Illuminate\Http\Client\RequestException` fırlatılmasını test etmek istersen, `failedRequest` metodunu kullanabilirsin:

```php
Http::fake([
    'github.com/*' => Http::failedRequest(['code' => 'not_found'], 404),
]);
```

<br>




## Yanıt Dizilerini Sahteleme (Faking Response Sequences)

Bazen tek bir URL’nin belirli bir sırada bir dizi sahte yanıt döndürmesini isteyebilirsin. Bunu `Http::sequence` metodu ile yapabilirsin:

```php
Http::fake([
    'github.com/*' => Http::sequence()
        ->push('Hello World', 200)
        ->push(['foo' => 'bar'], 200)
        ->pushStatus(404),
]);
```

Dizideki tüm yanıtlar tükendiğinde, sonraki istekler istisna fırlatır. Dizi boş kaldığında döndürülmesi gereken varsayılan bir yanıt belirtmek istersen, `whenEmpty` metodunu kullanabilirsin:

```php
Http::fake([
    'github.com/*' => Http::sequence()
        ->push('Hello World', 200)
        ->push(['foo' => 'bar'], 200)
        ->whenEmpty(Http::response()),
]);
```

Eğer belirli bir URL deseni belirtmeden yanıt dizisi oluşturmak istersen, `Http::fakeSequence` metodunu kullanabilirsin:

```php
Http::fakeSequence()
    ->push('Hello World', 200)
    ->whenEmpty(Http::response());
```

<br>




## Sahte Callback (Fake Callback)

Belirli endpoint’ler için dönecek yanıtı belirlemek üzere daha karmaşık bir mantığa ihtiyacın varsa, `fake` metoduna bir closure geçebilirsin. Bu closure bir `Illuminate\Http\Client\Request` örneği alır ve bir yanıt döndürmelidir:

```php
use Illuminate\Http\Client\Request;
 
Http::fake(function (Request $request) {
    return Http::response('Hello World', 200);
});
```

<br>




## İstekleri İnceleme (Inspecting Requests)

Sahte yanıtlar kullanırken, bazen client’in aldığı istekleri inceleyip doğru verilerin veya başlıkların gönderildiğinden emin olmak isteyebilirsin. Bunu `Http::fake` çağrısından sonra `Http::assertSent` metodu ile yapabilirsin.

`assertSent` metodu, bir `Illuminate\Http\Client\Request` örneği alacak bir closure kabul eder ve istek beklentilere uyuyorsa `true` döndürmelidir. Testin geçmesi için en az bir istek bu beklentilere uymalıdır:

```php
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
 
Http::fake();
 
Http::withHeaders([
    'X-First' => 'foo',
])->post('http://example.com/users', [
    'name' => 'Taylor',
    'role' => 'Developer',
]);
 
Http::assertSent(function (Request $request) {
    return $request->hasHeader('X-First', 'foo') &&
           $request->url() == 'http://example.com/users' &&
           $request['name'] == 'Taylor' &&
           $request['role'] == 'Developer';
});
```

Belirli bir isteğin **gönderilmediğini** doğrulamak istersen, `assertNotSent` metodunu kullanabilirsin:

```php
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
 
Http::fake();
 
Http::post('http://example.com/users', [
    'name' => 'Taylor',
    'role' => 'Developer',
]);
 
Http::assertNotSent(function (Request $request) {
    return $request->url() === 'http://example.com/posts';
});
```

Gönderilen isteklerin sayısını doğrulamak için `assertSentCount` metodunu kullanabilirsin:

```php
Http::fake();
 
Http::assertSentCount(5);
```

Ya da hiçbir isteğin gönderilmediğini doğrulamak için `assertNothingSent` metodunu kullanabilirsin:

```php
Http::fake();
 
Http::assertNothingSent();
```

<br>




## İstek / Yanıt Kaydı (Recording Requests / Responses)

Tüm istekleri ve bunlara karşılık gelen yanıtları toplamak için `recorded` metodunu kullanabilirsin. Bu metod, her biri bir `Illuminate\Http\Client\Request` ve bir `Illuminate\Http\Client\Response` örneği içeren dizilerden oluşan bir koleksiyon döndürür:

```php
Http::fake([
    'https://laravel.com' => Http::response(status: 500),
    'https://nova.laravel.com/' => Http::response(),
]);
 
Http::get('https://laravel.com');
Http::get('https://nova.laravel.com/');
 
$recorded = Http::recorded();
 
[$request, $response] = $recorded[0];
```

Ayrıca, `recorded` metoduna bir closure geçerek yalnızca belirli istek / yanıt çiftlerini filtreleyebilirsin:

```php
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
 
Http::fake([
    'https://laravel.com' => Http::response(status: 500),
    'https://nova.laravel.com/' => Http::response(),
]);
 
Http::get('https://laravel.com');
Http::get('https://nova.laravel.com/');
 
$recorded = Http::recorded(function (Request $request, Response $response) {
    return $request->url() !== 'https://laravel.com' &&
           $response->successful();
});
```

<br>




## Rastgele İstekleri Engelleme (Preventing Stray Requests)

Testin veya test paketinin tamamında HTTP client üzerinden gönderilen tüm isteklerin sahte olmasını sağlamak istersen, `preventStrayRequests` metodunu kullanabilirsin. Bu metod çağrıldıktan sonra, sahte yanıtı olmayan her istek gerçek HTTP isteği yapmak yerine istisna fırlatır:

```php
use Illuminate\Support\Facades.Http;
 
Http::preventStrayRequests();
 
Http::fake([
    'github.com/*' => Http::response('ok'),
]);
 
// "ok" yanıtı döner...
Http::get('https://github.com/laravel/framework');
 
// İstisna fırlatılır...
Http::get('https://laravel.com');
```

Bazen, çoğu istek engellenirken belirli isteklerin yürütülmesine izin vermek isteyebilirsin. Bunun için `allowStrayRequests` metoduna URL desenlerinden oluşan bir dizi geçebilirsin:

```php
use Illuminate\Support\Facades.Http;
 
Http::preventStrayRequests();
 
Http::allowStrayRequests([
    'http://127.0.0.1:5000/*',
]);
 
// Bu istek yürütülür...
Http::get('http://127.0.0.1:5000/generate');
 
// İstisna fırlatılır...
Http::get('https://laravel.com');
```

<br>




## Olaylar (Events)

Laravel, HTTP isteklerinin gönderilmesi sürecinde üç olay (event) tetikler.

* `RequestSending` olayı, bir istek gönderilmeden **önce** tetiklenir.
* `ResponseReceived` olayı, bir yanıt **alındıktan sonra** tetiklenir.
* `ConnectionFailed` olayı, yanıt alınamadığında tetiklenir.

`RequestSending` ve `ConnectionFailed` olayları, bir `Illuminate\Http\Client\Request` örneğini incelemeni sağlayan genel bir `$request` özelliği içerir.
`ResponseReceived` olayı ise hem `$request` hem de `$response` özelliklerine sahiptir, bu sayede `Illuminate\Http\Client\Response` örneğini de inceleyebilirsin.

Bu olaylar için dinleyiciler (listeners) oluşturabilirsin:

```php
use Illuminate\Http\Client\Events\RequestSending;
 
class LogRequest
{
    /**
     * Olayı işleme al.
     */
    public function handle(RequestSending $event): void
    {
        // $event->request ...
    }
}
```

---

**Laravel**, yazılım geliştirme, dağıtım ve izleme süreçlerini yönetmenin en üretken yoludur.

```
```
