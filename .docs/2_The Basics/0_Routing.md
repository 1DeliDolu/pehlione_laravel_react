# Yönlendirme

## Temel Yönlendirme

En basit Laravel yönlendirmeleri bir URI ve bir closure kabul eder, karmaşık yönlendirme yapılandırma dosyaları olmadan yönlendirmeleri ve davranışları tanımlamanın çok basit ve ifade gücü yüksek bir yöntemini sağlar:

```php
use Illuminate\Support\Facades\Route;
 
Route::get('/greeting', function () {
    return 'Hello World';
});
```

## Varsayılan Yönlendirme Dosyaları

Tüm Laravel yönlendirmeleri, **routes** dizininde bulunan yönlendirme dosyalarınızda tanımlanır. Bu dosyalar, uygulamanızın **bootstrap/app.php** dosyasında belirtilen yapılandırmayı kullanarak Laravel tarafından otomatik olarak yüklenir.

**routes/web.php** dosyası, web arayüzünüz için olan yönlendirmeleri tanımlar. Bu yönlendirmelere, oturum durumu ve CSRF koruması gibi özellikler sağlayan **web middleware grubu** atanır.

Çoğu uygulama için, yönlendirmeleri **routes/web.php** dosyasında tanımlayarak başlayacaksınız.
**routes/web.php** dosyasında tanımlanan yönlendirmelere, tanımlanan yönlendirme URL’sini tarayıcınıza girerek erişebilirsiniz.
Örneğin, aşağıdaki yönlendirmeye tarayıcınızda `http://example.com/user` adresine giderek erişebilirsiniz:

```php
use App\Http\Controllers\UserController;
 
Route::get('/user', [UserController::class, 'index']);
```

## API Yönlendirmeleri

Uygulamanız aynı zamanda durumsuz bir API sunacaksa, **install:api** Artisan komutunu kullanarak API yönlendirmeyi etkinleştirebilirsiniz:

```bash
php artisan install:api
```

**install:api** komutu, üçüncü taraf API tüketicilerini, SPA’leri veya mobil uygulamaları kimlik doğrulamak için kullanılabilen güçlü ama basit bir API token kimlik doğrulama koruması sağlayan **Laravel Sanctum**’u kurar.
Ayrıca, bu komut **routes/api.php** dosyasını oluşturur:

```php
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
```

**routes/api.php** içindeki yönlendirmeler durumsuzdur ve **api middleware grubu** atanır.
Ek olarak, bu yönlendirmelere otomatik olarak **/api** URI öneki uygulanır, bu nedenle dosyadaki her yönlendirmeye bunu manuel olarak eklemeniz gerekmez.
Bu öneki uygulamanızın **bootstrap/app.php** dosyasını değiştirerek değiştirebilirsiniz:

```php
->withRouting(
    api: __DIR__.'/../routes/api.php',
    apiPrefix: 'api/admin',
    // ...
)
```

## Mevcut Router Metodları

Router, herhangi bir HTTP fiiline yanıt veren yönlendirmeler kaydetmenize olanak tanır:

```php
Route::get($uri, $callback);
Route::post($uri, $callback);
Route::put($uri, $callback);
Route::patch($uri, $callback);
Route::delete($uri, $callback);
Route::options($uri, $callback);
```

Bazen birden fazla HTTP fiiline yanıt veren bir yönlendirme kaydetmeniz gerekebilir.
Bunu **match** metodunu kullanarak yapabilirsiniz.
Veya, **any** metodunu kullanarak tüm HTTP fiillerine yanıt veren bir yönlendirme kaydedebilirsiniz:

```php
Route::match(['get', 'post'], '/', function () {
    // ...
});
 
Route::any('/', function () {
    // ...
});
```

Aynı URI’yi paylaşan birden fazla yönlendirme tanımlarken, **get**, **post**, **put**, **patch**, **delete** ve **options** metodlarını kullanan yönlendirmeler, **any**, **match** ve **redirect** metodlarını kullanan yönlendirmelerden önce tanımlanmalıdır.
Bu, gelen isteğin doğru yönlendirmeyle eşleşmesini sağlar.

## Bağımlılık Enjeksiyonu

Yönlendirme callback tanımınızda gerekli olan tüm bağımlılıkları **type-hint** olarak belirtebilirsiniz.
Belirtilen bağımlılıklar, Laravel servis container tarafından otomatik olarak çözümlenip callback’e enjekte edilir.
Örneğin, mevcut HTTP isteğinin otomatik olarak yönlendirme callback’inize enjekte edilmesi için **Illuminate\Http\Request** sınıfını type-hint olarak belirtebilirsiniz:

```php
use Illuminate\Http\Request;
 
Route::get('/users', function (Request $request) {
    // ...
});
```

## CSRF Koruması

