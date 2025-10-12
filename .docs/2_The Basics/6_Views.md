Görünümler
Giriş
Elbette, tüm HTML belge dizelerini doğrudan rotalarınızdan ve denetleyicilerinizden döndürmek pratik değildir. Neyse ki, görünümler tüm HTML'mizi ayrı dosyalara yerleştirmemiz için kullanışlı bir yol sağlar.

Görünümler, denetleyici / uygulama mantığınızı sunum mantığınızdan ayırır ve `resources/views` dizininde saklanır. Laravel kullanırken, görünüm şablonları genellikle Blade şablon dili kullanılarak yazılır. Basit bir görünüm şöyle görünebilir:

```html
<!-- resources/views/greeting.blade.php içinde saklanan görünüm -->
<html>
    <body>
        <h1>Hello, {{ $name }}</h1>
    </body>
</html>
```

Bu görünüm `resources/views/greeting.blade.php` konumunda saklandığından, bunu global `view` helper'ı kullanarak şu şekilde döndürebiliriz:

```php
Route::get('/', function () {
    return view('greeting', ['name' => 'James']);
});
```

Blade şablonlarının nasıl yazılacağı hakkında daha fazla bilgi mi arıyorsunuz? Başlamak için Blade dokümantasyonuna göz atın.

### React / Vue ile Görünüm Yazma

Ön uç şablonlarını PHP üzerinden Blade kullanarak yazmak yerine, birçok geliştirici artık şablonlarını React veya Vue kullanarak yazmayı tercih ediyor. Laravel, bunu Inertia sayesinde zahmetsiz hale getirir. Inertia, React / Vue ön yüzünüzü, bir SPA oluşturmanın tipik karmaşıklıkları olmadan Laravel arka ucunuza bağlamayı kolaylaştıran bir kütüphanedir.

React ve Vue uygulama başlangıç kitlerimiz, Inertia ile güçlendirilmiş bir sonraki Laravel uygulamanız için harika bir başlangıç noktası sağlar.

### Görünüm Oluşturma ve Render Etme

Uygulamanızın `resources/views` dizininde `.blade.php` uzantısına sahip bir dosya yerleştirerek veya `make:view` Artisan komutunu kullanarak bir görünüm oluşturabilirsiniz:

```bash
php artisan make:view greeting
```

`.blade.php` uzantısı, dosyanın bir Blade şablonu içerdiğini framework'e bildirir. Blade şablonları HTML içerir ve ayrıca değerleri kolayca ekrana yazdırmanıza, “if” ifadeleri oluşturmanıza, veriler üzerinde yineleme yapmanıza ve daha fazlasını yapmanıza olanak tanıyan Blade direktifleri içerir.

Bir görünüm oluşturduktan sonra, uygulamanızın rotalarından veya denetleyicilerinden biri aracılığıyla global `view` helper'ını kullanarak bu görünümü döndürebilirsiniz:

```php
Route::get('/', function () {
    return view('greeting', ['name' => 'James']);
});
```

Görünümler ayrıca `View` facade’ı kullanılarak da döndürülebilir:

```php
use Illuminate\Support\Facades\View;
 
return View::make('greeting', ['name' => 'James']);
```

Gördüğünüz gibi, `view` helper’ına geçirilen ilk argüman `resources/views` dizinindeki görünüm dosyasının adına karşılık gelir. İkinci argüman ise görünüme aktarılması gereken verilerin bir dizisidir. Bu durumda, Blade sözdizimi kullanılarak görünümde görüntülenen `name` değişkenini aktarıyoruz.

### İç İçe Görünüm Dizinleri

Görünümler, `resources/views` dizininin alt dizinlerinde de iç içe yerleştirilebilir. İç içe görünümleri referanslamak için “nokta” (dot) gösterimi kullanılabilir. Örneğin, görünümünüz `resources/views/admin/profile.blade.php` konumunda saklanıyorsa, bunu rotalarınızdan / denetleyicilerinizden biri aracılığıyla şu şekilde döndürebilirsiniz:

```php
return view('admin.profile', $data);
```

Görünüm dizin adlarında `.` karakteri bulunmamalıdır.

### İlk Mevcut Görünümü Oluşturma

`View` facade’ının `first` yöntemini kullanarak, verilen bir dizi görünümden ilk mevcut olanını oluşturabilirsiniz. Bu, uygulamanızın veya paketinizin görünümlerin özelleştirilmesine veya üzerine yazılmasına izin vermesi durumunda yararlı olabilir:

```php
use Illuminate\Support\Facades\View;
 
return View::first(['custom.admin', 'admin'], $data);
```

### Bir Görünümün Var Olup Olmadığını Belirleme

Bir görünümün mevcut olup olmadığını belirlemeniz gerekiyorsa, `View` facade’ını kullanabilirsiniz. `exists` yöntemi, görünüm mevcutsa `true` döndürecektir:

```php
use Illuminate\Support\Facades\View;
 
if (View::exists('admin.profile')) {
    // ...
}
```
Görünümlere Veri Aktarma
Önceki örneklerde gördüğünüz gibi, görünümlere bir veri dizisi aktararak bu verileri görünümde kullanılabilir hale getirebilirsiniz:

```php
return view('greetings', ['name' => 'Victoria']);
```

Bu şekilde bilgi aktarırken, veriler anahtar / değer çiftlerinden oluşan bir dizi olmalıdır. Görünüme veri sağladıktan sonra, görünüm içinde her değere verinin anahtarı aracılığıyla erişebilirsiniz, örneğin `<?php echo $name; ?>` gibi.

Tam bir veri dizisini `view` helper fonksiyonuna geçirmek yerine, `with` metodunu kullanarak görünüme tek tek veri parçaları ekleyebilirsiniz. `with` metodu, görünüm nesnesinin bir örneğini döndürür, böylece görünümü döndürmeden önce metod zincirlemeye devam edebilirsiniz:

```php
return view('greeting')
    ->with('name', 'Victoria')
    ->with('occupation', 'Astronaut');
```

### Tüm Görünümlerle Veri Paylaşma

Bazen, uygulamanız tarafından render edilen tüm görünümlerle veri paylaşmanız gerekebilir. Bunu `View` facade’ının `share` metodunu kullanarak yapabilirsiniz. Genellikle, `share` metoduna yapılan çağrılar bir servis sağlayıcının `boot` metoduna yerleştirilmelidir. Bunları `App\Providers\AppServiceProvider` sınıfına ekleyebilir veya bunları barındırmak için ayrı bir servis sağlayıcı oluşturabilirsiniz:

```php
<?php
 
namespace App\Providers;
 
use Illuminate\Support\Facades\View;
 
class AppServiceProvider extends ServiceProvider
{
    /**
     * Uygulama servislerini kaydedin.
     */
    public function register(): void
    {
        // ...
    }
 
    /**
     * Uygulama servislerini başlatın.
     */
    public function boot(): void
    {
        View::share('key', 'value');
    }
}
```

### Görünüm Bestecileri

Görünüm bestecileri, bir görünüm render edildiğinde çağrılan geri çağırmalar veya sınıf metotlarıdır. Her render edildiğinde bir görünüme bağlanması gereken verileriniz varsa, bir görünüm bestecisi bu mantığı tek bir konumda düzenlemenize yardımcı olabilir. Görünüm bestecileri, uygulamanızda aynı görünümün birden fazla rota veya denetleyici tarafından döndürüldüğü ve her zaman belirli bir veriye ihtiyaç duyduğu durumlarda özellikle yararlı olabilir.

Genellikle, görünüm bestecileri uygulamanızın servis sağlayıcılarından birinde kaydedilir. Bu örnekte, bu mantığın `App\Providers\AppServiceProvider` içinde barındırılacağını varsayacağız.

`View` facade’ının `composer` metodunu kullanarak görünüm bestecisini kaydedeceğiz. Laravel, sınıf tabanlı görünüm bestecileri için varsayılan bir dizin içermez, bu nedenle bunları istediğiniz şekilde düzenleyebilirsiniz. Örneğin, tüm görünüm bestecilerinizi barındırmak için `app/View/Composers` dizinini oluşturabilirsiniz:

```php
<?php
 
namespace App\Providers;
 
use App\View\Composers\ProfileComposer;
use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
 
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ...
    }
 
    public function boot(): void
    {
        // Sınıf tabanlı besteciler kullanma...
        Facades\View::composer('profile', ProfileComposer::class);
 
        // Closure tabanlı besteciler kullanma...
        Facades\View::composer('welcome', function (View $view) {
            // ...
        });
 
        Facades\View::composer('dashboard', function (View $view) {
            // ...
        });
    }
}
```

Artık besteciyi kaydettiğimize göre, `App\View\Composers\ProfileComposer` sınıfının `compose` metodu, `profile` görünümü her render edildiğinde çalıştırılacaktır. Şimdi besteci sınıfının bir örneğine bakalım:

```php
<?php
 
namespace App\View\Composers;
 
use App\Repositories\UserRepository;
use Illuminate\View\View;
 
class ProfileComposer
{
    /**
     * Yeni bir profil bestecisi oluşturun.
     */
    public function __construct(
        protected UserRepository $users,
    ) {}
 
    /**
     * Verileri görünüme bağlayın.
     */
    public function compose(View $view): void
    {
        $view->with('count', $this->users->count());
    }
}
```

Gördüğünüz gibi, tüm görünüm bestecileri servis konteyneri aracılığıyla çözülür, bu nedenle bir bestecinin yapıcısında ihtiyaç duyduğunuz bağımlılıkları type-hint ile belirtebilirsiniz.

### Bir Besteciyi Birden Fazla Görünüme Bağlama

Bir görünüm bestecisini birden fazla görünüme aynı anda bağlamak için, `composer` metoduna ilk argüman olarak bir görünüm dizisi geçirebilirsiniz:

```php
use App\Views\Composers\MultiComposer;
use Illuminate\Support\Facades\View;
 
View::composer(
    ['profile', 'dashboard'],
    MultiComposer::class
);
```

`composer` metodu ayrıca tüm görünümlere bir besteci bağlamanıza olanak tanıyan `*` karakterini joker karakter olarak kabul eder:

```php
use Illuminate\Support\Facades;
use Illuminate\View\View;
 
Facades\View::composer('*', function (View $view) {
    // ...
});
```

### Görünüm Oluşturucuları

Görünüm “oluşturucuları”, görünüm bestecilerine oldukça benzer; ancak render edilmeden önce değil, görünüm oluşturulduktan hemen sonra çalıştırılırlar. Bir görünüm oluşturucu kaydetmek için `creator` metodunu kullanın:

```php
use App\View\Creators\ProfileCreator;
use Illuminate\Support\Facades\View;
 
View::creator('profile', ProfileCreator::class);
```

### Görünümleri Optimize Etme

Varsayılan olarak, Blade şablon görünümleri talep üzerine derlenir. Bir görünümü render eden bir istek yürütüldüğünde, Laravel derlenmiş bir sürümün mevcut olup olmadığını belirler. Dosya mevcutsa, Laravel derlenmemiş görünümün derlenmiş görünümden daha yakın bir zamanda değiştirilip değiştirilmediğini kontrol eder. Derlenmiş görünüm mevcut değilse veya derlenmemiş görünüm değiştirildiyse, Laravel görünümü yeniden derler.

Görünümleri istek sırasında derlemek performans üzerinde küçük bir olumsuz etkiye sahip olabilir, bu nedenle Laravel uygulamanız tarafından kullanılan tüm görünümleri önceden derlemek için `view:cache` Artisan komutunu sağlar. Performansı artırmak için bu komutu dağıtım sürecinizin bir parçası olarak çalıştırmak isteyebilirsiniz:

```bash
php artisan view:cache
```

Görünüm önbelleğini temizlemek için `view:clear` komutunu kullanabilirsiniz:

```bash
php artisan view:clear
```
