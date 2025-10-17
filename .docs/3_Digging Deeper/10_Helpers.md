# Helpers


<br>




## Introduction

Laravel, çeşitli global "helper" PHP fonksiyonları içerir. Bu fonksiyonların birçoğu framework'ün kendisi tarafından kullanılır; ancak, bunları kullanışlı bulursanız kendi uygulamalarınızda da özgürce kullanabilirsiniz.

<br>




## Available Methods

### Arrays & Objects
Arr::accessible  
Arr::add  
Arr::array  
Arr::boolean  
Arr::collapse  
Arr::crossJoin  
Arr::divide  
Arr::dot  
Arr::every  
Arr::except  
Arr::exists  
Arr::first  
Arr::flatten  
Arr::float  
Arr::forget  
Arr::from  
Arr::get  
Arr::has  
Arr::hasAll  
Arr::hasAny  
Arr::integer  
Arr::isAssoc  
Arr::isList  
Arr::join  
Arr::keyBy  
Arr::last  
Arr::map  
Arr::mapSpread  
Arr::mapWithKeys  
Arr::only  
Arr::partition  
Arr::pluck  
Arr::prepend  
Arr::prependKeysWith  
Arr::pull  
Arr::push  
Arr::query  
Arr::random  
Arr::reject  
Arr::select  
Arr::set  
Arr::shuffle  
Arr::sole  
Arr::some  
Arr::sort  
Arr::sortDesc  
Arr::sortRecursive  
Arr::string  
Arr::take  
Arr::toCssClasses  
Arr::toCssStyles  
Arr::undot  
Arr::where  
Arr::whereNotNull  
Arr::wrap  
data_fill  
data_get  
data_set  
data_forget  
head  
last  

### Numbers
Number::abbreviate  
Number::clamp  
Number::currency  
Number::defaultCurrency  
Number::defaultLocale  
Number::fileSize  
Number::forHumans  
Number::format  
Number::ordinal  
Number::pairs  
Number::parseInt  
Number::parseFloat  
Number::percentage  
Number::spell  
Number::spellOrdinal  
Number::trim  
Number::useLocale  
Number::withLocale  
Number::useCurrency  
Number::withCurrency  

### Paths
app_path  
base_path  
config_path  
database_path  
lang_path  
public_path  
resource_path  
storage_path  

### URLs
action  
asset  
route  
secure_asset  
secure_url  
to_action  
to_route  
uri  
url  

### Miscellaneous
abort  
abort_if  
abort_unless  
app  
auth  
back  
bcrypt  
blank  
broadcast  
broadcast_if  
broadcast_unless  
cache  
class_uses_recursive  
collect  
config  
context  
cookie  
csrf_field  
csrf_token  
decrypt  
dd  
dispatch  
dispatch_sync  
dump  
encrypt  
env  
event  
fake  
filled  
info  
literal  
logger  
method_field  
now  
old  
once  
optional  
policy  
redirect  
report  
report_if  
report_unless  
request  
rescue  
resolve  
response  
retry  
session  
tap  
throw_if  
throw_unless  
today  
trait_uses_recursive  
transform  
validator  
value  
view  
with  
when  

<br>




## Arrays & Objects

### Arr::accessible()
Arr::accessible metodu, verilen değerin array olarak erişilebilir olup olmadığını belirler:

```php
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
 
$isAccessible = Arr::accessible(['a' => 1, 'b' => 2]);
// true
 
$isAccessible = Arr::accessible(new Collection);
// true
 
$isAccessible = Arr::accessible('abc');
// false
 
$isAccessible = Arr::accessible(new stdClass);
// false
````

### Arr::add()

Arr::add metodu, verilen anahtar zaten array içinde mevcut değilse veya null ise belirtilen anahtar / değer çiftini array’e ekler:

```php
use Illuminate\Support\Arr;
 
$array = Arr::add(['name' => 'Desk'], 'price', 100);
// ['name' => 'Desk', 'price' => 100]
 
$array = Arr::add(['name' => 'Desk', 'price' => null], 'price', 100);
// ['name' => 'Desk', 'price' => 100]
```

### Arr::array()

Arr::array metodu, "dot" notasyonu kullanarak çok katmanlı bir array’den bir değeri alır (Arr::get() ile aynı şekilde), ancak istenen değer bir array değilse InvalidArgumentException fırlatır:

```php
use Illuminate\Support\Arr;
 
