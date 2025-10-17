
Doğrulama  
Giriş  
Laravel, uygulamanızın gelen verilerini doğrulamak için birkaç farklı yaklaşım sunar. En yaygın olanı, gelen tüm HTTP isteklerinde mevcut olan validate metodunu kullanmaktır. Ancak, doğrulama için diğer yaklaşımları da tartışacağız.

Laravel, verilerinize uygulayabileceğiniz çok çeşitli kullanışlı doğrulama kuralları içerir; hatta belirli bir veritabanı tablosundaki değerlerin benzersiz olup olmadığını doğrulama yeteneği bile sağlar. Laravel'in tüm doğrulama özelliklerine aşina olabilmeniz için bu doğrulama kurallarının her birini ayrıntılı olarak ele alacağız.

Doğrulama Hızlı Başlangıç  
Laravel’in güçlü doğrulama özelliklerini öğrenmek için, bir formun doğrulanması ve hata mesajlarının kullanıcıya geri gösterilmesine dair tam bir örneğe bakalım. Bu genel bakışı okuyarak, Laravel kullanarak gelen istek verilerini nasıl doğrulayacağınız konusunda iyi bir genel anlayış kazanabilirsiniz:

Rotaların Tanımlanması  
Öncelikle, routes/web.php dosyamızda aşağıdaki rotaların tanımlandığını varsayalım:

```php
use App\Http\Controllers\PostController;

Route::get('/post/create', [PostController::class, 'create']);
Route::post('/post', [PostController::class, 'store']);
````

GET rotası, kullanıcının yeni bir blog gönderisi oluşturması için bir form görüntülerken, POST rotası yeni blog gönderisini veritabanında saklayacaktır.

Denetleyicinin Oluşturulması
Sonra, bu rotalara gelen istekleri yöneten basit bir denetleyiciye bakalım. Şimdilik store metodunu boş bırakacağız:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Yeni bir blog gönderisi oluşturma formunu göster.
     */
    public function create(): View
    {
        return view('post.create');
    }

    /**
     * Yeni bir blog gönderisini kaydet.
     */
    public function store(Request $request): RedirectResponse
    {
        // Blog gönderisini doğrula ve kaydet...

        $post = /** ... */

        return to_route('post.show', ['post' => $post->id]);
    }
}
```

Doğrulama Mantığını Yazma
Artık store metodumuza yeni blog gönderisini doğrulamak için mantığı eklemeye hazırız. Bunu yapmak için, Illuminate\Http\Request nesnesinin sağladığı validate metodunu kullanacağız. Eğer doğrulama kuralları geçerse, kodunuz normal şekilde çalışmaya devam eder; ancak doğrulama başarısız olursa, bir Illuminate\Validation\ValidationException istisnası fırlatılır ve uygun hata yanıtı otomatik olarak kullanıcıya gönderilir.

Doğrulama, geleneksel bir HTTP isteği sırasında başarısız olursa, önceki URL’ye bir yönlendirme yanıtı oluşturulur. Eğer gelen istek bir XHR isteğiyse, doğrulama hata mesajlarını içeren bir JSON yanıtı döndürülür.

validate metodunu daha iyi anlamak için store metoduna geri dönelim:

```php
public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'title' => 'required|unique:posts|max:255',
        'body' => 'required',
    ]);

    // Blog gönderisi geçerli...

    return redirect('/posts');
}
```

Gördüğünüz gibi, doğrulama kuralları validate metoduna aktarılır. Endişelenmeyin — mevcut tüm doğrulama kuralları belgelenmiştir. Doğrulama başarısız olursa, uygun yanıt otomatik olarak oluşturulur. Doğrulama geçerse, denetleyicimiz normal şekilde çalışmaya devam eder.

Alternatif olarak, doğrulama kuralları tek bir | ile ayrılmış string yerine dizi olarak da belirtilebilir:

```php
$validatedData = $request->validate([
    'title' => ['required', 'unique:posts', 'max:255'],
    'body' => ['required'],
]);
```

Ayrıca, validateWithBag metodunu kullanarak bir isteği doğrulayabilir ve hata mesajlarını adlandırılmış bir hata torbasında saklayabilirsiniz:

```php
$validatedData = $request->validateWithBag('post', [
    'title' => ['required', 'unique:posts', 'max:255'],
    'body' => ['required'],
]);
```

İlk Doğrulama Hatasında Durdurma
Bazen, bir özellikteki ilk doğrulama hatasından sonra diğer doğrulama kurallarının çalışmasını durdurmak isteyebilirsiniz. Bunu yapmak için, özelliğe bail kuralını atayın:

```php
$request->validate([
    'title' => 'bail|required|unique:posts|max:255',
    'body' => 'required',
]);
```

Bu örnekte, title özelliğindeki unique kuralı başarısız olursa, max kuralı kontrol edilmez. Kurallar atanma sırasına göre doğrulanır.

İç İçe Geçmiş Özellikler Üzerine Not
Eğer gelen HTTP isteği “iç içe” alan verileri içeriyorsa, bu alanları “nokta” sözdizimi kullanarak doğrulama kurallarında belirtebilirsiniz:

```php
$request->validate([
    'title' => 'required|unique:posts|max:255',
    'author.name' => 'required',
    'author.description' => 'required',
]);
```

Öte yandan, alan adınızda gerçek bir nokta karakteri varsa, bunu “nokta” sözdizimi olarak yorumlanmaması için ters eğik çizgi ile kaçırabilirsiniz:

```php
$request->validate([
    'title' => 'required|unique:posts|max:255',
    'v1\.0' => 'required',
]);
```

Doğrulama Hatalarını Görüntüleme
Peki, gelen istek alanları verilen doğrulama kurallarını geçmezse ne olur? Daha önce bahsedildiği gibi, Laravel kullanıcıyı otomatik olarak önceki konumuna yönlendirecektir. Ayrıca, tüm doğrulama hataları ve istek girdisi otomatik olarak oturuma flash’lanır.

$errors değişkeni, web middleware grubunun sağladığı Illuminate\View\Middleware\ShareErrorsFromSession middleware’i tarafından tüm görünümlerle paylaşılır. Bu middleware uygulandığında, $errors değişkeni her zaman görünümlerinizde mevcut olur; böylece her zaman tanımlı olduğunu varsayabilir ve güvenle kullanabilirsiniz. $errors değişkeni, Illuminate\Support\MessageBag örneği olacaktır. Bu nesneyle çalışma hakkında daha fazla bilgi için belgelerine göz atın.

Örneğimizde, doğrulama başarısız olduğunda kullanıcı denetleyicimizin create metoduna yönlendirilir ve hata mesajlarını görünümde gösterebiliriz:

```blade
<!-- /resources/views/post/create.blade.php -->

<h1>Create Post</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Create Post Form -->
```

Hata Mesajlarını Özelleştirme
Laravel’in yerleşik doğrulama kurallarının her biri, uygulamanızın lang/en/validation.php dosyasında yer alan bir hata mesajına sahiptir. Eğer uygulamanızda lang dizini yoksa, Laravel’e bunu lang:publish Artisan komutu ile oluşturmasını söyleyebilirsiniz.

lang/en/validation.php dosyasında, her doğrulama kuralı için bir çeviri girdisi bulacaksınız. Uygulamanızın ihtiyaçlarına göre bu mesajları değiştirmekte özgürsünüz.

Ayrıca, bu dosyayı başka bir dil dizinine kopyalayarak, uygulamanızın dili için mesajları çevirebilirsiniz. Laravel yerelleştirmesi hakkında daha fazla bilgi edinmek için tam yerelleştirme belgelerine göz atın.

Varsayılan olarak, Laravel uygulama iskeleti lang dizinini içermez. Laravel’in dil dosyalarını özelleştirmek istiyorsanız, bunları lang:publish Artisan komutu aracılığıyla yayımlayabilirsiniz.

XHR İstekleri ve Doğrulama
Bu örnekte, verileri uygulamaya göndermek için geleneksel bir form kullandık. Ancak, birçok uygulama JavaScript destekli bir ön uçtan XHR istekleri alır. Bir XHR isteği sırasında validate metodunu kullanırken, Laravel yönlendirme yanıtı oluşturmaz. Bunun yerine, Laravel tüm doğrulama hatalarını içeren bir JSON yanıtı üretir. Bu JSON yanıtı 422 HTTP durum kodu ile gönderilir.

@error Direktifi
@error Blade direktifini, belirli bir özellik için doğrulama hata mesajlarının var olup olmadığını hızlıca belirlemek için kullanabilirsiniz. Bir @error bloğu içinde, hata mesajını göstermek için $message değişkenini yazdırabilirsiniz:

```blade
<!-- /resources/views/post/create.blade.php -->

<label for="title">Post Title</label>

<input
    id="title"
    type="text"
    name="title"
    class="@error('title') is-invalid @enderror"
/>

@error('title')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror
```

Eğer adlandırılmış hata torbaları kullanıyorsanız, @error direktifine hata torbasının adını ikinci parametre olarak geçebilirsiniz:

```blade
<input ... class="@error('title', 'post') is-invalid @enderror">
```

Formların Yeniden Doldurulması
Laravel, bir doğrulama hatası nedeniyle yönlendirme yanıtı oluşturduğunda, istek girdisinin tamamını otomatik olarak oturuma flash’lar. Bu, kullanıcı tarafından gönderilmeye çalışılan formu bir sonraki istekte yeniden doldurabilmenizi sağlar.

Önceki isteğin flash’lanmış girdisini almak için, Illuminate\Http\Request örneği üzerindeki old metodunu çağırın. old metodu, oturumdan daha önce flash’lanmış giriş verilerini çeker:

```php
$title = $request->old('title');
```

Laravel ayrıca global bir old yardımcı fonksiyonu da sağlar. Bir Blade şablonunda eski girdileri görüntülüyorsanız, formu yeniden doldurmak için old yardımcı fonksiyonunu kullanmak daha uygundur. Verilen alan için eski giriş yoksa, null döndürülür:

```blade
<input type="text" name="title" value="{{ old('title') }}">
```

Opsiyonel Alanlar Hakkında Not
Varsayılan olarak, Laravel uygulamanızın global middleware yığınına TrimStrings ve ConvertEmptyStringsToNull middleware’lerini dahil eder. Bu nedenle, doğrulayıcının null değerlerini geçersiz olarak değerlendirmesini istemiyorsanız, “opsiyonel” istek alanlarınızı genellikle nullable olarak işaretlemeniz gerekir. Örneğin:

```php
$request->validate([
    'title' => 'required|unique:posts|max:255',
    'body' => 'required',
    'publish_at' => 'nullable|date',
]);
```

Bu örnekte, publish_at alanının null veya geçerli bir tarih gösterimi olabileceğini belirtiyoruz. Eğer nullable değiştiricisi kural tanımına eklenmezse, doğrulayıcı null değerini geçersiz bir tarih olarak değerlendirir.

Doğrulama Hata Yanıtı Formatı
Uygulamanız bir Illuminate\Validation\ValidationException istisnası fırlattığında ve gelen HTTP isteği bir JSON yanıtı beklediğinde, Laravel hata mesajlarını otomatik olarak biçimlendirir ve 422 Unprocessable Entity HTTP yanıtı döndürür.

