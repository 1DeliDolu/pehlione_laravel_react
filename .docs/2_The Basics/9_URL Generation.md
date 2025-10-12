# URL Oluşturma

## Giriş

Laravel, uygulamanız için URL’ler oluşturmanıza yardımcı olacak birkaç yardımcı işlev (helper) sağlar. Bu yardımcılar, özellikle şablonlarda ve API yanıtlarında bağlantılar oluştururken veya uygulamanızın başka bir bölümüne yönlendirme (redirect) yanıtları üretirken kullanışlıdır.

---

## Temeller

### URL’ler Oluşturma

`url` yardımcı işlevi, uygulamanız için rastgele URL’ler oluşturmak için kullanılabilir. Oluşturulan URL, uygulamanın şu anda işlediği isteğe ait şema (HTTP veya HTTPS) ve ana bilgisayarı (host) otomatik olarak kullanacaktır:

```php
$post = App\Models\Post::find(1);
 
echo url("/posts/{$post->id}");
 
// http://example.com/posts/1
```

Sorgu dizesi (query string) parametreleri içeren bir URL oluşturmak için `query` metodunu kullanabilirsiniz:

```php
echo url()->query('/posts', ['search' => 'Laravel']);
// https://example.com/posts?search=Laravel
 
echo url()->query('/posts?sort=latest', ['search' => 'Laravel']);
// http://example.com/posts?sort=latest&search=Laravel
```

Yol (path) üzerinde zaten var olan sorgu parametreleri, sağladığınız yeni değerle değiştirilir:

```php
echo url()->query('/posts?sort=latest', ['sort' => 'oldest']);
// http://example.com/posts?sort=oldest
```

Dizi değerleri de sorgu parametreleri olarak geçilebilir. Bu değerler doğru şekilde anahtarlanır ve URL içinde kodlanır:

```php
echo $url = url()->query('/posts', ['columns' => ['title', 'body']]);
// http://example.com/posts?columns%5B0%5D=title&columns%5B1%5D=body
 
echo urldecode($url);
// http://example.com/posts?columns[0]=title&columns[1]=body
```

---

### Geçerli URL’ye Erişim

`url` yardımcı işlevine bir yol verilmezse, bir `Illuminate\Routing\UrlGenerator` örneği döndürülür. Bu sayede mevcut URL hakkında bilgi edinebilirsiniz:

```php
// Sorgu dizesi olmadan geçerli URL’yi al...
echo url()->current();
 
// Sorgu dizesi dahil geçerli URL’yi al...
echo url()->full();
 
// Önceki isteğin tam URL’sini al...
echo url()->previous();
 
// Önceki isteğin yolunu (path) al...
echo url()->previousPath();
```

Bu metodların her birine `URL` facade aracılığıyla da erişilebilir:

```php
use Illuminate\Support\Facades\URL;
 
echo URL::current();
```

---

## Adlandırılmış Rotalar İçin URL’ler

`route` yardımcı işlevi, adlandırılmış rotalara (named routes) ait URL’ler oluşturmak için kullanılabilir. Adlandırılmış rotalar, URL tanımlarına bağımlı kalmadan URL oluşturmanıza olanak tanır. Böylece rota URL’si değişse bile `route` fonksiyonunu çağırdığınız yerlerde değişiklik yapmanız gerekmez.

Örneğin, uygulamanızda şu rota tanımlıysa:

```php
Route::get('/post/{post}', function (Post $post) {
    // ...
})->name('post.show');
```

Bu rotaya bir URL oluşturmak için:

```php
echo route('post.show', ['post' => 1]);
// http://example.com/post/1
```

Birden fazla parametre içeren rotalar için de aynı yöntem geçerlidir:

```php
Route::get('/post/{post}/comment/{comment}', function (Post $post, Comment $comment) {
    // ...
})->name('comment.show');
 
echo route('comment.show', ['post' => 1, 'comment' => 3]);
// http://example.com/post/1/comment/3
```

Tanımsız parametreler URL’nin sorgu dizesine eklenir:

```php
echo route('post.show', ['post' => 1, 'search' => 'rocket']);
// http://example.com/post/1?search=rocket
```

### Eloquent Modelleri

Genellikle, Eloquent modellerinin route key (genellikle birincil anahtar) değerleriyle URL oluşturursunuz. Bu nedenle, `route` yardımcı işlevine model örneklerini doğrudan parametre olarak geçebilirsiniz:

```php
echo route('post.show', ['post' => $post]);
```

---

## İmzalı URL’ler

Laravel, adlandırılmış rotalar için “imzalı” URL’ler oluşturmayı kolaylaştırır. Bu URL’lerin sorgu dizesine bir “signature” hash’i eklenir ve Laravel bu sayede URL’nin oluşturulduktan sonra değiştirilip değiştirilmediğini doğrulayabilir. İmzalı URL’ler, herkese açık olup yine de URL manipülasyonuna karşı koruma gerektiren rotalar için özellikle faydalıdır.

