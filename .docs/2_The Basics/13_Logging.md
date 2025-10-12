## Günlük Kaydı (Logging)

### Giriş

Uygulamanızda neler olup bittiğini daha iyi anlamanıza yardımcı olmak için Laravel, mesajları dosyalara, sistem hata günlüğüne ve hatta tüm ekibinizi bilgilendirmek için Slack’e kaydetmenize olanak tanıyan güçlü günlük kayıt hizmetleri sağlar.

Laravel günlük kaydı “kanallar”a dayanır. Her kanal, günlük bilgisini yazmanın belirli bir yolunu temsil eder. Örneğin, **single** kanalı günlük dosyalarını tek bir dosyaya yazar, **slack** kanalı ise günlük mesajlarını Slack’e gönderir. Günlük mesajları, önem derecelerine göre birden fazla kanala yazılabilir.

Arka planda Laravel, çeşitli güçlü günlük işleyicilerini destekleyen **Monolog** kütüphanesini kullanır. Laravel, bu işleyicilerin yapılandırılmasını kolaylaştırır ve uygulamanızın günlük işleme biçimini özelleştirmenize olanak tanır.

---

### Yapılandırma

Uygulamanızın günlük davranışını kontrol eden tüm yapılandırma seçenekleri `config/logging.php` dosyasında yer alır. Bu dosya, uygulamanızın günlük kanallarını yapılandırmanıza olanak tanır. Mevcut kanalları ve seçeneklerini mutlaka inceleyin. Aşağıda yaygın seçeneklerden bazılarını göreceksiniz.

Varsayılan olarak Laravel, günlük mesajlarını kaydederken **stack** kanalını kullanır. **Stack** kanalı, birden fazla günlük kanalını tek bir kanal altında toplamak için kullanılır. Stack oluşturma hakkında daha fazla bilgi için aşağıdaki belgeleri inceleyin.

---

### Mevcut Kanal Sürücüleri

Her günlük kanalı bir “driver” tarafından desteklenir. Driver, günlük mesajının nasıl ve nereye kaydedileceğini belirler. Aşağıdaki günlük kanal sürücüleri her Laravel uygulamasında mevcuttur. Çoğu, `config/logging.php` dosyanızda zaten tanımlıdır, bu yüzden dosyanın içeriğine aşina olun:

| Ad             | Açıklama                                                                                 |
| -------------- | ---------------------------------------------------------------------------------------- |
| **custom**     | Belirli bir fabrika çağırarak bir kanal oluşturan sürücü.                                |
| **daily**      | Günlük olarak dönen bir `RotatingFileHandler` tabanlı Monolog sürücüsü.                  |
| **errorlog**   | `ErrorLogHandler` tabanlı Monolog sürücüsü.                                              |
| **monolog**    | Herhangi bir desteklenen Monolog işleyicisini kullanabilen bir Monolog fabrika sürücüsü. |
| **papertrail** | `SyslogUdpHandler` tabanlı Monolog sürücüsü.                                             |
| **single**     | Tek bir dosya veya yol tabanlı günlük kanalı (`StreamHandler`).                          |
| **slack**      | `SlackWebhookHandler` tabanlı Monolog sürücüsü.                                          |
| **stack**      | “Çoklu kanal” oluşturmayı kolaylaştıran bir sarmalayıcı.                                 |
| **syslog**     | `SyslogHandler` tabanlı Monolog sürücüsü.                                                |

**monolog** ve **custom** sürücüleri hakkında daha fazla bilgi için gelişmiş kanal özelleştirme belgelerine göz atın.

---

### Kanal Adını Yapılandırma

Varsayılan olarak Monolog, mevcut ortamla (örneğin `production` veya `local`) eşleşen bir “kanal adı” ile başlatılır. Bu değeri değiştirmek için kanal yapılandırmanıza bir `name` seçeneği ekleyebilirsiniz:

