# HTTP Yanıtları

## Yanıt Oluşturma

### Stringler ve Diziler

Tüm route'lar ve controller'lar, kullanıcının tarayıcısına geri gönderilecek bir yanıt döndürmelidir. Laravel, yanıt döndürmenin birkaç farklı yolunu sağlar. En basit yanıt, bir route veya controller'dan bir **string** döndürmektir. Framework, string'i otomatik olarak tam bir HTTP yanıtına dönüştürür:

```php
Route::get('/', function () {
    return 'Hello World';
});
```

Route’lardan veya controller’lardan string döndürmenin yanı sıra, diziler de döndürebilirsiniz. Framework, diziyi otomatik olarak bir **JSON** yanıtına dönüştürür:

```php
Route::get('/', function () {
    return [1, 2, 3];
});
```

Route veya controller’lardan **Eloquent koleksiyonları** da döndürebileceğinizi biliyor muydunuz? Bunlar otomatik olarak JSON’a dönüştürülür. Bir deneyin!

---

## Response Nesneleri

Genellikle, route işlemlerinizden yalnızca basit stringler veya diziler döndürmezsiniz. Bunun yerine, tam **Illuminate\Http\Response** örnekleri veya **view’ler** döndürürsünüz.

Tam bir Response örneği döndürmek, yanıtın HTTP durum kodunu ve başlıklarını (headers) özelleştirmenizi sağlar. Bir Response örneği, **Symfony\Component\HttpFoundation\Response** sınıfından miras alır ve HTTP yanıtları oluşturmak için çeşitli metodlar sunar:

```php
Route::get('/home', function () {
    return response('Hello World', 200)
        ->header('Content-Type', 'text/plain');
});
```

---

## Eloquent Modelleri ve Koleksiyonları

Route’larınızdan veya controller’larınızdan doğrudan **Eloquent ORM modelleri** ve **koleksiyonları** da döndürebilirsiniz. Bunu yaptığınızda Laravel, modelleri ve koleksiyonları modelin gizli (hidden) özelliklerine saygı göstererek otomatik olarak JSON yanıtlarına dönüştürür:

```php
use App\Models\User;
 
Route::get('/user/{user}', function (User $user) {
    return $user;
});
```

---

## Yanıtlara Header Ekleme

Çoğu response metodu **zincirlenebilir (chainable)** olduğundan, response örneklerini akıcı bir şekilde oluşturabilirsiniz. Örneğin, response’a bir dizi header eklemek için `header` metodunu kullanabilirsiniz:

```php
return response($content)
    ->header('Content-Type', $type)
    ->header('X-Header-One', 'Header Value')
    ->header('X-Header-Two', 'Header Value');
```

Veya, bir dizi header'ı tanımlamak için `withHeaders` metodunu kullanabilirsiniz:

```php
return response($content)
    ->withHeaders([
        'Content-Type' => $type,
        'X-Header-One' => 'Header Value',
        'X-Header-Two' => 'Header Value',
    ]);
```

---

## Cache Control Middleware

Laravel, bir grup route için hızlıca **Cache-Control** header’ını ayarlamak amacıyla kullanılabilen bir `cache.headers` middleware’i içerir. Yönergeler (“directives”), karşılık gelen cache-control yönergelerinin “snake case” eşdeğerleriyle belirtilmeli ve noktalı virgül ile ayrılmalıdır. Eğer `etag` yönergesi belirtilirse, yanıt içeriğinin bir MD5 hash’i otomatik olarak **ETag** tanımlayıcısı olarak ayarlanır:

```php
Route::middleware('cache.headers:public;max_age=2628000;etag')->group(function () {
    Route::get('/privacy', function () {
        // ...
    });
 
    Route::get('/terms', function () {
        // ...
    });
});
```

---

## Yanıtlara Çerez (Cookie) Ekleme

Bir **Illuminate\Http\Response** örneğine çerez eklemek için `cookie` metodunu kullanabilirsiniz. Bu metoda, çerezin adını, değerini ve geçerli olacağı dakika sayısını geçirmeniz gerekir:

```php
return response('Hello World')->cookie(
    'name', 'value', $minutes
);
```

`cookie` metodu ayrıca daha az sıklıkla kullanılan birkaç argüman daha kabul eder. Genellikle bu argümanlar, PHP’nin yerleşik `setcookie` metoduna verilen argümanlarla aynı amaca sahiptir:

```php
return response('Hello World')->cookie(
    'name', 'value', $minutes, $path, $domain, $secure, $httpOnly
);
```

Eğer bir response örneğiniz henüz yoksa ama çerezin gönderileceğinden emin olmak istiyorsanız, **Cookie facade**’ını kullanarak çerezleri “sıraya alabilirsiniz (queue)”. `queue` metodu, bir çerez örneği oluşturmak için gereken argümanları kabul eder. Bu çerezler, yanıt tarayıcıya gönderilmeden önce otomatik olarak eklenir:

```php
use Illuminate\Support\Facades\Cookie;
 
Cookie::queue('name', 'value', $minutes);
```

---

## Cookie Örnekleri Oluşturma

Daha sonra bir response örneğine eklenebilecek bir **Symfony\Component\HttpFoundation\Cookie** örneği oluşturmak isterseniz, global `cookie` helper’ını kullanabilirsiniz. Bu cookie, bir response’a eklenmediği sürece istemciye gönderilmez:

```php
$cookie = cookie('name', 'value', $minutes);
 
return response('Hello World')->cookie($cookie);
```

---

## Çerezleri Erken Süre Sonlandırma (Expiring)

Bir çerezi, çıkış yanıtında `withoutCookie` metodu aracılığıyla süresini dolarak kaldırabilirsiniz:

```php
return response('Hello World')->withoutCookie('name');
```

Henüz bir yanıt örneğiniz yoksa, **Cookie facade**’ının `expire` metodunu kullanarak bir çerezi süresini dolmuş hale getirebilirsiniz:

```php
Cookie::expire('name');
```

---

## Çerezler ve Şifreleme

Varsayılan olarak, **Illuminate\Cookie\Middleware\EncryptCookies** middleware’i sayesinde Laravel tarafından oluşturulan tüm çerezler **şifrelenir ve imzalanır**, böylece istemci tarafından okunamaz veya değiştirilemez.

Eğer uygulamanızda oluşturulan çerezlerin bir kısmı için şifrelemeyi devre dışı bırakmak isterseniz, `bootstrap/app.php` dosyanızda `encryptCookies` metodunu kullanabilirsiniz:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->encryptCookies(except: [
        'cookie_name',
    ]);
})
```
# Yönlendirmeler (Redirects)

Redirect yanıtları, kullanıcıyı başka bir URL'ye yönlendirmek için gerekli başlıkları içeren **Illuminate\Http\RedirectResponse** sınıfının örnekleridir.
Bir RedirectResponse örneği oluşturmanın birkaç yolu vardır. En basit yöntem, global **redirect** helper’ını kullanmaktır:

```php
Route::get('/dashboard', function () {
    return redirect('/home/dashboard');
});
```

Bazen, bir form geçersiz olduğunda olduğu gibi, kullanıcıyı **önceki konumuna** yönlendirmek isteyebilirsiniz.
Bunu, global **back** helper fonksiyonunu kullanarak yapabilirsiniz.
Bu özellik **session** kullandığından, `back` fonksiyonunu çağıran route’un **web middleware grubunu** kullandığından emin olun:

```php
Route::post('/user/profile', function () {
    // Validate the request...
 
    return back()->withInput();
});
```

---

## Named Route’lara Yönlendirme

`redirect` helper’ını parametresiz çağırdığınızda, bir **Illuminate\Routing\Redirector** örneği döner ve bu örnek üzerinden herhangi bir metodu çağırabilirsiniz.
Örneğin, bir **named route**’a yönlendirmek için `route` metodunu kullanabilirsiniz:

```php
return redirect()->route('login');
```

Route’unuz parametre alıyorsa, bu parametreleri `route` metoduna ikinci argüman olarak geçebilirsiniz:

```php
// Şu URI'ye sahip bir route için: /profile/{id}
return redirect()->route('profile', ['id' => 1]);
```

---

## Eloquent Modelleri ile Parametre Doldurma

Eğer bir route’a yönlendirme yaparken, “ID” parametresi bir **Eloquent model**’den geliyorsa, modelin kendisini doğrudan geçebilirsiniz.
Laravel, modelin ID’sini otomatik olarak alır:

```php
// Şu URI'ye sahip bir route için: /profile/{id}
return redirect()->route('profile', [$user]);
```

Route parametresine yerleştirilecek değeri özelleştirmek isterseniz, bunu ya route tanımında belirtebilirsiniz (`/profile/{id:slug}`),
ya da modelinizdeki `getRouteKey` metodunu override edebilirsiniz:

```php
/**
 * Modelin route anahtarının değerini döndür.
 */
