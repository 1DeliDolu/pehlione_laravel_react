
<br>


## Collections

<br>


### Introduction

`Illuminate\Support\Collection` sınıfı, dizi verileriyle çalışmak için kullanışlı ve akıcı (fluent) bir sarmalayıcı sağlar.  
Örneğin, aşağıdaki koda göz atalım. `collect` helper’ını kullanarak bir koleksiyon oluşturacağız, her bir elemana `strtoupper` fonksiyonunu uygulayacağız ve boş değerleri kaldıracağız:

```php
$collection = collect(['Taylor', 'Abigail', null])->map(function (?string $name) {
    return strtoupper($name);
})->reject(function (string $name) {
    return empty($name);
});
````

Görüldüğü gibi, `Collection` sınıfı, temel diziyi akıcı bir şekilde **map** ve **reduce** işlemlerine tabi tutmanıza olanak tanır.
Genel olarak koleksiyonlar **immutable**’dır; yani her `Collection` metodu tamamen yeni bir `Collection` örneği döndürür.

<br>


### Creating Collections

Yukarıda bahsedildiği gibi, `collect` helper’ı verilen dizi için yeni bir `Illuminate\Support\Collection` örneği döndürür.
Dolayısıyla bir koleksiyon oluşturmak oldukça basittir:

```php
$collection = collect([1, 2, 3]);
```

Ayrıca `make` ve `fromJson` metodlarını kullanarak da koleksiyon oluşturabilirsiniz.

Eloquent sorgularının sonuçları daima `Collection` örnekleri olarak döner.

<br>


### Extending Collections

Koleksiyonlar **"macroable"**’dır; yani çalışma zamanında `Collection` sınıfına yeni metotlar ekleyebilirsiniz.
`Illuminate\Support\Collection` sınıfının `macro` metodu, makronuz çağrıldığında çalışacak bir `closure` kabul eder.
Bu closure içinde `$this` kullanarak koleksiyonun diğer metodlarına erişebilirsiniz.

Aşağıdaki örnekte, `Collection` sınıfına bir `toUpper` metodu ekliyoruz:

```php
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

Collection::macro('toUpper', function () {
    return $this->map(function (string $value) {
        return Str::upper($value);
    });
});

$collection = collect(['first', 'second']);

$upper = $collection->toUpper();

// ['FIRST', 'SECOND']
```

Genellikle koleksiyon makrolarınızı bir **service provider**’ın `boot` metodunda tanımlamanız önerilir.

<br>


### Macro Arguments

Gerektiğinde, ek argümanlar kabul eden makrolar da tanımlayabilirsiniz:

```php
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

Collection::macro('toLocale', function (string $locale) {
    return $this->map(function (string $value) use ($locale) {
        return Lang::get($value, [], $locale);
    });
});

$collection = collect(['first', 'second']);

$translated = $collection->toLocale('es');
```

<br>


### Available Methods

Koleksiyon dokümantasyonunun geri kalan kısmında, `Collection` sınıfında mevcut olan her metodu tartışacağız.
Unutmayın, bu metodların tamamı zincirlenebilir (chained) şekilde kullanılabilir ve çoğu yeni bir `Collection` örneği döndürür.
Bu sayede, gerektiğinde orijinal koleksiyonu koruyabilirsiniz.

**Mevcut Metotlar:**

`after`, `all`, `average`, `avg`, `before`, `chunk`, `chunkWhile`, `collapse`, `collapseWithKeys`, `collect`, `combine`, `concat`,
`contains`, `containsOneItem`, `containsStrict`, `count`, `countBy`, `crossJoin`, `dd`, `diff`, `diffAssoc`, `diffAssocUsing`,
`diffKeys`, `doesntContain`, `doesntContainStrict`, `dot`, `dump`, `duplicates`, `duplicatesStrict`, `each`, `eachSpread`,
`ensure`, `every`, `except`, `filter`, `first`, `firstOrFail`, `firstWhere`, `flatMap`, `flatten`, `flip`, `forget`,
`forPage`, `fromJson`, `get`, `groupBy`, `has`, `hasAny`, `implode`, `intersect`, `intersectUsing`, `intersectAssoc`,
`intersectAssocUsing`, `intersectByKeys`, `isEmpty`, `isNotEmpty`, `join`, `keyBy`, `keys`, `last`, `lazy`, `macro`,
`make`, `map`, `mapInto`, `mapSpread`, `mapToGroups`, `mapWithKeys`, `max`, `median`, `merge`, `mergeRecursive`, `min`,
`mode`, `multiply`, `nth`, `only`, `pad`, `partition`, `percentage`, `pipe`, `pipeInto`, `pipeThrough`, `pluck`, `pop`,
`prepend`, `pull`, `push`, `put`, `random`, `range`, `reduce`, `reduceSpread`, `reject`, `replace`, `replaceRecursive`,
`reverse`, `search`, `select`, `shift`, `shuffle`, `skip`, `skipUntil`, `skipWhile`, `slice`, `sliding`, `sole`, `some`,
`sort`, `sortBy`, `sortByDesc`, `sortDesc`, `sortKeys`, `sortKeysDesc`, `sortKeysUsing`, `splice`, `split`, `splitIn`,
`sum`, `take`, `takeUntil`, `takeWhile`, `tap`, `times`, `toArray`, `toJson`, `toPrettyJson`, `transform`, `undot`,
`union`, `unique`, `uniqueStrict`, `unless`, `unlessEmpty`, `unlessNotEmpty`, `unwrap`, `value`, `values`, `when`,
`whenEmpty`, `whenNotEmpty`, `where`, `whereStrict`, `whereBetween`, `whereIn`, `whereInStrict`, `whereInstanceOf`,
`whereNotBetween`, `whereNotIn`, `whereNotInStrict`, `whereNotNull`, `whereNull`, `wrap`, `zip`

<br>


## Method Listing

<br>


### after()

`after` metodu, belirtilen öğeden sonraki öğeyi döndürür.
Belirtilen öğe bulunmazsa veya son öğeyse, `null` döner:

```php
$collection = collect([1, 2, 3, 4, 5]);

$collection->after(3);
// 4

$collection->after(5);
// null
```

Bu metod, "loose" karşılaştırma kullanır; yani `"4"` (string) değeri `4` (integer) ile eşit kabul edilir.
"Strict" karşılaştırma yapmak isterseniz, `strict` parametresini kullanabilirsiniz:

```php
collect([2, 4, 6, 8])->after('4', strict: true);
// null
```

Alternatif olarak, bir closure belirterek belirli bir koşulu sağlayan ilk öğeyi arayabilirsiniz:

```php
collect([2, 4, 6, 8])->after(function (int $item, int $key) {
    return $item > 5;
});
// 8
```

<br>


### all()

`all` metodu, koleksiyonun temsil ettiği temel diziyi döndürür:

```php
collect([1, 2, 3])->all();
// [1, 2, 3]
```

<br>


### average()

`average`, `avg` metodunun bir takma adıdır (alias).

<br>


### avg()

`avg` metodu, belirtilen anahtara ait değerlerin ortalamasını döndürür:

```php
$average = collect([
    ['foo' => 10],
    ['foo' => 10],
    ['foo' => 20],
    ['foo' => 40]
])->avg('foo');

// 20

$average = collect([1, 1, 2, 4])->avg();
// 2
```

<br>


### before()

`before` metodu, `after` metodunun tersidir.
Belirtilen öğeden **önceki** öğeyi döndürür.
Belirtilen öğe bulunmazsa veya ilk öğeyse, `null` döner:

```php
$collection = collect([1, 2, 3, 4, 5]);

$collection->before(3);
// 2

$collection->before(1);
// null

collect([2, 4, 6, 8])->before('4', strict: true);
// null

collect([2, 4, 6, 8])->before(function (int $item, int $key) {
    return $item > 5;
});
// 4
```

<br>


### chunk()

`chunk` metodu, koleksiyonu belirtilen boyutta daha küçük koleksiyonlara böler:

```php
$collection = collect([1, 2, 3, 4, 5, 6, 7]);

$chunks = $collection->chunk(4);

$chunks->all();
// [[1, 2, 3, 4], [5, 6, 7]]
```

Bu metod özellikle **Bootstrap** gibi grid sistemleriyle çalışırken kullanışlıdır.
Örneğin, bir ürün koleksiyonunu grid şeklinde göstermek isteyebilirsiniz:

```blade
@foreach ($products->chunk(3) as $chunk)
    <div class="row">
        @foreach ($chunk as $product)
            <div class="col-xs-4">{{ $product->name }}</div>
        @endforeach
    </div>
@endforeach
```

<br>


### chunkWhile()

`chunkWhile` metodu, verilen callback fonksiyonunun değerlendirmesine göre koleksiyonu birden fazla küçük koleksiyona böler.
Closure’a geçirilen `$chunk` değişkeni, önceki elemanı kontrol etmek için kullanılabilir:

```php
$collection = collect(str_split('AABBCCCD'));

$chunks = $collection->chunkWhile(function (string $value, int $key, Collection $chunk) {
    return $value === $chunk->last();
});

$chunks->all();
// [['A', 'A'], ['B', 'B'], ['C', 'C', 'C'], ['D']]
```

<br>


### collapse()

`collapse` metodu, diziler veya koleksiyonlardan oluşan bir koleksiyonu düzleştirerek tek bir koleksiyon haline getirir:

```php
$collection = collect([
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9],
]);

$collapsed = $collection->collapse();

$collapsed->all();
// [1, 2, 3, 4, 5, 6, 7, 8, 9]
```

<br>


### collapseWithKeys()

`collapseWithKeys` metodu, dizilerden veya koleksiyonlardan oluşan bir koleksiyonu **anahtarları koruyarak** düzleştirir.
Eğer koleksiyon zaten düzse, boş bir koleksiyon döner:

```php
$collection = collect([
    ['first'  => collect([1, 2, 3])],
    ['second' => [4, 5, 6]],
    ['third'  => collect([7, 8, 9])]
]);

$collapsed = $collection->collapseWithKeys();

$collapsed->all();

// [
//     'first'  => [1, 2, 3],
//     'second' => [4, 5, 6],
//     'third'  => [7, 8, 9],
// ]
```



<br>


### collect()

`collect` metodu, mevcut koleksiyondaki öğelerle yeni bir `Collection` örneği döndürür:

```php
$collectionA = collect([1, 2, 3]);

$collectionB = $collectionA->collect();

$collectionB->all();

// [1, 2, 3]
````

`collect` metodu özellikle **lazy collection**’ları standart `Collection` örneklerine dönüştürmek için kullanışlıdır:

```php
$lazyCollection = LazyCollection::make(function () {
    yield 1;
    yield 2;
    yield 3;
});

$collection = $lazyCollection->collect();

$collection::class;

// 'Illuminate\Support\Collection'

$collection->all();

// [1, 2, 3]
```

`collect` metodu, bir `Enumerable` örneğine sahip olduğunuzda ve **lazy olmayan** bir koleksiyon elde etmek istediğinizde özellikle faydalıdır.
`collect()` metodu `Enumerable` sözleşmesinin (contract) bir parçası olduğundan, güvenle bir `Collection` örneği almak için kullanılabilir.

<br>


### combine()

`combine` metodu, koleksiyondaki değerleri anahtar olarak, başka bir dizi veya koleksiyonun değerleriyle birleştirir:

```php
$collection = collect(['name', 'age']);

$combined = $collection->combine(['George', 29]);

$combined->all();

// ['name' => 'George', 'age' => 29]
```

<br>


### concat()

