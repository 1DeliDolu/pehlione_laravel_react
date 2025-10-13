````markdown
<br>
## Contracts

<br>
### Introduction

Laravel’in **“contracts”** (sözleşmeler) kavramı, framework tarafından sağlanan temel servisleri tanımlayan bir dizi arayüzdür (interface).  
Örneğin, `Illuminate\Contracts\Queue\Queue` sözleşmesi, job’ların kuyruğa alınması için gereken metodları tanımlar.  
Benzer şekilde, `Illuminate\Contracts\Mail\Mailer` sözleşmesi, e-posta gönderimi için gereken metodları tanımlar.

Her sözleşmenin, framework tarafından sağlanan bir karşılık gelen implementasyonu vardır.  
Örneğin, Laravel çeşitli sürücüleri destekleyen bir **queue** implementasyonu ve Symfony Mailer tarafından desteklenen bir **mailer** implementasyonu sunar.

Tüm Laravel contracts’ları kendi GitHub deposunda bulunur.  
Bu, mevcut tüm sözleşmelere hızlı bir şekilde başvurmanızı sağlar ve Laravel servisleriyle etkileşen paketler geliştirirken kullanılabilecek bağımsız bir pakettir.

<br>
### Contracts vs. Facades

Laravel’in **facade**’leri ve yardımcı fonksiyonları (helper functions), servis container üzerinden contract çözümlemesi yapmadan Laravel servislerini kullanmanın basit bir yolunu sunar.  
Çoğu durumda, her facade’in bir contract karşılığı bulunur.

Facadeler, sınıfın constructor’ında bağımlılık tanımlamayı gerektirmezken, **contracts** size sınıflarınız için açık bağımlılıklar tanımlama imkânı verir.  
Bazı geliştiriciler bu şekilde açık bağımlılık tanımlamayı tercih ederken, diğerleri facadelerin sunduğu kolaylığı sever.  
Genel olarak, çoğu uygulama geliştirme sürecinde facadeleri sorunsuzca kullanabilir.

<br>
### When to Use Contracts

Contracts veya facadeleri kullanma kararı, sizin ve ekibinizin tercihlerine bağlıdır.  
Her iki yöntem de güçlü ve test edilebilir Laravel uygulamaları oluşturmak için kullanılabilir.  
Contracts ve facadeler birbirini dışlamaz — uygulamanızın bazı kısımları facadeleri, diğer kısımları contracts’ları kullanabilir.  
Sınıflarınızın sorumluluklarını net tuttuğunuz sürece, bu iki yaklaşım arasında pratik bir fark görmezsiniz.

Genel olarak, çoğu uygulama geliştirme sırasında facadeleri rahatlıkla kullanabilir.  
Ancak birden fazla PHP framework’üyle entegre olacak bir paket geliştiriyorsanız, `illuminate/contracts` paketini kullanarak Laravel servisleriyle etkileşimi tanımlayabilir, paketinizin `composer.json` dosyasına Laravel’in somut implementasyonlarını eklemeyebilirsiniz.

<br>
### How to Use Contracts

Peki bir contract’ın implementasyonunu nasıl elde edersiniz? Aslında oldukça basittir.

Laravel’de birçok sınıf türü (controller’lar, event listener’lar, middleware’ler, kuyruklanmış job’lar ve hatta route closure’ları) servis container üzerinden çözülür.  
Dolayısıyla, çözülmekte olan sınıfın constructor’ında interface’i **type-hint** olarak belirterek contract’ın implementasyonunu elde edebilirsiniz.

Örneğin, aşağıdaki event listener’a bakalım:

```php
<?php
 
namespace App\Listeners;
 
use App\Events\OrderWasPlaced;
use App\Models\User;
use Illuminate\Contracts\Redis\Factory;
 
class CacheOrderInformation
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected Factory $redis,
    ) {}
 
    /**
     * Handle the event.
     */
    public function handle(OrderWasPlaced $event): void
    {
        // ...
    }
}
````

Event listener çözülürken, servis container sınıfın constructor’ındaki type-hint’leri okur ve uygun değerleri otomatik olarak enjekte eder.
Servis container’a nasıl kayıt yapılacağı hakkında daha fazla bilgi için container dokümantasyonuna bakabilirsiniz.

<br>
### Contract Reference

Aşağıdaki tablo, tüm Laravel contracts’larına ve bunların karşılık gelen facadelerine hızlı bir başvuru sağlar:

| **Contract**                                          | **References Facade**   |
| ----------------------------------------------------- | ----------------------- |
| Illuminate\Contracts\Auth\Access\Authorizable         |                         |
| Illuminate\Contracts\Auth\Access\Gate                 | Gate                    |
| Illuminate\Contracts\Auth\Authenticatable             |                         |
| Illuminate\Contracts\Auth\CanResetPassword            |                         |
| Illuminate\Contracts\Auth\Factory                     | Auth                    |
| Illuminate\Contracts\Auth\Guard                       | Auth::guard()           |
| Illuminate\Contracts\Auth\PasswordBroker              | Password::broker()      |
| Illuminate\Contracts\Auth\PasswordBrokerFactory       | Password                |
| Illuminate\Contracts\Auth\StatefulGuard               |                         |
| Illuminate\Contracts\Auth\SupportsBasicAuth           |                         |
| Illuminate\Contracts\Auth\UserProvider                |                         |
| Illuminate\Contracts\Broadcasting\Broadcaster         | Broadcast::connection() |
| Illuminate\Contracts\Broadcasting\Factory             | Broadcast               |
| Illuminate\Contracts\Broadcasting\ShouldBroadcast     |                         |
| Illuminate\Contracts\Broadcasting\ShouldBroadcastNow  |                         |
| Illuminate\Contracts\Bus\Dispatcher                   | Bus                     |
| Illuminate\Contracts\Bus\QueueingDispatcher           | Bus::dispatchToQueue()  |
| Illuminate\Contracts\Cache\Factory                    | Cache                   |
| Illuminate\Contracts\Cache\Lock                       |                         |
| Illuminate\Contracts\Cache\LockProvider               |                         |
| Illuminate\Contracts\Cache\Repository                 | Cache::driver()         |
| Illuminate\Contracts\Cache\Store                      |                         |
| Illuminate\Contracts\Config\Repository                | Config                  |
| Illuminate\Contracts\Console\Application              |                         |
| Illuminate\Contracts\Console\Kernel                   | Artisan                 |
| Illuminate\Contracts\Container\Container              | App                     |
| Illuminate\Contracts\Cookie\Factory                   | Cookie                  |
| Illuminate\Contracts\Cookie\QueueingFactory           | Cookie::queue()         |
| Illuminate\Contracts\Database\ModelIdentifier         |                         |
| Illuminate\Contracts\Debug\ExceptionHandler           |                         |
| Illuminate\Contracts\Encryption\Encrypter             | Crypt                   |
| Illuminate\Contracts\Events\Dispatcher                | Event                   |
| Illuminate\Contracts\Filesystem\Cloud                 | Storage::cloud()        |
| Illuminate\Contracts\Filesystem\Factory               | Storage                 |
| Illuminate\Contracts\Filesystem\Filesystem            | Storage::disk()         |
| Illuminate\Contracts\Foundation\Application           | App                     |
| Illuminate\Contracts\Hashing\Hasher                   | Hash                    |
| Illuminate\Contracts\Http\Kernel                      |                         |
| Illuminate\Contracts\Mail\Mailable                    |                         |
| Illuminate\Contracts\Mail\Mailer                      | Mail                    |
| Illuminate\Contracts\Mail\MailQueue                   | Mail::queue()           |
| Illuminate\Contracts\Notifications\Dispatcher         | Notification            |
| Illuminate\Contracts\Notifications\Factory            | Notification            |
| Illuminate\Contracts\Pagination\LengthAwarePaginator  |                         |
| Illuminate\Contracts\Pagination\Paginator             |                         |
| Illuminate\Contracts\Pipeline\Hub                     |                         |
| Illuminate\Contracts\Pipeline\Pipeline                | Pipeline                |
| Illuminate\Contracts\Queue\EntityResolver             |                         |
| Illuminate\Contracts\Queue\Factory                    | Queue                   |
| Illuminate\Contracts\Queue\Job                        |                         |
| Illuminate\Contracts\Queue\Monitor                    | Queue                   |
| Illuminate\Contracts\Queue\Queue                      | Queue::connection()     |
| Illuminate\Contracts\Queue\QueueableCollection        |                         |
| Illuminate\Contracts\Queue\QueueableEntity            |                         |
| Illuminate\Contracts\Queue\ShouldQueue                |                         |
| Illuminate\Contracts\Redis\Factory                    | Redis                   |
| Illuminate\Contracts\Routing\BindingRegistrar         | Route                   |
| Illuminate\Contracts\Routing\Registrar                | Route                   |
| Illuminate\Contracts\Routing\ResponseFactory          | Response                |
| Illuminate\Contracts\Routing\UrlGenerator             | URL                     |
| Illuminate\Contracts\Routing\UrlRoutable              |                         |
| Illuminate\Contracts\Session\Session                  | Session::driver()       |
| Illuminate\Contracts\Support\Arrayable                |                         |
| Illuminate\Contracts\Support\Htmlable                 |                         |
| Illuminate\Contracts\Support\Jsonable                 |                         |
| Illuminate\Contracts\Support\MessageBag               |                         |
| Illuminate\Contracts\Support\MessageProvider          |                         |
| Illuminate\Contracts\Support\Renderable               |                         |
| Illuminate\Contracts\Support\Responsable              |                         |
| Illuminate\Contracts\Translation\Loader               |                         |
| Illuminate\Contracts\Translation\Translator           | Lang                    |
| Illuminate\Contracts\Validation\Factory               | Validator               |
| Illuminate\Contracts\Validation\ValidatesWhenResolved |                         |
| Illuminate\Contracts\Validation\ValidationRule        |                         |
| Illuminate\Contracts\Validation\Validator             | Validator::make()       |
| Illuminate\Contracts\View\Engine                      |                         |
| Illuminate\Contracts\View\Factory                     | View                    |
| Illuminate\Contracts\View\View                        | View::make()            |

