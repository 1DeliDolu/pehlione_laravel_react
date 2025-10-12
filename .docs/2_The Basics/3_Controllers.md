### Denetleyiciler

#### Giriş

Tüm istek işleme mantığınızı rota dosyalarınızda closure olarak tanımlamak yerine, bu davranışı “controller” sınıfları kullanarak düzenlemek isteyebilirsiniz. Controller’lar, ilgili istek işleme mantığını tek bir sınıfta gruplayabilir. Örneğin, bir `UserController` sınıfı, kullanıcılarla ilgili gelen tüm istekleri (gösterme, oluşturma, güncelleme ve silme gibi) ele alabilir. Varsayılan olarak, controller’lar `app/Http/Controllers` dizininde saklanır.

#### Controller Yazma

##### Temel Controller’lar

Hızlıca yeni bir controller oluşturmak için `make:controller` Artisan komutunu çalıştırabilirsiniz. Varsayılan olarak, uygulamanızdaki tüm controller’lar `app/Http/Controllers` dizininde saklanır:

```bash
php artisan make:controller UserController
```

Basit bir controller örneğine bakalım. Bir controller, gelen HTTP isteklerine yanıt verecek herhangi sayıda public metoda sahip olabilir:

```php
<?php
 
namespace App\Http\Controllers;
 
use App\Models\User;
use Illuminate\View\View;
 
class UserController extends Controller
{
    /**
     * Belirli bir kullanıcının profilini göster.
     */
    public function show(string $id): View
    {
        return view('user.profile', [
            'user' => User::findOrFail($id)
        ]);
    }
}
```

Bir controller sınıfı ve metodu yazdıktan sonra, bu metoda yönlendirilen bir rota tanımlayabilirsiniz:

```php
use App\Http\Controllers\UserController;
 
Route::get('/user/{id}', [UserController::class, 'show']);
```

Belirtilen rota URI’siyle eşleşen bir istek geldiğinde, `App\Http\Controllers\UserController` sınıfındaki `show` metodu çağrılacak ve rota parametreleri metoda aktarılacaktır.

Controller’ların bir temel sınıftan türetilmesi zorunlu değildir. Ancak, tüm controller’larınız arasında paylaşılması gereken metodlar varsa, bir temel controller sınıfını genişletmek bazen kullanışlı olabilir.

#### Tek Eylemli Controller’lar

Bir controller eylemi özellikle karmaşıksa, tüm bir controller sınıfını o tek eyleme adamak isteyebilirsiniz. Bunu başarmak için controller içinde yalnızca bir `__invoke` metodu tanımlayabilirsiniz:

```php
<?php
 
namespace App\Http\Controllers;
 
class ProvisionServer extends Controller
{
    /**
     * Yeni bir web sunucusu oluştur.
     */
    public function __invoke()
    {
        // ...
    }
}
```

Tek eylemli controller’lar için rota kaydederken, bir controller metodu belirtmeniz gerekmez. Bunun yerine, sadece controller’ın adını yönlendiriciye geçebilirsiniz:

```php
use App\Http\Controllers\ProvisionServer;
 
Route::post('/server', ProvisionServer::class);
```

`--invokable` seçeneğini kullanarak çağrılabilir (invokable) bir controller oluşturabilirsiniz:

```bash
php artisan make:controller ProvisionServer --invokable
```

Controller şablonları (stub’ları) stub yayınlama kullanılarak özelleştirilebilir.

### Controller Middleware

Middleware, controller’ın rotalarına rota dosyalarınızda atanabilir:

```php
Route::get('/profile', [UserController::class, 'show'])->middleware('auth');
```

Ya da, middleware’leri doğrudan controller sınıfı içinde belirtmek daha kullanışlı olabilir. Bunu yapmak için controller’ınız `HasMiddleware` arayüzünü (interface) uygulamalıdır; bu, controller’ın statik bir `middleware` metoduna sahip olması gerektiğini belirtir. Bu metottan, controller eylemlerine uygulanacak middleware’lerin bir dizisini döndürebilirsiniz:

```php
<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
 
class UserController extends Controller implements HasMiddleware
{
    /**
     * Controller’a atanacak middleware’leri al.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('log', only: ['index']),
            new Middleware('subscribed', except: ['store']),
        ];
    }
 
    // ...
}
```

Controller middleware’lerini closure olarak da tanımlayabilirsiniz; bu, tüm bir middleware sınıfı yazmadan satır içi bir middleware tanımlamak için kullanışlı bir yoldur:

```php
use Closure;
use Illuminate\Http\Request;
 
/**
 * Controller’a atanacak middleware’leri al.
 */
public static function middleware(): array
{
    return [
        function (Request $request, Closure $next) {
            return $next($request);
        },
    ];
}
```

---

### Resource Controller’lar

Uygulamanızdaki her bir Eloquent modeli bir “resource” olarak düşünürseniz, genellikle her bir resource üzerinde aynı işlemler kümesini gerçekleştirirsiniz. Örneğin, uygulamanızda bir `Photo` modeli ve bir `Movie` modeli olduğunu hayal edin. Kullanıcıların bu kaynakları oluşturması, okuması, güncellemesi veya silmesi muhtemeldir.

Bu yaygın kullanım durumundan dolayı, Laravel resource yönlendirmesi tipik “create, read, update ve delete (CRUD)” rotalarını tek bir kod satırıyla bir controller’a atar. Başlamak için `make:controller` Artisan komutunun `--resource` seçeneğini kullanarak bu işlemleri yönetecek bir controller oluşturabilirsiniz:

```bash
php artisan make:controller PhotoController --resource
```

Bu komut, `app/Http/Controllers/PhotoController.php` konumunda bir controller oluşturur. Bu controller, mevcut resource işlemlerinin her biri için bir metoda sahip olacaktır. Daha sonra, bu controller’a işaret eden bir resource rotası kaydedebilirsiniz:

```php
use App\Http\Controllers\PhotoController;
 
Route::resource('photos', PhotoController::class);
```

Bu tek rota bildirimi, resource üzerinde çeşitli işlemleri gerçekleştirmek için birden çok rota oluşturur. Oluşturulan controller, bu işlemlerin her biri için halihazırda stub (taslak) metodlara sahiptir. Uygulamanızdaki rotaların genel görünümünü hızlıca görmek için `route:list` Artisan komutunu çalıştırabilirsiniz.

Birden fazla resource controller’ı aynı anda kaydetmek için `resources` metoduna bir dizi (array) aktarabilirsiniz:

```php
Route::resources([
    'photos' => PhotoController::class,
    'posts' => PostController::class,
]);
```

`softDeletableResources` metodu, tümü `withTrashed` metodunu kullanan birden fazla resource controller kaydeder:

```php
Route::softDeletableResources([
    'photos' => PhotoController::class,
    'posts' => PostController::class,
]);
```

---

### Resource Controller’ların Ele Aldığı Eylemler

| HTTP Verb | URI                  | Action  | Route Name     |
| --------- | -------------------- | ------- | -------------- |
| GET       | /photos              | index   | photos.index   |
| GET       | /photos/create       | create  | photos.create  |
| POST      | /photos              | store   | photos.store   |
| GET       | /photos/{photo}      | show    | photos.show    |
| GET       | /photos/{photo}/edit | edit    | photos.edit    |
| PUT/PATCH | /photos/{photo}      | update  | photos.update  |
| DELETE    | /photos/{photo}      | destroy | photos.destroy |

---

### Eksik Model Davranışını Özelleştirme

Genellikle, dolaylı olarak bağlanan bir resource modeli bulunamazsa bir `404` HTTP yanıtı oluşturulur. Ancak, `missing` metodunu çağırarak bu davranışı özelleştirebilirsiniz. Bu metot, resource’un herhangi bir rotası için model bulunamadığında çalıştırılacak bir closure kabul eder:

```php
use App\Http\Controllers\PhotoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
 
Route::resource('photos', PhotoController::class)
    ->missing(function (Request $request) {
        return Redirect::route('photos.index');
    });
```

---

### Soft Deleted Modeller

Genellikle, dolaylı model bağlama (implicit model binding) soft delete edilmiş modelleri getirmez ve bunun yerine `404` HTTP yanıtı döndürür. Ancak, resource rotanızı tanımlarken `withTrashed` metodunu çağırarak framework’ü soft delete edilmiş modelleri kabul etmesi için yönlendirebilirsiniz:

```php
use App\Http\Controllers\PhotoController;
 
Route::resource('photos', PhotoController::class)->withTrashed();
```

`withTrashed` metoduna hiçbir argüman verilmezse, bu metod `show`, `edit` ve `update` rotaları için soft delete edilmiş modellerin kullanılmasına izin verir. Bu rotaların bir alt kümesini belirtmek için metoda bir dizi aktarabilirsiniz:

```php
Route::resource('photos', PhotoController::class)->withTrashed(['show']);
```