public function getRouteKey(): mixed
{
    return $this->slug;
}
```

---

## Controller Aksiyonlarına Yönlendirme

Controller aksiyonlarına yönlendirme yapmak da mümkündür.
Bunu yapmak için, `action` metoduna controller ve aksiyon adını geçin:

```php
use App\Http\Controllers\UserController;
 
return redirect()->action([UserController::class, 'index']);
```

Controller route’unuz parametre gerektiriyorsa, bunları `action` metoduna ikinci argüman olarak iletebilirsiniz:

```php
return redirect()->action(
    [UserController::class, 'profile'], ['id' => 1]
);
```

---

## Harici Domainlere Yönlendirme

Bazen uygulamanızın dışındaki bir **domain’e yönlendirme** yapmanız gerekebilir.
Bunu, URL üzerinde herhangi bir kodlama, doğrulama veya kontrol yapılmadan bir RedirectResponse oluşturan `away` metodunu çağırarak yapabilirsiniz:

```php
return redirect()->away('https://www.google.com');
```

---

## Session Verisiyle Yönlendirme (Flashed Session Data)

Yeni bir URL’ye yönlendirme yapmak ve **veriyi session’a flash’lemek** genellikle aynı anda yapılır.
Bu genellikle başarılı bir işlemden sonra **başarı mesajı** göstermek istediğinizde kullanılır.
Kolaylık olması için, bir RedirectResponse örneği oluşturup session verisini tek bir zincirleme (fluent) metod çağrısıyla flash edebilirsiniz:

```php
Route::post('/user/profile', function () {
    // ...
 
    return redirect('/dashboard')->with('status', 'Profile updated!');
});
```

Kullanıcı yönlendirildikten sonra, flash’lenen mesajı session’dan görüntüleyebilirsiniz.
Örneğin, Blade sözdizimi ile:

```blade
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
```

---

## Input Verisiyle Yönlendirme

Kullanıcının girdiği verileri kaybetmeden yönlendirme yapmak için `withInput` metodunu kullanabilirsiniz.
Bu metod, geçerli isteğin input verisini session’a flash’lar ve kullanıcıyı yeni bir konuma yönlendirir.
Genellikle kullanıcı bir doğrulama hatasıyla karşılaştığında kullanılır.
Veri session’a flash’landıktan sonra, bir sonraki istekte formu yeniden doldurmak için kolayca erişilebilir:

```php
return back()->withInput();
```

# Diğer Yanıt Türleri (Other Response Types)

`response` helper'ı, farklı türlerde yanıt örnekleri oluşturmak için kullanılabilir.
Bu helper argümansız çağrıldığında, **Illuminate\Contracts\Routing\ResponseFactory** sözleşmesinin bir implementasyonunu döndürür.
Bu sözleşme, çeşitli yararlı yanıt oluşturma metodlarını sağlar.

---

## View Yanıtları

Eğer yanıtın durum kodunu (status) ve başlıklarını (headers) kontrol etmek istiyorsanız ancak aynı zamanda yanıt içeriği olarak bir **view** döndürmeniz gerekiyorsa, `view` metodunu kullanabilirsiniz:

```php
return response()
    ->view('hello', $data, 200)
    ->header('Content-Type', $type);
```

Tabii ki özel bir HTTP durum kodu veya başlık belirtmenize gerek yoksa, global **view** helper fonksiyonunu doğrudan kullanabilirsiniz.

---

## JSON Yanıtları

`json` metodu, **Content-Type** başlığını otomatik olarak `application/json` olarak ayarlar ve verilen diziyi PHP’nin `json_encode` fonksiyonunu kullanarak JSON’a dönüştürür:

```php
return response()->json([
    'name' => 'Abigail',
    'state' => 'CA',
]);
```

Eğer bir **JSONP** yanıtı oluşturmak isterseniz, `json` metodunu `withCallback` metodu ile birlikte kullanabilirsiniz:

```php
return response()
    ->json(['name' => 'Abigail', 'state' => 'CA'])
    ->withCallback($request->input('callback'));