Aşağıda, doğrulama hataları için JSON yanıt biçimine bir örnek inceleyebilirsiniz. İç içe geçmiş hata anahtarlarının “nokta” gösterimi biçiminde düzleştirildiğine dikkat edin:

```json
{
    "message": "The team name must be a string. (and 4 more errors)",
    "errors": {
        "team_name": [
            "The team name must be a string.",
            "The team name must be at least 1 characters."
        ],
        "authorization.role": [
            "The selected authorization.role is invalid."
        ],
        "users.0.email": [
            "The users.0.email field is required."
        ],
        "users.2.email": [
            "The users.2.email must be a valid email address."
        ]
    }
}
```


Form İstek Doğrulama  
Form İsteklerinin Oluşturulması  
Daha karmaşık doğrulama senaryoları için bir “form request” (form isteği) oluşturmak isteyebilirsiniz. Form istekleri, kendi doğrulama ve yetkilendirme mantığını kapsülleyen özel istek sınıflarıdır. Bir form isteği sınıfı oluşturmak için make:request Artisan CLI komutunu kullanabilirsiniz:

```bash
php artisan make:request StorePostRequest
````

Oluşturulan form isteği sınıfı, app/Http/Requests dizinine yerleştirilecektir. Bu dizin yoksa, make:request komutunu çalıştırdığınızda oluşturulur. Laravel tarafından oluşturulan her form isteği iki metoda sahiptir: authorize ve rules.

Tahmin edebileceğiniz gibi, authorize metodu, şu anda kimliği doğrulanmış kullanıcının istek tarafından temsil edilen işlemi gerçekleştirip gerçekleştiremeyeceğini belirlemekten sorumludur. rules metodu ise isteğin verilerine uygulanması gereken doğrulama kurallarını döndürür:

```php
public function rules(): array
{
    return [
        'title' => 'required|unique:posts|max:255',
        'body' => 'required',
    ];
}
```

rules metodunun imzası içinde ihtiyaç duyduğunuz tüm bağımlılıkları type-hint olarak belirtebilirsiniz. Bunlar Laravel servis container tarafından otomatik olarak çözülür.

Peki doğrulama kuralları nasıl değerlendirilir? Tek yapmanız gereken, isteği denetleyici metodunuzda type-hint olarak belirtmektir. Gelen form isteği, denetleyici metodu çağrılmadan önce doğrulanır, yani denetleyicinizi doğrulama mantığıyla doldurmanıza gerek yoktur:

```php
public function store(StorePostRequest $request): RedirectResponse
{
    // Gelen istek geçerli...

    // Doğrulanmış girdiyi al...
    $validated = $request->validated();

    // Doğrulanmış girdinin belirli bölümlerini al...
    $validated = $request->safe()->only(['name', 'email']);
    $validated = $request->safe()->except(['name', 'email']);

    // Blog gönderisini kaydet...

    return redirect('/posts');
}
```

Eğer doğrulama başarısız olursa, kullanıcıyı önceki konumuna göndermek için bir yönlendirme yanıtı oluşturulur. Hatalar da oturuma flash’lanarak görüntüleme için kullanılabilir hale gelir. Eğer istek bir XHR isteğiyse, kullanıcıya 422 durum koduna sahip bir HTTP yanıtı döndürülür ve bu yanıt doğrulama hatalarının JSON temsiline sahiptir.

Inertia destekli Laravel ön yüzünüzde gerçek zamanlı form doğrulaması eklemeniz mi gerekiyor? Laravel Precognition’a göz atın.

Ek Doğrulama Gerçekleştirme
Bazen, ilk doğrulamanız tamamlandıktan sonra ek doğrulama yapmanız gerekebilir. Bunu form isteğinin after metodunu kullanarak yapabilirsiniz.

after metodu, doğrulama tamamlandıktan sonra çağrılacak callable veya closure’lardan oluşan bir dizi döndürmelidir. Verilen callable’lar bir Illuminate\Validation\Validator örneği alır, böylece gerekirse ek hata mesajları oluşturabilirsiniz:

```php
use Illuminate\Validation\Validator;

public function after(): array
{
    return [
        function (Validator $validator) {
            if ($this->somethingElseIsInvalid()) {
                $validator->errors()->add(
                    'field',
                    'Something is wrong with this field!'
                );
            }
        }
    ];
}
```

Belirtildiği gibi, after metodunun döndürdüğü dizi çağrılabilir sınıflar da içerebilir. Bu sınıfların __invoke metodu bir Illuminate\Validation\Validator örneği alacaktır:

```php
use App\Validation\ValidateShippingTime;
use App\Validation\ValidateUserStatus;
use Illuminate\Validation\Validator;

public function after(): array
{
    return [
        new ValidateUserStatus,
        new ValidateShippingTime,
        function (Validator $validator) {
            //
        }
    ];
}
```

İlk Doğrulama Hatasında Durdurma
İstek sınıfınıza stopOnFirstFailure özelliğini ekleyerek, doğrulayıcıya ilk doğrulama hatasından sonra tüm doğrulamayı durdurması gerektiğini belirtebilirsiniz:

```php
protected $stopOnFirstFailure = true;
```

Yönlendirme Konumunu Özelleştirme
Form isteği doğrulaması başarısız olduğunda, kullanıcıyı önceki konumuna göndermek için bir yönlendirme yanıtı oluşturulur. Ancak bu davranışı özelleştirebilirsiniz. Bunu yapmak için, form isteğinizde bir $redirect özelliği tanımlayın:

```php
protected $redirect = '/dashboard';
```

Veya kullanıcıları adlandırılmış bir route’a yönlendirmek istiyorsanız, bunun yerine $redirectRoute özelliğini tanımlayabilirsiniz:

```php
protected $redirectRoute = 'dashboard';
```

Form İsteklerini Yetkilendirme
Form isteği sınıfı ayrıca bir authorize metodu içerir. Bu metot içinde, kimliği doğrulanmış kullanıcının belirli bir kaynağı güncelleme yetkisine sahip olup olmadığını belirleyebilirsiniz. Örneğin, bir kullanıcının güncellemeye çalıştığı blog yorumuna sahip olup olmadığını belirleyebilirsiniz. Çoğu durumda, bu metod içinde yetkilendirme kapıları ve politikaları ile etkileşime girersiniz:

```php
use App\Models\Comment;

public function authorize(): bool
{
    $comment = Comment::find($this->route('comment'));

    return $comment && $this->user()->can('update', $comment);
}
```

Tüm form istekleri temel Laravel request sınıfını genişlettiğinden, user metodunu kullanarak şu anda kimliği doğrulanmış kullanıcıya erişebiliriz. Ayrıca yukarıdaki örnekteki route metoduna yapılan çağrıya dikkat edin. Bu metod, çağrılan rotada tanımlı URI parametrelerine erişmenizi sağlar, örneğin:

```php
Route::post('/comment/{comment}');
```

Bu nedenle, uygulamanız rota model bağlamayı kullanıyorsa, çözümlenmiş modele request özelliği üzerinden erişerek kodunuzu daha da sadeleştirebilirsiniz:

```php
return $this->user()->can('update', $this->comment);
```

authorize metodu false dönerse, 403 durum koduna sahip bir HTTP yanıtı otomatik olarak döndürülür ve denetleyici metodunuz çalıştırılmaz.

Eğer isteğin yetkilendirme mantığını uygulamanızın başka bir bölümünde ele almayı planlıyorsanız, authorize metodunu tamamen kaldırabilir veya sadece true döndürebilirsiniz:

```php
public function authorize(): bool
{
    return true;
}
```

authorize metodunun imzası içinde ihtiyaç duyduğunuz tüm bağımlılıkları type-hint olarak belirtebilirsiniz. Bunlar, Laravel servis container tarafından otomatik olarak çözülür.

Hata Mesajlarını Özelleştirme
Form isteği tarafından kullanılan hata mesajlarını, messages metodunu geçersiz kılarak özelleştirebilirsiniz. Bu metod, attribute / rule çiftlerini ve bunlara karşılık gelen hata mesajlarını döndüren bir dizi döndürmelidir:

```php
public function messages(): array
{
    return [
        'title.required' => 'A title is required',
        'body.required' => 'A message is required',
    ];
}
```

Doğrulama Özelliklerini Özelleştirme
Laravel’in yerleşik doğrulama hata mesajlarının birçoğu :attribute placeholder’ı içerir. Eğer bu placeholder’ın özel bir isimle değiştirilmesini istiyorsanız, attributes metodunu geçersiz kılarak özel isimler belirtebilirsiniz:

```php
public function attributes(): array
{
    return [
        'email' => 'email address',
    ];
}
```

Doğrulama için Girdileri Hazırlama
Eğer doğrulama kurallarını uygulamadan önce istekteki verileri hazırlamanız veya temizlemeniz gerekiyorsa, prepareForValidation metodunu kullanabilirsiniz:

```php
use Illuminate\Support\Str;

