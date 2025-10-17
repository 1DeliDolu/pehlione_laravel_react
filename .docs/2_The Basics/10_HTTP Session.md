
HTTP Oturumu  
Giriş  
HTTP tabanlı uygulamalar durum bilgisi taşımadığı için, oturumlar kullanıcı hakkında bilgileri birden fazla istek boyunca saklamanın bir yolunu sağlar. Bu kullanıcı bilgileri genellikle kalıcı bir depolama alanına / backend’e yerleştirilir ve sonraki isteklerde erişilebilir hale gelir.  

Laravel, çeşitli oturum backend’leriyle birlikte gelir ve bu backend’lere erişim için açık, birleşik bir API sunar. Memcached, Redis ve veritabanları gibi popüler backend’ler için destek dahildir.  

Yapılandırma  
Uygulamanızın oturum yapılandırma dosyası `config/session.php` içinde saklanır. Bu dosyada size sunulan seçenekleri mutlaka gözden geçirin. Varsayılan olarak, Laravel veritabanı oturum sürücüsünü (session driver) kullanacak şekilde yapılandırılmıştır.  

`session driver` yapılandırma seçeneği, her istek için oturum verilerinin nerede saklanacağını tanımlar. Laravel çeşitli sürücüler içerir:  

- `file` - oturumlar `storage/framework/sessions` içinde saklanır.  
- `cookie` - oturumlar güvenli, şifrelenmiş çerezlerde saklanır.  
- `database` - oturumlar bir ilişkisel veritabanında saklanır.  
- `memcached / redis` - oturumlar bu hızlı, önbellek tabanlı depolarda saklanır.  
- `dynamodb` - oturumlar AWS DynamoDB’de saklanır.  
- `array` - oturumlar bir PHP dizisinde saklanır ve kalıcı hale getirilmez.  

`array` sürücüsü genellikle test sırasında kullanılır ve oturumda saklanan verilerin kalıcı olmasını engeller.  

Sürücü Önkoşulları  
**Veritabanı**  
Veritabanı oturum sürücüsünü kullanırken, oturum verilerini içerecek bir veritabanı tablosuna sahip olduğunuzdan emin olmalısınız. Genellikle bu, Laravel’in varsayılan `0001_01_01_000000_create_users_table.php` veritabanı migrasyonuna dahildir; ancak, herhangi bir nedenle `sessions` tablonuz yoksa, bu migrasyonu oluşturmak için `make:session-table` Artisan komutunu kullanabilirsiniz:  

```bash
php artisan make:session-table
php artisan migrate
````

**Redis**
Laravel ile Redis oturumlarını kullanmadan önce, PECL aracılığıyla PhpRedis PHP uzantısını yüklemeli veya Composer aracılığıyla `predis/predis` paketini (~1.0) yüklemelisiniz. Redis’in yapılandırılması hakkında daha fazla bilgi için Laravel’in Redis dokümantasyonuna bakın.

`SESSION_CONNECTION` ortam değişkeni veya `session.php` yapılandırma dosyasındaki `connection` seçeneği, oturum depolaması için hangi Redis bağlantısının kullanılacağını belirlemek için kullanılabilir.

Oturumla Etkileşim
**Veri Alma**
Laravel’de oturum verileriyle çalışmanın iki ana yolu vardır: global `session` helper’ı ve `Request` örneği aracılığıyla. Önce, bir `Request` örneği aracılığıyla oturuma erişmeye bakalım; bu, bir route closure veya controller metodunda type-hint ile belirtilebilir. Controller metodu bağımlılıkları, Laravel servis container’ı aracılığıyla otomatik olarak enjekte edilir:

```php
<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\View\View;
 
class UserController extends Controller
{
    /**
     * Show the profile for the given user.
     */
    public function show(Request $request, string $id): View
    {
        $value = $request->session()->get('key');
 
        // ...
 
        $user = $this->users->find($id);
 
        return view('user.profile', ['user' => $user]);
    }
}
```

Oturumdan bir öğe alırken, `get` metoduna ikinci argüman olarak varsayılan bir değer de geçebilirsiniz. Belirtilen anahtar oturumda mevcut değilse bu varsayılan değer döndürülür. Eğer varsayılan değeri bir closure olarak geçirirseniz ve istenen anahtar mevcut değilse, closure çalıştırılır ve sonucu döndürülür:

```php
$value = $request->session()->get('key', 'default');
 