```

---

## Dosya İndirme (File Downloads)

`download` metodu, kullanıcının tarayıcısının belirli bir dosyayı **indirmesini zorlayan** bir yanıt oluşturmak için kullanılabilir.
Bu metodun ikinci argümanı, kullanıcının göreceği dosya adını belirler.
Ayrıca üçüncü argüman olarak HTTP başlıklarını içeren bir dizi geçebilirsiniz:

```php
return response()->download($pathToFile);

return response()->download($pathToFile, $name, $headers);
```

Laravel’in kullandığı **Symfony HttpFoundation** bileşeni, indirilen dosya adının **ASCII karakterlerinden** oluşmasını gerektirir.

---

## Dosya Yanıtları (File Responses)

`file` metodu, bir dosyayı (örneğin bir **resim** veya **PDF**) indirilmeye zorlamak yerine **doğrudan tarayıcıda görüntülemek** için kullanılabilir.
Bu metodun ilk argümanı dosyanın tam yolu, ikinci argümanı ise başlık dizisidir:

```php
return response()->file($pathToFile);

return response()->file($pathToFile, $headers);
```

---

## Akış (Streamed) Yanıtlar

Veriyi oluşturuldukça istemciye göndermek, özellikle çok büyük yanıtlar için **bellek kullanımını azaltır** ve **performansı artırır**.
Streamed yanıtlar sayesinde istemci, sunucu tüm veriyi göndermeden önce işlemeye başlayabilir:

```php
Route::get('/stream', function () {
    return response()->stream(function (): void {
        foreach (['developer', 'admin'] as $string) {
            echo $string;
            ob_flush();
            flush();
            sleep(2); // Parçalar arasında gecikme simülasyonu...
        }
    }, 200, ['X-Accel-Buffering' => 'no']);
});
```

Kolaylık açısından, eğer `stream` metoduna verdiğiniz closure bir **Generator** döndürüyorsa, Laravel her döngü arasında çıkış arabelleğini (output buffer) otomatik olarak temizler ve **Nginx çıktı arabelleğini devre dışı bırakır**:

```php
Route::post('/chat', function () {
    return response()->stream(function (): Generator {
        $stream = OpenAI::client()->chat()->createStreamed(...);
 
        foreach ($stream as $response) {
            yield $response->choices[0];
        }
    });
});
```

---

## Streamed Yanıtları Tüketme (Consuming Streamed Responses)

Streamed yanıtlar, Laravel’in **stream npm paketi** aracılığıyla tüketilebilir.
Bu paket, Laravel response ve event stream’leriyle etkileşim için kullanışlı bir API sağlar.
Başlamak için aşağıdaki paketlerden birini kurun:

### React

```bash
npm install @laravel/stream-react
```

### Vue

```bash
npm install @laravel/stream-vue
```

---

## useStream Hook Kullanımı (React Örneği)

`useStream` hook’u, stream URL’inizi belirledikten sonra, dönen içeriğe göre `data` değişkenini otomatik olarak günceller:

```jsx
import { useStream } from "@laravel/stream-react";
 
function App() {
    const { data, isFetching, isStreaming, send } = useStream("chat");
 
    const sendMessage = () => {
        send({
            message: `Current timestamp: ${Date.now()}`,
        });
    };
 
    return (
        <div>
            <div>{data}</div>
            {isFetching && <div>Connecting...</div>}
            {isStreaming && <div>Generating...</div>}
            <button onClick={sendMessage}>Send Message</button>
        </div>
    );
}
```

`send` fonksiyonu aracılığıyla stream’e veri gönderildiğinde, mevcut bağlantı iptal edilir ve yeni veri gönderilir.
Tüm istekler **JSON POST** istekleri olarak gönderilir.

---

## CSRF Token Gereksinimi

`useStream` hook’u uygulamanıza bir POST isteği gönderdiği için, geçerli bir **CSRF token** gereklidir.
En kolay yöntem, layout’unuzun `<head>` kısmına bir **meta tag** eklemektir.

---

## useStream Opsiyonları

`useStream`’e verilen ikinci argüman, davranışı özelleştirmek için bir **options nesnesidir**.
Varsayılan değerler aşağıdaki gibidir:

```jsx
import { useStream } from "@laravel/stream-react";
 
