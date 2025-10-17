# Task Scheduling
<br>


## Introduction

Geçmişte, sunucunuzda zamanlanmış her görev için ayrı bir cron yapılandırma girişi yazmış olabilirsiniz. Ancak, bu yaklaşım kısa sürede zahmetli hale gelebilir çünkü görev zamanlamanız artık kaynak kontrolünde değildir ve mevcut cron girdilerini görüntülemek veya yeni girdiler eklemek için sunucunuza SSH ile bağlanmanız gerekir.

Laravel’in komut zamanlayıcısı (command scheduler), sunucunuzdaki zamanlanmış görevleri yönetmek için yeni bir yaklaşım sunar. Scheduler, komut zamanlamanızı doğrudan Laravel uygulamanız içinde akıcı (fluent) ve ifade gücü yüksek (expressive) bir biçimde tanımlamanıza olanak tanır. Scheduler kullanılırken, sunucunuzda yalnızca tek bir cron girdisine ihtiyaç duyulur. Görev zamanlamanız genellikle uygulamanızın `routes/console.php` dosyasında tanımlanır.

<br>


## Defining Schedules

Tüm zamanlanmış görevlerinizi uygulamanızın `routes/console.php` dosyasında tanımlayabilirsiniz. Başlamak için bir örneğe bakalım. Bu örnekte, her gün gece yarısı çağrılacak bir closure zamanlayacağız. Closure içinde bir tabloyu temizlemek için bir veritabanı sorgusu çalıştıracağız:

```php
<?php
 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
 
Schedule::call(function () {
    DB::table('recent_users')->delete();
})->daily();
````

Closure’lar kullanarak zamanlama yapmanın yanı sıra, çağrılabilir (invokable) nesneleri de zamanlayabilirsiniz. Çağrılabilir nesneler, bir `__invoke` metoduna sahip basit PHP sınıflarıdır:

```php
Schedule::call(new DeleteRecentUsers)->daily();
```

Eğer `routes/console.php` dosyanızı yalnızca komut tanımlamaları için ayırmak istiyorsanız, zamanlanmış görevlerinizi tanımlamak için uygulamanızın `bootstrap/app.php` dosyasındaki `withSchedule` metodunu kullanabilirsiniz. Bu metot, scheduler örneğini alan bir closure kabul eder:

```php
use Illuminate\Console\Scheduling\Schedule;
 
->withSchedule(function (Schedule $schedule) {
    $schedule->call(new DeleteRecentUsers)->daily();
})
```

Zamanlanmış görevlerinizin genel bir özetini ve bir sonraki çalıştırılma zamanlarını görmek isterseniz, `schedule:list` Artisan komutunu kullanabilirsiniz:

```bash
php artisan schedule:list
```

<br>


## Scheduling Artisan Commands

Closure’ların yanı sıra, Artisan komutlarını ve sistem komutlarını da zamanlayabilirsiniz. Örneğin, bir Artisan komutunu komutun adı veya sınıfı aracılığıyla zamanlamak için `command` metodunu kullanabilirsiniz.

Komut sınıf adını kullanarak Artisan komutlarını zamanlarken, komut çağrıldığında sağlanması gereken ek komut satırı argümanlarını bir dizi olarak geçebilirsiniz:

```php
use App\Console\Commands\SendEmailsCommand;
use Illuminate\Support\Facades\Schedule;
 
Schedule::command('emails:send Taylor --force')->daily();
 
Schedule::command(SendEmailsCommand::class, ['Taylor', '--force'])->daily();
```

<br>


## Scheduling Artisan Closure Commands

Eğer bir closure tarafından tanımlanmış Artisan komutunu zamanlamak istiyorsanız, komut tanımından sonra zamanlama ile ilgili metodları zincirleyebilirsiniz:

```php
Artisan::command('delete:recent-users', function () {
    DB::table('recent_users')->delete();
})->purpose('Delete recent users')->daily();
```

Closure komutuna argümanlar iletmeniz gerekiyorsa, bunları `schedule` metoduna sağlayabilirsiniz:

```php
Artisan::command('emails:send {user} {--force}', function ($user) {
    // ...
})->purpose('Send emails to the specified user')->schedule(['Taylor', '--force'])->daily();
```

<br>


## Scheduling Queued Jobs

`job` metodu, bir kuyruk (queue) işini zamanlamak için kullanılabilir. Bu metot, işi kuyruğa almak için closure tanımlamak zorunda kalmadan kuyruklanmış işleri zamanlamanın pratik bir yolunu sunar:

```php
use App\Jobs\Heartbeat;
use Illuminate\Support\Facades\Schedule;
 
