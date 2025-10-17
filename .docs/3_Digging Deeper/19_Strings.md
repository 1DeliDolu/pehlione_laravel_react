# Strings

<br>


## Introduction

Laravel, string değerlerini işlemek için çeşitli fonksiyonlar içerir. Bu fonksiyonların birçoğu framework'ün kendisi tarafından kullanılır; ancak, bunları kendi uygulamalarınızda da kullanmakta özgürsünüz.

<br>


## Available Methods

### Strings

__
class_basename  
e  
preg_replace_array  
Str::after  
Str::afterLast  
Str::apa  
Str::ascii  
Str::before  
Str::beforeLast  
Str::between  
Str::betweenFirst  
Str::camel  
Str::charAt  
Str::chopStart  
Str::chopEnd  
Str::contains  
Str::containsAll  
Str::doesntContain  
Str::doesntEndWith  
Str::doesntStartWith  
Str::deduplicate  
Str::endsWith  
Str::excerpt  
Str::finish  
Str::fromBase64  
Str::headline  
Str::inlineMarkdown  
Str::is  
Str::isAscii  
Str::isJson  
Str::isUlid  
Str::isUrl  
Str::isUuid  
Str::kebab  
Str::lcfirst  
Str::length  
Str::limit  
Str::lower  
Str::markdown  
Str::mask  
Str::match  
Str::matchAll  
Str::orderedUuid  
Str::padBoth  
Str::padLeft  
Str::padRight  
Str::password  
Str::plural  
Str::pluralStudly  
Str::position  
Str::random  
Str::remove  
Str::repeat  
Str::replace  
Str::replaceArray  
Str::replaceFirst  
Str::replaceLast  
Str::replaceMatches  
Str::replaceStart  
Str::replaceEnd  
Str::reverse  
Str::singular  
Str::slug  
Str::snake  
Str::squish  
Str::start  
Str::startsWith  
Str::studly  
Str::substr  
Str::substrCount  
Str::substrReplace  
Str::swap  
Str::take  
Str::title  
Str::toBase64  
Str::transliterate  
Str::trim  
Str::ltrim  
Str::rtrim  
Str::ucfirst  
Str::ucsplit  
Str::upper  
Str::ulid  
Str::unwrap  
Str::uuid  
Str::uuid7  
Str::wordCount  
Str::wordWrap  
Str::words  
Str::wrap  
str  
trans  
trans_choice  

<br>


### Fluent Strings

after  
afterLast  
apa  
append  
ascii  
basename  
before  
beforeLast  
between  
betweenFirst  
camel  
charAt  
classBasename  
chopStart  
chopEnd  
contains  
containsAll  
decrypt  
deduplicate  
dirname  
doesntEndWith  
doesntStartWith  
encrypt  
endsWith  
exactly  
excerpt  
explode  
finish  
fromBase64  
hash  
headline  
inlineMarkdown  
is  
isAscii  
isEmpty  
isNotEmpty  
isJson  
isUlid  
isUrl  
isUuid  
kebab  
lcfirst  
length  
limit  
lower  
markdown  
mask  
match  
matchAll  
isMatch  
newLine  
padBoth  
padLeft  
padRight  
pipe  
plural  
position  
prepend  
remove  
repeat  
replace  
replaceArray  
replaceFirst  
replaceLast  
replaceMatches  
replaceStart  
replaceEnd  
scan  
singular  
slug  
snake  
split  
squish  
start  
startsWith  
stripTags  
studly  
substr  
substrReplace  
swap  
take  
tap  
test  
title  
toBase64  
toHtmlString  
toUri  
transliterate  
trim  
ltrim  
rtrim  
ucfirst  
ucsplit  
unwrap  
upper  
when  
whenContains  
whenContainsAll  
whenDoesntEndWith  
whenDoesntStartWith  
whenEmpty  
whenNotEmpty  
whenStartsWith  
whenEndsWith  
whenExactly  
whenNotExactly  
whenIs  
whenIsAscii  
whenIsUlid  
whenIsUuid  
whenTest  
wordCount  
words  
wrap  

<br>


## Strings

### __()

`__` fonksiyonu, verilen çeviri dizgesini veya çeviri anahtarını dil dosyalarınızı kullanarak çevirir:

```php
echo __('Welcome to our application');

echo __('messages.welcome');
````

Eğer belirtilen çeviri dizgesi veya anahtarı mevcut değilse, `__` fonksiyonu verilen değeri döndürür. Yukarıdaki örnekte, `messages.welcome` çeviri anahtarı mevcut değilse fonksiyon aynı değeri döndürecektir.

<br>


### class_basename()

`class_basename` fonksiyonu, verilen sınıfın namespace’ini kaldırarak sadece sınıf adını döndürür:

```php
$class = class_basename('Foo\Bar\Baz');

// Baz
```

<br>


### e()

`e` fonksiyonu, PHP’nin `htmlspecialchars` fonksiyonunu `double_encode` seçeneği varsayılan olarak `true` olacak şekilde çalıştırır:

```php
echo e('<html>foo</html>');

// &lt;html&gt;foo&lt;/html&gt;
```

<br>


### preg_replace_array()

`preg_replace_array` fonksiyonu, bir string içinde verilen deseni sırayla bir dizi kullanarak değiştirir:

```php
$string = 'The event will take place between :start and :end';

$replaced = preg_replace_array('/:[a-z_]+/', ['8:30', '9:00'], $string);

// The event will take place between 8:30 and 9:00
```

<br>


### Str::after()

`Str::after` metodu, bir string içinde verilen değerden sonraki kısmı döndürür. Eğer değer string içinde bulunmazsa, tüm string döndürülür:

```php
use Illuminate\Support\Str;

$slice = Str::after('This is my name', 'This is');

// ' my name'
```

<br>


### Str::afterLast()

`Str::afterLast` metodu, bir string içinde verilen değerin son geçtiği yerden sonrasını döndürür. Eğer değer bulunmazsa, tüm string döndürülür:

```php
use Illuminate\Support\Str;

$slice = Str::afterLast('App\Http\Controllers\Controller', '\\');

// 'Controller'
```

<br>


### Str::apa()

`Str::apa` metodu, verilen string'i APA yönergelerine uygun başlık biçimine dönüştürür:

```php
use Illuminate\Support\Str;

$title = Str::apa('Creating A Project');

// 'Creating a Project'
```

<br>


### Str::ascii()

`Str::ascii` metodu, string'i ASCII karakterlerine dönüştürmeye çalışır:

```php
use Illuminate\Support\Str;

$slice = Str::ascii('û');

// 'u'
```

<br>


### Str::before()

`Str::before` metodu, verilen değerden önceki kısmı döndürür:

```php
use Illuminate\Support\Str;

$slice = Str::before('This is my name', 'my name');

// 'This is '
```

<br>


### Str::beforeLast()

`Str::beforeLast` metodu, verilen değerin son geçtiği yerden önceki kısmı döndürür:

```php
use Illuminate\Support\Str;

$slice = Str::beforeLast('This is my name', 'is');

// 'This '
```

<br>


### Str::between()

`Str::between` metodu, iki değer arasında kalan kısmı döndürür:

```php
use Illuminate\Support\Str;

$slice = Str::between('This is my name', 'This', 'name');

// ' is my '
```

<br>


### Str::betweenFirst()

`Str::betweenFirst` metodu, iki değer arasında mümkün olan en küçük kısmı döndürür:

```php
use Illuminate\Support\Str;

$slice = Str::betweenFirst('[a] bc [d]', '[', ']');

// 'a'
```

<br>


### Str::camel()

`Str::camel` metodu, verilen string'i `camelCase` biçimine dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::camel('foo_bar');

// 'fooBar'
```

<br>


### Str::charAt()

`Str::charAt` metodu, belirtilen indeksteki karakteri döndürür. Eğer indeks aralık dışında ise `false` döndürülür:

```php
use Illuminate\Support\Str;

$character = Str::charAt('This is my name.', 6);

// 's'
```

<br>


### Str::chopStart()

`Str::chopStart` metodu, verilen değer string’in başında yer alıyorsa, sadece ilk geçtiği yeri kaldırır:

```php
use Illuminate\Support\Str;

$url = Str::chopStart('https://laravel.com', 'https://');

// 'laravel.com'
```

İkinci argüman olarak bir dizi de geçebilirsiniz. Eğer string, dizideki herhangi bir değerle başlıyorsa o değer kaldırılır:

```php
use Illuminate\Support\Str;

$url = Str::chopStart('http://laravel.com', ['https://', 'http://']);

// 'laravel.com'
```

<br>


### Str::chopEnd()

`Str::chopEnd` metodu, verilen değer string’in sonunda yer alıyorsa, sadece son geçtiği yeri kaldırır:

```php
use Illuminate\Support\Str;

$url = Str::chopEnd('app/Models/Photograph.php', '.php');

// 'app/Models/Photograph'
```

İkinci argüman olarak bir dizi de geçebilirsiniz. Eğer string, dizideki herhangi bir değerle bitiyorsa o değer kaldırılır:

```php
use Illuminate\Support\Str;

$url = Str::chopEnd('laravel.com/index.php', ['/index.html', '/index.php']);

// 'laravel.com'
```

<br>


### Str::contains()

`Str::contains` metodu, verilen string’in belirtilen değeri içerip içermediğini belirler. Varsayılan olarak, bu metod büyük/küçük harf duyarlıdır:

```php
use Illuminate\Support\Str;

$contains = Str::contains('This is my name', 'my');

// true
```

Bir dizi değer de geçebilirsiniz; string, dizideki herhangi bir değeri içeriyorsa `true` döner:

```php
use Illuminate\Support\Str;

$contains = Str::contains('This is my name', ['my', 'foo']);

// true
```

Büyük/küçük harf duyarlılığını kapatmak için `ignoreCase` argümanını `true` yapabilirsiniz:

```php
use Illuminate\Support\Str;

$contains = Str::contains('This is my name', 'MY', ignoreCase: true);

// true
```

<br>


### Str::containsAll()

`Str::containsAll` metodu, verilen string’in bir dizideki tüm değerleri içerip içermediğini belirler:

```php
use Illuminate\Support\Str;

$containsAll = Str::containsAll('This is my name', ['my', 'name']);

// true
```

Büyük/küçük harf duyarlılığını kapatmak için `ignoreCase` argümanını `true` yapabilirsiniz:

```php
use Illuminate\Support\Str;

$containsAll = Str::containsAll('This is my name', ['MY', 'NAME'], ignoreCase: true);

// true
```

<br>


### Str::doesntContain()

`Str::doesntContain` metodu, verilen string’in belirtilen değeri **içermediğini** belirler. Varsayılan olarak büyük/küçük harf duyarlıdır:

```php
use Illuminate\Support\Str;

$doesntContain = Str::doesntContain('This is name', 'my');

// true
```

