## HTTP İstekleri

### Giriş

Laravel'in `Illuminate\Http\Request` sınıfı, uygulamanız tarafından işlenen mevcut HTTP isteğiyle etkileşime geçmenin ve istekle birlikte gönderilen girdileri, çerezleri ve dosyaları almanın nesne yönelimli bir yolunu sağlar.

---

### İstekle Etkileşim

#### İsteğe Erişim

Geçerli HTTP isteğinin bir örneğini bağımlılık enjeksiyonu yoluyla elde etmek için, `Illuminate\Http\Request` sınıfını rota kapanışınızda veya denetleyici metodunuzda type-hint yapmalısınız. Gelen istek örneği Laravel servis container tarafından otomatik olarak enjekte edilir:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Yeni bir kullanıcı kaydet.
     */
    public function store(Request $request): RedirectResponse
    {
        $name = $request->input('name');

        // Kullanıcıyı kaydet...

        return redirect('/users');
    }
}
```

Belirtildiği gibi, `Illuminate\Http\Request` sınıfını bir rota kapanışında da type-hint yapabilirsiniz. Servis container, kapanış çalıştırıldığında gelen isteği otomatik olarak enjekte edecektir:

```php
use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
    // ...
});
```

---

### Bağımlılık Enjeksiyonu ve Rota Parametreleri

Eğer denetleyici metodunuz bir rota parametresinden de giriş bekliyorsa, rota parametrelerini diğer bağımlılıklardan sonra listelemelisiniz. Örneğin, rotanız şu şekilde tanımlanmışsa:

```php
use App\Http\Controllers\UserController;

Route::put('/user/{id}', [UserController::class, 'update']);
```

`Illuminate\Http\Request`’i yine de type-hint yapabilir ve `id` rota parametresine şu şekilde erişebilirsiniz:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Belirtilen kullanıcıyı güncelle.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        // Kullanıcıyı güncelle...

        return redirect('/users');
    }
}
```

---

### İstek Yolu, Host ve Metodu

`Illuminate\Http\Request` örneği, gelen HTTP isteğini incelemek için çeşitli metodlar sağlar ve `Symfony\Component\HttpFoundation\Request` sınıfını genişletir. Aşağıda en önemli metodlardan bazılarını ele alacağız.

#### İstek Yolunu Alma

`path` metodu, isteğin yol bilgisini döndürür. Örneğin, gelen istek `http://example.com/foo/bar` adresine yönlendirilmişse, `path` metodu `foo/bar` döndürür:

```php
$uri = $request->path();
```

#### İstek Yolunu / Rotasını İnceleme

`is` metodu, gelen isteğin yolu belirli bir desenle eşleşip eşleşmediğini doğrulamanızı sağlar. Bu metodu kullanırken `*` karakterini joker karakter olarak kullanabilirsiniz:

```php
if ($request->is('admin/*')) {
    // ...
}
```

`routeIs` metodunu kullanarak, gelen isteğin belirli bir adlandırılmış rotayla eşleşip eşleşmediğini belirleyebilirsiniz:

```php
if ($request->routeIs('admin.*')) {
    // ...
}
```

---

### İstek URL’sini Alma

Gelen isteğin tam URL’sini almak için `url` veya `fullUrl` metodlarını kullanabilirsiniz. `url` metodu sorgu dizesi olmadan URL’yi döndürürken, `fullUrl` sorgu dizesini içerir:

```php
$url = $request->url();

$urlWithQueryString = $request->fullUrl();
```

Mevcut URL’ye sorgu dizesi verileri eklemek isterseniz `fullUrlWithQuery` metodunu çağırabilirsiniz. Bu metod, verilen sorgu değişkenlerini mevcut sorgu dizisiyle birleştirir:

```php
$request->fullUrlWithQuery(['type' => 'phone']);
```

Belirli bir sorgu parametresi olmadan mevcut URL’yi almak için `fullUrlWithoutQuery` metodunu kullanabilirsiniz:

```php
$request->fullUrlWithoutQuery(['type']);
```

---

### İstek Host’unu Alma

Gelen isteğin "host" bilgisini `host`, `httpHost` ve `schemeAndHttpHost` metodlarıyla alabilirsiniz:

```php
$request->host();
$request->httpHost();
$request->schemeAndHttpHost();
```

---

### İstek Metodunu Alma

`method` metodu, isteğin HTTP fiilini döndürür. `isMethod` metodunu kullanarak, HTTP fiilinin belirli bir string ile eşleşip eşleşmediğini kontrol edebilirsiniz:

```php
$method = $request->method();

if ($request->isMethod('post')) {
    // ...
}
```

---

### İstek Header’ları

`Illuminate\Http\Request` örneğinden bir header almak için `header` metodunu kullanabilirsiniz. Eğer header mevcut değilse, `null` döndürülür. Ancak, `header` metodu, header mevcut değilse döndürülecek isteğe bağlı ikinci bir argüman kabul eder:

```php
$value = $request->header('X-Header-Name');

$value = $request->header('X-Header-Name', 'default');
```

`hasHeader` metodu, isteğin belirli bir header içerip içermediğini belirlemenizi sağlar:

```php
if ($request->hasHeader('X-Header-Name')) {
    // ...
}
```

Kolaylık olması açısından, `bearerToken` metodu `Authorization` header’ından bir bearer token almak için kullanılabilir. Eğer böyle bir header mevcut değilse, boş bir string döndürülür:

```php
$token = $request->bearerToken();
```

---

### İstek IP Adresi

`ip` metodu, isteği yapan istemcinin IP adresini almak için kullanılabilir:

```php
$ipAddress = $request->ip();
```

Proxy’ler tarafından iletilen tüm istemci IP adreslerini de içeren bir IP adresleri dizisi almak isterseniz, `ips` metodunu kullanabilirsiniz. “Orijinal” istemci IP adresi dizinin sonunda bulunur:

```php
$ipAddresses = $request->ips();
```

Genel olarak, IP adreslerinin güvenilir olmadığını ve kullanıcı kontrolünde bir girdi olarak değerlendirilmesi gerektiğini unutmayın.

---

### İçerik Pazarlığı (Content Negotiation)

Laravel, gelen isteğin `Accept` header’ı aracılığıyla talep edilen içerik türlerini incelemek için çeşitli metodlar sağlar.

Öncelikle, `getAcceptableContentTypes` metodu, isteğin kabul ettiği tüm içerik türlerini içeren bir dizi döndürür:

```php
$contentTypes = $request->getAcceptableContentTypes();
```

`accepts` metodu, bir içerik türü dizisini kabul eder ve eğer bu türlerden herhangi biri istek tarafından kabul ediliyorsa `true`, aksi halde `false` döndürür:

```php
if ($request->accepts(['text/html', 'application/json'])) {
    // ...
}
```

`prefers` metodunu kullanarak, verilen içerik türlerinden hangisinin istek tarafından en çok tercih edildiğini belirleyebilirsiniz. Eğer hiçbir içerik türü kabul edilmiyorsa, `null` döndürülür:

```php
$preferred = $request->prefers(['text/html', 'application/json']);
```

Birçok uygulama yalnızca HTML veya JSON sunar, bu nedenle gelen isteğin JSON yanıtı bekleyip beklemediğini hızlıca belirlemek için `expectsJson` metodunu kullanabilirsiniz:

```php
if ($request->expectsJson()) {
    // ...
}
```

---

### PSR-7 İstekleri

PSR-7 standardı, istekler ve yanıtlar dahil olmak üzere HTTP mesajları için arayüzleri belirtir. Laravel isteği yerine bir PSR-7 isteği örneği almak istiyorsanız, önce birkaç kütüphane yüklemeniz gerekir. Laravel, tipik Laravel isteklerini ve yanıtlarını PSR-7 uyumlu uygulamalara dönüştürmek için Symfony HTTP Message Bridge bileşenini kullanır:

```bash
composer require symfony/psr-http-message-bridge
composer require nyholm/psr7
```

Bu kütüphaneleri yükledikten sonra, rota kapanışınızda veya denetleyici metodunuzda istek arayüzünü type-hint yaparak bir PSR-7 isteği elde edebilirsiniz:

```php
use Psr\Http\Message\ServerRequestInterface;

Route::get('/', function (ServerRequestInterface $request) {
    // ...
});
```

Bir rota veya denetleyiciden bir PSR-7 yanıt örneği döndürürseniz, bu yanıt otomatik olarak bir Laravel yanıt örneğine dönüştürülür ve framework tarafından görüntülenir.

## Girdi

### Girdiyi Alma

#### Tüm Girdi Verilerini Alma

Gelen isteğin tüm girdi verilerini bir dizi olarak almak için `all` metodunu kullanabilirsiniz. Bu metod, gelen isteğin bir HTML formundan mı yoksa bir XHR isteğinden mi geldiğine bakılmaksızın kullanılabilir:

```php
$input = $request->all();
```

`collect` metodunu kullanarak, gelen isteğin tüm girdi verilerini bir koleksiyon olarak da alabilirsiniz:

```php
$input = $request->collect();
```

`collect` metodu ayrıca, gelen isteğin girdisinin bir alt kümesini koleksiyon olarak almanızı da sağlar:

```php
$request->collect('users')->each(function (string $user) {
    // ...
});
```

---

#### Bir Girdi Değerini Alma

Birkaç basit metod kullanarak, `Illuminate\Http\Request` örneğinizden kullanıcı girdisinin tamamına hangi HTTP fiilinin kullanıldığını düşünmeden erişebilirsiniz. HTTP fiilinden bağımsız olarak, `input` metodu kullanıcı girdisini almak için kullanılabilir:

```php
$name = $request->input('name');
```

`input` metoduna ikinci argüman olarak varsayılan bir değer geçebilirsiniz. Bu değer, istenen girdi değeri istek içinde mevcut değilse döndürülür:

```php
$name = $request->input('name', 'Sally');
```

Dizi girdileri içeren formlarla çalışırken, dizilere erişmek için “nokta” gösterimini kullanın:

```php
$name = $request->input('products.0.name');

$names = $request->input('products.*.name');
```

`input` metodunu herhangi bir argüman olmadan çağırarak, tüm girdi değerlerini bir ilişkisel dizi olarak alabilirsiniz:

```php
$input = $request->input();
```

---

#### Sorgu Dizesinden Girdi Alma

`input` metodu, sorgu dizesi dahil tüm istek yükünden değerleri alırken, `query` metodu yalnızca sorgu dizesinden değerleri alır:

```php
$name = $request->query('name');
```

İstenen sorgu dizesi değeri mevcut değilse, bu metoda verilen ikinci argüman döndürülür:

```php
$name = $request->query('name', 'Helen');
```

`query` metodunu herhangi bir argüman olmadan çağırarak, tüm sorgu dizesi değerlerini bir ilişkisel dizi olarak alabilirsiniz:

```php
$query = $request->query();
```

---

#### JSON Girdi Değerlerini Alma

Uygulamanıza JSON istekleri gönderirken, isteğin `Content-Type` header’ı `application/json` olarak düzgün bir şekilde ayarlandığı sürece JSON verisine `input` metodu aracılığıyla erişebilirsiniz. JSON dizileri / nesneleri içinde iç içe geçmiş değerlere erişmek için “nokta” sözdizimini bile kullanabilirsiniz:

```php
$name = $request->input('user.name');
```

---

#### Stringable Girdi Değerlerini Alma

İstek girdisini ilkel bir string olarak almak yerine, `string` metodunu kullanarak isteğin verisini bir `Illuminate\Support\Stringable` örneği olarak alabilirsiniz:

```php
$name = $request->string('name')->trim();
```

---

#### Tam Sayı Girdi Değerlerini Alma

