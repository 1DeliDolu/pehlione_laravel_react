# Yapılandırma

## Giriş

Laravel framework’üne ait tüm yapılandırma dosyaları **config** dizininde saklanır. Her seçenek belgelenmiştir, bu nedenle dosyaları inceleyebilir ve size sunulan seçeneklerle tanışabilirsiniz.

Bu yapılandırma dosyaları, veritabanı bağlantı bilgilerinizi, mail sunucusu bilgilerinizi ve uygulama URL’iniz ile şifreleme anahtarınız gibi çeşitli temel yapılandırma değerlerini ayarlamanıza olanak tanır.

---

## about Komutu

Laravel, **about** Artisan komutu aracılığıyla uygulamanızın yapılandırması, sürücüleri ve çalışma ortamı hakkında genel bir görünüm sunabilir:

```bash
php artisan about
```

Yalnızca uygulama genel görünüm çıktısının belirli bir bölümüne ilgi duyuyorsanız, `--only` seçeneğini kullanarak o bölümü filtreleyebilirsiniz:

```bash
php artisan about --only=environment
```

Veya belirli bir yapılandırma dosyasının değerlerini ayrıntılı olarak incelemek için **config:show** Artisan komutunu kullanabilirsiniz:

```bash
php artisan config:show database
```

---

## Ortam Yapılandırması

Uygulamanın çalıştığı ortama göre farklı yapılandırma değerlerine sahip olmak genellikle faydalıdır. Örneğin, yerel ortamda üretim sunucusundan farklı bir cache sürücüsü kullanmak isteyebilirsiniz.

Bu işlemi kolaylaştırmak için Laravel, **DotEnv PHP** kütüphanesini kullanır. Yeni bir Laravel kurulumunda, uygulamanızın kök dizininde birçok yaygın ortam değişkenini tanımlayan bir **.env.example** dosyası bulunur. Laravel kurulumu sırasında bu dosya otomatik olarak **.env** dosyasına kopyalanır.

Laravel’in varsayılan `.env` dosyası, uygulamanızın yerel olarak mı yoksa üretim web sunucusunda mı çalıştığına bağlı olarak değişebilen bazı yaygın yapılandırma değerleri içerir. Bu değerler, Laravel’in **env** fonksiyonu kullanılarak **config** dizinindeki yapılandırma dosyaları tarafından okunur.

Bir ekip ile geliştiriyorsanız, `.env.example` dosyasını uygulamanızla birlikte dahil etmeye ve güncellemeye devam etmek isteyebilirsiniz. Örnek yapılandırma dosyasına yer tutucu (placeholder) değerler koyarak, ekibinizdeki diğer geliştiricilerin uygulamanızın çalışması için hangi ortam değişkenlerine ihtiyaç duyulduğunu açıkça görmesini sağlayabilirsiniz.

`.env` dosyanızdaki herhangi bir değişken, sunucu düzeyinde veya sistem düzeyinde tanımlanmış dış ortam değişkenleri tarafından geçersiz kılınabilir.

---

## Ortam Dosyası Güvenliği

`.env` dosyanız uygulamanızın kaynak kontrolüne dahil edilmemelidir, çünkü uygulamanızı kullanan her geliştirici / sunucu farklı bir ortam yapılandırmasına ihtiyaç duyabilir. Ayrıca, bir saldırgan kaynak kontrol deposuna erişirse bu durum bir güvenlik riski oluşturur, çünkü hassas kimlik bilgileri açığa çıkabilir.

Ancak, Laravel’in yerleşik ortam şifreleme özelliğini kullanarak ortam dosyanızı şifrelemeniz mümkündür. Şifrelenmiş ortam dosyaları kaynak kontrolüne güvenle dahil edilebilir.

---

## Ek Ortam Dosyaları

Laravel, uygulamanızın ortam değişkenlerini yüklemeden önce bir **APP_ENV** ortam değişkeninin harici olarak sağlanıp sağlanmadığını veya `--env` CLI argümanının belirtilip belirtilmediğini belirler. Eğer belirtilmişse, Laravel `.env.[APP_ENV]` adlı bir dosyayı yüklemeye çalışır. Bu dosya mevcut değilse, varsayılan `.env` dosyası yüklenir.

---

## Ortam Değişkeni Türleri