Unutmayın, **web yönlendirme dosyasında** tanımlanan **POST**, **PUT**, **PATCH** veya **DELETE** yönlendirmelerine işaret eden tüm HTML formları bir **CSRF token alanı** içermelidir.
Aksi takdirde, istek reddedilecektir.
CSRF koruması hakkında daha fazla bilgiyi CSRF belgelerinde okuyabilirsiniz:

```html
<form method="POST" action="/profile">
    @csrf
    ...
</form>
```

## Yönlendirme Yönlendirmeleri

Başka bir URI’ye yönlendiren bir yönlendirme tanımlıyorsanız, **Route::redirect** metodunu kullanabilirsiniz.
Bu yöntem, basit bir yönlendirme gerçekleştirmek için tam bir yönlendirme veya controller tanımlamak zorunda kalmadan kullanışlı bir kısayol sağlar:

```php
Route::redirect('/here', '/there');
```

Varsayılan olarak, **Route::redirect** bir **302** durum kodu döndürür.
İsteğe bağlı üçüncü parametreyi kullanarak durum kodunu özelleştirebilirsiniz:

```php
Route::redirect('/here', '/there', 301);
```

Veya, **Route::permanentRedirect** metodunu kullanarak **301** durum kodu döndürebilirsiniz:

```php
Route::permanentRedirect('/here', '/there');
```

Yönlendirme parametrelerini kullanırken, aşağıdaki parametreler Laravel tarafından ayrılmıştır ve kullanılamaz: **destination** ve **status**.

## Görünüm (View) Yönlendirmeleri

Yönlendirme sadece bir görünüm döndürmesi gerekiyorsa, **Route::view** metodunu kullanabilirsiniz.
**redirect** metodu gibi, bu metod da tam bir yönlendirme veya controller tanımlamak zorunda kalmadan kullanışlı bir kısayol sağlar.
**view** metodu, ilk parametre olarak bir URI, ikinci parametre olarak bir görünüm adı kabul eder.
Ayrıca, görünüme iletilecek verilerin bir dizisini isteğe bağlı üçüncü parametre olarak sağlayabilirsiniz:

```php
Route::view('/welcome', 'welcome');
 
Route::view('/welcome', 'welcome', ['name' => 'Taylor']);
```

Görünüm yönlendirmelerinde yönlendirme parametreleri kullanırken, aşağıdaki parametreler Laravel tarafından ayrılmıştır ve kullanılamaz: **view**, **data**, **status** ve **headers**.

## Yönlendirmelerinizi Listeleme

**route:list** Artisan komutu, uygulamanızda tanımlanan tüm yönlendirmelerin genel bir görünümünü kolayca sağlayabilir:

```bash
php artisan route:list
```

Varsayılan olarak, her yönlendirmeye atanan **route middleware** değerleri çıktıda gösterilmez;
ancak, komuta **-v** seçeneğini ekleyerek Laravel’e yönlendirme middleware’lerini ve middleware grup adlarını göstermesini belirtebilirsiniz:

```bash
php artisan route:list -v
```

Middleware gruplarını genişletmek için:

```bash
php artisan route:list -vv
```

Yalnızca belirli bir URI ile başlayan yönlendirmeleri göstermek için Laravel’e şu şekilde talimat verebilirsiniz:

```bash
php artisan route:list --path=api
```

Ayrıca, **route:list** komutunu yürütürken **--except-vendor** seçeneğini sağlayarak üçüncü taraf paketler tarafından tanımlanan yönlendirmeleri gizlemesini Laravel’e bildirebilirsiniz:

```bash
php artisan route:list --except-vendor
```

Benzer şekilde, yalnızca üçüncü taraf paketler tarafından tanımlanan yönlendirmeleri göstermek için **--only-vendor** seçeneğini sağlayabilirsiniz:

```bash
php artisan route:list --only-vendor
```

## Yönlendirme Özelleştirme

Varsayılan olarak, uygulamanızın yönlendirmeleri **bootstrap/app.php** dosyası tarafından yapılandırılır ve yüklenir:

```php
<?php
 
use Illuminate\Foundation\Application;
 
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )->create();
```

Ancak, bazen uygulamanızın yönlendirmelerinin bir alt kümesini içeren tamamen yeni bir dosya tanımlamak isteyebilirsiniz.
Bunu yapmak için, **withRouting** metoduna bir **then closure** sağlayabilirsiniz.
Bu closure içinde, uygulamanız için gerekli ek yönlendirmeleri kaydedebilirsiniz:

```php
use Illuminate\Support\Facades\Route;
 
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    then: function () {
        Route::middleware('api')
            ->prefix('webhooks')
            ->name('webhooks.')
            ->group(base_path('routes/webhooks.php'));
    },
)
```