protected function prepareForValidation(): void
{
    $this->merge([
        'slug' => Str::slug($this->slug),
    ]);
}
```

Benzer şekilde, doğrulama tamamlandıktan sonra herhangi bir istek verisini normalize etmeniz gerekiyorsa, passedValidation metodunu kullanabilirsiniz:

```php
protected function passedValidation(): void
{
    $this->replace(['name' => 'Taylor']);
}
```

Doğrulayıcıları Manuel Olarak Oluşturma
Eğer isteğin validate metodunu kullanmak istemiyorsanız, Validator facade kullanarak bir doğrulayıcı örneği manuel olarak oluşturabilirsiniz. Facade üzerindeki make metodu yeni bir doğrulayıcı örneği oluşturur:

```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make($request->all(), [
    'title' => 'required|unique:posts|max:255',
    'body' => 'required',
]);
```

make metoduna geçirilen ilk argüman doğrulanacak veridir. İkinci argüman ise bu verilere uygulanması gereken doğrulama kurallarını içeren dizidir.

Doğrulama başarısız olduktan sonra, hata mesajlarını oturuma flash’lamak için withErrors metodunu kullanabilirsiniz. Bu metod kullanıldığında, $errors değişkeni yönlendirmeden sonra otomatik olarak görünümlerle paylaşılır, böylece kullanıcıya geri göstermeniz kolaylaşır. withErrors metodu bir validator, MessageBag veya PHP dizisi kabul eder.

İlk Doğrulama Hatasında Durdurma
stopOnFirstFailure metodu, doğrulayıcıya bir doğrulama hatası oluştuğunda diğer tüm doğrulamaları durdurmasını söyler:

```php
if ($validator->stopOnFirstFailure()->fails()) {
    // ...
}
```

Otomatik Yönlendirme
Eğer manuel olarak bir doğrulayıcı örneği oluşturmak istiyor ancak HTTP isteğinin validate metodunun sunduğu otomatik yönlendirmeden faydalanmak istiyorsanız, mevcut doğrulayıcı örneği üzerinde validate metodunu çağırabilirsiniz:

```php
Validator::make($request->all(), [
    'title' => 'required|unique:posts|max:255',
    'body' => 'required',
])->validate();
```

Doğrulama başarısız olursa, kullanıcı otomatik olarak yönlendirilir veya bir XHR isteği durumunda JSON yanıtı döndürülür.

Doğrulama hatalarını adlandırılmış bir hata torbasında saklamak istiyorsanız, validateWithBag metodunu kullanabilirsiniz:

```php
Validator::make($request->all(), [
    'title' => 'required|unique:posts|max:255',
    'body' => 'required',
])->validateWithBag('post');
```

Adlandırılmış Hata Torbaları
Bir sayfada birden fazla formunuz varsa, her bir formun doğrulama hatalarını içeren MessageBag’leri adlandırabilirsiniz. Bunu yapmak için, withErrors metoduna ikinci argüman olarak bir ad geçirin:

```php
return redirect('/register')->withErrors($validator, 'login');
```

Daha sonra, $errors değişkeninden adlandırılmış MessageBag örneğine erişebilirsiniz:

```blade
{{ $errors->login->first('email') }}
```

Hata Mesajlarını Özelleştirme
Gerekirse, Laravel’in varsayılan hata mesajları yerine doğrulayıcı örneği için özel hata mesajları sağlayabilirsiniz. Bunu yapmanın birkaç yolu vardır. İlk olarak, özel mesajları Validator::make metoduna üçüncü argüman olarak geçirebilirsiniz:

```php
$validator = Validator::make($input, $rules, [
    'required' => 'The :attribute field is required.',
]);
```

Bu örnekte, :attribute placeholder’ı doğrulanan alanın gerçek adıyla değiştirilir. Ayrıca hata mesajlarında diğer placeholder’ları da kullanabilirsiniz:

```php
$messages = [
    'same' => 'The :attribute and :other must match.',
    'size' => 'The :attribute must be exactly :size.',
    'between' => 'The :attribute value :input is not between :min - :max.',
    'in' => 'The :attribute must be one of the following types: :values',
];
```

Belirli Bir Özellik İçin Özel Mesaj Belirtme
Bazen yalnızca belirli bir özellik için özel bir hata mesajı belirtmek isteyebilirsiniz. Bunu, “nokta” sözdizimini kullanarak yapabilirsiniz:

```php
$messages = [
    'email.required' => 'We need to know your email address!',
];
```

Özel Özellik Değerleri Belirtme
Laravel’in yerleşik hata mesajlarının birçoğu, doğrulanan alanın adıyla değiştirilen bir :attribute placeholder’ı içerir. Bu placeholder’ların belirli alanlar için özel değerlerle değiştirilmesini istiyorsanız, Validator::make metoduna dördüncü argüman olarak özel attribute dizisi geçebilirsiniz:

```php
$validator = Validator::make($input, $rules, $messages, [
    'email' => 'email address',
]);
```

Ek Doğrulama Gerçekleştirme
Bazen, ilk doğrulamanız tamamlandıktan sonra ek doğrulama yapmanız gerekebilir. Bunu validator’ın after metodunu kullanarak yapabilirsiniz. after metodu, doğrulama tamamlandıktan sonra çağrılacak bir closure veya callable dizisi kabul eder:

```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make(/* ... */);

$validator->after(function ($validator) {
    if ($this->somethingElseIsInvalid()) {
        $validator->errors()->add('field', 'Something is wrong with this field!');
    }
});

if ($validator->fails()) {
    // ...
}
```

after metodu ayrıca callable dizileri de kabul eder, bu özellikle “doğrulama sonrası” mantığınız çağrılabilir sınıflar içinde kapsüllenmişse kullanışlıdır:

```php
use App\Validation\ValidateShippingTime;
use App\Validation\ValidateUserStatus;

$validator->after([
    new ValidateUserStatus,
    new ValidateShippingTime,
    function ($validator) {
        // ...
    },
]);
```

Doğrulanmış Girdilerle Çalışma  
Bir form isteği veya manuel olarak oluşturulmuş bir doğrulayıcı örneği kullanarak gelen istek verilerini doğruladıktan sonra, gerçekten doğrulamadan geçmiş verileri almak isteyebilirsiniz. Bu, birkaç farklı şekilde yapılabilir. İlk olarak, bir form isteği veya doğrulayıcı örneği üzerinde validated metodunu çağırabilirsiniz. Bu metod, doğrulamadan geçen verileri içeren bir dizi döndürür:

```php
$validated = $request->validated();

$validated = $validator->validated();
````

Alternatif olarak, form isteği veya doğrulayıcı örneği üzerinde safe metodunu çağırabilirsiniz. Bu metod, Illuminate\Support\ValidatedInput sınıfının bir örneğini döndürür. Bu nesne, doğrulanmış verilerin bir alt kümesini veya tamamını almak için only, except ve all metotlarını sunar:

```php
$validated = $request->safe()->only(['name', 'email']);

$validated = $request->safe()->except(['name', 'email']);

$validated = $request->safe()->all();
```

Ayrıca, Illuminate\Support\ValidatedInput örneği bir dizi gibi yinelenebilir ve erişilebilir:

```php
// Doğrulanmış veriler yinelenebilir...
foreach ($request->safe() as $key => $value) {
    // ...
}

// Doğrulanmış verilere dizi olarak erişilebilir...
$validated = $request->safe();

$email = $validated['email'];
```

Doğrulanmış verilere ek alanlar eklemek isterseniz, merge metodunu çağırabilirsiniz:

```php
$validated = $request->safe()->merge(['name' => 'Taylor Otwell']);
```

Doğrulanmış verileri bir koleksiyon örneği olarak almak isterseniz, collect metodunu çağırabilirsiniz:

```php
$collection = $request->safe()->collect();
```

---

Hata Mesajlarıyla Çalışma
Bir Validator örneği üzerinde errors metodunu çağırdıktan sonra, hata mesajlarıyla çalışmak için çeşitli kullanışlı yöntemler sunan bir Illuminate\Support\MessageBag örneği alırsınız. Görünümlerde otomatik olarak kullanılabilir hale gelen $errors değişkeni de MessageBag sınıfının bir örneğidir.

---

Bir Alan İçin İlk Hata Mesajını Alma
Belirli bir alan için ilk hata mesajını almak için first metodunu kullanın:

```php
$errors = $validator->errors();

echo $errors->first('email');
```

---

Bir Alan İçin Tüm Hata Mesajlarını Alma
Belirli bir alan için tüm mesajların bir dizisini almak isterseniz, get metodunu kullanın:

```php
foreach ($errors->get('email') as $message) {
    // ...
}
```

Eğer bir dizi form alanını doğruluyorsanız, * karakterini kullanarak her bir dizi öğesinin tüm mesajlarını alabilirsiniz:

```php
foreach ($errors->get('attachments.*') as $message) {
    // ...
}
```

---

Tüm Alanlar İçin Tüm Hata Mesajlarını Alma
Tüm alanlardaki mesajların bir dizisini almak için all metodunu kullanın:

```php
foreach ($errors->all() as $message) {
    // ...
}
```

---

Bir Alan İçin Mesajların Var Olup Olmadığını Belirleme
Belirli bir alan için herhangi bir hata mesajı olup olmadığını belirlemek için has metodunu kullanabilirsiniz:

```php
if ($errors->has('email')) {
    // ...
}
```

---

Dil Dosyalarında Özel Mesajlar Belirtme
Laravel’in yerleşik doğrulama kurallarının her birinin, uygulamanızın `lang/en/validation.php` dosyasında yer alan bir hata mesajı vardır. Eğer uygulamanızda bir `lang` dizini yoksa, Laravel’e bunu oluşturması için `lang:publish` Artisan komutunu çalıştırabilirsiniz.

`lang/en/validation.php` dosyasında, her doğrulama kuralı için bir çeviri girdisi bulacaksınız. Uygulamanızın ihtiyaçlarına göre bu mesajları değiştirmekte özgürsünüz.

Ayrıca, bu dosyayı başka bir dil dizinine kopyalayarak uygulamanızın dili için mesajları çevirebilirsiniz. Laravel yerelleştirme hakkında daha fazla bilgi edinmek için tam yerelleştirme belgelerine göz atın.

Varsayılan olarak, Laravel uygulama iskeleti `lang` dizinini içermez. Laravel’in dil dosyalarını özelleştirmek istiyorsanız, bunları `lang:publish` Artisan komutu aracılığıyla yayımlayabilirsiniz.

---

Belirli Alanlar İçin Özel Mesajlar
Uygulamanızın doğrulama dil dosyalarında, belirli attribute ve rule kombinasyonları için kullanılan hata mesajlarını özelleştirebilirsiniz. Bunu yapmak için, `lang/xx/validation.php` dosyanızdaki `custom` dizisine özel mesajlarınızı ekleyin:

```php
'custom' => [
    'email' => [
        'required' => 'We need to know your email address!',
        'max' => 'Your email address is too long!'
    ],
],
```

---

Dil Dosyalarında Özellik Belirtme
Laravel’in yerleşik hata mesajlarının birçoğu, doğrulanan alanın veya özelliğin adıyla değiştirilen bir `:attribute` placeholder’ı içerir. Eğer doğrulama mesajınızın `:attribute` kısmının özel bir değerle değiştirilmesini istiyorsanız, `lang/xx/validation.php` dosyanızdaki `attributes` dizisinde özel attribute adını belirtebilirsiniz:

```php
'attributes' => [
    'email' => 'email address',
],
```

Varsayılan olarak, Laravel uygulama iskeleti `lang` dizinini içermez. Laravel’in dil dosyalarını özelleştirmek istiyorsanız, bunları `lang:publish` Artisan komutu aracılığıyla yayımlayabilirsiniz.

---

Dil Dosyalarında Değerler Belirtme
Laravel’in bazı yerleşik doğrulama hata mesajları, doğrulanan alanın mevcut değeriyle değiştirilen bir `:value` placeholder’ı içerir. Ancak bazen, doğrulama mesajınızın `:value` kısmının değerin daha kullanıcı dostu bir temsilcisiyle değiştirilmesini isteyebilirsiniz.
Örneğin, `payment_type` değeri `cc` olduğunda bir kredi kartı numarasının gerekli olduğunu belirten aşağıdaki kuralı düşünün:

```php
Validator::make($request->all(), [
    'credit_card_number' => 'required_if:payment_type,cc'
]);
```

Eğer bu doğrulama kuralı başarısız olursa, şu hata mesajı oluşturulur:

```
The credit card number field is required when payment type is cc.
```

`cc` yerine daha kullanıcı dostu bir değer göstermek isterseniz, `lang/xx/validation.php` dil dosyanızda bir `values` dizisi tanımlayabilirsiniz:

```php
'values' => [
    'payment_type' => [
        'cc' => 'credit card'
    ],
],
```

Bu değeri tanımladıktan sonra, doğrulama kuralı şu hata mesajını üretecektir:

```
The credit card number field is required when payment type is credit card.
```


Mevcut Doğrulama Kuralları  
Aşağıda mevcut tüm doğrulama kuralları ve işlevleri listelenmiştir:

---

### **Booleans**
- **Accepted**  
- **Accepted If**  
- **Boolean**  
- **Declined**  
- **Declined If**

### **Strings**
- **Active URL**  
- **Alpha**  
- **Alpha Dash**  
- **Alpha Numeric**  
- **Ascii**  
- **Confirmed**  
- **Current Password**  
- **Different**  
- **Doesnt Start With**  
- **Doesnt End With**  
- **Email**  
- **Ends With**  
- **Enum**  
- **Hex Color**  
- **In**  
- **IP Address**  
- **JSON**  
- **Lowercase**  
- **MAC Address**  
- **Max**  
- **Min**  
- **Not In**  
- **Regular Expression**  
- **Not Regular Expression**  
- **Same**  
- **Size**  
- **Starts With**  
- **String**  
- **Uppercase**  
- **URL**  
- **ULID**  
- **UUID**