```php
'stack' => [
    'driver' => 'stack',
    'name' => 'channel-name',
    'channels' => ['single', 'slack'],
],
```

---

### Kanal Ön Gereksinimleri

#### Single ve Daily Kanallarını Yapılandırma

**single** ve **daily** kanallarının üç isteğe bağlı yapılandırma seçeneği vardır: `bubble`, `permission` ve `locking`.

| Ad             | Açıklama                                                                            | Varsayılan |
| -------------- | ----------------------------------------------------------------------------------- | ---------- |
| **bubble**     | Mesajların işlendiikten sonra diğer kanallara aktarılıp aktarılmayacağını belirtir. | `true`     |
| **locking**    | Yazmadan önce günlük dosyasını kilitlemeyi dener.                                   | `false`    |
| **permission** | Günlük dosyasının izinleri.                                                         | `0644`     |

Ayrıca, **daily** kanalının saklama politikası `LOG_DAILY_DAYS` ortam değişkeni veya `days` yapılandırma seçeneği ile ayarlanabilir.

| Ad       | Açıklama                                            | Varsayılan |
| -------- | --------------------------------------------------- | ---------- |
| **days** | Günlük dosyalarının kaç gün saklanacağını belirtir. | `14`       |

---

### Papertrail Kanalını Yapılandırma

**papertrail** kanalı `host` ve `port` yapılandırma seçeneklerini gerektirir. Bu değerler `PAPERTRAIL_URL` ve `PAPERTRAIL_PORT` ortam değişkenleriyle tanımlanabilir. Bu bilgileri Papertrail’den edinebilirsiniz.

---

### Slack Kanalını Yapılandırma

**slack** kanalı bir `url` yapılandırma seçeneği gerektirir. Bu değer `LOG_SLACK_WEBHOOK_URL` ortam değişkeni aracılığıyla tanımlanabilir. Bu URL, Slack ekibiniz için yapılandırılmış bir “incoming webhook” URL’si ile eşleşmelidir.

Varsayılan olarak Slack yalnızca **critical** düzeyinde ve üzerindeki günlükleri alır; ancak bu ayarı `LOG_LEVEL` ortam değişkeni veya Slack kanalınızın yapılandırma dizisindeki `level` seçeneğini değiştirerek düzenleyebilirsiniz.

---

### Kullanımdan Kaldırma Uyarılarını (Deprecation Warnings) Günlüğe Kaydetme

PHP, Laravel ve diğer kütüphaneler genellikle bazı özelliklerinin kullanımdan kaldırıldığını ve gelecekteki bir sürümde kaldırılacağını kullanıcılarına bildirir. Bu uyarıları günlüğe kaydetmek istiyorsanız, tercih ettiğiniz “deprecation” günlük kanalını `LOG_DEPRECATIONS_CHANNEL` ortam değişkeni veya `config/logging.php` dosyanız aracılığıyla belirtebilirsiniz:

```php
'deprecations' => [
    'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
    'trace' => env('LOG_DEPRECATIONS_TRACE', false),
],
 
'channels' => [
    // ...
]
```

Veya `deprecations` adında bir günlük kanalı tanımlayabilirsiniz. Eğer bu isimde bir günlük kanalı varsa, Laravel her zaman bu kanalı kullanarak kullanımdan kaldırma uyarılarını günlüğe kaydeder:

```php
'channels' => [
    'deprecations' => [
        'driver' => 'single',
        'path' => storage_path('logs/php-deprecation-warnings.log'),
    ],
],
```
## Günlük Yığınları Oluşturma (Building Log Stacks)

Daha önce belirtildiği gibi, **stack** sürücüsü birden fazla kanalı tek bir günlük kanalı altında birleştirmenize olanak tanır. Günlük yığınlarını nasıl kullanacağınızı göstermek için, üretim ortamında görebileceğiniz bir örnek yapılandırmaya bakalım:

```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['syslog', 'slack'], 
        'ignore_exceptions' => false,
    ],
 
    'syslog' => [
        'driver' => 'syslog',
        'level' => env('LOG_LEVEL', 'debug'),
        'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
        'replace_placeholders' => true,
    ],
 
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
        'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
        'level' => env('LOG_LEVEL', 'critical'),
        'replace_placeholders' => true,
    ],
],
```

Bu yapılandırmayı inceleyelim. Öncelikle, **stack** kanalının `channels` seçeneği aracılığıyla iki başka kanalı (syslog ve slack) birleştirdiğine dikkat edin. Bu nedenle, günlük mesajları yazıldığında her iki kanal da bu mesajı kaydetme fırsatına sahip olur. Ancak aşağıda göreceğimiz gibi, kanalların mesajı gerçekten kaydedip kaydetmemesi, mesajın önem derecesine (**level**) bağlı olabilir.

---

### Günlük Düzeyleri (Log Levels)

Yukarıdaki örnekte **syslog** ve **slack** kanal yapılandırmalarında bulunan `level` seçeneğine dikkat edin. Bu seçenek, bir mesajın kanal tarafından kaydedilebilmesi için sahip olması gereken minimum “seviye”yi belirler. Laravel’in günlük sisteminin temelini oluşturan **Monolog**, RFC 5424 standardında tanımlanan tüm günlük seviyelerini destekler. Bu seviyeler, önem derecesine göre azalan sırayla şunlardır:

**emergency**, **alert**, **critical**, **error**, **warning**, **notice**, **info** ve **debug**.

Örneğin, şu şekilde bir mesaj kaydedelim:

```php
Log::debug('An informational message.');
```

Yapılandırmamıza göre, **syslog** kanalı bu mesajı sistem günlüğüne yazacaktır; ancak mesaj **critical** seviyesinde olmadığı için **Slack** kanalına gönderilmeyecektir. Fakat, şu şekilde bir **emergency** mesajı kaydedersek:

```php
Log::emergency('The system is down!');
```

Bu durumda mesaj hem sistem günlüğüne hem de **Slack**’e gönderilecektir, çünkü **emergency** seviyesi her iki kanalın minimum seviye eşiğinin üzerindedir.

---

### Günlük Mesajları Yazma

**Log** facade’ını kullanarak günlük dosyalarına bilgi yazabilirsiniz. Daha önce belirtildiği gibi, logger, RFC 5424 standardında tanımlanan sekiz günlük seviyesini destekler:

```php
use Illuminate\Support\Facades\Log;
 
Log::emergency($message);
Log::alert($message);
Log::critical($message);
Log::error($message);
Log::warning($message);
Log::notice($message);
Log::info($message);
Log::debug($message);
```

Bu yöntemlerin herhangi birini, karşılık gelen seviyede bir mesaj kaydetmek için çağırabilirsiniz. Varsayılan olarak, mesaj günlük yapılandırma dosyanızda belirtilen **varsayılan kanal**a yazılır:

```php
<?php
 
namespace App\Http\Controllers;
 
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
 
class UserController extends Controller
{
    /**
     * Verilen kullanıcının profilini göster.
     */
    public function show(string $id): View
    {
        Log::info('Showing the user profile for user: {id}', ['id' => $id]);
 
        return view('user.profile', [
            'user' => User::findOrFail($id)
        ]);
    }
}
```

---

### Bağlamsal Bilgi (Contextual Information)

Günlük yöntemlerine bir dizi bağlamsal veri (contextual data) iletebilirsiniz. Bu veriler, günlük mesajıyla birlikte biçimlendirilip görüntülenecektir:

```php
use Illuminate\Support\Facades\Log;
 
Log::info('User {id} failed to login.', ['id' => $user->id]);
```

Bazen belirli bir kanalda yapılan tüm sonraki günlük girdilerine dahil edilmesi gereken bazı bağlamsal bilgileri belirtmek isteyebilirsiniz. Örneğin, her gelen istekle ilişkilendirilmiş bir **request ID** kaydetmek isteyebilirsiniz. Bunu yapmak için **Log** facade’ının `withContext` metodunu çağırabilirsiniz:

```php
<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
 
class AssignRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) Str::uuid();
 
        Log::withContext([
            'request-id' => $requestId
        ]);
 
        $response = $next($request);
        $response->headers->set('Request-Id', $requestId);
 
        return $response;
    }
}
```

Tüm günlük kanalları arasında bağlamsal bilgi paylaşmak isterseniz, `Log::shareContext()` metodunu çağırabilirsiniz. Bu yöntem, hem mevcut hem de daha sonra oluşturulacak tüm kanallara bağlamsal bilgileri sağlar:

```php
<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
 
class AssignRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) Str::uuid();
 
        Log::shareContext([
            'request-id' => $requestId
        ]);
 
        // ...
    }
}
```

Eğer **queued job**’ları işlerken günlük bağlamı paylaşmanız gerekiyorsa, **job middleware** kullanabilirsiniz.

---

### Belirli Kanallara Yazma

Bazen mesajı uygulamanızın varsayılan kanalından farklı bir kanala kaydetmek isteyebilirsiniz. Bunu yapmak için **Log** facade’ının `channel` metodunu kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Log;
 
Log::channel('slack')->info('Something happened!');
```

Birden fazla kanaldan oluşan isteğe bağlı (on-demand) bir günlük yığını oluşturmak isterseniz, `stack` metodunu kullanabilirsiniz:

```php
Log::stack(['single', 'slack'])->info('Something happened!');
```

---

### İsteğe Bağlı Kanallar (On-Demand Channels)

Ayrıca, uygulamanızın yapılandırma dosyasında tanımlı olmadan, çalışma zamanında bir yapılandırma dizisi sağlayarak isteğe bağlı bir kanal oluşturabilirsiniz. Bunu yapmak için **Log** facade’ının `build` metoduna bir yapılandırma dizisi aktarabilirsiniz:

```php
use Illuminate\Support\Facades\Log;
 
Log::build([
  'driver' => 'single',
  'path' => storage_path('logs/custom.log'),
])->info('Something happened!');
```

Bir isteğe bağlı kanalın isteğe bağlı bir günlük yığınına dahil edilmesini de isteyebilirsiniz. Bunu yapmak için oluşturduğunuz kanal örneğini `stack` metoduna iletilen diziye ekleyebilirsiniz:

```php
use Illuminate\Support\Facades\Log;
 
$channel = Log::build([
  'driver' => 'single',
  'path' => storage_path('logs/custom.log'),
]);
 
Log::stack(['slack', $channel])->info('Something happened!');
```
## Monolog Kanal Özelleştirmesi (Monolog Channel Customization)

### Kanallar için Monolog'u Özelleştirme

Bazen mevcut bir kanal için Monolog’un nasıl yapılandırıldığını tamamen kontrol etmeniz gerekebilir. Örneğin, Laravel’in yerleşik **single** kanalı için özel bir `Monolog\Formatter\FormatterInterface` uygulaması yapılandırmak isteyebilirsiniz.

Başlamak için, kanalın yapılandırmasında bir `tap` dizisi tanımlayın. Bu `tap` dizisi, Monolog örneği oluşturulduktan sonra onu özelleştirme (ya da “dokunma”) fırsatına sahip olacak sınıfların listesini içermelidir. Bu sınıflar için özel bir konum zorunluluğu yoktur, bu yüzden uygulamanızda bu sınıfları barındırmak için bir dizin oluşturabilirsiniz:

```php
'single' => [
    'driver' => 'single',
    'tap' => [App\Logging\CustomizeFormatter::class],
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'replace_placeholders' => true,
],
```

Kanalınızda `tap` seçeneğini yapılandırdıktan sonra, Monolog örneğini özelleştirecek sınıfı tanımlamaya hazırsınız. Bu sınıf yalnızca tek bir metoda ihtiyaç duyar: **__invoke**, bu metod bir `Illuminate\Log\Logger` örneğini alır. `Illuminate\Log\Logger` sınıfı, tüm metot çağrılarını temel Monolog örneğine iletir:

```php
<?php
 