Veya, yönlendirme kaydını tamamen kontrol altına almak için **withRouting** metoduna bir **using closure** sağlayabilirsiniz.
Bu argüman sağlandığında, framework tarafından hiçbir HTTP yönlendirmesi kaydedilmez ve tüm yönlendirmeleri manuel olarak kaydetmek sizin sorumluluğunuzdadır:

```php
use Illuminate\Support\Facades\Route;
 
->withRouting(
    commands: __DIR__.'/../routes/console.php',
    using: function () {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
 
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    },
)
```
# Route Parametreleri

## Zorunlu Parametreler

Bazen URI’nin belirli bölümlerini yönlendirme içinde yakalamanız gerekebilir. Örneğin, bir kullanıcının kimliğini (ID) URL’den almak isteyebilirsiniz. Bunu, yönlendirme parametreleri tanımlayarak yapabilirsiniz:

```php
Route::get('/user/{id}', function (string $id) {
    return 'User '.$id;
});
```

Yönlendirmede gerektiği kadar çok parametre tanımlayabilirsiniz:

```php
Route::get('/posts/{post}/comments/{comment}', function (string $postId, string $commentId) {
    // ...
});
```

Yönlendirme parametreleri her zaman `{}` süslü parantezler içine alınır ve alfabetik karakterlerden oluşmalıdır.
Yönlendirme parametre adlarında alt çizgi (`_`) de kullanılabilir.
Yönlendirme parametreleri, tanımlandıkları sıraya göre callback’lere veya controller’lara enjekte edilir — callback/controller argümanlarının isimleri önemli değildir.

---

## Parametreler ve Bağımlılık Enjeksiyonu

Eğer yönlendirmenizin callback’ine Laravel servis container tarafından otomatik olarak enjekte edilmesini istediğiniz bağımlılıklar varsa, yönlendirme parametrelerinizi bağımlılıklardan sonra listelemelisiniz:

```php
use Illuminate\Http\Request;
 
Route::get('/user/{id}', function (Request $request, string $id) {
    return 'User '.$id;
});
```

---

## Opsiyonel Parametreler

Bazen URI’de her zaman bulunmayabilecek bir yönlendirme parametresi belirtmeniz gerekebilir.
Bunu, parametre adından sonra `?` işareti koyarak yapabilirsiniz.
Yönlendirmeye karşılık gelen değişkene varsayılan bir değer vermeyi unutmayın:

```php
Route::get('/user/{name?}', function (?string $name = null) {
    return $name;
});
 
Route::get('/user/{name?}', function (?string $name = 'John') {
    return $name;
});
```

---

## Düzenli İfade (Regex) Kısıtlamaları

Yönlendirme parametrelerinin biçimini, yönlendirme örneği üzerindeki **where** metodu ile kısıtlayabilirsiniz.
Bu metod, parametrenin adını ve parametrenin nasıl kısıtlanacağını tanımlayan bir düzenli ifadeyi kabul eder:

```php
Route::get('/user/{name}', function (string $name) {
    // ...
})->where('name', '[A-Za-z]+');
 
Route::get('/user/{id}', function (string $id) {
    // ...
})->where('id', '[0-9]+');
 
Route::get('/user/{id}/{name}', function (string $id, string $name) {
    // ...
})->where(['id' => '[0-9]+', 'name' => '[a-z]+']);
```

Kolaylık sağlamak için, sık kullanılan bazı düzenli ifade desenleri için yönlendirmelerinize hızlıca kısıtlama eklemenizi sağlayan yardımcı metotlar mevcuttur:

```php
Route::get('/user/{id}/{name}', function (string $id, string $name) {
    // ...
})->whereNumber('id')->whereAlpha('name');
 
Route::get('/user/{name}', function (string $name) {
    // ...
})->whereAlphaNumeric('name');
 
Route::get('/user/{id}', function (string $id) {
    // ...
})->whereUuid('id');
 
Route::get('/user/{id}', function (string $id) {
    // ...
})->whereUlid('id');
 
Route::get('/category/{category}', function (string $category) {
    // ...
})->whereIn('category', ['movie', 'song', 'painting']);
 
Route::get('/category/{category}', function (string $category) {
    // ...
})->whereIn('category', CategoryEnum::cases());
```

Eğer gelen istek, yönlendirme desen kısıtlamalarıyla eşleşmezse, **404 HTTP** yanıtı döndürülür.

---

## Global Kısıtlamalar

Bir yönlendirme parametresinin her zaman belirli bir düzenli ifadeyle kısıtlanmasını istiyorsanız, **pattern** metodunu kullanabilirsiniz.
Bu desenleri uygulamanızın **App\Providers\AppServiceProvider** sınıfının **boot** metodunda tanımlamalısınız:

```php
use Illuminate\Support\Facades\Route;
 
/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Route::pattern('id', '[0-9]+');
}
```