`.env` dosyalarındaki tüm değişkenler genellikle **string** olarak ayrıştırılır. Ancak, **env()** fonksiyonundan daha geniş bir tür yelpazesi döndürebilmek için bazı ayrılmış değerler oluşturulmuştur:

| .env Değeri | env() Değeri |
| ----------- | ------------ |
| true        | (bool) true  |
| (true)      | (bool) true  |
| false       | (bool) false |
| (false)     | (bool) false |
| empty       | (string) ''  |
| (empty)     | (string) ''  |
| null        | (null) null  |
| (null)      | (null) null  |

Eğer boşluk içeren bir ortam değişkeni değeri tanımlamanız gerekiyorsa, değeri çift tırnak içine alabilirsiniz:

```env
APP_NAME="My Application"
```

---

## Ortam Yapılandırmasını Alma

`.env` dosyasında listelenen tüm değişkenler, uygulamanız bir istek aldığında **$_ENV** PHP süper-global değişkenine yüklenir. Ancak, bu değişkenlerdeki değerleri yapılandırma dosyalarınızdan almak için **env** fonksiyonunu kullanabilirsiniz.

Aslında, Laravel yapılandırma dosyalarını incelerseniz, birçok seçeneğin halihazırda bu fonksiyonu kullandığını fark edeceksiniz:

```php
'debug' => (bool) env('APP_DEBUG', false),
```

**env** fonksiyonuna geçirilen ikinci değer, “varsayılan değerdir”. Belirtilen anahtar için herhangi bir ortam değişkeni yoksa bu değer döndürülür.

# Ortam Yapılandırmasını Alma

`.env` dosyasında listelenen tüm değişkenler, uygulamanız bir istek aldığında **$_ENV** PHP süper globaline yüklenir. Ancak, bu değişkenlerden değerleri yapılandırma dosyalarınızda almak için **env** fonksiyonunu kullanabilirsiniz.

Aslında, Laravel yapılandırma dosyalarını incelerseniz, birçok seçeneğin halihazırda bu fonksiyonu kullandığını fark edersiniz:

```php
'debug' => (bool) env('APP_DEBUG', false),
```

**env** fonksiyonuna verilen ikinci değer “varsayılan değer”dir. Bu değer, belirtilen anahtar için bir ortam değişkeni yoksa döndürülür.

---

## Geçerli Ortamı Belirleme

Geçerli uygulama ortamı, `.env` dosyanızdaki **APP_ENV** değişkeni aracılığıyla belirlenir. Bu değere **App** facade’ının **environment** yöntemiyle erişebilirsiniz:

```php
use Illuminate\Support\Facades\App;

$environment = App::environment();
```

Ayrıca, ortamın belirli bir değere eşleşip eşleşmediğini kontrol etmek için **environment** yöntemine argümanlar da geçebilirsiniz. Yöntem, ortam verilen değerlerden biriyle eşleşirse **true** döndürecektir:

```php
if (App::environment('local')) {
    // Ortam local
}

if (App::environment(['local', 'staging'])) {
    // Ortam local veya staging...
}
```

Geçerli uygulama ortamı tespiti, sunucu düzeyinde tanımlanan bir **APP_ENV** ortam değişkeniyle geçersiz kılınabilir.

---

## Ortam Dosyalarının Şifrelenmesi

Şifrelenmemiş ortam dosyaları hiçbir zaman kaynak kontrolünde saklanmamalıdır. Ancak Laravel, ortam dosyalarınızı şifrelemenize olanak tanır, böylece bunları uygulamanızın geri kalanıyla birlikte güvenli bir şekilde kaynak kontrolüne ekleyebilirsiniz.

### Şifreleme

Bir ortam dosyasını şifrelemek için **env:encrypt** komutunu kullanabilirsiniz:

```bash
php artisan env:encrypt
```

Bu komut çalıştırıldığında, `.env` dosyanızı şifreler ve şifrelenmiş içeriği `.env.encrypted` dosyasına yerleştirir. Şifre çözme anahtarı komut çıktısında gösterilir ve güvenli bir parola yöneticisinde saklanmalıdır.

Kendi şifreleme anahtarınızı sağlamak isterseniz, komutu çağırırken **--key** seçeneğini kullanabilirsiniz:

```bash
php artisan env:encrypt --key=3UVsEgGVK36XN82KKeyLFMhvosbZN1aF
```

