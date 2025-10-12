# Blade Şablonları

## Giriş

Blade, Laravel ile birlikte gelen basit ama güçlü bir şablon motorudur. Bazı PHP şablon motorlarının aksine, Blade şablonlarınızda düz PHP kodu kullanmanızı engellemez. Aslında, tüm Blade şablonları düz PHP koduna derlenir ve değiştirilene kadar önbelleğe alınır; bu da Blade’in uygulamanıza neredeyse sıfır ek yük getirdiği anlamına gelir. Blade şablon dosyaları `.blade.php` dosya uzantısını kullanır ve genellikle `resources/views` dizininde saklanır.

Blade görünümleri, global `view` helper’ı kullanılarak yönlendirmelerden veya denetleyicilerden döndürülebilir. Elbette, görünümlerle ilgili belgelerde belirtildiği gibi, `view` helper’ının ikinci argümanı kullanılarak Blade görünümüne veri aktarılabilir:

```php
Route::get('/', function () {
    return view('greeting', ['name' => 'Finn']);
});
```

## Livewire ile Blade’i Güçlendirme

Blade şablonlarınızı bir üst seviyeye taşımak ve dinamik arayüzler oluşturmayı kolaylaştırmak mı istiyorsunuz? Laravel Livewire’a göz atın. Livewire, React veya Vue gibi frontend framework’leriyle mümkün olabilecek dinamik işlevselliklerle güçlendirilmiş Blade bileşenleri yazmanıza olanak tanır. Böylece birçok JavaScript framework’ünün getirdiği karmaşık client-side render ve build adımlarına gerek kalmadan modern, reaktif arayüzler oluşturmak için harika bir yol sunar.

## Verileri Görüntüleme

Blade görünümlerinize aktarılan verileri, değişkeni süslü parantezler içine alarak görüntüleyebilirsiniz. Örneğin, aşağıdaki yönlendirme verildiğinde:

```php
Route::get('/', function () {
    return view('welcome', ['name' => 'Samantha']);
});
```

`name` değişkeninin içeriğini şu şekilde görüntüleyebilirsiniz:

```
Hello, {{ $name }}.
```

Blade’in `{{ }}` echo ifadeleri, XSS saldırılarını önlemek için otomatik olarak PHP’nin `htmlspecialchars` fonksiyonundan geçirilir.

Yalnızca görünüme aktarılan değişkenlerin içeriğini görüntülemekle sınırlı değilsiniz. Herhangi bir PHP fonksiyonunun sonucunu da yazdırabilirsiniz. Aslında, bir Blade echo ifadesi içinde istediğiniz herhangi bir PHP kodunu yazabilirsiniz:

```
The current UNIX timestamp is {{ time() }}.
```

## HTML Entity Kodlama

Varsayılan olarak, Blade (ve Laravel’in `e` fonksiyonu) HTML entity’lerini iki kez kodlar. Çift kodlamayı devre dışı bırakmak isterseniz, `AppServiceProvider` sınıfınızın `boot` metodundan `Blade::withoutDoubleEncoding` metodunu çağırabilirsiniz:

```php
<?php
 
namespace App\Providers;
 
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
 
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::withoutDoubleEncoding();
    }
}
```

## Kaçışsız Veri Görüntüleme

Varsayılan olarak, Blade `{{ }}` ifadeleri XSS saldırılarını önlemek için PHP’nin `htmlspecialchars` fonksiyonundan geçirilir. Verinizin kaçışsız görüntülenmesini istiyorsanız aşağıdaki sözdizimini kullanabilirsiniz:

```
Hello, {!! $name !!}.
```

Uygulamanızın kullanıcıları tarafından sağlanan içeriği yazdırırken çok dikkatli olun. Kullanıcı kaynaklı verileri görüntülerken genellikle XSS saldırılarını önlemek için kaçışlı, çift süslü parantezli sözdizimini kullanmalısınız.

## Blade ve JavaScript Framework’leri

Birçok JavaScript framework’ü, tarayıcıda bir ifadenin görüntülenmesi gerektiğini belirtmek için “süslü” parantezler kullandığından, bir ifadeyi olduğu gibi bırakması gerektiğini Blade motoruna bildirmek için `@` sembolünü kullanabilirsiniz. Örneğin:

```html
<h1>Laravel</h1>
Hello, @{{ name }}.
```

Bu örnekte `@` sembolü Blade tarafından kaldırılacak, ancak `{{ name }}` ifadesi Blade motoru tarafından dokunulmadan kalacak ve JavaScript framework’ünüz tarafından render edilecektir.

`@` sembolü ayrıca Blade yönergelerini kaçırmak (escape) için de kullanılabilir:

```blade
{{-- Blade template --}}
@@if()
 
<!-- HTML output -->
@if()
```

## JSON Render Etme

Bazen bir dizi veriyi görünümünüze geçirip, bir JavaScript değişkenini başlatmak için JSON olarak render etmek isteyebilirsiniz. Örneğin:

```html
<script>
    var app = <?php echo json_encode($array); ?>;
</script>
```

Ancak `json_encode` fonksiyonunu manuel olarak çağırmak yerine, `Illuminate\Support\Js::from` metodunu kullanabilirsiniz. `from` metodu, PHP’nin `json_encode` fonksiyonuyla aynı argümanları kabul eder; ancak elde edilen JSON’un HTML içinde düzgün bir şekilde kaçışlandığından emin olur. Bu metot, verilen nesne veya diziyi geçerli bir JavaScript nesnesine dönüştürecek bir `JSON.parse` JavaScript ifadesi döndürür:

```html
<script>
    var app = {{ Illuminate\Support\Js::from($array) }};
</script>
```

Laravel uygulama iskeletinin en son sürümleri, bu işlevselliğe Blade şablonlarınızdan kolayca erişim sağlamak için bir `Js` facade içerir:

```html
<script>
    var app = {{ Js::from($array) }};
</script>
```

Yalnızca mevcut değişkenleri JSON olarak render etmek için `Js::from` metodunu kullanmalısınız. Blade şablonlama sistemi düzenli ifadeler (regular expressions) üzerine kuruludur ve karmaşık ifadeleri bu direktife geçirmeye çalışmak beklenmedik hatalara neden olabilir.

## @verbatim Direktifi

Şablonunuzun büyük bir kısmında JavaScript değişkenleri görüntülüyorsanız, her Blade echo ifadesinin başına `@` sembolü koymak zorunda kalmamak için HTML’i `@verbatim` direktifiyle sarmalayabilirsiniz:

```blade
@verbatim
    <div class="container">
        Hello, {{ name }}.
    </div>
@endverbatim
```
# Blade Direktifleri