### **Numbers**
- **Between**  
- **Decimal**  
- **Different**  
- **Digits**  
- **Digits Between**  
- **Greater Than**  
- **Greater Than Or Equal**  
- **Integer**  
- **Less Than**  
- **Less Than Or Equal**  
- **Max**  
- **Max Digits**  
- **Min**  
- **Min Digits**  
- **Multiple Of**  
- **Numeric**  
- **Same**  
- **Size**

### **Arrays**
- **Array**  
- **Between**  
- **Contains**  
- **Doesnt Contain**  
- **Distinct**  
- **In Array**  
- **In Array Keys**  
- **List**  
- **Max**  
- **Min**  
- **Size**

### **Dates**
- **After**  
- **After Or Equal**  
- **Before**  
- **Before Or Equal**  
- **Date**  
- **Date Equals**  
- **Date Format**  
- **Different**  
- **Timezone**

### **Files**
- **Between**  
- **Dimensions**  
- **Extensions**  
- **File**  
- **Image**  
- **Max**  
- **MIME Types**  
- **MIME Type By File Extension**  
- **Size**

### **Database**
- **Exists**  
- **Unique**

### **Utilities**
- **Any Of**  
- **Bail**  
- **Exclude**  
- **Exclude If**  
- **Exclude Unless**  
- **Exclude With**  
- **Exclude Without**  
- **Filled**  
- **Missing**  
- **Missing If**  
- **Missing Unless**  
- **Missing With**  
- **Missing With All**  
- **Nullable**  
- **Present**  
- **Present If**  
- **Present Unless**  
- **Present With**  
- **Present With All**  
- **Prohibited**  
- **Prohibited If**  
- **Prohibited If Accepted**  
- **Prohibited If Declined**  
- **Prohibited Unless**  
- **Prohibits**  
- **Required**  
- **Required If**  
- **Required If Accepted**  
- **Required If Declined**  
- **Required Unless**  
- **Required With**  
- **Required With All**  
- **Required Without**  
- **Required Without All**  
- **Required Array Keys**  
- **Sometimes**

---

### **accepted**
Doğrulama altındaki alan “yes”, “on”, 1, “1”, true veya “true” olmalıdır. Bu, “Hizmet Şartları” kabulü veya benzeri alanları doğrulamak için kullanışlıdır.

---

### **accepted_if:anotherfield,value,...**
Doğrulama altındaki alan, başka bir alan belirtilen değere eşitse “yes”, “on”, 1, “1”, true veya “true” olmalıdır. Bu, “Hizmet Şartları” kabulü veya benzeri koşullu alanları doğrulamak için kullanışlıdır.

---

### **active_url**
Doğrulama altındaki alan, PHP’nin `dns_get_record` fonksiyonuna göre geçerli bir A veya AAAA kaydına sahip olmalıdır. Verilen URL’nin hostname kısmı `parse_url` fonksiyonu ile çıkarılır ve `dns_get_record` fonksiyonuna aktarılır.

---

### **after:date**
Doğrulama altındaki alan, verilen tarihten sonra bir değer olmalıdır. Tarihler `strtotime` PHP fonksiyonuna geçirilir ve geçerli bir `DateTime` örneğine dönüştürülür:

```php
'start_date' => 'required|date|after:tomorrow'
````

Bir tarih dizesi geçirmek yerine, karşılaştırma yapmak için başka bir alan da belirtebilirsiniz:

```php
'finish_date' => 'required|date|after:start_date'
```

Kolaylık olması için, tarih tabanlı kurallar “fluent” date rule builder kullanılarak oluşturulabilir:

```php
use Illuminate\Validation\Rule;

'start_date' => [
    'required',
    Rule::date()->after(today()->addDays(7)),
],
```

`afterToday` ve `todayOrAfter` metodları, sırasıyla “bugünden sonra” veya “bugün veya sonrası” ifadelerini daha akıcı şekilde tanımlamak için kullanılabilir:

```php
'start_date' => [
    'required',
    Rule::date()->afterToday(),
],
```

---

### **after_or_equal:date**

Doğrulama altındaki alan, verilen tarihten sonra veya o tarihe eşit bir değer olmalıdır. Daha fazla bilgi için `after` kuralına bakın.

Fluent tarih kural oluşturucu kullanılarak oluşturulabilir:

```php
use Illuminate\Validation\Rule;

'start_date' => [
    'required',
    Rule::date()->afterOrEqual(today()->addDays(7)),
],
```

---

### **anyOf**

`Rule::anyOf` doğrulama kuralı, doğrulama altındaki alanın verilen doğrulama kurallarından herhangi birini karşılamasını sağlar. Örneğin, aşağıdaki kural `username` alanının ya bir e-posta adresi ya da en az 6 karakter uzunluğunda bir alpha-numeric (tire dahil) dize olmasını doğrular:

```php
use Illuminate\Validation\Rule;

'username' => [
    'required',
    Rule::anyOf([
        ['string', 'email'],
        ['string', 'alpha_dash', 'min:6'],
    ]),
],
```

---

### **alpha**

Doğrulama altındaki alan tamamen Unicode alfabetik karakterlerden (`\p{L}` ve `\p{M}`) oluşmalıdır.

Sadece ASCII aralığındaki karakterlere (a-z, A-Z) izin vermek için `ascii` seçeneğini ekleyebilirsiniz:

```php
'username' => 'alpha:ascii',
```

---

### **alpha_dash**

Doğrulama altındaki alan tamamen Unicode harf, rakam, ASCII tire (-) ve alt çizgi (_) karakterlerinden oluşmalıdır.

Sadece ASCII karakterlerle sınırlandırmak için `ascii` seçeneğini ekleyin:

```php
'username' => 'alpha_dash:ascii',
```

---

### **alpha_num**

Doğrulama altındaki alan tamamen Unicode harf ve rakamlardan oluşmalıdır (`\p{L}`, `\p{M}`, `\p{N}`).

Sadece ASCII karakterlerle sınırlandırmak için `ascii` seçeneğini ekleyin:

```php
'username' => 'alpha_num:ascii',
```

---

### **array**

Doğrulama altındaki alan bir PHP dizisi olmalıdır.

Ek değerler sağlandığında, giriş dizisindeki her anahtar, kurala sağlanan değerler listesinde bulunmalıdır. Aşağıdaki örnekte, `admin` anahtarı geçersizdir çünkü `array` kuralında belirtilen listeye dahil değildir:

```php
use Illuminate\Support\Facades\Validator;

$input = [
    'user' => [
        'name' => 'Taylor Otwell',
        'username' => 'taylorotwell',
        'admin' => true,
    ],
];

Validator::make($input, [
    'user' => 'array:name,username',
]);
```

Genel olarak, dizinizde hangi anahtarların mevcut olmasına izin verildiğini her zaman belirtmelisiniz.

---

### **ascii**

Doğrulama altındaki alan tamamen 7-bit ASCII karakterlerinden oluşmalıdır.

---

### **bail**

Bir alanda ilk doğrulama hatasından sonra o alan için diğer doğrulama kurallarını çalıştırmayı durdurur.

`bail` yalnızca belirli bir alanın doğrulamasını durdururken, `stopOnFirstFailure` metodu tüm alanların doğrulamasını durdurur:

```php
if ($validator->stopOnFirstFailure()->fails()) {
    // ...
}
```

---

### **before:date**

Doğrulama altındaki alan, verilen tarihten önce bir değer olmalıdır. Tarihler, PHP’nin `strtotime` fonksiyonu kullanılarak geçerli bir `DateTime` örneğine dönüştürülür. Ayrıca, `after` kuralında olduğu gibi başka bir alan da karşılaştırma için kullanılabilir.

Fluent date rule builder ile oluşturulabilir:

```php
use Illuminate\Validation\Rule;

'start_date' => [
    'required',
    Rule::date()->before(today()->subDays(7)),
],
```

`beforeToday` ve `todayOrBefore` metodları sırasıyla “bugünden önce” veya “bugün veya öncesi” koşullarını tanımlamak için kullanılabilir:

```php
'start_date' => [
    'required',
    Rule::date()->beforeToday(),
],
```

---

### **before_or_equal:date**

Doğrulama altındaki alan, verilen tarihten önce veya o tarihe eşit olmalıdır. Tarihler `strtotime` fonksiyonu ile `DateTime` örneğine dönüştürülür. Ayrıca başka bir alanın adı da tarih olarak kullanılabilir.

Fluent date rule builder ile oluşturulabilir:

```php
use Illuminate\Validation\Rule;

'start_date' => [
    'required',
    Rule::date()->beforeOrEqual(today()->subDays(7)),
],
```

---

### **between:min,max**

Doğrulama altındaki alanın boyutu verilen `min` ve `max` arasında olmalıdır (dahil). String, sayısal değerler, diziler ve dosyalar `size` kuralıyla aynı şekilde değerlendirilir.

---

### **boolean**

Doğrulama altındaki alan boolean olarak dönüştürülebilir olmalıdır. Kabul edilen değerler: `true`, `false`, `1`, `0`, `"1"`, `"0"`.

Sadece `true` veya `false` değerlerini geçerli saymak için `strict` parametresini kullanabilirsiniz:

```php
'foo' => 'boolean:strict'
```

---

### **confirmed**

Doğrulama altındaki alanın `{field}_confirmation` adlı bir eşleşen alanı olmalıdır. Örneğin, doğrulanan alan `password` ise, `password_confirmation` alanı da mevcut olmalıdır.

Ayrıca özel bir onay alanı ismi de belirtebilirsiniz. Örneğin:

```php
'username' => 'confirmed:repeat_username'
```

Bu durumda, `repeat_username` alanının `username` alanıyla eşleşmesi beklenir.


### **contains:foo,bar,...**
Doğrulama altındaki alan, belirtilen tüm parametre değerlerini içeren bir dizi olmalıdır. Bu kural genellikle bir diziyi implode etmenizi gerektirdiğinden, `Rule::contains` metodu kuralı akıcı bir şekilde oluşturmak için kullanılabilir:

```php
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

Validator::make($data, [
    'roles' => [
        'required',
        'array',
        Rule::contains(['admin', 'editor']),
    ],
]);
````

---

### **doesnt_contain:foo,bar,...**

Doğrulama altındaki alan, belirtilen parametre değerlerinden hiçbirini içermeyen bir dizi olmalıdır. Bu kural genellikle bir diziyi implode etmenizi gerektirdiğinden, `Rule::doesntContain` metodu akıcı bir şekilde oluşturmak için kullanılabilir:

```php
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

Validator::make($data, [
    'roles' => [
        'required',
        'array',
        Rule::doesntContain(['admin', 'editor']),
    ],
]);
```

---

### **current_password**

Doğrulama altındaki alan, kimliği doğrulanmış kullanıcının parolasıyla eşleşmelidir. İlk parametre olarak bir kimlik doğrulama guard’ı belirtebilirsiniz:

```php
'password' => 'current_password:api'
```

---

### **date**

Doğrulama altındaki alan, PHP’nin `strtotime` fonksiyonuna göre geçerli ve göreli olmayan bir tarih olmalıdır.