Schedule::job(new Heartbeat)->everyFiveMinutes();
```

`job` metoduna ikinci ve üçüncü isteğe bağlı argümanlar verilerek, kuyruğa alınacak iş için kullanılacak kuyruk adı ve bağlantısı belirtilebilir:

```php
use App\Jobs\Heartbeat;
use Illuminate\Support\Facades\Schedule;
 
// "heartbeats" kuyruğunda, "sqs" bağlantısı üzerinden çalıştır...
Schedule::job(new Heartbeat, 'heartbeats', 'sqs')->everyFiveMinutes();
```

<br>


## Scheduling Shell Commands

`exec` metodu, işletim sistemine bir komut göndermek için kullanılabilir:

```php
use Illuminate\Support\Facades\Schedule;
 
Schedule::exec('node /home/forge/script.js')->daily();
```

<br>


## Schedule Frequency Options

Bir görevin belirli aralıklarla çalışmasını nasıl yapılandırabileceğimizi birkaç örnekte gördük. Ancak, bir göreve atanabilecek çok daha fazla zamanlama sıklığı vardır:

| Metot                            | Açıklama                                                |
| -------------------------------- | ------------------------------------------------------- |
| ->cron('* * * * *');             | Görevi özel bir cron zamanlamasında çalıştır.           |
| ->everySecond();                 | Görevi her saniye çalıştır.                             |
| ->everyTwoSeconds();             | Görevi her iki saniyede bir çalıştır.                   |
| ->everyFiveSeconds();            | Görevi her beş saniyede bir çalıştır.                   |
| ->everyTenSeconds();             | Görevi her on saniyede bir çalıştır.                    |
| ->everyFifteenSeconds();         | Görevi her on beş saniyede bir çalıştır.                |
| ->everyTwentySeconds();          | Görevi her yirmi saniyede bir çalıştır.                 |
| ->everyThirtySeconds();          | Görevi her otuz saniyede bir çalıştır.                  |
| ->everyMinute();                 | Görevi her dakika çalıştır.                             |
| ->everyTwoMinutes();             | Görevi her iki dakikada bir çalıştır.                   |
| ->everyThreeMinutes();           | Görevi her üç dakikada bir çalıştır.                    |
| ->everyFourMinutes();            | Görevi her dört dakikada bir çalıştır.                  |
| ->everyFiveMinutes();            | Görevi her beş dakikada bir çalıştır.                   |
| ->everyTenMinutes();             | Görevi her on dakikada bir çalıştır.                    |
| ->everyFifteenMinutes();         | Görevi her on beş dakikada bir çalıştır.                |
| ->everyThirtyMinutes();          | Görevi her otuz dakikada bir çalıştır.                  |
| ->hourly();                      | Görevi her saat çalıştır.                               |
| ->hourlyAt(17);                  | Görevi her saatin 17. dakikasında çalıştır.             |
| ->everyOddHour($minutes = 0);    | Görevi her tek saatte çalıştır.                         |
| ->everyTwoHours($minutes = 0);   | Görevi her iki saatte bir çalıştır.                     |
| ->everyThreeHours($minutes = 0); | Görevi her üç saatte bir çalıştır.                      |
| ->everyFourHours($minutes = 0);  | Görevi her dört saatte bir çalıştır.                    |
| ->everySixHours($minutes = 0);   | Görevi her altı saatte bir çalıştır.                    |
| ->daily();                       | Görevi her gün gece yarısı çalıştır.                    |
| ->dailyAt('13:00');              | Görevi her gün saat 13:00’te çalıştır.                  |
| ->twiceDaily(1, 13);             | Görevi her gün saat 1:00 ve 13:00’te çalıştır.          |
| ->twiceDailyAt(1, 13, 15);       | Görevi her gün saat 1:15 ve 13:15’te çalıştır.          |
| ->weekly();                      | Görevi her pazar 00:00’da çalıştır.                     |
| ->weeklyOn(1, '8:00');           | Görevi her pazartesi saat 8:00’de çalıştır.             |
| ->monthly();                     | Görevi her ayın ilk gününde 00:00’da çalıştır.          |
| ->monthlyOn(4, '15:00');         | Görevi her ayın 4’ünde saat 15:00’te çalıştır.          |
| ->twiceMonthly(1, 16, '13:00');  | Görevi her ayın 1’i ve 16’sında saat 13:00’te çalıştır. |
| ->lastDayOfMonth('15:00');       | Görevi ayın son gününde saat 15:00’te çalıştır.         |
| ->quarterly();                   | Görevi her çeyreğin ilk gününde 00:00’da çalıştır.      |
| ->quarterlyOn(4, '14:00');       | Görevi her çeyreğin 4’ünde saat 14:00’te çalıştır.      |
| ->yearly();                      | Görevi her yılın ilk gününde 00:00’da çalıştır.         |
| ->yearlyOn(6, 1, '17:00');       | Görevi her yıl 1 Haziran’da saat 17:00’de çalıştır.     |
| ->timezone('America/New_York');  | Görev için saat dilimini ayarla.                        |

Bu metodlar, belirli haftanın günlerinde çalışacak şekilde ek kısıtlamalarla birleştirilebilir. Örneğin, bir komutu her pazartesi çalışacak şekilde haftalık olarak zamanlayabilirsiniz:

```php
use Illuminate\Support\Facades\Schedule;
 