Bir dizi değer de geçebilirsiniz; string, dizideki hiçbir değeri içermiyorsa `true` döner:

```php
use Illuminate\Support\Str;

$doesntContain = Str::doesntContain('This is name', ['my', 'foo']);

// true
```

Büyük/küçük harf duyarlılığını kapatmak için `ignoreCase` argümanını `true` yapabilirsiniz:

```php
use Illuminate\Support\Str;

$doesntContain = Str::doesntContain('This is name', 'MY', ignoreCase: true);

// true
```

```
```

<br>


### Str::deduplicate()

`Str::deduplicate` metodu, verilen string içindeki bir karakterin art arda gelen tekrarlarını tek bir karaktere indirger. Varsayılan olarak, bu metod boşluk karakterlerini tekilleştirir:

```php
use Illuminate\Support\Str;

$result = Str::deduplicate('The   Laravel   Framework');

// The Laravel Framework
````

Farklı bir karakteri tekilleştirmek isterseniz, ikinci argüman olarak o karakteri belirtebilirsiniz:

```php
use Illuminate\Support\Str;

$result = Str::deduplicate('The---Laravel---Framework', '-');

// The-Laravel-Framework
```

<br>


### Str::doesntEndWith()

`Str::doesntEndWith` metodu, verilen string’in belirtilen değerle bitmediğini belirler:

```php
use Illuminate\Support\Str;

$result = Str::doesntEndWith('This is my name', 'dog');

// true
```

Bir dizi değer de geçebilirsiniz; string, bu değerlerden hiçbirisiyle bitmiyorsa `true` döner:

```php
use Illuminate\Support\Str;

$result = Str::doesntEndWith('This is my name', ['this', 'foo']);

// true

$result = Str::doesntEndWith('This is my name', ['name', 'foo']);

// false
```

<br>


### Str::doesntStartWith()

`Str::doesntStartWith` metodu, verilen string’in belirtilen değerle başlamadığını belirler:

```php
use Illuminate\Support\Str;

$result = Str::doesntStartWith('This is my name', 'That');

// true
```

Eğer bir dizi değer geçilirse, `doesntStartWith` metodu string’in bu değerlerden hiçbirisiyle başlamaması durumunda `true` döndürür:

```php
$result = Str::doesntStartWith('This is my name', ['What', 'That', 'There']);

// true
```

<br>


### Str::endsWith()

`Str::endsWith` metodu, verilen string’in belirtilen değerle bitip bitmediğini belirler:

```php
use Illuminate\Support\Str;

$result = Str::endsWith('This is my name', 'name');

// true
```

Bir dizi değer de geçebilirsiniz; string, bu değerlerden herhangi biriyle bitiyorsa `true` döner:

```php
use Illuminate\Support\Str;

$result = Str::endsWith('This is my name', ['name', 'foo']);

// true

$result = Str::endsWith('This is my name', ['this', 'foo']);

// false
```

<br>


### Str::excerpt()

`Str::excerpt` metodu, verilen string içinde belirtilen ifadeyi içeren ilk kısmı çıkarır:

```php
use Illuminate\Support\Str;

$excerpt = Str::excerpt('This is my name', 'my', [
    'radius' => 3
]);

// '...is my na...'
```

`radius` seçeneği (varsayılan olarak 100), kesilmiş string’in her iki yanında kaç karakterin gösterileceğini belirler.

Ayrıca, `omission` seçeneğini kullanarak kesilmiş string’in başına ve sonuna eklenecek dizgeyi belirtebilirsiniz:

```php
use Illuminate\Support\Str;

$excerpt = Str::excerpt('This is my name', 'name', [
    'radius' => 3,
    'omission' => '(...) '
]);

// '(...) my name'
```

<br>


### Str::finish()

`Str::finish` metodu, bir string belirli bir değerle bitmiyorsa, o değeri sonuna ekler:

```php
use Illuminate\Support\Str;

$adjusted = Str::finish('this/string', '/');

// this/string/

$adjusted = Str::finish('this/string/', '/');

// this/string/
```

<br>


### Str::fromBase64()

`Str::fromBase64` metodu, verilen Base64 string’i çözümler:

```php
use Illuminate\Support\Str;

$decoded = Str::fromBase64('TGFyYXZlbA==');

// Laravel
```

<br>


### Str::headline()

`Str::headline` metodu, tire veya alt çizgiyle ayrılmış string’leri kelimelerin ilk harfi büyük olacak şekilde boşlukla ayrılmış hale getirir:

```php
use Illuminate\Support\Str;

$headline = Str::headline('steve_jobs');

// Steve Jobs

$headline = Str::headline('EmailNotificationSent');

// Email Notification Sent
```

<br>


### Str::inlineMarkdown()

`Str::inlineMarkdown` metodu, GitHub uyumlu Markdown içeriğini `CommonMark` kullanarak satır içi HTML’e dönüştürür. Ancak `markdown` metodunun aksine, üretilen HTML'i blok düzeyinde bir element içine sarmaz:

```php
use Illuminate\Support\Str;

$html = Str::inlineMarkdown('**Laravel**');

// <strong>Laravel</strong>
```

<br>


#### Markdown Güvenliği

Varsayılan olarak Markdown, ham HTML kullanımını destekler. Bu da, ham kullanıcı girdisiyle birlikte kullanıldığında XSS (Cross-Site Scripting) açıklarına yol açabilir. CommonMark güvenlik dokümantasyonuna göre, `html_input` seçeneğini kullanarak ham HTML’i kaçırabilir veya silebilir, `allow_unsafe_links` seçeneğiyle de güvensiz bağlantılara izin verilip verilmeyeceğini belirleyebilirsiniz.

Bazı ham HTML’lere izin vermeniz gerekiyorsa, derlenmiş Markdown çıktısını bir HTML Purifier’dan geçirmeniz önerilir:

```php
use Illuminate\Support\Str;