Desen bir kez tanımlandıktan sonra, o parametre adını kullanan tüm yönlendirmelere otomatik olarak uygulanır:

```php
Route::get('/user/{id}', function (string $id) {
    // Sadece {id} sayısal ise çalıştırılır...
});
```

---

## Kodlanmış Eğik Çizgiler (Encoded Forward Slashes)

Laravel yönlendirme bileşeni, yönlendirme parametre değerlerinde **/** karakteri dışındaki tüm karakterlere izin verir.
Eğer **/** karakterinin de yer tutucunun bir parçası olmasına izin vermek istiyorsanız, bunu bir **where** koşulu düzenli ifadesi kullanarak açıkça belirtmelisiniz:

```php
Route::get('/search/{search}', function (string $search) {
    return $search;
})->where('search', '.*');
```

Kodlanmış eğik çizgilere yalnızca **son yönlendirme segmenti** içinde izin verilir.


# Adlandırılmış Rotalar

Adlandırılmış rotalar, belirli rotalar için URL veya yönlendirme oluşturmayı kolaylaştırır.
Bir rotaya ad vermek için, rota tanımına **name** metodunu zincirleyebilirsiniz:

```php
Route::get('/user/profile', function () {
    // ...
})->name('profile');
```

Controller eylemleri için de rota adları belirtebilirsiniz:

```php
Route::get(
    '/user/profile',
    [UserProfileController::class, 'show']
)->name('profile');
```

Rota adları her zaman **benzersiz** olmalıdır.

---

## Adlandırılmış Rotalara URL Oluşturma

Bir rotaya ad atadıktan sonra, Laravel’in **route** ve **redirect** yardımcı fonksiyonlarını kullanarak bu rotaya URL veya yönlendirme oluşturabilirsiniz:

```php
// URL oluşturma...
$url = route('profile');
 
// Yönlendirme oluşturma...
return redirect()->route('profile');
 
return to_route('profile');
```

Eğer adlandırılmış rota parametreler tanımlıyorsa, bu parametreleri **route** fonksiyonuna ikinci argüman olarak geçebilirsiniz.
Belirtilen parametreler, oluşturulan URL’ye otomatik olarak doğru konumlarına yerleştirilir:

```php
Route::get('/user/{id}/profile', function (string $id) {
    // ...
})->name('profile');
 
$url = route('profile', ['id' => 1]);
```

Ek parametreleri diziye eklerseniz, bu anahtar/değer çiftleri oluşturulan URL’nin **sorgu dizesine (query string)** otomatik olarak eklenir:

```php
Route::get('/user/{id}/profile', function (string $id) {
    // ...
})->name('profile');
 
$url = route('profile', ['id' => 1, 'photos' => 'yes']);
 
// http://example.com/user/1/profile?photos=yes
```

Bazen, geçerli yerel ayar (locale) gibi URL parametreleri için **istek genelinde varsayılan değerler** belirtmek isteyebilirsiniz.
Bunu gerçekleştirmek için **URL::defaults** metodunu kullanabilirsiniz.

---

## Mevcut Rotayı İnceleme

Geçerli isteğin belirli bir adlandırılmış rotaya yönlendirilip yönlendirilmediğini belirlemek istiyorsanız, **Route** örneği üzerindeki **named** metodunu kullanabilirsiniz.
Örneğin, geçerli rota adını bir rota middleware’inden kontrol edebilirsiniz:

```php
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
/**
 * Handle an incoming request.
 *
 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
 */
public function handle(Request $request, Closure $next): Response
{
    if ($request->route()->named('profile')) {
        // ...
    }
 
    return $next($request);
}
```
# Route Grupları

Route grupları, çok sayıda rota arasında **middleware** gibi rota özelliklerini paylaşmanıza olanak tanır; böylece bu özellikleri her bir rotada ayrı ayrı tanımlamanıza gerek kalmaz.

İç içe geçmiş (nested) gruplar, üst gruplarının özellikleriyle akıllıca “birleşir”.
**Middleware** ve **where** koşulları birleştirilirken, **name** ve **prefix** değerleri eklenir.
Namespace ayırıcıları ve URI öneklerindeki eğik çizgiler gerektiğinde otomatik olarak eklenir.

---

## Middleware

Bir grup içindeki tüm rotalara **middleware** atamak için, grubu tanımlamadan önce **middleware** metodunu kullanabilirsiniz.
Middleware’ler, dizide listelendikleri sırayla çalıştırılır:

```php
Route::middleware(['first', 'second'])->group(function () {
    Route::get('/', function () {
        // first & second middleware kullanır...
    });
 
    Route::get('/user/profile', function () {
        // first & second middleware kullanır...
    });
});
```

---

## Controller’lar