namespace App\Logging;
 
use Illuminate\Log\Logger;
use Monolog\Formatter\LineFormatter;
 
class CustomizeFormatter
{
    /**
     * Verilen logger örneğini özelleştir.
     */
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new LineFormatter(
                '[%datetime%] %channel%.%level_name%: %message% %context% %extra%'
            ));
        }
    }
}
```

Tüm `tap` sınıflarınız servis container tarafından çözülür, bu nedenle ihtiyaç duydukları tüm bağımlılıklar otomatik olarak enjekte edilir.

---

### Monolog Handler Kanalları Oluşturma

Monolog’un birçok kullanılabilir **handler**’ı vardır ve Laravel her biri için yerleşik bir kanal içermez. Bazı durumlarda, Laravel’in günlük sürücülerinden biriyle eşleşmeyen belirli bir Monolog handler’ının örneği olan özel bir kanal oluşturmak isteyebilirsiniz. Bu kanallar **monolog** sürücüsü kullanılarak kolayca oluşturulabilir.

**monolog** sürücüsünü kullanırken, `handler` yapılandırma seçeneği hangi handler’ın başlatılacağını belirtmek için kullanılır. İsteğe bağlı olarak, handler’ın ihtiyaç duyduğu kurucu parametreleri `handler_with` seçeneğiyle belirtebilirsiniz:

```php
'logentries' => [
    'driver'  => 'monolog',
    'handler' => Monolog\Handler\SyslogUdpHandler::class,
    'handler_with' => [
        'host' => 'my.logentries.internal.datahubhost.company.com',
        'port' => '10000',
    ],
],
```

---

### Monolog Formatlayıcıları (Formatters)

**monolog** sürücüsünü kullanırken, varsayılan olarak `Monolog\Formatter\LineFormatter` kullanılır. Ancak, handler’a iletilen formatlayıcı türünü `formatter` ve `formatter_with` yapılandırma seçeneklerini kullanarak özelleştirebilirsiniz:

```php
'browser' => [
    'driver' => 'monolog',
    'handler' => Monolog\Handler\BrowserConsoleHandler::class,
    'formatter' => Monolog\Formatter\HtmlFormatter::class,
    'formatter_with' => [
        'dateFormat' => 'Y-m-d',
    ],
],
```

Eğer kullandığınız Monolog handler kendi formatlayıcısını sağlayabiliyorsa, `formatter` seçeneğinin değerini **default** olarak ayarlayabilirsiniz:

```php
'newrelic' => [
    'driver' => 'monolog',
    'handler' => Monolog\Handler\NewRelicHandler::class,
    'formatter' => 'default',
],
```

---

### Monolog İşleyicileri (Processors)

Monolog, mesajları kaydedilmeden önce işleyebilir. Kendi **processor**’larınızı oluşturabilir veya Monolog’un sunduğu mevcut processor’ları kullanabilirsiniz.

Bir **monolog** sürücüsü için processor’ları özelleştirmek istiyorsanız, kanalın yapılandırmasına bir `processors` değeri ekleyin:

```php
'memory' => [
    'driver' => 'monolog',
    'handler' => Monolog\Handler\StreamHandler::class,
    'handler_with' => [
        'stream' => 'php://stderr',
    ],
    'processors' => [
        // Basit kullanım...
        Monolog\Processor\MemoryUsageProcessor::class,
 
        // Seçeneklerle birlikte...
        [
            'processor' => Monolog\Processor\PsrLogMessageProcessor::class,
            'with' => ['removeUsedContextFields' => true],
        ],
    ],
],
```

---

### Fabrikalar Aracılığıyla Özel Kanallar Oluşturma

Monolog’un başlatılması ve yapılandırılması üzerinde tam kontrol sahibi olacağınız tamamen özel bir kanal tanımlamak isterseniz, `config/logging.php` dosyanızda özel bir **driver** türü belirtebilirsiniz. Yapılandırmanız, Monolog örneğini oluşturmak için çağrılacak fabrika sınıfının adını içeren bir `via` seçeneği içermelidir:

```php
'channels' => [
    'example-custom-channel' => [
        'driver' => 'custom',
        'via' => App\Logging\CreateCustomLogger::class,
    ],
],
```

Özel sürücü kanalınızı yapılandırdıktan sonra, Monolog örneğini oluşturacak sınıfı tanımlamaya hazırsınız. Bu sınıf yalnızca bir **__invoke** metoduna ihtiyaç duyar ve bu metod Monolog logger örneğini döndürmelidir. Metod, kanalın yapılandırma dizisini tek parametre olarak alır:

```php
<?php
 