$value = $request->session()->get('key', function () {
    return 'default';
});
```

**Global Session Helper**
Global `session` PHP fonksiyonunu da oturumda veri almak ve depolamak için kullanabilirsiniz. `session` helper tek bir string argümanla çağrıldığında, o oturum anahtarının değerini döndürür. Helper bir dizi key/value çiftiyle çağrıldığında, bu değerler oturumda saklanır:

```php
Route::get('/home', function () {
    // Retrieve a piece of data from the session...
    $value = session('key');
 
    // Specifying a default value...
    $value = session('key', 'default');
 
    // Store a piece of data in the session...
    session(['key' => 'value']);
});
```

Bir HTTP request örneği aracılığıyla oturumu kullanmak ile global `session` helper’ını kullanmak arasında pratikte çok az fark vardır. Her iki yöntem de testlerinizde kullanılabilen `assertSessionHas` metodu aracılığıyla test edilebilir.

**Tüm Oturum Verilerini Alma**
Oturumdaki tüm verileri almak istiyorsanız, `all` metodunu kullanabilirsiniz:

```php
$data = $request->session()->all();
```

**Oturum Verilerinin Bir Kısmını Alma**
`only` ve `except` metodları, oturum verilerinin belirli bir alt kümesini almak için kullanılabilir:

```php
$data = $request->session()->only(['username', 'email']);
 
$data = $request->session()->except(['username', 'email']);
```

**Bir Öğenin Oturumda Mevcut Olup Olmadığını Belirleme**
Bir öğenin oturumda mevcut olup olmadığını belirlemek için `has` metodunu kullanabilirsiniz. `has` metodu, öğe mevcutsa ve `null` değilse `true` döndürür:

```php
if ($request->session()->has('users')) {
    // ...
}
```

Bir öğe oturumda mevcut olsa bile değeri `null` ise bunu kontrol etmek için `exists` metodunu kullanabilirsiniz:

```php
if ($request->session()->exists('users')) {
    // ...
}
```

Bir öğenin oturumda bulunmadığını kontrol etmek için `missing` metodunu kullanabilirsiniz. Bu metod, öğe mevcut değilse `true` döndürür:

```php
if ($request->session()->missing('users')) {
    // ...
}
```

**Veri Saklama**
Oturumda veri saklamak için genellikle request örneğinin `put` metodunu veya global `session` helper’ını kullanırsınız:

```php
// Via a request instance...
$request->session()->put('key', 'value');
 
// Via the global "session" helper...
session(['key' => 'value']);
```

**Dizi Oturum Değerlerine Ekleme (Push)**
`push` metodu, dizi olan bir oturum değerine yeni bir öğe eklemek için kullanılabilir. Örneğin, `user.teams` anahtarı takım adlarını içeren bir dizi barındırıyorsa, yeni bir değer şu şekilde eklenebilir:

```php
$request->session()->push('user.teams', 'developers');
```

**Bir Öğeyi Alma ve Silme**
`pull` metodu, bir öğeyi tek adımda oturumdan alır ve siler:

```php
$value = $request->session()->pull('key', 'default');
```

**Oturum Değerlerini Artırma ve Azaltma**
Oturum verilerinizde artırmak veya azaltmak istediğiniz bir tamsayı varsa, `increment` ve `decrement` metodlarını kullanabilirsiniz:

```php
$request->session()->increment('count');
 
$request->session()->increment('count', $incrementBy = 2);
 
$request->session()->decrement('count');
 
$request->session()->decrement('count', $decrementBy = 2);
```

**Flash Verileri**
Bazen bir öğeyi yalnızca bir sonraki istek için oturumda saklamak isteyebilirsiniz. Bunu `flash` metodunu kullanarak yapabilirsiniz. Bu yöntemle oturumda saklanan veriler hemen ve bir sonraki HTTP isteği boyunca kullanılabilir. Sonraki isteğin ardından bu veriler silinir. Flash verileri genellikle kısa süreli durum mesajları için kullanılır:

```php
$request->session()->flash('status', 'Task was successful!');
```

Flash verilerini birkaç istek boyunca kalıcı tutmanız gerekiyorsa, `reflash` metodunu kullanabilirsiniz. Bu, tüm flash verilerini ek bir istek boyunca korur. Yalnızca belirli flash verilerini tutmanız gerekiyorsa `keep` metodunu kullanabilirsiniz:

```php
$request->session()->reflash();
 