$array = ['name' => 'Joe', 'languages' => ['PHP', 'Ruby']];
 
$value = Arr::array($array, 'languages');
// ['PHP', 'Ruby']
 
$value = Arr::array($array, 'name');
// throws InvalidArgumentException
```

### Arr::boolean()

Arr::boolean metodu, "dot" notasyonu kullanarak çok katmanlı bir array’den bir değeri alır (Arr::get() ile aynı şekilde), ancak istenen değer boolean değilse InvalidArgumentException fırlatır:

```php
use Illuminate\Support\Arr;
 
$array = ['name' => 'Joe', 'available' => true];
 
$value = Arr::boolean($array, 'available');
// true
 
$value = Arr::boolean($array, 'name');
// throws InvalidArgumentException
```

### Arr::collapse()

Arr::collapse metodu, array’lerin veya Collection’ların bir array’ini tek bir array haline getirir:

```php
use Illuminate\Support\Arr;
 
$array = Arr::collapse([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);
// [1, 2, 3, 4, 5, 6, 7, 8, 9]
```

### Arr::crossJoin()

Arr::crossJoin metodu, verilen array’leri çapraz olarak birleştirir ve tüm olası permütasyonları döndürür (Cartesian product):

```php
use Illuminate\Support\Arr;
 
$matrix = Arr::crossJoin([1, 2], ['a', 'b']);
/*
    [
        [1, 'a'],
        [1, 'b'],
        [2, 'a'],
        [2, 'b'],
    ]
*/
 
$matrix = Arr::crossJoin([1, 2], ['a', 'b'], ['I', 'II']);
/*
    [
        [1, 'a', 'I'],
        [1, 'a', 'II'],
        [1, 'b', 'I'],
        [1, 'b', 'II'],
        [2, 'a', 'I'],
        [2, 'a', 'II'],
        [2, 'b', 'I'],
        [2, 'b', 'II'],
    ]
*/
```

### Arr::divide()

Arr::divide metodu, verilen array’in anahtarlarını ve değerlerini iki ayrı array olarak döndürür:

```php
use Illuminate\Support\Arr;
 
[$keys, $values] = Arr::divide(['name' => 'Desk']);
// $keys: ['name']
// $values: ['Desk']
```

### Arr::dot()

Arr::dot metodu, çok boyutlu bir array’i "dot" notasyonu kullanarak düzleştirir:

```php
use Illuminate\Support\Arr;
 
$array = ['products' => ['desk' => ['price' => 100]]];
 
$flattened = Arr::dot($array);
// ['products.desk.price' => 100]
```

```
```

<br>




### Arr::some()
Arr::some metodu, array içindeki en az bir değerin verilen doğruluk testini geçtiğinden emin olur:

```php
use Illuminate\Support\Arr;
 
$array = [1, 2, 3];
 
Arr::some($array, fn ($i) => $i > 2);
 
// true
````

<br>




### Arr::sort()
Arr::sort metodu, bir array’i değerlerine göre sıralar:

```php
use Illuminate\Support\Arr;
 
$array = ['Desk', 'Table', 'Chair'];
 
$sorted = Arr::sort($array);
 
// ['Chair', 'Desk', 'Table']
```

Ayrıca, bir closure kullanarak array’i özel bir değere göre sıralayabilirsiniz:

```php
use Illuminate\Support\Arr;
 
$array = [
    ['name' => 'Desk'],
    ['name' => 'Table'],
    ['name' => 'Chair'],
];
 
$sorted = array_values(Arr::sort($array, function (array $value) {
    return $value['name'];
}));
 
/*
    [
        ['name' => 'Chair'],
        ['name' => 'Desk'],
        ['name' => 'Table'],
    ]
*/
```

<br>




### Arr::sortDesc()
Arr::sortDesc metodu, bir array’i değerlerine göre azalan sırada sıralar:

```php
use Illuminate\Support\Arr;
 
$array = ['Desk', 'Table', 'Chair'];
 
$sorted = Arr::sortDesc($array);
 
// ['Table', 'Desk', 'Chair']
```

Closure kullanarak da sıralama yapabilirsiniz:

```php
use Illuminate\Support\Arr;
 
$array = [
    ['name' => 'Desk'],
    ['name' => 'Table'],
    ['name' => 'Chair'],
];
 
$sorted = array_values(Arr::sortDesc($array, function (array $value) {
    return $value['name'];
}));
 
/*
    [
        ['name' => 'Table'],
        ['name' => 'Desk'],
        ['name' => 'Chair'],
    ]
*/
```

<br>




### Arr::sortRecursive()
Arr::sortRecursive metodu, bir array’i özyinelemeli (recursive) olarak sıralar. Sayısal indeksli alt diziler için `sort`, ilişkisel diziler (associative arrays) için `ksort` kullanır:

```php
use Illuminate\Support\Arr;
 
$array = [
    ['Roman', 'Taylor', 'Li'],
    ['PHP', 'Ruby', 'JavaScript'],
    ['one' => 1, 'two' => 2, 'three' => 3],
];
 
$sorted = Arr::sortRecursive($array);
 
/*
    [
        ['JavaScript', 'PHP', 'Ruby'],
        ['one' => 1, 'three' => 3, 'two' => 2],
        ['Li', 'Roman', 'Taylor'],
    ]
*/
```

Sonuçları azalan sırada sıralamak isterseniz `Arr::sortRecursiveDesc` metodunu kullanabilirsiniz:

```php
$sorted = Arr::sortRecursiveDesc($array);
```

<br>




### Arr::string()
Arr::string metodu, "dot" notasyonu kullanarak çok katmanlı bir array’den bir değeri alır (Arr::get() gibi), ancak istenen değer string değilse InvalidArgumentException fırlatır:

```php
use Illuminate\Support\Arr;
 
$array = ['name' => 'Joe', 'languages' => ['PHP', 'Ruby']];
 
$value = Arr::string($array, 'name');
 
// Joe
 
$value = Arr::string($array, 'languages');
 
// throws InvalidArgumentException
```

<br>




### Arr::take()
Arr::take metodu, belirtilen sayıda öğe içeren yeni bir array döndürür:

```php
use Illuminate\Support\Arr;
 
$array = [0, 1, 2, 3, 4, 5];
 
$chunk = Arr::take($array, 3);
 
// [0, 1, 2]
```

Negatif bir sayı geçerek, belirtilen sayıda öğeyi array’in sonundan alabilirsiniz:

```php
$array = [0, 1, 2, 3, 4, 5];
 
$chunk = Arr::take($array, -2);
 
// [4, 5]
```

<br>




### Arr::toCssClasses()
Arr::toCssClasses metodu, koşullu olarak bir CSS class string’i derler. Bu metod, anahtarın class adını, değerin ise boolean bir ifadeyi temsil ettiği bir array alır. Anahtar sayısal ise, o öğe her zaman class listesine dahil edilir:

```php
use Illuminate\Support\Arr;
 
$isActive = false;
$hasError = true;
 
$array = ['p-4', 'font-bold' => $isActive, 'bg-red' => $hasError];
 
$classes = Arr::toCssClasses($array);
 
/*
    'p-4 bg-red'
*/
```

<br>




### Arr::toCssStyles()
Arr::toCssStyles metodu, koşullu olarak bir CSS style string’i derler. Bu metod, anahtarın CSS stilini, değerin ise boolean bir ifadeyi temsil ettiği bir array alır. Anahtar sayısal ise, o stil her zaman dahil edilir:

```php
use Illuminate\Support\Arr;
 
$hasColor = true;
 
$array = ['background-color: blue', 'color: blue' => $hasColor];
 
$classes = Arr::toCssStyles($array);
 
/*
    'background-color: blue; color: blue;'
*/
```

Bu metod, Blade component attribute bag’i ile class birleştirme ve `@class` Blade directive fonksiyonelliğini sağlar.

<br>




### Arr::undot()
Arr::undot metodu, "dot" notasyonu kullanan tek katmanlı bir array’i çok boyutlu bir array’e genişletir:

```php
use Illuminate\Support\Arr;
 
$array = [
    'user.name' => 'Kevin Malone',
    'user.occupation' => 'Accountant',
];
 
$array = Arr::undot($array);
 
// ['user' => ['name' => 'Kevin Malone', 'occupation' => 'Accountant']]
```

<br>




### Arr::where()
Arr::where metodu, verilen closure’a göre bir array’i filtreler:

```php
use Illuminate\Support\Arr;
 
$array = [100, '200', 300, '400', 500];
 
$filtered = Arr::where($array, function (string|int $value, int $key) {
    return is_string($value);
});
 
// [1 => '200', 3 => '400']
```

<br>




### Arr::whereNotNull()
Arr::whereNotNull metodu, verilen array’deki tüm null değerleri kaldırır:

```php
use Illuminate\Support\Arr;
 
$array = [0, null];
 
$filtered = Arr::whereNotNull($array);
 
// [0 => 0]
```

<br>




### Arr::wrap()
Arr::wrap metodu, verilen değeri bir array içerisine sarar. Eğer verilen değer zaten bir array ise, aynı şekilde döndürülür:

```php
use Illuminate\Support\Arr;
 
$string = 'Laravel';
 
$array = Arr::wrap($string);
 
// ['Laravel']
```

Eğer verilen değer `null` ise, boş bir array döndürülür:

```php
use Illuminate\Support\Arr;
 
$array = Arr::wrap(null);
 
// []
```


<br>




### Number::format()
Number::format metodu, verilen sayıyı belirtilen locale’e özgü bir biçimde formatlar:

```php
use Illuminate\Support\Number;
 
$number = Number::format(100000);
// 100,000
 
$number = Number::format(100000, precision: 2);
// 100,000.00
 
$number = Number::format(100000.123, maxPrecision: 2);
// 100,000.12
 
$number = Number::format(100000, locale: 'de');
// 100.000
````

<br>




### Number::ordinal()
Number::ordinal metodu, bir sayının sıra (ordinal) biçimini döndürür:

```php
use Illuminate\Support\Number;
 
$number = Number::ordinal(1);
// 1st
 
$number = Number::ordinal(2);
// 2nd
 
$number = Number::ordinal(21);
// 21st
```

<br>




### Number::pairs()
Number::pairs metodu, belirtilen aralık ve adım değerine göre sayı çiftleri (alt aralıklar) oluşturur. Bu metod, büyük bir sayı aralığını sayfa numaralandırma veya toplu görevler için küçük alt aralıklara bölmek için kullanılabilir. Dönüş değeri, her biri bir sayı aralığını temsil eden iç içe dizilerden oluşan bir array’dir:

```php
use Illuminate\Support\Number;
 
$result = Number::pairs(25, 10);
// [[0, 9], [10, 19], [20, 25]]
 
$result = Number::pairs(25, 10, offset: 0);
// [[0, 10], [10, 20], [20, 25]]
```

<br>




### Number::parseInt()
Number::parseInt metodu, verilen string ifadeyi belirtilen locale’e göre bir integer’a dönüştürür:

```php
use Illuminate\Support\Number;
 
$result = Number::parseInt('10.123');
// (int) 10
 
$result = Number::parseInt('10,123', locale: 'fr');
// (int) 10
```

<br>




### Number::parseFloat()
Number::parseFloat metodu, verilen string ifadeyi belirtilen locale’e göre float’a dönüştürür:

```php
use Illuminate\Support\Number;
 
$result = Number::parseFloat('10');
// (float) 10.0
 
$result = Number::parseFloat('10', locale: 'fr');
// (float) 10.0
```

<br>




### Number::percentage()
Number::percentage metodu, verilen değerin yüzde biçiminde bir string temsilini döndürür:

```php
use Illuminate\Support\Number;
 
$percentage = Number::percentage(10);
// 10%
 
$percentage = Number::percentage(10, precision: 2);
// 10.00%
 
$percentage = Number::percentage(10.123, maxPrecision: 2);
// 10.12%
 
$percentage = Number::percentage(10, precision: 2, locale: 'de');
// 10,00%
```

<br>




### Number::spell()
Number::spell metodu, verilen sayıyı kelime biçiminde string olarak döndürür:

```php
use Illuminate\Support\Number;
 
$number = Number::spell(102);
// one hundred and two
 
$number = Number::spell(88, locale: 'fr');
// quatre-vingt-huit
```

`after` argümanı, belirli bir değerden sonra tüm sayıların kelimeyle yazılmasını belirlemenizi sağlar:

```php
$number = Number::spell(10, after: 10);
// 10
 
$number = Number::spell(11, after: 10);
// eleven
```

`until` argümanı ise, belirli bir değere kadar tüm sayıların kelimeyle yazılmasını sağlar:

```php
$number = Number::spell(5, until: 10);
// five
 
$number = Number::spell(10, until: 10);
// 10
```

<br>




### Number::spellOrdinal()
Number::spellOrdinal metodu, sayının sıra (ordinal) biçimini kelime olarak döndürür:

```php
use Illuminate\Support\Number;
 
$number = Number::spellOrdinal(1);
// first
 
$number = Number::spellOrdinal(2);
// second
 
$number = Number::spellOrdinal(21);
// twenty-first
```

<br>




### Number::trim()
Number::trim metodu, verilen sayının ondalık kısmındaki gereksiz sıfırları kaldırır:

```php
use Illuminate\Support\Number;
 
$number = Number::trim(12.0);
// 12
 
$number = Number::trim(12.30);
// 12.3
```

<br>




### Number::useLocale()
Number::useLocale metodu, Number sınıfının sonraki işlemlerinde kullanılacak varsayılan locale değerini global olarak ayarlar:

```php
use Illuminate\Support\Number;
 
/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Number::useLocale('de');
}
```

<br>




### Number::withLocale()
Number::withLocale metodu, belirtilen locale değeriyle verilen closure’ı çalıştırır ve ardından orijinal locale’i geri yükler:

```php
use Illuminate\Support\Number;
 