// Haftada bir, Pazartesi günü saat 13:00'te çalıştır...
Schedule::call(function () {
    // ...
})->weekly()->mondays()->at('13:00');
 
// Haftaiçi günlerinde 08:00 - 17:00 arasında her saat çalıştır...
Schedule::command('foo')
    ->weekdays()
    ->hourly()
    ->timezone('America/Chicago')
    ->between('8:00', '17:00');
```

Aşağıda ek zamanlama kısıtlamalarının bir listesi verilmiştir:

| Metot                                  | Açıklama                                                               |                                  |
| -------------------------------------- | ---------------------------------------------------------------------- | -------------------------------- |
| ->weekdays();                          | Görevi haftaiçi günleriyle sınırla.                                    |                                  |
| ->weekends();                          | Görevi hafta sonlarıyla sınırla.                                       |                                  |
| ->sundays();                           | Görevi Pazar günüyle sınırla.                                          |                                  |
| ->mondays();                           | Görevi Pazartesi günüyle sınırla.                                      |                                  |
| ->tuesdays();                          | Görevi Salı günüyle sınırla.                                           |                                  |
| ->wednesdays();                        | Görevi Çarşamba günüyle sınırla.                                       |                                  |
| ->thursdays();                         | Görevi Perşembe günüyle sınırla.                                       |                                  |
| ->fridays();                           | Görevi Cuma günüyle sınırla.                                           |                                  |
| ->saturdays();                         | Görevi Cumartesi günüyle sınırla.                                      |                                  |
| ->days(array                           | mixed);                                                                | Görevi belirli günlerle sınırla. |
| ->between($startTime, $endTime);       | Görevi başlangıç ve bitiş saatleri arasında çalışacak şekilde sınırla. |                                  |
| ->unlessBetween($startTime, $endTime); | Görevi belirli saatler arasında çalışmaması için sınırla.              |                                  |
| ->when(Closure);                       | Görevi bir doğruluk testine göre sınırla.                              |                                  |
| ->environments($env);                  | Görevi belirli ortamlarla sınırla.                                     |                                  |

<br>


## Day Constraints

`days` metodu, bir görevin yalnızca haftanın belirli günlerinde çalışmasını sağlamak için kullanılabilir. Örneğin, bir komutu yalnızca Pazar ve Çarşamba günleri saatlik olarak çalışacak şekilde zamanlayabilirsiniz:

```php
use Illuminate\Support\Facades\Schedule;
 
Schedule::command('emails:send')
    ->hourly()
    ->days([0, 3]);
```

Alternatif olarak, bir görevin hangi günlerde çalışacağını tanımlarken `Illuminate\Console\Scheduling\Schedule` sınıfında bulunan sabitleri kullanabilirsiniz:

```php
use Illuminate\Support\Facades;
use Illuminate\Console\Scheduling\Schedule;
 
Facades\Schedule::command('emails:send')
    ->hourly()
    ->days([Schedule::SUNDAY, Schedule::WEDNESDAY]);