$request->session()->keep(['username', 'email']);
```

Flash verilerini yalnızca mevcut istek boyunca kalıcı tutmak istiyorsanız, `now` metodunu kullanabilirsiniz:

```php
$request->session()->now('status', 'Task was successful!');
```

**Veri Silme**
`forget` metodu, oturumdan belirli bir veriyi kaldırır. Oturumdaki tüm verileri kaldırmak istiyorsanız, `flush` metodunu kullanabilirsiniz:

```php
// Forget a single key...
$request->session()->forget('name');
 
// Forget multiple keys...
$request->session()->forget(['name', 'status']);
 
$request->session()->flush();
```

**Oturum ID’sini Yeniden Oluşturma**
Oturum ID’sinin yeniden oluşturulması genellikle kötü niyetli kullanıcıların uygulamanızda oturum sabitleme saldırısı gerçekleştirmesini önlemek için yapılır.

Laravel, Laravel Fortify veya başlangıç kitlerinden birini kullanıyorsanız kimlik doğrulama sırasında oturum ID’sini otomatik olarak yeniden oluşturur; ancak oturum ID’sini manuel olarak yeniden oluşturmanız gerekirse `regenerate` metodunu kullanabilirsiniz:

```php
$request->session()->regenerate();
```

Oturum ID’sini yeniden oluşturmak ve tüm oturum verilerini tek bir adımda kaldırmak istiyorsanız `invalidate` metodunu kullanabilirsiniz:

```php
$request->session()->invalidate();
```


Session Cache  
Laravel'ın session cache’i, bireysel kullanıcı oturumuna özgü verileri önbelleğe almak için kullanışlı bir yol sağlar. Global uygulama önbelleğinden farklı olarak, session cache verileri her oturum için otomatik olarak izole edilir ve oturum sona erdiğinde veya yok edildiğinde temizlenir. Session cache, `get`, `put`, `remember`, `forget` gibi tüm tanıdık Laravel cache metodlarını destekler, ancak mevcut oturuma göre kapsamlanmıştır.  

Session cache, aynı oturum içindeki birden fazla istek arasında kalıcı olmasını istediğiniz ancak kalıcı olarak saklamanız gerekmediği geçici, kullanıcıya özgü verileri depolamak için mükemmeldir. Buna form verileri, geçici hesaplamalar, API yanıtları veya belirli bir kullanıcının oturumuna bağlı olması gereken herhangi bir geçici veri dahildir.  

Session cache’e `session` üzerindeki `cache` metodu aracılığıyla erişebilirsiniz:  

```php
$discount = $request->session()->cache()->get('discount');
 
$request->session()->cache()->put(
    'discount', 10, now()->addMinutes(5)
);
````

Laravel’ın cache metodları hakkında daha fazla bilgi için cache dokümantasyonuna bakın.

Session Blocking
Session blocking’i kullanmak için, uygulamanızın atomik kilitleri (atomic locks) destekleyen bir cache sürücüsü kullanması gerekir. Şu anda bu sürücüler `memcached`, `dynamodb`, `redis`, `mongodb` (resmî `mongodb/laravel-mongodb` paketine dahil), `database`, `file` ve `array` sürücüleridir. Ayrıca `cookie` session driver’ını kullanamazsınız.

Varsayılan olarak, Laravel aynı oturumu kullanan isteklerin eşzamanlı olarak çalışmasına izin verir. Örneğin, bir JavaScript HTTP kütüphanesi kullanarak uygulamanıza iki HTTP isteği gönderirseniz, her ikisi de aynı anda çalışacaktır. Çoğu uygulama için bu bir sorun değildir; ancak, farklı endpoint’lere eşzamanlı olarak yapılan iki isteğin her ikisinin de oturuma veri yazdığı küçük bir senaryoda oturum verisi kaybı meydana gelebilir.

Bunu önlemek için Laravel, belirli bir oturum için eşzamanlı istekleri sınırlamanıza olanak tanıyan bir işlevsellik sağlar. Başlamak için, route tanımınıza `block` metodunu zincirleyebilirsiniz. Bu örnekte `/profile` endpoint’ine gelen bir istek bir session lock (oturum kilidi) alacaktır. Bu kilit tutulurken, aynı session ID’sini paylaşan `/profile` veya `/order` endpoint’lerine gelen diğer istekler ilk isteğin tamamlanmasını bekleyecek ve ardından çalışmaya devam edecektir:

```php
Route::post('/profile', function () {
    // ...
})->block($lockSeconds = 10, $waitSeconds = 10);
 
Route::post('/order', function () {
    // ...
})->block($lockSeconds = 10, $waitSeconds = 10);
```

