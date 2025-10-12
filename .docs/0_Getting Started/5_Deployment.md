# Dağıtım (Deployment)

## Giriş

Laravel uygulamanızı production ortamına dağıtmaya hazır olduğunuzda, uygulamanızın mümkün olan en verimli şekilde çalıştığından emin olmak için yapabileceğiniz bazı önemli şeyler vardır. Bu belgede, Laravel uygulamanızın doğru şekilde dağıtıldığından emin olmak için harika başlangıç noktalarını ele alacağız.

---

## Sunucu Gereksinimleri

Laravel framework’ünün bazı sistem gereksinimleri vardır. Web sunucunuzun aşağıdaki minimum PHP sürümüne ve eklentilere sahip olduğundan emin olmalısınız:

* **PHP >= 8.2**
* **Ctype PHP Extension**
* **cURL PHP Extension**
* **DOM PHP Extension**
* **Fileinfo PHP Extension**
* **Filter PHP Extension**
* **Hash PHP Extension**
* **Mbstring PHP Extension**
* **OpenSSL PHP Extension**
* **PCRE PHP Extension**
* **PDO PHP Extension**
* **Session PHP Extension**
* **Tokenizer PHP Extension**
* **XML PHP Extension**

---

## Sunucu Yapılandırması

### Nginx

Uygulamanızı Nginx çalıştıran bir sunucuya dağıtıyorsanız, web sunucunuzu yapılandırmak için aşağıdaki yapılandırma dosyasını başlangıç noktası olarak kullanabilirsiniz.
Büyük olasılıkla, bu dosya sunucunuzun yapılandırmasına bağlı olarak özelleştirilmesi gerekecektir.
Eğer sunucunuzu yönetme konusunda yardım almak istiyorsanız, tam yönetimli bir Laravel platformu olan **Laravel Cloud** kullanmayı düşünebilirsiniz.

Aşağıdaki yapılandırmada olduğu gibi, web sunucunuzun tüm istekleri uygulamanızın **public/index.php** dosyasına yönlendirdiğinden emin olun.
**index.php** dosyasını asla projenizin kök dizinine taşımaya çalışmamalısınız; çünkü uygulamayı proje kök dizininden sunmak, birçok hassas yapılandırma dosyasını genel internete açık hale getirir.

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name example.com;
    root /srv/example.com/public;
 
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
 
    index index.php;
 
    charset utf-8;
 
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
 
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
 
    error_page 404 /index.php;
 
    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
 
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

### FrankenPHP

Laravel uygulamalarınızı sunmak için **FrankenPHP** de kullanılabilir.
FrankenPHP, **Go** diliyle yazılmış modern bir PHP uygulama sunucusudur.

Bir Laravel PHP uygulamasını FrankenPHP kullanarak çalıştırmak için şu komutu basitçe kullanabilirsiniz:

```bash
frankenphp php-server -r public/
```

FrankenPHP’nin sunduğu daha gelişmiş özelliklerden (örneğin **Laravel Octane entegrasyonu**, **HTTP/3**, **modern sıkıştırma**, veya Laravel uygulamalarını **bağımsız binary dosyalar** olarak paketleme) yararlanmak için, **FrankenPHP’nin Laravel dokümantasyonuna** başvurun.

---

## Dizin İzinleri

Laravel’in **bootstrap/cache** ve **storage** dizinlerine yazma erişimine ihtiyacı vardır.
Bu nedenle, web sunucusu işlem sahibinin bu dizinlere yazma iznine sahip olduğundan emin olmalısınız.

# Optimizasyon

Uygulamanızı **production** ortamına dağıtırken, yapılandırmalarınız, event’leriniz, route’larınız ve view’larınız dahil olmak üzere çeşitli dosyaların önbelleğe alınması gerekir.
Laravel, tüm bu dosyaları önbelleğe almak için tek bir kullanışlı **optimize** Artisan komutu sağlar. Bu komut genellikle uygulamanızın dağıtım sürecinin bir parçası olarak çalıştırılmalıdır:

```bash
php artisan optimize
```

Oluşturulan tüm önbellek dosyalarını ve varsayılan cache sürücüsündeki tüm anahtarları kaldırmak için **optimize:clear** komutu kullanılabilir:

```bash
php artisan optimize:clear
```

Aşağıdaki belgede, optimize komutu tarafından çalıştırılan ayrıntılı optimizasyon komutlarının her birini inceleyeceğiz.

---

## Yapılandırma (Configuration) Önbelleğe Alma

Uygulamanızı production ortamına dağıtırken, dağıtım sürecinizde **config:cache** Artisan komutunu çalıştırdığınızdan emin olmalısınız:

```bash
php artisan config:cache
```

Bu komut, Laravel’in tüm yapılandırma dosyalarını tek bir önbelleğe alınmış dosyada birleştirir. Bu, framework’ün yapılandırma değerlerini yüklerken dosya sistemine erişim sayısını önemli ölçüde azaltır.