Sağladığınız anahtarın uzunluğu, kullanılan şifreleme algoritmasının gerektirdiği anahtar uzunluğuyla eşleşmelidir. Laravel varsayılan olarak **AES-256-CBC** şifreleme algoritmasını kullanır ve bu, 32 karakterlik bir anahtar gerektirir.
Laravel’in şifreleyicisi tarafından desteklenen herhangi bir algoritmayı, komutu çağırırken **--cipher** seçeneğiyle kullanabilirsiniz.

Uygulamanızda birden fazla ortam dosyası varsa (örneğin `.env` ve `.env.staging`), **--env** seçeneğiyle hangi ortam dosyasının şifreleneceğini belirtebilirsiniz:

```bash
php artisan env:encrypt --env=staging
```

---

### Şifre Çözme

Bir ortam dosyasının şifresini çözmek için **env:decrypt** komutunu kullanabilirsiniz. Bu komut bir şifre çözme anahtarı gerektirir; Laravel bu anahtarı **LARAVEL_ENV_ENCRYPTION_KEY** ortam değişkeninden alır:

```bash
php artisan env:decrypt
```

Veya anahtarı doğrudan komuta **--key** seçeneğiyle verebilirsiniz:

```bash
php artisan env:decrypt --key=3UVsEgGVK36XN82KKeyLFMhvosbZN1aF
```

Bu komut çalıştırıldığında, Laravel `.env.encrypted` dosyasının içeriğini çözer ve çözülen verileri `.env` dosyasına yerleştirir.

Farklı bir şifreleme algoritması kullanmak isterseniz, **--cipher** seçeneğini ekleyebilirsiniz:

```bash
php artisan env:decrypt --key=qUWuNRdfuImXcKxZ --cipher=AES-128-CBC
```

Uygulamanızda birden fazla ortam dosyası varsa (örneğin `.env` ve `.env.staging`), hangi dosyanın çözüleceğini **--env** seçeneğiyle belirtebilirsiniz:

```bash
php artisan env:decrypt --env=staging
```

Mevcut bir ortam dosyasının üzerine yazmak isterseniz, **--force** seçeneğini ekleyebilirsiniz:

```bash
php artisan env:decrypt --force
```

---

## Yapılandırma Değerlerine Erişim

Uygulamanızın herhangi bir yerinden yapılandırma değerlerine **Config** facade’ı veya global **config** fonksiyonu aracılığıyla kolayca erişebilirsiniz.
Yapılandırma değerlerine, dosya adını ve erişmek istediğiniz seçeneği içeren **“dot” (nokta)** sözdizimiyle erişilir.
Ayrıca, yapılandırma değeri yoksa döndürülecek bir varsayılan değer de belirtilebilir:

```php
use Illuminate\Support\Facades\Config;

$value = Config::get('app.timezone');

$value = config('app.timezone');

// Yapılandırma değeri yoksa varsayılan değer döndürülür...
$value = config('app.timezone', 'Asia/Seoul');
```

Çalışma zamanında yapılandırma değerlerini ayarlamak için **Config** facade’ının **set** metodunu çağırabilir veya **config** fonksiyonuna bir dizi geçebilirsiniz:

```php
Config::set('app.timezone', 'America/Chicago');

config(['app.timezone' => 'America/Chicago']);
```

Statik analiz desteği sağlamak için, **Config** facade’ı ayrıca tür bazlı yapılandırma alma yöntemleri de sunar. Eğer alınan değer beklenen türle eşleşmezse bir hata fırlatılır:

```php
Config::string('config-key');
Config::integer('config-key');
Config::float('config-key');
Config::boolean('config-key');
Config::array('config-key');
Config::collection('config-key');
```

---

## Yapılandırma Önbellekleme

Uygulamanıza hız kazandırmak için, tüm yapılandırma dosyalarınızı tek bir dosyada önbelleğe almak amacıyla **config:cache** Artisan komutunu çalıştırmalısınız.
Bu komut, uygulamanızdaki tüm yapılandırma seçeneklerini tek bir dosyada birleştirir ve framework’ün bu dosyayı hızlıca yüklemesini sağlar.

**config:cache** komutu genellikle üretim ortamına dağıtım sürecinizin bir parçası olarak çalıştırılmalıdır.
Yerel geliştirme sırasında bu komutu çalıştırmamalısınız, çünkü uygulamanızın geliştirilmesi süresince yapılandırma seçenekleri sık sık değiştirilecektir.