---

### **date_equals:date**

Doğrulama altındaki alan, verilen tarihe eşit olmalıdır. Tarihler, PHP’nin `strtotime` fonksiyonuna geçirilir ve geçerli bir `DateTime` nesnesine dönüştürülür.

---

### **date_format:format,...**

Doğrulama altındaki alan, belirtilen formatlardan biriyle eşleşmelidir. Bir alanı doğrularken `date` veya `date_format` kurallarından yalnızca birini kullanmalısınız. Bu doğrulama kuralı, PHP’nin `DateTime` sınıfı tarafından desteklenen tüm formatları destekler.

Tarih tabanlı kurallar, “fluent” date rule builder ile oluşturulabilir:

```php
use Illuminate\Validation\Rule;

'start_date' => [
    'required',
    Rule::date()->format('Y-m-d'),
],
```

---

### **decimal:min,max**

Doğrulama altındaki alan sayısal olmalı ve belirtilen sayıda ondalık basamağa sahip olmalıdır:

```php
// Tam olarak iki ondalık basamak içermelidir (örnek: 9.99)
'price' => 'decimal:2'

// 2 ile 4 arasında ondalık basamak içermelidir
'price' => 'decimal:2,4'
```

---

### **declined**

Doğrulama altındaki alan “no”, “off”, 0, “0”, false veya “false” olmalıdır.

---

### **declined_if:anotherfield,value,...**

Doğrulama altındaki alan, başka bir alan belirtilen değere eşitse “no”, “off”, 0, “0”, false veya “false” olmalıdır.

---

### **different:field**

Doğrulama altındaki alan, belirtilen alandan farklı bir değere sahip olmalıdır.

---

### **digits:value**

Doğrulama altındaki tamsayı, tam olarak belirtilen uzunluğa sahip olmalıdır.

---

### **digits_between:min,max**

Doğrulama altındaki tamsayı, belirtilen minimum ve maksimum uzunluk arasında olmalıdır.

---

### **dimensions**

Doğrulama altındaki dosya, belirtilen boyut kısıtlamalarına uyan bir resim olmalıdır:

```php
'avatar' => 'dimensions:min_width=100,min_height=200'
```

Kullanılabilir kısıtlamalar:
`min_width`, `max_width`, `min_height`, `max_height`, `width`, `height`, `ratio`.

Bir oran kısıtlaması, genişliğin yüksekliğe bölünmesi olarak temsil edilmelidir. Bu, 3/2 gibi bir kesirle veya 1.5 gibi bir ondalık değerle belirtilebilir:

```php
'avatar' => 'dimensions:ratio=3/2'
```

Birden fazla argüman gerektirdiği için, bu kuralı akıcı biçimde oluşturmak genellikle daha uygundur:

```php
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

Validator::make($data, [
    'avatar' => [
        'required',
        Rule::dimensions()
            ->maxWidth(1000)
            ->maxHeight(500)
            ->ratio(3 / 2),
    ],
]);
```

---

### **distinct**

Dizi doğrulaması yaparken, doğrulama altındaki alanın yinelenen değerleri olmamalıdır:

```php
'foo.*.id' => 'distinct'
```

Varsayılan olarak, `distinct` gevşek değişken karşılaştırmaları kullanır. Katı karşılaştırmalar yapmak için `strict` parametresini ekleyebilirsiniz:

```php
'foo.*.id' => 'distinct:strict'
```

Büyük/küçük harf farklarını yok saymak için `ignore_case` parametresini ekleyebilirsiniz:

```php
'foo.*.id' => 'distinct:ignore_case'
```

---

### **doesnt_start_with:foo,bar,...**

Doğrulama altındaki alan, belirtilen değerlerden biriyle başlamamalıdır.

---

### **doesnt_end_with:foo,bar,...**

Doğrulama altındaki alan, belirtilen değerlerden biriyle bitmemelidir.

---

### **email**

Doğrulama altındaki alan, bir e-posta adresi biçiminde olmalıdır. Bu kural, e-posta doğrulaması için `egulias/email-validator` paketini kullanır. Varsayılan olarak `RFCValidation` doğrulayıcısı uygulanır, ancak diğer doğrulama stillerini de kullanabilirsiniz:

```php
'email' => 'email:rfc,dns'
```

Yukarıdaki örnek, hem `RFCValidation` hem de `DNSCheckValidation` doğrulamalarını uygular. Kullanabileceğiniz doğrulama stillerinin tam listesi:

* **rfc:** RFCValidation — desteklenen RFC’lere göre doğrulama yapar.
* **strict:** NoRFCWarningsValidation — desteklenen RFC’lere göre doğrulama yapar, ancak uyarılar bulunduğunda (ör. sondaki noktalar) başarısız olur.
* **dns:** DNSCheckValidation — e-posta alan adının geçerli bir MX kaydına sahip olduğunu doğrular.
* **spoof:** SpoofCheckValidation — e-posta adresinde homograf veya yanıltıcı Unicode karakterlerinin bulunmadığını doğrular.
* **filter:** FilterEmailValidation — PHP’nin `filter_var` fonksiyonuna göre doğrulama yapar.
* **filter_unicode:** FilterEmailValidation::unicode() — PHP’nin `filter_var` fonksiyonuna göre doğrular, bazı Unicode karakterlerine izin verir.

E-posta doğrulama kuralları akıcı bir biçimde oluşturulabilir:

```php
use Illuminate\Validation\Rule;

$request->validate([
    'email' => [
        'required',
        Rule::email()
            ->rfcCompliant(strict: false)
            ->validateMxRecord()
            ->preventSpoofing()
    ],
]);
```

**Not:** `dns` ve `spoof` doğrulayıcılarının çalışması için PHP `intl` uzantısı gereklidir.

---

### **ends_with:foo,bar,...**

Doğrulama altındaki alan, belirtilen değerlerden biriyle bitmelidir.

---

### **enum**

`Enum` kuralı, doğrulama altındaki alanın geçerli bir `enum` değeri içerip içermediğini denetleyen sınıf tabanlı bir kuraldır. `Enum` kuralı, yapıcı parametresi olarak enum adını alır. İlkel değerleri doğrularken, destekli bir Enum sınıfı belirtilmelidir:

```php
use App\Enums\ServerStatus;
use Illuminate\Validation\Rule;

$request->validate([
    'status' => [Rule::enum(ServerStatus::class)],
]);
```

Enum kuralının `only` ve `except` metodları, hangi enum değerlerinin geçerli sayılacağını sınırlamak için kullanılabilir:

```php
Rule::enum(ServerStatus::class)
    ->only([ServerStatus::Pending, ServerStatus::Active]);

Rule::enum(ServerStatus::class)
    ->except([ServerStatus::Pending, ServerStatus::Active]);
```

`when` metodu, koşullu olarak Enum kuralını değiştirmek için kullanılabilir:

```php
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

Rule::enum(ServerStatus::class)
    ->when(
        Auth::user()->isAdmin(),
        fn ($rule) => $rule->only(...),
        fn ($rule) => $rule->only(...),
    );
```

---

### **exclude**

Doğrulama altındaki alan, `validate` ve `validated` metotları tarafından döndürülen istek verilerinden hariç tutulur.

---

### **exclude_if:anotherfield,value**

Eğer belirtilen başka bir alan verilen değere eşitse, doğrulama altındaki alan `validate` ve `validated` metotları tarafından döndürülen verilerden hariç tutulur.

Daha karmaşık koşullu dışlama mantıkları için `Rule::excludeIf` kullanılabilir. Bu metot bir boolean veya closure kabul eder. Closure true veya false döndürerek alanın dışlanıp dışlanmayacağını belirler:

```php
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

Validator::make($request->all(), [
    'role_id' => Rule::excludeIf($request->user()->is_admin),
]);

Validator::make($request->all(), [
    'role_id' => Rule::excludeIf(fn () => $request->user()->is_admin),
]);
```

---

### **exclude_unless:anotherfield,value**

Doğrulama altındaki alan, `validate` ve `validated` metotları tarafından döndürülen verilerden hariç tutulur, **ancak** başka bir alan belirtilen değere eşitse hariç tutulmaz. Eğer değer `null` ise (`exclude_unless:name,null`), karşılaştırma alanı null değilse veya istekte bulunmuyorsa dışlanır.

---

### **exclude_with:anotherfield**

Belirtilen başka bir alan mevcutsa, doğrulama altındaki alan `validate` ve `validated` metotları tarafından döndürülen verilerden hariç tutulur.

---

### **exclude_without:anotherfield**

Belirtilen başka bir alan mevcut değilse, doğrulama altındaki alan `validate` ve `validated` metotları tarafından döndürülen verilerden hariç tutulur.

---

### **exists:table,column**

Doğrulama altındaki alan, belirtilen veritabanı tablosunda mevcut olmalıdır.



### **exists:table,column — Temel Kullanım**
```php
'state' => 'exists:states'
````

Eğer sütun (column) belirtilmemişse, alan adı kullanılacaktır. Bu durumda, kural `states` veritabanı tablosunda, `state` sütununda, istekteki `state` alanı ile eşleşen bir kayıt olup olmadığını doğrular.

---

### **Özel Bir Sütun Adı Belirtme**

Doğrulama kuralında kullanılacak veritabanı sütun adını tablo adından sonra belirtebilirsiniz:

```php
'state' => 'exists:states,abbreviation'
```

Bazen `exists` sorgusu için belirli bir veritabanı bağlantısını belirtmeniz gerekebilir. Bunu, bağlantı adını tablo adının önüne ekleyerek yapabilirsiniz:

```php
'email' => 'exists:connection.staff,email'
```

Tablo adını doğrudan belirtmek yerine, kullanılacak tabloyu belirlemek için bir **Eloquent model** de belirtebilirsiniz:

```php
'user_id' => 'exists:App\Models\User,id'
```

---

### **Sorguyu Özelleştirme**

`Rule` sınıfını kullanarak kuralı akıcı bir biçimde tanımlayabilir ve sorguyu özelleştirebilirsiniz. Bu örnekte ayrıca `|` karakteri yerine dizi kullanılarak kurallar belirtilmiştir:

```php
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