`concat` metodu, verilen dizi veya koleksiyonun değerlerini mevcut koleksiyonun sonuna ekler:

```php
$collection = collect(['John Doe']);

$concatenated = $collection->concat(['Jane Doe'])->concat(['name' => 'Johnny Doe']);

$concatenated->all();

// ['John Doe', 'Jane Doe', 'Johnny Doe']
```

`concat` metodu, eklenen öğeler için sayısal anahtarları yeniden indeksler.
Eğer **associative** (anahtar–değer) koleksiyonlardaki anahtarları korumak istiyorsanız, bunun yerine `merge` metodunu kullanın.

<br>


### contains()

`contains` metodu, koleksiyonun belirli bir öğeyi içerip içermediğini kontrol eder.
Bir closure geçirerek, belirtilen koşulu sağlayan bir öğe olup olmadığını test edebilirsiniz:

```php
$collection = collect([1, 2, 3, 4, 5]);

$collection->contains(function (int $value, int $key) {
    return $value > 5;
});

// false
```

Alternatif olarak, bir string değer geçerek koleksiyonun belirli bir değeri içerip içermediğini kontrol edebilirsiniz:

```php
$collection = collect(['name' => 'Desk', 'price' => 100]);

$collection->contains('Desk');

// true

$collection->contains('New York');

// false
```

Bir **key / value** çifti geçirerek de, koleksiyonda bu eşleşmenin olup olmadığını kontrol edebilirsiniz:

```php
$collection = collect([
    ['product' => 'Desk', 'price' => 200],
    ['product' => 'Chair', 'price' => 100],
]);

$collection->contains('product', 'Bookcase');

// false
```

`contains` metodu, değerleri kontrol ederken **"loose" karşılaştırma** kullanır; yani `"2"` string değeri `2` integer değeriyle eşit kabul edilir.
"Strict" karşılaştırma yapmak için `containsStrict` metodunu kullanabilirsiniz.

Tersi için, yani bir öğenin **bulunmadığını** kontrol etmek isterseniz `doesntContain` metoduna bakın.

<br>


### containsOneItem()

`containsOneItem` metodu, koleksiyonun yalnızca **tek bir öğe** içerip içermediğini belirler:

```php
collect([])->containsOneItem();
// false

collect(['1'])->containsOneItem();
// true

collect(['1', '2'])->containsOneItem();
// false

collect([1, 2, 3])->containsOneItem(fn (int $item) => $item === 2);
// true
```

<br>


### containsStrict()

Bu metod, `contains` metoduyla aynı imzaya sahiptir; ancak tüm değerleri **"strict" karşılaştırma** kullanarak kontrol eder.
Bu metodun davranışı, **Eloquent Collection** kullanıldığında farklılık gösterebilir.

<br>


### count()

`count` metodu, koleksiyondaki toplam öğe sayısını döndürür:

```php
$collection = collect([1, 2, 3, 4]);

$collection->count();

// 4
```

<br>


### countBy()

`countBy` metodu, koleksiyondaki değerlerin kaç kez geçtiğini sayar.
Varsayılan olarak, her öğenin kaç kez bulunduğunu sayar:

```php
$collection = collect([1, 2, 2, 2, 3]);

$counted = $collection->countBy();

$counted->all();

// [1 => 1, 2 => 3, 3 => 1]
```

Bir closure geçirerek, özel bir değere göre sayım yapabilirsiniz:

```php
$collection = collect(['alice@gmail.com', 'bob@yahoo.com', 'carlos@gmail.com']);

$counted = $collection->countBy(function (string $email) {
    return substr(strrchr($email, '@'), 1);
});

$counted->all();

// ['gmail.com' => 2, 'yahoo.com' => 1]
```

<br>


### crossJoin()

`crossJoin` metodu, koleksiyonun değerlerini verilen diziler veya koleksiyonlarla **çapraz birleştirir**, yani tüm olası kombinasyonları döndüren bir **Cartesian product** üretir:

```php
$collection = collect([1, 2]);

$matrix = $collection->crossJoin(['a', 'b']);

$matrix->all();

/*
    [
        [1, 'a'],
        [1, 'b'],
        [2, 'a'],
        [2, 'b'],
    ]
*/
```

Birden fazla diziyle de kullanılabilir:

```php
$collection = collect([1, 2]);

$matrix = $collection->crossJoin(['a', 'b'], ['I', 'II']);

$matrix->all();

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

<br>


### dd()

`dd` metodu, koleksiyondaki öğeleri ekrana yazdırır ve script’in çalışmasını durdurur:

```php
$collection = collect(['John Doe', 'Jane Doe']);

$collection->dd();

/*
    array:2 [
        0 => "John Doe"
        1 => "Jane Doe"
    ]
*/
```

Eğer script’in çalışmasını durdurmak istemiyorsanız, bunun yerine `dump` metodunu kullanabilirsiniz.

<br>


### diff()

`diff` metodu, koleksiyonu başka bir koleksiyon veya düz bir PHP dizisiyle karşılaştırır ve **yalnızca mevcut koleksiyonda olup diğerinde bulunmayan değerleri** döndürür:

```php
$collection = collect([1, 2, 3, 4, 5]);

$diff = $collection->diff([2, 4, 6, 8]);

$diff->all();

// [1, 3, 5]
```

Bu metodun davranışı, **Eloquent Collection** kullanıldığında farklılık gösterebilir.


<br>


### diffAssoc()

`diffAssoc` metodu, koleksiyonu anahtarlar **ve** değerler temelinde başka bir koleksiyon veya düz bir PHP dizisiyle karşılaştırır.  
Bu metod, yalnızca verilen koleksiyonda bulunmayan **anahtar / değer** çiftlerini döndürür:

```php
$collection = collect([
    'color' => 'orange',
    'type' => 'fruit',
    'remain' => 6,
]);

$diff = $collection->diffAssoc([
    'color' => 'yellow',
    'type' => 'fruit',
    'remain' => 3,
    'used' => 6,
]);

$diff->all();

// ['color' => 'orange', 'remain' => 6]
````

<br>


### diffAssocUsing()

`diffAssoc` metodundan farklı olarak, `diffAssocUsing` metodu indeks karşılaştırmaları için kullanıcı tanımlı bir callback fonksiyonu kabul eder:

```php
$collection = collect([
    'color' => 'orange',
    'type' => 'fruit',
    'remain' => 6,
]);

$diff = $collection->diffAssocUsing([
    'Color' => 'yellow',
    'Type' => 'fruit',
    'Remain' => 3,
], 'strnatcasecmp');

$diff->all();

// ['color' => 'orange', 'remain' => 6]
```

Callback fonksiyonu, sırasıyla 0’dan küçük, eşit veya büyük bir tamsayı döndüren bir karşılaştırma fonksiyonu olmalıdır.
Daha fazla bilgi için PHP’nin dahili olarak kullanılan `array_diff_uassoc` fonksiyonunun dökümantasyonuna bakabilirsiniz.

<br>


### diffKeys()

`diffKeys` metodu, koleksiyonu başka bir koleksiyon veya düz PHP dizisiyle **anahtarlarına göre** karşılaştırır.
Bu metod, yalnızca verilen koleksiyonda bulunmayan anahtar–değer çiftlerini döndürür:

```php
$collection = collect([
    'one' => 10,
    'two' => 20,
    'three' => 30,
    'four' => 40,
    'five' => 50,
]);

$diff = $collection->diffKeys([
    'two' => 2,
    'four' => 4,
    'six' => 6,
    'eight' => 8,
]);

$diff->all();

// ['one' => 10, 'three' => 30, 'five' => 50]
```

<br>


### doesntContain()

`doesntContain` metodu, koleksiyonun belirli bir öğeyi **içermediğini** belirler.
Bir closure geçirerek, verilen koşulu sağlamayan bir öğe olup olmadığını test edebilirsiniz:

```php
$collection = collect([1, 2, 3, 4, 5]);

$collection->doesntContain(function (int $value, int $key) {
    return $value < 5;
});

// false
```

Alternatif olarak, bir string geçirerek belirli bir değerin bulunmadığını kontrol edebilirsiniz:

```php
$collection = collect(['name' => 'Desk', 'price' => 100]);

$collection->doesntContain('Table');
// true

$collection->doesntContain('Desk');
// false
```

Bir **key / value** çifti de geçirilebilir; bu durumda verilen çiftin bulunmadığı test edilir:

```php
$collection = collect([
    ['product' => 'Desk', 'price' => 200],
    ['product' => 'Chair', 'price' => 100],
]);

$collection->doesntContain('product', 'Bookcase');

// true
```

`doesntContain` metodu, öğe değerlerini kontrol ederken **"loose" karşılaştırma** kullanır;
yani `"2"` string değeri `2` integer değeriyle eşit kabul edilir.

<br>


### doesntContainStrict()

Bu metod, `doesntContain` metoduyla aynı imzaya sahiptir; ancak değerleri **"strict" karşılaştırma** kullanarak kontrol eder.

<br>


### dot()

`dot` metodu, çok boyutlu bir koleksiyonu tek seviyeli hale getirir ve derinliği belirtmek için **dot (.) notasyonu** kullanır:

```php
$collection = collect(['products' => ['desk' => ['price' => 100]]]);

$flattened = $collection->dot();

$flattened->all();

// ['products.desk.price' => 100]
```

<br>


### dump()

`dump` metodu, koleksiyondaki öğeleri ekrana yazdırır:

```php
$collection = collect(['John Doe', 'Jane Doe']);

$collection->dump();

/*
    array:2 [
        0 => "John Doe"
        1 => "Jane Doe"
    ]
*/
```

Eğer dump işleminden sonra script’in çalışmasını durdurmak isterseniz, bunun yerine `dd` metodunu kullanın.

<br>


### duplicates()

`duplicates` metodu, koleksiyondaki yinelenen (duplicate) değerleri döndürür:

```php
$collection = collect(['a', 'b', 'a', 'c', 'b']);

$collection->duplicates();

// [2 => 'a', 4 => 'b']
```

Eğer koleksiyon diziler veya objeler içeriyorsa, yinelenen değerleri kontrol etmek için bir anahtar belirtebilirsiniz:

```php
$employees = collect([
    ['email' => 'abigail@example.com', 'position' => 'Developer'],
    ['email' => 'james@example.com', 'position' => 'Designer'],
    ['email' => 'victoria@example.com', 'position' => 'Developer'],
]);

$employees->duplicates('position');

// [2 => 'Developer']
```

<br>


### duplicatesStrict()

Bu metod, `duplicates` metoduyla aynı imzaya sahiptir; ancak değerleri **strict** karşılaştırma kullanarak denetler.

<br>


### each()

`each` metodu, koleksiyondaki her öğe üzerinde gezinir ve her bir öğeyi belirtilen closure’a geçirir:

```php
$collection = collect([1, 2, 3, 4]);

$collection->each(function (int $item, int $key) {
    // ...
});
```

Döngüyü durdurmak isterseniz, closure’dan `false` döndürebilirsiniz:

```php
$collection->each(function (int $item, int $key) {
    if (/* condition */) {
        return false;
    }
});
```

<br>


### eachSpread()

`eachSpread` metodu, koleksiyondaki öğeler üzerinde gezinir ve her bir alt öğeyi parametre olarak callback fonksiyonuna aktarır:

```php
$collection = collect([['John Doe', 35], ['Jane Doe', 33]]);

$collection->eachSpread(function (string $name, int $age) {
    // ...
});
```

Döngüyü durdurmak için `false` döndürebilirsiniz:

```php
$collection->eachSpread(function (string $name, int $age) {
    return false;
});
```

<br>


### ensure()

`ensure` metodu, koleksiyondaki tüm öğelerin belirtilen türde (veya türlerden birinde) olduğunu doğrular.
Eğer herhangi bir öğe uygun türde değilse, `UnexpectedValueException` fırlatılır:

```php
return $collection->ensure(User::class);

return $collection->ensure([User::class, Customer::class]);
```

`string`, `int`, `float`, `bool`, `array` gibi **primitive** türler de belirtilebilir:

```php
return $collection->ensure('int');
```

> ⚠️ `ensure` metodu, sonradan farklı türde öğelerin eklenmeyeceğini garanti etmez.

<br>


### every()

`every` metodu, koleksiyondaki tüm öğelerin belirtilen koşulu sağlayıp sağlamadığını doğrular:

```php
collect([1, 2, 3, 4])->every(function (int $value, int $key) {
    return $value > 2;
});

// false
```

Eğer koleksiyon boşsa, `every` metodu her zaman `true` döndürür:

```php
$collection = collect([]);

$collection->every(function (int $value, int $key) {
    return $value > 2;
});

// true
```

<br>


### except()

`except` metodu, belirtilen anahtarlara sahip olanlar hariç tüm öğeleri döndürür:

```php
$collection = collect(['product_id' => 1, 'price' => 100, 'discount' => false]);

$filtered = $collection->except(['price', 'discount']);

$filtered->all();

// ['product_id' => 1]
```

Bu metodun tersi için `only` metoduna bakabilirsiniz.
Ayrıca, **Eloquent Collection** kullanıldığında davranışı değişebilir.

<br>


### filter()

`filter` metodu, verilen callback fonksiyonunu kullanarak koleksiyonu filtreler.
Sadece koşulu sağlayan öğeler tutulur:

```php
$collection = collect([1, 2, 3, 4]);

$filtered = $collection->filter(function (int $value, int $key) {
    return $value > 2;
});

$filtered->all();

// [3, 4]
```

Eğer callback belirtilmezse, `false` değerine eşdeğer olan tüm öğeler kaldırılır:

```php
$collection = collect([1, 2, 3, null, false, '', 0, []]);

$collection->filter()->all();

// [1, 2, 3]
```

Bu metodun tersi için `reject` metoduna bakın.

<br>


### first()

`first` metodu, belirtilen koşulu sağlayan ilk öğeyi döndürür:

```php
collect([1, 2, 3, 4])->first(function (int $value, int $key) {
    return $value > 2;
});

// 3
```

Eğer argüman verilmezse, koleksiyondaki ilk öğe döner.
Koleksiyon boşsa `null` döner:

```php
collect([1, 2, 3, 4])->first();

// 1
```

<br>


### firstOrFail()

`firstOrFail` metodu `first` metoduyla aynıdır; ancak eşleşme bulunmazsa bir `Illuminate\Support\ItemNotFoundException` hatası fırlatır:

```php
collect([1, 2, 3, 4])->firstOrFail(function (int $value, int $key) {
    return $value > 5;
});

// Throws ItemNotFoundException...
```

Argüman olmadan da çağrılabilir; eğer koleksiyon boşsa yine istisna fırlatır:

```php
collect([])->firstOrFail();

// Throws ItemNotFoundException...
```

<br>


### firstWhere()

`firstWhere` metodu, belirtilen **key / value** eşleşmesine sahip ilk öğeyi döndürür:

```php
$collection = collect([
    ['name' => 'Regena', 'age' => null],
    ['name' => 'Linda', 'age' => 14],
    ['name' => 'Diego', 'age' => 23],
    ['name' => 'Linda', 'age' => 84],
]);

$collection->firstWhere('name', 'Linda');

// ['name' => 'Linda', 'age' => 14]
```

Bir karşılaştırma operatörü de kullanılabilir:

```php
$collection->firstWhere('age', '>=', 18);

// ['name' => 'Diego', 'age' => 23]
```

Ayrıca yalnızca bir argüman geçerek, belirtilen anahtarın değeri "truthy" olan ilk öğeyi alabilirsiniz:

```php
$collection->firstWhere('age');

// ['name' => 'Linda', 'age' => 14]
```

<br>


### flatMap()

`flatMap` metodu, koleksiyon üzerinde döner ve her öğeyi verilen closure’a geçirir.
Closure, öğeyi değiştirebilir ve yeni bir koleksiyon oluşturulur. Ardından, sonuç bir seviye düzleştirilir:

```php
$collection = collect([
    ['name' => 'Sally'],
    ['school' => 'Arkansas'],
    ['age' => 28]
]);

$flattened = $collection->flatMap(function (array $values) {
    return array_map('strtoupper', $values);
});

$flattened->all();

// ['name' => 'SALLY', 'school' => 'ARKANSAS', 'age' => '28'];
```

<br>


### flatten()

`flatten` metodu, çok boyutlu bir koleksiyonu tek boyutlu hale getirir:

```php
$collection = collect([
    'name' => 'Taylor',
    'languages' => [
        'PHP', 'JavaScript'
    ]
]);

$flattened = $collection->flatten();

$flattened->all();

// ['Taylor', 'PHP', 'JavaScript'];
```

Gerekirse, `flatten` metoduna bir **depth (derinlik)** argümanı geçebilirsiniz:

```php
$collection = collect([
    'Apple' => [
        [
            'name' => 'iPhone 6S',
            'brand' => 'Apple'
        ],
    ],
    'Samsung' => [
        [
            'name' => 'Galaxy S7',
            'brand' => 'Samsung'
        ],
    ],
]);

$products = $collection->flatten(1);

$products->values()->all();

/*
    [
        ['name' => 'iPhone 6S', 'brand' => 'Apple'],
        ['name' => 'Galaxy S7', 'brand' => 'Samsung'],
    ]
*/
```

Eğer `depth` belirtilmezse, tüm iç diziler düzleştirilecektir (örneğin: `['iPhone 6S', 'Apple', 'Galaxy S7', 'Samsung']`).
Derinlik değeri, kaç seviyenin düzleştirileceğini belirler.

<br>


### flip()

`flip` metodu, koleksiyonun anahtarlarıyla değerlerini yer değiştirir:

```php
$collection = collect(['name' => 'Taylor', 'framework' => 'Laravel']);

$flipped = $collection->flip();

$flipped->all();

// ['Taylor' => 'name', 'Laravel' => 'framework']
```

<br>


### forget()

`forget` metodu, belirtilen anahtara (veya anahtarlara) sahip öğeleri koleksiyondan kaldırır:

```php
$collection = collect(['name' => 'Taylor', 'framework' => 'Laravel']);

// Tek bir anahtar silme...
$collection->forget('name');

// ['framework' => 'Laravel']

// Birden fazla anahtar silme...
$collection->forget(['name', 'framework']);

// []
```

> ⚠️ Diğer çoğu koleksiyon metodunun aksine, `forget` yeni bir koleksiyon döndürmez.
> Mevcut koleksiyonu **yerinde (in-place)** değiştirir.

<br>


### forPage()

`forPage` metodu, belirtilen sayfa numarasına ve sayfa başına gösterilecek öğe sayısına göre yeni bir koleksiyon döndürür:

```php
$collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

$chunk = $collection->forPage(2, 3);

$chunk->all();

// [4, 5, 6]
```


<br>


### fromJson()

`fromJson` statik metodu, verilen bir JSON string’ini `json_decode` fonksiyonunu kullanarak çözümler ve bu verilerden yeni bir `Collection` örneği oluşturur:

```php
use Illuminate\Support\Collection;

$json = json_encode([
    'name' => 'Taylor Otwell',
    'role' => 'Developer',
    'status' => 'Active',
]);

$collection = Collection::fromJson($json);
````

<br>


### get()

`get` metodu, belirtilen anahtardaki öğeyi döndürür.
Eğer anahtar mevcut değilse, `null` döner:

```php
$collection = collect(['name' => 'Taylor', 'framework' => 'Laravel']);

$value = $collection->get('name');

// Taylor
```

İsteğe bağlı olarak, ikinci argüman olarak bir varsayılan değer belirtebilirsiniz:

```php
$collection = collect(['name' => 'Taylor', 'framework' => 'Laravel']);

$value = $collection->get('age', 34);

// 34
```

Varsayılan değer olarak bir callback de geçebilirsiniz.
Anahtar bulunmazsa, callback’in sonucu döndürülür:

```php
$collection->get('email', function () {
    return 'taylor@example.com';
});

// taylor@example.com
```

<br>


### groupBy()

`groupBy` metodu, koleksiyondaki öğeleri belirtilen anahtara göre gruplandırır:

```php
$collection = collect([
    ['account_id' => 'account-x10', 'product' => 'Chair'],
    ['account_id' => 'account-x10', 'product' => 'Bookcase'],
    ['account_id' => 'account-x11', 'product' => 'Desk'],
]);

$grouped = $collection->groupBy('account_id');

$grouped->all();

/*
[
    'account-x10' => [
        ['account_id' => 'account-x10', 'product' => 'Chair'],
        ['account_id' => 'account-x10', 'product' => 'Bookcase'],
    ],
    'account-x11' => [
        ['account_id' => 'account-x11', 'product' => 'Desk'],
    ],
]
*/
```

Bir string anahtar yerine callback de geçebilirsiniz.
Callback, gruplandırılacak değeri döndürmelidir:

```php
$grouped = $collection->groupBy(function (array $item, int $key) {
    return substr($item['account_id'], -3);
});

$grouped->all();

/*
[
    'x10' => [
        ['account_id' => 'account-x10', 'product' => 'Chair'],
        ['account_id' => 'account-x10', 'product' => 'Bookcase'],
    ],
    'x11' => [
        ['account_id' => 'account-x11', 'product' => 'Desk'],
    ],
]
*/
```

Birden fazla gruplama kriteri de belirtebilirsiniz.
Her dizi öğesi, çok boyutlu gruplandırma düzeyine uygulanır:

```php
$data = new Collection([
    10 => ['user' => 1, 'skill' => 1, 'roles' => ['Role_1', 'Role_3']],
    20 => ['user' => 2, 'skill' => 1, 'roles' => ['Role_1', 'Role_2']],
    30 => ['user' => 3, 'skill' => 2, 'roles' => ['Role_1']],
    40 => ['user' => 4, 'skill' => 2, 'roles' => ['Role_2']],
]);

$result = $data->groupBy(['skill', function (array $item) {
    return $item['roles'];
}], preserveKeys: true);
```

Yukarıdaki örnekte sonuç:

```php
[
    1 => [
        'Role_1' => [
            10 => ['user' => 1, 'skill' => 1, 'roles' => ['Role_1', 'Role_3']],
            20 => ['user' => 2, 'skill' => 1, 'roles' => ['Role_1', 'Role_2']],
        ],
        'Role_2' => [
            20 => ['user' => 2, 'skill' => 1, 'roles' => ['Role_1', 'Role_2']],
        ],
        'Role_3' => [
            10 => ['user' => 1, 'skill' => 1, 'roles' => ['Role_1', 'Role_3']],
        ],
    ],
    2 => [
        'Role_1' => [
            30 => ['user' => 3, 'skill' => 2, 'roles' => ['Role_1']],
        ],
        'Role_2' => [
            40 => ['user' => 4, 'skill' => 2, 'roles' => ['Role_2']],
        ],
    ],
];
```

<br>


### has()

`has` metodu, belirtilen anahtarın koleksiyonda mevcut olup olmadığını belirler:

```php
$collection = collect(['account_id' => 1, 'product' => 'Desk', 'amount' => 5]);