namespace App\Logging;
 
use Monolog\Logger;
 
class CreateCustomLogger
{
    /**
     * Özel bir Monolog örneği oluştur.
     */
    public function __invoke(array $config): Logger
    {
        return new Logger(/* ... */);
    }
}
```
## Pail Kullanarak Günlük Mesajlarını İzleme (Tailing Log Messages Using Pail)

Uygulamanızın günlüklerini gerçek zamanlı olarak izlemeniz gerekebilir. Örneğin, bir hatayı ayıklarken veya uygulamanızın günlüklerini belirli hata türleri için izlerken bu faydalı olur.

**Laravel Pail**, Laravel uygulamanızın günlük dosyalarını doğrudan komut satırından kolayca incelemenizi sağlayan bir pakettir. Standart `tail` komutundan farklı olarak, **Pail** tüm günlük sürücüleriyle (örneğin **Sentry** veya **Flare**) çalışacak şekilde tasarlanmıştır. Buna ek olarak, Pail aradığınız bilgiyi hızlıca bulmanıza yardımcı olan bir dizi kullanışlı filtre sağlar.

---

### Kurulum (Installation)

Laravel Pail, **PCNTL PHP eklentisini** gerektirir.

Başlamak için Pail’i projenize Composer aracılığıyla yükleyin:

```bash
composer require --dev laravel/pail
```

---

### Kullanım (Usage)

Günlükleri izlemeye başlamak için aşağıdaki komutu çalıştırın:

```bash
php artisan pail
```

Çıktının ayrıntı düzeyini artırmak ve kesilmeleri (… şeklindeki kısaltmaları) önlemek için `-v` seçeneğini kullanabilirsiniz:

```bash
php artisan pail -v
```

Maksimum ayrıntı için ve istisna (exception) yığın izlerini görüntülemek için `-vv` seçeneğini kullanın:

```bash
php artisan pail -vv
```

Günlük izlemeyi durdurmak için istediğiniz zaman **Ctrl + C** tuşlarına basabilirsiniz.

---

### Günlükleri Filtreleme (Filtering Logs)

#### `--filter`

Günlükleri türüne, dosyasına, mesajına veya yığın izi (stack trace) içeriğine göre filtrelemek için `--filter` seçeneğini kullanabilirsiniz:

```bash
php artisan pail --filter="QueryException"
```

#### `--message`

Sadece mesaj içeriğine göre günlükleri filtrelemek için `--message` seçeneğini kullanın:

```bash
php artisan pail --message="User created"
```

#### `--level`

Günlük seviyesine göre filtreleme yapmak için `--level` seçeneğini kullanabilirsiniz:

```bash
php artisan pail --level=error
```

#### `--user`

Belirli bir kullanıcı kimliğiyle (ID) kimlik doğrulaması yapılmışken yazılmış günlükleri görüntülemek için `--user` seçeneğine kullanıcı ID’sini belirtin:

```bash
php artisan pail --user=1
```
