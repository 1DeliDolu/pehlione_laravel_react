
<br>



  

## Artisan Console

### Giriş

Artisan, Laravel ile birlikte gelen komut satırı arayüzüdür. Artisan, uygulamanızın kök dizininde **artisan** betiği olarak bulunur ve uygulamanızı geliştirirken size yardımcı olabilecek birçok kullanışlı komut sağlar. Mevcut tüm Artisan komutlarının bir listesini görmek için aşağıdaki komutu kullanabilirsiniz:

```bash
php artisan list
````

Her komut ayrıca, komutun kullanılabilir argümanlarını ve seçeneklerini görüntüleyen bir "yardım" ekranı içerir. Yardım ekranını görmek için komut adının önüne `help` ekleyin:

```bash
php artisan help migrate
```

<br>



 

### Laravel Sail

Eğer yerel geliştirme ortamınız olarak **Laravel Sail** kullanıyorsanız, Artisan komutlarını çalıştırmak için `sail` komut satırını kullanmayı unutmayın. Sail, Artisan komutlarını uygulamanızın Docker konteynerleri içinde çalıştırır:

```bash
./vendor/bin/sail artisan list
```

<br>



  

### Tinker (REPL)

**Laravel Tinker**, Laravel framework’ü için **PsySH** paketi tarafından desteklenen güçlü bir REPL ortamıdır.

<br>



   

#### Kurulum

Tüm Laravel uygulamaları varsayılan olarak Tinker içerir. Ancak, uygulamanızdan kaldırdıysanız Composer aracılığıyla yeniden yükleyebilirsiniz:

```bash
composer require laravel/tinker
```

Laravel uygulamanızla etkileşime girerken otomatik tamamlama, çok satırlı düzenleme ve canlı yenileme istiyorsanız **Tinkerwell**’e göz atabilirsiniz!

<br>





#### Kullanım

Tinker, komut satırından tüm Laravel uygulamanızla etkileşim kurmanıza olanak tanır; buna **Eloquent modelleriniz**, **jobs**, **events** ve daha fazlası dahildir. Tinker ortamına girmek için aşağıdaki Artisan komutunu çalıştırın:

```bash
php artisan tinker
```

Tinker yapılandırma dosyasını aşağıdaki komutla yayınlayabilirsiniz:

```bash
php artisan vendor:publish --provider="Laravel\Tinker\TinkerServiceProvider"
```

`dispatch` yardımcı fonksiyonu ve `Dispatchable` sınıfındaki `dispatch` metodu, job’un kuyruğa eklenmesi için garbage collection’a bağlıdır. Bu nedenle **tinker** kullanırken job’ları kuyruğa eklemek için `Bus::dispatch` veya `Queue::push` kullanmalısınız.

<br>



    

#### Komut İzin Listesi

Tinker, kabuğu içinde hangi Artisan komutlarının çalıştırılmasına izin verildiğini belirlemek için bir "allow list" kullanır. Varsayılan olarak aşağıdaki komutları çalıştırabilirsiniz:
`clear-compiled`, `down`, `env`, `inspire`, `migrate`, `migrate:install`, `up`, ve `optimize`.

Daha fazla komuta izin vermek istiyorsanız, `tinker.php` yapılandırma dosyasındaki `commands` dizisine ekleyebilirsiniz:

```php
'commands' => [
    // App\Console\Commands\ExampleCommand::class,
],
```

<br>



  

#### Alias Verilmemesi Gereken Sınıflar

Genellikle Tinker, etkileşim sırasında sınıflara otomatik olarak alias verir. Ancak bazı sınıflara hiçbir zaman alias verilmemesini isteyebilirsiniz. Bunu `tinker.php` yapılandırma dosyasındaki `dont_alias` dizisine ekleyerek yapabilirsiniz:

```php
'dont_alias' => [
    App\Models\User::class,
],
```

<br>



   

## Komut Yazma

Artisan ile birlikte gelen komutlara ek olarak, kendi özel komutlarınızı da oluşturabilirsiniz. Komutlar genellikle `app/Console/Commands` dizininde saklanır; ancak Laravel’i başka dizinleri de taraması için yapılandırırsanız farklı bir konum da seçebilirsiniz.

<br>



 

### Komut Oluşturma

Yeni bir komut oluşturmak için `make:command` Artisan komutunu kullanabilirsiniz. Bu komut, `app/Console/Commands` dizininde yeni bir komut sınıfı oluşturur. Bu dizin mevcut değilse, komut çalıştırıldığında otomatik olarak oluşturulur:

```bash
php artisan make:command SendEmails
```

<br>



   

### Komut Yapısı

Komut oluşturduktan sonra, sınıfın `signature` ve `description` özelliklerine uygun değerler atamalısınız. Bu özellikler, komutun liste ekranında görüntülenirken kullanılır.
`signature` özelliği ayrıca komutun beklediği girdileri tanımlamanıza da olanak tanır.
Komut çalıştırıldığında `handle` metodu çağrılır ve komut mantığınızı bu metoda yerleştirebilirsiniz.

Aşağıda bir örnek komut görebilirsiniz. Dikkat ederseniz, `handle` metodunda ihtiyaç duyduğumuz bağımlılıkları type-hint ile isteyebiliyoruz. Laravel servis container’ı, bu bağımlılıkları otomatik olarak enjekte eder:

```php
<?php
 