$collection->has('product');
// true

$collection->has(['product', 'amount']);
// true

$collection->has(['amount', 'price']);
// false
```

<br>


### hasAny()

`hasAny` metodu, verilen anahtarlardan **herhangi birinin** koleksiyonda mevcut olup olmadığını belirler:

```php
$collection = collect(['account_id' => 1, 'product' => 'Desk', 'amount' => 5]);

$collection->hasAny(['product', 'price']);
// true

$collection->hasAny(['name', 'price']);
// false
```

<br>


### implode()

`implode` metodu, koleksiyondaki değerleri birleştirir.
Koleksiyon diziler veya nesneler içeriyorsa, birleştirilecek öznitelik anahtarını ve ayraç (“glue”) string’ini belirtmelisiniz:

```php
$collection = collect([
    ['account_id' => 1, 'product' => 'Desk'],
    ['account_id' => 2, 'product' => 'Chair'],
]);

$collection->implode('product', ', ');

// 'Desk, Chair'
```

Koleksiyon yalnızca string veya sayısal değerler içeriyorsa, yalnızca ayraç string’i geçebilirsiniz:

```php
collect([1, 2, 3, 4, 5])->implode('-');

// '1-2-3-4-5'
```

Değerleri biçimlendirmek isterseniz, `implode` metoduna bir closure geçebilirsiniz:

```php
$collection->implode(function (array $item, int $key) {
    return strtoupper($item['product']);
}, ', ');

// 'DESK, CHAIR'
```

<br>


### intersect()

`intersect` metodu, verilen dizi veya koleksiyonda bulunmayan değerleri orijinal koleksiyondan kaldırır.
Sonuç koleksiyon, orijinal anahtarları korur:

```php
$collection = collect(['Desk', 'Sofa', 'Chair']);

$intersect = $collection->intersect(['Desk', 'Chair', 'Bookcase']);

$intersect->all();

// [0 => 'Desk', 2 => 'Chair']
```

> Bu metodun davranışı, **Eloquent Collection** kullanıldığında değişebilir.

<br>


### intersectUsing()

`intersectUsing` metodu, verilen dizi veya koleksiyonla karşılaştırma yaparken özel bir callback kullanır.
Değerleri karşılaştırmak için callback fonksiyonu kullanılır ve anahtarlar korunur:

```php
$collection = collect(['Desk', 'Sofa', 'Chair']);

$intersect = $collection->intersectUsing(['desk', 'chair', 'bookcase'], function (string $a, string $b) {
    return strcasecmp($a, $b);
});

$intersect->all();

// [0 => 'Desk', 2 => 'Chair']
```

<br>


### intersectAssoc()

`intersectAssoc` metodu, koleksiyonu başka bir koleksiyon veya diziyle karşılaştırır ve **her iki** koleksiyonda da mevcut olan **anahtar / değer çiftlerini** döndürür:

```php
$collection = collect([
    'color' => 'red',
    'size' => 'M',
    'material' => 'cotton'
]);

$intersect = $collection->intersectAssoc([
    'color' => 'blue',
    'size' => 'M',
    'material' => 'polyester'
]);

$intersect->all();

// ['size' => 'M']
```

<br>


### intersectAssocUsing()

`intersectAssocUsing` metodu, iki koleksiyonun anahtar ve değerlerini karşılaştırır,
ve hem anahtar hem değer eşitliğini belirlemek için özel bir callback fonksiyonu kullanır:

```php
$collection = collect([
    'color' => 'red',
    'Size' => 'M',
    'material' => 'cotton',
]);

$intersect = $collection->intersectAssocUsing([
    'color' => 'blue',
    'size' => 'M',
    'material' => 'polyester',
], function (string $a, string $b) {
    return strcasecmp($a, $b);
});

$intersect->all();

// ['Size' => 'M']
```

<br>


### intersectByKeys()

`intersectByKeys` metodu, belirtilen dizide veya koleksiyonda bulunmayan **anahtarları ve bunlara karşılık gelen değerleri** orijinal koleksiyondan kaldırır:

```php
$collection = collect([
    'serial' => 'UX301', 'type' => 'screen', 'year' => 2009,
]);

$intersect = $collection->intersectByKeys([
    'reference' => 'UX404', 'type' => 'tab', 'year' => 2011,
]);

$intersect->all();

// ['type' => 'screen', 'year' => 2009]
```

<br>


### isEmpty()

`isEmpty` metodu, koleksiyon boşsa `true`, değilse `false` döndürür:

```php
collect([])->isEmpty();

// true
```

<br>


### isNotEmpty()

`isNotEmpty` metodu, koleksiyon **boş değilse** `true`, boşsa `false` döndürür:

```php
collect([])->isNotEmpty();

// false
```

<br>


### join()

`join` metodu, koleksiyondaki değerleri bir string olarak birleştirir.
İkinci argüman ile, son öğenin string’e nasıl ekleneceğini belirtebilirsiniz:

```php
collect(['a', 'b', 'c'])->join(', '); // 'a, b, c'
collect(['a', 'b', 'c'])->join(', ', ', and '); // 'a, b, and c'
collect(['a', 'b'])->join(', ', ' and '); // 'a and b'
collect(['a'])->join(', ', ' and '); // 'a'
collect([])->join(', ', ' and '); // ''
```

<br>


### keyBy()

`keyBy` metodu, koleksiyonu belirtilen anahtara göre yeniden indeksler.
Aynı anahtara sahip birden fazla öğe varsa, yalnızca sonuncusu kalır:

```php
$collection = collect([
    ['product_id' => 'prod-100', 'name' => 'Desk'],
    ['product_id' => 'prod-200', 'name' => 'Chair'],
]);

$keyed = $collection->keyBy('product_id');

$keyed->all();

/*
[
    'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
    'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
]
*/
```

Bir callback geçerek de anahtarlama işlemini özelleştirebilirsiniz:

```php
$keyed = $collection->keyBy(function (array $item, int $key) {
    return strtoupper($item['product_id']);
});

$keyed->all();

/*
[
    'PROD-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
    'PROD-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
]
*/
```

<br>


### keys()

`keys` metodu, koleksiyondaki tüm anahtarları döndürür:

```php
$collection = collect([
    'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
    'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
]);

$keys = $collection->keys();

$keys->all();

// ['prod-100', 'prod-200']
```

<br>


### last()

`last` metodu, belirtilen koşulu sağlayan son öğeyi döndürür:

```php
collect([1, 2, 3, 4])->last(function (int $value, int $key) {
    return $value < 3;
});

// 2
```

Eğer argüman verilmezse, koleksiyondaki son öğe döndürülür.
Koleksiyon boşsa `null` döner:

```php
collect([1, 2, 3, 4])->last();

// 4
```



<br>


### lazy()

`lazy` metodu, koleksiyondaki mevcut öğelerden yeni bir `LazyCollection` örneği döndürür:

```php
$lazyCollection = collect([1, 2, 3, 4])->lazy();

$lazyCollection::class;

// Illuminate\Support\LazyCollection

$lazyCollection->all();

// [1, 2, 3, 4]
````

Bu yöntem özellikle **çok büyük koleksiyonlarla** çalışırken faydalıdır.
Örneğin, büyük veri kümelerinde dönüşüm işlemleri yaparken belleği verimli kullanmak için `lazy()` kullanabilirsiniz:

```php
$count = $hugeCollection
    ->lazy()
    ->where('country', 'FR')
    ->where('balance', '>', '100')
    ->count();
```

Koleksiyonu `LazyCollection`’a dönüştürerek, filtreleme işlemleri sırasında fazladan bellek ayrılmasını önlemiş olursunuz.
Orijinal koleksiyon bellekte kalır, ancak sonraki işlemler **lazy** olarak yürütülür.

<br>


### macro()

`macro` statik metodu, çalışma zamanında `Collection` sınıfına yeni metodlar eklemenizi sağlar.
Daha fazla bilgi için “Extending Collections” (Koleksiyonları Genişletme) bölümüne bakın.

<br>


### make()

`make` statik metodu, yeni bir `Collection` örneği oluşturur.
Bkz: “Creating Collections” bölümü.

```php
use Illuminate\Support\Collection;

$collection = Collection::make([1, 2, 3]);
```

<br>


### map()

`map` metodu, koleksiyondaki her öğe üzerinde gezinir ve verilen callback’e aktarır.
Callback öğeyi değiştirebilir ve döndürebilir; bu şekilde yeni bir koleksiyon oluşturulur:

```php
$collection = collect([1, 2, 3, 4, 5]);

$multiplied = $collection->map(function (int $item, int $key) {
    return $item * 2;
});

$multiplied->all();

// [2, 4, 6, 8, 10]
```

`map` metodu **yeni bir koleksiyon** döndürür; orijinal koleksiyonu değiştirmez.
Orijinal koleksiyonu dönüştürmek istiyorsanız, `transform` metodunu kullanın.

<br>


### mapInto()

`mapInto()` metodu, koleksiyon üzerinde gezinir ve her değeri belirtilen sınıfın kurucusuna (constructor) geçirerek yeni bir örnek oluşturur:

```php
class Currency
{
    /**
     * Yeni bir currency örneği oluşturur.
     */
    function __construct(
        public string $code,
    ) {}
}

$collection = collect(['USD', 'EUR', 'GBP']);

$currencies = $collection->mapInto(Currency::class);

$currencies->all();

// [Currency('USD'), Currency('EUR'), Currency('GBP')]
```

<br>


### mapSpread()

`mapSpread` metodu, koleksiyondaki iç içe geçmiş (nested) öğeler üzerinde gezinir ve her bir öğeyi closure’a parametre olarak aktarır.
Closure öğeyi değiştirebilir ve yeni bir koleksiyon oluşturur:

```php
$collection = collect([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]);

$chunks = $collection->chunk(2);

$sequence = $chunks->mapSpread(function (int $even, int $odd) {
    return $even + $odd;
});

$sequence->all();

// [1, 5, 9, 13, 17]
```

<br>


### mapToGroups()

`mapToGroups` metodu, koleksiyondaki öğeleri verilen closure’a göre gruplar.
Closure, tek bir **key / value** çifti içeren bir **associative array** döndürmelidir:

```php
$collection = collect([
    ['name' => 'John Doe', 'department' => 'Sales'],
    ['name' => 'Jane Doe', 'department' => 'Sales'],
    ['name' => 'Johnny Doe', 'department' => 'Marketing']
]);

$grouped = $collection->mapToGroups(function (array $item, int $key) {
    return [$item['department'] => $item['name']];
});

$grouped->all();

/*
[
    'Sales' => ['John Doe', 'Jane Doe'],
    'Marketing' => ['Johnny Doe'],
]
*/

$grouped->get('Sales')->all();

// ['John Doe', 'Jane Doe']
```

<br>


### mapWithKeys()

`mapWithKeys` metodu, koleksiyondaki her öğe üzerinde gezinir ve closure’dan dönen key/value çiftlerini kullanarak yeni bir koleksiyon oluşturur.
Closure tek bir **associative array** döndürmelidir:

```php
$collection = collect([
    [
        'name' => 'John',
        'department' => 'Sales',
        'email' => 'john@example.com',
    ],
    [
        'name' => 'Jane',
        'department' => 'Marketing',
        'email' => 'jane@example.com',
    ]
]);

$keyed = $collection->mapWithKeys(function (array $item, int $key) {
    return [$item['email'] => $item['name']];
});

$keyed->all();

/*
[
    'john@example.com' => 'John',
    'jane@example.com' => 'Jane',
]
*/
```