Validator::make($data, [
    'email' => [
        'required',
        Rule::exists('staff')->where(function (Builder $query) {
            $query->where('account_id', 1);
        }),
    ],
]);
```

`Rule::exists` metoduna ikinci argüman olarak sütun adını geçirerek, hangi sütunun kullanılacağını belirleyebilirsiniz:

```php
'state' => Rule::exists('states', 'abbreviation'),
```

---

### **Bir Dizi Değerin Veritabanında Bulunduğunu Doğrulama**

Bir dizi değerin veritabanında bulunup bulunmadığını doğrulamak isterseniz, hem `exists` hem de `array` kurallarını birlikte kullanabilirsiniz:

```php
'states' => ['array', Rule::exists('states', 'abbreviation')],
```

Bu iki kural birlikte kullanıldığında, Laravel otomatik olarak belirtilen tablodaki tüm değerlerin var olup olmadığını tek bir sorgu ile kontrol eder.

---

### **extensions:foo,bar,...**

Doğrulama altındaki dosya, listelenen uzantılardan birine sahip olmalıdır:

```php
'photo' => ['required', 'extensions:jpg,png'],
```

> ⚠️ Kullanıcının belirttiği uzantıya göre doğrulama yapmak güvenilir değildir. Bu kural her zaman `mimes` veya `mimetypes` kuralları ile birlikte kullanılmalıdır.

---

### **file**

Doğrulama altındaki alan, başarıyla yüklenmiş bir dosya olmalıdır.

---

### **filled**

Doğrulama altındaki alan mevcutsa boş olmamalıdır.

---

### **gt:field**

Doğrulama altındaki alan, verilen alan veya değerden **büyük** olmalıdır. İki alan aynı türde olmalıdır. Karakter dizeleri, sayılar, diziler ve dosyalar `size` kuralı ile aynı şekilde değerlendirilir.

---

### **gte:field**

Doğrulama altındaki alan, verilen alan veya değerden **büyük veya eşit** olmalıdır. İki alan aynı türde olmalıdır.

---

### **hex_color**

Doğrulama altındaki alan, geçerli bir onaltılık (hexadecimal) renk değeri içermelidir.

---

### **image**

Doğrulama altındaki dosya bir resim olmalıdır (`jpg`, `jpeg`, `png`, `bmp`, `gif` veya `webp`).

Varsayılan olarak, XSS güvenlik açıkları nedeniyle SVG dosyalarına izin verilmez. SVG dosyalarına izin vermek için `allow_svg` yönergesini kullanabilirsiniz:

```php
'image:allow_svg'
```

---

### **in:foo,bar,...**

Doğrulama altındaki alan, verilen değerlerden biriyle eşleşmelidir. Bu kural genellikle bir diziyle çalıştığından, `Rule::in` metodu akıcı bir şekilde kullanılabilir:

```php
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

Validator::make($data, [
    'zones' => [
        'required',
        Rule::in(['first-zone', 'second-zone']),
    ],
]);
```

`in` kuralı `array` kuralı ile birlikte kullanıldığında, giriş dizisindeki her değer, `in` kuralına sağlanan değerler listesinde yer almalıdır. Aşağıdaki örnekte `LAS` geçersizdir çünkü `in` kuralında belirtilmemiştir:

```php
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

$input = [
    'airports' => ['NYC', 'LAS'],
];

Validator::make($input, [
    'airports' => [
        'required',
        'array',
    ],
    'airports.*' => Rule::in(['NYC', 'LIT']),
]);
```

---

### **in_array:anotherfield.***

Doğrulama altındaki alan, belirtilen başka bir alanın değerleri arasında bulunmalıdır.

---

### **in_array_keys:value.***

Doğrulama altındaki alan, belirtilen değerlerden en az birine sahip bir anahtarı bulunan bir dizi olmalıdır:

```php
'config' => 'array|in_array_keys:timezone'
```

---

### **integer**

Doğrulama altındaki alan bir tamsayı olmalıdır.

`strict` parametresini kullanarak yalnızca **gerçek tamsayı türlerini** kabul edebilirsiniz. Tamsayı biçimindeki string’ler geçersiz sayılır:

```php
'age' => 'integer:strict'
```

> Bu kural yalnızca değerin PHP’nin `FILTER_VALIDATE_INT` kuralına göre geçerli olup olmadığını kontrol eder. Gerçek sayısal değer doğrulaması için `numeric` kuralıyla birlikte kullanılmalıdır.

---

### **ip**

Doğrulama altındaki alan, geçerli bir IP adresi olmalıdır.

---

### **ipv4**

Doğrulama altındaki alan, geçerli bir IPv4 adresi olmalıdır.

---

### **ipv6**

Doğrulama altındaki alan, geçerli bir IPv6 adresi olmalıdır.

---

### **json**

Doğrulama altındaki alan, geçerli bir JSON dizesi olmalıdır.

---

### **lt:field**

Doğrulama altındaki alan, verilen alandan **küçük** olmalıdır. İki alan aynı türde olmalıdır.

---

### **lte:field**

Doğrulama altındaki alan, verilen alandan **küçük veya eşit** olmalıdır. İki alan aynı türde olmalıdır.

---

### **lowercase**

Doğrulama altındaki alan tamamen küçük harflerden oluşmalıdır.

---

### **list**

Doğrulama altındaki alan bir dizi olmalı ve **liste** biçiminde olmalıdır. Bir dizi, anahtarları 0’dan başlayıp ardışık sayılardan oluşuyorsa liste olarak kabul edilir.

---

### **mac_address**

Doğrulama altındaki alan, geçerli bir MAC adresi olmalıdır.

---

### **max:value**

Doğrulama altındaki alan, belirtilen maksimum değerden büyük olmamalıdır. Karakter dizeleri, sayılar, diziler ve dosyalar `size` kuralı ile aynı şekilde değerlendirilir.

---

### **max_digits:value**

Doğrulama altındaki tamsayı, en fazla belirtilen basamak uzunluğuna sahip olmalıdır.

---

### **mimetypes:text/plain,...**

Doğrulama altındaki dosya, belirtilen MIME türlerinden biriyle eşleşmelidir:

```php
'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime'
```

MIME türü, dosya içeriği okunarak tahmin edilir ve istemcinin gönderdiği MIME türünden farklı olabilir.

---

### **mimes:foo,bar,...**

Doğrulama altındaki dosya, belirtilen uzantılardan birine karşılık gelen MIME türüne sahip olmalıdır:

```php
'photo' => 'mimes:jpg,bmp,png'
```

Bu kural, yalnızca uzantılara bakmak yerine dosya içeriğini okuyarak MIME türünü tahmin eder. MIME türlerinin ve uzantılarının tam listesi şu adreste bulunabilir:
[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

> Bu kural, MIME türü ile dosya uzantısının eşleştiğini doğrulamaz. Örneğin, `mimes:png` kuralı, geçerli PNG içeriğine sahip `photo.txt` adlı bir dosyayı da geçerli sayar. Kullanıcının belirttiği uzantıyı doğrulamak istiyorsanız `extensions` kuralını kullanın.

---

### **min:value**

Doğrulama altındaki alan, belirtilen minimum değerden küçük olmamalıdır.

---

### **min_digits:value**

Doğrulama altındaki tamsayı, belirtilen minimum basamak uzunluğuna sahip olmalıdır.

---

### **multiple_of:value**

Doğrulama altındaki alan, belirtilen değerin bir katı olmalıdır.

---

### **missing**

Doğrulama altındaki alan, giriş verilerinde **bulunmamalıdır**.

---

### **missing_if:anotherfield,value,...**

Eğer başka bir alan belirtilen değere eşitse, doğrulama altındaki alan giriş verilerinde bulunmamalıdır.

---

### **missing_unless:anotherfield,value**

Belirtilen başka bir alan verilen değere eşit olmadığı sürece doğrulama altındaki alan girişte bulunmamalıdır.

---

### **missing_with:foo,bar,...**

Belirtilen diğer alanlardan herhangi biri mevcutsa, doğrulama altındaki alan **bulunmamalıdır.**

---

### **missing_with_all:foo,bar,...**

Belirtilen tüm diğer alanlar mevcutsa, doğrulama altındaki alan **bulunmamalıdır.**

---

### **not_in:foo,bar,...**

Doğrulama altındaki alan, belirtilen değerler listesinde **olmamalıdır**. `Rule::notIn` metodu akıcı biçimde kullanılabilir:

```php
use Illuminate\Validation\Rule;

Validator::make($data, [
    'toppings' => [
        'required',
        Rule::notIn(['sprinkles', 'cherries']),
    ],
]);
```

---

### **not_regex:pattern**

Doğrulama altındaki alan, verilen düzenli ifadeyle **eşleşmemelidir.**

```php
'email' => 'not_regex:/^.+$/i'
```

`preg_match` fonksiyonu kullanılır. Düzenli ifadede `|` karakteri varsa, kural dizi biçiminde tanımlanmalıdır.

---

### **nullable**

Doğrulama altındaki alan `null` olabilir.

---

### **numeric**

Doğrulama altındaki alan sayısal olmalıdır.

`strict` parametresi kullanılırsa, yalnızca integer veya float türleri geçerli kabul edilir. Sayısal string’ler geçersiz sayılır:

```php
'amount' => 'numeric:strict'
```

---

### **present**

Doğrulama altındaki alan giriş verilerinde mevcut olmalıdır.

---

### **present_if:anotherfield,value,...**

Başka bir alan belirtilen değere eşitse, doğrulama altındaki alan mevcut olmalıdır.

---

### **present_unless:anotherfield,value**

Belirtilen başka bir alan belirtilen değere eşit değilse, doğrulama altındaki alan mevcut olmalıdır.

---

### **present_with:foo,bar,...**

Belirtilen diğer alanlardan herhangi biri mevcutsa, doğrulama altındaki alan da mevcut olmalıdır.

---

### **present_with_all:foo,bar,...**

Belirtilen tüm diğer alanlar mevcutsa, doğrulama altındaki alan da mevcut olmalıdır.

---

### **prohibited**

Doğrulama altındaki alan mevcut **olmamalı** veya **boş** olmalıdır.
Bir alan aşağıdaki durumlardan birini sağlıyorsa “boş” kabul edilir:

* Değer `null`
* Değer boş string
* Değer boş dizi veya `Countable` nesne
* Değer, yolu olmayan yüklenmiş bir dosya

---

### **prohibited_if:anotherfield,value,...**

Belirtilen başka bir alan, verilen değerlerden birine eşitse doğrulama altındaki alan mevcut **olmamalı veya boş** olmalıdır.

---

### **prohibited_if_accepted:anotherfield,...**

Belirtilen başka bir alan `yes`, `on`, `1`, `true` gibi değerlerden birine eşitse doğrulama altındaki alan **boş** olmalıdır.

---

### **prohibited_if_declined:anotherfield,...**

Belirtilen başka bir alan `no`, `off`, `0`, `false` gibi değerlerden birine eşitse doğrulama altındaki alan **boş** olmalıdır.

---

### **prohibited_unless:anotherfield,value,...**

Belirtilen başka bir alan verilen değerlerden birine eşit olmadığı sürece doğrulama altındaki alan **boş** veya **mevcut olmamalıdır.**

---

### **prohibits:anotherfield,...**

Eğer doğrulama altındaki alan **mevcut ve boş değilse**, belirtilen diğer alanlar **boş veya mevcut olmamalıdır.**

---

### **regex:pattern**

Doğrulama altındaki alan, belirtilen düzenli ifadeyle **eşleşmelidir.**

```php
'email' => 'regex:/^.+@.+$/i'
```

`preg_match` fonksiyonu kullanılır. Düzenli ifadede `|` karakteri varsa, kural dizi biçiminde tanımlanmalıdır.

---

### **required**

Doğrulama altındaki alan, giriş verilerinde mevcut olmalı ve **boş olmamalıdır.**
Bir alan aşağıdaki durumlarda “boş” kabul edilir:

* Değer `null`
* Değer boş string
* Değer boş dizi veya `Countable` nesne
* Değer yolu olmayan yüklenmiş bir dosya

---

### **required_if:anotherfield,value,...**

Belirtilen başka bir alan verilen değerlere eşitse, doğrulama altındaki alan mevcut olmalı ve boş olmamalıdır.

Daha karmaşık koşullar için `Rule::requiredIf` kullanılabilir:

```php
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