Şablon miras alma ve veri görüntülemenin yanı sıra, Blade ayrıca koşullu ifadeler ve döngüler gibi yaygın PHP kontrol yapıları için kullanışlı kısayollar sağlar. Bu kısayollar, PHP kontrol yapılarıyla çalışmanın oldukça temiz, kısa bir yolunu sunar ve aynı zamanda PHP muadilleriyle tanıdık kalır.

## If İfadeleri

@if, @elseif, @else ve @endif direktiflerini kullanarak if ifadeleri oluşturabilirsiniz. Bu direktifler, PHP muadilleriyle aynı şekilde çalışır:

```blade
@if (count($records) === 1)
    I have one record!
@elseif (count($records) > 1)
    I have multiple records!
@else
    I don't have any records!
@endif
```

Kolaylık sağlamak için Blade ayrıca bir @unless direktifi sağlar:

```blade
@unless (Auth::check())
    You are not signed in.
@endunless
```

Ayrıca, @isset ve @empty direktifleri de kendi PHP fonksiyonlarının kısayolları olarak kullanılabilir:

```blade
@isset($records)
    // $records tanımlı ve null değil...
@endisset
 
@empty($records)
    // $records "boş"...
@endempty
```

## Kimlik Doğrulama Direktifleri

@auth ve @guest direktifleri, geçerli kullanıcının kimliği doğrulanmış mı yoksa misafir mi olduğunu hızlı bir şekilde belirlemek için kullanılabilir:

```blade
@auth
    // Kullanıcı kimliği doğrulandı...
@endauth
 
@guest
    // Kullanıcı kimliği doğrulanmadı...
@endguest
```

Gerekirse, kontrol edilecek kimlik doğrulama guard’ını belirtebilirsiniz:

```blade
@auth('admin')
    // Kullanıcı kimliği doğrulandı...
@endauth
 
@guest('admin')
    // Kullanıcı kimliği doğrulanmadı...
@endguest
```

## Ortam Direktifleri

Uygulamanın production ortamında çalışıp çalışmadığını @production direktifiyle kontrol edebilirsiniz:

```blade
@production
    // Production’a özel içerik...
@endproduction
```

Ya da uygulamanın belirli bir ortamda çalışıp çalışmadığını @env direktifiyle belirleyebilirsiniz:

```blade
@env('staging')
    // Uygulama "staging" ortamında çalışıyor...
@endenv
 
@env(['staging', 'production'])
    // Uygulama "staging" veya "production" ortamında çalışıyor...
@endenv
```

## Bölüm (Section) Direktifleri

Bir şablon miras alma bölümünün içeriğe sahip olup olmadığını @hasSection direktifiyle kontrol edebilirsiniz:

```blade
@hasSection('navigation')
    <div class="pull-right">
        @yield('navigation')
    </div>
 
    <div class="clearfix"></div>
@endif
```

Bir bölümün içeriğe sahip olmadığını belirlemek için sectionMissing direktifini kullanabilirsiniz:

```blade
@sectionMissing('navigation')
    <div class="pull-right">
        @include('default-navigation')
    </div>
@endif
```

## Oturum (Session) Direktifleri

@session direktifi, bir oturum değerinin mevcut olup olmadığını belirlemek için kullanılabilir. Eğer oturum değeri mevcutsa, @session ve @endsession direktifleri arasındaki içerik değerlendirilir. İçeride, $value değişkenini yazarak oturum değerini görüntüleyebilirsiniz:

```blade
@session('status')
    <div class="p-4 bg-green-100">
        {{ $value }}
    </div>
@endsession
```

## Context Direktifleri

@context direktifi, bir context değerinin mevcut olup olmadığını belirlemek için kullanılabilir. Eğer mevcutsa, @context ve @endcontext direktifleri arasındaki içerik değerlendirilir ve $value değişkeniyle context değeri görüntülenebilir:

```blade
@context('canonical')
    <link href="{{ $value }}" rel="canonical">
@endcontext
```

## Switch İfadeleri

Switch ifadeleri, @switch, @case, @break, @default ve @endswitch direktifleriyle oluşturulabilir:

```blade
@switch($i)
    @case(1)
        First case...
        @break
 
    @case(2)
        Second case...
        @break
 
    @default
        Default case...
@endswitch
```

## Döngüler

Blade, PHP’nin döngü yapılarıyla çalışmak için basit direktifler sağlar. Her biri PHP muadilleriyle aynı şekilde çalışır:

```blade
@for ($i = 0; $i < 10; $i++)
    The current value is {{ $i }}
@endfor
 
@foreach ($users as $user)
    <p>This is user {{ $user->id }}</p>
@endforeach
 
@forelse ($users as $user)
    <li>{{ $user->name }}</li>
@empty
    <p>No users</p>
@endforelse
 
@while (true)
    <p>I'm looping forever.</p>
@endwhile
```

foreach döngüsünde iterasyon yaparken, döngü hakkında bilgi almak için $loop değişkenini kullanabilirsiniz — örneğin, döngünün ilk veya son iterasyonunda olup olmadığını belirlemek için.

Döngülerde ayrıca @continue ve @break direktifleriyle mevcut iterasyonu atlayabilir veya döngüyü sonlandırabilirsiniz:

```blade
@foreach ($users as $user)
    @if ($user->type == 1)
        @continue
    @endif
 
    <li>{{ $user->name }}</li>
 
    @if ($user->number == 5)
        @break
    @endif
@endforeach
```

Ayrıca koşulu direktifin içinde de belirtebilirsiniz:

```blade
@foreach ($users as $user)
    @continue($user->type == 1)
 
    <li>{{ $user->name }}</li>
 
    @break($user->number == 5)
@endforeach
```

## Döngü Değişkeni ($loop)

foreach döngüsünde iterasyon yaparken, $loop değişkeni kullanıma sunulur. Bu değişken, mevcut döngü indeksi, ilk veya son iterasyon olup olmadığı gibi yararlı bilgilere erişim sağlar:

```blade
@foreach ($users as $user)
    @if ($loop->first)
        This is the first iteration.
    @endif
 
    @if ($loop->last)
        This is the last iteration.
    @endif
 
    <p>This is user {{ $user->id }}</p>
@endforeach
```

İç içe döngülerde, parent özelliğiyle üst döngünün $loop değişkenine erişebilirsiniz:

```blade
@foreach ($users as $user)
    @foreach ($user->posts as $post)
        @if ($loop->parent->first)
            This is the first iteration of the parent loop.
        @endif
    @endforeach
@endforeach
```

### $loop Değişkeni Özellikleri

