

<br>



## Concurrency

<br>


### Introduction

Bazen birbirine bağlı olmayan birkaç yavaş görevi yürütmeniz gerekebilir. Birçok durumda, görevleri eşzamanlı olarak yürütmek önemli performans iyileştirmeleri sağlayabilir. Laravel'in **Concurrency** facadesi, closure'ları eşzamanlı olarak yürütmek için basit ve kullanışlı bir API sunar.

<br>


### How it Works

Laravel, verilen closure'ları serileştirip gizli bir **Artisan CLI** komutuna göndererek eşzamanlılık sağlar. Bu komut closure'ları yeniden serileştirir ve kendi PHP süreci içinde çalıştırır. Closure yürütüldükten sonra, elde edilen değer tekrar ana sürece serileştirilir.

**Concurrency** facadesi üç sürücüyü destekler: `process` (varsayılan), `fork` ve `sync`.

`fork` sürücüsü varsayılan `process` sürücüsüne kıyasla daha iyi performans sağlar, ancak yalnızca PHP'nin CLI bağlamında kullanılabilir; çünkü PHP, web istekleri sırasında forking desteklemez. `fork` sürücüsünü kullanmadan önce şu paketi yüklemeniz gerekir:

```bash
composer require spatie/fork
````

`sync` sürücüsü ise genellikle test sırasında, tüm eşzamanlılığı devre dışı bırakmak ve closure'ları ana süreçte sırayla yürütmek istediğiniz durumlarda kullanışlıdır.

<br>


### Running Concurrent Tasks

Eşzamanlı görevler yürütmek için **Concurrency** facadesinin `run` metodunu çağırabilirsiniz. `run` metodu, alt PHP süreçlerinde aynı anda yürütülmesi gereken closure'ların bir dizisini kabul eder:

```php
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;
 
[$userCount, $orderCount] = Concurrency::run([
    fn () => DB::table('users')->count(),
    fn () => DB::table('orders')->count(),
]);
```

Belirli bir sürücüyü kullanmak için `driver` metodunu kullanabilirsiniz:

```php
$results = Concurrency::driver('fork')->run(...);
```

Ya da varsayılan concurrency sürücüsünü değiştirmek için, **config:publish** Artisan komutu ile concurrency yapılandırma dosyasını yayımlamalı ve dosya içindeki `default` seçeneğini güncellemelisiniz:

```bash
php artisan config:publish concurrency
```

<br>


### Deferring Concurrent Tasks

Eğer bir dizi closure'ı eşzamanlı olarak yürütmek istiyor, ancak bu closure'lardan dönen sonuçlarla ilgilenmiyorsanız, `defer` metodunu kullanmalısınız. `defer` metodu çağrıldığında, verilen closure'lar hemen yürütülmez. Bunun yerine, Laravel bu closure'ları HTTP yanıtı kullanıcıya gönderildikten sonra eşzamanlı olarak çalıştırır:

```php
use App\Services\Metrics;
use Illuminate\Support\Facades\Concurrency;
 
Concurrency::defer([
    fn () => Metrics::report('users'),
    fn () => Metrics::report('orders'),
]);
```

```
```