Girdi değerlerini tam sayı olarak almak için `integer` metodunu kullanabilirsiniz. Bu metod, girdi değerini tam sayıya dönüştürmeye çalışır. Eğer girdi mevcut değilse veya dönüşüm başarısız olursa, belirttiğiniz varsayılan değeri döndürür. Bu, sayfalama veya diğer sayısal girdiler için özellikle kullanışlıdır:

```php
$perPage = $request->integer('per_page');
```

---

#### Boolean Girdi Değerlerini Alma

HTML elemanları (örneğin checkbox’lar) ile çalışırken, uygulamanız aslında string olan “doğru” (truthy) değerler alabilir. Örneğin, `"true"` veya `"on"`. Kolaylık sağlamak için, bu değerleri boolean olarak almak üzere `boolean` metodunu kullanabilirsiniz. `boolean` metodu, `1`, `"1"`, `true`, `"true"`, `"on"`, ve `"yes"` değerleri için `true` döndürür. Diğer tüm değerler için `false` döner:

```php
$archived = $request->boolean('archived');
```

---

#### Dizi Girdi Değerlerini Alma

Dizi içeren girdi değerleri `array` metoduyla alınabilir. Bu metod, girdi değerini her zaman bir diziye dönüştürür. Eğer istek verilen ada sahip bir girdi içermiyorsa, boş bir dizi döndürülür:

```php
$versions = $request->array('versions');
```

---

#### Tarih Girdi Değerlerini Alma

Kolaylık olması açısından, tarih / saat içeren girdi değerleri `date` metoduyla `Carbon` örnekleri olarak alınabilir. Eğer istek belirtilen ada sahip bir girdi içermiyorsa, `null` döndürülür:

```php
$birthday = $request->date('birthday');
```

`date` metodunun ikinci ve üçüncü argümanları, sırasıyla tarihin biçimini ve zaman dilimini belirtmek için kullanılabilir:

```php
$elapsed = $request->date('elapsed', '!H:i', 'Europe/Madrid');
```

Eğer girdi değeri mevcutsa ancak geçersiz bir biçime sahipse, bir `InvalidArgumentException` fırlatılır; bu nedenle, `date` metodunu çağırmadan önce girdiyi doğrulamanız önerilir.

---

#### Enum Girdi Değerlerini Alma

PHP enum’larına karşılık gelen girdi değerleri de istekten alınabilir. Eğer istek belirtilen ada sahip bir girdi içermiyorsa veya enum’un desteklenen bir değeriyle eşleşmiyorsa, `null` döndürülür. `enum` metodu, girdi adını ve enum sınıfını birinci ve ikinci argüman olarak kabul eder:

```php
use App\Enums\Status;

$status = $request->enum('status', Status::class);
```

Eksik veya geçersiz bir değer durumunda döndürülecek varsayılan bir değer de belirtebilirsiniz:

```php
$status = $request->enum('status', Status::class, Status::Pending);
```

Eğer girdi değeri bir PHP enum’una karşılık gelen değerlerin dizisiyse, bu değerleri enum örnekleri olarak almak için `enums` metodunu kullanabilirsiniz:

```php
use App\Enums\Product;

$products = $request->enums('products', Product::class);
```

---

#### Dinamik Özelliklerle Girdi Alma

Kullanıcı girdisine `Illuminate\Http\Request` örneği üzerindeki dinamik özellikleri kullanarak da erişebilirsiniz. Örneğin, uygulamanızdaki bir formda `name` alanı varsa, bu alanın değerine şu şekilde erişebilirsiniz:

```php
$name = $request->name;
```

Dinamik özellikler kullanıldığında, Laravel önce parametrenin değerini istek yükünde arar. Eğer bulunamazsa, Laravel bu alanı eşleşen rotanın parametrelerinde arar.

---

#### Girdi Verisinin Bir Kısmını Alma

Girdi verisinin bir alt kümesini almanız gerekiyorsa, `only` ve `except` metodlarını kullanabilirsiniz. Her iki metod da tek bir dizi veya dinamik argüman listesi kabul eder:

```php
$input = $request->only(['username', 'password']);

$input = $request->only('username', 'password');

$input = $request->except(['credit_card']);

$input = $request->except('credit_card');
```

`only` metodu, talep ettiğiniz tüm anahtar / değer çiftlerini döndürür; ancak istekte mevcut olmayan anahtar / değer çiftlerini döndürmez.

---

### Girdi Varlığı

Bir değerin istek üzerinde mevcut olup olmadığını belirlemek için `has` metodunu kullanabilirsiniz. `has` metodu, değer istek üzerinde mevcutsa `true` döndürür:

```php
if ($request->has('name')) {
    // ...
}
```

Bir dizi verildiğinde, `has` metodu belirtilen tüm değerlerin mevcut olup olmadığını belirler:

```php
if ($request->has(['name', 'email'])) {
    // ...
}
```

`hasAny` metodu, belirtilen değerlerden herhangi biri mevcutsa `true` döndürür:

```php
if ($request->hasAny(['name', 'email'])) {
    // ...
}
```

`whenHas` metodu, bir değer istekte mevcutsa verilen kapanışı çalıştırır:

```php
$request->whenHas('name', function (string $input) {
    // ...
});
```

`whenHas` metoduna, belirtilen değer istekte mevcut değilse çalıştırılacak ikinci bir kapanış da geçilebilir:

```php
$request->whenHas('name', function (string $input) {
    // "name" değeri mevcut...
}, function () {
    // "name" değeri mevcut değil...
});
```

Bir değerin istekte mevcut olup olmadığını ve boş bir string olmadığını belirlemek için `filled` metodunu kullanabilirsiniz:

```php
if ($request->filled('name')) {
    // ...
}
```

Bir değerin istekte eksik olup olmadığını veya boş bir string olup olmadığını belirlemek için `isNotFilled` metodunu kullanabilirsiniz:

```php
if ($request->isNotFilled('name')) {
    // ...
}
```

Bir dizi verildiğinde, `isNotFilled` metodu belirtilen tüm değerlerin eksik veya boş olup olmadığını belirler:

```php
if ($request->isNotFilled(['name', 'email'])) {
    // ...
}
```

`anyFilled` metodu, belirtilen değerlerden herhangi biri boş bir string değilse `true` döndürür:

```php
if ($request->anyFilled(['name', 'email'])) {
    // ...
}
```

`whenFilled` metodu, bir değer istekte mevcutsa ve boş bir string değilse verilen kapanışı çalıştırır:

```php
$request->whenFilled('name', function (string $input) {
    // ...
});
```

`whenFilled` metoduna, belirtilen değerin “dolu” olmaması durumunda çalıştırılacak ikinci bir kapanış da geçilebilir:

```php
$request->whenFilled('name', function (string $input) {
    // "name" değeri dolu...
}, function () {
    // "name" değeri dolu değil...
});
```

Belirli bir anahtarın istekten eksik olup olmadığını belirlemek için `missing` ve `whenMissing` metodlarını kullanabilirsiniz:

```php
if ($request->missing('name')) {
    // ...
}

$request->whenMissing('name', function () {
    // "name" değeri eksik...
}, function () {
    // "name" değeri mevcut...
});
```
## Ek Girdi Birleştirme

Bazen mevcut isteğin girdi verilerine manuel olarak ek veri birleştirmeniz gerekebilir. Bunu gerçekleştirmek için `merge` metodunu kullanabilirsiniz. Eğer verilen bir girdi anahtarı zaten istek içinde mevcutsa, bu anahtar `merge` metoduna sağladığınız verilerle üzerine yazılacaktır:

```php
$request->merge(['votes' => 0]);
```

`mergeIfMissing` metodu, yalnızca ilgili anahtarlar isteğin girdi verileri içinde halihazırda mevcut değilse girdiyi birleştirmek için kullanılabilir:

```php
$request->mergeIfMissing(['votes' => 0]);
```

---

## Eski Girdi