| Özellik          | Açıklama                                           |
| ---------------- | -------------------------------------------------- |
| $loop->index     | Mevcut döngü iterasyonunun indeksi (0’dan başlar). |
| $loop->iteration | Mevcut döngü iterasyonu (1’den başlar).            |
| $loop->remaining | Döngüde kalan iterasyon sayısı.                    |
| $loop->count     | Döngü yapılan dizideki toplam öğe sayısı.          |
| $loop->first     | İlk iterasyon olup olmadığını belirtir.            |
| $loop->last      | Son iterasyon olup olmadığını belirtir.            |
| $loop->even      | Çift iterasyon olup olmadığını belirtir.           |
| $loop->odd       | Tek iterasyon olup olmadığını belirtir.            |
| $loop->depth     | Mevcut döngünün iç içelik seviyesi.                |
| $loop->parent    | İç içe döngülerde üst döngünün $loop değişkeni.    |

## Koşullu Sınıflar ve Stiller

@class direktifi, koşullu olarak CSS sınıfı oluşturmayı sağlar. Dizi anahtarı sınıf adını, değer ise boolean koşulu belirtir:

```blade
@php
    $isActive = false;
    $hasError = true;
@endphp
 
<span @class([
    'p-4',
    'font-bold' => $isActive,
    'text-gray-500' => ! $isActive,
    'bg-red' => $hasError,
])></span>
```

Benzer şekilde, @style direktifi HTML elementlerine koşullu inline CSS stilleri eklemek için kullanılabilir:

```blade
@php
    $isActive = true;
@endphp
 
<span @style([
    'background-color: red',
    'font-weight: bold' => $isActive,
])></span>
```

## Ek HTML Özellikleri

Belirli HTML öğelerini kolayca işaretlemek için çeşitli Blade direktifleri vardır:

```blade
@checked(old('active', $user->active))
@selected(old('version') == $version)
@disabled($errors->isNotEmpty())
@readonly($user->isNotAdmin())
@required($user->isAdmin())
```

## Alt Görünümleri Dahil Etme

@include direktifi, bir Blade görünümünü başka bir görünüm içine eklemenizi sağlar:

```blade
<div>
    @include('shared.errors')
    <form>
        <!-- Form Contents -->
    </form>
</div>
```

Ek veri geçirmek isterseniz:

```blade
@include('view.name', ['status' => 'complete'])
```

Eğer dahil edilecek görünüm mevcut değilse, @includeIf direktifini kullanabilirsiniz. Ayrıca koşula bağlı dahil etme için:

```blade
@includeWhen($boolean, 'view.name', ['status' => 'complete'])
@includeUnless($boolean, 'view.name', ['status' => 'complete'])
@includeFirst(['custom.admin', 'admin'], ['status' => 'complete'])
```

## Koleksiyonlar İçin Görünüm Render Etme

Döngü ve include işlemlerini tek satırda birleştirmek için @each direktifini kullanabilirsiniz:

```blade
@each('view.name', $jobs, 'job', 'view.empty')
```

@each ile render edilen görünümler, parent görünümden değişkenleri devralmaz.

## @once Direktifi

@once direktifi, şablonun belirli bir bölümünün her render döngüsünde yalnızca bir kez değerlendirilmesini sağlar:

```blade
@once
    @push('scripts')
        <script>
            // Your custom JavaScript...
        </script>
    @endpush
@endonce
```

Sıklıkla kullanılan @pushOnce ve @prependOnce varyantları da mevcuttur.

## Ham PHP

Bazı durumlarda şablon içinde doğrudan PHP kodu kullanmak isteyebilirsiniz:

```blade
@php
    $counter = 1;
@endphp
```

Ya da yalnızca bir sınıfı içe aktarmak istiyorsanız:

```blade
@use('App\Models\Flight')
@use('App\Models\Flight', 'FlightModel')
@use('App\Models\{Flight, Airport}')
@use(function App\Helpers\format_currency)
@use(const App\Constants\MAX_ATTEMPTS)
@use(function App\Helpers\{format_currency, format_date})
@use(const App\Constants\{MAX_ATTEMPTS, DEFAULT_TIMEOUT})
```

## Yorumlar

Blade ayrıca görünümlerinizde yorum tanımlamanıza da izin verir. Ancak, HTML yorumlarının aksine, Blade yorumları uygulamanız tarafından döndürülen HTML’ye dahil edilmez:

```blade
{{-- Bu yorum render edilen HTML’de yer almayacak --}}
```

# Bileşenler (Components)

Bileşenler ve slot’lar, section’lar, layout’lar ve include’lar ile benzer avantajlar sağlar; ancak bazıları bileşenlerin ve slot’ların zihinsel modelini anlamayı daha kolay bulabilir. Bileşenleri yazmanın iki yolu vardır: **sınıf tabanlı bileşenler** ve **anonim bileşenler**.

## Sınıf Tabanlı Bileşen Oluşturma

Sınıf tabanlı bir bileşen oluşturmak için `make:component` Artisan komutunu kullanabilirsiniz. Örneğin, basit bir **Alert** bileşeni oluşturmak için:

```bash
php artisan make:component Alert
```

Bu komut, bileşeni `app/View/Components` dizinine yerleştirir ve bileşen için bir görünüm şablonu oluşturur. Görünüm şablonu `resources/views/components` dizinine yerleştirilir.
Kendi uygulamanız için bileşen yazarken, bu dizinlerdeki bileşenler otomatik olarak keşfedilir; yani ekstra bir kayıt işlemi gerekmez.

Alt dizinlerde bileşen oluşturmak isterseniz:

```bash
php artisan make:component Forms/Input
```

Bu komut, sınıfı `app/View/Components/Forms` dizinine, görünümü ise `resources/views/components/forms` dizinine yerleştirir.

Sadece Blade şablonuna sahip **anonim bir bileşen** oluşturmak için `--view` bayrağını kullanabilirsiniz:

```bash
php artisan make:component forms.input --view
```

Bu komut, `resources/views/components/forms/input.blade.php` dosyasını oluşturur ve bileşeni şu şekilde kullanabilirsiniz:

```blade
<x-forms.input />
```

---

## Paket Bileşenlerini Manuel Olarak Kaydetme

Kendi uygulamanız için bileşenler otomatik olarak keşfedilir; ancak bir **paket** geliştiriyorsanız, bileşen sınıfını ve HTML etiket takma adını (alias) manuel olarak kaydetmeniz gerekir. Bu işlemi genellikle paketinizin service provider’ının `boot` metodunda yaparsınız:

```php
use Illuminate\Support\Facades\Blade;
 
public function boot(): void
{
    Blade::component('package-alert', Alert::class);
}
```

Kayıttan sonra bileşen şu şekilde kullanılabilir:

```blade
<x-package-alert />
```

Alternatif olarak, `componentNamespace` metodu ile bileşen sınıflarını otomatik yükleyebilirsiniz:

```php
use Illuminate\Support\Facades\Blade;

public function boot(): void
{
    Blade::componentNamespace('Nightshade\\Views\\Components', 'nightshade');
}
```

Bu, vendor namespace kullanarak bileşenleri şu şekilde çağırmanıza izin verir:

```blade
<x-nightshade::calendar />
<x-nightshade::color-picker />
```

