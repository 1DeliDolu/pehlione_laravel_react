# İstek Yaşam Döngüsü

## Giriş  
<br>
Gerçek dünyada herhangi bir aracı kullanırken, o aracın nasıl çalıştığını anlarsanız kendinizi daha güvende hissedersiniz. Uygulama geliştirme de farklı değildir. Geliştirme araçlarınızın nasıl çalıştığını anladığınızda, onları kullanırken daha rahat ve kendinizden emin hissedersiniz.  

Bu belgenin amacı, Laravel framework'ünün nasıl çalıştığına dair iyi bir genel bakış sağlamaktır. Framework’ün genel yapısını daha iyi tanıyarak, her şey daha az “büyülü” görünür ve uygulamalarınızı oluştururken daha fazla güven duyarsınız. Tüm terimleri hemen anlamazsanız üzülmeyin! Sadece neler olup bittiğine dair temel bir anlayış kazanmaya çalışın; diğer dokümantasyon bölümlerini keşfettikçe bilginiz artacaktır.  

---
<br>

## Yaşam Döngüsü Genel Bakış  

### İlk Adımlar  
<br>

Bir Laravel uygulamasına gelen tüm isteklerin giriş noktası `public/index.php` dosyasıdır. Web sunucusu (Apache / Nginx) yapılandırmanız, tüm istekleri bu dosyaya yönlendirir. `index.php` dosyası çok fazla kod içermez. Bunun yerine, framework’ün geri kalanını yüklemek için bir başlangıç noktasıdır.  

`index.php` dosyası Composer tarafından oluşturulan autoloader tanımını yükler ve ardından `bootstrap/app.php` dosyasından Laravel uygulamasının bir örneğini alır. Laravel’in kendisi tarafından yapılan ilk işlem, bir uygulama / service container örneği oluşturmaktır.  

<br>

### HTTP / Console Kernels 
<br> 
Sonraki adımda, gelen istek uygulama örneğinin `handleRequest` veya `handleCommand` metodları kullanılarak HTTP kernel veya console kernel’e gönderilir; bu, uygulamaya giren isteğin türüne bağlıdır. Bu iki kernel, tüm isteklerin geçtiği merkezi yerlerdir. Şimdilik, `Illuminate\Foundation\Http\Kernel` örneği olan HTTP kernel’e odaklanalım.  

HTTP kernel, isteğin yürütülmesinden önce çalıştırılacak bir dizi **bootstrapper** tanımlar. Bu bootstrapper’lar hata yönetimini yapılandırır, loglamayı ayarlar, uygulama ortamını algılar ve isteğin gerçekten işlenmesinden önce yapılması gereken diğer görevleri gerçekleştirir. Genellikle bu sınıflar, sizin endişelenmenize gerek olmayan Laravel’in dahili yapılandırmasını yönetir.  

HTTP kernel ayrıca isteği uygulamanın middleware yığınına aktarmaktan sorumludur. Bu middleware’lar HTTP oturumunu okuma ve yazma, uygulamanın bakım modunda olup olmadığını belirleme, CSRF token’ını doğrulama ve daha fazlasını yönetir. Bunlardan daha sonra detaylıca bahsedeceğiz.  

HTTP kernel’in `handle` metodunun imzası oldukça basittir: bir `Request` alır ve bir `Response` döndürür. Kernel’i, tüm uygulamanızı temsil eden büyük bir kara kutu olarak düşünün. Ona HTTP istekleri gönderin ve o da size HTTP yanıtları döndürsün.  

<br>

### Service Providers 
<br> 
Kernel’in en önemli başlatma adımlarından biri, uygulamanızın **service provider**’larını yüklemektir. Service provider’lar framework’ün veritabanı, queue, validation ve routing gibi çeşitli bileşenlerini başlatmaktan sorumludur.  