namespace App\Console\Commands;
 
use App\Models\User;
use App\Support\DripEmailer;
use Illuminate\Console\Command;
 
class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send {user}';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a marketing email to a user';
 
    /**
     * Execute the console command.
     */
    public function handle(DripEmailer $drip): void
    {
        $drip->send(User::find($this->argument('user')));
    }
}
```

Daha fazla kod tekrarını önlemek için, **console komutlarınızı olabildiğince hafif tutmak** ve görevleri gerçekleştirmek için **uygulama servislerine** yönlendirmek iyi bir uygulamadır. Yukarıdaki örnekte, e-postaları göndermek için bir servis sınıfı enjekte ediyoruz.

<br>



  

### Çıkış Kodları

Eğer `handle` metodundan bir şey döndürülmezse ve komut başarıyla çalışırsa, komut başarıyı belirten `0` çıkış kodu ile sonlanır.
Ancak, çıkış kodunu manuel olarak belirtmek için `handle` metodundan bir tamsayı döndürebilirsiniz:

```php
$this->error('Something went wrong.');
 
return 1;
```

Komutu herhangi bir noktada başarısız saymak isterseniz `fail` metodunu kullanabilirsiniz. Bu metod, komutun çalışmasını hemen durdurur ve `1` çıkış kodu döndürür:

```php
$this->fail('Something went wrong.');
```

<br>



    

## Closure Komutları

Closure tabanlı komutlar, komutları sınıflar yerine closure (anonim fonksiyon) olarak tanımlamanın alternatif bir yoludur.
Nasıl ki **route closures** controller’lara alternatifse, **command closures** da komut sınıflarına alternatiftir.

`routes/console.php` dosyası HTTP route’larını tanımlamaz; bunun yerine uygulamanız için **konsol tabanlı giriş noktalarını (routes)** tanımlar.
Bu dosya içinde `Artisan::command` metodunu kullanarak tüm closure tabanlı komutlarınızı tanımlayabilirsiniz.
Bu metod iki argüman alır: komut imzası (`signature`) ve komutun argümanlarını ve seçeneklerini alan bir closure:

```php
Artisan::command('mail:send {user}', function (string $user) {
    $this->info("Sending email to: {$user}!");
});
```

Closure, altta yatan komut örneğine bağlandığı için, tipik bir komut sınıfında erişebileceğiniz tüm yardımcı metotlara erişebilirsiniz.

<br>



   

### Bağımlılıkları Type-Hint ile Belirtme

Komut closure’ları, komutun argümanlarını ve seçeneklerini almanın yanı sıra, servis container’dan çözülmesini istediğiniz ek bağımlılıkları da **type-hint** ile belirtebilir:

```php
use App\Models\User;
use App\Support\DripEmailer;
use Illuminate\Support\Facades\Artisan;
 