---

## Bileşenleri Render Etme

Bir bileşeni görüntülemek için, Blade şablonunuzda `x-` ile başlayan bir etiket kullanın:

```blade
<x-alert />
<x-user-profile />
```

Eğer bileşen sınıfı alt bir dizinde bulunuyorsa, dizinleri belirtmek için `.` karakterini kullanabilirsiniz:

```blade
<x-inputs.button />
```

Bir bileşeni **koşullu olarak render etmek** isterseniz, bileşen sınıfınızda `shouldRender` metodunu tanımlayabilirsiniz:

```php
use Illuminate\Support\Str;
 
public function shouldRender(): bool
{
    return Str::length($this->message) > 0;
}
```

---

## Index Bileşenleri

Bazen bileşenleri bir grup olarak tek bir dizin altında toplamak isteyebilirsiniz:

```
App\Views\Components\Card\Card
App\Views\Components\Card\Header
App\Views\Components\Card\Body
```

Laravel, bileşenin dosya adı ile dizin adının aynı olması durumunda, bileşeni `<x-card.card>` yerine `<x-card>` olarak render etmenize izin verir:

```blade
<x-card>
    <x-card.header>...</x-card.header>
    <x-card.body>...</x-card.body>
</x-card>
```

---

## Bileşenlere Veri Aktarma

Bileşenlere veri aktarmak için HTML özniteliklerini (attributes) kullanabilirsiniz:

```blade
<x-alert type="error" :message="$message" />
```

Tüm veri özniteliklerini bileşenin **constructor** metodunda tanımlamalısınız. Public property’ler otomatik olarak bileşen görünümünde kullanılabilir hale gelir:

```php
namespace App\View\Components;
 
use Illuminate\View\Component;
use Illuminate\View\View;
 
class Alert extends Component
{
    public function __construct(
        public string $type,
        public string $message,
    ) {}

    public function render(): View
    {
        return view('components.alert');
    }
}
```

Görünümde bu property’leri şu şekilde kullanabilirsiniz:

```blade
<div class="alert alert-{{ $type }}">
    {{ $message }}
</div>
```

---

## İsimlendirme Kuralları

Constructor argümanları **camelCase**, HTML öznitelikleri ise **kebab-case** olarak yazılmalıdır:

```php
public function __construct(public string $alertType) {}
```

Kullanımı:

```blade
<x-alert alert-type="danger" />
```

---

## Kısa Öznitelik Sözdizimi

Değişken adları öznitelik adlarıyla eşleşiyorsa kısa yazım kullanılabilir:

```blade
<x-profile :$userId :$name />
```

Bu, aşağıdakiyle aynıdır:

```blade
<x-profile :user-id="$userId" :name="$name" />
```

---

## Öznitelik Render’ını Kaçışlama

Alpine.js gibi bazı framework’ler `:` ön ekini kullandığından, Blade’e bunun bir PHP ifadesi olmadığını belirtmek için `::` öneki kullanabilirsiniz:

```blade
<x-button ::class="{ danger: isDeleting }">Submit</x-button>
```

Render edilen HTML:

```html
<button :class="{ danger: isDeleting }">Submit</button>
```

---

## Bileşen Metodları

Bileşen içinde tanımlı public metodlar, görünümden çağrılabilir:

```php
public function isSelected(string $option): bool
{
    return $option === $this->selected;
}
```

Kullanımı:

```blade
<option {{ $isSelected($value) ? 'selected' : '' }} value="{{ $value }}">
    {{ $label }}
</option>
```

---

## Bileşen Sınıfında Attribute ve Slot Erişimi

Bir bileşenin `render` metodundan bir **closure** döndürerek, bileşenin adı, attribute’ları ve slot’una erişebilirsiniz:

```php
use Closure;
 
public function render(): Closure
{
    return function () {
        return '<div {{ $attributes }}>Components content</div>';
    };
}
```

Closure ayrıca `$data` dizisini alabilir:

```php
return function (array $data) {
    // $data['componentName']
    // $data['attributes']
    // $data['slot']
 
    return '<div {{ $attributes }}>Components content</div>';
};
```

> ⚠️ `$data` öğeleri doğrudan Blade string’i içine gömülmemelidir. Bu, uzaktan kod çalıştırma açıklarına neden olabilir.

---

## Ek Bağımlılıklar

Bir bileşen Laravel servis container’ından bağımlılık gerektiriyorsa, bunları veri attribute’lerinden önce constructor’da tanımlayabilirsiniz:

```php
use App\Services\AlertCreator;
 
public function __construct(
    public AlertCreator $creator,
    public string $type,
    public string $message,
) {}
```

---

## Attribute ve Metodları Gizleme

Bazı public property veya metodların bileşen görünümüne aktarılmasını istemiyorsanız, bunları `$except` dizisine ekleyebilirsiniz:

```php
namespace App\View\Components;
 
use Illuminate\View\Component;
 
class Alert extends Component
{
    protected $except = ['type'];

    public function __construct(public string $type) {}
}
```
# Bileşen Öznitelikleri (Component Attributes)

Daha önce, bir bileşene veri özniteliklerinin nasıl aktarılacağını inceledik; ancak bazen, bir bileşenin çalışması için gerekli olmayan ek HTML özniteliklerini (örneğin `class`) belirtmeniz gerekebilir. Genellikle bu ek özniteliklerin, bileşenin şablonundaki kök (root) HTML öğesine aktarılmasını istersiniz.

Örneğin şu şekilde bir bileşen kullandığımızı düşünelim:

```blade
<x-alert type="error" :message="$message" class="mt-4"/>
```

Bileşenin constructor’ında tanımlanmayan tüm öznitelikler otomatik olarak bileşenin **"attribute bag"**’ine eklenir. Bu torba (`attribute bag`), bileşen içinde `$attributes` değişkeni aracılığıyla erişilebilir. Tüm öznitelikleri bileşen içinde aşağıdaki gibi görüntüleyebilirsiniz:

```blade
<div {{ $attributes }}>
    <!-- Component content -->
</div>
```

> ⚠️ Şu anda, bileşen etiketleri içinde `@env` gibi direktiflerin kullanımı desteklenmemektedir.
> Örneğin: `<x-alert :live="@env('production')"/>` derlenmeyecektir.

---

## Varsayılan / Birleştirilmiş Öznitelikler

Bazen bir bileşen için varsayılan öznitelik değerleri tanımlamak veya bazı özniteliklere ek değerler birleştirmek isteyebilirsiniz. Bunu yapmak için `merge` metodunu kullanabilirsiniz.
Bu yöntem, özellikle bileşene her zaman uygulanması gereken varsayılan CSS sınıflarını tanımlamak için faydalıdır:

```blade
<div {{ $attributes->merge(['class' => 'alert alert-'.$type]) }}>
    {{ $message }}
</div>
```