Örneğin, müşterilerinize gönderilen bir e-postada "abonelikten çık" bağlantısı oluşturmak için kullanabilirsiniz:

```php
use Illuminate\Support\Facades\URL;
 
return URL::signedRoute('unsubscribe', ['user' => 1]);
```

Domain’i imza hash’inden hariç tutmak istiyorsanız:

```php
return URL::signedRoute('unsubscribe', ['user' => 1], absolute: false);
```

Zamanla sona eren geçici bir imzalı URL oluşturmak için:

```php
use Illuminate\Support\Facades\URL;
 
return URL::temporarySignedRoute(
    'unsubscribe', now()->addMinutes(30), ['user' => 1]
);
```

### İmzalı Rota İsteklerini Doğrulama

Gelen isteğin geçerli bir imzaya sahip olup olmadığını doğrulamak için:

```php
use Illuminate\Http\Request;
 
Route::get('/unsubscribe/{user}', function (Request $request) {
    if (! $request->hasValidSignature()) {
        abort(401);
    }
});
```

Belirli sorgu parametrelerini yok saymak için:

```php
if (! $request->hasValidSignatureWhileIgnoring(['page', 'order'])) {
    abort(401);
}
```

İmzalı URL’leri doğrulamak için `signed` middleware’i de kullanabilirsiniz:

```php
Route::post('/unsubscribe/{user}', function (Request $request) {
    // ...
})->name('unsubscribe')->middleware('signed');
```

Eğer imzanız domain içermiyorsa:

```php
Route::post('/unsubscribe/{user}', function (Request $request) {
    // ...
})->name('unsubscribe')->middleware('signed:relative');
```

### Geçersiz İmzalı Rotalara Yanıt Verme

Süresi dolmuş bir imzalı URL ziyaret edildiğinde varsayılan olarak 403 hatası döner. Bu davranışı `InvalidSignatureException` için özel bir “render” işlevi tanımlayarak özelleştirebilirsiniz:

```php
use Illuminate\Routing\Exceptions\InvalidSignatureException;
 
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (InvalidSignatureException $e) {
        return response()->view('errors.link-expired', status: 403);
    });
});
```

---

## Controller Eylemleri İçin URL’ler

`action` fonksiyonu belirli bir controller metoduna ait URL üretir:

```php
use App\Http\Controllers\HomeController;
 
$url = action([HomeController::class, 'index']);
```

Controller metodu parametre alıyorsa:

```php
$url = action([UserController::class, 'profile'], ['id' => 1]);
```

---

## Fluent URI Nesneleri

Laravel’in `Uri` sınıfı, nesne tabanlı URI oluşturma ve düzenleme için akıcı (fluent) bir arayüz sağlar. Bu sınıf, League URI paketini temel alır ve Laravel’in yönlendirme sistemiyle sorunsuz çalışır.

```php
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvokableController;
use Illuminate\Support\Uri;
 
$uri = Uri::of('https://example.com/path');
$uri = Uri::to('/dashboard');
$uri = Uri::route('users.show', ['user' => 1]);
$uri = Uri::signedRoute('users.show', ['user' => 1]);
$uri = Uri::temporarySignedRoute('user.index', now()->addMinutes(5));
$uri = Uri::action([UserController::class, 'index']);
$uri = Uri::action(InvokableController::class);
$uri = $request->uri();
```

Bir URI örneğini akıcı şekilde düzenleyebilirsiniz:

```php
$uri = Uri::of('https://example.com')
    ->withScheme('http')
    ->withHost('test.com')
    ->withPort(8000)
    ->withPath('/users')
    ->withQuery(['page' => 2])
    ->withFragment('section-1');
```

---

## Varsayılan Değerler

Bazı uygulamalarda, belirli URL parametreleri için istek boyunca geçerli olacak varsayılan değerler belirlemek isteyebilirsiniz. Örneğin, birçok rotanız `{locale}` parametresi içeriyorsa:

```php
Route::get('/{locale}/posts', function () {
    // ...
})->name('post.index');
```

Her seferinde locale parametresini manuel olarak geçmek yerine, `URL::defaults` metodunu kullanabilirsiniz:

```php
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;
 
class SetDefaultLocaleForUrls
{
    public function handle(Request $request, Closure $next): Response
    {
        URL::defaults(['locale' => $request->user()->locale]);
        return $next($request);
    }
}
```

Artık `route` fonksiyonuyla URL oluştururken `locale` parametresini geçmeniz gerekmez.

### URL Varsayılanları ve Middleware Önceliği

URL varsayılanlarını ayarlamak, Laravel’in implicit model binding işlemlerini etkileyebilir. Bu yüzden, URL varsayılanlarını ayarlayan middleware’inizi `SubstituteBindings` middleware’inden önce çalışacak şekilde önceliklendirmelisiniz:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->prependToPriorityList(
        before: \Illuminate\Routing\Middleware\SubstituteBindings::class,
        prepend: \App\Http\Middleware\SetDefaultLocaleForUrls::class,
    );
});
```