Artisan::command('mail:send {user}', function (DripEmailer $drip, string $user) {
    $drip->send(User::find($user));
});
```


<br>




## Closure Komut Açıklamaları

Closure tabanlı bir komut tanımlarken, komuta bir açıklama eklemek için `purpose` metodunu kullanabilirsiniz. Bu açıklama, `php artisan list` veya `php artisan help` komutlarını çalıştırdığınızda görüntülenir:

```php
Artisan::command('mail:send {user}', function (string $user) {
    // ...
})->purpose('Send a marketing email to a user');
````

<br>





## İzole Edilebilir Komutlar (Isolatable Commands)

Bu özelliği kullanmak için uygulamanızın varsayılan cache sürücüsü olarak **memcached**, **redis**, **dynamodb**, **database**, **file** veya **array** sürücülerinden birini kullanması gerekir. Ayrıca tüm sunucuların aynı merkezi cache sunucusuyla iletişim kurması gerekir.

Bazen yalnızca bir komut örneğinin aynı anda çalışmasını sağlamak isteyebilirsiniz. Bunu gerçekleştirmek için komut sınıfınızda `Illuminate\Contracts\Console\Isolatable` arayüzünü uygulayabilirsiniz:

```php
<?php
 
namespace App\Console\Commands;
 
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
 
class SendEmails extends Command implements Isolatable
{
    // ...
}
```

Bir komutu **Isolatable** olarak işaretlediğinizde, Laravel otomatik olarak komuta `--isolated` seçeneğini ekler. Bu seçeneği ayrıca tanımlamanıza gerek yoktur. Komut bu seçenekle çağrıldığında, Laravel aynı komutun başka örneklerinin zaten çalışmadığından emin olur.
Laravel bunu, uygulamanızın varsayılan cache sürücüsünü kullanarak atomik bir kilit (atomic lock) elde etmeye çalışarak yapar.
Eğer başka örnekler çalışıyorsa komut yürütülmez, ancak yine de başarılı bir çıkış kodu (exit status code) ile sonlanır:

```bash
php artisan mail:send 1 --isolated
```

Komutun çalışamayacağı durumda döndürmesi gereken çıkış kodunu belirtmek isterseniz, `--isolated` seçeneğine bir değer verebilirsiniz:

```bash
php artisan mail:send 1 --isolated=12
```

<br>





### Lock ID

Varsayılan olarak, Laravel komutun adını kullanarak cache üzerinde atomik kilidi elde etmek için bir anahtar oluşturur. Ancak, bu anahtarı özelleştirmek isterseniz, komut sınıfınızda `isolatableId` metodunu tanımlayarak komutun argüman veya seçeneklerini anahtara dahil edebilirsiniz:

```php
/**
 * Get the isolatable ID for the command.
 */
public function isolatableId(): string
{
    return $this->argument('user');
}
```

<br>





### Lock Süresi (Expiration Time)

Varsayılan olarak, izolasyon kilitleri komut tamamlandığında sona erer.
Eğer komut yarıda kesilirse, kilit bir saat sonra otomatik olarak sona erer.
Ancak, kilit süresini değiştirmek isterseniz, komutunuzda `isolationLockExpiresAt` metodunu tanımlayabilirsiniz:

```php
use DateTimeInterface;
use DateInterval;
 
/**
 * Determine when an isolation lock expires for the command.
 */
public function isolationLockExpiresAt(): DateTimeInterface|DateInterval
{
    return now()->addMinutes(5);
}
```

<br>





## Girdi Beklentilerini Tanımlama

Console komutları yazarken, kullanıcıdan **argümanlar** veya **seçenekler (options)** aracılığıyla girdi almak oldukça yaygındır.
Laravel, bu girdileri `signature` özelliğiyle tanımlamayı son derece kolaylaştırır.
`signature` özelliği, komutun adını, argümanlarını ve seçeneklerini tek bir ifadede, route benzeri bir sözdizimiyle tanımlamanızı sağlar.

<br>




### Argümanlar (Arguments)

Kullanıcı tarafından sağlanan tüm argümanlar ve seçenekler süslü parantez içine alınır.
Aşağıdaki örnekte, komut bir tane zorunlu argüman tanımlar: `user`.

```php
/**
 * The name and signature of the console command.
 *
 * @var string
 */
protected $signature = 'mail:send {user}';
```