```

```
```

<br>


## Between Time Constraints

`between` metodu, bir görevin günün belirli saatlerine göre çalışmasını sınırlamak için kullanılabilir:

```php
Schedule::command('emails:send')
    ->hourly()
    ->between('7:00', '22:00');
````

Benzer şekilde, `unlessBetween` metodu belirli bir zaman aralığında bir görevin çalışmasını engellemek için kullanılabilir:

```php
Schedule::command('emails:send')
    ->hourly()
    ->unlessBetween('23:00', '4:00');
```

<br>


## Truth Test Constraints

`when` metodu, verilen bir doğruluk testi sonucuna göre bir görevin çalışmasını sınırlamak için kullanılabilir. Başka bir deyişle, belirtilen closure `true` dönerse, başka hiçbir kısıtlama engel olmadıkça görev çalışacaktır:

```php
Schedule::command('emails:send')->daily()->when(function () {
    return true;
});
```

`skip` metodu, `when` metodunun tersi olarak düşünülebilir. Eğer `skip` metodu `true` dönerse, zamanlanmış görev çalıştırılmaz:

```php
Schedule::command('emails:send')->daily()->skip(function () {
    return true;
});
```

Zincirlenmiş `when` metodları kullanıldığında, zamanlanmış komut yalnızca tüm `when` koşulları `true` dönerse çalışacaktır.

<br>


## Environment Constraints

`environments` metodu, görevlerin yalnızca belirli ortam değişkenlerinde (`APP_ENV`) çalışmasını sağlamak için kullanılabilir:

```php
Schedule::command('emails:send')
    ->daily()
    ->environments(['staging', 'production']);
```

<br>


## Timezones

`timezone` metodunu kullanarak, zamanlanmış bir görevin belirli bir saat diliminde yorumlanmasını sağlayabilirsiniz:

```php
use Illuminate\Support\Facades\Schedule;
 
Schedule::command('report:generate')
    ->timezone('America/New_York')
    ->at('2:00');
```

Tüm zamanlanmış görevlerinize aynı saat dilimini atamak istiyorsanız, uygulamanızın `app` yapılandırma dosyasında bir `schedule_timezone` seçeneği tanımlayabilirsiniz:

```php
'timezone' => 'UTC',
 
'schedule_timezone' => 'America/Chicago',
```

Bazı saat dilimlerinin yaz saati uygulamasını (daylight savings time) kullandığını unutmayın. Yaz saati değişimleri meydana geldiğinde, zamanlanmış göreviniz iki kez çalışabilir veya hiç çalışmayabilir. Bu nedenle, mümkün olduğunca saat dilimine dayalı zamanlamadan kaçınmanız önerilir.

<br>


## Preventing Task Overlaps

Varsayılan olarak, zamanlanmış görevler bir önceki örneği hâlâ çalışıyorsa bile yeniden çalıştırılır. Bunu önlemek için `withoutOverlapping` metodunu kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Schedule;
 
Schedule::command('emails:send')->withoutOverlapping();
```

Bu örnekte, `emails:send` Artisan komutu yalnızca hâlihazırda çalışmıyorsa her dakika çalıştırılacaktır. `withoutOverlapping` metodu, çalışma süresi büyük ölçüde değişen görevler için özellikle faydalıdır.

Gerekirse, “çakışmayı önleme” kilidinin ne kadar süre sonra sona ereceğini (dakika cinsinden) belirtebilirsiniz. Varsayılan olarak bu süre 24 saattir:

```php
Schedule::command('emails:send')->withoutOverlapping(10);
```

Arka planda, `withoutOverlapping` metodu, uygulamanızın cache’ini kullanarak kilitler oluşturur. Gerekirse, bu cache kilitlerini `schedule:clear-cache` Artisan komutunu kullanarak temizleyebilirsiniz. Bu genellikle bir görevin beklenmedik bir sunucu hatası nedeniyle takılı kalması durumunda gereklidir.

<br>


## Running Tasks on One Server

Bu özelliği kullanabilmek için uygulamanızın varsayılan cache sürücüsü olarak `database`, `memcached`, `dynamodb` veya `redis` kullanıyor olması gerekir. Ayrıca tüm sunucuların aynı merkezi cache sunucusuna bağlanabiliyor olması gerekir.

Eğer scheduler birden fazla sunucuda çalışıyorsa, bir görevi yalnızca tek bir sunucuda çalışacak şekilde sınırlayabilirsiniz. Örneğin, her cuma gecesi yeni bir rapor oluşturan bir göreviniz olduğunu varsayalım. Scheduler üç farklı sunucuda çalışıyorsa, görev üç kez çalıştırılacak ve üç rapor üretilecektir. Bu istenmeyen bir durumdur!

Görevin yalnızca tek bir sunucuda çalışması gerektiğini belirtmek için, görev tanımında `onOneServer` metodunu kullanabilirsiniz. İlk kilidi alan sunucu, diğer sunucuların aynı görevi eşzamanlı çalıştırmasını engelleyecek atomik bir kilit elde eder:

```php
use Illuminate\Support\Facades\Schedule;
 