---

### Resource Model Belirtme

Rota model bağlaması (route model binding) kullanıyorsanız ve resource controller metodlarınızın bir model örneğini (instance) tür ipucu (type-hint) olarak almasını istiyorsanız, controller oluştururken `--model` seçeneğini kullanabilirsiniz:

```bash
php artisan make:controller PhotoController --model=Photo --resource
```

---

### Form Request Oluşturma

Bir resource controller oluştururken `--requests` seçeneğini ekleyerek Artisan’a, controller’ın `store` ve `update` metodları için form request sınıfları oluşturmasını söyleyebilirsiniz:

```bash
php artisan make:controller PhotoController --model=Photo --resource --requests
```

---

### Kısmi Resource Rotaları

Bir resource rotası tanımlarken, controller’ın yönetmesi gereken eylemlerin tam kümesi yerine yalnızca bir alt kümesini belirtebilirsiniz:

```php
use App\Http\Controllers\PhotoController;
 
Route::resource('photos', PhotoController::class)->only([
    'index', 'show'
]);
 
Route::resource('photos', PhotoController::class)->except([
    'create', 'store', 'update', 'destroy'
]);
```

---

### API Resource Rotaları

API’ler tarafından tüketilecek resource rotalarını tanımlarken, genellikle `create` ve `edit` gibi HTML şablonları sunan rotaları hariç tutmak istersiniz. Kolaylık sağlamak için, bu iki rotayı otomatik olarak hariç tutan `apiResource` metodunu kullanabilirsiniz:

```php
use App\Http\Controllers\PhotoController;
 
Route::apiResource('photos', PhotoController::class);
```

Birden fazla API resource controller’ı aynı anda kaydetmek için `apiResources` metoduna bir dizi aktarabilirsiniz:

```php
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PostController;
 
Route::apiResources([
    'photos' => PhotoController::class,
    'posts' => PostController::class,
]);
```

`make:controller` komutunu çalıştırırken `--api` anahtarını kullanarak, `create` veya `edit` metodlarını içermeyen bir API resource controller hızlıca oluşturabilirsiniz:

```bash
php artisan make:controller PhotoController --api
```
### İç İçe (Nested) Resource’lar

Bazen bir iç içe geçmiş resource’a rota tanımlamanız gerekebilir. Örneğin, bir “photo” resource’u, fotoğrafa bağlı birden fazla yorum (comment) içerebilir. Resource controller’ları iç içe tanımlamak için rota tanımınızda “nokta (dot)” gösterimini kullanabilirsiniz:

```php
use App\Http\Controllers\PhotoCommentController;
 
Route::resource('photos.comments', PhotoCommentController::class);
```

Bu rota, aşağıdaki gibi URI’lerle erişilebilen iç içe bir resource kaydeder:

```
/photos/{photo}/comments/{comment}
```

---

### İç İçe Resource’ları Kapsamlandırma (Scoping)

Laravel’in dolaylı model bağlama (implicit model binding) özelliği, iç içe geçmiş binding’leri otomatik olarak kapsamlandırabilir; böylece çözümlenen alt modelin (child model) üst modele (parent model) ait olduğu doğrulanır. İç içe resource’unuzu tanımlarken `scoped` metodunu kullanarak otomatik kapsamlandırmayı etkinleştirebilir ve Laravel’e alt resource’un hangi alan üzerinden getirileceğini belirtebilirsiniz.
Bu konunun nasıl gerçekleştirileceği hakkında daha fazla bilgi için resource rotalarının kapsamlandırılmasıyla ilgili belgeleri inceleyebilirsiniz.

---

### Yüzeysel İç İçe Yapı (Shallow Nesting)

Genellikle, URI içinde hem üst (parent) hem de alt (child) kimliklere (ID) ihtiyaç duyulmaz; çünkü alt kimlik zaten benzersiz bir tanımlayıcıdır. Modellerinizi URI segmentlerinde tanımlamak için otomatik artan birincil anahtar (auto-incrementing primary key) gibi benzersiz tanımlayıcılar kullanıyorsanız, “shallow nesting” yöntemini kullanabilirsiniz:

```php
use App\Http\Controllers\CommentController;
 
Route::resource('photos.comments', CommentController::class)->shallow();
```

Bu rota tanımı aşağıdaki rotaları oluşturur:

| Verb      | URI                             | Action  | Route Name             |
| --------- | ------------------------------- | ------- | ---------------------- |
| GET       | /photos/{photo}/comments        | index   | photos.comments.index  |
| GET       | /photos/{photo}/comments/create | create  | photos.comments.create |
| POST      | /photos/{photo}/comments        | store   | photos.comments.store  |
| GET       | /comments/{comment}             | show    | comments.show          |
| GET       | /comments/{comment}/edit        | edit    | comments.edit          |
| PUT/PATCH | /comments/{comment}             | update  | comments.update        |
| DELETE    | /comments/{comment}             | destroy | comments.destroy       |

---

### Resource Rotalarını Adlandırma

Varsayılan olarak, tüm resource controller eylemlerinin bir rota adı vardır; ancak, kendi istediğiniz rota adlarını belirterek bu adları geçersiz kılabilirsiniz:

```php
use App\Http\Controllers\PhotoController;
 
Route::resource('photos', PhotoController::class)->names([
    'create' => 'photos.build'
]);
```

---

### Resource Rota Parametrelerini Adlandırma

Varsayılan olarak, `Route::resource` resource adının “tekil hâli”ni kullanarak rota parametrelerini oluşturur. Bunu, her bir resource için `parameters` metodunu kullanarak kolayca özelleştirebilirsiniz. Bu metoda geçirilen dizi, resource adları ile parametre adlarının eşlendiği ilişkisel bir dizidir:

```php
use App\Http\Controllers\AdminUserController;
 
Route::resource('users', AdminUserController::class)->parameters([
    'users' => 'admin_user'
]);
```

Yukarıdaki örnek, resource’un `show` rotası için şu URI’yı üretir:

```
/users/{admin_user}
```

---

### Resource Rotalarını Kapsamlandırma (Scoping Resource Routes)

Laravel’in kapsamlandırılmış dolaylı model bağlama (scoped implicit model binding) özelliği, iç içe geçmiş binding’leri otomatik olarak kapsamlandırabilir; böylece çözümlenen alt modelin üst modele ait olduğu doğrulanır. İç içe resource tanımlarken `scoped` metodunu kullanarak otomatik kapsamlandırmayı etkinleştirebilir ve alt resource’un hangi alan üzerinden getirileceğini belirtebilirsiniz:

```php
use App\Http\Controllers\PhotoCommentController;
 
Route::resource('photos.comments', PhotoCommentController::class)->scoped([
    'comment' => 'slug',
]);
```

Bu rota, aşağıdaki gibi URI’lerle erişilebilen kapsamlandırılmış bir iç içe resource kaydeder:

```
/photos/{photo}/comments/{comment:slug}
```

Dolaylı bağlama (implicit binding) bir özel anahtar (custom key) kullanıyorsa, Laravel iç içe modeli, üst model üzerinden otomatik olarak kapsamlandırır ve ilişki adını tahmin etmek için konvansiyonları kullanır. Bu durumda, `Photo` modelinin `comments` adlı bir ilişkiye sahip olduğu varsayılır (rota parametresinin çoğul hali).

---

### Resource URI’larını Yerelleştirme

Varsayılan olarak, `Route::resource` İngilizce fiilleri ve çoğul kuralları kullanarak resource URI’ları oluşturur. `create` ve `edit` fiillerini yerelleştirmeniz gerekiyorsa, `Route::resourceVerbs` metodunu kullanabilirsiniz. Bu, uygulamanızın `App\Providers\AppServiceProvider` sınıfındaki `boot` metodunun başında yapılabilir:

```php
/**
 * Uygulama servislerini başlat.
 */
public function boot(): void
{
    Route::resourceVerbs([
        'create' => 'crear',
        'edit' => 'editar',
    ]);
}
```

Laravel’in çoğullaştırıcısı (pluralizer) birden fazla dili destekler ve ihtiyaçlarınıza göre yapılandırılabilir.
Fiiller ve çoğullaştırma dili özelleştirildikten sonra, aşağıdaki gibi bir resource kaydı:

```php
Route::resource('publicacion', PublicacionController::class);
```

şu URI’ları üretir:

```
/publicacion/crear
/publicacion/{publicaciones}/editar
```

---

### Resource Controller’ları Tamamlayıcı Rotalar

Varsayılan resource rotalarının ötesinde ek rotalar eklemeniz gerekiyorsa, bu rotaları `Route::resource` çağrısından **önce** tanımlamalısınız; aksi takdirde, `resource` metodu tarafından tanımlanan rotalar bu ek rotalarınızın önüne geçebilir:

```php
use App\Http\Controller\PhotoController;
 
Route::get('/photos/popular', [PhotoController::class, 'popular']);
Route::resource('photos', PhotoController::class);
```

Controller’larınızı odaklı tutmayı unutmayın. Sık sık varsayılan resource eylemleri dışında metodlara ihtiyaç duyuyorsanız, controller’ınızı iki daha küçük controller’a bölmeyi düşünün.

---

### Singleton Resource Controller’lar

Bazen, uygulamanız yalnızca tek bir örneğe (instance) sahip olabilecek resource’lara sahip olabilir. Örneğin, bir kullanıcının “profile”ı düzenlenebilir veya güncellenebilir, ancak bir kullanıcının birden fazla “profile”ı olamaz. Benzer şekilde, bir resim yalnızca tek bir “thumbnail”e sahip olabilir. Bu tür resource’lar “singleton resource” olarak adlandırılır — yani yalnızca tek bir örnek vardır.
Bu senaryolarda, bir “singleton” resource controller kaydedebilirsiniz:

```php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
 
Route::singleton('profile', ProfileController::class);
```

Yukarıdaki singleton resource tanımı aşağıdaki rotaları kaydeder. Görüldüğü gibi, “oluşturma” rotaları (creation routes) singleton resource’lar için kaydedilmez ve yalnızca bir örnek bulunduğu için kimlik (identifier) kabul edilmez:

| Verb      | URI           | Action | Route Name     |
| --------- | ------------- | ------ | -------------- |
| GET       | /profile      | show   | profile.show   |
| GET       | /profile/edit | edit   | profile.edit   |
| PUT/PATCH | /profile      | update | profile.update |

Singleton resource’lar, standart bir resource’un içinde de iç içe tanımlanabilir:

```php
Route::singleton('photos.thumbnail', ThumbnailController::class);
```

Bu örnekte, `photos` resource’u tüm standart resource rotalarını alırken, `thumbnail` resource’u aşağıdaki gibi bir singleton resource olur:

| Verb      | URI                            | Action | Route Name              |
| --------- | ------------------------------ | ------ | ----------------------- |
| GET       | /photos/{photo}/thumbnail      | show   | photos.thumbnail.show   |
| GET       | /photos/{photo}/thumbnail/edit | edit   | photos.thumbnail.edit   |
| PUT/PATCH | /photos/{photo}/thumbnail      | update | photos.thumbnail.update |

---

### Oluşturulabilir Singleton Resource’lar

Bazen, bir singleton resource için oluşturma (creation) ve depolama (storage) rotaları tanımlamak isteyebilirsiniz. Bunu gerçekleştirmek için, singleton resource rotasını kaydederken `creatable` metodunu çağırabilirsiniz:

```php
Route::singleton('photos.thumbnail', ThumbnailController::class)->creatable();
```

Bu durumda, aşağıdaki rotalar kaydedilecektir. Görüldüğü gibi, oluşturulabilir singleton resource’lar için bir `DELETE` rotası da kaydedilir:

| Verb      | URI                              | Action  | Route Name               |
| --------- | -------------------------------- | ------- | ------------------------ |
| GET       | /photos/{photo}/thumbnail/create | create  | photos.thumbnail.create  |
| POST      | /photos/{photo}/thumbnail        | store   | photos.thumbnail.store   |
| GET       | /photos/{photo}/thumbnail        | show    | photos.thumbnail.show    |
| GET       | /photos/{photo}/thumbnail/edit   | edit    | photos.thumbnail.edit    |
| PUT/PATCH | /photos/{photo}/thumbnail        | update  | photos.thumbnail.update  |
| DELETE    | /photos/{photo}/thumbnail        | destroy | photos.thumbnail.destroy |

Eğer Laravel’in bir singleton resource için `DELETE` rotasını kaydetmesini, ancak `create` veya `store` rotalarını kaydetmemesini istiyorsanız, `destroyable` metodunu kullanabilirsiniz:

```php
Route::singleton(...)->destroyable();
```
### API Singleton Resource’lar

`apiSingleton` metodu, bir API aracılığıyla yönetilecek (create ve edit rotalarına ihtiyaç duymayan) bir singleton resource kaydetmek için kullanılabilir:

```php
Route::apiSingleton('profile', ProfileController::class);
```

Elbette, API singleton resource’lar **oluşturulabilir (creatable)** da olabilir; bu durumda resource için `store` ve `destroy` rotaları kaydedilir:

```php
Route::apiSingleton('photos.thumbnail', ProfileController::class)->creatable();
```