Argümanları isteğe bağlı hale getirebilir veya varsayılan değerler tanımlayabilirsiniz:

```php
// İsteğe bağlı argüman...
'mail:send {user?}'
 
// Varsayılan değerli isteğe bağlı argüman...
'mail:send {user=foo}'
```

<br>





### Seçenekler (Options)

Seçenekler de bir tür kullanıcı girdisidir ve komut satırında **iki tire (--)** ile başlarlar.
İki tür seçenek vardır:

1. Değer alan seçenekler
2. Değer almayan (boolean “switch”) seçenekler

Örneğin bir boolean “switch” seçeneği şu şekilde tanımlanabilir:

```php
/**
 * The name and signature of the console command.
 *
 * @var string
 */
protected $signature = 'mail:send {user} {--queue}';
```

Bu durumda, `--queue` seçeneği komut çağrılırken belirtilirse değeri `true`, belirtilmezse `false` olur:

```bash
php artisan mail:send 1 --queue
```

<br>




### Değer Alan Seçenekler

Eğer bir seçenek bir değer almalıdır diyorsanız, seçenek adının sonuna bir `=` işareti ekleyin:

```php
protected $signature = 'mail:send {user} {--queue=}';
```

Kullanıcı komutu şu şekilde çağırabilir. Seçenek belirtilmezse değeri `null` olur:

```bash
php artisan mail:send 1 --queue=default
```

Bir seçeneğe varsayılan değer atamak için eşittir işaretinden sonra değeri belirtebilirsiniz:

```php
'mail:send {user} {--queue=default}'
```

<br>




### Seçenek Kısayolları

Bir seçeneğe kısayol atamak için, kısayolu seçenek adından önce belirtebilir ve araya `|` karakteri koyabilirsiniz:

```php
'mail:send {user} {--Q|queue}'
```

Komutu terminalde çağırırken kısayollar **tek tire (-)** ile başlatılır ve değer atanırken `=` karakteri kullanılmaz:

```bash
php artisan mail:send 1 -Qdefault
```

<br>




### Girdi Dizileri (Input Arrays)

Birden fazla girdi değerini kabul edecek argüman veya seçenek tanımlamak istiyorsanız, `*` karakterini kullanabilirsiniz.
Örneğin:

```php
'mail:send {user*}'
```

Bu durumda kullanıcı, argümanları sırasıyla geçebilir. Aşağıdaki örnek `user` değerini `[1, 2]` dizisi olarak ayarlar:

```bash
php artisan mail:send 1 2
```

`*` karakteri, isteğe bağlı argüman tanımıyla da birleştirilerek sıfır veya daha fazla değer kabul edilmesini sağlar:

```php
'mail:send {user?*}'
```

<br>




### Seçenek Dizileri (Option Arrays)

Bir seçenek birden fazla değer alacaksa, her değer komut satırında seçenek adıyla birlikte belirtilmelidir:

```php
'mail:send {--id=*}'
```

Komut şu şekilde çağrılabilir:

```bash
php artisan mail:send --id=1 --id=2
```

<br>




### Girdi Açıklamaları (Input Descriptions)

Argümanlara ve seçeneklere açıklama eklemek için isimden sonra iki nokta (`:`) kullanabilirsiniz.
Tanımı birden fazla satıra yaymak isterseniz bunu da yapabilirsiniz:

```php
/**
 * The name and signature of the console command.
 *
 * @var string
 */
protected $signature = 'mail:send
                        {user : The ID of the user}
                        {--queue : Whether the job should be queued}';
```

<br>




## Eksik Girdi İçin Kullanıcıdan Bilgi İsteme (Prompting for Missing Input)

Eğer komutunuz zorunlu argümanlar içeriyorsa ve kullanıcı bunları belirtmezse, hata mesajı görüntülenir.
Alternatif olarak, kullanıcıdan otomatik olarak bilgi istemek için `PromptsForMissingInput` arayüzünü uygulayabilirsiniz:

```php
<?php
 
namespace App\Console\Commands;
 
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
 
class SendEmails extends Command implements PromptsForMissingInput
{
    protected $signature = 'mail:send {user}';
 
    // ...
}
```