Bir grup rotanın tamamı aynı controller’ı kullanıyorsa, bu controller’ı grup için ortak olarak tanımlamak üzere **controller** metodunu kullanabilirsiniz.
Daha sonra, rotaları tanımlarken yalnızca çağıracakları controller metodunu belirtmeniz yeterlidir:

```php
use App\Http\Controllers\OrderController;
 
Route::controller(OrderController::class)->group(function () {
    Route::get('/orders/{id}', 'show');
    Route::post('/orders', 'store');
});
```

---

## Alt Alan Adı (Subdomain) Yönlendirme

Route grupları, alt alan adı yönlendirmesini (subdomain routing) ele almak için de kullanılabilir.
Alt alan adlarına, tıpkı rota URI’leri gibi, rota parametreleri atanabilir.
Bu, alt alan adının bir bölümünü rotada veya controller’da kullanmak üzere yakalamanıza olanak tanır.
Alt alan adı, grubu tanımlamadan önce **domain** metodunu çağırarak belirtilir:

```php
Route::domain('{account}.example.com')->group(function () {
    Route::get('/user/{id}', function (string $account, string $id) {
        // ...
    });
});
```

Alt alan adı rotalarınızın erişilebilir olmasını sağlamak için, bu rotaları **kök alan adı rotalarından önce** kaydetmelisiniz.
Bu, aynı URI yoluna sahip kök alan adı rotalarının alt alan adı rotalarını geçersiz kılmasını engeller.

---

## Rota Önekleri (Route Prefixes)

**prefix** metodu, grup içindeki her rotanın URI’sine belirli bir önek eklemek için kullanılabilir.
Örneğin, bir grup içindeki tüm rota URI’lerine `admin` öneki eklemek isteyebilirsiniz:

```php
Route::prefix('admin')->group(function () {
    Route::get('/users', function () {
        // "/admin/users" URL’siyle eşleşir
    });
});
```

---

## Rota Adı Önekleri (Route Name Prefixes)

**name** metodu, grup içindeki her rota adına belirli bir dizeyi önek olarak eklemek için kullanılabilir.
Örneğin, grup içindeki tüm rotaların adlarını `admin` ile başlatmak isteyebilirsiniz.
Belirtilen dize, rota adına tam olarak yazıldığı şekilde eklenir; bu nedenle önek sonuna bir `.` karakteri eklediğinizden emin olun:

```php
Route::name('admin.')->group(function () {
    Route::get('/users', function () {
        // Rota adı "admin.users" olarak atanır...
    })->name('users');
});
```

# Route Model Binding

Bir model ID’sini bir route veya controller eylemine enjekte ederken, genellikle o ID’ye karşılık gelen modeli veritabanından sorgulamanız gerekir.
**Laravel route model binding**, model örneklerini doğrudan rotalarınıza otomatik olarak enjekte etmenin kullanışlı bir yolunu sağlar.
Örneğin, bir kullanıcının ID’sini enjekte etmek yerine, verilen ID’ye karşılık gelen **User** model örneğini doğrudan enjekte edebilirsiniz.

---

## **Implicit Binding (Örtük Bağlama)**

Laravel, rota veya controller eylemlerinde tanımlanan Eloquent modellerini, type-hint edilmiş değişken adları rota segmentiyle eşleştiğinde otomatik olarak çözümler.

```php
use App\Models\User;
 
Route::get('/users/{user}', function (User $user) {
    return $user->email;
});
```

`$user` değişkeni `App\Models\User` Eloquent modeliyle type-hint edildiği ve değişken adı `{user}` URI segmentiyle eşleştiği için, Laravel istekteki URI değerine karşılık gelen ID’ye sahip model örneğini otomatik olarak enjekte eder.
Eğer eşleşen bir model bulunamazsa, otomatik olarak **404 HTTP** yanıtı döndürülür.

Controller metotlarında da aynı mantıkla implicit binding kullanılabilir:

```php
use App\Http\Controllers\UserController;
use App\Models\User;
 
// Rota tanımı...
Route::get('/users/{user}', [UserController::class, 'show']);
 
// Controller metodu...
public function show(User $user)
{
    return view('user.profile', ['user' => $user]);
}
```

---

## **Soft Deleted Modeller**

Varsayılan olarak, implicit model binding soft delete edilmiş modelleri almaz.
Ancak, rotanın tanımına **withTrashed** metodunu ekleyerek bu modellerin de alınmasını sağlayabilirsiniz:

```php
use App\Models\User;
 
Route::get('/users/{user}', function (User $user) {
    return $user->email;
})->withTrashed();
```

---

## **Anahtarı Özelleştirme (Customizing the Key)**

Bazen modelleri `id` sütunu dışında başka bir sütuna göre çözümlemek isteyebilirsiniz.
Bunu, rota parametresi tanımında sütunu belirterek yapabilirsiniz:

```php
use App\Models\Post;
 
Route::get('/posts/{post:slug}', function (Post $post) {
    return $post;
});
```

