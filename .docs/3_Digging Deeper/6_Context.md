````markdown
<br>
## Context

<br>
### Introduction

Laravel’in **context** (bağlam) özellikleri, uygulamanız içinde yürütülen istekler, işler (jobs) ve komutlar boyunca bilgiyi yakalamanıza, almanıza ve paylaşmanıza olanak tanır. Bu yakalanan bilgiler, uygulamanız tarafından yazılan log’lara da dahil edilir. Böylece bir log girdisi yazılmadan önceki kod yürütme geçmişine dair daha derin bir içgörü elde edebilir ve dağıtık bir sistem boyunca yürütme akışlarını izleyebilirsiniz.

<br>
### How it Works

Laravel’in context yeteneklerini anlamanın en iyi yolu, yerleşik loglama özellikleriyle birlikte nasıl çalıştığını görmektir. Başlamak için, **Context** facadesini kullanarak bağlama bilgi ekleyebilirsiniz. Bu örnekte, her gelen istek için istek URL’sini ve benzersiz bir **trace ID**’yi bağlama eklemek için bir middleware kullanıyoruz:

```php
<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
 
class AddContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Context::add('url', $request->url());
        Context::add('trace_id', Str::uuid()->toString());
 
        return $next($request);
    }
}
````

Bağlama eklenen bilgiler, istek boyunca yazılan tüm log girdilerine otomatik olarak metadata olarak eklenir. Context bilgilerini metadata olarak eklemek, bireysel log girdilerine geçirilen bilgilerin, **Context** üzerinden paylaşılan bilgilerden ayrılmasını sağlar. Örneğin, aşağıdaki log girdisini yazdığımızı varsayalım:

```php
Log::info('User authenticated.', ['auth_id' => Auth::id()]);
```

Yazılan log girdisi, log girdisine geçirilen `auth_id` değerini içerirken aynı zamanda context’e eklenen `url` ve `trace_id` değerlerini de metadata olarak içerir:

```
User authenticated. {"auth_id":27} {"url":"https://example.com/login","trace_id":"e04e1a11-e75c-4db3-b5b5-cfef4ef56697"}
```

Bağlama eklenen bilgiler, kuyruğa gönderilen job’larda da kullanılabilir. Örneğin, bağlama bazı bilgiler ekledikten sonra bir **ProcessPodcast** job’unu kuyruğa gönderdiğimizi düşünelim:

```php
// Middleware içinde...
Context::add('url', $request->url());
Context::add('trace_id', Str::uuid()->toString());
 
// Controller içinde...
ProcessPodcast::dispatch($podcast);
```

Job kuyruğa gönderildiğinde, o anda bağlamda saklanan bilgiler yakalanır ve job ile paylaşılır. Bu bilgiler job yürütülürken yeniden bağlama yüklenir. Dolayısıyla, job’un `handle` metodu log yazarsa:

```php
class ProcessPodcast implements ShouldQueue
{
    use Queueable;
 
    public function handle(): void
    {
        Log::info('Processing podcast.', [
            'podcast_id' => $this->podcast->id,
        ]);
    }
}
```

Ortaya çıkan log girdisi, job’un kuyruğa gönderildiği istekte bağlama eklenen bilgileri de içerecektir:

```
Processing podcast. {"podcast_id":95} {"url":"https://example.com/login","trace_id":"e04e1a11-e75c-4db3-b5b5-cfef4ef56697"}
```

Burada Laravel’in loglama ile ilgili yerleşik context özelliklerine odaklandık, ancak aşağıdaki belgelerde context’in HTTP isteği / job sınırında nasıl bilgi paylaşabildiğini ve log girdilerine yazılmayan gizli context verilerinin nasıl eklendiğini göreceksiniz.

<br>
### Capturing Context

Geçerli bağlamda bilgi depolamak için **Context** facadesinin `add` metodunu kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Context;
 
Context::add('key', 'value');
```

Birden fazla öğe eklemek için `add` metoduna bir ilişkisel dizi gönderebilirsiniz:

```php
Context::add([
    'first_key' => 'value',
    'second_key' => 'value',
]);
```

`add` metodu, aynı anahtarı paylaşan mevcut değeri geçersiz kılar. Eğer sadece anahtar henüz yoksa bilgi eklemek istiyorsanız, `addIf` metodunu kullanabilirsiniz:

```php
Context::add('key', 'first');
 
Context::get('key');
// "first"
 
Context::addIf('key', 'second');
 
Context::get('key');
// "first"
```

**Context**, verilen bir anahtarı artırmak veya azaltmak için kullanışlı yöntemler de sağlar. Her iki yöntem de en az bir argüman alır: izlenecek anahtar. İkinci bir argüman, anahtarın ne kadar artırılacağını veya azaltılacağını belirtir:

```php
Context::increment('records_added');
Context::increment('records_added', 5);
 
Context::decrement('records_added');
Context::decrement('records_added', 5);
```

<br>
### Conditional Context

`when` metodu, belirli bir koşula bağlı olarak bağlama veri eklemek için kullanılabilir. Koşul doğruysa ilk closure, yanlışsa ikinci closure çağrılır:

```php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
 
Context::when(
    Auth::user()->isAdmin(),
    fn ($context) => $context->add('permissions', Auth::user()->permissions),
    fn ($context) => $context->add('permissions', []),
);
```

<br>
### Scoped Context

`scope` metodu, belirli bir callback’in yürütülmesi sırasında bağlamı geçici olarak değiştirme ve callback tamamlandığında eski haline döndürme imkânı sağlar. Ayrıca, closure çalışırken bağlama ek veriler (ikinci ve üçüncü argüman olarak) birleştirilebilir.

```php
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
 
Context::add('trace_id', 'abc-999');
Context::addHidden('user_id', 123);
 
Context::scope(
    function () {
        Context::add('action', 'adding_friend');
 
        $userId = Context::getHidden('user_id');
 
        Log::debug("Adding user [{$userId}] to friends list.");
        // Adding user [987] to friends list.  {"trace_id":"abc-999","user_name":"taylor_otwell","action":"adding_friend"}
    },
    data: ['user_name' => 'taylor_otwell'],
    hidden: ['user_id' => 987],
);
 
Context::all();
// [
//     'trace_id' => 'abc-999',
// ]
 
Context::allHidden();
// [
//     'user_id' => 123,
// ]
```

Bir scope içindeki nesne değiştirilirse, bu değişiklik scope dışında da yansıtılır.

<br>
### Stacks

**Context**, "stack" (yığın) oluşturma özelliği sunar; bu, eklenen verilerin sırasıyla saklandığı bir listedir. Bir stack’e bilgi eklemek için `push` metodunu çağırabilirsiniz:

```php
use Illuminate\Support\Facades\Context;
 
Context::push('breadcrumbs', 'first_value');
 
Context::push('breadcrumbs', 'second_value', 'third_value');
 
Context::get('breadcrumbs');
// [
//     'first_value',
//     'second_value',
//     'third_value',
// ]
```

Stack’ler, uygulamanızda gerçekleşen olaylar gibi bir istek hakkında tarihsel bilgi toplamak için kullanışlı olabilir. Örneğin, her sorgu çalıştığında sorgunun SQL’ini ve süresini yakalayan bir event listener oluşturabilirsiniz:

```php
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
 
// AppServiceProvider.php içinde...
DB::listen(function ($event) {
    Context::push('queries', [$event->time, $event->sql]);
});
```

Bir değerin stack içinde olup olmadığını `stackContains` ve `hiddenStackContains` metodlarıyla belirleyebilirsiniz:

```php
if (Context::stackContains('breadcrumbs', 'first_value')) {
    //
}
 
if (Context::hiddenStackContains('secrets', 'first_value')) {
    //
}
```

Bu metodlar, ikinci argüman olarak bir closure da kabul eder. Bu sayede değer karşılaştırma işlemi üzerinde daha fazla kontrol elde edebilirsiniz:

```php
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
 
return Context::stackContains('breadcrumbs', function ($value) {
    return Str::startsWith($value, 'query_');
});
```

````markdown
<br>
## Retrieving Context

<br>
### Retrieving Context Values

Bağlamdan (context) bilgi almak için **Context** facadesinin `get` metodunu kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Context;
 
$value = Context::get('key');
````

`only` ve `except` metodları, bağlamdaki bilgilerin belirli bir alt kümesini almak için kullanılabilir:

```php
$data = Context::only(['first_key', 'second_key']);
 
$data = Context::except(['first_key']);
```

`pull` metodu, bağlamdan bir bilgi alır ve hemen ardından bağlamdan kaldırır:

```php
$value = Context::pull('key');
```

Eğer bağlam verisi bir **stack** içinde depolanmışsa, `pop` metodu ile bu stack’ten öğeleri çıkarabilirsiniz:

```php
Context::push('breadcrumbs', 'first_value', 'second_value');
 
Context::pop('breadcrumbs');
// second_value
 
Context::get('breadcrumbs');
// ['first_value']
```

`remember` ve `rememberHidden` metodları, bağlamdan bilgi almak için kullanılabilir. Eğer istenen bilgi mevcut değilse, verilen closure çalıştırılarak elde edilen değer bağlama kaydedilir ve döndürülür:

```php
$permissions = Context::remember(
    'user-permissions',
    fn () => $user->permissions,
);
```

Bağlamda depolanan tüm bilgileri almak için `all` metodunu çağırabilirsiniz:

```php
$data = Context::all();
```

<br>
### Determining Item Existence

Belirli bir anahtar için bağlamda bir değer olup olmadığını belirlemek amacıyla `has` ve `missing` metodlarını kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Context;
 
if (Context::has('key')) {
    // ...
}
 
if (Context::missing('key')) {
    // ...
}
```

`has` metodu, anahtarın değeri ne olursa olsun (örneğin `null` bile olsa) **true** döndürür:

```php
Context::add('key', null);
 
Context::has('key');
// true
```

<br>
### Removing Context

Bir anahtarı ve değerini bağlamdan kaldırmak için `forget` metodunu kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Context;
 
Context::add(['first_key' => 1, 'second_key' => 2]);
 
Context::forget('first_key');
 
Context::all();
// ['second_key' => 2]
```

Birden fazla anahtarı aynı anda kaldırmak için, `forget` metoduna bir dizi gönderebilirsiniz:

```php
Context::forget(['first_key', 'second_key']);
```

<br>
### Hidden Context

**Context**, "gizli" veri depolama özelliği sunar. Bu gizli bilgiler log’lara eklenmez ve yukarıda belgelenen veri alma yöntemleriyle erişilemez. **Context**, gizli bağlam verileriyle etkileşime geçmek için farklı bir yöntem seti sağlar:

```php
use Illuminate\Support\Facades\Context;
 
Context::addHidden('key', 'value');
 
Context::getHidden('key');
// 'value'
 
Context::get('key');
// null
```

“Hidden” metodlar, yukarıda açıklanan normal metodların işlevselliğini yansıtır:

```php
Context::addHidden(/* ... */);
Context::addHiddenIf(/* ... */);
Context::pushHidden(/* ... */);
Context::getHidden(/* ... */);
Context::pullHidden(/* ... */);
Context::popHidden(/* ... */);
Context::onlyHidden(/* ... */);
Context::exceptHidden(/* ... */);
Context::allHidden(/* ... */);
Context::hasHidden(/* ... */);
Context::missingHidden(/* ... */);
Context::forgetHidden(/* ... */);
```

<br>
### Events

**Context**, bağlamın “dehydration” (kurutulma) ve “hydration” (yeniden yüklenme) süreçlerine bağlanmanızı sağlayan iki olay (event) yayınlar.

Bu olayların nasıl kullanılabileceğini göstermek için, uygulamanızdaki bir middleware içinde `app.locale` yapılandırma değerini gelen HTTP isteğinin **Accept-Language** başlığına göre ayarladığınızı varsayalım. **Context** olayları, bu değeri istek sırasında yakalamanıza ve kuyruğa gönderilen job’larda yeniden yüklemenize olanak tanır. Böylece kuyruğa gönderilen bildirimler doğru `app.locale` değeriyle çalışır. Bunu context’in event’leri ve hidden verileriyle başarabilirsiniz.

<br>
### Dehydrating

Bir job kuyruğa gönderildiğinde, bağlamdaki veriler “dehydrate” edilir (yani kurutulur) ve job’un payload’ına eklenir. `Context::dehydrating` metodu, bu süreçte çağrılacak bir closure kaydetmenizi sağlar. Bu closure içinde, kuyruğa gönderilecek job ile paylaşılacak veriler üzerinde değişiklik yapabilirsiniz.

Bu callback’leri genellikle uygulamanızın **AppServiceProvider** sınıfının `boot` metodunda kaydetmelisiniz:

```php
use Illuminate\Log\Context\Repository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Context;
 
/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Context::dehydrating(function (Repository $context) {
        $context->addHidden('locale', Config::get('app.locale'));
    });
}
```

**Dikkat:** `dehydrating` callback’i içinde **Context** facadesini kullanmayın; bu, mevcut sürecin bağlamını değiştirir. Bunun yerine yalnızca callback’e geçirilen `Repository` nesnesi üzerinde işlem yapın.

<br>
### Hydrated

Bir job kuyruğa alındığında yürütülmeye başladığında, job ile paylaşılan bağlam verileri yeniden yüklenir (hydrate edilir). `Context::hydrated` metodu, bu süreçte çağrılacak bir closure kaydetmenizi sağlar.

Bu callback’leri de genellikle **AppServiceProvider** sınıfının `boot` metodunda kaydetmelisiniz:

```php
use Illuminate\Log\Context\Repository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Context;
 
/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Context::hydrated(function (Repository $context) {
        if ($context->hasHidden('locale')) {
            Config::set('app.locale', $context->getHidden('locale'));
        }
    });
}
```

**Not:** `hydrated` callback’i içinde de **Context** facadesini kullanmayın; bunun yerine yalnızca callback’e geçirilen `Repository` nesnesinde değişiklik yapın.