Laravel, eksik argümanları toplamak için kullanıcıya argüman adını veya açıklamasını kullanarak otomatik bir soru yöneltir.
Eğer bu soruyu özelleştirmek isterseniz, `promptForMissingArgumentsUsing` metodunu tanımlayıp, argüman adlarını anahtar olarak içeren bir dizi döndürebilirsiniz:

```php
/**
 * Prompt for missing input arguments using the returned questions.
 *
 * @return array<string, string>
 */
protected function promptForMissingArgumentsUsing(): array
{
    return [
        'user' => 'Which user ID should receive the mail?',
    ];
}
```

Yer tutucu (placeholder) eklemek isterseniz, bir dizi (tuple) kullanabilirsiniz:

```php
return [
    'user' => ['Which user ID should receive the mail?', 'E.g. 123'],
];
```

Kullanıcıdan alınacak girdiyi tamamen kontrol etmek istiyorsanız, bir closure tanımlayabilirsiniz:

```php
use App\Models\User;
use function Laravel\Prompts\search;
 
return [
    'user' => fn () => search(
        label: 'Search for a user:',
        placeholder: 'E.g. Taylor Otwell',
        options: fn ($value) => strlen($value) > 0
            ? User::whereLike('name', "%{$value}%")->pluck('name', 'id')->all()
            : []
    ),
];
```

Laravel Prompts dokümantasyonu, kullanılabilir prompt türleri ve kullanımları hakkında daha fazla bilgi içerir.

Eğer kullanıcıdan seçenek seçmesini veya değer girmesini istemek istiyorsanız, `handle` metodunda da prompt kullanabilirsiniz.
Ancak yalnızca eksik argümanlar için otomatik olarak prompt tetiklenmişse bunu yapmak istiyorsanız, `afterPromptingForMissingArguments` metodunu uygulayabilirsiniz:

```php
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Laravel\Prompts\confirm;
 
/**
 * Perform actions after the user was prompted for missing arguments.
 */
protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
{
    $input->setOption('queue', confirm(
        label: 'Would you like to queue the mail?',
        default: $this->option('queue')
    ));
}
```


<br>




## Komut Girdi / Çıktısı (Command I/O)

<br>




### Girdi Alma (Retrieving Input)

Komutunuz çalışırken, kabul edilen argüman ve seçeneklerin değerlerine erişmeniz gerekebilir.  
Bunu yapmak için `argument` ve `option` metodlarını kullanabilirsiniz.  
Eğer belirtilen argüman veya seçenek mevcut değilse `null` döner:

```php
/**
 * Execute the console command.
 */
public function handle(): void
{
    $userId = $this->argument('user');
}
````

Tüm argümanları bir dizi (array) olarak almak isterseniz, `arguments` metodunu çağırabilirsiniz:

```php
$arguments = $this->arguments();
```

Seçenekler, `option` metodu ile aynı şekilde alınabilir.
Tüm seçenekleri dizi olarak almak için `options` metodunu kullanabilirsiniz:

```php
// Belirli bir seçenek al...
$queueName = $this->option('queue');
 
// Tüm seçenekleri al...
$options = $this->options();
```

<br>




### Kullanıcıdan Girdi İsteme (Prompting for Input)

**Laravel Prompts**, komut satırı uygulamalarınıza güzel ve kullanıcı dostu formlar eklemenizi sağlayan bir PHP paketidir.
Bu paket, **placeholder** metinleri ve **doğrulama (validation)** gibi tarayıcı benzeri özellikler sunar.

Komut yürütülürken kullanıcıdan girdi almak istiyorsanız `ask` metodunu kullanabilirsiniz.
Bu metot kullanıcıya bir soru sorar, yanıtı alır ve girdiyi döndürür:

```php
/**
 * Execute the console command.
 */