<br>


### max()

`max` metodu, belirtilen anahtarın maksimum değerini döndürür:

```php
$max = collect([
    ['foo' => 10],
    ['foo' => 20]
])->max('foo');

// 20

$max = collect([1, 2, 3, 4, 5])->max();

// 5
```

<br>


### median()

`median` metodu, belirtilen anahtarın **ortanca (median)** değerini döndürür:

```php
$median = collect([
    ['foo' => 10],
    ['foo' => 10],
    ['foo' => 20],
    ['foo' => 40]
])->median('foo');

// 15

$median = collect([1, 1, 2, 4])->median();

// 1.5
```

<br>


### merge()

`merge` metodu, verilen dizi veya koleksiyonu mevcut koleksiyonla birleştirir.
Eğer anahtarlar string ise, yeni değer eski değerin üzerine yazılır:

```php
$collection = collect(['product_id' => 1, 'price' => 100]);

$merged = $collection->merge(['price' => 200, 'discount' => false]);

$merged->all();

// ['product_id' => 1, 'price' => 200, 'discount' => false]
```

Eğer anahtarlar sayısalsa, değerler koleksiyonun sonuna eklenir:

```php
$collection = collect(['Desk', 'Chair']);

$merged = $collection->merge(['Bookcase', 'Door']);

$merged->all();

// ['Desk', 'Chair', 'Bookcase', 'Door']
```

<br>


### mergeRecursive()

`mergeRecursive` metodu, verilen dizi veya koleksiyonu mevcut koleksiyonla **özyinelemeli (recursive)** olarak birleştirir.
Aynı string anahtarlara sahip değerler dizi haline getirilir ve bu işlem iç içe şekilde uygulanır:

```php
$collection = collect(['product_id' => 1, 'price' => 100]);

$merged = $collection->mergeRecursive([
    'product_id' => 2,
    'price' => 200,
    'discount' => false
]);

$merged->all();

// ['product_id' => [1, 2], 'price' => [100, 200], 'discount' => false]
```

<br>


### min()

`min` metodu, belirtilen anahtarın minimum değerini döndürür:

```php
$min = collect([['foo' => 10], ['foo' => 20]])->min('foo');

// 10

$min = collect([1, 2, 3, 4, 5])->min();

// 1
```

<br>


### mode()

`mode` metodu, belirtilen anahtarın **mod (en sık tekrar eden değer)** değerini döndürür:

```php
$mode = collect([
    ['foo' => 10],
    ['foo' => 10],
    ['foo' => 20],
    ['foo' => 40]
])->mode('foo');

// [10]

$mode = collect([1, 1, 2, 4])->mode();

// [1]

$mode = collect([1, 1, 2, 2])->mode();

// [1, 2]
```

<br>


### multiply()

`multiply` metodu, koleksiyondaki tüm öğelerin belirtilen sayıda kopyasını oluşturur:

```php
$users = collect([
    ['name' => 'User #1', 'email' => 'user1@example.com'],
    ['name' => 'User #2', 'email' => 'user2@example.com'],
])->multiply(3);

/*
[
    ['name' => 'User #1', 'email' => 'user1@example.com'],
    ['name' => 'User #2', 'email' => 'user2@example.com'],
    ['name' => 'User #1', 'email' => 'user1@example.com'],
    ['name' => 'User #2', 'email' => 'user2@example.com'],
    ['name' => 'User #1', 'email' => 'user1@example.com'],
    ['name' => 'User #2', 'email' => 'user2@example.com'],
]
*/
```



<br>


### nth()

`nth` metodu, koleksiyondan her n’inci öğeyi içeren yeni bir koleksiyon döndürür:

```php
$collection = collect(['a', 'b', 'c', 'd', 'e', 'f']);

$collection->nth(4);

// ['a', 'e']
````

İsteğe bağlı olarak, başlangıç ofsetini (kaçıncı öğeden başlanacağını) ikinci argüman olarak geçebilirsiniz:

```php
$collection->nth(4, 1);

// ['b', 'f']
```

---

### only()

`only` metodu, belirtilen anahtarlara sahip koleksiyon öğelerini döndürür:

```php
$collection = collect([
    'product_id' => 1,
    'name' => 'Desk',
    'price' => 100,
    'discount' => false
]);

$filtered = $collection->only(['product_id', 'name']);

$filtered->all();

// ['product_id' => 1, 'name' => 'Desk']
```

> Bunun tersi için bkz: **`except()`** metodu.
> Bu metodun davranışı **Eloquent Collections** kullanıldığında değişebilir.

---

### pad()

`pad` metodu, koleksiyonu belirtilen uzunluğa ulaşana kadar verilen değerle doldurur.
Bu metod, PHP’nin `array_pad()` fonksiyonu gibi çalışır.

Negatif uzunluk verilirse, doldurma işlemi **sola** yapılır.
Eğer belirtilen uzunluk, mevcut eleman sayısından küçük veya eşitse, doldurma yapılmaz:

```php
$collection = collect(['A', 'B', 'C']);

$filtered = $collection->pad(5, 0);

$filtered->all();

// ['A', 'B', 'C', 0, 0]

$filtered = $collection->pad(-5, 0);

$filtered->all();

// [0, 0, 'A', 'B', 'C']
```

---

### partition()

`partition` metodu, koleksiyon öğelerini verilen koşula göre iki gruba ayırır.
Genellikle PHP’nin **array destructuring** özelliğiyle birlikte kullanılır:

```php
$collection = collect([1, 2, 3, 4, 5, 6]);

[$underThree, $equalOrAboveThree] = $collection->partition(function (int $i) {
    return $i < 3;
});

$underThree->all();
// [1, 2]

$equalOrAboveThree->all();
// [3, 4, 5, 6]
```

> Bu metodun davranışı **Eloquent Collections** ile kullanıldığında değişebilir.

---

### percentage()

`percentage` metodu, koleksiyondaki öğelerin yüzde kaçının belirtilen koşulu sağladığını hesaplar:

```php
$collection = collect([1, 1, 2, 2, 2, 3]);

$percentage = $collection->percentage(fn (int $value) => $value === 1);

// 33.33
```

Varsayılan olarak sonuç **iki ondalık basamağa** yuvarlanır, ancak bunu değiştirebilirsiniz:

```php
$percentage = $collection->percentage(fn (int $value) => $value === 1, precision: 3);

// 33.333
```

---

### pipe()

`pipe` metodu, koleksiyonu belirtilen closure’a geçirir ve closure’ın döndürdüğü sonucu döndürür:

```php
$collection = collect([1, 2, 3]);

$piped = $collection->pipe(function (Collection $collection) {
    return $collection->sum();
});

// 6
```

---

### pipeInto()

`pipeInto` metodu, belirtilen sınıfın yeni bir örneğini oluşturur ve koleksiyonu constructor’a geçirir:

```php
class ResourceCollection
{
    public function __construct(
        public Collection $collection,
    ) {}
}

$collection = collect([1, 2, 3]);

$resource = $collection->pipeInto(ResourceCollection::class);

$resource->collection->all();

// [1, 2, 3]
```

---

### pipeThrough()

`pipeThrough` metodu, koleksiyonu closure dizisine sırayla aktarır.
Her closure bir işlem uygular ve sonucu bir sonrakine aktarır:

```php
use Illuminate\Support\Collection;

$collection = collect([1, 2, 3]);

$result = $collection->pipeThrough([
    function (Collection $collection) {
        return $collection->merge([4, 5]);
    },
    function (Collection $collection) {
        return $collection->sum();
    },
]);

// 15
```

---

### pluck()

`pluck` metodu, belirtilen anahtarın tüm değerlerini koleksiyondan çıkarır:

```php
$collection = collect([
    ['product_id' => 'prod-100', 'name' => 'Desk'],
    ['product_id' => 'prod-200', 'name' => 'Chair'],
]);

$plucked = $collection->pluck('name');

$plucked->all();

// ['Desk', 'Chair']
```

Sonuç koleksiyonunun anahtarlarını belirlemek için ikinci bir parametre geçebilirsiniz:

```php
$plucked = $collection->pluck('name', 'product_id');

$plucked->all();

// ['prod-100' => 'Desk', 'prod-200' => 'Chair']
```

`pluck`, **“dot notation”** kullanarak iç içe geçmiş değerleri de alabilir:

```php
$collection = collect([
    [
        'name' => 'Laracon',
        'speakers' => [
            'first_day' => ['Rosa', 'Judith'],
        ],
    ],
    [
        'name' => 'VueConf',
        'speakers' => [
            'first_day' => ['Abigail', 'Joey'],
        ],
    ],
]);

$plucked = $collection->pluck('speakers.first_day');

$plucked->all();

// [['Rosa', 'Judith'], ['Abigail', 'Joey']]
```

Aynı anahtara sahip birden fazla öğe varsa, **sonuncusu** koleksiyonda kalır:

```php
$collection = collect([
    ['brand' => 'Tesla',  'color' => 'red'],
    ['brand' => 'Pagani', 'color' => 'white'],
    ['brand' => 'Tesla',  'color' => 'black'],
    ['brand' => 'Pagani', 'color' => 'orange'],
]);

$plucked = $collection->pluck('color', 'brand');

$plucked->all();

// ['Tesla' => 'black', 'Pagani' => 'orange']
```

---

### pop()

`pop` metodu, koleksiyonun son öğesini kaldırır ve döndürür.
Koleksiyon boşsa `null` döner:

```php
$collection = collect([1, 2, 3, 4, 5]);

$collection->pop();

// 5

$collection->all();

// [1, 2, 3, 4]
```

Bir tam sayı geçirerek birden fazla öğeyi sondan kaldırabilirsiniz:

```php
$collection = collect([1, 2, 3, 4, 5]);

$collection->pop(3);

// collect([5, 4, 3])

$collection->all();

// [1, 2]
```

---

### prepend()

`prepend` metodu, koleksiyonun **başına** yeni bir öğe ekler:

```php
$collection = collect([1, 2, 3, 4, 5]);

$collection->prepend(0);

$collection->all();

// [0, 1, 2, 3, 4, 5]
```

Anahtar belirterek ekleme yapabilirsiniz:

```php
$collection = collect(['one' => 1, 'two' => 2]);

$collection->prepend(0, 'zero');

$collection->all();

// ['zero' => 0, 'one' => 1, 'two' => 2]
```

---

### pull()

`pull` metodu, belirtilen anahtardaki öğeyi koleksiyondan kaldırır ve döndürür:

```php
$collection = collect(['product_id' => 'prod-100', 'name' => 'Desk']);

$collection->pull('name');

// 'Desk'

$collection->all();

// ['product_id' => 'prod-100']
```

---

### push()

`push` metodu, koleksiyonun **sonuna** öğe ekler:

```php
$collection = collect([1, 2, 3, 4]);

$collection->push(5);

$collection->all();

// [1, 2, 3, 4, 5]
```

Birden fazla öğeyi de ekleyebilirsiniz:

```php
$collection = collect([1, 2, 3, 4]);

$collection->push(5, 6, 7);

$collection->all();

// [1, 2, 3, 4, 5, 6, 7]
```

---

### put()

`put` metodu, belirtilen anahtar ve değeri koleksiyona ekler veya mevcutsa günceller:

```php
$collection = collect(['product_id' => 1, 'name' => 'Desk']);

$collection->put('price', 100);

$collection->all();