Eğer bir model sınıfı için her zaman `id` dışında başka bir sütunu kullanmak istiyorsanız, modeldeki **getRouteKeyName** metodunu geçersiz kılabilirsiniz:

```php
/**
 * Get the route key for the model.
 */
public function getRouteKeyName(): string
{
    return 'slug';
}
```

---

## **Custom Keys ve Scoping**

Birden fazla Eloquent modelini aynı rota tanımında implicit binding ile kullanırken, ikinci modelin birincinin alt modeli olmasını isteyebilirsiniz.
Örneğin, belirli bir kullanıcı için slug’a göre bir blog gönderisi almak istiyorsanız:

```php
use App\Models\Post;
use App\Models\User;
 
Route::get('/users/{user}/posts/{post:slug}', function (User $user, Post $post) {
    return $post;
});
```

Bu durumda Laravel, **User** modelinde `posts` (rota parametresinin çoğul hali) adında bir ilişki olduğunu varsayar ve **Post** modelini bu ilişki üzerinden çözümler.

Eğer özel bir anahtar kullanmasanız bile alt modelleri kapsamlı (scoped) olarak çözümlemek isterseniz, rotayı tanımlarken **scopeBindings** metodunu çağırabilirsiniz:

```php
use App\Models\Post;
use App\Models\User;
 
Route::get('/users/{user}/posts/{post}', function (User $user, Post $post) {
    return $post;
})->scopeBindings();
```

Tüm bir rota grubuna da scoped binding uygulanabilir:

```php
Route::scopeBindings()->group(function () {
    Route::get('/users/{user}/posts/{post}', function (User $user, Post $post) {
        return $post;
    });
});
```

Scoped binding’i devre dışı bırakmak isterseniz, **withoutScopedBindings** metodunu çağırabilirsiniz:

```php
Route::get('/users/{user}/posts/{post:slug}', function (User $user, Post $post) {
    return $post;
})->withoutScopedBindings();
```

---

## **Eksik Model Davranışını Özelleştirme**

Varsayılan olarak, implicit olarak bağlanan bir model bulunamazsa **404 HTTP** yanıtı oluşturulur.
Ancak, bu davranışı özelleştirmek için **missing** metodunu kullanabilirsiniz.
Bu metod, model bulunamadığında çalıştırılacak bir closure kabul eder:

```php
use App\Http\Controllers\LocationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
 
Route::get('/locations/{location:slug}', [LocationsController::class, 'show'])
    ->name('locations.view')
    ->missing(function (Request $request) {
        return Redirect::route('locations.index');
    });
```

---

## **Implicit Enum Binding**

PHP 8.1 ile **Enum** desteği geldi.
Bu özellikle uyumlu olarak, Laravel route tanımlarında string tabanlı bir Enum’u type-hint edebilirsiniz.
Laravel, rota segmenti geçerli bir Enum değeriyle eşleşiyorsa rotayı çalıştırır; aksi halde otomatik olarak **404 HTTP** döndürür.

Örneğin, şu Enum tanımı:

```php
namespace App\Enums;
 
enum Category: string
{
    case Fruits = 'fruits';
    case People = 'people';
}
```

Aşağıdaki rota yalnızca `{category}` segmenti `fruits` veya `people` olduğunda çalışır:

```php
use App\Enums\Category;
use Illuminate\Support\Facades\Route;
 
Route::get('/categories/{category}', function (Category $category) {
    return $category->value;
});
```

---

## **Explicit Binding (Açık Bağlama)**

Model binding kullanmak için Laravel’in otomatik (implicit) çözümlemesine bağlı kalmak zorunda değilsiniz.
Model parametrelerinin nasıl çözümleneceğini **explicit (açıkça)** tanımlayabilirsiniz.
Bunun için **Route::model** metodunu kullanarak bir parametreye karşılık gelen modeli belirtin.
Bu tanımlamayı **AppServiceProvider** sınıfınızın **boot** metodunun başında yapmalısınız:

```php
use App\Models\User;
use Illuminate\Support\Facades\Route;
 
public function boot(): void
{
    Route::model('user', User::class);
}
```

Ardından `{user}` parametresini içeren bir rota tanımlayın:

```php
use App\Models\User;
 
Route::get('/users/{user}', function (User $user) {
    // ...
});
```

Artık `{user}` parametreleri **App\Models\User** modeline bağlanacaktır.
Yani `/users/1` isteği, veritabanındaki ID’si 1 olan User örneğini enjekte eder.

Eğer model bulunamazsa, otomatik olarak **404 HTTP** yanıtı döndürülür.

---

## **Çözümleme Mantığını Özelleştirme (Custom Resolution Logic)**

