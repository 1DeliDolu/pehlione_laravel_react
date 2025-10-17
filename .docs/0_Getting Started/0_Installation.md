# Kurulum

## Laravel ile Tanışın

Laravel, ifade gücü yüksek, zarif bir sözdizimine sahip bir web uygulama framework’üdür. Bir web framework’ü, uygulamanızı oluşturmak için bir yapı ve başlangıç noktası sağlar, böylece biz detaylarla uğraşırken siz harika bir şey yaratmaya odaklanabilirsiniz.

Laravel, kapsamlı dependency injection, ifadeli bir veritabanı soyutlama katmanı, kuyruklar ve zamanlanmış işler, birim ve entegrasyon testleri gibi güçlü özellikler sunarken aynı zamanda harika bir geliştirici deneyimi sağlamaya çalışır.

İster PHP web framework’lerinde yeni olun, ister yılların deneyimine sahip olun, Laravel sizinle birlikte büyüyebilecek bir framework’tür. Sizi bir web geliştiricisi olarak ilk adımlarınızda destekleyecek veya uzmanlığınızı bir üst seviyeye taşımanız için size ivme kazandıracak. Neler inşa edeceğinizi görmek için sabırsızlanıyoruz.

## Neden Laravel?

Bir web uygulaması oluştururken kullanabileceğiniz çeşitli araçlar ve framework’ler vardır. Ancak biz, modern, full-stack web uygulamaları oluşturmak için Laravel’in en iyi seçenek olduğuna inanıyoruz.

### İlerleyici Bir Framework

Biz Laravel’i “ilerleyici” bir framework olarak adlandırmayı seviyoruz. Bununla, Laravel’in sizinle birlikte büyüdüğünü kastediyoruz. Web geliştirmeye yeni başlıyorsanız, Laravel’in kapsamlı dokümantasyonu, rehberleri ve video eğitimleri, bunalmadan temelleri öğrenmenize yardımcı olacaktır.

Kıdemli bir geliştiriciyseniz, Laravel size dependency injection, birim testleri, kuyruklar, gerçek zamanlı olaylar ve daha fazlası için sağlam araçlar sunar. Laravel, profesyonel web uygulamaları oluşturmak için ince ayarlanmış ve kurumsal iş yüklerini karşılamaya hazırdır.

### Ölçeklenebilir Bir Framework

Laravel inanılmaz derecede ölçeklenebilirdir. PHP’nin ölçeklemeye elverişli yapısı ve Laravel’in Redis gibi hızlı, dağıtılmış önbellek sistemlerini yerleşik olarak desteklemesi sayesinde, Laravel ile yatay ölçekleme çocuk oyuncağıdır. Aslında, Laravel uygulamaları ayda yüz milyonlarca isteği kolayca karşılayacak şekilde ölçeklenmiştir.

Aşırı ölçeklenmeye mi ihtiyacınız var? Laravel Cloud gibi platformlar, Laravel uygulamanızı neredeyse sınırsız bir ölçekte çalıştırmanıza olanak tanır.

### Topluluk Odaklı Bir Framework

Laravel, PHP ekosistemindeki en iyi paketleri birleştirerek mevcut en güçlü ve geliştirici dostu framework’ü sunar. Ayrıca, dünyanın dört bir yanından binlerce yetenekli geliştirici framework’e katkıda bulunmuştur. Kim bilir, belki siz de bir Laravel katkıcısı olursunuz.

## Bir Laravel Uygulaması Oluşturma

### PHP ve Laravel Installer Kurulumu

İlk Laravel uygulamanızı oluşturmadan önce, yerel makinenizde PHP, Composer ve Laravel installer’ın kurulu olduğundan emin olun. Ayrıca, uygulamanızın frontend varlıklarını derleyebilmek için Node ve NPM ya da Bun kurmanız gerekir.

Yerel makinenizde PHP ve Composer kurulu değilse, aşağıdaki komutlar macOS, Windows veya Linux üzerinde PHP, Composer ve Laravel installer’ı kuracaktır:

#### macOS