Bu bileşen şu şekilde kullanıldığında:

```blade
<x-alert type="error" :message="$message" class="mb-4"/>
```

Ortaya çıkan HTML:

```html
<div class="alert alert-error mb-4">
    <!-- $message içeriği -->
</div>
```

---

## Koşullu Sınıfları Birleştirme

Belirli bir koşul doğruysa sınıfları birleştirmek isteyebilirsiniz. Bunu `class` metodu ile yapabilirsiniz. Bu metot, anahtarların sınıf adlarını, değerlerin ise boolean ifadeleri temsil ettiği bir dizi kabul eder.

```blade
<div {{ $attributes->class(['p-4', 'bg-red' => $hasError]) }}>
    {{ $message }}
</div>
```

Diğer öznitelikleri de birleştirmeniz gerekiyorsa, `class` metoduna `merge` metodunu zincirleyebilirsiniz:

```blade
<button {{ $attributes->class(['p-4'])->merge(['type' => 'button']) }}>
    {{ $slot }}
</button>
```

Eğer birleşik öznitelik almayacak bir HTML öğesi üzerinde koşullu sınıf derlemesi yapmak istiyorsanız, `@class` direktifini kullanabilirsiniz.

---

## Sınıf Dışı Öznitelikleri Birleştirme

Sınıf (`class`) dışındaki öznitelikler birleştirildiğinde, `merge` metoduna verilen değerler özniteliklerin "varsayılan" değerleri olarak kabul edilir. Ancak, sınıf özniteliğinden farklı olarak, bu değerler eklenen değerlerle birleştirilmez, üzerine yazılır.

Örneğin, bir buton bileşeni şu şekilde uygulanabilir:

```blade
<button {{ $attributes->merge(['type' => 'button']) }}>
    {{ $slot }}
</button>
```

Bu bileşeni şu şekilde kullandığınızda:

```blade
<x-button type="submit">
    Submit
</x-button>
```

Oluşan HTML:

```html
<button type="submit">
    Submit
</button>
```

Sınıf dışındaki bir özniteliğin varsayılan değeriyle gelen değerlerin **birleştirilmesini** istiyorsanız, `prepends` metodunu kullanabilirsiniz.
Aşağıdaki örnekte, `data-controller` özniteliği her zaman `profile-controller` ile başlar ve eklenen diğer değerler bunun sonuna eklenir:

```blade
<div {{ $attributes->merge(['data-controller' => $attributes->prepends('profile-controller')]) }}>
    {{ $slot }}
</div>
```

---

## Öznitelikleri Alma ve Filtreleme

Öznitelikleri filtrelemek için `filter` metodunu kullanabilirsiniz. Bu metot, özniteliğin korunup korunmayacağına karar vermek için `true` döndüren bir closure kabul eder:

```blade
{{ $attributes->filter(fn (string $value, string $key) => $key == 'foo') }}
```

Belirli bir dizeyle başlayan tüm öznitelikleri almak için `whereStartsWith` metodunu kullanabilirsiniz:

```blade
{{ $attributes->whereStartsWith('wire:model') }}
```

Tersine, bu dizeyle başlayan öznitelikleri hariç tutmak için `whereDoesntStartWith` metodunu kullanabilirsiniz:

```blade
{{ $attributes->whereDoesntStartWith('wire:model') }}
```

Bir öznitelik torbasındaki **ilk** özniteliği almak için `first` metodunu kullanabilirsiniz:

```blade
{{ $attributes->whereStartsWith('wire:model')->first() }}
```

Bir özniteliğin mevcut olup olmadığını kontrol etmek için `has` metodunu kullanabilirsiniz:

```blade
@if ($attributes->has('class'))
    <div>Class attribute is present</div>
@endif
```

Birden fazla özniteliğin mevcut olup olmadığını kontrol etmek için bir dizi geçirebilirsiniz:

```blade
@if ($attributes->has(['name', 'class']))
    <div>All of the attributes are present</div>
@endif
```

Belirtilen özniteliklerden herhangi birinin mevcut olup olmadığını kontrol etmek için `hasAny` metodunu kullanabilirsiniz:

```blade
@if ($attributes->hasAny(['href', ':href', 'v-bind:href']))
    <div>One of the attributes is present</div>
@endif
```

Belirli bir özniteliğin değerini almak için `get` metodunu kullanabilirsiniz:

```blade
{{ $attributes->get('class') }}
```

Belirli anahtarlara sahip öznitelikleri almak için `only` metodunu kullanabilirsiniz:

```blade
{{ $attributes->only(['class']) }}
```

Belirli anahtarlara sahip öznitelikleri hariç tutmak için `except` metodunu kullanabilirsiniz:

```blade
{{ $attributes->except(['class']) }}
```

---

## Ayrılmış (Reserved) Anahtar Kelimeler

Bazı anahtar kelimeler Blade’in bileşenleri derleyebilmesi için dahili olarak ayrılmıştır. Bu anahtar kelimeler, bileşenlerinizde public property veya metot adı olarak **kullanılamaz**:

* `data`
* `render`
* `resolveView`
* `shouldRender`
* `view`
* `withAttributes`
* `withName`

# Slotlar (Slots)

Bileşenlerinize ek içerikler aktarmanız gerektiğinde **slot** kullanırsınız. Slot’lar, bileşen içinde `$slot` değişkeniyle echo edilerek görüntülenir.

Örneğin, aşağıdaki gibi bir `alert` bileşenimiz olduğunu varsayalım:

```blade
<!-- /resources/views/components/alert.blade.php -->
<div class="alert alert-danger">
    {{ $slot }}
</div>
```

Bu bileşene içerik eklemek için:

```blade
<x-alert>
    <strong>Whoops!</strong> Something went wrong!
</x-alert>
```

---

## Adlandırılmış Slotlar (Named Slots)

Bazen bir bileşen içinde birden fazla farklı slot içeriğini farklı yerlerde göstermeniz gerekir.
Örneğin, `title` adında bir slot ekleyelim:

```blade
<!-- /resources/views/components/alert.blade.php -->
<span class="alert-title">{{ $title }}</span>

<div class="alert alert-danger">
    {{ $slot }}
</div>
```

Slot’un içeriğini `x-slot` etiketiyle tanımlayabilirsiniz. `x-slot` dışında kalan tüm içerikler `$slot` değişkeniyle bileşene aktarılır:

```blade
<x-alert>
    <x-slot:title>
        Server Error
    </x-slot>
 
    <strong>Whoops!</strong> Something went wrong!
</x-alert>
```

---

## Slot İçeriği Kontrol Etme

Bir slot’un içeriği olup olmadığını `isEmpty()` metodu ile kontrol edebilirsiniz:

```blade
<span class="alert-title">{{ $title }}</span>

<div class="alert alert-danger">
    @if ($slot->isEmpty())
        This is default content if the slot is empty.
    @else
        {{ $slot }}
    @endif
</div>
```

Slot’un yalnızca HTML yorumları değil, gerçek içerik barındırıp barındırmadığını anlamak için `hasActualContent()` metodunu kullanabilirsiniz:

```blade
@if ($slot->hasActualContent())
    The scope has non-comment content.
@endif
```

---

## Scoped Slotlar

Vue gibi framework’leri kullandıysanız, bileşen içinde veri veya metotlara erişim sağlayan **scoped slot** kavramına aşinasınızdır.
Laravel’de benzer bir davranış elde etmek için bileşen sınıfınızda public metod veya property tanımlayıp, slot içinde `$component` değişkeni aracılığıyla erişebilirsiniz.

Örneğin, `x-alert` bileşeninin `formatAlert` adlı bir public metodu olsun:

```blade
<x-alert>
    <x-slot:title>
        {{ $component->formatAlert('Server Error') }}
    </x-slot>
 
    <strong>Whoops!</strong> Something went wrong!
</x-alert>
```

---

## Slot Öznitelikleri

Blade bileşenlerinde olduğu gibi, slot’lara da `class` gibi ek öznitelikler verebilirsiniz:

```blade
<x-card class="shadow-sm">
    <x-slot:heading class="font-bold">
        Heading
    </x-slot>
 
    Content
 
    <x-slot:footer class="text-sm">
        Footer
    </x-slot>
</x-card>
```

Slot öznitelikleriyle etkileşime geçmek için slot değişkeninin `attributes` özelliğine erişebilirsiniz.
Örneğin:

```blade
@props([
    'heading',
    'footer',
])
 
<div {{ $attributes->class(['border']) }}>
    <h1 {{ $heading->attributes->class(['text-lg']) }}>
        {{ $heading }}
    </h1>
 
    {{ $slot }}
 
    <footer {{ $footer->attributes->class(['text-gray-700']) }}>
        {{ $footer }}
    </footer>
</div>
```

---

## Inline Bileşen Görünümleri

Küçük bileşenler için hem sınıf hem de görünüm dosyası yönetmek zahmetli olabilir. Bu nedenle, bileşenin `render` metodundan doğrudan bileşen işaretlemesini döndürebilirsiniz:

```php
public function render(): string
{
    return <<<'blade'
        <div class="alert alert-danger">
            {{ $slot }}
        </div>
    blade;
}
```

Inline bileşen oluşturmak için Artisan komutunu `--inline` seçeneğiyle kullanabilirsiniz:

```bash
php artisan make:component Alert --inline
```

---

## Dinamik Bileşenler

Bazen hangi bileşenin render edileceğini çalışma zamanına kadar bilemezsiniz.
Bu durumda Laravel’in yerleşik `dynamic-component` bileşenini kullanabilirsiniz:

```blade
{{-- $componentName = "secondary-button"; --}}
<x-dynamic-component :component="$componentName" class="mt-4" />
```

---

## Bileşenleri Manuel Kaydetme

Bu bölüm genellikle Laravel paketleri geliştirenler içindir.
Kendi uygulamanız için bileşenler otomatik olarak keşfedilir; ancak bir **paket** geliştiriyorsanız veya bileşenlerinizi farklı dizinlerde tutuyorsanız, bunları manuel olarak kaydetmelisiniz.

```php
use Illuminate\Support\Facades\Blade;
use VendorPackage\View\Components\AlertComponent;
 
public function boot(): void
{
    Blade::component('package-alert', AlertComponent::class);
}
```

Kayıttan sonra bileşen şu şekilde kullanılabilir:

```blade
<x-package-alert />
```

---

## Paket Bileşenlerini Otomatik Yükleme

`componentNamespace` metodu ile bileşen sınıflarını otomatik olarak yükleyebilirsiniz.
Örneğin, `Nightshade` adında bir paketin `Calendar` ve `ColorPicker` bileşenleri `Package\Views\Components` namespace’i altında bulunsun:

```php
use Illuminate\Support\Facades\Blade;
 
public function boot(): void
{
    Blade::componentNamespace('Nightshade\\Views\\Components', 'nightshade');
}
```

Bu sayede bileşenleri vendor namespace kullanarak çağırabilirsiniz:

```blade
<x-nightshade::calendar />
<x-nightshade::color-picker />
```

Laravel, bileşen adını **PascalCase** biçimine çevirerek ilgili sınıfı otomatik olarak algılar.
Alt dizinler de `"dot"` notasyonu kullanılarak desteklenir.

# Anonim Bileşenler (Anonymous Components)

**Anonim bileşenler**, inline bileşenlere benzer şekilde tek bir dosya üzerinden yönetilen bileşenlerdir. Ancak anonim bileşenlerin **sınıfı yoktur**, sadece bir Blade şablonundan oluşurlar.

Bir anonim bileşen tanımlamak için, `resources/views/components` dizinine bir Blade dosyası yerleştirmeniz yeterlidir.
Örneğin aşağıdaki dosya tanımlıysa:

```
resources/views/components/alert.blade.php
```

bileşeni şu şekilde render edebilirsiniz:

```blade
<x-alert />
```

Bileşen bir alt dizinde tanımlanmışsa, alt dizini belirtmek için `.` karakterini kullanabilirsiniz:

```blade
<x-inputs.button />
```

---

## Anonim Index Bileşenleri

Bazen bir bileşen birçok Blade şablonundan oluşur ve bunları tek bir dizin altında gruplamak isteyebilirsiniz.
Örneğin bir **accordion** bileşeni için şu yapı olsun:

```
/resources/views/components/accordion.blade.php
/resources/views/components/accordion/item.blade.php
```

Bu yapıyla bileşeni şu şekilde render edebilirsiniz:

```blade
<x-accordion>
    <x-accordion.item>
        ...
    </x-accordion.item>
</x-accordion>
```

Ancak bu örnekte `accordion.blade.php` dosyasını `components` dizinine koymak zorunda kaldık.
Laravel bunu kolaylaştırır: bileşenin diziniyle aynı adı taşıyan bir dosya, o dizinin içinde “kök (root)” bileşen olarak tanınır.

Yani şu yapı da geçerlidir:

```
/resources/views/components/accordion/accordion.blade.php
/resources/views/components/accordion/item.blade.php
```

Ve bileşen yine şu şekilde kullanılabilir:

```blade
<x-accordion>
    <x-accordion.item>...</x-accordion.item>
</x-accordion>
```

---

## Veri Özellikleri / Öznitelikler

Anonim bileşenlerin bir sınıfı olmadığı için, hangi verilerin değişken olarak, hangilerinin `attribute bag` içinde olacağını belirtmek için `@props` direktifi kullanılır.
Varsayılan değer atamak için anahtar-değer biçimi kullanılabilir:

```blade
<!-- /resources/views/components/alert.blade.php -->

@props(['type' => 'info', 'message'])

<div {{ $attributes->merge(['class' => 'alert alert-'.$type]) }}>
    {{ $message }}
</div>
```

Kullanımı:

```blade
<x-alert type="error" :message="$message" class="mb-4"/>
```

---

## Üst (Parent) Veriye Erişim

Bir alt bileşenin, üst bileşendeki verilere erişmesi gerekebilir. Bu durumda `@aware` direktifi kullanılır.
Örneğin bir `menu` bileşenimiz olduğunu düşünelim:

```blade
<x-menu color="purple">
    <x-menu.item>...</x-menu.item>
    <x-menu.item>...</x-menu.item>
</x-menu>
```

`menu` bileşeni şu şekilde tanımlanabilir:

```blade
<!-- /resources/views/components/menu/index.blade.php -->
@props(['color' => 'gray'])

<ul {{ $attributes->merge(['class' => 'bg-'.$color.'-200']) }}>
    {{ $slot }}
</ul>
```

Ve `menu.item` bileşeni, `@aware` direktifiyle üst bileşenin `color` özelliğine erişebilir:

```blade
<!-- /resources/views/components/menu/item.blade.php -->
@aware(['color' => 'gray'])

<li {{ $attributes->merge(['class' => 'text-'.$color.'-800']) }}>
    {{ $slot }}
</li>
```

> 🔹 Not: `@aware`, yalnızca **üst bileşene HTML özniteliği olarak aktarılan veriye** erişebilir.
> `@props` ile varsayılan olarak tanımlanan ancak aktarılmayan değerler erişilemez.

---

## Anonim Bileşen Yolları

Varsayılan olarak, anonim bileşenler `resources/views/components` dizininde tanımlanır.
Ancak Laravel’e başka dizinlerdeki anonim bileşenleri de tanıtabilirsiniz.

`Blade::anonymousComponentPath` metodu, bileşenlerin bulunduğu dizini ve isteğe bağlı bir isim alanı (namespace) tanımlar.
Bu metot genellikle bir Service Provider’ın `boot()` metodunda çağrılır:

```php
public function boot(): void
{
    Blade::anonymousComponentPath(__DIR__.'/../components');
}
```

Bu şekilde kaydedilen bileşenler, doğrudan şu şekilde çağrılabilir:

```blade
<x-panel />
```

Bir isim alanı (prefix) eklemek için ikinci argümanı kullanabilirsiniz:

```php
Blade::anonymousComponentPath(__DIR__.'/../components', 'dashboard');
```

Böylece bileşenler şu şekilde çağrılır:

```blade
<x-dashboard::panel />
```

---

## Bileşenlerle Layout (Düzen) Oluşturma

Çoğu web uygulamasında sayfalar genel bir layout paylaşır.
Bu layout’u her sayfada tekrar yazmak yerine, tek bir Blade bileşeni olarak tanımlayıp her yerde kullanabilirsiniz.

### Layout Bileşenini Tanımlama

```blade
<!-- resources/views/components/layout.blade.php -->
<html>
    <head>
        <title>{{ $title ?? 'Todo Manager' }}</title>
    </head>
    <body>
        <h1>Todos</h1>
        <hr/>
        {{ $slot }}
    </body>
</html>
```

### Layout Bileşenini Kullanma

```blade
<!-- resources/views/tasks.blade.php -->
<x-layout>
    @foreach ($tasks as $task)
        <div>{{ $task }}</div>
    @endforeach
</x-layout>
```

`$slot`, bileşene enjekte edilen içeriği temsil eder.
Ayrıca layout, bir `$title` slot’u da kabul eder.

```blade
<!-- resources/views/tasks.blade.php -->
<x-layout>
    <x-slot:title>
        Custom Title
    </x-slot>

    @foreach ($tasks as $task)
        <div>{{ $task }}</div>
    @endforeach
</x-layout>
```

### Route Üzerinden Görünüm Döndürme

```php
use App\Models\Task;

Route::get('/tasks', function () {
    return view('tasks', ['tasks' => Task::all()]);
});
```
# Şablon Mirası (Template Inheritance) Kullanarak Layout’lar

## Layout Tanımlama

Layout’lar “template inheritance” (şablon mirası) kullanılarak da oluşturulabilir. Bu yöntem, bileşenlerin (components) tanıtılmasından önce Laravel uygulamalarının temel yapı taşıydı.

Basit bir örnek üzerinden gidelim.
Genellikle çoğu web uygulaması sayfalar arasında aynı genel yerleşimi (layout) korur. Bu yerleşimi tek bir Blade görünümü olarak tanımlamak en uygun yoldur:

```blade
<!-- resources/views/layouts/app.blade.php -->
<html>
    <head>
        <title>App Name - @yield('title')</title>
    </head>
    <body>
        @section('sidebar')
            This is the master sidebar.
        @show
 
        <div class="container">
            @yield('content')
        </div>
    </body>
</html>
```

Bu dosya, normal HTML işaretlemesi (markup) içerir.
Burada dikkat edilmesi gereken iki Blade direktifi vardır:

* `@section` → bir içerik bölümü tanımlar.
* `@yield` → belirli bir bölümün içeriğini görüntüler.

Artık uygulamamız için bir layout tanımladık. Şimdi bu layout’u miras alan bir alt (child) sayfa tanımlayalım.

---

## Layout’u Genişletme (Extending a Layout)

Bir alt görünüm (child view) tanımlarken, hangi layout’un miras alınacağını belirtmek için `@extends` direktifini kullanırız.
Alt görünümler, layout’un bölümlerine içerik eklemek için `@section` direktifini kullanır.

```blade
<!-- resources/views/child.blade.php -->

@extends('layouts.app')
 
@section('title', 'Page Title')
 
@section('sidebar')
    @@parent
 
    <p>This is appended to the master sidebar.</p>
@endsection
 
@section('content')
    <p>This is my body content.</p>
@endsection
```

Burada `@@parent`, layout’un sidebar içeriğini **koruyup** üzerine ekleme yapmamızı sağlar (overwrite etmez).
`@@parent` direktifi, görünüm render edilirken layout’un içeriğiyle değiştirilir.

> `@endsection` yalnızca bir bölüm tanımlar.
> `@show` ise bölümü tanımlar **ve hemen görüntüler**.

`@yield` direktifi ikinci parametre olarak **varsayılan değer** alabilir:

```blade
@yield('content', 'Default content')
```

---

## Formlar

### CSRF Alanı

Her HTML formunda CSRF koruması için bir gizli `token` alanı bulunmalıdır.
`@csrf` direktifi bu alanı oluşturur:

```blade
<form method="POST" action="/profile">
    @csrf
    ...
</form>
```

### HTTP Method Alanı