Kendi model çözümleme mantığınızı tanımlamak isterseniz, **Route::bind** metodunu kullanabilirsiniz.
Bu metoda verdiğiniz closure, URI segmentinin değerini alır ve rotaya enjekte edilecek model örneğini döndürmelidir:

```php
use App\Models\User;
use Illuminate\Support\Facades\Route;
 
public function boot(): void
{
    Route::bind('user', function (string $value) {
        return User::where('name', $value)->firstOrFail();
    });
}
```

Alternatif olarak, model sınıfınızda **resolveRouteBinding** metodunu geçersiz kılabilirsiniz:

```php
/**
 * Retrieve the model for a bound value.
 *
 * @param  mixed  $value
 * @param  string|null  $field
 * @return \Illuminate\Database\Eloquent\Model|null
 */
public function resolveRouteBinding($value, $field = null)
{
    return $this->where('name', $value)->firstOrFail();
}
```

Eğer rota implicit binding scoping kullanıyorsa, **resolveChildRouteBinding** metodu, ebeveyn modele bağlı alt modelin çözümlemesi için çağrılır:

```php
/**
 * Retrieve the child model for a bound value.
 *
 * @param  string  $childType
 * @param  mixed  $value
 * @param  string|null  $field
 * @return \Illuminate\Database\Eloquent\Model|null
 */
public function resolveChildRouteBinding($childType, $value, $field)
{
    return parent::resolveChildRouteBinding($childType, $value, $field);
}
```
# Fallback Rotaları

**Route::fallback** metodunu kullanarak, gelen isteğe hiçbir rota eşleşmediğinde çalıştırılacak bir rota tanımlayabilirsiniz.
Genellikle, işlenmeyen istekler uygulamanızın hata işleyicisi (exception handler) tarafından otomatik olarak bir “404” sayfası olarak işlenir.
Ancak, fallback rotasını genellikle **routes/web.php** dosyasında tanımlayacağınız için, **web middleware grubu** içindeki tüm middleware’ler bu rota için de geçerli olur.
Gerekirse bu rotaya ek middleware’ler de ekleyebilirsiniz:

```php
Route::fallback(function () {
    // ...
});
```

---

# Rate Limiting (Oran Sınırlama)

## Rate Limiter Tanımlama

Laravel, belirli rotalar veya rota grupları için trafiği sınırlamak amacıyla güçlü ve özelleştirilebilir **rate limiting** servisleri sağlar.
Başlamak için, uygulamanızın ihtiyaçlarına uygun rate limiter yapılandırmalarını tanımlamalısınız.

Rate limiter’lar, uygulamanızın **App\Providers\AppServiceProvider** sınıfının **boot** metodunda tanımlanabilir:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
 
protected function boot(): void
{
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });
}
```

Rate limiter’lar, **RateLimiter** facade’ının **for** metodu kullanılarak tanımlanır.
Bu metod, bir limiter adı ve limit yapılandırmasını döndüren bir closure alır.
Limit yapılandırması, **Illuminate\Cache\RateLimiting\Limit** sınıfının bir örneğidir.

Örneğin:

```php
RateLimiter::for('global', function (Request $request) {
    return Limit::perMinute(1000);
});
```

Eğer gelen istek belirtilen limiti aşarsa, Laravel otomatik olarak **429 HTTP** yanıtı döndürür.
Kendi özel yanıtınızı döndürmek isterseniz, **response** metodunu kullanabilirsiniz:

```php
RateLimiter::for('global', function (Request $request) {
    return Limit::perMinute(1000)->response(function (Request $request, array $headers) {
        return response('Custom response...', 429, $headers);
    });
});
```

Rate limiter callback’i gelen **HTTP request** örneğini aldığı için, isteğe veya kimliği doğrulanmış kullanıcıya göre dinamik bir limit de oluşturabilirsiniz:

```php
RateLimiter::for('uploads', function (Request $request) {
    return $request->user()->vipCustomer()
        ? Limit::none()
        : Limit::perHour(10);
});
```

---

## Rate Limit Segmentasyonu

Bazen rate limitleri belirli değerlere göre bölmek isteyebilirsiniz.
Örneğin, bir IP adresi başına dakikada 100 isteğe izin vermek isteyebilirsiniz:

```php
RateLimiter::for('uploads', function (Request $request) {
    return $request->user()->vipCustomer()
        ? Limit::none()
        : Limit::perMinute(100)->by($request->ip());
});
```

Başka bir örnekte, kimliği doğrulanmış kullanıcılar için dakikada 100, misafir kullanıcılar için dakikada 10 istekle sınırlandırabilirsiniz:

```php
RateLimiter::for('uploads', function (Request $request) {
    return $request->user()
        ? Limit::perMinute(100)->by($request->user()->id)
        : Limit::perMinute(10)->by($request->ip());
});
```

---

## Birden Fazla Rate Limit

Gerekirse, bir rate limiter yapılandırması için bir **dizi (array)** limit döndürebilirsiniz.
Dizideki her limit, sırayla değerlendirilir:

```php
RateLimiter::for('login', function (Request $request) {
    return [
        Limit::perMinute(500),
        Limit::perMinute(3)->by($request->input('email')),
    ];
});
```

Aynı `by` değerine göre segmentlenmiş birden fazla rate limit atıyorsanız, her `by` değerinin **benzersiz** olduğundan emin olun.
Bunu kolayca yapmak için, `by` değerine bir önek (prefix) ekleyebilirsiniz:

```php
RateLimiter::for('uploads', function (Request $request) {
    return [
        Limit::perMinute(10)->by('minute:'.$request->user()->id),
        Limit::perDay(1000)->by('day:'.$request->user()->id),
    ];
});
```

---

## Response-Based Rate Limiting

Laravel, yalnızca belirli **HTTP yanıtlarına göre** rate limit uygulamanıza da olanak tanır.
Bu, yalnızca doğrulama hataları, 404 yanıtları veya belirli durum kodlarını sınırlamak istediğinizde kullanışlıdır.

**after** metodu bir closure alır ve yanıt sayılmalıysa `true`, sayılmamalıysa `false` döndürmelidir:

```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
 
