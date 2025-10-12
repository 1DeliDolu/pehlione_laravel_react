# CSRF Koruması

## Giriş

Cross-site request forgery (CSRF), kimliği doğrulanmış bir kullanıcı adına yetkisiz komutların yürütülmesine olanak tanıyan kötü niyetli bir saldırı türüdür. Neyse ki, Laravel uygulamanızı CSRF saldırılarına karşı korumayı kolaylaştırır.

## Güvenlik Açığının Açıklaması

Cross-site request forgery kavramına aşina değilseniz, bu güvenlik açığının nasıl kullanılabileceğini bir örnekle açıklayalım. Diyelim ki uygulamanızda kimliği doğrulanmış kullanıcının e-posta adresini değiştirmek için bir POST isteğini kabul eden `/user/email` adında bir route var. Büyük olasılıkla, bu route, kullanıcının kullanmak istediği e-posta adresini içeren bir `email` input alanı bekler.

CSRF koruması olmadan, kötü niyetli bir web sitesi uygulamanızın `/user/email` route’una yönlendiren bir HTML formu oluşturabilir ve kendi kötü niyetli e-posta adresini gönderebilir:

```html
<form action="https://your-application.com/user/email" method="POST">
    <input type="email" value="malicious-email@example.com">
</form>
 
<script>
    document.forms[0].submit();
</script>
```

Kötü niyetli site sayfa yüklendiğinde formu otomatik olarak gönderirse, bu kişi sadece uygulamanızın farkında olmayan bir kullanıcısını kendi sitesine yönlendirmesiyle, o kullanıcının e-posta adresi uygulamanızda değiştirilmiş olur.

Bu güvenlik açığını önlemek için, gelen her **POST**, **PUT**, **PATCH** veya **DELETE** isteğini, kötü niyetli uygulamanın erişemeyeceği gizli bir oturum değeriyle kontrol etmemiz gerekir.

## CSRF İsteklerini Önleme

Laravel, uygulama tarafından yönetilen her aktif kullanıcı oturumu için otomatik olarak bir CSRF "token" (jeton) üretir. Bu token, kimliği doğrulanmış kullanıcının gerçekten isteği yapan kişi olduğunu doğrulamak için kullanılır. Bu token, kullanıcının oturumunda saklandığı ve oturum her yenilendiğinde değiştiği için, kötü niyetli bir uygulamanın buna erişmesi mümkün değildir.

Mevcut oturumun CSRF token’ına isteğin oturumu üzerinden veya `csrf_token` helper fonksiyonu ile erişebilirsiniz:

```php
use Illuminate\Http\Request;
 
Route::get('/token', function (Request $request) {
    $token = $request->session()->token();
 
    $token = csrf_token();
 
    // ...
});
```

Uygulamanızda bir **POST**, **PUT**, **PATCH** veya **DELETE** HTML formu tanımladığınızda, formda gizli bir CSRF `_token` alanı eklemelisiniz ki CSRF koruma middleware’i isteği doğrulayabilsin. Kolaylık olması için, gizli token input alanını oluşturmak amacıyla **@csrf** Blade direktifini kullanabilirsiniz:

```html
<form method="POST" action="/profile">
    @csrf
 
    <!-- Eşdeğeri... -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
</form>
```

`Illuminate\Foundation\Http\Middleware\ValidateCsrfToken` middleware’i, varsayılan olarak `web` middleware grubuna dahildir ve isteğin girişindeki token’ın oturumda saklanan token ile eşleşip eşleşmediğini otomatik olarak doğrular. Bu iki token eşleştiğinde, isteği başlatanın gerçekten kimliği doğrulanmış kullanıcı olduğunu biliriz.

## CSRF Token’ları ve SPA’lar

Eğer Laravel’i bir API backend olarak kullanan bir **SPA (Single Page Application)** oluşturuyorsanız, API’nizle kimlik doğrulama ve CSRF güvenlik açıklarına karşı koruma hakkında bilgi almak için Laravel **Sanctum** dokümantasyonuna bakmalısınız.

## CSRF Korumasından Hariç Tutulan URI’ler

Bazen bazı URI’leri CSRF korumasından hariç tutmak isteyebilirsiniz. Örneğin, ödemeleri işlemek için **Stripe** kullanıyorsanız ve Stripe’ın webhook sistemini kullanıyorsanız, Stripe webhook handler route’unuzu CSRF korumasından hariç tutmanız gerekir; çünkü Stripe, route’larınıza hangi CSRF token’ını göndereceğini bilemez.

Genellikle bu tür route’ları, Laravel’in `routes/web.php` dosyasındaki **web middleware** grubunun dışında tanımlamalısınız. Ancak, belirli route’ları hariç tutmak için URI’lerini uygulamanızın `bootstrap/app.php` dosyasındaki `validateCsrfTokens` metoduna verebilirsiniz:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->validateCsrfTokens(except: [
        'stripe/*',
        'http://example.com/foo/bar',
        'http://example.com/foo/*',
    ]);
})
```

Kolaylık olması açısından, testler çalıştırılırken CSRF middleware’i tüm route’lar için otomatik olarak devre dışı bırakılır.

## X-CSRF-TOKEN

CSRF token’ını bir POST parametresi olarak kontrol etmenin yanı sıra, varsayılan olarak **web** middleware grubuna dahil edilen `Illuminate\Foundation\Http\Middleware\ValidateCsrfToken` middleware’i, **X-CSRF-TOKEN** istek başlığını da kontrol eder. Örneğin, token’ı bir HTML meta etiketinde saklayabilirsiniz:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

Daha sonra, **jQuery** gibi bir kütüphaneye, token’ı tüm istek başlıklarına otomatik olarak eklemesini söyleyebilirsiniz. Bu, **AJAX** tabanlı uygulamalarınız için basit ve kullanışlı bir CSRF koruması sağlar:

```javascript
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

## X-XSRF-TOKEN

Laravel, her framework yanıtında **XSRF-TOKEN** adlı şifrelenmiş bir çerez içinde mevcut CSRF token’ını saklar. Bu çerezin değerini kullanarak **X-XSRF-TOKEN** istek başlığını ayarlayabilirsiniz.

Bu çerez, esas olarak bir geliştirici kolaylığı olarak gönderilir; çünkü **Angular** ve **Axios** gibi bazı JavaScript framework’leri ve kütüphaneleri, aynı kaynak üzerindeki isteklere bu değeri **X-XSRF-TOKEN** başlığına otomatik olarak ekler.

Varsayılan olarak, `resources/js/bootstrap.js` dosyası **Axios** HTTP kütüphanesini içerir ve bu kütüphane **X-XSRF-TOKEN** başlığını sizin için otomatik olarak gönderir.