public function handle(): void
{
    $name = $this->ask('What is your name?');
 
    // ...
}
```

`ask` metodu, ikinci bir parametre olarak varsayılan bir değer de kabul eder.
Kullanıcı herhangi bir girdi sağlamazsa bu değer döndürülür:

```php
$name = $this->ask('What is your name?', 'Taylor');
```

**Gizli girdi** almak için `secret` metodunu kullanabilirsiniz.
Kullanıcının yazdığı karakterler konsolda görünmez.
Bu yöntem, şifre gibi hassas bilgiler istenirken faydalıdır:

```php
$password = $this->secret('What is the password?');
```

<br>




### Onay İsteme (Asking for Confirmation)

Kullanıcıdan basit bir "evet veya hayır" cevabı almak isterseniz, `confirm` metodunu kullanabilirsiniz.
Varsayılan olarak `false` döner. Ancak kullanıcı `y` veya `yes` girerse `true` döner:

```php
if ($this->confirm('Do you wish to continue?')) {
    // ...
}
```

Eğer varsayılan cevabın `true` olmasını isterseniz, ikinci argümanı `true` olarak geçebilirsiniz:

```php
if ($this->confirm('Do you wish to continue?', true)) {
    // ...
}
```

<br>




### Otomatik Tamamlama (Auto-Completion)

`anticipate` metodu, olası yanıtlar için otomatik tamamlama önerileri sunar.
Kullanıcı isterse farklı bir yanıt da girebilir:

```php
$name = $this->anticipate('What is your name?', ['Taylor', 'Dayle']);
```

Ayrıca, ikinci argüman olarak bir closure geçebilirsiniz.
Bu closure, kullanıcı her karakter girdiğinde çağrılır.
Kullanıcının o ana kadar yazdığı değeri alır ve otomatik tamamlama seçeneklerinin bir dizisini döndürmelidir:

```php
use App\Models\Address;
 
$name = $this->anticipate('What is your address?', function (string $input) {
    return Address::whereLike('name', "{$input}%")
        ->limit(5)
        ->pluck('name')
        ->all();
});
```

<br>




### Çoktan Seçmeli Sorular (Multiple Choice Questions)

Kullanıcıya önceden tanımlanmış seçenekler sunmak isterseniz, `choice` metodunu kullanabilirsiniz.
Üçüncü parametre, kullanıcı seçim yapmazsa hangi dizin numarasındaki (index) seçeneğin varsayılan olarak döneceğini belirler:

```php
$name = $this->choice(
    'What is your name?',
    ['Taylor', 'Dayle'],
    $defaultIndex
);
```

Ayrıca, `choice` metodu dördüncü ve beşinci isteğe bağlı argümanlar alır:

* Maksimum deneme sayısı (`maxAttempts`)
* Birden fazla seçim yapılmasına izin verilip verilmediği (`allowMultipleSelections`)

```php
$name = $this->choice(
    'What is your name?',
    ['Taylor', 'Dayle'],
    $defaultIndex,
    $maxAttempts = null,
    $allowMultipleSelections = false
);
```

<br>




## Çıktı Yazdırma (Writing Output)

Konsola çıktı göndermek için şu metotları kullanabilirsiniz:
`line`, `newLine`, `info`, `comment`, `question`, `warn`, `alert`, `error`.

Bu metotlar, amaçlarına uygun ANSI renklerini kullanarak metni biçimlendirir.
Örneğin, kullanıcıya bilgi vermek için `info` metodunu kullanabilirsiniz.
Genellikle yeşil renkte görüntülenir:

```php
/**
 * Execute the console command.
 */
public function handle(): void
{
    // ...
 
    $this->info('The command was successful!');
}
```

Bir hata mesajı göstermek için `error` metodunu kullanın.
Bu metin genellikle kırmızı renkte görüntülenir:

```php
$this->error('Something went wrong!');
```

Renkli olmayan düz metin görüntülemek için `line` metodunu kullanabilirsiniz:

```php
$this->line('Display this on the screen');
```

Boş satır eklemek için `newLine` metodunu kullanabilirsiniz:

```php
// Tek bir boş satır yazdır...
$this->newLine();
 
// Üç boş satır yazdır...
$this->newLine(3);
```

<br>




### Tablolar (Tables)

`table` metodu, birden fazla satır ve sütundan oluşan verileri düzgün biçimlendirmek için kullanılır.
Sadece sütun adlarını ve verileri sağlamanız yeterlidir; Laravel otomatik olarak tablo genişliğini ve yüksekliğini hesaplar:

```php
use App\Models\User;
 
