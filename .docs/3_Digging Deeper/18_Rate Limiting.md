# Rate Limiting


<br>




## Introduction

Laravel, uygulamanızın cache sistemiyle birlikte çalışan, kullanımı kolay bir **rate limiting (oran sınırlama)** yapısı sunar.  
Bu yapı, belirli bir zaman aralığında herhangi bir eylemin kaç kez gerçekleştirilebileceğini kolayca sınırlamanızı sağlar.

Gelen HTTP isteklerini sınırlamak istiyorsanız, **rate limiter middleware** dokümantasyonuna bakmalısınız.

<br>


## Cache Configuration

Genellikle rate limiter, uygulamanızın **cache configuration** dosyasındaki `default` anahtarıyla tanımlanmış olan varsayılan cache sürücüsünü kullanır.  
Ancak, rate limiter’ın hangi cache sürücüsünü kullanacağını `limiter` anahtarıyla belirtebilirsiniz:

```php
'default' => env('CACHE_STORE', 'database'),

'limiter' => 'redis',
````

<br>


## Basic Usage

`Illuminate\Support\Facades\RateLimiter` facade’ı rate limiter ile etkileşim kurmak için kullanılır.
En basit yöntem `attempt` metodudur. Bu metod, belirtilen işlem için verilen saniye aralığında sınırlı sayıda çalışmaya izin verir.

`attempt` metodu, kalan deneme hakkı yoksa `false` döner; aksi halde callback’in sonucunu veya `true` döner.
İlk argüman, sınırlanacak işlemi temsil eden bir “anahtar” (key) olmalıdır:

```php
use Illuminate\Support\Facades\RateLimiter;

$executed = RateLimiter::attempt(
    'send-message:'.$user->id,
    $perMinute = 5,
    function() {
        // Mesaj gönder...
    }
);

if (! $executed) {
    return 'Too many messages sent!';
}
```

Gerekirse, `attempt` metoduna dördüncü argüman olarak “decay rate” yani kalan denemelerin sıfırlanacağı süreyi saniye cinsinden belirtebilirsiniz.
Aşağıdaki örnekte, iki dakikada beş denemeye izin verilmiştir:

```php
$executed = RateLimiter::attempt(
    'send-message:'.$user->id,
    $perTwoMinutes = 5,
    function() {
        // Mesaj gönder...
    },
    $decayRate = 120,
);
```

<br>


## Manually Incrementing Attempts

Rate limiter ile manuel olarak etkileşim kurmak isterseniz, kullanabileceğiniz birkaç yardımcı metod mevcuttur.

Belirli bir key’in izin verilen maksimum deneme sayısını aşıp aşmadığını kontrol etmek için `tooManyAttempts` metodunu kullanabilirsiniz:

```php
use Illuminate\Support\Facades\RateLimiter;

if (RateLimiter::tooManyAttempts('send-message:'.$user->id, $perMinute = 5)) {
    return 'Too many attempts!';
}

RateLimiter::increment('send-message:'.$user->id);

// Mesaj gönder...
```

Bir key için kalan deneme sayısını öğrenmek isterseniz `remaining` metodunu kullanabilirsiniz.
Kalan hakkı olan bir key için, `increment` metoduyla toplam deneme sayısını artırabilirsiniz:

```php
use Illuminate\Support\Facades\RateLimiter;

if (RateLimiter::remaining('send-message:'.$user->id, $perMinute = 5)) {
    RateLimiter::increment('send-message:'.$user->id);

    // Mesaj gönder...
}
```

Bir key’in değerini 1 yerine farklı bir miktarda artırmak isterseniz, `increment` metoduna `amount` parametresi verebilirsiniz:

```php
RateLimiter::increment('send-message:'.$user->id, amount: 5);
```

<br>


## Determining Limiter Availability

Bir key’in deneme hakkı kalmadığında, `availableIn` metodu yeni denemelerin ne kadar süre sonra mümkün olacağını saniye cinsinden döner:

```php
use Illuminate\Support\Facades\RateLimiter;

if (RateLimiter::tooManyAttempts('send-message:'.$user->id, $perMinute = 5)) {
    $seconds = RateLimiter::availableIn('send-message:'.$user->id);

    return 'You may try again in '.$seconds.' seconds.';
}

RateLimiter::increment('send-message:'.$user->id);

// Mesaj gönder...
```

<br>


## Clearing Attempts

Belirli bir rate limiter key’i için deneme sayısını sıfırlamak isterseniz `clear` metodunu kullanabilirsiniz.
Örneğin, bir mesaj alıcı tarafından okunduğunda deneme sayısını sıfırlayabilirsiniz:

```php
use App\Models\Message;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Mark the message as read.
 */
public function read(Message $message): Message
{
    $message->markAsRead();

    RateLimiter::clear('send-message:'.$message->user_id);

    return $message;
}
```

<br>


Laravel, yazılım oluşturmanın, dağıtmanın ve izlemenin en verimli yoludur.
```