// ['product_id' => 1, 'name' => 'Desk', 'price' => 100]
```

---

### random()

`random` metodu, koleksiyondan rastgele bir öğe döndürür:

```php
$collection = collect([1, 2, 3, 4, 5]);

$collection->random();

// 4 (örnek sonuç)
```

Kaç adet öğe çekileceğini belirtebilirsiniz:

```php
$random = $collection->random(3);

$random->all();

// [2, 4, 5] (rastgele)
```

Eğer istenen sayıda öğe koleksiyonda yoksa, `InvalidArgumentException` fırlatılır.

Closure da geçebilirsiniz. Closure, mevcut koleksiyon örneğini parametre olarak alır:

```php
use Illuminate\Support\Collection;

$random = $collection->random(fn (Collection $items) => min(10, count($items)));

$random->all();

// [1, 2, 3, 4, 5] (rastgele)
```


<br>


### range()

`range` metodu, belirtilen aralıkta yer alan tam sayılardan oluşan bir koleksiyon döndürür:

```php
$collection = collect()->range(3, 6);

$collection->all();

// [3, 4, 5, 6]
````

---

### reduce()

`reduce` metodu, koleksiyonu tek bir değere indirger.
Her yinelemede (iteration) önceki sonucun değeri bir sonraki adıma aktarılır:

```php
$collection = collect([1, 2, 3]);

$total = $collection->reduce(function (?int $carry, int $item) {
    return $carry + $item;
});

// 6
```

İlk yinelemede `$carry` değeri `null`’dır, ancak ikinci argümanla başlangıç değeri belirtebilirsiniz:

```php
$collection->reduce(function (int $carry, int $item) {
    return $carry + $item;
}, 4);

// 10
```

Ayrıca `reduce` metodu, callback’e anahtarları da geçirir:

```php
$collection = collect([
    'usd' => 1400,
    'gbp' => 1200,
    'eur' => 1000,
]);

$ratio = [
    'usd' => 1,
    'gbp' => 1.37,
    'eur' => 1.22,
];

$collection->reduce(function (int $carry, int $value, string $key) use ($ratio) {
    return $carry + ($value * $ratio[$key]);
}, 0);

// 4264
```

---

### reduceSpread()

`reduceSpread` metodu, koleksiyonu birden fazla değerden oluşan bir diziye indirger.
`reduce` metoduna benzer, ancak birden fazla başlangıç değeri kabul eder:

```php
[$creditsRemaining, $batch] = Image::where('status', 'unprocessed')
    ->get()
    ->reduceSpread(function (int $creditsRemaining, Collection $batch, Image $image) {
        if ($creditsRemaining >= $image->creditsRequired()) {
            $batch->push($image);

            $creditsRemaining -= $image->creditsRequired();
        }

        return [$creditsRemaining, $batch];
    }, $creditsAvailable, collect());
```

---

### reject()

`reject` metodu, closure’da belirtilen koşulu sağlayan öğeleri koleksiyondan çıkarır.
Closure `true` döndürürse, o öğe **kaldırılır**:

```php
$collection = collect([1, 2, 3, 4]);

$filtered = $collection->reject(function (int $value, int $key) {
    return $value > 2;
});

$filtered->all();

// [1, 2]
```

> Tersi için bkz: **`filter()`** metodu.

---

### replace()

`replace` metodu `merge` metoduna benzer, ancak hem string hem de **sayısal anahtarları** eşleştirip değiştirir:

```php
$collection = collect(['Taylor', 'Abigail', 'James']);

$replaced = $collection->replace([1 => 'Victoria', 3 => 'Finn']);

$replaced->all();

// ['Taylor', 'Victoria', 'James', 'Finn']
```

---

### replaceRecursive()

`replaceRecursive` metodu, `replace` metoduna benzer şekilde çalışır,
ancak iç içe dizilerde (nested arrays) aynı işlemi **özyinelemeli (recursive)** olarak uygular:

```php
$collection = collect([
    'Taylor',
    'Abigail',
    ['James', 'Victoria', 'Finn']
]);

$replaced = $collection->replaceRecursive([
    'Charlie',
    2 => [1 => 'King']
]);

$replaced->all();

// ['Charlie', 'Abigail', ['James', 'King', 'Finn']]
```

---

### reverse()

`reverse` metodu, koleksiyon öğelerinin sırasını ters çevirir ve anahtarları korur:

```php
$collection = collect(['a', 'b', 'c', 'd', 'e']);

$reversed = $collection->reverse();

$reversed->all();

/*
[
    4 => 'e',
    3 => 'd',
    2 => 'c',
    1 => 'b',
    0 => 'a',
]
*/
```

---

### search()

`search` metodu, koleksiyon içinde belirtilen değeri arar ve bulunursa **anahtarını** döndürür.
Bulunamazsa `false` döner:

```php
$collection = collect([2, 4, 6, 8]);

$collection->search(4);

// 1
```

Varsayılan olarak **gevşek karşılaştırma (loose comparison)** yapılır.
**Sıkı (strict)** karşılaştırma yapmak için ikinci argüman olarak `true` geçin:

```php
collect([2, 4, 6, 8])->search('4', strict: true);

// false
```

Closure da geçebilirsiniz; bu durumda, koşulu sağlayan ilk öğenin anahtarı döner:

```php
collect([2, 4, 6, 8])->search(function (int $item, int $key) {
    return $item > 5;
});

// 2
```

---

### select()

`select` metodu, koleksiyondaki belirli anahtarları seçer — SQL’deki `SELECT` ifadesine benzer:

```php
$users = collect([
    ['name' => 'Taylor Otwell', 'role' => 'Developer', 'status' => 'active'],
    ['name' => 'Victoria Faith', 'role' => 'Researcher', 'status' => 'active'],
]);

$users->select(['name', 'role']);

/*
[
    ['name' => 'Taylor Otwell', 'role' => 'Developer'],
    ['name' => 'Victoria Faith', 'role' => 'Researcher'],
],
*/
```

---

### shift()

`shift` metodu, koleksiyonun ilk öğesini kaldırır ve döndürür:

```php
$collection = collect([1, 2, 3, 4, 5]);

$collection->shift();

// 1

$collection->all();

// [2, 3, 4, 5]
```

Bir tam sayı geçirerek birden fazla öğeyi baştan kaldırabilirsiniz:

```php
$collection = collect([1, 2, 3, 4, 5]);

$collection->shift(3);

// collect([1, 2, 3])

$collection->all();

// [4, 5]
```

---

### shuffle()

`shuffle` metodu, koleksiyon öğelerini rastgele karıştırır:

```php
$collection = collect([1, 2, 3, 4, 5]);

$shuffled = $collection->shuffle();

$shuffled->all();

// [3, 2, 5, 1, 4] (rastgele)
```

---

### skip()

`skip` metodu, koleksiyonun başından belirtilen sayıda öğeyi atlayarak yeni bir koleksiyon döndürür:

```php
$collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

$collection = $collection->skip(4);

$collection->all();

// [5, 6, 7, 8, 9, 10]
```

---

### skipUntil()

`skipUntil` metodu, verilen koşul `false` döndürdüğü sürece öğeleri atlar.
İlk kez `true` döndüğünde kalan öğeler yeni koleksiyon olarak döner:

```php
$collection = collect([1, 2, 3, 4]);

$subset = $collection->skipUntil(function (int $item) {
    return $item >= 3;
});

$subset->all();

// [3, 4]
```

Basit bir değer de geçebilirsiniz:

```php
$collection = collect([1, 2, 3, 4]);

$subset = $collection->skipUntil(3);

$subset->all();

// [3, 4]
```

Eğer değer bulunmazsa veya callback `true` dönmezse, boş koleksiyon döner.

---

### skipWhile()

`skipWhile` metodu, callback `true` döndürdüğü sürece öğeleri atlar.
Callback `false` döndürdüğünde kalan öğeler döndürülür:

```php
$collection = collect([1, 2, 3, 4]);

$subset = $collection->skipWhile(function (int $item) {
    return $item <= 3;
});

$subset->all();

// [4]
```

Callback hiçbir zaman `false` döndürmezse, boş koleksiyon döner.

---

### slice()

`slice` metodu, belirtilen dizinden itibaren koleksiyonun bir dilimini döndürür:

```php
$collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

$slice = $collection->slice(4);

$slice->all();

// [5, 6, 7, 8, 9, 10]
```

İkinci argümanla döndürülecek öğe sayısını sınırlayabilirsiniz:

```php
$slice = $collection->slice(4, 2);

$slice->all();

// [5, 6]
```

Dilim orijinal anahtarları korur. Anahtarları sıfırlamak için `values()` metodunu kullanabilirsiniz.

---

### sliding()

`sliding` metodu, koleksiyonu “kaydırmalı pencere” (sliding window) mantığıyla gruplar:

```php
$collection = collect([1, 2, 3, 4, 5]);

$chunks = $collection->sliding(2);

$chunks->toArray();

// [[1, 2], [2, 3], [3, 4], [4, 5]]
```

Bu metod, özellikle `eachSpread()` ile birlikte kullanışlıdır:

```php
$transactions->sliding(2)->eachSpread(function (Collection $previous, Collection $current) {
    $current->total = $previous->total + $current->amount;
});
```

İkinci argüman olarak “step” değeri geçebilirsiniz (pencere kayma aralığı):

```php
$collection = collect([1, 2, 3, 4, 5]);

$chunks = $collection->sliding(3, step: 2);

$chunks->toArray();

// [[1, 2, 3], [3, 4, 5]]
```

---

### sole()

`sole` metodu, koşulu **yalnızca bir öğe** sağlıyorsa o öğeyi döndürür.
Birden fazla öğe koşulu sağlarsa, `MultipleItemsFoundException`;
hiç öğe bulunmazsa `ItemNotFoundException` fırlatılır:

```php
collect([1, 2, 3, 4])->sole(function (int $value, int $key) {
    return $value === 2;
});

// 2
```

Anahtar/değer çiftiyle de kullanılabilir:

```php
$collection = collect([
    ['product' => 'Desk', 'price' => 200],
    ['product' => 'Chair', 'price' => 100],
]);

$collection->sole('product', 'Chair');

// ['product' => 'Chair', 'price' => 100]
```

Tek öğeli koleksiyonlarda parametresiz çağrılabilir:

```php
$collection = collect([
    ['product' => 'Desk', 'price' => 200],
]);

$collection->sole();

// ['product' => 'Desk', 'price' => 200]
```

---

### some()

`some` metodu, `contains()` metodunun **takma adıdır (alias)**.

---

### sort()

`sort` metodu, koleksiyonu sıralar.
Anahtarları korur; `values()` ile sıfırlayabilirsiniz:

```php
$collection = collect([5, 3, 1, 2, 4]);

$sorted = $collection->sort();

$sorted->values()->all();

// [1, 2, 3, 4, 5]
```

Gelişmiş sıralama için özel bir callback verebilirsiniz (bkz. PHP `uasort` fonksiyonu).

İç içe diziler veya nesneler sıralamak için bkz: `sortBy()` veya `sortByDesc()`.

---

### sortBy()

`sortBy` metodu, belirtilen anahtara göre sıralama yapar.
Anahtarlar korunur; `values()` ile yeniden sıralayabilirsiniz:

```php
$collection = collect([
    ['name' => 'Desk', 'price' => 200],
    ['name' => 'Chair', 'price' => 100],
    ['name' => 'Bookcase', 'price' => 150],
]);

$sorted = $collection->sortBy('price');

$sorted->values()->all();

/*
[
    ['name' => 'Chair', 'price' => 100],
    ['name' => 'Bookcase', 'price' => 150],
    ['name' => 'Desk', 'price' => 200],
]
*/
```