$this->table(
    ['Name', 'Email'],
    User::all(['name', 'email'])->toArray()
);
```

<br>





### İlerleme Çubukları (Progress Bars)

Uzun süren görevlerde, görevin ilerleme durumunu göstermek için bir **progress bar** kullanmak faydalıdır.
`withProgressBar` metodu, verilen iterable değer üzerinde her yinelemede ilerleyen bir çubuk gösterir:

```php
use App\Models\User;
 
$users = $this->withProgressBar(User::all(), function (User $user) {
    $this->performTask($user);
});
```

Daha fazla kontrol gerektiğinde, ilerleme çubuğunu manuel olarak da yönetebilirsiniz.
Önce işlem yapılacak toplam adım sayısını tanımlayın, sonra her öğe işlendiğinde çubuğu ilerletin:

```php
$users = App\Models\User::all();
 
$bar = $this->output->createProgressBar(count($users));
 
$bar->start();
 
foreach ($users as $user) {
    $this->performTask($user);
 
    $bar->advance();
}
 
$bar->finish();
```

Daha gelişmiş kullanım seçenekleri için **Symfony Progress Bar** bileşeninin belgelerine göz atabilirsiniz.




<br>



## Komutları Kaydetme (Registering Commands)

Varsayılan olarak, Laravel **app/Console/Commands** dizinindeki tüm komutları otomatik olarak kaydeder.  
Ancak, Laravel’in başka dizinlerdeki Artisan komutlarını da taramasını isterseniz, uygulamanızın **bootstrap/app.php** dosyasındaki `withCommands` metodunu kullanabilirsiniz:

```php
->withCommands([
    __DIR__.'/../app/Domain/Orders/Commands',
])
````

Gerekirse, komut sınıfının adını doğrudan `withCommands` metoduna vererek komutları manuel olarak da kaydedebilirsiniz:

```php
use App\Domain\Orders\Commands\SendEmails;
 
->withCommands([
    SendEmails::class,
])
```

Artisan başlatıldığında, uygulamanızdaki tüm komutlar **service container** tarafından çözümlenir ve Artisan’a kaydedilir.

<br>



## Komutları Programatik Olarak Çalıştırma (Programmatically Executing Commands)

Bazen bir Artisan komutunu CLI dışından çalıştırmak isteyebilirsiniz.
Örneğin, bir **route** veya **controller** içinden bir komutu tetiklemek isteyebilirsiniz.
Bunu yapmak için `Artisan` facade’ındaki `call` metodunu kullanabilirsiniz.

`call` metodu, ilk argüman olarak komutun **signature adını** veya **sınıf adını**,
ikinci argüman olarak ise komut parametrelerini içeren bir dizi alır.
Metot, komutun **çıkış kodunu (exit code)** döndürür:

```php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
 
Route::post('/user/{user}/mail', function (string $user) {
    $exitCode = Artisan::call('mail:send', [
        'user' => $user, '--queue' => 'default'
    ]);
 
    // ...
});
```

Alternatif olarak, tüm komutu bir string olarak da `call` metoduna geçebilirsiniz:

```php
Artisan::call('mail:send 1 --queue=default');
```

<br>



### Dizi Değerleri Gönderme (Passing Array Values)

Komutunuz bir dizi değer kabul eden bir seçenek tanımlıyorsa, o seçeneğe bir dizi değer gönderebilirsiniz:

```php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
 
Route::post('/mail', function () {
    $exitCode = Artisan::call('mail:send', [
        '--id' => [5, 13]
    ]);
});
```

<br>



### Boolean Değerler Gönderme (Passing Boolean Values)

Eğer komut, string değer kabul etmeyen bir seçenek içeriyorsa (örneğin `migrate:refresh` komutundaki `--force` bayrağı gibi),
bu seçeneğe `true` veya `false` değerlerini geçmelisiniz:

```php
$exitCode = Artisan::call('migrate:refresh', [
    '--force' => true,
]);
```

<br>



## Artisan Komutlarını Kuyruğa Ekleme (Queueing Artisan Commands)

