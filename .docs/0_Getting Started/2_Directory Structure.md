### Dizin Yapısı

#### Giriş

Varsayılan Laravel uygulama yapısı, hem büyük hem de küçük uygulamalar için harika bir başlangıç noktası sağlamayı amaçlar. Ancak uygulamanızı dilediğiniz gibi düzenlemekte özgürsünüz. Laravel, Composer sınıfı otomatik yükleyebildiği sürece herhangi bir sınıfın nerede bulunacağı konusunda neredeyse hiçbir kısıtlama getirmez.

---

### Kök Dizin

#### App Dizini

**app** dizini, uygulamanızın çekirdek kodlarını içerir. Bu dizini yakında daha ayrıntılı olarak inceleyeceğiz; ancak, uygulamanızdaki sınıfların neredeyse tamamı bu dizinde bulunacaktır.

#### Bootstrap Dizini

**bootstrap** dizini, framework’ü başlatan **app.php** dosyasını içerir. Bu dizin ayrıca, performans optimizasyonu için framework tarafından oluşturulan dosyaları (örneğin, route ve service cache dosyaları gibi) barındıran bir **cache** dizini içerir.

#### Config Dizini

**config** dizini, adından da anlaşılacağı üzere, uygulamanızın tüm yapılandırma dosyalarını içerir. Bu dosyaların tamamını gözden geçirip size sunulan seçeneklerle tanışmanız iyi bir fikirdir.

#### Database Dizini

**database** dizini, veritabanı migration’larını, model factory’lerini ve seed’leri içerir. İsterseniz bu dizini bir **SQLite** veritabanı tutmak için de kullanabilirsiniz.

#### Public Dizini

**public** dizini, uygulamanıza gelen tüm isteklerin giriş noktası olan **index.php** dosyasını içerir ve autoloading yapılandırmasını yapar. Ayrıca bu dizin, **image**, **JavaScript** ve **CSS** gibi varlıklarınızı barındırır.

#### Resources Dizini

**resources** dizini, **view** dosyalarınızı ve derlenmemiş ham varlıklarınızı (örneğin CSS veya JavaScript dosyaları) içerir.

#### Routes Dizini

**routes** dizini, uygulamanızın tüm route tanımlarını içerir. Varsayılan olarak Laravel, iki route dosyasıyla birlikte gelir: **web.php** ve **console.php**.

**web.php** dosyası, Laravel’in **web middleware** grubuna yerleştirdiği route’ları içerir. Bu grup, oturum durumu (session state), **CSRF protection** ve **cookie encryption** sağlar. Uygulamanız stateless bir RESTful API sunmuyorsa, tüm route’larınız büyük olasılıkla **web.php** dosyasında tanımlanacaktır.

**console.php** dosyası, tüm **closure-based console command**’lerinizi tanımlayabileceğiniz yerdir. Her closure, bir komut örneğine (command instance) bağlanarak komutun IO (girdi/çıktı) metodlarıyla etkileşim kurmanın basit bir yolunu sağlar. Bu dosya HTTP route’ları tanımlamasa da, uygulamanız için **console tabanlı giriş noktaları (route’lar)** tanımlar. Ayrıca bu dosyada görevleri zamanlayabilirsiniz.

İsteğe bağlı olarak, **install:api** ve **install:broadcasting** Artisan komutlarını kullanarak API route’ları (**api.php**) ve yayın kanalları (**channels.php**) için ek route dosyaları yükleyebilirsiniz.

**api.php** dosyası, stateless olacak şekilde tasarlanmış route’ları içerir, bu nedenle bu route’lar aracılığıyla uygulamaya gelen istekler **token** ile kimlik doğrulaması yapılacak şekilde tasarlanmıştır ve oturum durumuna erişemezler.

**channels.php** dosyası, uygulamanızın desteklediği tüm **event broadcasting channel**’larını kaydettiğiniz yerdir.

#### Storage Dizini

**storage** dizini, loglarınızı, derlenmiş **Blade template**’lerinizi, dosya tabanlı session’larınızı, dosya cache’lerinizi ve framework tarafından oluşturulan diğer dosyaları içerir. Bu dizin üç alt dizine ayrılmıştır: **app**, **framework** ve **logs**.

* **app** dizini, uygulamanız tarafından oluşturulan dosyaları saklamak için kullanılabilir.
* **framework** dizini, framework tarafından oluşturulan dosyaları ve cache’leri saklamak için kullanılır.
* **logs** dizini, uygulamanızın log dosyalarını içerir.