`block` metodu iki isteğe bağlı argüman kabul eder. İlk argüman, oturum kilidinin serbest bırakılmadan önce tutulması gereken maksimum saniye sayısıdır. Elbette istek bu süreden önce tamamlanırsa, kilit daha erken serbest bırakılacaktır.

İkinci argüman, bir isteğin oturum kilidi almaya çalışırken beklemesi gereken saniye sayısıdır. Eğer istek belirtilen saniye içinde bir oturum kilidi alamazsa, bir `Illuminate\Contracts\Cache\LockTimeoutException` fırlatılacaktır.

Bu argümanlardan hiçbiri geçirilmezse, kilit maksimum 10 saniye boyunca alınacak ve istekler bir kilit almaya çalışırken maksimum 10 saniye bekleyecektir:

```php
Route::post('/profile', function () {
    // ...
})->block();
```

Custom Session Drivers Ekleme
**Driver’ı Uygulama**
Mevcut oturum sürücülerinden hiçbiri uygulamanızın ihtiyaçlarını karşılamıyorsa, Laravel kendi session handler’ınızı yazmanıza olanak tanır. Özel session driver’ınız PHP’nin yerleşik `SessionHandlerInterface` arayüzünü uygulamalıdır. Bu arayüz sadece birkaç basit metot içerir. MongoDB için bir örnek implementasyon aşağıdaki gibidir:

```php
<?php
 
namespace App\Extensions;
 
class MongoSessionHandler implements \SessionHandlerInterface
{
    public function open($savePath, $sessionName) {}
    public function close() {}
    public function read($sessionId) {}
    public function write($sessionId, $data) {}
    public function destroy($sessionId) {}
    public function gc($lifetime) {}
}
```

Laravel, uzantılarınızı yerleştirmek için varsayılan bir dizin içermez. Bu nedenle, bunları istediğiniz herhangi bir yere yerleştirmekte özgürsünüz. Bu örnekte, `MongoSessionHandler`’ı barındırmak için bir `Extensions` dizini oluşturduk.

Bu metodların amaçları hemen anlaşılır olmadığından, her birinin amacına genel bir bakış aşağıda verilmiştir:

* **open** metodu genellikle dosya tabanlı session sistemlerinde kullanılır. Laravel zaten bir file session driver’ı içerdiği için genellikle bu metoda herhangi bir şey eklemeniz gerekmez; boş bırakabilirsiniz.
* **close** metodu da genellikle göz ardı edilebilir. Çoğu sürücüde gerekli değildir.
* **read** metodu, verilen `$sessionId` ile ilişkili oturum verisinin string halini döndürmelidir. Veriyi alırken veya saklarken herhangi bir serileştirme veya kodlama yapmanıza gerek yoktur, Laravel bunu sizin için otomatik olarak yapar.
* **write** metodu, verilen `$data` string’ini `$sessionId` ile ilişkilendirerek MongoDB veya seçtiğiniz başka bir kalıcı depolama sistemine yazmalıdır. Yine, serileştirme işlemini sizin yapmanız gerekmez; Laravel bunu zaten halleder.
* **destroy** metodu, `$sessionId` ile ilişkili veriyi kalıcı depolamadan kaldırmalıdır.
* **gc** (garbage collection) metodu, verilen `$lifetime` (UNIX zaman damgası) değerinden daha eski tüm oturum verilerini silmelidir. Memcached ve Redis gibi kendi kendini temizleyen sistemlerde bu metod boş bırakılabilir.

**Driver’ı Kaydetme**
Driver’ınızı uyguladıktan sonra, Laravel’e kaydetmeye hazırsınız. Laravel’in session backend’ine ek sürücüler eklemek için `Session` facade’ı tarafından sağlanan `extend` metodunu kullanabilirsiniz. Bu metod, bir service provider’ın `boot` metodundan çağrılmalıdır. Bunu mevcut `App\Providers\AppServiceProvider` içinden yapabilir veya tamamen yeni bir provider oluşturabilirsiniz:

```php
<?php
 
namespace App\Providers;
 
use App\Extensions\MongoSessionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
 
class SessionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ...
    }
 
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Session::extend('mongo', function (Application $app) {
            // Return an implementation of SessionHandlerInterface...
            return new MongoSessionHandler;
        });
    }
}
```

Session driver kaydedildikten sonra, `SESSION_DRIVER` ortam değişkenini veya uygulamanın `config/session.php` yapılandırma dosyasını kullanarak uygulamanızın oturum sürücüsü olarak `mongo` driver’ını belirtebilirsiniz.