#### Windows PowerShell

#### Linux

```powershell
# Yönetici olarak çalıştırın...
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.4'))
```

Yukarıdaki komutlardan birini çalıştırdıktan sonra terminal oturumunuzu yeniden başlatmalısınız. php.new üzerinden yükledikten sonra PHP, Composer ve Laravel installer’ı güncellemek için, aynı komutu terminalinizde yeniden çalıştırabilirsiniz.

PHP ve Composer zaten kuruluysa, Laravel installer’ı Composer aracılığıyla yükleyebilirsiniz:

```bash
composer global require laravel/installer
```

Tam özellikli, grafik arayüzlü bir PHP yükleme ve yönetim deneyimi için Laravel Herd’a göz atın.

### Bir Uygulama Oluşturma

PHP, Composer ve Laravel installer’ı yükledikten sonra yeni bir Laravel uygulaması oluşturmaya hazırsınız. Laravel installer, tercih ettiğiniz test framework’ünü, veritabanını ve starter kit’i seçmenizi isteyecektir:

```bash
laravel new pehlione --react --pest --mysql
```

Uygulama oluşturulduktan sonra, Laravel’in yerel geliştirme sunucusunu, kuyruk işçisini ve Vite geliştirme sunucusunu dev Composer script’iyle başlatabilirsiniz:

```bash
cd pehlione # Enter your new project directory
npm install && npm run build
composer run dev
```