Yapılandırma önbelleğe alındıktan sonra, framework `.env` dosyasını artık istekler veya Artisan komutları sırasında yüklemez. Bu nedenle, **env** fonksiyonu yalnızca dış (sistem düzeyinde) ortam değişkenlerini döndürür.

Bu sebeple, **env** fonksiyonunu yalnızca uygulamanızın yapılandırma dosyaları içinde çağırdığınızdan emin olmalısınız. Laravel’in varsayılan yapılandırma dosyalarına bakarak bunun birçok örneğini görebilirsiniz.
Yapılandırma değerlerine, yukarıda açıklanan **config** fonksiyonu aracılığıyla uygulamanızın herhangi bir yerinden erişebilirsiniz.

Önbelleğe alınmış yapılandırmayı temizlemek için aşağıdaki komutu kullanabilirsiniz:

```bash
php artisan config:clear
```

Dağıtım sürecinizde **config:cache** komutunu çalıştırıyorsanız, **env** fonksiyonunu yalnızca yapılandırma dosyalarınızda çağırdığınızdan emin olun. Çünkü yapılandırma önbelleğe alındıktan sonra `.env` dosyası yüklenmeyecek ve **env** fonksiyonu yalnızca dış sistem ortam değişkenlerini döndürecektir.

# Yapılandırma Yayınlama

Laravel’in yapılandırma dosyalarının çoğu zaten uygulamanızın **config** dizininde yayınlanmıştır; ancak, **cors.php** ve **view.php** gibi bazı yapılandırma dosyaları varsayılan olarak yayınlanmaz, çünkü çoğu uygulamanın bu dosyaları değiştirmesi gerekmez.

Yine de, varsayılan olarak yayınlanmayan herhangi bir yapılandırma dosyasını yayınlamak için **config:publish** Artisan komutunu kullanabilirsiniz:

```bash
php artisan config:publish
```

Tüm yapılandırma dosyalarını yayınlamak için:

```bash
php artisan config:publish --all
```

---

## Hata Ayıklama Modu (Debug Mode)

`config/app.php` yapılandırma dosyanızdaki **debug** seçeneği, bir hata hakkında kullanıcıya ne kadar bilgi gösterileceğini belirler. Varsayılan olarak, bu seçenek `.env` dosyanızda depolanan **APP_DEBUG** ortam değişkeninin değerine göre ayarlanır.

Yerel geliştirme ortamı için, **APP_DEBUG** ortam değişkenini `true` olarak ayarlamalısınız.
Üretim ortamında ise bu değerin **her zaman false** olması gerekir.
Bu değişken üretim ortamında `true` olarak ayarlanırsa, uygulamanızın son kullanıcılarına hassas yapılandırma bilgilerini sızdırma riski oluşur.

---

## Bakım Modu (Maintenance Mode)

Uygulamanız bakım modundayken, gelen tüm istekler için özel bir görünüm (view) gösterilir. Bu, uygulamanızı güncellerken veya bakım yaparken “devre dışı bırakmanızı” kolaylaştırır.
Laravel’in varsayılan middleware yığını, bir bakım modu denetimi içerir. Eğer uygulama bakım modundaysa, **Symfony\Component\HttpKernel\Exception\HttpException** örneği oluşturulur ve **503** durum kodu döndürülür.

Bakım modunu etkinleştirmek için şu komutu çalıştırın:

```bash
php artisan down
```

Tüm bakım modu yanıtlarına **Refresh** HTTP başlığının eklenmesini istiyorsanız, **--refresh** seçeneğini belirtebilirsiniz. Bu başlık, tarayıcıya sayfayı belirttiğiniz saniye sayısı sonra otomatik olarak yenilemesini söyler:

```bash
php artisan down --refresh=15
```

Ayrıca, **--retry** seçeneğiyle **Retry-After** HTTP başlığı için bir değer belirtebilirsiniz, ancak tarayıcılar genellikle bu başlığı dikkate almaz:

```bash
php artisan down --retry=60
```

---

### Bakım Modunu Atlatma (Bypassing Maintenance Mode)

Bakım modunun gizli bir token aracılığıyla atlatılmasına izin vermek için **--secret** seçeneğini kullanabilirsiniz:

```bash
php artisan down --secret="1630542a-246b-4b66-afa1-dd72a4c43515"
```

Uygulamayı bakım moduna aldıktan sonra, token ile eşleşen URL’ye gittiğinizde Laravel, tarayıcınıza bakım modunu atlayan bir cookie verir:

```
https://example.com/1630542a-246b-4b66-afa1-dd72a4c43515
```

Laravel’in sizin için bu gizli token’ı oluşturmasını isterseniz, **--with-secret** seçeneğini kullanabilirsiniz. Uygulama bakım moduna alındıktan sonra gizli token size gösterilir:

```bash
php artisan down --with-secret
```

Bu gizli rotaya eriştiğinizde, uygulamanın `/` rotasına yönlendirilirsiniz. Cookie tarayıcınıza eklendikten sonra, uygulamayı sanki bakım modunda değilmiş gibi normal şekilde gezebilirsiniz.

Bakım modu gizliniz genellikle **alfanümerik karakterlerden** ve isteğe bağlı olarak **tirelerden (-)** oluşmalıdır. URL’lerde özel anlam taşıyan `?` veya `&` gibi karakterlerden kaçınmalısınız.

---

### Birden Fazla Sunucuda Bakım Modu

Varsayılan olarak, Laravel uygulamanızın bakım modunda olup olmadığını dosya tabanlı bir sistemle belirler. Bu, `php artisan down` komutunun uygulamanızın barındırıldığı her sunucuda çalıştırılması gerektiği anlamına gelir.

Alternatif olarak, Laravel bakım modunu yönetmek için önbellek tabanlı bir yöntem sunar.
Bu yöntemle, yalnızca **bir** sunucuda `php artisan down` komutunu çalıştırmanız yeterlidir.
Bu yöntemi kullanmak için uygulamanızın `.env` dosyasındaki bakım modu değişkenlerini aşağıdaki gibi düzenleyin.
Tüm sunucuların erişebildiği bir cache store seçmelisiniz. Bu, bakım modu durumunun her sunucuda tutarlı bir şekilde korunmasını sağlar:

```env
APP_MAINTENANCE_DRIVER=cache
APP_MAINTENANCE_STORE=database
```

---

### Bakım Modu Görünümünü Önceden Render Etme

Dağıtım sırasında `php artisan down` komutunu kullanıyorsanız, kullanıcılarınız uygulamaya erişmeye çalışırken Composer bağımlılıkları veya altyapı bileşenleri güncellenirken hatalarla karşılaşabilir.
Bunun nedeni, Laravel framework’ünün büyük bir kısmının uygulamanızın bakım modunda olduğunu anlamak ve bakım modu görünümünü oluşturmak için yüklenmesi gerekmesidir.

Bu nedenle, Laravel isteğin en başında döndürülecek bir **önceden render edilmiş** bakım modu görünümüne izin verir.
Bu görünüm, uygulamanızın hiçbir bağımlılığı yüklenmeden önce gösterilir.
Kendi şablonunuzu önceden render etmek için `--render` seçeneğini kullanabilirsiniz:

```bash
php artisan down --render="errors::503"
```

---

### Bakım Modu İsteklerini Yönlendirme

Bakım modundayken, Laravel kullanıcıların erişmeye çalıştığı tüm uygulama URL’leri için bakım modu görünümünü gösterir.
Ancak, isterseniz Laravel’e tüm istekleri belirli bir URL’ye yönlendirmesini söyleyebilirsiniz.
Bu, **--redirect** seçeneğiyle yapılır. Örneğin, tüm istekleri `/` URI’sine yönlendirmek için:

```bash
php artisan down --redirect=/
```

---

### Bakım Modunu Devre Dışı Bırakma

Bakım modunu devre dışı bırakmak için şu komutu kullanın:

```bash
php artisan up
```

Varsayılan bakım modu şablonunu özelleştirmek isterseniz, kendi şablonunuzu **resources/views/errors/503.blade.php** dosyasında tanımlayabilirsiniz.

---

### Bakım Modu ve Kuyruklar (Queues)

Uygulamanız bakım modundayken, **queued job**’lar işlenmez.
Uygulama bakım modundan çıktığında işler normal şekilde işlenmeye devam eder.

---

### Bakım Moduna Alternatifler

Bakım modu, uygulamanızın birkaç saniyelik bir kesinti yaşamasını gerektirdiğinden, **Laravel Cloud** gibi tam yönetilen bir platformda uygulamalarınızı çalıştırmayı düşünün.
Böylece Laravel ile **sıfır kesintiyle dağıtım (zero-downtime deployment)** gerçekleştirebilirsiniz.