Schedule::command('report:generate')
    ->fridays()
    ->at('17:00')
    ->onOneServer();
```

Scheduler’ın tek sunuculu görevler için kullandığı atomik kilitlerin hangi cache deposunda tutulacağını özelleştirmek için `useCache` metodunu kullanabilirsiniz:

```php
Schedule::useCache('database');
```

<br>


## Naming Single Server Jobs

Bazen aynı işi farklı parametrelerle zamanlamanız gerekebilir, ancak her bir varyasyonun yalnızca bir sunucuda çalıştırılmasını isteyebilirsiniz. Bunu başarmak için her zamanlama tanımına `name` metodu aracılığıyla benzersiz bir isim atayabilirsiniz:

```php
Schedule::job(new CheckUptime('https://laravel.com'))
    ->name('check_uptime:laravel.com')
    ->everyFiveMinutes()
    ->onOneServer();
 
Schedule::job(new CheckUptime('https://vapor.laravel.com'))
    ->name('check_uptime:vapor.laravel.com')
    ->everyFiveMinutes()
    ->onOneServer();
```

Benzer şekilde, tek bir sunucuda çalışacak şekilde tasarlanmış closure görevlerine de bir isim atanmalıdır:

```php
Schedule::call(fn () => User::resetApiRequestCount())
    ->name('reset-api-request-count')
    ->daily()
    ->onOneServer();
```

<br>


## Background Tasks

Varsayılan olarak, aynı anda zamanlanmış birden fazla görev, tanımlandıkları sıraya göre ardışık olarak çalıştırılır. Uzun süren görevleriniz varsa, bu durum sonraki görevlerin planlanandan çok daha geç başlamasına neden olabilir. Tüm görevlerin aynı anda çalışabilmesi için `runInBackground` metodunu kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Schedule;
 
Schedule::command('analytics:report')
    ->daily()
    ->runInBackground();
```

`runInBackground` metodu yalnızca `command` ve `exec` metodlarıyla zamanlanan görevlerde kullanılabilir.

<br>


## Maintenance Mode

Uygulamanız bakım modundayken zamanlanmış görevler çalıştırılmaz, çünkü bakım sırasında görevlerin işlemlere müdahale etmesini istemeyiz. Ancak, bir görevin bakım modunda bile çalışmasını istiyorsanız, tanımına `evenInMaintenanceMode` metodunu ekleyebilirsiniz:

```php
Schedule::command('emails:send')->evenInMaintenanceMode();
```

<br>


## Schedule Groups

Benzer yapılandırmalara sahip birden fazla zamanlanmış görevi tanımlarken, Laravel’in görev gruplama özelliğini kullanarak tekrar eden ayarları önleyebilirsiniz. Görev gruplama kodunuzu sadeleştirir ve benzer görevler arasında tutarlılığı sağlar.

Bir grup zamanlanmış görev oluşturmak için, istediğiniz yapılandırma metodlarını çağırın ve ardından `group` metodunu zincirleyin. `group` metodu, belirtilen yapılandırmayı paylaşan görevleri tanımlayan bir closure kabul eder:

```php
use Illuminate\Support\Facades\Schedule;
 
Schedule::daily()
    ->onOneServer()
    ->timezone('America/New_York')
    ->group(function () {
        Schedule::command('emails:send --force');
        Schedule::command('emails:prune');
    });
```

<br>


## Running the Scheduler