RateLimiter::for('resource-not-found', function (Request $request) {
    return Limit::perMinute(10)
        ->by($request->user()?->id ?: $request->ip())
        ->after(function (Response $response) {
            // Sadece 404 yanıtlarını say...
            return $response->status() === 404;
        });
});
```

---

## Rotalara Rate Limiter Ekleme

Rate limiter’lar, **throttle** middleware’i kullanılarak rotalara veya rota gruplarına eklenebilir.
Bu middleware, atamak istediğiniz limiter adını kabul eder:

```php
Route::middleware(['throttle:uploads'])->group(function () {
    Route::post('/audio', function () {
        // ...
    });
 
    Route::post('/video', function () {
        // ...
    });
});
```

---

## Redis ile Throttling

Varsayılan olarak, **throttle** middleware’i **Illuminate\Routing\Middleware\ThrottleRequests** sınıfına eşlenmiştir.
Ancak, uygulamanızda Redis cache sürücüsünü kullanıyorsanız, rate limiting işlemlerini Redis ile yönetmek için Laravel’i yapılandırabilirsiniz.

Bunun için, uygulamanızın **bootstrap/app.php** dosyasında **throttleWithRedis** metodunu çağırın:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->throttleWithRedis();
    // ...
})
```

---

# Form Method Spoofing

HTML formları **PUT**, **PATCH** veya **DELETE** eylemlerini desteklemez.
Bu nedenle, bu tür rotaları çağıran bir form tanımlarken, forma gizli bir `_method` alanı eklemeniz gerekir:

```html
<form action="/example" method="POST">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>
```

Kolaylık için, `_method` alanını oluşturmak için **@method** Blade direktifini kullanabilirsiniz:

```html
<form action="/example" method="POST">
    @method('PUT')
    @csrf
</form>
```

---

# Geçerli Rotaya Erişim

Gelen isteği işleyen rotayla ilgili bilgilere erişmek için, **Route** facade’ındaki şu metotları kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Route;
 
$route = Route::current(); // Illuminate\Routing\Route
$name = Route::currentRouteName(); // string
$action = Route::currentRouteAction(); // string
```

---

# CORS (Cross-Origin Resource Sharing)

Laravel, CORS (Cross-Origin Resource Sharing) OPTIONS HTTP isteklerine otomatik olarak yanıt verebilir.
Bu, uygulamanızın global middleware yığınına dahil edilen **HandleCors** middleware’i tarafından yönetilir.

CORS yapılandırma değerlerini özelleştirmeniz gerekirse, aşağıdaki komutla `cors.php` yapılandırma dosyasını yayımlayabilirsiniz:

```bash
php artisan config:publish cors
```

Bu komut, uygulamanızın **config** dizinine bir `cors.php` dosyası ekler.

Daha fazla bilgi için MDN belgelerindeki **CORS** dökümanına başvurabilirsiniz.

---

# Route Caching

Uygulamanızı üretime (production) dağıtırken, **Laravel’in route cache** özelliğinden yararlanmalısınız.
Route cache kullanmak, rotaların kayıt süresini büyük ölçüde azaltır.

Route cache oluşturmak için:

```bash
php artisan route:cache
```

Bu komut çalıştırıldığında, oluşturulan önbellek dosyası her istekte yüklenecektir.
Yeni bir rota eklerseniz, route cache’i yeniden oluşturmanız gerekir.
Bu nedenle, **route:cache** komutunu yalnızca dağıtım sürecinde çalıştırmalısınız.

Route cache’i temizlemek için:

```bash
php artisan route:clear
```