**storage/app/public** dizini, kullanıcılar tarafından oluşturulan ve genel erişime açık olması gereken (örneğin profil avatarları) dosyaları depolamak için kullanılabilir. Bu dizine işaret eden **public/storage** konumunda sembolik bir bağlantı (symbolic link) oluşturmalısınız. Bu bağlantıyı oluşturmak için şu Artisan komutunu kullanabilirsiniz:

```bash
php artisan storage:link
```
### Tests Dizini

**tests** dizini, otomatik testlerinizi içerir. Varsayılan olarak, **Pest** veya **PHPUnit** ile yazılmış birim testleri (unit tests) ve özellik testleri (feature tests) örnekleri sağlanır. Her test sınıfının sonu **Test** kelimesiyle bitmelidir.
Testlerinizi aşağıdaki komutlarla çalıştırabilirsiniz:

```bash
/vendor/bin/pest
/vendor/bin/phpunit
```

Ya da test sonuçlarının daha ayrıntılı ve görsel bir şekilde gösterilmesini isterseniz şu komutu kullanabilirsiniz:

```bash
php artisan test
```

---

### Vendor Dizini

**vendor** dizini, **Composer** bağımlılıklarını içerir.

---

### App Dizini

Uygulamanızın büyük bölümü **app** dizininde bulunur. Varsayılan olarak bu dizin **App** namespace’i altındadır ve **Composer** tarafından **PSR-4 autoloading standardı** ile otomatik olarak yüklenir.

Varsayılan olarak **app** dizini, **Http**, **Models** ve **Providers** dizinlerini içerir. Ancak zamanla, **make** Artisan komutlarını kullanarak sınıflar oluşturdukça bu dizin içinde başka dizinler de oluşacaktır.
Örneğin, **app/Console** dizini, bir komut sınıfı oluşturmak için **make:command** Artisan komutunu çalıştırana kadar mevcut olmayacaktır.

Hem **Console** hem de **Http** dizinleri aşağıda kendi bölümlerinde daha ayrıntılı olarak açıklanacaktır, ancak bu iki dizini uygulamanızın çekirdeğine bir API sağlayan yapılar olarak düşünebilirsiniz.
**HTTP protokolü** ve **CLI**, uygulamanızla etkileşim kurmanın iki farklı mekanizmasıdır; ancak uygulama mantığını doğrudan içermezler. Başka bir deyişle, bunlar uygulamanıza komut göndermenin iki farklı yoludur.

* **Console** dizini tüm **Artisan command**’lerinizi içerir.
* **Http** dizini ise **controller**, **middleware** ve **request** sınıflarınızı içerir.

**app** dizinindeki birçok sınıf **Artisan** komutlarıyla oluşturulabilir. Mevcut komutların listesini görmek için terminalde şu komutu çalıştırabilirsiniz:

```bash
php artisan list make
```

---

### Broadcasting Dizini

**Broadcasting** dizini, uygulamanızdaki tüm **broadcast channel** sınıflarını içerir. Bu sınıflar **make:channel** komutu ile oluşturulur.
Bu dizin varsayılan olarak mevcut değildir; ilk kanalınızı oluşturduğunuzda otomatik olarak oluşturulur.
Kanallar hakkında daha fazla bilgi edinmek için **event broadcasting** belgelerine göz atabilirsiniz.

---

### Console Dizini

**Console** dizini, uygulamanızdaki tüm özel **Artisan command**’leri içerir. Bu komutlar **make:command** komutu ile oluşturulabilir.

---

### Events Dizini

Bu dizin varsayılan olarak mevcut değildir, ancak **event:generate** veya **make:event** Artisan komutlarını çalıştırdığınızda oluşturulur.
**Events** dizini, **event** sınıflarını barındırır. Event’ler, uygulamanızın diğer bölümlerine belirli bir eylemin gerçekleştiğini bildirmek için kullanılır ve bu da esneklik ve bağımsızlık sağlar.

---

### Exceptions Dizini

**Exceptions** dizini, uygulamanızdaki tüm özel **exception** sınıflarını içerir. Bu exception’lar **make:exception** komutu ile oluşturulabilir.

---

### Http Dizini

**Http** dizini, **controller**, **middleware** ve **form request** sınıflarınızı içerir.
Uygulamanıza gelen istekleri işlemek için gerekli mantığın neredeyse tamamı bu dizinde bulunur.