$number = Number::withLocale('de', function () {
    return Number::format(1500);
});
```

<br>




### Number::useCurrency()
Number::useCurrency metodu, Number sınıfının sonraki işlemlerinde kullanılacak varsayılan para birimini global olarak ayarlar:

```php
use Illuminate\Support\Number;
 
/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Number::useCurrency('GBP');
}
```

<br>




### Number::withCurrency()
Number::withCurrency metodu, belirtilen para birimiyle verilen closure’ı çalıştırır ve ardından orijinal para birimini geri yükler:

```php
use Illuminate\Support\Number;
 
$number = Number::withCurrency('GBP', function () {
    // ...
});
```


<br>




### dispatch_sync()
dispatch_sync fonksiyonu, verilen job’u senkron (sync) queue’ya gönderir, böylece job hemen işlenir:

```php
dispatch_sync(new App\Jobs\SendEmails);
````

<br>




### dump()
dump fonksiyonu, verilen değişkenleri ekrana yazdırır:

```php
dump($value);
 
dump($value1, $value2, $value3, ...);
```

Eğer dump işleminden sonra script’in çalışmasını durdurmak istiyorsanız, `dd` fonksiyonunu kullanın.

<br>




### encrypt()
encrypt fonksiyonu, verilen değeri şifreler. Bu fonksiyon, Crypt facade’ına alternatif olarak kullanılabilir:

```php
$secret = encrypt('my-secret-value');
```

encrypt fonksiyonunun tersi için `decrypt` fonksiyonuna bakın.

<br>




### env()
env fonksiyonu, bir environment değişkeninin değerini alır veya varsayılan bir değer döndürür:

```php
$env = env('APP_ENV');
 
$env = env('APP_ENV', 'production');
```

Deployment sırasında `config:cache` komutunu çalıştırıyorsanız, `env` fonksiyonunu yalnızca configuration dosyaları içinde çağırdığınızdan emin olun. Çünkü configuration cache aktifken `.env` dosyası yüklenmez ve `env` çağrıları yalnızca sistem veya sunucu düzeyinde environment değişkenlerini döndürür ya da `null` verir.

<br>




### event()
event fonksiyonu, verilen event’i ilgili dinleyicilere gönderir:

```php
event(new UserRegistered($user));
```

<br>




### fake()
fake fonksiyonu, container’dan bir Faker singleton’ı çözer. Bu, model factory’lerinde, veritabanı seed işlemlerinde, testlerde veya prototip view oluştururken sahte veri üretmek için kullanışlıdır:

```php
@for ($i = 0; $i < 10; $i++)
    <dl>
        <dt>Name</dt>
        <dd>{{ fake()->name() }}</dd>
 
        <dt>Email</dt>
        <dd>{{ fake()->unique()->safeEmail() }}</dd>
    </dl>
@endfor
```