Validator::make($request->all(), [
    'role_id' => Rule::requiredIf($request->user()->is_admin),
]);
```

---

### **required_if_accepted:anotherfield,...**

Belirtilen başka bir alan “yes”, “on”, “1”, “true” gibi bir değere sahipse doğrulama altındaki alan **gerekli** olur.

---

### **required_if_declined:anotherfield,...**

Belirtilen başka bir alan “no”, “off”, “0”, “false” gibi bir değere sahipse doğrulama altındaki alan **gerekli** olur.

---

### **required_unless:anotherfield,value,...**

Belirtilen başka bir alan verilen değerlere eşit olmadığı sürece doğrulama altındaki alan **gerekli** olur.
Bu ayrıca, karşılaştırma yapılan alanın da mevcut olması gerektiği anlamına gelir.

---

### **required_with:foo,bar,...**

Belirtilen diğer alanlardan **herhangi biri** mevcut ve boş değilse, doğrulama altındaki alan da mevcut ve boş olmamalıdır.

---

### **required_with_all:foo,bar,...**

Belirtilen **tüm** diğer alanlar mevcut ve boş değilse, doğrulama altındaki alan da mevcut ve boş olmamalıdır.

---

### **required_without:foo,bar,...**

Belirtilen diğer alanlardan **herhangi biri** boş veya mevcut değilse, doğrulama altındaki alan **gerekli** olur.

---

### **required_without_all:foo,bar,...**

Belirtilen **tüm** diğer alanlar boş veya mevcut değilse, doğrulama altındaki alan **gerekli** olur.

---

### **required_array_keys:foo,bar,...**

Doğrulama altındaki alan bir dizi olmalı ve belirtilen anahtarlara sahip olmalıdır.

---

### **same:field**

Verilen alan, doğrulama altındaki alanla aynı değere sahip olmalıdır.

---

### **size:value**

Doğrulama altındaki alanın boyutu belirtilen değere eşit olmalıdır.

* **String:** karakter sayısı
* **Numeric:** verilen tam sayı değeri
* **Array:** öğe sayısı
* **File:** kilobayt cinsinden dosya boyutu

Örnekler:

```php
// String tam olarak 12 karakter olmalı
'title' => 'size:12';

// Sayısal değer tam olarak 10 olmalı
'seats' => 'integer|size:10';

// Dizi tam olarak 5 öğeye sahip olmalı
'tags' => 'array|size:5';

// Dosya tam olarak 512 KB olmalı
'image' => 'file|size:512';
```


### **starts_with:foo,bar,...**
Doğrulama altındaki alan, belirtilen değerlerden biriyle başlamalıdır.

---

### **string**
Doğrulama altındaki alan bir **string (metin)** olmalıdır.  
Alan `null` değeri alabiliyorsa, `nullable` kuralını da eklemelisiniz.

---

### **timezone**
Doğrulama altındaki alan, PHP’nin `DateTimeZone::listIdentifiers` metoduna göre geçerli bir **zaman dilimi kimliği** olmalıdır.

Bu metoda verilen argümanlar doğrulama kuralına da aktarılabilir:

```php
'timezone' => 'required|timezone:all';

'timezone' => 'required|timezone:Africa';

'timezone' => 'required|timezone:per_country,US';
````

---

### **unique:table,column**

Doğrulama altındaki alan, belirtilen veritabanı tablosunda **benzersiz** olmalıdır.

#### **Özel Tablo / Sütun Adı Belirtme**

Tablo adını doğrudan belirtmek yerine, kullanılacak tabloyu belirlemek için **Eloquent model** sınıfını kullanabilirsiniz:

```php
'email' => 'unique:App\Models\User,email_address'
```

Sütun adı belirtilmezse, alanın ismi sütun adı olarak kullanılır:

```php
'email' => 'unique:users,email_address'
```

#### **Özel Veritabanı Bağlantısı Kullanma**

Bazen `unique` doğrulaması için özel bir veritabanı bağlantısı kullanmanız gerekebilir. Bunun için bağlantı adını tablo adının önüne ekleyebilirsiniz:

```php
'email' => 'unique:connection.users,email_address'
```

---

### **Belirli Bir ID’nin Yok Sayılmasını Sağlama**

Bazen, güncelleme işlemlerinde mevcut kaydı doğrulamadan hariç tutmak isteyebilirsiniz.
Örneğin, bir kullanıcı profilini güncellerken, aynı e-posta adresine sahip kendi kaydının doğrulama hatası oluşturmasını istemezsiniz.

Bunun için `Rule` sınıfını kullanarak `ignore` metodunu çağırabilirsiniz:

```php
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

Validator::make($data, [
    'email' => [
        'required',
        Rule::unique('users')->ignore($user->id),
    ],
]);
```

> ⚠️ **Güvenlik Notu:**
> `ignore` metoduna **kullanıcıdan gelen herhangi bir değer** aktarılmamalıdır.
> Yalnızca sistem tarafından oluşturulan benzersiz ID’ler (ör. otomatik artan ID veya UUID) kullanılmalıdır.
> Aksi takdirde uygulamanız **SQL injection** saldırılarına karşı savunmasız hale gelir.

Model örneğini doğrudan geçirerek de Laravel’in anahtar değerini otomatik çıkarmasını sağlayabilirsiniz:

```php
Rule::unique('users')->ignore($user)
```

Eğer tablo, `id` dışında farklı bir birincil anahtar kullanıyorsa, `ignore` metoduna sütun adını da belirtebilirsiniz:

```php
Rule::unique('users')->ignore($user->id, 'user_id')
```

Varsayılan olarak `unique` kuralı, doğrulanan alanın adına karşılık gelen sütunu kontrol eder.
Farklı bir sütunu denetlemek isterseniz, `unique` metoduna ikinci argüman olarak geçebilirsiniz:

```php
Rule::unique('users', 'email_address')->ignore($user->id)
```

---

### **Ek Sorgu Koşulları Ekleme**

Ek `where` koşulları ekleyerek sorguyu özelleştirebilirsiniz.
Örneğin, yalnızca `account_id` sütunu 1 olan kayıtları kontrol etmek istiyorsanız:

```php
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

'email' => Rule::unique('users')->where(fn (Builder $query) => $query->where('account_id', 1))
```

---

### **Soft Delete (Yumuşak Silme) Kayıtlarını Hariç Tutma**

Varsayılan olarak `unique` kuralı, **soft deleted (yumuşak silinmiş)** kayıtları da kontrol eder.
Bunları hariç tutmak için `withoutTrashed` metodunu kullanabilirsiniz:

```php
Rule::unique('users')->withoutTrashed();
```

Eğer modeliniz `deleted_at` dışında farklı bir sütun kullanıyorsa, bu sütun adını belirtebilirsiniz:

```php
Rule::unique('users')->withoutTrashed('was_deleted_at');
```

---

### **uppercase**

Doğrulama altındaki alan tamamen **büyük harflerden** oluşmalıdır.

---

### **url**

Doğrulama altındaki alan, geçerli bir **URL** olmalıdır.

Geçerli sayılacak URL protokollerini belirtebilirsiniz:

```php
'url' => 'url:http,https',

'game' => 'url:minecraft,steam',
```

---

### **ulid**

Doğrulama altındaki alan, geçerli bir **ULID (Universally Unique Lexicographically Sortable Identifier)** olmalıdır.

---

### **uuid**

Doğrulama altındaki alan, **RFC 9562** standardına uygun bir **UUID (v1, v3, v4, v5, v6, v7, veya v8)** olmalıdır.

Belirli bir sürümü doğrulamak isterseniz:

```php
'uuid' => 'uuid:4'
```

---

## **Koşullu Kurallar Ekleme (Conditionally Adding Rules)**

### **Belirli Değerlerde Doğrulamayı Atlama**

Bazen, başka bir alan belirli bir değere sahipse, belirli alanların doğrulanmasını istemeyebilirsiniz.
Bunu `exclude_if` kuralıyla yapabilirsiniz:

```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make($data, [
    'has_appointment' => 'required|boolean',
    'appointment_date' => 'exclude_if:has_appointment,false|required|date',
    'doctor_name' => 'exclude_if:has_appointment,false|required|string',
]);
```

Alternatif olarak, `exclude_unless` kuralını kullanabilirsiniz.
Bu durumda, başka bir alan belirli bir değere **sahip olmadıkça** doğrulama yapılmaz:

```php
$validator = Validator::make($data, [
    'has_appointment' => 'required|boolean',
    'appointment_date' => 'exclude_unless:has_appointment,true|required|date',
    'doctor_name' => 'exclude_unless:has_appointment,true|required|string',
]);
```

---

### **Alan Mevcut Olduğunda Doğrulama (sometimes)**

Bazı durumlarda, bir alan yalnızca mevcutsa doğrulanmalıdır.
Bunu hızlıca yapmak için `sometimes` kuralını kullanabilirsiniz:

```php
$validator = Validator::make($data, [
    'email' => 'sometimes|required|email',
]);
```

Yukarıdaki örnekte, `email` alanı yalnızca `$data` dizisinde mevcutsa doğrulanacaktır.

---

### **Karmaşık Koşullu Doğrulama**

Daha karmaşık doğrulama koşulları gerektiğinde, `sometimes` metodu kullanılabilir.
Örneğin, başka bir alanın değeri 100’den büyükse başka bir alanı zorunlu yapmak:

```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make($request->all(), [
    'email' => 'required|email',
    'games' => 'required|integer|min:0',
]);

use Illuminate\Support\Fluent;

$validator->sometimes('reason', 'required|max:500', function (Fluent $input) {
    return $input->games >= 100;
});
```

Aynı anda birden fazla alan için koşullu doğrulama ekleyebilirsiniz:

```php
$validator->sometimes(['reason', 'cost'], 'required', function (Fluent $input) {
    return $input->games >= 100;
});
```

`$input` parametresi, `Illuminate\Support\Fluent` örneğidir ve doğrulanan verilere erişmenizi sağlar.

---

### **Karmaşık Koşullu Dizi Doğrulama**

Dizi içindeki bilinmeyen bir indekse göre doğrulama yapmanız gerektiğinde, closure ikinci argüman olarak mevcut öğeyi alabilir:

```php
$input = [
    'channels' => [
        ['type' => 'email', 'address' => 'abigail@example.com'],
        ['type' => 'url', 'address' => 'https://example.com'],
    ],
];

$validator->sometimes('channels.*.address', 'email', function (Fluent $input, Fluent $item) {
    return $item->type === 'email';
});

$validator->sometimes('channels.*.address', 'url', function (Fluent $input, Fluent $item) {
    return $item->type !== 'email';
});
```

---

### **Dizileri Doğrulama**

`array` kuralı, izin verilen anahtarların listesini kabul eder.
Dizide belirtilmeyen ek anahtarlar varsa doğrulama başarısız olur:

```php
use Illuminate\Support\Facades\Validator;

$input = [
    'user' => [
        'name' => 'Taylor Otwell',
        'username' => 'taylorotwell',
        'admin' => true,
    ],
];

Validator::make($input, [
    'user' => 'array:name,username',
]);
```

---

### **İç İçe Dizi Girdilerini Doğrulama**

İç içe dizilerdeki alanları “nokta notasyonu” ile doğrulayabilirsiniz:

```php
use Illuminate\Support\Facades\Validator;

$validator = Validator::make($request->all(), [
    'photos.profile' => 'required|image',
]);
```

Dizideki her öğeyi ayrı ayrı doğrulamak için `*` karakterini kullanabilirsiniz:

```php
$validator = Validator::make($request->all(), [
    'users.*.email' => 'email|unique:users',
    'users.*.first_name' => 'required_with:users.*.last_name',
]);
```

Dil dosyalarında `*` kullanarak dizi temelli alanlar için özel mesajlar tanımlayabilirsiniz:

```php
'custom' => [
    'users.*.email' => [
        'unique' => 'Each user must have a unique email address',
    ]
],
```

---

### **İç İçe Dizi Verilerine Erişim**

Bir dizi alt öğesinin değerine erişmeniz gerekiyorsa, `Rule::forEach` metodunu kullanabilirsiniz:

```php
use App\Rules\HasPermission;
use Illuminate\Validation\Rule;

$validator = Validator::make($request->all(), [
    'companies.*.id' => Rule::forEach(function (string|null $value, string $attribute) {
        return [
            Rule::exists(Company::class, 'id'),
            new HasPermission('manage-company', $value),
        ];
    }),
]);
```

---

### **Hata Mesajlarında Dizi İndekslerini Gösterme**

Diziler doğrulanırken, hata mesajlarında indeks veya sıralama numarasını göstermek isteyebilirsiniz.
Bunun için `:index`, `:position` veya `:ordinal-position` yer tutucularını kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Validator;

$input = [
    'photos' => [
        ['name' => 'BeachVacation.jpg', 'description' => 'A photo of my beach vacation!'],
        ['name' => 'GrandCanyon.jpg', 'description' => ''],
    ],
];

Validator::validate($input, [
    'photos.*.description' => 'required',
], [
    'photos.*.description.required' => 'Please describe photo #:position.',
]);
```

Yukarıdaki örnekte hata mesajı şu şekilde olur:

> “Please describe photo #2.”

Daha derin iç içe dizilerde `:second-position`, `:third-index` gibi yer tutucular kullanılabilir.

---

### **Dosya Doğrulama (File Validation)**

Laravel, dosyaları doğrulamak için çeşitli kurallar sağlar (`mimes`, `image`, `min`, `max`).
Ancak bunları tek tek yazmak yerine, akıcı bir şekilde `File` sınıfını kullanabilirsiniz:

```php
use Illuminate\Validation\Rules\File;

Validator::validate($input, [
    'attachment' => [
        'required',
        File::types(['mp3', 'wav'])
            ->min(1024)
            ->max(12 * 1024),
    ],
]);
```


### **Dosya Türlerini Doğrulama**
`types` metodunu çağırırken yalnızca uzantıları belirtmeniz gerekse de, bu metod aslında dosyanın içeriğini okuyarak MIME türünü tahmin eder ve doğrulama işlemini MIME türüne göre yapar.  
MIME türlerinin ve uzantılarının tam listesini aşağıdaki bağlantıdan bulabilirsiniz:  
🔗 [https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

---

### **Dosya Boyutlarını Doğrulama**
Kolaylık olması açısından, minimum ve maksimum dosya boyutlarını birim belirten bir string olarak tanımlayabilirsiniz.  
Desteklenen birimler: `kb`, `mb`, `gb`, `tb`

```php
File::types(['mp3', 'wav'])
    ->min('1kb')
    ->max('10mb');
````

---

### **Görsel Dosyaları Doğrulama**

Uygulamanız kullanıcılar tarafından yüklenen görselleri kabul ediyorsa, dosyanın bir **resim** (`jpg`, `jpeg`, `png`, `bmp`, `gif`, `webp`) olduğunu doğrulamak için `File::image()` metodunu kullanabilirsiniz.

Ayrıca, `dimensions` kuralı ile görselin boyutlarını da sınırlayabilirsiniz:

```php
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

Validator::validate($input, [
    'photo' => [
        'required',
        File::image()
            ->min(1024)
            ->max(12 * 1024)
            ->dimensions(Rule::dimensions()->maxWidth(1000)->maxHeight(500)),
    ],
]);
```

Görsel boyutlarını doğrulama hakkında daha fazla bilgi için **`dimensions`** kuralı dokümantasyonuna bakabilirsiniz.

Varsayılan olarak, **SVG dosyaları** XSS güvenlik risklerinden dolayı kabul edilmez.
SVG dosyalarına izin vermek isterseniz, `allowSvg: true` parametresini geçebilirsiniz:

```php
File::image(allowSvg: true)
```

---

### **Görsel Boyutlarını Doğrulama**

Bir görselin belirli piksel değerlerinde olmasını istiyorsanız, `dimensions` kuralını kullanabilirsiniz.
Örneğin, bir görselin **en az 1000 piksel genişliğinde** ve **500 piksel yüksekliğinde** olmasını sağlamak:

```php
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

File::image()->dimensions(
    Rule::dimensions()
        ->maxWidth(1000)
        ->maxHeight(500)
);
```

---

### **Şifreleri Doğrulama**

Şifrelerin yeterli karmaşıklığa sahip olduğundan emin olmak için Laravel’in `Password` kural nesnesini kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

$validator = Validator::make($request->all(), [
    'password' => ['required', 'confirmed', Password::min(8)],
]);
```

`Password` nesnesi, şifre karmaşıklığını kolayca özelleştirmenizi sağlar:

```php
// En az 8 karakter
Password::min(8)

// En az bir harf içermeli
Password::min(8)->letters()

// En az bir büyük ve bir küçük harf içermeli
Password::min(8)->mixedCase()

// En az bir rakam içermeli
Password::min(8)->numbers()

// En az bir sembol içermeli
Password::min(8)->symbols()
```

Ayrıca, şifrenin veri ihlallerinde sızdırılmadığını doğrulamak için `uncompromised` metodunu kullanabilirsiniz:

```php
Password::min(8)->uncompromised()
```

Bu doğrulama, **haveibeenpwned.com** servisini kullanarak, şifrelerin gizliliğini koruyan **k-Anonymity** modeline göre çalışır.

Varsayılan olarak, şifre bir veri sızıntısında **en az bir kez** görünmüşse geçersiz sayılır.
Bu eşiği değiştirmek için `uncompromised` metoduna bir argüman verebilirsiniz:

```php
// Aynı sızıntıda 3 kereden az görünmüşse geçerli
Password::min(8)->uncompromised(3);
```

Tüm gereksinimleri zincirleyebilirsiniz:

```php
Password::min(8)
    ->letters()
    ->mixedCase()
    ->numbers()
    ->symbols()
    ->uncompromised()
```

---

### **Varsayılan Şifre Kurallarını Tanımlama**

Tüm uygulamanızda varsayılan şifre doğrulama kurallarını tek bir yerde tanımlamak isteyebilirsiniz.
Bunu `Password::defaults` metoduyla yapabilirsiniz. Bu metod, bir closure alır ve bu closure varsayılan kuralı döndürür.

```php
use Illuminate\Validation\Rules\Password;

/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Password::defaults(function () {
        $rule = Password::min(8);

        return $this->app->isProduction()
            ? $rule->mixedCase()->uncompromised()
            : $rule;
    });
}
```

Daha sonra bu varsayılan kuralı doğrulamada şu şekilde kullanabilirsiniz:

```php
'password' => ['required', Password::defaults()],
```

Varsayılan kurallara ek olarak özel kurallar eklemek isterseniz `rules` metodunu kullanabilirsiniz:

```php
use App\Rules\ZxcvbnRule;

Password::defaults(function () {
    $rule = Password::min(8)->rules([new ZxcvbnRule]);

    // ...
});
```

---

## **Özel Doğrulama Kuralları**

### **Kural Nesnelerini Kullanma**

Laravel birçok yerleşik doğrulama kuralı sağlar; ancak bazen kendi özel kuralınızı yazmak isteyebilirsiniz.
Yeni bir kural nesnesi oluşturmak için Artisan komutunu kullanabilirsiniz:

```bash
php artisan make:rule Uppercase
```

Laravel, bu kuralı `app/Rules` dizininde oluşturacaktır (dizin yoksa otomatik oluşturulur).

Oluşturulan sınıfın içeriği aşağıdaki gibidir:

```php
<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Uppercase implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strtoupper($value) !== $value) {
            $fail('The :attribute must be uppercase.');
        }
    }
}
```

Bu kuralı doğrulamada kullanmak için sınıf örneğini diğer kurallarla birlikte iletebilirsiniz:

```php
use App\Rules\Uppercase;

$request->validate([
    'name' => ['required', 'string', new Uppercase],
]);
```

---

### **Doğrulama Mesajlarını Çevirme**

Hata mesajını doğrudan yazmak yerine, çeviri anahtarlarını kullanabilirsiniz:

```php
if (strtoupper($value) !== $value) {
    $fail('validation.uppercase')->translate();
}
```

Yer tutucular ve dil seçeneği de ekleyebilirsiniz:

```php
$fail('validation.location')->translate([
    'value' => $this->value,
], 'fr');
```

---

### **Ek Verilere Erişim**

Eğer özel doğrulama sınıfınız doğrulanan diğer verilere de erişmek istiyorsa,
`Illuminate\Contracts\Validation\DataAwareRule` arayüzünü uygulayabilirsiniz.

```php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class Uppercase implements DataAwareRule, ValidationRule
{
    /**
     * Doğrulanan tüm veriler.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Verileri ayarla.
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
```

Eğer kuralınız doğrulayıcı (validator) nesnesine erişmek istiyorsa,
`ValidatorAwareRule` arayüzünü uygulayabilirsiniz:

```php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Validation\Validator;

class Uppercase implements ValidationRule, ValidatorAwareRule
{
    /**
     * Doğrulayıcı örneği.
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Aktif doğrulayıcıyı ayarla.
     */
    public function setValidator(Validator $validator): static
    {
        $this->validator = $validator;

        return $this;
    }
}
```

---

### **Closure Kullanarak Doğrulama**

Bir özel kuralı yalnızca bir kez kullanacaksanız, sınıf oluşturmak yerine **closure** kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Validator;
use Closure;

$validator = Validator::make($request->all(), [
    'title' => [
        'required',
        'max:255',
        function (string $attribute, mixed $value, Closure $fail) {
            if ($value === 'foo') {
                $fail("The {$attribute} is invalid.");
            }
        },
    ],
]);
```

---

### **Implicit (Dolaylı) Kurallar**

Varsayılan olarak, bir alan mevcut değilse veya boşsa, doğrulama kuralları çalıştırılmaz.
Örneğin, `unique` kuralı boş bir string üzerinde çalışmaz:

```php
use Illuminate\Support\Facades\Validator;

$rules = ['name' => 'unique:users,name'];

$input = ['name' => ''];

Validator::make($input, $rules)->passes(); // true
```

Bir özel kuralın, alan boş olsa bile çalışmasını istiyorsanız, **implicit (örtük)** bir kural olarak tanımlamanız gerekir.
Bunu yapmak için Artisan komutuna `--implicit` seçeneğini ekleyin:

```bash
php artisan make:rule Uppercase --implicit
```

> 🔸 “Implicit” bir kural, yalnızca alanın “gerekli” olduğunu ima eder.
> Alanın gerçekten geçersiz olup olmadığını belirlemek size kalmıştır.