HTML formları yalnızca `GET` ve `POST` istekleri gönderebilir.
`PUT`, `PATCH` veya `DELETE` istekleri göndermek için `_method` alanını eklemeniz gerekir.
`@method` direktifi bu alanı otomatik olarak oluşturur:

```blade
<form action="/foo/bar" method="POST">
    @method('PUT')
    ...
</form>
```

---

## Doğrulama Hataları (Validation Errors)

`@error` direktifi, belirli bir form alanı için doğrulama hatası olup olmadığını hızlıca kontrol eder.
İçinde `$message` değişkenini kullanarak hata mesajını görüntüleyebilirsiniz:

```blade
<!-- /resources/views/post/create.blade.php -->
<label for="title">Post Title</label>
 
<input
    id="title"
    type="text"
    class="@error('title') is-invalid @enderror"
/>
 
@error('title')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror
```

`@error` bir `if` ifadesine dönüştüğü için, `@else` ile hatasız durumda içerik gösterebilirsiniz:

```blade
<!-- /resources/views/auth.blade.php -->
<label for="email">Email address</label>
 
<input
    id="email"
    type="email"
    class="@error('email') is-invalid @else is-valid @enderror"
/>
```

Birden fazla formu olan sayfalarda belirli bir **error bag** için hata mesajı göstermek isterseniz ikinci parametreyi kullanabilirsiniz:

```blade
<!-- /resources/views/auth.blade.php -->
<label for="email">Email address</label>
 
<input
    id="email"
    type="email"
    class="@error('email', 'login') is-invalid @enderror"
/>
 
@error('email', 'login')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror
```

---

## Stack’ler

Blade, belirli bir isimle “stack” (yığın) tanımlamanıza ve başka görünümlerden bu yığına içerik eklemenize olanak tanır.
Bu genellikle child view’ların gerekli JavaScript dosyalarını eklemesi için kullanılır:

```blade
@push('scripts')
    <script src="/example.js"></script>
@endpush
```

Belirli bir koşul sağlanırsa içerik push etmek için `@pushIf` kullanılabilir:

```blade
@pushIf($shouldPush, 'scripts')
    <script src="/example.js"></script>
@endPushIf
```

Stack’e istediğiniz kadar push yapabilirsiniz.
Yığının tamamını görüntülemek için `@stack` kullanılır:

```blade
<head>
    @stack('scripts')
</head>
```

İçeriği yığının başına eklemek isterseniz `@prepend` kullanabilirsiniz:

```blade
@push('scripts')
    This will be second...
@endpush
 
@prepend('scripts')
    This will be first...
@endprepend
```

---

## Servis Enjeksiyonu (Service Injection)

`@inject` direktifi, Laravel servis container’dan bir servisi almanızı sağlar.
İlk parametre değişken adı, ikinci parametre servis sınıfıdır:

```blade
@inject('metrics', 'App\Services\MetricsService')
 
<div>
    Monthly Revenue: {{ $metrics->monthlyRevenue() }}.
</div>
```

---

## Inline Blade Şablonlarını Render Etme

Bazen bir Blade şablonu metnini doğrudan HTML’e dönüştürmeniz gerekebilir.
Bunu `Blade::render` metodu ile yapabilirsiniz:

```php
use Illuminate\Support\Facades\Blade;

return Blade::render('Hello, {{ $name }}', ['name' => 'Julian Bashir']);
```

Laravel, bu geçici dosyaları `storage/framework/views` dizinine kaydeder.
Render işleminden sonra bu dosyaların silinmesini isterseniz `deleteCachedView` parametresini kullanabilirsiniz:

```php
return Blade::render(
    'Hello, {{ $name }}',
    ['name' => 'Julian Bashir'],
    deleteCachedView: true
);
```

---

## Blade Fragment’lerini Render Etme

Turbo veya htmx gibi frontend framework’leri kullanırken, bazen sadece şablonun bir kısmını döndürmek istersiniz.
Bunun için `@fragment` direktifini kullanabilirsiniz:

```blade
@fragment('user-list')
    <ul>
        @foreach ($users as $user)
            <li>{{ $user->name }}</li>
        @endforeach
    </ul>
@endfragment
```

Yalnızca belirli bir fragment’i döndürmek için:

```php
return view('dashboard', ['users' => $users])->fragment('user-list');
```

Koşullu olarak fragment döndürmek için `fragmentIf` kullanılır:

```php
return view('dashboard', ['users' => $users])
    ->fragmentIf($request->hasHeader('HX-Request'), 'user-list');
```

Birden fazla fragment döndürmek için:

```php
view('dashboard', ['users' => $users])
    ->fragments(['user-list', 'comment-list']);
```

---

## Blade’i Genişletme (Extending Blade)

Blade’e özel direktifler eklemek için `Blade::directive` metodunu kullanabilirsiniz.
Bu metot, özel bir direktif oluşturur ve direktifin içindeki ifadeyi callback fonksiyonuna iletir.

Örneğin, bir `@datetime($var)` direktifi tanımlayalım:

```php
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::directive('datetime', function (string $expression) {
            return "<?php echo ($expression)->format('m/d/Y H:i'); ?>";
        });
    }
}
```

Bu direktif, şu şekilde PHP’ye dönüştürülür:

```php
<?php echo ($var)->format('m/d/Y H:i'); ?>
```

> 🧹 Direktiflerde değişiklik yaptıktan sonra `php artisan view:clear` komutuyla önbelleğe alınan görünümleri temizleyin.

---

## Özel Echo İşleyicileri (Custom Echo Handlers)

Bir nesneyi Blade içinde `{{ }}` ile yazdırdığınızda, PHP’nin `__toString()` metodu çağrılır.
Ancak üçüncü parti sınıflarda bu metoda erişemeyebilirsiniz.
Bu durumda, Blade için özel bir echo işleyicisi tanımlayabilirsiniz:

```php
use Illuminate\Support\Facades\Blade;
use Money\Money;

public function boot(): void
{
    Blade::stringable(function (Money $money) {
        return $money->formatTo('en_GB');
    });
}
```

Böylece Blade içinde:

```blade
Cost: {{ $money }}
```

şeklinde kullanabilirsiniz.

---

## Özel If İfadeleri (Custom If Statements)

Basit koşullar için özel bir direktif tanımlamak yerine `Blade::if` metodu kullanılabilir.

```php
use Illuminate\Support\Facades\Blade;

public function boot(): void
{
    Blade::if('disk', function (string $value) {
        return config('filesystems.default') === $value;
    });
}
```

Kullanımı:

```blade
@disk('local')
    <!-- Using local disk -->
@elsedisk('s3')
    <!-- Using s3 disk -->
@else
    <!-- Using another disk -->
@enddisk
 
@unlessdisk('local')
    <!-- Not using local disk -->
@enddisk
```