Geliştirme sunucusunu başlattıktan sonra, uygulamanız web tarayıcınızda şu adreste erişilebilir olacaktır:
[http://localhost:8000](http://localhost:8000)

Sonraki adımınız Laravel ekosistemine daha derin adımlar atmak olacak. Elbette, bir veritabanı yapılandırmak da isteyebilirsiniz.

Laravel uygulamanızı geliştirirken hızlı bir başlangıç yapmak istiyorsanız, starter kit’lerimizden birini kullanmayı düşünün. Laravel’in starter kit’leri, yeni Laravel uygulamanız için backend ve frontend kimlik doğrulama iskeleti sağlar.

## İlk Yapılandırma

Laravel framework’üne ait tüm yapılandırma dosyaları `config` dizininde saklanır. Her seçenek belgelenmiştir, bu nedenle dosyaları inceleyebilir ve size sunulan seçeneklerle tanışabilirsiniz.

Laravel, varsayılan olarak kutudan çıktığı haliyle neredeyse hiçbir ek yapılandırma gerektirmez. Geliştirmeye başlamaya hazırsınız! Ancak, `config/app.php` dosyasını ve belgelerini gözden geçirmek isteyebilirsiniz. Bu dosya, uygulamanıza göre değiştirmek isteyebileceğiniz `url` ve `locale` gibi birkaç seçenek içerir.

### Ortam Tabanlı Yapılandırma

Laravel’in birçok yapılandırma seçeneği, uygulamanızın yerel makinenizde mi yoksa üretim web sunucusunda mı çalıştığına bağlı olarak değişebilir. Bu nedenle, birçok önemli yapılandırma değeri uygulamanızın kök dizininde bulunan `.env` dosyasında tanımlanır.

`.env` dosyanız uygulamanızın kaynak kontrolüne dahil edilmemelidir, çünkü uygulamanızı kullanan her geliştirici / sunucu farklı bir ortam yapılandırmasına ihtiyaç duyabilir. Ayrıca, bir saldırgan kaynak kontrol deposuna erişirse bu durum güvenlik riski oluşturur, çünkü hassas kimlik bilgileri açığa çıkabilir.

`.env` dosyası ve ortam tabanlı yapılandırma hakkında daha fazla bilgi için tam yapılandırma dokümantasyonuna göz atın.

# Veritabanları ve Migrationlar

Laravel uygulamanızı oluşturduğunuza göre, muhtemelen bazı verileri bir veritabanında saklamak isteyeceksiniz. Varsayılan olarak, uygulamanızın `.env` yapılandırma dosyası Laravel’in bir SQLite veritabanı ile etkileşime gireceğini belirtir.

Uygulama oluşturulurken, Laravel sizin için `database/database.sqlite` dosyasını oluşturur ve uygulamanın veritabanı tablolarını oluşturmak için gerekli migrationları çalıştırır.

Eğer MySQL veya PostgreSQL gibi başka bir veritabanı sürücüsü kullanmayı tercih ederseniz, `.env` yapılandırma dosyanızı uygun veritabanını kullanacak şekilde güncelleyebilirsiniz. Örneğin, MySQL kullanmak istiyorsanız, `.env` dosyanızdaki `DB_*` değişkenlerini şu şekilde ayarlayın:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

SQLite dışındaki bir veritabanını kullanmayı seçerseniz, veritabanını oluşturmanız ve uygulamanızın veritabanı migrationlarını çalıştırmanız gerekir:

```bash
php artisan migrate
```

macOS veya Windows üzerinde geliştiriyor ve MySQL, PostgreSQL veya Redis’i yerel olarak yüklemeniz gerekiyorsa, **Herd Pro** veya **DBngin** kullanmayı düşünün.

---

## Dizin Yapılandırması
<br>


Laravel her zaman web sunucunuz için yapılandırılmış “web dizininin” kökünden servis edilmelidir. Laravel uygulamasını “web dizininin” bir alt dizininden çalıştırmaya çalışmamalısınız. Bunu yapmak, uygulamanızdaki hassas dosyaların açığa çıkmasına neden olabilir.

<br>



---

<br>



## Herd Kullanarak Kurulum
<br>


**Laravel Herd**, macOS ve Windows için son derece hızlı, yerel bir Laravel ve PHP geliştirme ortamıdır. Herd, PHP ve Nginx dahil olmak üzere Laravel geliştirmeye başlamak için ihtiyaç duyduğunuz her şeyi içerir.

Herd’ü yükledikten sonra Laravel ile geliştirmeye başlamaya hazırsınız. Herd, şu komut satırı araçlarını içerir: `php`, `composer`, `laravel`, `expose`, `node`, `npm` ve `nvm`.

**Herd Pro**, Herd’e ek olarak yerel **MySQL**, **Postgres** ve **Redis** veritabanları oluşturma ve yönetme yeteneği, yerel e-posta görüntüleme ve log izleme gibi ek güçlü özellikler kazandırır.

<br>



---

<br>



### macOS Üzerinde Herd

macOS üzerinde geliştiriyorsanız, Herd yükleyicisini Herd web sitesinden indirebilirsiniz. Yükleyici, PHP’nin en son sürümünü otomatik olarak indirir ve Mac’inizi her zaman arka planda Nginx çalışacak şekilde yapılandırır.

macOS için Herd, “park edilmiş” dizinleri desteklemek için **dnsmasq** kullanır. Park edilmiş bir dizindeki herhangi bir Laravel uygulaması, Herd tarafından otomatik olarak servis edilir. Varsayılan olarak Herd, `~/Herd` dizininde bir park edilmiş dizin oluşturur ve bu dizindeki herhangi bir Laravel uygulamasına, dizin adını kullanarak `.test` alan adı üzerinden erişebilirsiniz.

Herd’ü yükledikten sonra yeni bir Laravel uygulaması oluşturmanın en hızlı yolu, Herd ile birlikte gelen Laravel CLI’yi kullanmaktır:

```bash
cd ~/Herd
laravel new pehlione
cd pehlione
herd open
```

Elbette, sistem tepsisindeki Herd menüsünden açılabilen Herd’in kullanıcı arayüzü aracılığıyla park edilmiş dizinlerinizi ve diğer PHP ayarlarını her zaman yönetebilirsiniz.

Herd hakkında daha fazla bilgi edinmek için **Herd dokümantasyonuna** göz atabilirsiniz.

---

### Windows Üzerinde Herd

Windows için Herd yükleyicisini Herd web sitesinden indirebilirsiniz. Kurulum tamamlandıktan sonra Herd’ü başlatarak ilk yapılandırma sürecini tamamlayabilir ve Herd kullanıcı arayüzüne ilk kez erişebilirsiniz.

Herd UI’a, sistem tepsisindeki Herd simgesine **sol tıklayarak** erişebilirsiniz. **Sağ tıklamak**, günlük olarak ihtiyaç duyduğunuz tüm araçlara erişim sağlayan hızlı menüyü açar.

Kurulum sırasında Herd, ev dizininizde `%USERPROFILE%\Herd` konumunda bir “park edilmiş” dizin oluşturur. Bu dizindeki herhangi bir Laravel uygulaması Herd tarafından otomatik olarak servis edilir ve dizin adını kullanarak `.test` alan adı üzerinden erişilebilir.

Herd’ü yükledikten sonra yeni bir Laravel uygulaması oluşturmanın en hızlı yolu, Herd ile birlikte gelen Laravel CLI’yi kullanmaktır. Başlamak için PowerShell’i açın ve şu komutları çalıştırın:

```powershell
cd ~\Herd
laravel new pehlione
cd pehlione
herd open
```

Windows için Herd hakkında daha fazla bilgi edinmek için **Herd dokümantasyonuna** göz atabilirsiniz.

---

## IDE Desteği

Laravel uygulamaları geliştirirken dilediğiniz kod düzenleyicisini kullanabilirsiniz. Hafif ve genişletilebilir editörler arıyorsanız, **VS Code** veya **Cursor** ile birlikte resmi **Laravel VS Code Extension**, sözdizimi vurgulama, snippet’ler, Artisan komut entegrasyonu ve **Eloquent model**, **route**, **middleware**, **asset**, **config** ve **Inertia.js** için akıllı otomatik tamamlama gibi özelliklerle mükemmel bir Laravel desteği sunar.

Laravel için kapsamlı ve güçlü destek arıyorsanız, bir **JetBrains IDE’si olan PhpStorm**’a göz atın. **Laravel Idea** eklentisiyle birlikte, **Laravel Pint**, **Pest**, **Larastan** ve daha fazlası dahil olmak üzere Laravel ve ekosistemine tam destek sağlar. Laravel Idea’nın framework desteği; **Blade template’leri**, **Eloquent model** otomatik tamamlama, **route**, **view**, **çeviri** ve **bileşen** desteği ile güçlü kod üretimi ve Laravel projeleri arasında gezinme yetenekleri içerir.

Bulut tabanlı bir geliştirme deneyimi arayanlar için **Firebase Studio**, Laravel ile doğrudan tarayıcınızda geliştirmeye anında erişim sağlar. Herhangi bir kurulum gerektirmeden, Firebase Studio sayesinde herhangi bir cihazdan Laravel uygulamaları oluşturmaya hemen başlayabilirsiniz.

---

## Laravel ve Yapay Zeka

**Laravel Boost**, AI kodlama ajanları ile Laravel uygulamaları arasındaki boşluğu kapatan güçlü bir araçtır. Boost, AI ajanlarına Laravel’e özel bağlam, araçlar ve yönergeler sağlar; böylece ajanlar, Laravel kurallarına uygun, sürüme özgü, daha doğru kod üretebilirler.

Boost’u Laravel uygulamanıza yüklediğinizde, AI ajanları şu yeteneklere sahip 15’ten fazla özel araca erişim kazanır: hangi paketleri kullandığınızı bilme, veritabanınızı sorgulama, Laravel dokümantasyonunu arama, tarayıcı loglarını okuma, testler oluşturma ve **Tinker** aracılığıyla kod yürütme.

Ayrıca, Boost AI ajanlarına kurulu paket sürümlerinize özel olarak 17.000’den fazla vektörize edilmiş Laravel ekosistem dokümantasyonuna erişim sağlar. Bu, ajanların projenizin tam olarak kullandığı sürümlere yönelik rehberlik sunabileceği anlamına gelir.

Boost ayrıca, Laravel tarafından hazırlanmış AI yönergelerini içerir; bu yönergeler, ajanların framework kurallarına uymasına, uygun testler yazmasına ve Laravel kodu oluştururken yaygın hatalardan kaçınmasına yardımcı olur.

# Laravel Boost Kurulumu

Boost, PHP 8.1 veya üzerini çalıştıran **Laravel 10, 11 ve 12** uygulamalarına kurulabilir. Başlamak için, Boost’u bir geliştirme bağımlılığı olarak yükleyin:

```bash
composer require laravel/boost --dev
```

Yükleme tamamlandıktan sonra, etkileşimli yükleyiciyi çalıştırın:

```bash
php artisan boost:install
```

Yükleyici, IDE’nizi ve AI ajanlarınızı otomatik olarak algılar ve projeniz için uygun olan özellikleri etkinleştirmenize olanak tanır. Boost, mevcut proje kurallarına saygı duyar ve varsayılan olarak katı stil kurallarını zorunlu kılmaz.

Boost hakkında daha fazla bilgi edinmek için **Laravel Boost deposuna GitHub’da** göz atın.

---

# Sonraki Adımlar

<br>



Artık Laravel uygulamanızı oluşturduğunuza göre, sırada ne öğrenmeniz gerektiğini merak ediyor olabilirsiniz. Öncelikle, Laravel’in nasıl çalıştığını anlamak için aşağıdaki dokümantasyonu okumanızı şiddetle tavsiye ederiz:

* Request Lifecycle
* Configuration
* Directory Structure
* Frontend
* Service Container
* Facades

Laravel’i nasıl kullanmak istediğiniz, yolculuğunuzun sonraki adımlarını da belirleyecektir. Laravel’i kullanmanın çeşitli yolları vardır ve aşağıda framework’ün iki temel kullanım durumunu inceleyeceğiz.

<br>


---

<br>



## Laravel: Full Stack Framework Olarak

Laravel bir **full stack framework** olarak hizmet edebilir. “Full stack” framework derken, Laravel’i uygulamanıza gelen istekleri yönlendirmek ve frontend’inizi **Blade template’leri** veya **Inertia** gibi tek sayfa uygulama hibrit teknolojileri aracılığıyla render etmek için kullanacağınızı kastediyoruz. Bu, Laravel framework’ünü kullanmanın en yaygın ve —bizce— en verimli yoludur.

Bu şekilde Laravel kullanmayı planlıyorsanız, **frontend development**, **routing**, **views** veya **Eloquent ORM** dokümantasyonumuza göz atmak isteyebilirsiniz. Ayrıca, **Livewire** ve **Inertia** gibi topluluk paketlerini öğrenmekle ilgilenebilirsiniz. Bu paketler, Laravel’i full stack bir framework olarak kullanırken tek sayfa JavaScript uygulamalarının sunduğu birçok arayüz avantajından yararlanmanızı sağlar.

Laravel’i full stack bir framework olarak kullanıyorsanız, ayrıca **Vite** kullanarak uygulamanızın CSS ve JavaScript dosyalarını nasıl derleyeceğinizi öğrenmenizi de şiddetle öneririz.

Uygulamanızı oluşturmaya hızlı bir başlangıç yapmak istiyorsanız, resmi **application starter kitlerimizden** birine göz atın.

---

<br>



## Laravel: API Backend Olarak

<br>



Laravel ayrıca bir **API backend** olarak da hizmet edebilir; örneğin, bir JavaScript tek sayfa uygulaması veya mobil uygulama için. Örneğin, Laravel’i **Next.js** uygulamanız için bir API backend olarak kullanabilirsiniz. Bu bağlamda, Laravel’i uygulamanız için kimlik doğrulama ve veri depolama / alma işlemlerini sağlamak amacıyla kullanabilir, aynı zamanda **kuyruklar**, **e-postalar**, **bildirimler** gibi güçlü Laravel servislerinden de yararlanabilirsiniz.

Bu şekilde Laravel kullanmayı planlıyorsanız, **routing**, **Laravel Sanctum** ve **Eloquent ORM** dokümantasyonlarımıza göz atmak isteyebilirsiniz.