`Artisan` facade’ındaki `queue` metodu, Artisan komutlarını arka planda **queue worker** tarafından işlenmek üzere kuyruğa eklemenizi sağlar.
Bu metodu kullanmadan önce kuyruğunuzu yapılandırdığınızdan ve bir **queue listener** çalıştırdığınızdan emin olun:

```php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
 
Route::post('/user/{user}/mail', function (string $user) {
    Artisan::queue('mail:send', [
        'user' => $user, '--queue' => 'default'
    ]);
 
    // ...
});
```

Ayrıca, `onConnection` ve `onQueue` metotlarını kullanarak Artisan komutunun hangi bağlantı veya kuyruğa gönderileceğini belirtebilirsiniz:

```php
Artisan::queue('mail:send', [
    'user' => 1, '--queue' => 'default'
])->onConnection('redis')->onQueue('commands');
```

<br>



## Diğer Komutlardan Komut Çağırma (Calling Commands From Other Commands)

Bazen mevcut bir Artisan komutu içinden başka bir komutu çalıştırmak isteyebilirsiniz.
Bunu yapmak için `call` metodunu kullanabilirsiniz.
Bu metot, komut adını ve komutun argüman/seçeneklerini alan bir dizi kabul eder:

```php
/**
 * Execute the console command.
 */
public function handle(): void
{
    $this->call('mail:send', [
        'user' => 1, '--queue' => 'default'
    ]);
 
    // ...
}
```

Eğer başka bir komutu çağırmak, ancak çıktısını bastırmak (suppress) isterseniz,
aynı imzaya sahip olan `callSilently` metodunu kullanabilirsiniz:

```php
$this->callSilently('mail:send', [
    'user' => 1, '--queue' => 'default'
]);
```

<br>



## Sinyal Yönetimi (Signal Handling)

Bildiğiniz gibi, işletim sistemleri çalışan işlemlere **sinyaller** gönderebilir.
Örneğin, `SIGTERM` sinyali bir programın sonlandırılması için gönderilir.
Artisan komutlarınızda bu sinyalleri dinleyip özel işlemler yapmak isterseniz `trap` metodunu kullanabilirsiniz:

```php
/**
 * Execute the console command.
 */
public function handle(): void
{
    $this->trap(SIGTERM, fn () => $this->shouldKeepRunning = false);
 
    while ($this->shouldKeepRunning) {
        // ...
    }
}
```

Birden fazla sinyali aynı anda dinlemek için `trap` metoduna bir dizi sinyal verebilirsiniz:

```php
$this->trap([SIGTERM, SIGQUIT], function (int $signal) {
    $this->shouldKeepRunning = false;
 
    dump($signal); // SIGTERM / SIGQUIT
});
```

<br>



## Stub Özelleştirme (Stub Customization)

Artisan’ın `make` komutları, **controller**, **job**, **migration**, ve **test** gibi çeşitli sınıflar oluşturmak için kullanılır.
Bu sınıflar, girdilerinize göre doldurulan **stub** dosyalarından üretilir.

Ancak, Artisan tarafından oluşturulan bu dosyalarda küçük değişiklikler yapmak isteyebilirsiniz.
Bunu yapmak için `stub:publish` komutunu kullanarak en yaygın stub dosyalarını uygulamanıza yayınlayabilirsiniz:

```bash
php artisan stub:publish
```

Yayınlanan stub dosyaları, uygulamanızın kök dizininde **stubs/** klasörü altında bulunur.
Bu dosyalarda yaptığınız tüm değişiklikler, Artisan’ın ilgili `make` komutları çalıştırıldığında oluşturulan dosyalara yansıtılır.

<br>



## Olaylar (Events)

Artisan, komutlar çalıştırılırken üç farklı olay (event) tetikler:

1. **Illuminate\Console\Events\ArtisanStarting** — Artisan çalışmaya başladığında tetiklenir.
2. **Illuminate\Console\Events\CommandStarting** — Bir komut çalıştırılmadan hemen önce tetiklenir.
3. **Illuminate\Console\Events\CommandFinished** — Bir komutun yürütülmesi tamamlandığında tetiklenir.

Bu olayları dinleyerek komut çalıştırma sürecine özel davranışlar ekleyebilirsiniz.