Zamanlanmış görevlerinizi nasıl tanımlayacağınızı öğrendik, şimdi bunları sunucuda nasıl çalıştıracağımızı inceleyelim. `schedule:run` Artisan komutu, tüm zamanlanmış görevlerinizi değerlendirir ve sunucunun mevcut zamanına göre çalıştırılıp çalıştırılmayacaklarını belirler.

Laravel’in scheduler’ını kullanırken, sunucunuza yalnızca tek bir cron yapılandırma girdisi eklemeniz gerekir. Bu girdi, `schedule:run` komutunu her dakika çalıştırır. Eğer sunucunuza cron girişi eklemeyi bilmiyorsanız, zamanlanmış görevlerinizi sizin için yöneten **Laravel Cloud** gibi bir yönetimli platform kullanabilirsiniz:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

<br>


## Sub-Minute Scheduled Tasks

Çoğu işletim sisteminde cron görevleri en fazla bir dakika aralıklarla çalıştırılabilir. Ancak Laravel’in scheduler’ı, görevlerin saniyelik aralıklarla bile çalıştırılmasına olanak tanır:

```php
use Illuminate\Support\Facades\Schedule;
 
Schedule::call(function () {
    DB::table('recent_users')->delete();
})->everySecond();
```

Uygulamanızda alt dakikalık (sub-minute) görevler tanımlandığında, `schedule:run` komutu hemen çıkmak yerine mevcut dakika bitene kadar çalışmaya devam eder. Böylece, bu süre boyunca gerekli tüm alt dakikalık görevleri çalıştırabilir.

Uzun sürebilecek alt dakikalık görevler, diğer alt dakikalık görevlerin gecikmesine neden olabileceğinden, bu tür görevlerin iş kuyruklarına veya arka plan komutlarına devredilmesi önerilir:

```php
use App\Jobs\DeleteRecentUsers;
 
Schedule::job(new DeleteRecentUsers)->everyTenSeconds();
 
Schedule::command('users:delete')->everyTenSeconds()->runInBackground();
```

<br>


## Interrupting Sub-Minute Tasks

Alt dakikalık görevler tanımlandığında `schedule:run` komutu bir dakika boyunca kesintisiz çalışır. Uygulamanızı dağıtırken (deploy) bu komutu durdurmanız gerekebilir. Aksi takdirde, hâlihazırda çalışan bir `schedule:run` örneği eski kodla çalışmaya devam eder.

Devam eden `schedule:run` çalıştırmalarını durdurmak için, uygulamanızın dağıtım (deployment) betiğine `schedule:interrupt` komutunu ekleyebilirsiniz. Bu komut, dağıtım işlemi tamamlandıktan sonra çalıştırılmalıdır:

```bash
php artisan schedule:interrupt
```

<br>


## Running the Scheduler Locally

Yerel geliştirme makinenize cron girdisi eklemeniz genellikle gerekmez. Bunun yerine, `schedule:work` Artisan komutunu kullanabilirsiniz. Bu komut ön planda çalışır ve her dakika scheduler’ı çağırır. Alt dakikalık görevler tanımlandığında, scheduler her dakika boyunca çalışmaya devam ederek bu görevleri işler:

```bash
php artisan schedule:work
```

<br>


## Task Output

Laravel scheduler, zamanlanmış görevlerden üretilen çıktılarla çalışmak için birkaç kullanışlı metot sunar. Öncelikle, `sendOutputTo` metodunu kullanarak çıktıyı bir dosyaya yönlendirebilirsiniz:

```php
use Illuminate\Support\Facades\Schedule;
 
Schedule::command('emails:send')
    ->daily()
    ->sendOutputTo($filePath);
```

Eğer çıktının bir dosyaya eklenmesini (append) istiyorsanız, `appendOutputTo` metodunu kullanabilirsiniz:

```php
Schedule::command('emails:send')
    ->daily()
    ->appendOutputTo($filePath);
```

`emailOutputTo` metodunu kullanarak çıktıyı belirli bir e-posta adresine gönderebilirsiniz. Ancak, bunu yapmadan önce Laravel’in e-posta servislerini yapılandırmanız gerekir:

```php
Schedule::command('report:generate')
    ->daily()
    ->sendOutputTo($filePath)
    ->emailOutputTo('taylor@example.com');
```

Eğer yalnızca zamanlanmış Artisan veya sistem komutu **başarısız olursa** (yani sıfır olmayan bir çıkış koduyla sonlanırsa) e-posta göndermek istiyorsanız, `emailOutputOnFailure` metodunu kullanabilirsiniz:

```php
Schedule::command('report:generate')
    ->daily()
    ->emailOutputOnFailure('taylor@example.com');
```

`emailOutputTo`, `emailOutputOnFailure`, `sendOutputTo` ve `appendOutputTo` metotları yalnızca `command` ve `exec` metotlarıyla kullanılabilir.

```
```

<br>


## Task Hooks

`before` ve `after` metotlarını kullanarak, zamanlanmış bir görev çalıştırılmadan **önce** ve **sonra** yürütülecek kodu belirleyebilirsiniz:

```php
use Illuminate\Support\Facades\Schedule;
 
Schedule::command('emails:send')
    ->daily()
    ->before(function () {
        // Görev çalıştırılmak üzere...
    })
    ->after(function () {
        // Görev çalıştırıldı...
    });
````

`onSuccess` ve `onFailure` metotları, zamanlanmış görevin başarılı veya başarısız olması durumunda çalıştırılacak kodu belirlemenize olanak tanır. Bir başarısızlık, zamanlanmış Artisan veya sistem komutunun sıfırdan farklı bir çıkış kodu ile sonlanması anlamına gelir:

```php
Schedule::command('emails:send')
    ->daily()
    ->onSuccess(function () {
        // Görev başarıyla tamamlandı...
    })
    ->onFailure(function () {
        // Görev başarısız oldu...
    });
```

Komutunuzdan bir çıktı mevcutsa, bu çıktıya `after`, `onSuccess` veya `onFailure` hook’larında, closure tanımında `$output` parametresi olarak `Illuminate\Support\Stringable` örneğini type-hint ederek erişebilirsiniz:

```php
use Illuminate\Support\Stringable;
 
Schedule::command('emails:send')
    ->daily()
    ->onSuccess(function (Stringable $output) {
        // Görev başarıyla tamamlandı...
    })
    ->onFailure(function (Stringable $output) {
        // Görev başarısız oldu...
    });
```

<br>


## Pinging URLs

`pingBefore` ve `thenPing` metotlarını kullanarak, scheduler bir görevi çalıştırmadan önce veya sonra belirtilen bir URL’ye otomatik olarak ping atabilir. Bu yöntem, Envoyer gibi harici bir servise zamanlanmış görevin başladığını veya tamamlandığını bildirmek için kullanışlıdır:

```php
Schedule::command('emails:send')
    ->daily()
    ->pingBefore($url)
    ->thenPing($url);
```

`pingOnSuccess` ve `pingOnFailure` metotları, görevin yalnızca başarılı veya başarısız olması durumunda belirli bir URL’ye ping atmak için kullanılabilir. Bir başarısızlık, zamanlanmış Artisan veya sistem komutunun sıfırdan farklı bir çıkış koduyla sonlanması anlamına gelir:

```php
Schedule::command('emails:send')
    ->daily()
    ->pingOnSuccess($successUrl)
    ->pingOnFailure($failureUrl);
```

`pingBeforeIf`, `thenPingIf`, `pingOnSuccessIf` ve `pingOnFailureIf` metotları, yalnızca belirli bir koşul doğruysa URL’ye ping atmak için kullanılabilir:

```php
Schedule::command('emails:send')
    ->daily()
    ->pingBeforeIf($condition, $url)
    ->thenPingIf($condition, $url);
 
Schedule::command('emails:send')
    ->daily()
    ->pingOnSuccessIf($condition, $successUrl)
    ->pingOnFailureIf($condition, $failureUrl);
```

<br>


## Events

Laravel, zamanlama süreci sırasında çeşitli olaylar (event) yayınlar. Aşağıdaki olaylardan herhangi biri için listener tanımlayabilirsiniz:

| Event Name                                                  |
| ----------------------------------------------------------- |
| `Illuminate\Console\Events\ScheduledTaskStarting`           |
| `Illuminate\Console\Events\ScheduledTaskFinished`           |
| `Illuminate\Console\Events\ScheduledBackgroundTaskFinished` |
| `Illuminate\Console\Events\ScheduledTaskSkipped`            |
| `Illuminate\Console\Events\ScheduledTaskFailed`             |

Laravel, yazılım oluşturmanın, dağıtmanın ve izlemenin en verimli yoludur.

```
```