Eğer **config:cache** komutunu çalıştırırsanız, yalnızca **env()** fonksiyonunu yapılandırma dosyalarınız içinde çağırdığınızdan emin olun. Çünkü yapılandırma önbelleğe alındıktan sonra, **.env** dosyası yüklenmeyecek ve **env()** çağrıları **null** döndürecektir.

---

## Event’leri Önbelleğe Alma

Dağıtım sürecinizde, uygulamanızın otomatik olarak keşfedilen **event** → **listener** eşlemelerini önbelleğe almalısınız.
Bu işlem, dağıtım sırasında aşağıdaki komutla yapılabilir:

```bash
php artisan event:cache
```

---

## Route’ları Önbelleğe Alma

Birçok route’a sahip büyük bir uygulama oluşturuyorsanız, dağıtım sürecinizde **route:cache** Artisan komutunu çalıştırdığınızdan emin olun:

```bash
php artisan route:cache
```

Bu komut, tüm route kayıtlarınızı tek bir method çağrısına indirger ve yüzlerce route kaydı yapılırken performansı artırır.

---

## View’ları Önbelleğe Alma

Uygulamanızı production ortamına dağıtırken, dağıtım sürecinizde **view:cache** Artisan komutunu çalıştırdığınızdan emin olun:

```bash
php artisan view:cache
```

Bu komut, tüm Blade view’larınızı önceden derler, böylece istek anında derlenmeleri gerekmez. Bu da view döndüren her isteğin performansını artırır.

---

## Debug Modu

`config/app.php` yapılandırma dosyasındaki **debug** seçeneği, bir hata hakkında kullanıcıya ne kadar bilgi gösterileceğini belirler.
Varsayılan olarak, bu seçenek `.env` dosyanızda bulunan **APP_DEBUG** ortam değişkeninin değerine göre ayarlanır.

**Production** ortamında bu değer **false** olmalıdır.
Eğer **APP_DEBUG** değişkeni production ortamında **true** olarak ayarlanmışsa, uygulamanızın son kullanıcılarına hassas yapılandırma değerlerini sızdırma riskiyle karşı karşıya kalırsınız.

---

## Health Route

Laravel, uygulamanızın durumunu izlemek için kullanılabilecek yerleşik bir **health check** route içerir.
Production ortamında bu route, uygulamanızın durumunu bir **uptime monitor**, **load balancer** veya **Kubernetes** gibi bir orkestrasyon sistemine bildirmek için kullanılabilir.

Varsayılan olarak, health check route’u **/up** adresinde sunulur ve uygulama hatasız başlatılmışsa **200 HTTP** yanıtı döner.
Aksi halde **500 HTTP** yanıtı döndürülür.

Bu route’un URI’sini uygulamanızın `bootstrap/app` dosyasında yapılandırabilirsiniz:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up', 
    health: '/status', 
)
```

Bu route’a HTTP isteği yapıldığında, Laravel ayrıca **Illuminate\Foundation\Events\DiagnosingHealth** event’ini tetikler.
Bu event için bir listener içerisinde, uygulamanızın veritabanı veya cache durumunu kontrol edebilirsiniz.
Eğer bir sorun tespit ederseniz, listener’dan bir exception fırlatarak uygulamanızın “sağlıksız” durumunu belirtebilirsiniz.

---

## Laravel Cloud veya Forge ile Dağıtım

### Laravel Cloud

Laravel’e özel optimize edilmiş, **tam yönetimli, otomatik ölçeklenen bir dağıtım platformu** arıyorsanız, **Laravel Cloud**’u inceleyin.
Laravel Cloud, yönetilen compute kaynakları, veritabanları, cache’ler ve obje depolama hizmetleri sunan güçlü bir dağıtım platformudur.

Uygulamanızı Cloud üzerinde başlatın ve ölçeklenebilirliğin sadeliğine hayran kalın.
Laravel Cloud, Laravel’in geliştiricileri tarafından framework ile kusursuz çalışacak şekilde optimize edilmiştir, böylece uygulamalarınızı alıştığınız şekilde geliştirmeye devam edebilirsiniz.

---

### Laravel Forge

Kendi sunucunuzu yönetmek istiyor ancak güçlü bir Laravel uygulaması çalıştırmak için gereken tüm servisleri yapılandırmakta rahat değilseniz, **Laravel Forge** tam size göre.
Forge, Laravel uygulamaları için bir **VPS sunucu yönetim platformudur**.

Forge, **DigitalOcean**, **Linode**, **AWS** ve diğer altyapı sağlayıcılarında sunucular oluşturabilir.
Ayrıca, sağlam Laravel uygulamaları oluşturmak için gerekli tüm araçları (ör. **Nginx**, **MySQL**, **Redis**, **Memcached**, **Beanstalk** ve daha fazlasını) kurar ve yönetir.