Varsayılan olarak, `fake` fonksiyonu `config/app.php` dosyasındaki `app.faker_locale` ayarını kullanır. Genellikle bu ayar `.env` dosyasındaki `APP_FAKER_LOCALE` değişkeniyle belirlenir. Ayrıca locale’i doğrudan fonksiyona parametre olarak verebilirsiniz:

```php
fake('nl_NL')->name();
```

<br>




### filled()
filled fonksiyonu, verilen değerin “boş” olmadığını belirler:

```php
filled(0);
filled(true);
filled(false);
// true
 
filled('');
filled('   ');
filled(null);
filled(collect());
// false
```

Bu fonksiyonun tersi için `blank` fonksiyonuna bakın.

<br>




### info()
info fonksiyonu, uygulamanızın log dosyasına bilgi mesajı yazar:

```php
info('Some helpful information!');
```

Ayrıca bağlamsal veri dizisi de iletebilirsiniz:

```php
info('User login attempt failed.', ['id' => $user->id]);
```

<br>




### literal()
literal fonksiyonu, verilen isimli argümanları property olarak içeren yeni bir `stdClass` örneği oluşturur:

```php
$obj = literal(
    name: 'Joe',
    languages: ['PHP', 'Ruby'],
);
 
$obj->name; // 'Joe'
$obj->languages; // ['PHP', 'Ruby']
```