Laravel bu provider listesini döngüyle gezer ve her birini başlatır. Provider’lar başlatıldıktan sonra, tüm provider’larda `register` metodu çağrılır. Ardından, tüm provider’lar kayıt olduktan sonra, her bir provider’da `boot` metodu çağrılır. Bu, service provider’ların `boot` metodları yürütülmeden önce tüm container binding’lerinin kayıtlı ve kullanılabilir olmasını sağlar.  

Özetle, Laravel’in sunduğu her büyük özellik bir service provider tarafından başlatılır ve yapılandırılır. Framework’ün bu kadar çok özelliğini başlatıp yapılandırdıkları için, service provider’lar tüm Laravel başlatma sürecinin en önemli unsurudur.  

Framework dahili olarak onlarca service provider kullanırken, kendi provider’larınızı oluşturma seçeneğiniz de vardır. Uygulamanızda kullanılan kullanıcı tanımlı veya üçüncü taraf service provider’ların listesini `bootstrap/providers.php` dosyasında bulabilirsiniz.  

<br>

### Routing  
<br>

Uygulama başlatıldıktan ve tüm service provider’lar kayıt olduktan sonra, `Request` yönlendirme (router) tarafından dağıtılmak üzere devredilir. Router isteği bir route veya controller’a yönlendirir ve rota bazlı middleware’ları da çalıştırır.  

Middleware’lar, uygulamanıza giren HTTP isteklerini filtrelemek veya incelemek için kullanışlı bir mekanizma sağlar. Örneğin, Laravel bir kullanıcının kimlik doğrulamasını yapan bir middleware içerir. Eğer kullanıcı kimlik doğrulaması yapılmamışsa, middleware kullanıcıyı giriş ekranına yönlendirir. Ancak kullanıcı doğrulanmışsa, middleware isteğin uygulamada daha ileriye gitmesine izin verir.  

Bazı middleware’lar (örneğin `PreventRequestsDuringMaintenance`) uygulamadaki tüm rotalara atanır, bazıları ise yalnızca belirli rotalara veya rota gruplarına atanır. Middleware hakkında daha fazla bilgi edinmek için middleware dokümantasyonunu okuyabilirsiniz.  

Eğer istek, eşleşen rotanın tüm middleware’larından geçerse, rota veya controller metodu yürütülür ve rota veya controller metodunun döndürdüğü yanıt, rotanın middleware zinciri aracılığıyla geri gönderilir.  

<br>

### Sonuç 

Rota veya controller metodu bir yanıt döndürdüğünde, yanıt uygulamanın çıkış yönünde rotanın middleware’larından geçer; bu, uygulamaya giden yanıtı değiştirme veya inceleme fırsatı verir.  

Son olarak, yanıt middleware’lardan geçtikten sonra HTTP kernel’in `handle` metodu, yanıt nesnesini uygulama örneğinin `handleRequest` metoduna döndürür ve bu metod, döndürülen yanıt üzerinde `send` metodunu çağırır. `send` metodu yanıt içeriğini kullanıcının web tarayıcısına gönderir. Artık Laravel istek yaşam döngüsü boyunca yolculuğumuzu tamamlamış olduk!  

<br>

### Service Provider’lara Odaklanma  
<br>
Service provider’lar, bir Laravel uygulamasını başlatmanın gerçek anahtarıdır. Uygulama örneği oluşturulur, service provider’lar kaydedilir ve istek başlatılmış uygulamaya devredilir. Aslında bu kadar basittir!  

Bir Laravel uygulamasının service provider’lar aracılığıyla nasıl oluşturulduğunu ve başlatıldığını sağlam bir şekilde kavramak oldukça değerlidir. Uygulamanızın kullanıcı tanımlı service provider’ları `app/Providers` dizininde saklanır.  

Varsayılan olarak, `AppServiceProvider` oldukça boştur. Bu provider, uygulamanızın kendi başlatma işlemlerini ve service container binding’lerini eklemek için harika bir yerdir. Büyük uygulamalarda, her biri uygulamada kullanılan belirli hizmetler için daha ayrıntılı başlatma işlemleri içeren birkaç service provider oluşturmak isteyebilirsiniz.  