Laravel, bir istekteki girdiyi bir sonraki istek sırasında korumanıza olanak tanır. Bu özellik, özellikle doğrulama hataları tespit edildikten sonra formları yeniden doldurmak için kullanışlıdır. Ancak, Laravel’in dahili doğrulama özelliklerini kullanıyorsanız, bu oturum girdi “flashlama” metodlarını manuel olarak çağırmanıza gerek kalmayabilir, çünkü Laravel’in bazı yerleşik doğrulama mekanizmaları bunları otomatik olarak çağırır.

---

### Girdiyi Oturuma Flashlamak

`Illuminate\Http\Request` sınıfındaki `flash` metodu, mevcut girdiyi oturuma flashlar; böylece kullanıcı uygulamaya bir sonraki isteğini gönderdiğinde bu girdi kullanılabilir hale gelir:

```php
$request->flash();
```

Ayrıca, istekteki verilerin yalnızca bir alt kümesini oturuma flashlamak için `flashOnly` ve `flashExcept` metodlarını da kullanabilirsiniz. Bu metodlar, parolalar gibi hassas bilgilerin oturum dışında tutulması için faydalıdır:

```php
$request->flashOnly(['username', 'email']);

$request->flashExcept('password');
```

---

### Girdiyi Flashlayıp Yeniden Yönlendirme

Genellikle girdiyi oturuma flashlamak ve ardından önceki sayfaya yönlendirmek isteyeceksiniz. Bunu kolayca yapmak için, `withInput` metodunu kullanarak bir yönlendirmeye girdi flashlamayı zincirleyebilirsiniz:

```php
return redirect('/form')->withInput();

return redirect()->route('user.create')->withInput();

return redirect('/form')->withInput(
    $request->except('password')
);
```

---

### Eski Girdiyi Alma

Önceki istekten flashlanan girdiyi almak için, `Illuminate\Http\Request` örneği üzerinde `old` metodunu çağırın. `old` metodu, daha önce oturuma flashlanmış girdi verilerini çeker:

```php
$username = $request->old('username');
```

Laravel ayrıca küresel bir `old` helper sağlar. Eğer bir Blade şablonunda eski girdiyi görüntülüyorsanız, formu yeniden doldurmak için `old` helper’ı kullanmak daha uygundur. Verilen alan için herhangi bir eski girdi mevcut değilse, `null` döndürülür:

```html
<input type="text" name="username" value="{{ old('username') }}">
```

---

## Çerezler

### İsteklerden Çerezleri Alma

Laravel framework’ü tarafından oluşturulan tüm çerezler şifrelenir ve bir kimlik doğrulama koduyla imzalanır. Bu, istemci tarafından değiştirilmiş çerezlerin geçersiz sayılacağı anlamına gelir. Bir istekteki çerez değerini almak için, `Illuminate\Http\Request` örneği üzerinde `cookie` metodunu kullanabilirsiniz:

```php
$value = $request->cookie('name');
```

---

## Girdi Kırpma ve Normalizasyon

Varsayılan olarak Laravel, uygulamanızın global middleware yığınında `Illuminate\Foundation\Http\Middleware\TrimStrings` ve `Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull` middleware’lerini içerir. Bu middleware’ler, gelen tüm string alanları otomatik olarak kırpar ve boş string alanları `null`’a dönüştürür. Bu sayede, yönlendirmelerde ve denetleyicilerde bu normalizasyon işlemleriyle uğraşmanıza gerek kalmaz.

---

### Girdi Normalizasyonunu Devre Dışı Bırakma

Bu davranışı tüm istekler için devre dışı bırakmak istiyorsanız, `bootstrap/app.php` dosyanızda `$middleware->remove` metodunu çağırarak bu iki middleware’i uygulamanızın middleware yığınından kaldırabilirsiniz:

```php
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\TrimStrings;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->remove([
        ConvertEmptyStringsToNull::class,
        TrimStrings::class,
    ]);
})
```

---