Doğal sıralama için `SORT_NATURAL` bayrağını geçebilirsiniz:

```php
$collection = collect([
    ['title' => 'Item 1'],
    ['title' => 'Item 12'],
    ['title' => 'Item 3'],
]);

$sorted = $collection->sortBy('title', SORT_NATURAL);

$sorted->values()->all();

/*
[
    ['title' => 'Item 1'],
    ['title' => 'Item 3'],
    ['title' => 'Item 12'],
]
*/
```

Kendi closure’ınızı geçerek özelleştirilmiş sıralama yapabilirsiniz:

```php
$collection = collect([
    ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
    ['name' => 'Chair', 'colors' => ['Black']],
    ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
]);

$sorted = $collection->sortBy(function (array $product) {
    return count($product['colors']);
});

$sorted->values()->all();

/*
[
    ['name' => 'Chair', 'colors' => ['Black']],
    ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
    ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
]
*/
```

Birden fazla kritere göre sıralama yapmak için dizi olarak tanımlayabilirsiniz:

```php
$collection = collect([
    ['name' => 'Taylor Otwell', 'age' => 34],
    ['name' => 'Abigail Otwell', 'age' => 30],
    ['name' => 'Taylor Otwell', 'age' => 36],
    ['name' => 'Abigail Otwell', 'age' => 32],
]);

$sorted = $collection->sortBy([
    ['name', 'asc'],
    ['age', 'desc'],
]);

$sorted->values()->all();

/*
[
    ['name' => 'Abigail Otwell', 'age' => 32],
    ['name' => 'Abigail Otwell', 'age' => 30],
    ['name' => 'Taylor Otwell', 'age' => 36],
    ['name' => 'Taylor Otwell', 'age' => 34],
]
*/
```

Ayrıca, her sıralama kriterini tanımlamak için closure kullanabilirsiniz:

```php
$sorted = $collection->sortBy([
    fn (array $a, array $b) => $a['name'] <=> $b['name'],
    fn (array $a, array $b) => $b['age'] <=> $a['age'],
]);
```

---


<br>


### sortByDesc()

`sortByDesc` metodu, `sortBy` ile aynı yapıya sahiptir ancak koleksiyonu **ters sırada (azalan)** sıralar.

---

### sortDesc()

`sortDesc` metodu, `sort` metodunun tersidir; koleksiyonu azalan sırada sıralar:

```php
$collection = collect([5, 3, 1, 2, 4]);

$sorted = $collection->sortDesc();

$sorted->values()->all();

// [5, 4, 3, 2, 1]
````

> `sort` metodunun aksine, `sortDesc` bir closure kabul etmez.
> Eğer özel bir karşılaştırma algoritması kullanmak istiyorsanız `sort` metodunu kullanmalı ve karşılaştırmayı tersine çevirmelisiniz.

---

### sortKeys()

`sortKeys` metodu, koleksiyonu altta yatan dizinin **anahtarlarına göre** sıralar:

```php
$collection = collect([
    'id' => 22345,
    'first' => 'John',
    'last' => 'Doe',
]);

$sorted = $collection->sortKeys();

$sorted->all();

/*
[
    'first' => 'John',
    'id' => 22345,
    'last' => 'Doe',
]
*/
```

---

### sortKeysDesc()

`sortKeysDesc` metodu, `sortKeys` ile aynı imzaya sahiptir ancak anahtarları **azalan sırada** sıralar.

---

### sortKeysUsing()

`sortKeysUsing` metodu, koleksiyonu anahtarlara göre sıralar ancak sıralamayı özel bir callback fonksiyonuna göre yapar:

```php
$collection = collect([
    'ID' => 22345,
    'first' => 'John',
    'last' => 'Doe',
]);

$sorted = $collection->sortKeysUsing('strnatcasecmp');

$sorted->all();

/*
[
    'first' => 'John',
    'ID' => 22345,
    'last' => 'Doe',
]
*/
```

Callback, bir karşılaştırma fonksiyonu olmalı ve **0'dan küçük, eşit veya büyük** bir tamsayı döndürmelidir.
Bu metot dahili olarak PHP’nin `uksort()` fonksiyonunu kullanır.

---

### splice()

`splice` metodu, belirtilen konumdan itibaren öğeleri çıkarır ve çıkarılan parçayı döndürür:

```php
$collection = collect([1, 2, 3, 4, 5]);

$chunk = $collection->splice(2);

$chunk->all();

// [3, 4, 5]

$collection->all();

// [1, 2]
```

İkinci argüman olarak **çıkarılacak öğe sayısını** belirtebilirsiniz:

```php
$collection = collect([1, 2, 3, 4, 5]);

$chunk = $collection->splice(2, 1);

$chunk->all();

// [3]

$collection->all();

// [1, 2, 4, 5]
```

Üçüncü argüman olarak **yerine eklenecek yeni öğeleri** belirtebilirsiniz:

```php
$collection = collect([1, 2, 3, 4, 5]);

$chunk = $collection->splice(2, 1, [10, 11]);

$chunk->all();

// [3]

$collection->all();

// [1, 2, 10, 11, 4, 5]
```

---

### split()

`split` metodu, koleksiyonu belirtilen sayıda **gruba** böler:

```php
$collection = collect([1, 2, 3, 4, 5]);

$groups = $collection->split(3);

$groups->all();

// [[1, 2], [3, 4], [5]]
```

---

### splitIn()

`splitIn` metodu da koleksiyonu belirtilen sayıda gruba böler,
ancak son grup hariç diğer gruplar **tam olarak doldurulur**:

```php
$collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

$groups = $collection->splitIn(3);

$groups->all();

// [[1, 2, 3, 4], [5, 6, 7, 8], [9, 10]]
```

---

### sum()

`sum` metodu, koleksiyondaki tüm değerlerin toplamını döndürür:

```php
collect([1, 2, 3, 4, 5])->sum();

// 15
```

İç içe diziler veya nesneler varsa, toplanacak değeri belirten anahtarı geçebilirsiniz:

```php
$collection = collect([
    ['name' => 'JavaScript: The Good Parts', 'pages' => 176],
    ['name' => 'JavaScript: The Definitive Guide', 'pages' => 1096],
]);

$collection->sum('pages');

// 1272
```

Closure kullanarak toplanacak değeri özelleştirebilirsiniz:

```php
$collection = collect([
    ['name' => 'Chair', 'colors' => ['Black']],
    ['name' => 'Desk', 'colors' => ['Black', 'Mahogany']],
    ['name' => 'Bookcase', 'colors' => ['Red', 'Beige', 'Brown']],
]);

$collection->sum(function (array $product) {
    return count($product['colors']);
});

// 6
```

---

### take()

`take` metodu, koleksiyondan belirtilen sayıda öğeyi alarak yeni bir koleksiyon döndürür:

```php
$collection = collect([0, 1, 2, 3, 4, 5]);

$chunk = $collection->take(3);

$chunk->all();

// [0, 1, 2]
```

Negatif bir sayı geçirerek sondan öğeler alabilirsiniz:

```php
$collection = collect([0, 1, 2, 3, 4, 5]);

$chunk = $collection->take(-2);

$chunk->all();

// [4, 5]
```

---

### takeUntil()

`takeUntil` metodu, callback `true` döndürene kadar olan öğeleri döndürür:

```php
$collection = collect([1, 2, 3, 4]);

$subset = $collection->takeUntil(function (int $item) {
    return $item >= 3;
});

$subset->all();

// [1, 2]
```

Basit bir değer de geçebilirsiniz:

```php
$collection = collect([1, 2, 3, 4]);

$subset = $collection->takeUntil(3);

$subset->all();

// [1, 2]
```

Eğer değer bulunmazsa veya callback hiçbir zaman `true` döndürmezse, tüm koleksiyon döner.

---

### takeWhile()

`takeWhile` metodu, callback `false` döndürene kadar olan öğeleri döndürür:

```php
$collection = collect([1, 2, 3, 4]);

$subset = $collection->takeWhile(function (int $item) {
    return $item < 3;
});

$subset->all();

// [1, 2]
```

Eğer callback hiç `false` döndürmezse, tüm koleksiyon döndürülür.

---

### tap()

`tap` metodu, koleksiyonu callback’e geçirerek üzerinde yan işlemler yapmanıza olanak tanır,
ancak koleksiyonun kendisini değiştirmez:

```php
collect([2, 4, 3, 1, 5])
    ->sort()
    ->tap(function (Collection $collection) {
        Log::debug('Values after sorting', $collection->values()->all());
    })
    ->shift();

// 1
```

---

### times()

`times` statik metodu, verilen closure’ı belirtilen sayıda çağırarak yeni bir koleksiyon oluşturur:

```php
$collection = Collection::times(10, function (int $number) {
    return $number * 9;
});

$collection->all();

// [9, 18, 27, 36, 45, 54, 63, 72, 81, 90]
```

---

### toArray()

`toArray` metodu, koleksiyonu düz bir PHP dizisine dönüştürür.
Eğer koleksiyon Eloquent modeller içeriyorsa, modeller de dizilere dönüştürülür:

```php
$collection = collect(['name' => 'Desk', 'price' => 200]);

$collection->toArray();

/*
[
    ['name' => 'Desk', 'price' => 200],
]
*/
```

> `toArray`, koleksiyon içindeki tüm `Arrayable` nesneleri de dönüştürür.
> Ham (işlenmemiş) diziyi almak istiyorsanız `all()` metodunu kullanın.

---

### toJson()

`toJson` metodu, koleksiyonu JSON string’ine dönüştürür:

```php
$collection = collect(['name' => 'Desk', 'price' => 200]);

$collection->toJson();

// '{"name":"Desk","price":200}'
```

---

### toPrettyJson()

`toPrettyJson` metodu, koleksiyonu **biçimlendirilmiş (pretty-printed)** JSON string’ine dönüştürür:

```php
$collection = collect(['name' => 'Desk', 'price' => 200]);

$collection->toPrettyJson();
```

---

### transform()

`transform` metodu, koleksiyondaki her öğe üzerinde işlem yapar ve yeni değerle değiştirir.
Bu metot, orijinal koleksiyonu **değiştirir**:

```php
$collection = collect([1, 2, 3, 4, 5]);

$collection->transform(function (int $item, int $key) {
    return $item * 2;
});

$collection->all();

// [2, 4, 6, 8, 10]
```

> Eğer orijinali değiştirmek istemiyorsanız, `map()` metodunu kullanın.

---

### undot()

`undot` metodu, “dot notation” ile belirtilmiş tek boyutlu diziyi çok boyutlu hale dönüştürür:

```php
$person = collect([
    'name.first_name' => 'Marie',
    'name.last_name' => 'Valentine',
    'address.line_1' => '2992 Eagle Drive',
    'address.line_2' => '',
    'address.suburb' => 'Detroit',
    'address.state' => 'MI',
    'address.postcode' => '48219'
]);

$person = $person->undot();

$person->toArray();

/*
[
    "name" => [
        "first_name" => "Marie",
        "last_name" => "Valentine",
    ],
    "address" => [
        "line_1" => "2992 Eagle Drive",
        "line_2" => "",
        "suburb" => "Detroit",
        "state" => "MI",
        "postcode" => "48219",
    ],
]
*/
```

---

(👉 Devamında `union()`, `unique()`, `unless()`, `unwrap()`, `when()` ve `where()` metotlarının çevirisiyle devam edebilirim.
İstersen oradan sürdüreyim mi?)



<br>


### whereIn()

`whereIn` metodu, koleksiyondan belirtilen anahtarın değerinin verilen dizi içinde **olmayan** öğeleri kaldırır:

```php
$collection = collect([
    ['product' => 'Desk', 'price' => 200],
    ['product' => 'Chair', 'price' => 100],
    ['product' => 'Bookcase', 'price' => 150],
    ['product' => 'Door', 'price' => 100],
]);