---

### Middleware ve Resource Controller’lar

Laravel, `middleware`, `middlewareFor` ve `withoutMiddlewareFor` metotlarını kullanarak resource rotalarının tümüne veya yalnızca belirli metotlarına middleware atamanıza olanak tanır.
Bu metotlar, her bir resource eylemine hangi middleware’lerin uygulanacağı üzerinde ince ayar yapmanızı sağlar.

---

### Tüm Metotlara Middleware Uygulama

Bir resource veya singleton resource rotası tarafından oluşturulan **tüm rotalara** middleware atamak için `middleware` metodunu kullanabilirsiniz:

```php
Route::resource('users', UserController::class)
    ->middleware(['auth', 'verified']);
 
Route::singleton('profile', ProfileController::class)
    ->middleware('auth');
```

---

### Belirli Metotlara Middleware Uygulama

Bir resource controller’ın yalnızca bir veya birkaç metoduna middleware atamak için `middlewareFor` metodunu kullanabilirsiniz:

```php
Route::resource('users', UserController::class)
    ->middlewareFor('show', 'auth');
 
Route::apiResource('users', UserController::class)
    ->middlewareFor(['show', 'update'], 'auth');
 
Route::resource('users', UserController::class)
    ->middlewareFor('show', 'auth')
    ->middlewareFor('update', 'auth');
 
Route::apiResource('users', UserController::class)
    ->middlewareFor(['show', 'update'], ['auth', 'verified']);
```

`middlewareFor` metodu, singleton ve API singleton resource controller’larıyla birlikte de kullanılabilir:

```php
Route::singleton('profile', ProfileController::class)
    ->middlewareFor('show', 'auth');
 
Route::apiSingleton('profile', ProfileController::class)
    ->middlewareFor(['show', 'update'], 'auth');
```

---

### Belirli Metotlardan Middleware Hariç Tutma

Bir resource controller’ın belirli metotlarından middleware’i hariç tutmak için `withoutMiddlewareFor` metodunu kullanabilirsiniz:

```php
Route::middleware(['auth', 'verified', 'subscribed'])->group(function () {
    Route::resource('users', UserController::class)
        ->withoutMiddlewareFor('index', ['auth', 'verified'])
        ->withoutMiddlewareFor(['create', 'store'], 'verified')
        ->withoutMiddlewareFor('destroy', 'subscribed');
});
```

---

### Bağımlılık Enjeksiyonu ve Controller’lar

#### Constructor Injection

Laravel servis konteyneri, tüm Laravel controller’larını çözümlemek için kullanılır.
Bu nedenle, controller’ınızın ihtiyaç duyduğu bağımlılıkları (dependencies) yapıcı (constructor) içinde type-hint olarak belirtebilirsiniz.
Belirtilen bağımlılıklar, controller örneğine otomatik olarak çözülüp enjekte edilir:

```php
<?php
 
namespace App\Http\Controllers;
 
use App\Repositories\UserRepository;
 
class UserController extends Controller
{
    /**
     * Yeni bir controller örneği oluştur.
     */
    public function __construct(
        protected UserRepository $users,
    ) {}
}
```

---

#### Method Injection

Constructor injection’a ek olarak, controller metotlarınızda da bağımlılık enjeksiyonu (dependency injection) kullanabilirsiniz.
En yaygın kullanım örneği, `Illuminate\Http\Request` örneğini controller metotlarınıza enjekte etmektir:

```php
<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
 
class UserController extends Controller
{
    /**
     * Yeni bir kullanıcıyı kaydet.
     */
    public function store(Request $request): RedirectResponse
    {
        $name = $request->name;
 
        // Kullanıcıyı kaydet...
 
        return redirect('/users');
    }
}
```

Eğer controller metodunuz ayrıca bir rota parametresinden girdi bekliyorsa, rota argümanlarını diğer bağımlılıklardan **sonra** listeleyin.
Örneğin, rotanız aşağıdaki gibi tanımlandıysa:

```php
use App\Http\Controllers\UserController;
 
Route::put('/user/{id}', [UserController::class, 'update']);
```

Yine de `Illuminate\Http\Request`’i type-hint olarak belirtebilir ve `id` parametresine erişebilirsiniz.
Controller metodunuzu şu şekilde tanımlayabilirsiniz:

```php
<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
 
class UserController extends Controller
{
    /**
     * Verilen kullanıcıyı güncelle.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        // Kullanıcıyı güncelle...
 
        return redirect('/users');
    }
}
```