function App() {
    const { data } = useStream("chat", {
        id: undefined,
        initialInput: undefined,
        headers: undefined,
        csrfToken: undefined,
        onResponse: (response: Response) => void,
        onData: (data: string) => void,
        onCancel: () => void,
        onFinish: () => void,
        onError: (error: Error) => void,
    });
 
    return <div>{data}</div>;
}
```

* **onResponse** → Akıştan başarılı bir ilk yanıt alındığında tetiklenir.
* **onData** → Her veri parçası alındığında çağrılır.
* **onFinish** → Akış tamamlandığında veya bir hata oluştuğunda çağrılır.

---

## Başlangıç Verisi Gönderme

Varsayılan olarak, stream başlatıldığında hemen bir istek yapılmaz.
İlk gönderiyi başlatmak istiyorsanız, `initialInput` seçeneğini kullanabilirsiniz:

```jsx
import { useStream } from "@laravel/stream-react";
 
function App() {
    const { data } = useStream("chat", {
        initialInput: {
            message: "Introduce yourself.",
        },
    });
 
    return <div>{data}</div>;
}
```

---

## Akışı Manuel Olarak İptal Etme

Bir stream’i manuel olarak iptal etmek için, hook’tan dönen `cancel` metodunu kullanabilirsiniz:

```jsx
import { useStream } from "@laravel/stream-react";
 
function App() {
    const { data, cancel } = useStream("chat");
 
    return (
        <div>
            <div>{data}</div>
            <button onClick={cancel}>Cancel</button>
        </div>
    );
}
```

---

## Stream ID Paylaşımı

Her `useStream` çağrısında rastgele bir **id** üretilir ve bu kimlik, her istekle birlikte **X-STREAM-ID** başlığına eklenir.
Aynı stream’i birden fazla bileşende (component) paylaşmak istiyorsanız, kendi `id`’nizi sağlayabilirsiniz:

```jsx
// App.tsx
import { useStream } from "@laravel/stream-react";
 
function App() {
    const { data, id } = useStream("chat");
 
    return (
        <div>
            <div>{data}</div>
            <StreamStatus id={id} />
        </div>
    );
}
 
// StreamStatus.tsx
import { useStream } from "@laravel/stream-react";
 
function StreamStatus({ id }) {
    const { isFetching, isStreaming } = useStream("chat", { id });
 
    return (
        <div>
            {isFetching && <div>Connecting...</div>}
            {isStreaming && <div>Generating...</div>}
        </div>
    );
}
```
# Akışlı JSON Yanıtları (Streamed JSON Responses)

Eğer JSON verisini **kademeli olarak (parça parça)** göndermeniz gerekiyorsa, `streamJson` metodunu kullanabilirsiniz.
Bu yöntem, özellikle **tarayıcıya kademeli olarak gönderilmesi gereken büyük veri kümeleri** için faydalıdır ve JavaScript tarafından kolayca ayrıştırılabilir bir biçimde veri gönderir:

```php
use App\Models\User;
 
Route::get('/users.json', function () {
    return response()->streamJson([
        'users' => User::cursor(),
    ]);
});
```

---

## useJsonStream Hook (React)

`useJsonStream` hook’u, `useStream` ile aynıdır ancak akış tamamlandıktan sonra veriyi **JSON olarak ayrıştırmaya** çalışır:

```jsx
import { useJsonStream } from "@laravel/stream-react";
 
type User = {
    id: number;
    name: string;
    email: string;
};
 
function App() {
    const { data, send } = useJsonStream<{ users: User[] }>("users");
 
    const loadUsers = () => {
        send({
            query: "taylor",
        });
    };
 
    return (
        <div>
            <ul>
                {data?.users.map((user) => (
                    <li>
                        {user.id}: {user.name}
                    </li>
                ))}
            </ul>
            <button onClick={loadUsers}>Load Users</button>
        </div>
    );
}
```

---

# Event Streams (SSE)

`eventStream` metodu, **text/event-stream** içerik türünü kullanarak **server-sent events (SSE)** akış yanıtları döndürmek için kullanılır.
Bu metod, bir **closure** kabul eder ve yanıtlar kullanıma hazır hale geldikçe bunları akışa **yield** eder:

```php
Route::get('/chat', function () {
    return response()->eventStream(function () {
        $stream = OpenAI::client()->chat()->createStreamed(...);
 
        foreach ($stream as $response) {
            yield $response->choices[0];
        }
    });
});
```

Eğer olayın (event) adını özelleştirmek istiyorsanız, `StreamedEvent` sınıfının bir örneğini yield edebilirsiniz:

```php
use Illuminate\Http\StreamedEvent;
 