Belirli isteklerin bir alt kümesi için string kırpma ve boş string dönüştürmeyi devre dışı bırakmak isterseniz, uygulamanızın `bootstrap/app.php` dosyasında `trimStrings` ve `convertEmptyStringsToNull` middleware metodlarını kullanabilirsiniz. Her iki metod da, normalizasyonun atlanıp atlanmaması gerektiğini belirten `true` veya `false` döndüren closure dizilerini kabul eder:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->convertEmptyStringsToNull(except: [
        fn (Request $request) => $request->is('admin/*'),
    ]);

    $middleware->trimStrings(except: [
        fn (Request $request) => $request->is('admin/*'),
    ]);
})
```
## Dosyalar

### Yüklenen Dosyaları Alma

Yüklenen dosyaları bir `Illuminate\Http\Request` örneğinden `file` metodu veya dinamik özellikler kullanarak alabilirsiniz. `file` metodu, PHP’nin `SplFileInfo` sınıfını genişleten ve dosyayla etkileşim kurmak için çeşitli metodlar sağlayan `Illuminate\Http\UploadedFile` sınıfının bir örneğini döndürür:

```php
$file = $request->file('photo');

$file = $request->photo;
```

Bir dosyanın istekte mevcut olup olmadığını `hasFile` metoduyla belirleyebilirsiniz:

```php
if ($request->hasFile('photo')) {
    // ...
}
```

---

### Başarılı Yüklemeleri Doğrulama

Dosyanın mevcut olup olmadığını kontrol etmenin yanı sıra, dosyanın yüklenmesinde herhangi bir sorun yaşanmadığını `isValid` metodu ile doğrulayabilirsiniz:

```php
if ($request->file('photo')->isValid()) {
    // ...
}
```

---

### Dosya Yolları ve Uzantıları

`UploadedFile` sınıfı ayrıca dosyanın tam yoluna ve uzantısına erişmek için metodlar içerir. `extension` metodu, dosyanın içeriğine dayanarak uzantısını tahmin etmeye çalışır. Bu uzantı, istemci tarafından sağlanan uzantıdan farklı olabilir:

```php
$path = $request->photo->path();

$extension = $request->photo->extension();
```

---

### Diğer Dosya Metodları

`UploadedFile` örneklerinde kullanılabilecek birçok başka metod vardır. Bu metodlarla ilgili daha fazla bilgi için sınıfın API dokümantasyonuna göz atın.

---

## Yüklenen Dosyaları Depolama

Yüklenen bir dosyayı depolamak için genellikle yapılandırılmış dosya sistemlerinden birini kullanırsınız. `UploadedFile` sınıfı, yüklenen dosyayı yerel dosya sisteminizdeki bir konuma veya Amazon S3 gibi bulut depolama alanına taşıyacak bir `store` metoduna sahiptir.

`store` metodu, dosyanın dosya sisteminin yapılandırılmış kök dizinine göre nereye kaydedileceğini belirten bir yol alır. Bu yol, bir dosya adı içermemelidir; çünkü benzersiz bir kimlik otomatik olarak dosya adı olarak oluşturulur.

`store` metodu ayrıca, dosyanın depolanacağı diskin adını belirlemek için isteğe bağlı bir ikinci argüman da kabul eder. Metod, dosyanın disk köküne göre yolunu döndürür:

```php
$path = $request->photo->store('images');

$path = $request->photo->store('images', 's3');
```

Otomatik olarak oluşturulmuş bir dosya adı istemiyorsanız, `storeAs` metodunu kullanabilirsiniz. Bu metod, yol, dosya adı ve disk adını argüman olarak kabul eder:

```php
$path = $request->photo->storeAs('images', 'filename.jpg');