Str::inlineMarkdown('Inject: <script>alert("Hello XSS!");</script>', [
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);

// Inject: alert(&quot;Hello XSS!&quot;);
```

<br>


### Str::is()

`Str::is` metodu, verilen string’in bir pattern ile eşleşip eşleşmediğini belirler. Yıldız karakteri (`*`) joker değer olarak kullanılabilir:

```php
use Illuminate\Support\Str;

$matches = Str::is('foo*', 'foobar');

// true

$matches = Str::is('baz*', 'foobar');

// false
```

Büyük/küçük harf duyarlılığını kapatmak için `ignoreCase` argümanını `true` yapabilirsiniz:

```php
use Illuminate\Support\Str;

$matches = Str::is('*.jpg', 'photo.JPG', ignoreCase: true);

// true
```

<br>


### Str::isAscii()

`Str::isAscii` metodu, verilen string’in 7-bit ASCII olup olmadığını belirler:

```php
use Illuminate\Support\Str;

$isAscii = Str::isAscii('Taylor');

// true

$isAscii = Str::isAscii('ü');

// false
```

<br>


### Str::isJson()

`Str::isJson` metodu, verilen string’in geçerli bir JSON olup olmadığını belirler:

```php
use Illuminate\Support\Str;

$result = Str::isJson('[1,2,3]');

// true

$result = Str::isJson('{"first": "John", "last": "Doe"}');

// true

$result = Str::isJson('{first: "John", last: "Doe"}');

// false
```

<br>


### Str::isUrl()

`Str::isUrl` metodu, verilen string’in geçerli bir URL olup olmadığını belirler:

```php
use Illuminate\Support\Str;

$isUrl = Str::isUrl('http://example.com');

// true

$isUrl = Str::isUrl('laravel');

// false
```

`isUrl` metodu, çok sayıda protokolü geçerli olarak kabul eder. Ancak, yalnızca belirli protokollerin geçerli sayılmasını istiyorsanız, bu protokolleri ikinci parametre olarak geçebilirsiniz:

```php
$isUrl = Str::isUrl('http://example.com', ['http', 'https']);
```

<br>


### Str::isUlid()

`Str::isUlid` metodu, verilen string’in geçerli bir ULID olup olmadığını belirler:

```php
use Illuminate\Support\Str;

$isUlid = Str::isUlid('01gd6r360bp37zj17nxb55yv40');

// true

$isUlid = Str::isUlid('laravel');

// false
```

<br>


### Str::isUuid()

`Str::isUuid` metodu, verilen string’in geçerli bir UUID olup olmadığını belirler:

```php
use Illuminate\Support\Str;

$isUuid = Str::isUuid('a0a2a2d2-0b87-4a18-83f2-2529882be2de');

// true

$isUuid = Str::isUuid('laravel');

// false
```

Ayrıca, verilen UUID’nin belirli bir sürüm (1, 3, 4, 5, 6, 7 veya 8) ile uyumlu olup olmadığını da doğrulayabilirsiniz:

```php
use Illuminate\Support\Str;

$isUuid = Str::isUuid('a0a2a2d2-0b87-4a18-83f2-2529882be2de', version: 4);

// true

$isUuid = Str::isUuid('a0a2a2d2-0b87-4a18-83f2-2529882be2de', version: 1);

// false
```

<br>


### Str::kebab()

`Str::kebab` metodu, verilen string’i `kebab-case` biçimine dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::kebab('fooBar');

// foo-bar
```

```
```

<br>


### Str::lcfirst()

`Str::lcfirst` metodu, verilen string’in ilk karakterini küçük harfe dönüştürür:

```php
use Illuminate\Support\Str;

$string = Str::lcfirst('Foo Bar');

// foo Bar
````

<br>


### Str::length()

`Str::length` metodu, verilen string’in uzunluğunu döndürür:

```php
use Illuminate\Support\Str;

$length = Str::length('Laravel');

// 7
```

<br>


### Str::limit()

`Str::limit` metodu, verilen string’i belirtilen uzunluğa kadar kısaltır:

```php
use Illuminate\Support\Str;

$truncated = Str::limit('The quick brown fox jumps over the lazy dog', 20);

// The quick brown fox...
```

Üçüncü bir argüman olarak, kısaltılmış string’in sonuna eklenecek dizgeyi belirtebilirsiniz:

```php
$truncated = Str::limit('The quick brown fox jumps over the lazy dog', 20, ' (...)');

// The quick brown fox (...)
```

String’i kısaltırken tam kelimeleri korumak istiyorsanız, `preserveWords` argümanını kullanabilirsiniz. Bu argüman `true` olduğunda, kesme işlemi en yakın kelime sınırında yapılır:

```php
$truncated = Str::limit('The quick brown fox', 12, preserveWords: true);

// The quick...
```

<br>


### Str::lower()

`Str::lower` metodu, verilen string’i tamamen küçük harfe dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::lower('LARAVEL');

// laravel
```

<br>


### Str::markdown()

`Str::markdown` metodu, GitHub uyumlu Markdown içeriğini `CommonMark` kullanarak HTML’e dönüştürür:

```php
use Illuminate\Support\Str;

$html = Str::markdown('# Laravel');

// <h1>Laravel</h1>

$html = Str::markdown('# Taylor <b>Otwell</b>', [
    'html_input' => 'strip',
]);

// <h1>Taylor Otwell</h1>
```

<br>


#### Markdown Güvenliği

Varsayılan olarak Markdown, ham HTML’i destekler; bu da, ham kullanıcı girdisiyle kullanıldığında XSS (Cross-Site Scripting) açıklarına neden olabilir.
CommonMark güvenlik belgelerine göre, `html_input` seçeneğiyle ham HTML’i kaçırabilir veya silebilir, `allow_unsafe_links` seçeneğiyle de güvensiz bağlantılara izin verilip verilmeyeceğini belirleyebilirsiniz.

Bazı ham HTML’lere izin vermeniz gerekiyorsa, derlenmiş Markdown çıktısını bir HTML Purifier’dan geçirmeniz önerilir:

```php
use Illuminate\Support\Str;

Str::markdown('Inject: <script>alert("Hello XSS!");</script>', [
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);

// <p>Inject: alert(&quot;Hello XSS!&quot;);</p>
```

<br>


### Str::mask()

`Str::mask` metodu, bir string’in belirli bir kısmını tekrarlanan bir karakterle gizler. Bu metod, e-posta adresleri veya telefon numaraları gibi bilgilerin bir bölümünü gizlemek için kullanılabilir:

```php
use Illuminate\Support\Str;

$string = Str::mask('taylor@example.com', '*', 3);

// tay***************
```

Gizleme işleminin string’in sonundan başlamasını istiyorsanız, üçüncü argüman olarak negatif bir sayı verebilirsiniz:

```php
$string = Str::mask('taylor@example.com', '*', -15, 3);

// tay***@example.com
```

<br>


### Str::match()

`Str::match` metodu, verilen düzenli ifade (regex) desenine uyan string kısmını döndürür:

```php
use Illuminate\Support\Str;

$result = Str::match('/bar/', 'foo bar');

// 'bar'

$result = Str::match('/foo (.*)/', 'foo bar');

// 'bar'
```

<br>


### Str::matchAll()

`Str::matchAll` metodu, verilen düzenli ifade (regex) desenine uyan tüm string bölümlerini bir koleksiyon (collection) olarak döndürür:

```php
use Illuminate\Support\Str;

$result = Str::matchAll('/bar/', 'bar foo bar');

// collect(['bar', 'bar'])
```

Eğer ifade içinde bir eşleşme grubu (matching group) belirtirseniz, Laravel ilk eşleşme grubuna ait sonuçları döndürür:

```php
use Illuminate\Support\Str;

$result = Str::matchAll('/f(\w*)/', 'bar fun bar fly');

// collect(['un', 'ly'])
```

Eşleşme bulunmazsa, boş bir koleksiyon döner.

<br>


### Str::orderedUuid()

`Str::orderedUuid` metodu, “zaman damgası önce” mantığıyla sıralanabilir bir UUID oluşturur. Bu UUID’ler, veritabanı indekslerinde daha verimli şekilde saklanabilir:

```php
use Illuminate\Support\Str;

return (string) Str::orderedUuid();
```

<br>


### Str::padBoth()

`Str::padBoth` metodu, PHP’nin `str_pad` fonksiyonunu kullanarak bir string’i hem sağ hem sol tarafından belirli bir uzunluğa kadar doldurur:

```php
use Illuminate\Support\Str;

$padded = Str::padBoth('James', 10, '_');

// '__James___'

$padded = Str::padBoth('James', 10);

// '  James   '
```

<br>


### Str::padLeft()

`Str::padLeft` metodu, PHP’nin `str_pad` fonksiyonunu kullanarak bir string’in sol tarafını doldurur:

```php
use Illuminate\Support\Str;

$padded = Str::padLeft('James', 10, '-=');

// '-=-=-James'

$padded = Str::padLeft('James', 10);

// '     James'
```

<br>


### Str::padRight()

`Str::padRight` metodu, PHP’nin `str_pad` fonksiyonunu kullanarak bir string’in sağ tarafını doldurur:

```php
use Illuminate\Support\Str;

$padded = Str::padRight('James', 10, '-');

// 'James-----'

$padded = Str::padRight('James', 10);

// 'James     '
```

<br>


### Str::password()

`Str::password` metodu, belirtilen uzunlukta güvenli ve rastgele bir parola oluşturur. Parola, harfler, sayılar, semboller ve boşluklardan oluşur. Varsayılan uzunluk 32 karakterdir:

```php
use Illuminate\Support\Str;

$password = Str::password();

// 'EbJo2vE-AS:U,$%_gkrV4n,q~1xy/-_4'

$password = Str::password(12);

// 'qwuar>#V|i]N'
```

<br>


### Str::plural()

`Str::plural` metodu, verilen kelimeyi çoğul hâline dönüştürür. Bu metod, Laravel’in çoğul yapıcı (pluralizer) tarafından desteklenen tüm dilleri destekler:

```php
use Illuminate\Support\Str;

$plural = Str::plural('car');

// cars

$plural = Str::plural('child');

// children
```

İkinci argüman olarak bir tamsayı geçerek, kelimenin tekil veya çoğul hâlini döndürebilirsiniz:

```php
use Illuminate\Support\Str;

$plural = Str::plural('child', 2);

// children

$singular = Str::plural('child', 1);

// child
```

`prependCount` argümanını kullanarak, çoğullaştırılmış kelimenin önüne biçimlendirilmiş `$count` değerini ekleyebilirsiniz:

```php
use Illuminate\Support\Str;

$label = Str::plural('car', 1000, prependCount: true);

// 1,000 cars
```

<br>


### Str::pluralStudly()

`Str::pluralStudly` metodu, studly case biçimindeki bir kelimeyi çoğul hâline dönüştürür. Bu metod da Laravel’in çoğul yapıcısının desteklediği tüm dilleri destekler:

```php
use Illuminate\Support\Str;

$plural = Str::pluralStudly('VerifiedHuman');

// VerifiedHumans

$plural = Str::pluralStudly('UserFeedback');

// UserFeedback
```

İkinci argüman olarak bir tamsayı geçerek, kelimenin tekil veya çoğul hâlini alabilirsiniz:

```php
use Illuminate\Support\Str;

$plural = Str::pluralStudly('VerifiedHuman', 2);

// VerifiedHumans

$singular = Str::pluralStudly('VerifiedHuman', 1);

// VerifiedHuman
```

<br>


### Str::position()

`Str::position` metodu, bir alt dizgenin (substring) bir string içindeki ilk geçtiği konumun pozisyonunu döndürür. Eğer alt dizge bulunmazsa, `false` döner:

```php
use Illuminate\Support\Str;

$position = Str::position('Hello, World!', 'Hello');

// 0

$position = Str::position('Hello, World!', 'W');

// 7
```

<br>


### Str::random()

`Str::random` metodu, belirtilen uzunlukta rastgele bir string oluşturur. Bu metod, PHP’nin `random_bytes` fonksiyonunu kullanır:

```php
use Illuminate\Support\Str;

$random = Str::random(40);
```

Test sırasında, `Str::random` metodunun döndürdüğü değeri “sahte” bir değerle değiştirmek isteyebilirsiniz. Bunu yapmak için `createRandomStringsUsing` metodunu kullanabilirsiniz:

```php
Str::createRandomStringsUsing(function () {
    return 'fake-random-string';
});
```

Rastgele string üretimini normale döndürmek için `createRandomStringsNormally` metodunu çağırabilirsiniz:

```php
Str::createRandomStringsNormally();
```

<br>


### Str::remove()

`Str::remove` metodu, verilen değeri veya değer dizisini string’ten kaldırır:

```php
use Illuminate\Support\Str;

$string = 'Peter Piper picked a peck of pickled peppers.';

$removed = Str::remove('e', $string);

// Ptr Pipr pickd a pck of pickld ppprs.
```

Üçüncü argüman olarak `false` geçerseniz, string silme işlemi sırasında büyük/küçük harf farkı göz ardı edilir.

<br>


### Str::repeat()

`Str::repeat` metodu, verilen string’i belirtilen sayıda tekrar eder:

```php
use Illuminate\Support\Str;

$string = 'a';

$repeat = Str::repeat($string, 5);

// aaaaa
```

<br>


### Str::replace()

`Str::replace` metodu, string içinde verilen değeri başka bir değerle değiştirir:

```php
use Illuminate\Support\Str;

$string = 'Laravel 11.x';

$replaced = Str::replace('11.x', '12.x', $string);

// Laravel 12.x
```

`replace` metodu ayrıca `caseSensitive` argümanını kabul eder. Varsayılan olarak büyük/küçük harf duyarlıdır:

```php
$replaced = Str::replace(
    'php',
    'Laravel',
    'PHP Framework for Web Artisans',
    caseSensitive: false
);

// Laravel Framework for Web Artisans
```

<br>


### Str::replaceArray()

`Str::replaceArray` metodu, bir string içinde verilen değeri sırayla bir dizi kullanarak değiştirir:

```php
use Illuminate\Support\Str;

$string = 'The event will take place between ? and ?';

$replaced = Str::replaceArray('?', ['8:30', '9:00'], $string);

// The event will take place between 8:30 and 9:00
```

<br>


### Str::replaceFirst()

`Str::replaceFirst` metodu, string içinde verilen değerin **ilk** geçtiği yeri değiştirir:

```php
use Illuminate\Support\Str;

$replaced = Str::replaceFirst('the', 'a', 'the quick brown fox jumps over the lazy dog');

// a quick brown fox jumps over the lazy dog
```

<br>


### Str::replaceLast()

`Str::replaceLast` metodu, string içinde verilen değerin **son** geçtiği yeri değiştirir:

```php
use Illuminate\Support\Str;

$replaced = Str::replaceLast('the', 'a', 'the quick brown fox jumps over the lazy dog');

// the quick brown fox jumps over a lazy dog
```

<br>


### Str::replaceMatches()

`Str::replaceMatches` metodu, bir düzenli ifade (regex) desenine uyan tüm bölümleri verilen değerle değiştirir:

```php
use Illuminate\Support\Str;

$replaced = Str::replaceMatches(
    pattern: '/[^A-Za-z0-9]++/',
    replace: '',
    subject: '(+1) 501-555-1000'
);

// '15015551000'
```

Ayrıca, değiştirilecek değeri bir closure aracılığıyla belirleyebilirsiniz. Bu closure, her eşleşme için çağrılır ve döndürdüğü değer ile değişiklik yapılır:

```php
use Illuminate\Support\Str;

$replaced = Str::replaceMatches('/\d/', function (array $matches) {
    return '['.$matches[0].']';
}, '123');

// '[1][2][3]'
```

<br>


### Str::replaceStart()

`Str::replaceStart` metodu, string’in başında bulunan değeri değiştirir:

```php
use Illuminate\Support\Str;

$replaced = Str::replaceStart('Hello', 'Laravel', 'Hello World');

// Laravel World

$replaced = Str::replaceStart('World', 'Laravel', 'Hello World');

// Hello World
```

<br>


### Str::replaceEnd()

`Str::replaceEnd` metodu, string’in sonunda bulunan değeri değiştirir:

```php
use Illuminate\Support\Str;

$replaced = Str::replaceEnd('World', 'Laravel', 'Hello World');

// Hello Laravel

$replaced = Str::replaceEnd('Hello', 'Laravel', 'Hello World');

// Hello World
```

<br>


### Str::reverse()

`Str::reverse` metodu, verilen string’i tersine çevirir:

```php
use Illuminate\Support\Str;

$reversed = Str::reverse('Hello World');

// dlroW olleH
```

<br>


### Str::singular()

`Str::singular` metodu, verilen string’i tekil hâline dönüştürür. Bu metod, Laravel’in çoğul yapıcısının desteklediği tüm dilleri destekler:

```php
use Illuminate\Support\Str;

$singular = Str::singular('cars');

// car

$singular = Str::singular('children');

// child
```

<br>


### Str::slug()

`Str::slug` metodu, verilen string’den URL dostu bir “slug” oluşturur:

```php
use Illuminate\Support\Str;

$slug = Str::slug('Laravel 5 Framework', '-');

// laravel-5-framework
```

<br>


### Str::snake()

`Str::snake` metodu, verilen string’i `snake_case` biçimine dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::snake('fooBar');

// foo_bar

$converted = Str::snake('fooBar', '-');

// foo-bar
```

<br>


### Str::squish()

`Str::squish` metodu, bir string içindeki gereksiz boşlukları, kelimeler arasındakiler dahil olmak üzere kaldırır:

```php
use Illuminate\Support\Str;

$string = Str::squish('    laravel    framework    ');

// laravel framework
```

<br>


### Str::start()

`Str::start` metodu, bir string belirli bir değerle başlamıyorsa, o değeri string’in başına ekler:

```php
use Illuminate\Support\Str;

$adjusted = Str::start('this/string', '/');

// /this/string

$adjusted = Str::start('/this/string', '/');

// /this/string
```

```
```

<br>


### Str::startsWith()

`Str::startsWith` metodu, verilen string’in belirtilen değerle başlayıp başlamadığını belirler:

```php
use Illuminate\Support\Str;

$result = Str::startsWith('This is my name', 'This');

// true
````

Bir dizi değer geçilirse, string bu değerlerden herhangi biriyle başlıyorsa `true` döner:

```php
$result = Str::startsWith('This is my name', ['This', 'That', 'There']);

// true
```

<br>


### Str::studly()

`Str::studly` metodu, verilen string’i `StudlyCase` biçimine dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::studly('foo_bar');

// FooBar
```

<br>


### Str::substr()

`Str::substr` metodu, başlangıç ve uzunluk parametreleriyle belirtilen kısmı döndürür:

```php
use Illuminate\Support\Str;

$converted = Str::substr('The Laravel Framework', 4, 7);

// Laravel
```

<br>


### Str::substrCount()

`Str::substrCount` metodu, bir string içinde belirli bir değerin kaç kez geçtiğini döndürür:

```php
use Illuminate\Support\Str;

$count = Str::substrCount('If you like ice cream, you will like snow cones.', 'like');

// 2
```

<br>


### Str::substrReplace()

`Str::substrReplace` metodu, bir string’in belirli bir konumundan itibaren metni değiştirir. Üçüncü parametre başlangıç pozisyonunu, dördüncü parametre ise değiştirilecek karakter sayısını belirtir.
Dördüncü argüman `0` olarak verilirse, mevcut karakterler silinmeden belirtilen pozisyona ekleme yapılır:

```php
use Illuminate\Support\Str;

$result = Str::substrReplace('1300', ':', 2);
// 13:

$result = Str::substrReplace('1300', ':', 2, 0);
// 13:00
```

<br>


### Str::swap()

`Str::swap` metodu, bir string içinde birden fazla değeri `PHP`’nin `strtr` fonksiyonunu kullanarak değiştirir:

```php
use Illuminate\Support\Str;

$string = Str::swap([
    'Tacos' => 'Burritos',
    'great' => 'fantastic',
], 'Tacos are great!');

// Burritos are fantastic!
```

<br>


### Str::take()

`Str::take` metodu, string’in başından belirtilen sayıda karakter döndürür:

```php
use Illuminate\Support\Str;

$taken = Str::take('Build something amazing!', 5);

// Build
```

<br>


### Str::title()

`Str::title` metodu, string’i her kelimenin ilk harfi büyük olacak şekilde dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::title('a nice title uses the correct case');

// A Nice Title Uses The Correct Case
```

<br>


### Str::toBase64()

`Str::toBase64` metodu, verilen string’i Base64 formatına dönüştürür:

```php
use Illuminate\Support\Str;

$base64 = Str::toBase64('Laravel');

// TGFyYXZlbA==
```

<br>


### Str::transliterate()

`Str::transliterate` metodu, verilen string’i en yakın ASCII temsiline dönüştürmeye çalışır:

```php
use Illuminate\Support\Str;

$email = Str::transliterate('ⓣⓔⓢⓣ@ⓛⓐⓡⓐⓥⓔⓛ.ⓒⓞⓜ');

// 'test@laravel.com'
```

<br>


### Str::trim()

`Str::trim` metodu, verilen string’in başındaki ve sonundaki boşlukları (veya belirtilen karakterleri) kaldırır. PHP’nin yerel `trim` fonksiyonundan farklı olarak, Unicode boşluk karakterlerini de kaldırır:

```php
use Illuminate\Support\Str;

$string = Str::trim(' foo bar ');

// 'foo bar'
```

<br>


### Str::ltrim()

`Str::ltrim` metodu, string’in başındaki boşlukları (veya belirtilen karakterleri) kaldırır. Unicode boşluk karakterlerini de temizler:

```php
use Illuminate\Support\Str;

$string = Str::ltrim('  foo bar  ');

// 'foo bar  '
```

<br>


### Str::rtrim()

`Str::rtrim` metodu, string’in sonundaki boşlukları (veya belirtilen karakterleri) kaldırır. Unicode boşluk karakterlerini de destekler:

```php
use Illuminate\Support\Str;

$string = Str::rtrim('  foo bar  ');

// '  foo bar'
```

<br>


### Str::ucfirst()

`Str::ucfirst` metodu, verilen string’in ilk karakterini büyük harfe dönüştürür:

```php
use Illuminate\Support\Str;

$string = Str::ucfirst('foo bar');

// Foo bar
```

<br>


### Str::ucsplit()

`Str::ucsplit` metodu, büyük harf karakterlerine göre bir string’i böler ve bir dizi döndürür:

```php
use Illuminate\Support\Str;

$segments = Str::ucsplit('FooBar');

// [0 => 'Foo', 1 => 'Bar']
```

<br>


### Str::upper()

`Str::upper` metodu, verilen string’i tamamen büyük harfe dönüştürür:

```php
use Illuminate\Support\Str;

$string = Str::upper('laravel');

// LARAVEL
```

<br>


### Str::ulid()

`Str::ulid` metodu, sıkıştırılmış ve zaman sıralı benzersiz bir tanımlayıcı (ULID) oluşturur:

```php
use Illuminate\Support\Str;

return (string) Str::ulid();

// 01gd6r360bp37zj17nxb55yv40
```

Oluşturulan ULID’in tarih ve saat bilgisini `Illuminate\Support\Carbon` örneği olarak almak isterseniz, `createFromId` metodunu kullanabilirsiniz:

```php
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

$date = Carbon::createFromId((string) Str::ulid());
```

Test sırasında, `Str::ulid` metodunun döndürdüğü değeri sahte bir değerle değiştirmek isteyebilirsiniz. Bunun için `createUlidsUsing` metodunu kullanabilirsiniz:

```php
use Symfony\Component\Uid\Ulid;

Str::createUlidsUsing(function () {
    return new Ulid('01HRDBNHHCKNW2AK4Z29SN82T9');
});
```

ULID üretimini normale döndürmek için `createUlidsNormally` metodunu çağırabilirsiniz:

```php
Str::createUlidsNormally();
```

<br>


### Str::unwrap()

`Str::unwrap` metodu, verilen string’in başından ve sonundan belirtilen karakterleri kaldırır:

```php
use Illuminate\Support\Str;

Str::unwrap('-Laravel-', '-');

// Laravel

Str::unwrap('{framework: "Laravel"}', '{', '}');

// framework: "Laravel"
```

<br>


### Str::uuid()

`Str::uuid` metodu, bir UUID (sürüm 4) oluşturur:

```php
use Illuminate\Support\Str;

return (string) Str::uuid();
```

Test sırasında `Str::uuid` metodunun döndürdüğü değeri sahte bir değerle değiştirmek için `createUuidsUsing` metodunu kullanabilirsiniz:

```php
use Ramsey\Uuid\Uuid;

Str::createUuidsUsing(function () {
    return Uuid::fromString('eadbfeac-5258-45c2-bab7-ccb9b5ef74f9');
});
```

UUID üretimini normale döndürmek için `createUuidsNormally` metodunu çağırabilirsiniz:

```php
Str::createUuidsNormally();
```

<br>


### Str::uuid7()

`Str::uuid7` metodu, bir UUID (sürüm 7) oluşturur:

```php
use Illuminate\Support\Str;

return (string) Str::uuid7();
```

Opsiyonel olarak bir `DateTimeInterface` örneği geçerek, belirli bir zamana göre sıralanmış UUID üretebilirsiniz:

```php
return (string) Str::uuid7(time: now());
```

<br>


### Str::wordCount()

`Str::wordCount` metodu, bir string’in içerdiği kelime sayısını döndürür:

```php
use Illuminate\Support\Str;

Str::wordCount('Hello, world!'); // 2
```

<br>


### Str::wordWrap()

`Str::wordWrap` metodu, bir string’i belirtilen karakter sayısına göre satırlara böler:

```php
use Illuminate\Support\Str;

$text = "The quick brown fox jumped over the lazy dog.";

Str::wordWrap($text, characters: 20, break: "<br />\n");

/*
The quick brown fox<br />
jumped over the lazy<br />
dog.
*/
```

<br>


### Str::words()

`Str::words` metodu, string’deki kelime sayısını sınırlamak için kullanılır.
Üçüncü parametre olarak kısaltılmış metnin sonuna eklenecek dizgeyi belirtebilirsiniz:

```php
use Illuminate\Support\Str;

return Str::words('Perfectly balanced, as all things should be.', 3, ' >>>');

// Perfectly balanced, as >>>
```

<br>


### Str::wrap()

`Str::wrap` metodu, bir string’i belirtilen karakter(ler)le çevreler:

```php
use Illuminate\Support\Str;

Str::wrap('Laravel', '"');

// "Laravel"

Str::wrap('is', before: 'This ', after: ' Laravel!');

// This is Laravel!
```

<br>


### str()

`str` fonksiyonu, verilen string için yeni bir `Illuminate\Support\Stringable` örneği döndürür.
Bu fonksiyon, `Str::of` metoduna denktir:

```php
$string = str('Taylor')->append(' Otwell');

// 'Taylor Otwell'
```

Eğer `str` fonksiyonuna argüman verilmezse, `Illuminate\Support\Str` sınıfının bir örneğini döndürür:

```php
$snake = str()->snake('FooBar');

// 'foo_bar'
```

<br>


### trans()

`trans` fonksiyonu, dil dosyalarınızı kullanarak belirtilen çeviri anahtarını çevirir:

```php
echo trans('messages.welcome');
```

Belirtilen çeviri anahtarı mevcut değilse, `trans` fonksiyonu anahtarın kendisini döndürür.
Yani yukarıdaki örnekte `messages.welcome` bulunmazsa, `trans` fonksiyonu `messages.welcome` değerini döndürür.

<br>


### trans_choice()

`trans_choice` fonksiyonu, belirtilen çeviri anahtarını çokluk (inflection) kurallarına göre çevirir:

```php
echo trans_choice('messages.notifications', $unreadCount);
```

Belirtilen çeviri anahtarı mevcut değilse, `trans_choice` fonksiyonu da anahtarın kendisini döndürür.
Yani yukarıdaki örnekte `messages.notifications` bulunmazsa, `trans_choice` fonksiyonu `messages.notifications` değerini döndürür.

```
```

<br>


## Fluent Strings

Fluent string’ler, string değerleriyle çalışmak için daha akıcı, nesne yönelimli bir arayüz sağlar. Bu yaklaşım, geleneksel string işlemlerine kıyasla daha okunabilir bir sözdizimiyle birden fazla string işlemini zincirleme şekilde uygulamanıza olanak tanır.

<br>


### after

`after` metodu, string içinde verilen değerden sonraki kısmı döndürür. Eğer değer string içinde yoksa, tüm string döndürülür:

```php
use Illuminate\Support\Str;

$slice = Str::of('This is my name')->after('This is');

// ' my name'
````

<br>


### afterLast

`afterLast` metodu, string içinde verilen değerin son geçtiği yerden sonraki kısmı döndürür. Eğer değer bulunmazsa, tüm string döndürülür:

```php
use Illuminate\Support\Str;

$slice = Str::of('App\Http\Controllers\Controller')->afterLast('\\');

// 'Controller'
```

<br>


### apa

`apa` metodu, verilen string’i APA kurallarına uygun başlık biçimine dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::of('a nice title uses the correct case')->apa();

// A Nice Title Uses the Correct Case
```

<br>


### append

`append` metodu, verilen değer(ler)i string’in sonuna ekler:

```php
use Illuminate\Support\Str;

$string = Str::of('Taylor')->append(' Otwell');

// 'Taylor Otwell'
```

<br>


### ascii

`ascii` metodu, string’i ASCII karakterlerine dönüştürmeye çalışır:

```php
use Illuminate\Support\Str;

$string = Str::of('ü')->ascii();

// 'u'
```

<br>


### basename

`basename` metodu, verilen string’in sonundaki dosya veya klasör adını döndürür:

```php
use Illuminate\Support\Str;

$string = Str::of('/foo/bar/baz')->basename();

// 'baz'
```

Gerekirse, sondaki bileşenden kaldırılacak bir “uzantı” belirtebilirsiniz:

```php
use Illuminate\Support\Str;

$string = Str::of('/foo/bar/baz.jpg')->basename('.jpg');

// 'baz'
```

<br>


### before

`before` metodu, string içinde verilen değerden önceki kısmı döndürür:

```php
use Illuminate\Support\Str;

$slice = Str::of('This is my name')->before('my name');

// 'This is '
```

<br>


### beforeLast

`beforeLast` metodu, string içinde verilen değerin son geçtiği yerden önceki kısmı döndürür:

```php
use Illuminate\Support\Str;

$slice = Str::of('This is my name')->beforeLast('is');

// 'This '
```

<br>


### between

`between` metodu, iki değer arasındaki kısmı döndürür:

```php
use Illuminate\Support\Str;

$converted = Str::of('This is my name')->between('This', 'name');

// ' is my '
```

<br>


### betweenFirst

`betweenFirst` metodu, iki değer arasındaki en küçük olası kısmı döndürür:

```php
use Illuminate\Support\Str;

$converted = Str::of('[a] bc [d]')->betweenFirst('[', ']');

// 'a'
```

<br>


### camel

`camel` metodu, string’i `camelCase` biçimine dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::of('foo_bar')->camel();

// 'fooBar'
```

<br>


### charAt

`charAt` metodu, belirtilen indeksteki karakteri döndürür. Eğer indeks aralık dışındaysa `false` döndürür:

```php
use Illuminate\Support\Str;

$character = Str::of('This is my name.')->charAt(6);

// 's'
```

<br>


### classBasename

`classBasename` metodu, bir sınıfın namespace’ini kaldırarak yalnızca sınıf adını döndürür:

```php
use Illuminate\Support\Str;

$class = Str::of('Foo\Bar\Baz')->classBasename();

// 'Baz'
```

<br>


### chopStart

`chopStart` metodu, string’in başında belirtilen değer varsa onu kaldırır:

```php
use Illuminate\Support\Str;

$url = Str::of('https://laravel.com')->chopStart('https://');

// 'laravel.com'
```

İkinci argüman olarak bir dizi geçebilirsiniz. String, bu değerlerden herhangi biriyle başlıyorsa o değer kaldırılır:

```php
use Illuminate\Support\Str;

$url = Str::of('http://laravel.com')->chopStart(['https://', 'http://']);

// 'laravel.com'
```

<br>


### chopEnd

`chopEnd` metodu, string’in sonunda belirtilen değer varsa onu kaldırır:

```php
use Illuminate\Support\Str;

$url = Str::of('https://laravel.com')->chopEnd('.com');

// 'https://laravel'
```

İkinci argüman olarak bir dizi geçebilirsiniz. String, bu değerlerden biriyle bitiyorsa o değer kaldırılır:

```php
use Illuminate\Support\Str;

$url = Str::of('http://laravel.com')->chopEnd(['.com', '.io']);

// 'http://laravel'
```

<br>


### contains

`contains` metodu, string’in belirtilen değeri içerip içermediğini belirler. Varsayılan olarak büyük/küçük harf duyarlıdır:

```php
use Illuminate\Support\Str;

$contains = Str::of('This is my name')->contains('my');

// true
```

Bir dizi değer de geçebilirsiniz; string bu değerlerden herhangi birini içeriyorsa `true` döner:

```php
use Illuminate\Support\Str;

$contains = Str::of('This is my name')->contains(['my', 'foo']);

// true
```

Büyük/küçük harf duyarlılığını devre dışı bırakmak için `ignoreCase` argümanını `true` yapabilirsiniz:

```php
use Illuminate\Support\Str;

$contains = Str::of('This is my name')->contains('MY', ignoreCase: true);

// true
```

<br>


### containsAll

`containsAll` metodu, string’in bir dizideki tüm değerleri içerip içermediğini belirler:

```php
use Illuminate\Support\Str;

$containsAll = Str::of('This is my name')->containsAll(['my', 'name']);

// true
```

Büyük/küçük harf duyarlılığını devre dışı bırakmak için `ignoreCase` argümanını `true` yapabilirsiniz:

```php
use Illuminate\Support\Str;

$containsAll = Str::of('This is my name')->containsAll(['MY', 'NAME'], ignoreCase: true);

// true
```

<br>


### decrypt

`decrypt` metodu, şifrelenmiş string’i çözer:

```php
use Illuminate\Support\Str;

$decrypted = $encrypted->decrypt();

// 'secret'
```

Ters işlem için bkz. `encrypt` metodu.

<br>


### deduplicate

`deduplicate` metodu, ardışık tekrar eden karakterleri tek bir karakterle değiştirir. Varsayılan olarak boşluk karakterlerini tekilleştirir:

```php
use Illuminate\Support\Str;

$result = Str::of('The   Laravel   Framework')->deduplicate();

// The Laravel Framework
```

Farklı bir karakteri tekilleştirmek için ikinci argümanı kullanabilirsiniz:

```php
use Illuminate\Support\Str;

$result = Str::of('The---Laravel---Framework')->deduplicate('-');

// The-Laravel-Framework
```

<br>


### dirname

`dirname` metodu, bir dosya yolunun üst dizin kısmını döndürür:

```php
use Illuminate\Support\Str;

$string = Str::of('/foo/bar/baz')->dirname();

// '/foo/bar'
```

İsterseniz, kaç dizin seviyesinin kırpılacağını belirtebilirsiniz:

```php
use Illuminate\Support\Str;

$string = Str::of('/foo/bar/baz')->dirname(2);

// '/foo'
```

<br>


### doesntEndWith

`doesntEndWith` metodu, string’in belirtilen değerle bitmediğini belirler:

```php
use Illuminate\Support\Str;

$result = Str::of('This is my name')->doesntEndWith('dog');

// true
```

Bir dizi değer de geçebilirsiniz; string, bu değerlerden hiçbiriyle bitmiyorsa `true` döner:

```php
use Illuminate\Support\Str;

$result = Str::of('This is my name')->doesntEndWith(['this', 'foo']);

// true

$result = Str::of('This is my name')->doesntEndWith(['name', 'foo']);

// false
```

<br>


### doesntStartWith

`doesntStartWith` metodu, string’in belirtilen değerle başlamadığını belirler:

```php
use Illuminate\Support\Str;

$result = Str::of('This is my name')->doesntStartWith('That');

// true
```

Bir dizi değer de geçebilirsiniz; string bu değerlerden hiçbiriyle başlamıyorsa `true` döner:

```php
use Illuminate\Support\Str;

$result = Str::of('This is my name')->doesntStartWith(['What', 'That', 'There']);

// true
```

<br>


### encrypt

`encrypt` metodu, string’i şifreler:

```php
use Illuminate\Support\Str;

$encrypted = Str::of('secret')->encrypt();
```

Ters işlem için bkz. `decrypt` metodu.

<br>


### endsWith

`endsWith` metodu, string’in belirtilen değerle bitip bitmediğini belirler:

```php
use Illuminate\Support\Str;

$result = Str::of('This is my name')->endsWith('name');

// true
```

Bir dizi değer de geçebilirsiniz; string bu değerlerden herhangi biriyle bitiyorsa `true` döner:

```php
use Illuminate\Support\Str;

$result = Str::of('This is my name')->endsWith(['name', 'foo']);

// true

$result = Str::of('This is my name')->endsWith(['this', 'foo']);

// false
```

<br>


### exactly

`exactly` metodu, string’in verilen değerle tamamen aynı olup olmadığını kontrol eder:

```php
use Illuminate\Support\Str;

$result = Str::of('Laravel')->exactly('Laravel');

// true
```

<br>


### excerpt

`excerpt` metodu, string içinde belirtilen ifadeyi içeren kısmı çıkarır:

```php
use Illuminate\Support\Str;

$excerpt = Str::of('This is my name')->excerpt('my', [
    'radius' => 3
]);

// '...is my na...'
```

`radius` seçeneği (varsayılan 100), kesilmiş string’in her iki yanındaki karakter sayısını belirler.
Ayrıca `omission` seçeneğiyle kesilmiş kısmın başına ve sonuna eklenecek dizgeyi belirleyebilirsiniz:

```php
use Illuminate\Support\Str;

$excerpt = Str::of('This is my name')->excerpt('name', [
    'radius' => 3,
    'omission' => '(...) '
]);

// '(...) my name'
```

<br>


### explode

`explode` metodu, verilen ayraç karakterine göre string’i böler ve her parçayı içeren bir koleksiyon döndürür:

```php
use Illuminate\Support\Str;

$collection = Str::of('foo bar baz')->explode(' ');

// collect(['foo', 'bar', 'baz'])
```

```
```

<br>


### finish

`finish` metodu, bir string belirli bir değerle bitmiyorsa o değeri sonuna ekler:

```php
use Illuminate\Support\Str;

$adjusted = Str::of('this/string')->finish('/');

// this/string/

$adjusted = Str::of('this/string/')->finish('/');

// this/string/
````

<br>


### fromBase64

`fromBase64` metodu, verilen Base64 kodlu string’i çözümler:

```php
use Illuminate\Support\Str;

$decoded = Str::of('TGFyYXZlbA==')->fromBase64();

// Laravel
```

<br>


### hash

`hash` metodu, verilen string’i belirtilen algoritmayla şifreler (hashler):

```php
use Illuminate\Support\Str;

$hashed = Str::of('secret')->hash(algorithm: 'sha256');

// '2bb80d537b1da3e38bd30361aa855686bde0eacd7162fef6a25fe97bf527a25b'
```

<br>


### headline

`headline` metodu, tire veya alt çizgiyle ayrılmış string’leri kelimelerin baş harfleri büyük olacak şekilde boşluklu biçime dönüştürür:

```php
use Illuminate\Support\Str;

$headline = Str::of('taylor_otwell')->headline();

// Taylor Otwell

$headline = Str::of('EmailNotificationSent')->headline();

// Email Notification Sent
```

<br>


### inlineMarkdown

`inlineMarkdown` metodu, GitHub uyumlu Markdown içeriğini `CommonMark` kullanarak satır içi HTML’e dönüştürür. Ancak `markdown` metodundan farklı olarak, çıktıyı blok düzeyinde bir HTML etiketi içine sarmaz:

```php
use Illuminate\Support\Str;

$html = Str::of('**Laravel**')->inlineMarkdown();

// <strong>Laravel</strong>
```

#### Markdown Güvenliği

Varsayılan olarak Markdown, ham HTML desteği sunar. Bu, ham kullanıcı girdisiyle kullanıldığında XSS (Cross-Site Scripting) açıklarına neden olabilir.
`CommonMark` güvenlik dokümantasyonuna göre `html_input` seçeneğiyle ham HTML’i kaçırabilir veya silebilir, `allow_unsafe_links` seçeneğiyle de güvensiz bağlantılara izin verilip verilmeyeceğini belirleyebilirsiniz.
Eğer bazı ham HTML’lere izin vermeniz gerekiyorsa, çıktıyı bir HTML Purifier’dan geçirmeniz önerilir:

```php
use Illuminate\Support\Str;

Str::of('Inject: <script>alert("Hello XSS!");</script>')->inlineMarkdown([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);

// Inject: alert(&quot;Hello XSS!&quot;);
```

<br>


### is

`is` metodu, string’in belirtilen desenle (pattern) eşleşip eşleşmediğini kontrol eder.
Yıldız (`*`) karakteri joker olarak kullanılabilir:

```php
use Illuminate\Support\Str;

$matches = Str::of('foobar')->is('foo*');

// true

$matches = Str::of('foobar')->is('baz*');

// false
```

<br>


### isAscii

`isAscii` metodu, string’in yalnızca ASCII karakterlerinden oluşup oluşmadığını belirler:

```php
use Illuminate\Support\Str;

$result = Str::of('Taylor')->isAscii();

// true

$result = Str::of('ü')->isAscii();

// false
```

<br>


### isEmpty

`isEmpty` metodu, string’in boş olup olmadığını belirler:

```php
use Illuminate\Support\Str;

$result = Str::of('  ')->trim()->isEmpty();

// true

$result = Str::of('Laravel')->trim()->isEmpty();

// false
```

<br>


### isNotEmpty

`isNotEmpty` metodu, string’in **boş olmadığını** belirler:

```php
use Illuminate\Support\Str;

$result = Str::of('  ')->trim()->isNotEmpty();

// false

$result = Str::of('Laravel')->trim()->isNotEmpty();

// true
```

<br>


### isJson

`isJson` metodu, string’in geçerli bir JSON olup olmadığını belirler:

```php
use Illuminate\Support\Str;

$result = Str::of('[1,2,3]')->isJson();

// true

$result = Str::of('{"first": "John", "last": "Doe"}')->isJson();

// true

$result = Str::of('{first: "John", last: "Doe"}')->isJson();

// false
```

<br>


### isUlid

`isUlid` metodu, string’in geçerli bir ULID olup olmadığını kontrol eder:

```php
use Illuminate\Support\Str;

$result = Str::of('01gd6r360bp37zj17nxb55yv40')->isUlid();

// true

$result = Str::of('Taylor')->isUlid();

// false
```

<br>


### isUrl

`isUrl` metodu, string’in geçerli bir URL olup olmadığını belirler:

```php
use Illuminate\Support\Str;

$result = Str::of('http://example.com')->isUrl();

// true

$result = Str::of('Taylor')->isUrl();

// false
```

`isUrl` metodu, çok çeşitli protokolleri geçerli kabul eder. Ancak yalnızca belirli protokollere izin vermek istiyorsanız, bunları parametre olarak belirtebilirsiniz:

```php
$result = Str::of('http://example.com')->isUrl(['http', 'https']);
```

<br>


### isUuid

`isUuid` metodu, string’in geçerli bir UUID olup olmadığını belirler:

```php
use Illuminate\Support\Str;

$result = Str::of('5ace9ab9-e9cf-4ec6-a19d-5881212a452c')->isUuid();

// true

$result = Str::of('Taylor')->isUuid();

// false
```

Ayrıca belirli bir UUID sürümüne (1, 3, 4, 5, 6, 7 veya 8) göre doğrulama da yapabilirsiniz:

```php
use Illuminate\Support\Str;

$isUuid = Str::of('a0a2a2d2-0b87-4a18-83f2-2529882be2de')->isUuid(version: 4);

// true

$isUuid = Str::of('a0a2a2d2-0b87-4a18-83f2-2529882be2de')->isUuid(version: 1);

// false
```

<br>


### kebab

`kebab` metodu, string’i `kebab-case` biçimine dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::of('fooBar')->kebab();

// foo-bar
```

<br>


### lcfirst

`lcfirst` metodu, string’in ilk karakterini küçük harfe dönüştürür:

```php
use Illuminate\Support\Str;

$string = Str::of('Foo Bar')->lcfirst();

// foo Bar
```

<br>


### length

`length` metodu, string’in uzunluğunu döndürür:

```php
use Illuminate\Support\Str;

$length = Str::of('Laravel')->length();

// 7
```

<br>


### limit

`limit` metodu, string’i belirtilen uzunluğa kadar kısaltır:

```php
use Illuminate\Support\Str;

$truncated = Str::of('The quick brown fox jumps over the lazy dog')->limit(20);

// The quick brown fox...
```

Sonuna eklenecek dizgeyi değiştirmek için ikinci argüman kullanılabilir:

```php
$truncated = Str::of('The quick brown fox jumps over the lazy dog')->limit(20, ' (...)');

// The quick brown fox (...)
```

Kelime bütünlüğünü korumak istiyorsanız `preserveWords` argümanını kullanabilirsiniz:

```php
$truncated = Str::of('The quick brown fox')->limit(12, preserveWords: true);

// The quick...
```

<br>


### lower

`lower` metodu, string’i küçük harfe dönüştürür:

```php
use Illuminate\Support\Str;

$result = Str::of('LARAVEL')->lower();

// 'laravel'
```

<br>


### markdown

`markdown` metodu, GitHub uyumlu Markdown içeriğini HTML’e dönüştürür:

```php
use Illuminate\Support\Str;

$html = Str::of('# Laravel')->markdown();

// <h1>Laravel</h1>

$html = Str::of('# Taylor <b>Otwell</b>')->markdown([
    'html_input' => 'strip',
]);

// <h1>Taylor Otwell</h1>
```

#### Markdown Güvenliği

Varsayılan olarak Markdown, ham HTML desteği sağlar ve bu da XSS açıklarına neden olabilir.
`CommonMark` güvenlik yönergelerine göre `html_input` seçeneğiyle HTML’leri silebilir veya kaçırabilir, `allow_unsafe_links` seçeneğiyle güvensiz bağlantılara izin verilip verilmeyeceğini belirleyebilirsiniz:

```php
use Illuminate\Support\Str;

Str::of('Inject: <script>alert("Hello XSS!");</script>')->markdown([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);

// <p>Inject: alert(&quot;Hello XSS!&quot;);</p>
```

<br>


### mask

`mask` metodu, string’in bir kısmını belirli bir karakterle gizler. Bu metod, e-posta adresleri veya telefon numaraları gibi bilgileri gizlemek için kullanılabilir:

```php
use Illuminate\Support\Str;

$string = Str::of('taylor@example.com')->mask('*', 3);

// tay***************
```

Gizleme işleminin string’in sonundan başlamasını veya belirli bir uzunluğu kapsamasını istiyorsanız, negatif sayılar kullanabilirsiniz:

```php
$string = Str::of('taylor@example.com')->mask('*', -15, 3);

// tay***@example.com

$string = Str::of('taylor@example.com')->mask('*', 4, -4);

// tayl**********.com
```

<br>


### match

`match` metodu, belirtilen regex desenine uyan string kısmını döndürür:

```php
use Illuminate\Support\Str;

$result = Str::of('foo bar')->match('/bar/');

// 'bar'

$result = Str::of('foo bar')->match('/foo (.*)/');

// 'bar'
```

<br>


### matchAll

`matchAll` metodu, regex desenine uyan tüm eşleşmeleri bir koleksiyon olarak döndürür:

```php
use Illuminate\Support\Str;

$result = Str::of('bar foo bar')->matchAll('/bar/');

// collect(['bar', 'bar'])
```

Regex ifadesinde bir grup tanımlarsanız, Laravel yalnızca ilk gruptaki eşleşmeleri döndürür:

```php
use Illuminate\Support\Str;

$result = Str::of('bar fun bar fly')->matchAll('/f(\w*)/');

// collect(['un', 'ly'])
```

Eşleşme bulunmazsa, boş bir koleksiyon döndürülür.

<br>


### isMatch

`isMatch` metodu, string’in belirtilen regex ifadesiyle eşleşip eşleşmediğini kontrol eder:

```php
use Illuminate\Support\Str;

$result = Str::of('foo bar')->isMatch('/foo (.*)/');

// true

$result = Str::of('laravel')->isMatch('/foo (.*)/');

// false
```

<br>


### newLine

`newLine` metodu, string’in sonuna bir satır sonu karakteri ekler:

```php
use Illuminate\Support\Str;

$padded = Str::of('Laravel')->newLine()->append('Framework');

// 'Laravel
//  Framework'
```

<br>


### padBoth

`padBoth` metodu, string’i hem sol hem sağdan belirtilen karakterlerle doldurarak istenen uzunluğa getirir:

```php
use Illuminate\Support\Str;

$padded = Str::of('James')->padBoth(10, '_');

// '__James___'

$padded = Str::of('James')->padBoth(10);

// '  James   '
```

<br>


### padLeft

`padLeft` metodu, string’in sol tarafını belirtilen karakterlerle doldurarak istenen uzunluğa getirir:

```php
use Illuminate\Support\Str;

$padded = Str::of('James')->padLeft(10, '-=');

// '-=-=-James'

$padded = Str::of('James')->padLeft(10);

// '     James'
```

```
```

<br>


### padRight

`padRight` metodu, PHP’nin `str_pad` fonksiyonunu sarmalarak, bir string’in sağ tarafını belirtilen karakterlerle doldurur ve istenen uzunluğa ulaştırır:

```php
use Illuminate\Support\Str;

$padded = Str::of('James')->padRight(10, '-');

// 'James-----'

$padded = Str::of('James')->padRight(10);

// 'James     '
````

<br>


### pipe

`pipe` metodu, string değerini bir callable’a (fonksiyon veya closure) geçirerek dönüştürmenizi sağlar:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$hash = Str::of('Laravel')->pipe('md5')->prepend('Checksum: ');

// 'Checksum: a5c95b86291ea299fcbe64458ed12702'

$closure = Str::of('foo')->pipe(function (Stringable $str) {
    return 'bar';
});

// 'bar'
```

<br>


### plural

`plural` metodu, tekil bir kelimeyi çoğul hale dönüştürür. Bu fonksiyon Laravel’in `pluralizer`’ının desteklediği tüm dilleri destekler:

```php
use Illuminate\Support\Str;

$plural = Str::of('car')->plural();

// cars

$plural = Str::of('child')->plural();

// children
```

Bir tamsayı argümanı sağlayarak kelimenin tekil mi yoksa çoğul mu olacağını belirleyebilirsiniz:

```php
$plural = Str::of('child')->plural(2);

// children

$singular = Str::of('child')->plural(1);

// child
```

`prependCount` argümanını kullanarak sayıyı çoğul kelimenin önüne ekleyebilirsiniz:

```php
$label = Str::of('car')->plural(1000, prependCount: true);

// 1,000 cars
```

<br>


### position

`position` metodu, bir alt string’in ilk geçtiği konumu döndürür. Eğer alt string bulunmazsa `false` döner:

```php
use Illuminate\Support\Str;

$position = Str::of('Hello, World!')->position('Hello');

// 0

$position = Str::of('Hello, World!')->position('W');

// 7
```

<br>


### prepend

`prepend` metodu, verilen değeri string’in başına ekler:

```php
use Illuminate\Support\Str;

$string = Str::of('Framework')->prepend('Laravel ');

// Laravel Framework
```

<br>


### remove

`remove` metodu, string’ten belirtilen değeri veya değer dizisini kaldırır:

```php
use Illuminate\Support\Str;

$string = Str::of('Arkansas is quite beautiful!')->remove('quite ');

// Arkansas is beautiful!
```

Büyük/küçük harf duyarlılığını yok saymak için ikinci parametreyi `false` olarak geçebilirsiniz.

<br>


### repeat

`repeat` metodu, verilen string’i belirtilen sayıda tekrarlar:

```php
use Illuminate\Support\Str;

$repeated = Str::of('a')->repeat(5);

// aaaaa
```

<br>


### replace

`replace` metodu, string içinde belirli bir değeri değiştirir:

```php
use Illuminate\Support\Str;

$replaced = Str::of('Laravel 6.x')->replace('6.x', '7.x');

// Laravel 7.x
```

Varsayılan olarak `caseSensitive` argümanı `true`’dur. Bunu devre dışı bırakabilirsiniz:

```php
$replaced = Str::of('macOS 13.x')->replace('macOS', 'iOS', caseSensitive: false);
```

<br>


### replaceArray

`replaceArray` metodu, string içinde verilen değeri bir diziyle sırasıyla değiştirir:

```php
use Illuminate\Support\Str;

$string = 'The event will take place between ? and ?';

$replaced = Str::of($string)->replaceArray('?', ['8:30', '9:00']);

// The event will take place between 8:30 and 9:00
```

<br>


### replaceFirst

`replaceFirst` metodu, belirtilen değerin yalnızca ilk geçtiği yeri değiştirir:

```php
use Illuminate\Support\Str;

$replaced = Str::of('the quick brown fox jumps over the lazy dog')->replaceFirst('the', 'a');

// a quick brown fox jumps over the lazy dog
```

<br>


### replaceLast

`replaceLast` metodu, belirtilen değerin yalnızca son geçtiği yeri değiştirir:

```php
use Illuminate\Support\Str;

$replaced = Str::of('the quick brown fox jumps over the lazy dog')->replaceLast('the', 'a');

// the quick brown fox jumps over a lazy dog
```

<br>


### replaceMatches

`replaceMatches` metodu, belirtilen regex desenine uyan tüm eşleşmeleri verilen string veya closure ile değiştirir:

```php
use Illuminate\Support\Str;

$replaced = Str::of('(+1) 501-555-1000')->replaceMatches('/[^A-Za-z0-9]++/', '');

// '15015551000'
```

Closure kullanarak eşleşen kısımları dinamik olarak değiştirebilirsiniz:

```php
use Illuminate\Support\Str;

$replaced = Str::of('123')->replaceMatches('/\d/', function (array $matches) {
    return '['.$matches[0].']';
});

// '[1][2][3]'
```

<br>


### replaceStart

`replaceStart` metodu, string’in başında belirtilen değer varsa onu değiştirir:

```php
use Illuminate\Support\Str;

$replaced = Str::of('Hello World')->replaceStart('Hello', 'Laravel');

// Laravel World

$replaced = Str::of('Hello World')->replaceStart('World', 'Laravel');

// Hello World
```

<br>


### replaceEnd

`replaceEnd` metodu, string’in sonunda belirtilen değer varsa onu değiştirir:

```php
use Illuminate\Support\Str;

$replaced = Str::of('Hello World')->replaceEnd('World', 'Laravel');

// Hello Laravel

$replaced = Str::of('Hello World')->replaceEnd('Hello', 'Laravel');

// Hello World
```

<br>


### scan

`scan` metodu, bir string’i `sscanf` fonksiyonunun desteklediği biçime göre ayrıştırır ve bir koleksiyon döndürür:

```php
use Illuminate\Support\Str;

$collection = Str::of('filename.jpg')->scan('%[^.].%s');

// collect(['filename', 'jpg'])
```

<br>


### singular

`singular` metodu, çoğul bir kelimeyi tekil hale dönüştürür:

```php
use Illuminate\Support\Str;

$singular = Str::of('cars')->singular();

// car

$singular = Str::of('children')->singular();

// child
```

<br>


### slug

`slug` metodu, verilen string’den URL dostu bir slug oluşturur:

```php
use Illuminate\Support\Str;

$slug = Str::of('Laravel Framework')->slug('-');

// laravel-framework
```

<br>


### snake

`snake` metodu, string’i `snake_case` biçimine dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::of('fooBar')->snake();

// foo_bar
```

<br>


### split

`split` metodu, string’i bir düzenli ifadeye (regex) göre parçalar ve bir koleksiyon döndürür:

```php
use Illuminate\Support\Str;

$segments = Str::of('one, two, three')->split('/[\s,]+/');

// collect(["one", "two", "three"])
```

<br>


### squish

`squish` metodu, bir string’teki gereksiz boşlukları —kelimeler arasındaki dahil— kaldırır:

```php
use Illuminate\Support\Str;

$string = Str::of('    laravel    framework    ')->squish();

// laravel framework
```

<br>


### start

`start` metodu, string belirtilen değerle başlamıyorsa onu başına ekler:

```php
use Illuminate\Support\Str;

$adjusted = Str::of('this/string')->start('/');

// /this/string

$adjusted = Str::of('/this/string')->start('/');

// /this/string
```

<br>


### startsWith

`startsWith` metodu, string’in belirtilen değerle başlayıp başlamadığını belirler:

```php
use Illuminate\Support\Str;

$result = Str::of('This is my name')->startsWith('This');

// true
```

<br>


### stripTags

`stripTags` metodu, string’ten tüm HTML ve PHP etiketlerini kaldırır:

```php
use Illuminate\Support\Str;

$result = Str::of('<a href="https://laravel.com">Taylor <b>Otwell</b></a>')->stripTags();

// Taylor Otwell

$result = Str::of('<a href="https://laravel.com">Taylor <b>Otwell</b></a>')->stripTags('<b>');

// Taylor <b>Otwell</b>
```

<br>


### studly

`studly` metodu, string’i `StudlyCase` biçimine dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::of('foo_bar')->studly();

// FooBar
```

<br>


### substr

`substr` metodu, başlangıç konumuna ve uzunluğa göre string’in bir kısmını döndürür:

```php
use Illuminate\Support\Str;

$string = Str::of('Laravel Framework')->substr(8);

// Framework

$string = Str::of('Laravel Framework')->substr(8, 5);

// Frame
```

<br>


### substrReplace

`substrReplace` metodu, string’in belirli bir kısmını değiştirir.
İkinci parametre başlangıç konumunu, üçüncü parametre değiştirilecek karakter sayısını belirtir.
Üçüncü argüman `0` ise, yeni metin belirtilen konuma eklenir:

```php
use Illuminate\Support\Str;

$string = Str::of('1300')->substrReplace(':', 2);

// 13:

$string = Str::of('The Framework')->substrReplace(' Laravel', 3, 0);

// The Laravel Framework
```

<br>


### swap

`swap` metodu, birden fazla değeri `PHP`’nin `strtr` fonksiyonunu kullanarak değiştirir:

```php
use Illuminate\Support\Str;

$string = Str::of('Tacos are great!')
    ->swap([
        'Tacos' => 'Burritos',
        'great' => 'fantastic',
    ]);

// Burritos are fantastic!
```

<br>


### take

`take` metodu, string’in başından belirtilen sayıda karakter döndürür:

```php
use Illuminate\Support\Str;

$taken = Str::of('Build something amazing!')->take(5);

// Build
```

<br>


### tap

`tap` metodu, string’i verilen closure’a geçirmenizi sağlar.
Bu closure string üzerinde işlem yapabilir, ancak `tap` metodu her zaman orijinal string’i döndürür:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('Laravel')
    ->append(' Framework')
    ->tap(function (Stringable $string) {
        dump('String after append: '.$string);
    })
    ->upper();

// LARAVEL FRAMEWORK
```

<br>


### test

`test` metodu, string’in belirtilen regex desenine uyup uymadığını kontrol eder:

```php
use Illuminate\Support\Str;

$result = Str::of('Laravel Framework')->test('/Laravel/');

// true
```

<br>


### title

`title` metodu, string’i başlık biçimine (her kelimenin ilk harfi büyük) dönüştürür:

```php
use Illuminate\Support\Str;

$converted = Str::of('a nice title uses the correct case')->title();

// A Nice Title Uses The Correct Case
```

<br>


### toBase64

`toBase64` metodu, string’i Base64 biçimine dönüştürür:

```php
use Illuminate\Support\Str;

$base64 = Str::of('Laravel')->toBase64();

// TGFyYXZlbA==
```

<br>


### toHtmlString

`toHtmlString` metodu, string’i `Illuminate\Support\HtmlString` örneğine dönüştürür.
Bu tür string’ler Blade template’lerinde kaçış yapılmadan (`{{!! !!}}`) render edilir:

```php
use Illuminate\Support\Str;

$htmlString = Str::of('Nuno Maduro')->toHtmlString();
```

<br>


### toUri

`toUri` metodu, string’i `Illuminate\Support\Uri` örneğine dönüştürür:

```php
use Illuminate\Support\Str;

$uri = Str::of('https://example.com')->toUri();
```

<br>


### transliterate

`transliterate` metodu, string’i en yakın ASCII temsiline dönüştürmeye çalışır:

```php
use Illuminate\Support\Str;

$email = Str::of('ⓣⓔⓢⓣ@ⓛⓐⓡⓐⓥⓔⓛ.ⓒⓞⓜ')->transliterate();

// 'test@laravel.com'
```

<br>


### trim

`trim` metodu, string’in başındaki ve sonundaki boşlukları (veya belirtilen karakterleri) kaldırır.
PHP’nin yerel `trim` fonksiyonundan farklı olarak Unicode boşluk karakterlerini de kaldırır:

```php
use Illuminate\Support\Str;

$string = Str::of('  Laravel  ')->trim();

// 'Laravel'

$string = Str::of('/Laravel/')->trim('/');

// 'Laravel'
```

```
```

<br>


### ltrim

`ltrim` metodu, string’in **sol tarafındaki** boşlukları veya belirtilen karakterleri kaldırır.  
PHP’nin yerel `ltrim` fonksiyonundan farklı olarak Unicode boşluk karakterlerini de temizler:

```php
use Illuminate\Support\Str;

$string = Str::of('  Laravel  ')->ltrim();

// 'Laravel  '

$string = Str::of('/Laravel/')->ltrim('/');

// 'Laravel/'
````

<br>


### rtrim

`rtrim` metodu, string’in **sağ tarafındaki** boşlukları veya belirtilen karakterleri kaldırır.
PHP’nin yerel `rtrim` fonksiyonundan farklı olarak Unicode boşluk karakterlerini de temizler:

```php
use Illuminate\Support\Str;

$string = Str::of('  Laravel  ')->rtrim();

// '  Laravel'

$string = Str::of('/Laravel/')->rtrim('/');

// '/Laravel'
```

<br>


### ucfirst

`ucfirst` metodu, string’in ilk karakterini büyük harfe dönüştürür:

```php
use Illuminate\Support\Str;

$string = Str::of('foo bar')->ucfirst();

// Foo bar
```

<br>


### ucsplit

`ucsplit` metodu, büyük harfleri esas alarak string’i bir koleksiyon haline getirir:

```php
use Illuminate\Support\Str;

$string = Str::of('Foo Bar')->ucsplit();

// collect(['Foo ', 'Bar'])
```

<br>


### unwrap

`unwrap` metodu, string’in başından ve sonundan belirtilen karakter(ler)i kaldırır:

```php
use Illuminate\Support\Str;

Str::of('-Laravel-')->unwrap('-');

// Laravel

Str::of('{framework: "Laravel"}')->unwrap('{', '}');

// framework: "Laravel"
```

<br>


### upper

`upper` metodu, string’i tamamen büyük harflere dönüştürür:

```php
use Illuminate\Support\Str;

$adjusted = Str::of('laravel')->upper();

// LARAVEL
```

<br>


### when

`when` metodu, belirtilen koşul doğruysa verilen closure’ı çalıştırır.
Closure, `Stringable` örneğini alır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('Taylor')
    ->when(true, function (Stringable $string) {
        return $string->append(' Otwell');
    });

// 'Taylor Otwell'
```

Koşul `false` dönerse, üçüncü parametre olarak verilen closure çalıştırılır.

<br>


### whenContains

`whenContains` metodu, string belirtilen değeri içeriyorsa verilen closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('tony stark')
    ->whenContains('tony', function (Stringable $string) {
        return $string->title();
    });

// 'Tony Stark'
```

String, belirtilen değeri **içermiyorsa**, üçüncü parametre olarak verilen closure çalışır.

Bir dizi değer de geçebilirsiniz; string bu değerlerden **herhangi birini** içeriyorsa closure tetiklenir:

```php
$string = Str::of('tony stark')
    ->whenContains(['tony', 'hulk'], function (Stringable $string) {
        return $string->title();
    });

// Tony Stark
```

<br>


### whenContainsAll

`whenContainsAll` metodu, string belirtilen tüm alt string’leri içeriyorsa closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('tony stark')
    ->whenContainsAll(['tony', 'stark'], function (Stringable $string) {
        return $string->title();
    });

// 'Tony Stark'
```

<br>


### whenDoesntEndWith

`whenDoesntEndWith` metodu, string belirtilen değerle bitmiyorsa closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('disney world')->whenDoesntEndWith('land', function (Stringable $string) {
    return $string->title();
});

// 'Disney World'
```

<br>


### whenDoesntStartWith

`whenDoesntStartWith` metodu, string belirtilen değerle başlamıyorsa closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('disney world')->whenDoesntStartWith('sea', function (Stringable $string) {
    return $string->title();
});

// 'Disney World'
```

<br>


### whenEmpty

`whenEmpty` metodu, string boşsa verilen closure’ı çalıştırır.
Closure bir değer döndürürse, o değer metodun dönüş değeri olur; döndürmezse orijinal string döner:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('  ')->trim()->whenEmpty(function (Stringable $string) {
    return $string->prepend('Laravel');
});

// 'Laravel'
```

<br>


### whenNotEmpty

`whenNotEmpty` metodu, string boş **değilse** verilen closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('Framework')->whenNotEmpty(function (Stringable $string) {
    return $string->prepend('Laravel ');
});

// 'Laravel Framework'
```

<br>


### whenStartsWith

`whenStartsWith` metodu, string belirtilen değerle başlıyorsa closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('disney world')->whenStartsWith('disney', function (Stringable $string) {
    return $string->title();
});

// 'Disney World'
```

<br>


### whenEndsWith

`whenEndsWith` metodu, string belirtilen değerle bitiyorsa closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('disney world')->whenEndsWith('world', function (Stringable $string) {
    return $string->title();
});

// 'Disney World'
```

<br>


### whenExactly

`whenExactly` metodu, string verilen değere **tam olarak eşitse** closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('laravel')->whenExactly('laravel', function (Stringable $string) {
    return $string->title();
});

// 'Laravel'
```

<br>


### whenNotExactly

`whenNotExactly` metodu, string verilen değere **eşit değilse** closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('framework')->whenNotExactly('laravel', function (Stringable $string) {
    return $string->title();
});

// 'Framework'
```

<br>


### whenIs

`whenIs` metodu, string verilen kalıpla (pattern) eşleşiyorsa closure’ı çalıştırır.
Yıldız (`*`) karakteri joker olarak kullanılabilir:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('foo/bar')->whenIs('foo/*', function (Stringable $string) {
    return $string->append('/baz');
});

// 'foo/bar/baz'
```

<br>


### whenIsAscii

`whenIsAscii` metodu, string yalnızca ASCII karakterlerinden oluşuyorsa closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('laravel')->whenIsAscii(function (Stringable $string) {
    return $string->title();
});

// 'Laravel'
```

<br>


### whenIsUlid

`whenIsUlid` metodu, string geçerli bir **ULID** ise closure’ı çalıştırır:

```php
use Illuminate\Support\Str;

$string = Str::of('01gd6r360bp37zj17nxb55yv40')->whenIsUlid(function ($string) {
    return $string->substr(0, 8);
});

// '01gd6r36'
```

<br>


### whenIsUuid

`whenIsUuid` metodu, string geçerli bir **UUID** ise closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('a0a2a2d2-0b87-4a18-83f2-2529882be2de')->whenIsUuid(function (Stringable $string) {
    return $string->substr(0, 8);
});

// 'a0a2a2d2'
```

<br>


### whenTest

`whenTest` metodu, string belirtilen regex deseniyle eşleşiyorsa closure’ı çalıştırır:

```php
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

$string = Str::of('laravel framework')->whenTest('/laravel/', function (Stringable $string) {
    return $string->title();
});

// 'Laravel Framework'
```

<br>


### wordCount

`wordCount` metodu, string’in içerdiği **kelime sayısını** döndürür:

```php
use Illuminate\Support\Str;

Str::of('Hello, world!')->wordCount();

// 2
```

<br>


### words

`words` metodu, string’i belirli sayıda kelimeyle sınırlandırır.
İsteğe bağlı olarak, kısaltılmış string’in sonuna eklenecek dizgeyi belirtebilirsiniz:

```php
use Illuminate\Support\Str;

$string = Str::of('Perfectly balanced, as all things should be.')->words(3, ' >>>');

// Perfectly balanced, as >>>
```

<br>


### wrap

`wrap` metodu, string’in başına ve sonuna belirtilen karakter(ler)i ekler:

```php
use Illuminate\Support\Str;

Str::of('Laravel')->wrap('"');

// "Laravel"

Str::of('is')->wrap(before: 'This ', after: ' Laravel!');

// This is Laravel!
```

<br>

<br>


✅ **Laravel**, modern PHP ile yazılım geliştirmenin, dağıtımın ve izlenmenin en verimli yoludur.

```
```