---

### Jobs Dizini

Bu dizin varsayılan olarak mevcut değildir, ancak **make:job** Artisan komutunu çalıştırdığınızda oluşturulur.
**Jobs** dizini, uygulamanızın **queueable job**’larını barındırır.
Job’lar, uygulamanız tarafından sıraya alınabilir (queued) veya mevcut istek döngüsü içinde senkron şekilde çalıştırılabilir.
Mevcut istek sırasında senkron şekilde çalışan job’lara bazen “**command**” da denir, çünkü bunlar **command pattern**’ın bir uygulamasıdır.

---

### Listeners Dizini

Bu dizin varsayılan olarak mevcut değildir, ancak **event:generate** veya **make:listener** Artisan komutlarını çalıştırdığınızda oluşturulur.
**Listeners** dizini, event’lerinizi işleyen sınıfları içerir.
**Event listener**’lar bir event instance’ı alır ve event tetiklendiğinde belirli bir mantığı uygular.
Örneğin, bir **UserRegistered** event’i, **SendWelcomeEmail** listener’ı tarafından işlenebilir.

---

### Mail Dizini

Bu dizin varsayılan olarak mevcut değildir, ancak **make:mail** Artisan komutunu çalıştırdığınızda oluşturulur.
**Mail** dizini, uygulamanız tarafından gönderilen e-postaları temsil eden tüm sınıfları içerir.
**Mail object**’leri, bir e-postayı oluşturma mantığının tamamını tek, basit bir sınıf içinde kapsüllemeye (encapsulate) olanak tanır ve bu sınıflar **Mail::send** yöntemiyle gönderilebilir.


### Models Dizini

**Models** dizini, tüm **Eloquent model** sınıflarınızı içerir. Laravel ile birlikte gelen **Eloquent ORM**, veritabanınızla çalışmak için güzel ve basit bir **ActiveRecord** implementasyonu sunar.
Her veritabanı tablosunun, o tabloyla etkileşime geçmek için kullanılan karşılık gelen bir “Model” sınıfı vardır.
Model’ler, tablolarınızdaki verileri sorgulamanıza ve tabloya yeni kayıtlar eklemenize olanak tanır.

---

### Notifications Dizini

Bu dizin varsayılan olarak mevcut değildir, ancak **make:notification** Artisan komutunu çalıştırdığınızda oluşturulur.
**Notifications** dizini, uygulamanız tarafından gönderilen tüm **“transactional” notification**’ları içerir — örneğin, uygulamanız içinde gerçekleşen olaylar hakkında basit bildirimler.
Laravel’in **notification** özelliği, bildirimleri e-posta, **Slack**, **SMS** veya veritabanında saklama gibi çeşitli sürücüler (driver) üzerinden göndermeyi soyutlar (abstract).

---

### Policies Dizini

Bu dizin varsayılan olarak mevcut değildir, ancak **make:policy** Artisan komutunu çalıştırdığınızda oluşturulur.
**Policies** dizini, uygulamanızın **authorization policy** sınıflarını içerir.
Policy’ler, bir kullanıcının belirli bir kaynak üzerinde belirli bir eylemi gerçekleştirip gerçekleştiremeyeceğini belirlemek için kullanılır.

---

### Providers Dizini

**Providers** dizini, uygulamanızdaki tüm **service provider**’ları içerir.
Service provider’lar, **service container**’a servisler bağlayarak, event’leri kaydederek veya gelen istekler için uygulamanızı hazırlayan diğer görevleri yerine getirerek uygulamanızı başlatır (**bootstrap** eder).

Yeni bir Laravel uygulamasında bu dizin, varsayılan olarak **AppServiceProvider** dosyasını zaten içerir.
Gerektiğinde bu dizine kendi provider’larınızı eklemekte özgürsünüz.

---

### Rules Dizini

Bu dizin varsayılan olarak mevcut değildir, ancak **make:rule** Artisan komutunu çalıştırdığınızda oluşturulur.
**Rules** dizini, uygulamanız için özel **validation rule** (doğrulama kuralı) nesnelerini içerir.
**Rules**, karmaşık doğrulama mantığını basit bir nesne içinde kapsüllemek (encapsulate) için kullanılır.
Daha fazla bilgi için **validation** dokümantasyonuna göz atabilirsiniz.