$path = $request->photo->storeAs('images', 'filename.jpg', 's3');
```

Laravel’de dosya depolama hakkında daha fazla bilgi için, dosya depolama belgelerine göz atın.

---

## Güvenilen Proxy’leri Yapılandırma

TLS / SSL sertifikalarını sonlandıran bir yük dengeleyicisinin (load balancer) arkasında uygulamanızı çalıştırırken, bazen `url` helper’ı kullanıldığında uygulamanızın HTTPS bağlantıları üretmediğini fark edebilirsiniz. Bu genellikle, uygulamanızın yük dengeleyiciden 80 numaralı port üzerinden iletilen trafiği alması ve güvenli bağlantılar oluşturması gerektiğini bilmemesinden kaynaklanır.

Bunu çözmek için, Laravel uygulamanızda bulunan `Illuminate\Http\Middleware\TrustProxies` middleware’ini etkinleştirebilirsiniz. Bu middleware, uygulamanız tarafından güvenilmesi gereken proxy’leri veya yük dengeleyicileri hızlı bir şekilde özelleştirmenizi sağlar. Güvenilen proxy’ler, uygulamanızın `bootstrap/app.php` dosyasındaki `trustProxies` middleware metodu aracılığıyla belirtilmelidir:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: [
        '192.168.1.1',
        '10.0.0.0/8',
    ]);
})
```

Güvenilen proxy’leri yapılandırmanın yanı sıra, güvenilecek proxy başlıklarını (headers) da yapılandırabilirsiniz:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(headers: Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB
    );
})
```

AWS Elastic Load Balancing kullanıyorsanız, `headers` değeri `Request::HEADER_X_FORWARDED_AWS_ELB` olmalıdır. Yük dengeleyiciniz RFC 7239’daki standart `Forwarded` başlığını kullanıyorsa, `headers` değeri `Request::HEADER_FORWARDED` olmalıdır. `headers` değeri içinde kullanılabilecek sabitler hakkında daha fazla bilgi için Symfony’nin proxy güveni dokümantasyonuna göz atın.

---

### Tüm Proxy’lere Güvenmek

Amazon AWS veya başka bir “bulut” yük dengeleyici sağlayıcısı kullanıyorsanız, gerçek yük dengeleyicilerinizin IP adreslerini bilmeyebilirsiniz. Bu durumda, tüm proxy’lere güvenmek için `*` kullanabilirsiniz:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');
})
```

---

## Güvenilen Host’ları Yapılandırma

Varsayılan olarak, Laravel aldığı tüm isteklere, HTTP isteğinin `Host` header içeriğine bakılmaksızın yanıt verir. Ayrıca, `Host` header’ın değeri, bir web isteği sırasında uygulamanız için mutlak URL’ler oluşturulurken kullanılır.

Genellikle, web sunucunuzu (örneğin Nginx veya Apache) yalnızca belirli bir hostname ile eşleşen istekleri uygulamanıza gönderecek şekilde yapılandırmalısınız. Ancak, web sunucunuzu doğrudan özelleştirme imkanınız yoksa ve Laravel’e yalnızca belirli hostname’lere yanıt vermesini söylemeniz gerekiyorsa, `Illuminate\Http\Middleware\TrustHosts` middleware’ini uygulamanız için etkinleştirebilirsiniz.

`TrustHosts` middleware’ini etkinleştirmek için, uygulamanızın `bootstrap/app.php` dosyasında `trustHosts` middleware metodunu çağırmalısınız. Bu metodun `at` argümanını kullanarak, uygulamanızın yanıt vereceği host adlarını belirtebilirsiniz. Diğer `Host` header değerlerine sahip gelen istekler reddedilecektir:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustHosts(at: ['laravel.test']);
})
```

Varsayılan olarak, uygulamanızın URL’sinin alt alan adlarından gelen istekler de otomatik olarak güvenilir kabul edilir. Bu davranışı devre dışı bırakmak isterseniz, `subdomains` argümanını kullanabilirsiniz:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustHosts(at: ['laravel.test'], subdomains: false);
})
```

Uygulamanızın yapılandırma dosyalarına veya veritabanına erişmeniz gerekiyorsa, güvenilir host’larınızı belirlemek için `at` argümanına bir closure sağlayabilirsiniz:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustHosts(at: fn () => config('app.trusted_hosts'));
})
```