yield new StreamedEvent(
    event: 'update',
    data: $response->choices[0],
);
```

---

## Event Stream’leri Tüketme (Consuming Event Streams)

Event stream’ler, Laravel’in **@laravel/stream** npm paketi ile tüketilebilir.
Bu paket, Laravel event stream’leriyle etkileşim kurmak için kolay bir API sunar.

### Kurulum

```bash
npm install @laravel/stream-react
```

---

## useEventStream Hook (React Örneği)

`useEventStream` hook’u, stream URL’inizi aldıktan sonra gelen mesajları otomatik olarak **birleştirerek günceller**:

```jsx
import { useEventStream } from "@laravel/stream-react";
 
function App() {
  const { message } = useEventStream("/chat");
 
  return <div>{message}</div>;
}
```

---

## useEventStream Seçenekleri

`useEventStream`’e verilen ikinci argüman, davranışı özelleştirmek için kullanılabilir bir **options nesnesidir**:

```jsx
import { useEventStream } from "@laravel/stream-react";
 
function App() {
  const { message } = useEventStream("/stream", {
    eventName: "update",
    onMessage: (message) => {
      // Mesaj alındığında
    },
    onError: (error) => {
      // Hata durumunda
    },
    onComplete: () => {
      // Akış tamamlandığında
    },
    endSignal: "</stream>",
    glue: " ",
  });
 
  return <div>{message}</div>;
}
```

---

## EventSource ile Manuel Kullanım

Event stream’ler, uygulamanızın frontend’inde yerleşik **EventSource** nesnesiyle de tüketilebilir.
`eventStream` metodu, akış tamamlandığında otomatik olarak `</stream>` mesajını gönderir:

```js
const source = new EventSource('/chat');
 
source.addEventListener('update', (event) => {
    if (event.data === '</stream>') {
        source.close();
        return;
    }
 
    console.log(event.data);
});
```

Son olarak gönderilen olayın içeriğini özelleştirmek isterseniz, `eventStream` metodunun `endStreamWith` argümanına bir `StreamedEvent` örneği sağlayabilirsiniz:

```php
return response()->eventStream(function () {
    // ...
}, endStreamWith: new StreamedEvent(event: 'update', data: '</stream>'));
```

---

# Akışlı İndirmeler (Streamed Downloads)

Bazen, bir işlemin ürettiği string yanıtı **diske yazmadan** indirme yanıtına dönüştürmek isteyebilirsiniz.
Bu durumda `streamDownload` metodunu kullanabilirsiniz.
Bu metod bir **callback**, bir **dosya adı** ve isteğe bağlı bir **header dizisi** alır:

```php
use App\Services\GitHub;
 
return response()->streamDownload(function () {
    echo GitHub::api('repo')
        ->contents()
        ->readme('laravel', 'laravel')['contents'];
}, 'laravel-readme.md');
```

---

# Yanıt Makroları (Response Macros)

Birçok route ve controller’da yeniden kullanılabilecek özel yanıtlar tanımlamak istiyorsanız, `Response` facade’ındaki `macro` metodunu kullanabilirsiniz.
Genellikle bu metod, uygulamanızın servis sağlayıcılarından birinin `boot` metodunda (örneğin `App\Providers\AppServiceProvider`) çağrılmalıdır:

```php
<?php
 
namespace App\Providers;
 
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
 
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('caps', function (string $value) {
            return Response::make(strtoupper($value));
        });
    }
}
```

`macro` fonksiyonu, ilk argüman olarak makronun adını, ikinci argüman olarak ise bir **closure** kabul eder.
Bu closure, ResponseFactory implementasyonu veya `response` helper’ı üzerinden makro çağrıldığında çalıştırılır:

```php
return response()->caps('foo');
```
