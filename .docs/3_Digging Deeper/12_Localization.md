

<br>







## Giriş

Varsayılan olarak, Laravel uygulama iskeleti `lang` dizinini içermez. Laravel’in dil dosyalarını özelleştirmek istersen, bunları `lang:publish` Artisan komutu aracılığıyla yayımlayabilirsin.

Laravel’in yerelleştirme (localization) özellikleri, farklı dillerdeki metinleri kolayca almanı sağlar ve uygulamanı birden fazla dili destekleyecek şekilde yapılandırmanı mümkün kılar.

Laravel, çeviri metinlerini yönetmek için iki yöntem sunar.  
Birincisi, dil metinleri uygulamanın `lang` dizini içinde dosyalar halinde saklanabilir. Bu dizin içinde, uygulamanın desteklediği her dil için bir alt dizin bulunabilir.  
Laravel, doğrulama hata mesajları gibi yerleşik özelliklerin çeviri metinlerini yönetmek için bu yaklaşımı kullanır:

```

/lang
/en
messages.php
/es
messages.php

```

Alternatif olarak, çeviri metinleri `lang` dizinine yerleştirilen JSON dosyaları içinde tanımlanabilir. Bu yaklaşımda, uygulamanın desteklediği her dil için bir JSON dosyası bulunur. Çok sayıda çevrilebilir metni olan uygulamalar için bu yöntem önerilir:

```

/lang
en.json
es.json

````

Bu belgede, her iki yaklaşımı da detaylı olarak inceleyeceğiz.

<br>





## Dil Dosyalarını Yayımlama (Publishing the Language Files)

Varsayılan olarak Laravel uygulama iskeleti `lang` dizinini içermez. Laravel’in dil dosyalarını özelleştirmek veya kendi dosyalarını oluşturmak istersen, `lang:publish` Artisan komutu ile `lang` dizinini oluşturabilirsin.  
Bu komut, uygulamanda `lang` dizinini oluşturur ve Laravel’in varsayılan dil dosyalarını yayımlar:

```bash
php artisan lang:publish
````

<br>





## Locale Yapılandırması (Configuring the Locale)

Uygulamanın varsayılan dili, `config/app.php` dosyasındaki `locale` yapılandırma seçeneğinde saklanır ve genellikle `APP_LOCALE` ortam değişkeni aracılığıyla ayarlanır. Bu değeri uygulamanın ihtiyaçlarına göre değiştirebilirsin.

Ayrıca, varsayılan dilde bulunmayan çeviri metinleri için kullanılacak bir “yedek dil” (fallback language) de belirleyebilirsin. Bu değer de `config/app.php` dosyasında yapılandırılır ve genellikle `APP_FALLBACK_LOCALE` ortam değişkeniyle ayarlanır.

Varsayılan dili çalışma zamanında (runtime) tek bir HTTP isteği için değiştirmek istersen, `App` facade’ının `setLocale` metodunu kullanabilirsin:

```php
use Illuminate\Support\Facades\App;
 
Route::get('/greeting/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'es', 'fr'])) {
        abort(400);
    }
 
    App::setLocale($locale);
 
    // ...
});
```

<br>





## Geçerli Locale’i Belirleme (Determining the Current Locale)

Geçerli locale değerini öğrenmek veya belirli bir locale’in kullanılıp kullanılmadığını kontrol etmek için `App` facade’ının `currentLocale` ve `isLocale` metotlarını kullanabilirsin:

```php
use Illuminate\Support\Facades\App;
 
$locale = App::currentLocale();
 
if (App::isLocale('en')) {
    // ...
}
```

<br>





## Çoğullama Dili (Pluralization Language)

Laravel’in, Eloquent gibi kısımlarda tekil kelimeleri çoğul hale getirmek için kullandığı “pluralizer” dilini İngilizce dışında bir dile ayarlayabilirsin.
Bunu, uygulamanın bir service provider’ındaki `boot` metodunda `useLanguage` metodunu çağırarak yapabilirsin.
Desteklenen diller: **french, norwegian-bokmal, portuguese, spanish, turkish**

```php
use Illuminate\Support\Pluralizer;
 
public function boot(): void
{
    Pluralizer::useLanguage('spanish');

    // ...
}
```

Pluralizer dilini özelleştirirsen, Eloquent modellerinin tablo adlarını açıkça tanımlamalısın.

<br>





## Çeviri Metinlerini Tanımlama (Defining Translation Strings)

### Kısa Anahtarlar Kullanma (Using Short Keys)

Genellikle çeviri metinleri `lang` dizinindeki dosyalarda saklanır.
Bu dizin içinde uygulamanın desteklediği her dil için bir alt dizin bulunur. Laravel’in yerleşik özelliklerinin (örneğin doğrulama hata mesajlarının) çevirileri bu şekilde yönetilir:

```
/lang
    /en
        messages.php
    /es
        messages.php
```

Tüm dil dosyaları, anahtar/değer çiftlerinden oluşan bir dizi döndürür:

```php
<?php
 
// lang/en/messages.php
 
return [
    'welcome' => 'Welcome to our application!',
];
```

Bölgelere göre farklılık gösteren diller için ISO 15897 standardına uygun dizin adları kullanılmalıdır.
Örneğin, İngilizce (Birleşik Krallık) için `en-gb` yerine `en_GB` kullanılmalıdır.

<br>





### Çeviri Metinlerini Anahtar Olarak Kullanma (Using Translation Strings as Keys)

Çok sayıda çevrilebilir metni olan uygulamalarda, her metin için kısa anahtarlar oluşturmak karmaşık hale gelebilir.
Bu nedenle, Laravel çeviri metinlerini anahtar olarak kullanmayı da destekler.
Bu durumda dil dosyaları JSON formatında `lang` dizininde saklanır.
Örneğin, uygulamanda İspanyolca çeviri varsa, `lang/es.json` dosyası oluşturabilirsin:

```json
{
    "I love programming.": "Me encanta programar."
}
```

<br>





### Anahtar / Dosya Çakışmaları (Key / File Conflicts)

Diğer çeviri dosya adlarıyla çakışan anahtarlar tanımlamamalısın.
Örneğin, `__('Action')` ifadesini “NL” locale’i için kullanırken `nl/action.php` dosyası varsa fakat `nl.json` yoksa, çevirici tüm `nl/action.php` dosyasının içeriğini döndürür.

<br>





## Çeviri Metinlerini Getirme (Retrieving Translation Strings)

Dil dosyalarındaki çeviri metinlerini almak için `__` helper fonksiyonunu kullanabilirsin.
Kısa anahtarlar kullanıyorsan, `__` fonksiyonuna dosya adı ve anahtarı “dot” (nokta) notasyonu ile birlikte iletmelisin:

```php
echo __('messages.welcome');
```

Belirtilen çeviri mevcut değilse, `__` fonksiyonu anahtarın kendisini döndürür (örneğin `messages.welcome`).

Eğer çeviri metinlerini anahtar olarak kullanıyorsan, metnin varsayılan halini doğrudan `__` fonksiyonuna iletebilirsin:

```php
echo __('I love programming.');
```

Bu durumda da çeviri bulunamazsa, metnin kendisi döndürülür.

Blade şablon motorunda, çeviri metinlerini aşağıdaki gibi görüntüleyebilirsin:

```blade
{{ __('messages.welcome') }}
```

<br>





## Çeviri Metinlerinde Parametreleri Değiştirme (Replacing Parameters in Translation Strings)

İstersen çeviri metinlerinde yer tutucular (placeholders) tanımlayabilirsin.
Tüm yer tutucular `:` karakteriyle başlar:

```php
'welcome' => 'Welcome, :name',
```

Yer tutucuların değerlerini değiştirmek için, `__` fonksiyonuna ikinci argüman olarak bir dizi geçebilirsin:

```php
echo __('messages.welcome', ['name' => 'dayle']);
```

Yer tutucuların tamamı büyük harf veya baş harfi büyükse, çeviri metninde buna uygun biçimlendirme yapılır:

```php
'welcome' => 'Welcome, :NAME', // Welcome, DAYLE
'goodbye' => 'Goodbye, :Name', // Goodbye, Dayle
```

<br>





## Nesne Formatlama (Object Replacement Formatting)

Bir nesneyi yer tutucu olarak kullanırsan, nesnenin `__toString` metodu çağrılır.
Ancak bazı durumlarda, bu metoda erişimin olmayabilir (örneğin üçüncü taraf bir kütüphanede).

Bu tür durumlarda, Laravel belirli nesne türleri için özel biçimlendirme işlemleri tanımlamana izin verir.
Bunu yapmak için `translator`’ın `stringable` metodunu kullanabilirsin.
Genellikle bu metod, `AppServiceProvider`’ın `boot` metodunda çağrılır:

```php
use Illuminate\Support\Facades\Lang;
use Money\Money;
 
public function boot(): void
{
    Lang::stringable(function (Money $money) {
        return $money->formatTo('en_GB');
    });
}
```

<br>





## Çoğullama (Pluralization)

Çoğullama karmaşık bir konudur çünkü farklı diller farklı çoğullama kurallarına sahiptir.
Laravel, senin tanımladığın çoğullama kurallarına göre farklı çeviri metinleri döndürebilir.

`|` karakterini kullanarak tekil ve çoğul halleri ayırabilirsin:

```php
'apples' => 'There is one apple|There are many apples',
```

JSON formatındaki çevirilerde de çoğullama desteklenir:

```json
{
    "There is one apple|There are many apples": "Hay una manzana|Hay muchas manzanas"
}
```

Ayrıca, belirli sayı aralıkları için farklı metinler tanımlayabilirsin:

```php
'apples' => '{0} There are none|[1,19] There are some|[20,*] There are many',
```

Bu tür bir çeviri tanımladıktan sonra, `trans_choice` fonksiyonunu kullanarak belirli bir sayı için uygun metni alabilirsin:

```php
echo trans_choice('messages.apples', 10);
```

Yer tutucu (placeholder) değerleri de `trans_choice` fonksiyonuna üçüncü parametre olarak gönderilebilir:

```php
'minutes_ago' => '{1} :value minute ago|[2,*] :value minutes ago',

echo trans_choice('time.minutes_ago', 5, ['value' => 5]);
```

Gönderilen sayısal değeri göstermek istersen, yerleşik `:count` yer tutucusunu kullanabilirsin:

```php
'apples' => '{0} There are none|{1} There is one|[2,*] There are :count',
```

<br>





## Paket Dil Dosyalarını Geçersiz Kılma (Overriding Package Language Files)

Bazı paketler kendi dil dosyalarıyla birlikte gelir.
Bu dosyaları doğrudan değiştirmek yerine, `lang/vendor/{package}/{locale}` dizinine yerleştirerek geçersiz kılabilirsin.

Örneğin, `skyrim/hearthfire` adında bir paketin İngilizce `messages.php` dosyasını geçersiz kılmak istersen, dosyanı şu dizine yerleştirmelisin:

```
lang/vendor/hearthfire/en/messages.php
```

Bu dosyada yalnızca değiştirmek istediğin çeviri metinlerini tanımlamalısın.
Tanımlanmamış metinler, paketin orijinal dil dosyasından yüklenmeye devam eder.

---

**Laravel**, yazılım oluşturmanın, dağıtmanın ve izlemenin en üretken yoludur.