$filtered = $collection->whereIn('price', [150, 200]);

$filtered->all();

/*
[
    ['product' => 'Desk', 'price' => 200],
    ['product' => 'Bookcase', 'price' => 150],
]
*/
````

> `whereIn` gevşek karşılaştırma (loose comparison) kullanır.
> Yani `"150"` string değeri, `150` tamsayısına eşit kabul edilir.
> Katı karşılaştırma (strict comparison) için `whereInStrict()` metodunu kullanın.

---

### whereInStrict()

`whereInStrict` metodu, `whereIn` ile aynı imzaya sahiptir ancak **tüm değerleri katı (strict)** biçimde karşılaştırır.

---

### whereInstanceOf()

`whereInstanceOf` metodu, koleksiyonu belirli bir sınıf türüne göre filtreler:

```php
use App\Models\User;
use App\Models\Post;

$collection = collect([
    new User,
    new User,
    new Post,
]);

$filtered = $collection->whereInstanceOf(User::class);

$filtered->all();

// [App\Models\User, App\Models\User]
```

---

### whereNotBetween()

`whereNotBetween` metodu, belirtilen anahtarın değerinin verilen aralığın **dışında** olup olmadığını kontrol eder:

```php
$collection = collect([
    ['product' => 'Desk', 'price' => 200],
    ['product' => 'Chair', 'price' => 80],
    ['product' => 'Bookcase', 'price' => 150],
    ['product' => 'Pencil', 'price' => 30],
    ['product' => 'Door', 'price' => 100],
]);

$filtered = $collection->whereNotBetween('price', [100, 200]);

$filtered->all();

/*
[
    ['product' => 'Chair', 'price' => 80],
    ['product' => 'Pencil', 'price' => 30],
]
*/
```

---

### whereNotIn()

`whereNotIn` metodu, belirtilen anahtarın değeri verilen dizi içinde olan öğeleri koleksiyondan **kaldırır**:

```php
$collection = collect([
    ['product' => 'Desk', 'price' => 200],
    ['product' => 'Chair', 'price' => 100],
    ['product' => 'Bookcase', 'price' => 150],
    ['product' => 'Door', 'price' => 100],
]);

$filtered = $collection->whereNotIn('price', [150, 200]);

$filtered->all();

/*
[
    ['product' => 'Chair', 'price' => 100],
    ['product' => 'Door', 'price' => 100],
]
*/
```

> `whereNotIn` gevşek karşılaştırma yapar.
> Katı karşılaştırma için `whereNotInStrict()` metodunu kullanın.

---

### whereNotInStrict()

`whereNotInStrict` metodu, `whereNotIn` ile aynı imzaya sahiptir ancak değerleri **katı biçimde** karşılaştırır.

---

### whereNotNull()

`whereNotNull` metodu, belirtilen anahtarın değeri **null olmayan** öğeleri döndürür:

```php
$collection = collect([
    ['name' => 'Desk'],
    ['name' => null],
    ['name' => 'Bookcase'],
    ['name' => 0],
    ['name' => ''],
]);

$filtered = $collection->whereNotNull('name');

$filtered->all();

/*
[
    ['name' => 'Desk'],
    ['name' => 'Bookcase'],
    ['name' => 0],
    ['name' => ''],
]
*/
```

---

### whereNull()

`whereNull` metodu, belirtilen anahtarın değeri **null olan** öğeleri döndürür:

```php
$collection = collect([
    ['name' => 'Desk'],
    ['name' => null],
    ['name' => 'Bookcase'],
    ['name' => 0],
    ['name' => ''],
]);

$filtered = $collection->whereNull('name');

$filtered->all();

/*
[
    ['name' => null],
]
*/
```

---

### wrap()

`wrap` statik metodu, verilen değeri gerektiğinde bir `Collection` içine sarar:

```php
use Illuminate\Support\Collection;

$collection = Collection::wrap('John Doe');

$collection->all();

// ['John Doe']

$collection = Collection::wrap(['John Doe']);

$collection->all();

// ['John Doe']

$collection = Collection::wrap(collect('John Doe'));

$collection->all();

// ['John Doe']
```

---

### zip()

`zip` metodu, koleksiyon öğelerini, verilen dizideki öğelerle **karşılıklı indekslerine göre birleştirir**:

```php
$collection = collect(['Chair', 'Desk']);

$zipped = $collection->zip([100, 200]);

$zipped->all();

// [['Chair', 100], ['Desk', 200]]
```

---

<br>


## Higher Order Messages

Koleksiyonlar, “**higher order messages**” olarak adlandırılan kısa sözdizimi desteği sunar.
Bu özellik, koleksiyon öğeleri üzerinde sık kullanılan işlemleri daha basit bir şekilde çağırmanıza olanak tanır.

Bu özelliği destekleyen metotlar şunlardır:
`average`, `avg`, `contains`, `each`, `every`, `filter`, `first`, `flatMap`, `groupBy`,
`keyBy`, `map`, `max`, `min`, `partition`, `reject`, `skipUntil`, `skipWhile`,
`some`, `sortBy`, `sortByDesc`, `sum`, `takeUntil`, `takeWhile`, ve `unique`.

Örneğin, koleksiyondaki her nesne için bir metot çağırmak isterseniz:

```php
use App\Models\User;

$users = User::where('votes', '>', 500)->get();

$users->each->markAsVip();
```

Aynı şekilde, koleksiyondaki tüm kullanıcıların toplam “votes” değerini almak için:

```php
$users = User::where('group', 'Development')->get();

return $users->sum->votes;
```

---

<br>


## Lazy Collections

### Introduction

Laravel’ın **LazyCollection** sınıfı, PHP’nin **generator** özelliğini kullanarak büyük veri kümeleriyle düşük bellek tüketimiyle çalışmanıza olanak tanır.

Örneğin, gigabaytlarca boyutunda bir log dosyasını okumanız gerekiyorsa, dosyanın tamamını belleğe yüklemek yerine `LazyCollection` kullanabilirsiniz:

```php
use App\Models\LogEntry;
use Illuminate\Support\LazyCollection;

LazyCollection::make(function () {
    $handle = fopen('log.txt', 'r');

    while (($line = fgets($handle)) !== false) {
        yield $line;
    }

    fclose($handle);
})
->chunk(4)
->map(function (array $lines) {
    return LogEntry::fromLines($lines);
})
->each(function (LogEntry $logEntry) {
    // Process the log entry...
});
```

Ayrıca, 10.000’den fazla Eloquent modelini işlerken `cursor()` metodu bir `LazyCollection` döndürür.
Bu sayede veritabanına sadece **bir sorgu** yapılır ve modeller **tek tek belleğe yüklenir**:

```php
use App\Models\User;

$users = User::cursor()->filter(function (User $user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

---

### Creating Lazy Collections

Bir **LazyCollection** örneği oluşturmak için, `make()` metoduna bir PHP **generator** fonksiyonu geçin:

```php
use Illuminate\Support\LazyCollection;

LazyCollection::make(function () {
    $handle = fopen('log.txt', 'r');

    while (($line = fgets($handle)) !== false) {
        yield $line;
    }

    fclose($handle);
});
```

---

### The Enumerable Contract

`Collection` ve `LazyCollection` sınıfları, `Illuminate\Support\Enumerable` sözleşmesini (contract) uygular.
Bu sözleşme, her iki sınıfın da aşağıdaki metotlara sahip olmasını sağlar:

`all`, `average`, `chunk`, `collapse`, `collect`, `combine`, `concat`, `contains`, `count`,
`diff`, `each`, `every`, `filter`, `first`, `flatMap`, `flip`, `forPage`, `get`,
`groupBy`, `has`, `implode`, `intersect`, `isEmpty`, `join`, `keyBy`, `keys`,
`last`, `map`, `max`, `median`, `merge`, `min`, `mode`, `nth`, `only`, `pad`,
`pipe`, `pluck`, `reduce`, `reject`, `replace`, `reverse`, `search`, `shuffle`,
`slice`, `some`, `sort`, `sortBy`, `sortKeys`, `split`, `sum`, `take`, `tap`,
`toArray`, `toJson`, `union`, `unique`, `unless`, `unwrap`, `values`, `when`,
`where`, `whereIn`, `wrap`, `zip`, vb.

> Ancak, `shift`, `pop`, `prepend` gibi **koleksiyonu değiştiren (mutating)** metotlar `LazyCollection` üzerinde mevcut değildir.

---

<br>


## Lazy Collection Methods

### takeUntilTimeout()

Belirtilen zamana kadar öğeleri döndüren bir `LazyCollection` oluşturur:

```php
$lazyCollection = LazyCollection::times(INF)
    ->takeUntilTimeout(now()->addMinute());

$lazyCollection->each(function (int $number) {
    dump($number);
    sleep(1);
});
```

Bu yöntem, örneğin her 15 dakikada bir çalışan görevlerde, belirli bir süre boyunca işlem yapmanızı sağlar:

```php
use App\Models\Invoice;
use Illuminate\Support\Carbon;

Invoice::pending()->cursor()
    ->takeUntilTimeout(
        Carbon::createFromTimestamp(LARAVEL_START)->add(14, 'minutes')
    )
    ->each(fn (Invoice $invoice) => $invoice->submit());
```

---

### tapEach()

`each()` metodundan farklı olarak, `tapEach()` yalnızca öğeler **çekilirken** callback’i çağırır:

```php
$lazyCollection = LazyCollection::times(INF)->tapEach(function (int $value) {
    dump($value);
});

// Henüz hiçbir şey yazdırılmadı...

$array = $lazyCollection->take(3)->all();

// 1
// 2
// 3
```

---

### throttle()

`throttle()` metodu, her öğeyi belirtilen saniye aralıklarıyla döndürür.
Bu özellikle **API oran sınırlamaları (rate limiting)** durumlarında faydalıdır:

```php
use App\Models\User;

User::where('vip', true)
    ->cursor()
    ->throttle(seconds: 1)
    ->each(function (User $user) {
        // Call external API...
    });
```

---

### remember()

`remember()` metodu, önceden elde edilen öğeleri **önbellekte tutar** ve yeniden okuma sırasında tekrar sorgulamaz:

```php
// Henüz sorgu çalışmadı...
$users = User::cursor()->remember();

// Sorgu çalıştı ve ilk 5 kullanıcı alındı...
$users->take(5)->all();

// İlk 5 kullanıcı önbellekten, geri kalanı veritabanından gelir...
$users->take(20)->all();
```

---

### withHeartbeat()

`withHeartbeat()` metodu, koleksiyon işlenirken düzenli aralıklarla bir callback’in çağrılmasını sağlar.
Bu, uzun süren işlemler için (ör. kilidi yenilemek, ilerleme bildirmek) yararlıdır:

```php
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Cache;

$lock = Cache::lock('generate-reports', seconds: 60 * 5);

if ($lock->get()) {
    try {
        Report::where('status', 'pending')
            ->lazy()
            ->withHeartbeat(
                CarbonInterval::minutes(4),
                fn () => $lock->extend(CarbonInterval::minutes(5))
            )
            ->each(fn ($report) => $report->process());
    } finally {
        $lock->release();
    }
}
```

```
```