<br>




### logger()
logger fonksiyonu, log dosyasına “debug” seviyesinde bir mesaj yazar:

```php
logger('Debug message');
```

Bağlamsal veri dizisi de eklenebilir:

```php
logger('User has logged in.', ['id' => $user->id]);
```

Eğer hiçbir argüman verilmezse, bir logger instance’ı döner:

```php
logger()->error('You are not allowed here.');
```

<br>




### method_field()
method_field fonksiyonu, formun HTTP metodunu gizli bir input alanında sahte (spoofed) olarak belirtmek için kullanılır:

```blade
<form method="POST">
    {{ method_field('DELETE') }}
</form>
```

<br>




### now()
now fonksiyonu, şu anki zamanı temsil eden yeni bir `Illuminate\Support\Carbon` örneği oluşturur:

```php
$now = now();
```

<br>




### old()
old fonksiyonu, session’a flash’lanmış eski input değerini alır:

```php
$value = old('value');
$value = old('value', 'default');
```

Genellikle ikinci argüman olarak Eloquent model attribute’ları kullanıldığından, Laravel modelin kendisini de alabilir:

```blade
{{ old('name', $user->name) }}
// ile eşdeğer:
{{ old('name', $user) }}
```

<br>




### once()
once fonksiyonu, verilen callback’i bir kez çalıştırır ve sonucu isteğin süresi boyunca bellekte önbelleğe alır. Aynı callback tekrar çağrıldığında önbelleğe alınan değer döner:

```php
function random(): int
{
    return once(function () {
        return random_int(1, 1000);
    });
}

random(); // 123
random(); // 123 (cached result)
```

Bir nesne örneği içinde çalıştırıldığında, sonuç o nesne örneğine özgü olarak önbelleğe alınır:

```php
class NumberService
{
    public function all(): array
    {
        return once(fn () => [1, 2, 3]);
    }
}

$service = new NumberService;
$service->all(); // [1, 2, 3]
$service->all(); // cached result
```

<br>






### optional()
optional fonksiyonu, herhangi bir değeri kabul eder ve bu nesneye ait property veya method’lara güvenli şekilde erişim sağlar. Eğer nesne `null` ise, hata fırlatmak yerine `null` döner:

```php
return optional($user->address)->street;
{!! old('name', optional($user)->name) !!}
```

Ayrıca ikinci argüman olarak bir closure kabul eder; closure yalnızca ilk değer `null` değilse çağrılır:

```php
return optional(User::find($id), function (User $user) {
    return $user->name;
});
```

```
```
