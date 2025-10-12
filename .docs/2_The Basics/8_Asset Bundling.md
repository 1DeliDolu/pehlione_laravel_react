### Varlık Paketleme (Vite)

#### Giriş

Vite, son derece hızlı bir geliştirme ortamı sağlayan ve kodunuzu üretim için paketleyen modern bir frontend yapı aracıdır. Laravel ile uygulamalar geliştirirken, genellikle uygulamanızın CSS ve JavaScript dosyalarını üretime hazır varlıklara dönüştürmek için Vite kullanırsınız.

Laravel, geliştirme ve üretim ortamlarında varlıklarınızı yüklemek için resmi bir eklenti ve Blade yönergesi sağlayarak Vite ile sorunsuz bir şekilde entegre olur.

---

### Kurulum ve Ayarlama

Aşağıdaki belgeler, Laravel Vite eklentisini manuel olarak nasıl kuracağınızı ve yapılandıracağınızı anlatır. Ancak Laravel’in starter kit’leri bu yapılandırmanın tümünü zaten içerir ve Laravel ile Vite’e başlamak için en hızlı yoldur.

---

#### Node Kurulumu

Vite ve Laravel eklentisini çalıştırmadan önce Node.js (16+) ve NPM’in kurulu olduğundan emin olmalısınız:

```bash
node -v
npm -v
```

En son Node ve NPM sürümünü resmi Node web sitesinden basit grafiksel yükleyicilerle kolayca kurabilirsiniz. Veya Laravel Sail kullanıyorsanız, Node ve NPM’i Sail aracılığıyla çalıştırabilirsiniz:

```bash
./vendor/bin/sail node -v
./vendor/bin/sail npm -v
```

---

#### Vite ve Laravel Eklentisini Kurma

Yeni bir Laravel kurulumunda, uygulamanızın dizin yapısının kökünde bir `package.json` dosyası bulacaksınız. Varsayılan `package.json` dosyası, Vite ve Laravel eklentisini kullanmaya başlamak için gereken her şeyi zaten içerir. Uygulamanızın frontend bağımlılıklarını NPM ile kurabilirsiniz:

```bash
npm install
```

---

### Vite’i Yapılandırma

Vite, projenizin kök dizinindeki `vite.config.js` dosyası aracılığıyla yapılandırılır. Bu dosyayı ihtiyaçlarınıza göre özelleştirebilir ve uygulamanızın gerektirdiği diğer eklentileri (örneğin `@vitejs/plugin-vue` veya `@vitejs/plugin-react`) kurabilirsiniz.

Laravel Vite eklentisi, uygulamanız için giriş noktalarını belirtmenizi gerektirir. Bunlar JavaScript veya CSS dosyaları olabilir ve TypeScript, JSX, TSX ve Sass gibi önceden işlenmiş dilleri içerebilir.

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
    ],
});
```

Eğer bir SPA (özellikle Inertia ile oluşturulmuş uygulamalar) geliştiriyorsanız, Vite CSS giriş noktaları olmadan en iyi şekilde çalışır:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css', 
            'resources/js/app.js',
        ]),
    ],
});
```

Bunun yerine CSS’i JavaScript üzerinden içe aktarmalısınız. Genellikle bu işlem `resources/js/app.js` dosyanızda yapılır:

```js
import './bootstrap';
import '../css/app.css'; 
```

Laravel eklentisi ayrıca birden fazla giriş noktası ve SSR giriş noktaları gibi gelişmiş yapılandırma seçeneklerini de destekler.

---

### Güvenli Bir Geliştirme Sunucusu ile Çalışmak

Yerel geliştirme web sunucunuz uygulamanızı HTTPS üzerinden sunuyorsa, Vite geliştirme sunucusuna bağlanırken sorun yaşayabilirsiniz.

Eğer Laravel Herd kullanıyorsanız ve siteyi güvenceye aldıysanız ya da Laravel Valet kullanarak uygulamanız için `secure` komutunu çalıştırdıysanız, Laravel Vite eklentisi sizin için otomatik olarak oluşturulan TLS sertifikasını algılar ve kullanır.

Eğer siteyi uygulamanın dizin adıyla eşleşmeyen bir host adıyla güvenceye aldıysanız, `vite.config.js` dosyanızda host’u manuel olarak belirtebilirsiniz:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel({
            // ...
            detectTls: 'my-app.test', 
        }),
    ],
});
```

Başka bir web sunucusu kullanıyorsanız, güvenilir bir sertifika oluşturmalı ve Vite’ı bu sertifikaları kullanacak şekilde yapılandırmalısınız:

```js
import fs from 'fs'; 
 
const host = 'my-app.test'; 
 
export default defineConfig({
    server: { 
        host, 
        hmr: { host }, 
        https: { 
            key: fs.readFileSync(`/path/to/${host}.key`), 
            cert: fs.readFileSync(`/path/to/${host}.crt`), 
        }, 
    }, 
});
```

Eğer sisteminiz için güvenilir bir sertifika oluşturamıyorsanız, `@vitejs/plugin-basic-ssl` eklentisini kurup yapılandırabilirsiniz. Güvenilir olmayan sertifikalar kullanıldığında, `npm run dev` komutunu çalıştırdığınızda konsoldaki “Local” bağlantısını takip ederek tarayıcınızda Vite geliştirme sunucusunun sertifika uyarısını kabul etmeniz gerekir.

---

### Sail Üzerinde WSL2’de Geliştirme Sunucusunu Çalıştırmak

Laravel Sail’i Windows Subsystem for Linux 2 (WSL2) üzerinde çalıştırırken, tarayıcının geliştirme sunucusuyla iletişim kurabilmesi için `vite.config.js` dosyanıza aşağıdaki yapılandırmayı eklemelisiniz:

```js
export default defineConfig({
    server: { 
        hmr: {
            host: 'localhost',
        },
    }, 
});
```

Eğer dosya değişiklikleri geliştirme sunucusu çalışırken tarayıcıya yansımıyorsa, ayrıca Vite’ın `server.watch.usePolling` seçeneğini yapılandırmanız gerekebilir.

---

### Script ve Stil Dosyalarınızı Yüklemek

Vite giriş noktalarınızı yapılandırdıktan sonra, bunları uygulamanızın kök şablonunun `<head>` kısmına ekleyeceğiniz bir `@vite()` Blade yönergesi ile referans verebilirsiniz:

```html
<!DOCTYPE html>
<head>
    {{-- ... --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

CSS’i JavaScript üzerinden içe aktarıyorsanız, yalnızca JavaScript giriş noktasını eklemeniz yeterlidir:

```html
<!DOCTYPE html>
<head>
    {{-- ... --}}
    @vite('resources/js/app.js')
</head>
```

`@vite` yönergesi, Vite geliştirme sunucusunu otomatik olarak algılar ve Hot Module Replacement (HMR) özelliğini etkinleştirmek için Vite istemcisini enjekte eder. Derleme modunda, yönerge derlenmiş ve sürümlenmiş varlıklarınızı (içe aktarılan CSS dahil) yükler.

Gerekirse, derlenmiş varlıklarınızın oluşturulduğu yolu da `@vite` yönergesiyle belirtebilirsiniz:

```html
<!doctype html>
<head>
    {{-- Belirtilen build yolu public dizinine göredir. --}}
    @vite('resources/js/app.js', 'vendor/courier/build')
</head>
```

---

### Satır İçi Varlıklar

Bazen, varlıkların sürümlenmiş URL’sine bağlantı vermek yerine, ham içeriğini doğrudan dahil etmeniz gerekebilir. Örneğin, bir PDF oluşturucuya HTML içeriği geçirirken varlık içeriğini doğrudan sayfaya gömmeniz gerekebilir. Vite varlıklarının içeriğini, Vite facade’ının sağladığı `content` yöntemiyle çıktısını alabilirsiniz:

```php
@use('Illuminate\Support\Facades\Vite')
 
<!doctype html>
<head>
    {{-- ... --}}
    <style>
        {!! Vite::content('resources/css/app.css') !!}
    </style>
    <script>
        {!! Vite::content('resources/js/app.js') !!}
    </script>
</head>
```
### Vite’i Çalıştırmak

Vite’i çalıştırmanın iki yolu vardır. Geliştirme sırasında kullanışlı olan **development server**’ı `dev` komutu ile çalıştırabilirsiniz. Development server, dosyalarınızdaki değişiklikleri otomatik olarak algılar ve açık olan tarayıcı pencerelerine anında yansıtır.

Ya da **build** komutunu çalıştırarak uygulamanızın varlıklarını sürümlendirip paketleyebilir ve üretim ortamına dağıtıma hazır hale getirebilirsiniz:

```bash
# Vite geliştirme sunucusunu çalıştırın...
npm run dev
 
# Üretim için varlıkları oluşturun ve sürümlendirin...
npm run build
```

Eğer Sail üzerinde WSL2 kullanıyorsanız, ek yapılandırma seçeneklerine ihtiyaç duyabilirsiniz.

---

### JavaScript ile Çalışmak

#### Takma Adlar (Aliases)

Varsayılan olarak Laravel eklentisi, uygulamanızın varlıklarını kolayca içe aktarmanızı sağlamak için yaygın bir alias sağlar:

```js
{
    '@' => '/resources/js'
}
```

Bu `'@'` alias’ını, `vite.config.js` dosyanızda kendi alias’ınızı ekleyerek geçersiz kılabilirsiniz:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel(['resources/ts/app.tsx']),
    ],
    resolve: {
        alias: {
            '@': '/resources/ts',
        },
    },
});
```

---

### Vue

Frontend’inizi **Vue** framework’ü ile oluşturmak istiyorsanız, `@vitejs/plugin-vue` eklentisini de kurmanız gerekir:

```bash
npm install --save-dev @vitejs/plugin-vue
```

Ardından eklentiyi `vite.config.js` dosyanıza dahil edebilirsiniz. Laravel ile Vue eklentisini kullanırken birkaç ek seçeneğe ihtiyaç duyulur:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
 
export default defineConfig({
    plugins: [
        laravel(['resources/js/app.js']),
        vue({
            template: {
                transformAssetUrls: {
                    // Vue eklentisi, Single File Component'lerdeki asset URL’lerini
                    // Laravel web sunucusuna yönlendirecek şekilde yeniden yazar.
                    // Bunu `null` olarak ayarlamak, Laravel eklentisinin
                    // bu URL’leri Vite sunucusuna yönlendirmesini sağlar.
                    base: null,
 
                    // Vue eklentisi, mutlak URL’leri dosya yolu olarak işler.
                    // Bunu `false` yaparak bu URL’leri dokunmadan bırakırsınız
                    // ve public dizinindeki varlıkları referans alabilirler.
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
```

Laravel’in **starter kitleri**, Laravel, Vue ve Vite için uygun yapılandırmaları zaten içerir. Bu kitler, Laravel + Vue + Vite kombinasyonu ile en hızlı şekilde başlamanızı sağlar.

---

### React

Frontend’inizi **React** framework’ü ile oluşturmak istiyorsanız, `@vitejs/plugin-react` eklentisini kurmanız gerekir:

```bash
npm install --save-dev @vitejs/plugin-react
```

Daha sonra eklentiyi `vite.config.js` dosyanıza ekleyin:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
 
export default defineConfig({
    plugins: [
        laravel(['resources/js/app.jsx']),
        react(),
    ],
});
```

JSX içeren tüm dosyaların `.jsx` veya `.tsx` uzantısına sahip olduğundan emin olun ve gerekirse giriş noktanızı buna göre güncelleyin.

Ayrıca `@viteReactRefresh` Blade yönergesini mevcut `@vite` yönergenizle birlikte eklemeniz gerekir:

```blade
@viteReactRefresh
@vite('resources/js/app.jsx')
```

`@viteReactRefresh` yönergesi **@vite** yönergesinden önce çağrılmalıdır.

Laravel’in starter kitleri, Laravel, React ve Vite için gerekli tüm yapılandırmaları zaten içerir. Bu kitler, Laravel + React + Vite ile başlamanın en hızlı yoludur.

---

### Inertia

Laravel Vite eklentisi, **Inertia** sayfa bileşenlerinizi çözmenize yardımcı olacak kullanışlı bir `resolvePageComponent` fonksiyonu sağlar. Aşağıda Vue 3 ile kullanım örneği gösterilmiştir, ancak bu fonksiyonu React gibi diğer framework’lerde de kullanabilirsiniz:

```js
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
 
createInertiaApp({
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
});
```

Eğer Inertia ile **Vite’in code splitting** (kod bölme) özelliğini kullanıyorsanız, **asset prefetching** yapılandırmanızı öneririz.

Laravel’in starter kitleri, Laravel, Inertia ve Vite için gerekli yapılandırmaları zaten içerir. Bu kitler, Laravel + Inertia + Vite ile en hızlı şekilde başlamanızı sağlar.

---

### URL İşleme

Vite kullanırken HTML, CSS veya JS içinde varlıkları referans alırken bazı noktalara dikkat etmelisiniz.

İlk olarak, **mutlak yollarla** varlık referans ederseniz, Vite bu varlığı derlemeye dahil etmez. Bu nedenle, bu varlığın `public` dizininde bulunduğundan emin olmalısınız. Ayrıca, ayrı bir CSS giriş noktası kullanıyorsanız mutlak yolları kullanmaktan kaçının çünkü geliştirme sırasında tarayıcı bu yolları Vite geliştirme sunucusundan yüklemeye çalışır, public dizininden değil.

**Göreceli yollarla** varlıkları referans alırken, yolların referans verildiği dosyaya göre göreceli olduğunu unutmayın. Göreceli yolla referans verilen varlıklar Vite tarafından yeniden yazılır, sürümlendirilir ve paketlenir.

Örnek proje yapısı:

```
public/
  taylor.png
resources/
  js/
    Pages/
      Welcome.vue
  images/
    abigail.png
```

Aşağıdaki örnek, Vite’in göreceli ve mutlak URL’leri nasıl işlediğini gösterir:

```html
<!-- Bu varlık Vite tarafından işlenmez ve derlemeye dahil edilmez -->
<img src="/taylor.png">
 
<!-- Bu varlık yeniden yazılır, sürümlendirilir ve Vite tarafından paketlenir -->
<img src="../../images/abigail.png">
```
### Stil Dosyalarıyla Çalışmak

Laravel’in starter kit’leri, **Tailwind** ve **Vite** için gerekli yapılandırmayı zaten içerir.
Veya Laravel’in starter kit’lerini kullanmadan **Tailwind + Laravel** kullanmak istiyorsanız, Tailwind’in Laravel için hazırladığı kurulum rehberine göz atabilirsiniz.

Tüm Laravel uygulamaları, halihazırda **Tailwind** ve düzgün yapılandırılmış bir `vite.config.js` dosyası ile birlikte gelir.
Yani yalnızca Vite geliştirme sunucusunu başlatmanız veya Laravel ve Vite geliştirme sunucularını aynı anda çalıştıracak **Composer dev komutunu** kullanmanız yeterlidir:

```bash
composer run dev
```

Uygulamanızın CSS dosyalarını `resources/css/app.css` dosyasında tutabilirsiniz.

---

### Blade ve Rotalarla Çalışmak

#### Statik Varlıkları Vite ile İşleme

JavaScript veya CSS dosyalarınızda varlıklara referans verirken, **Vite** bu varlıkları otomatik olarak işler ve sürümlendirir.
Buna ek olarak, Blade tabanlı uygulamalar oluştururken yalnızca **Blade şablonlarında** referans verilen statik varlıkları da Vite işleyebilir ve sürümlendirebilir.

Ancak bunun gerçekleşebilmesi için, Vite’ın bu varlıklardan haberdar olması gerekir.
Bunu yapmak için statik varlıkları uygulamanızın giriş noktasına (entry point) import etmelisiniz.

Örneğin, `resources/images` klasöründeki tüm resimleri ve `resources/fonts` klasöründeki tüm fontları işleyip sürümlendirmek istiyorsanız, `resources/js/app.js` dosyanıza şunu eklemelisiniz:

```js
import.meta.glob([
  '../images/**',
  '../fonts/**',
]);
```

Bu varlıklar artık `npm run build` çalıştırıldığında Vite tarafından işlenecektir.
Ardından Blade şablonlarında bu varlıkları `Vite::asset` metodu ile çağırabilirsiniz. Bu metot, belirtilen varlığın sürümlendirilmiş URL’sini döndürür:

```blade
<img src="{{ Vite::asset('resources/images/logo.png') }}">
```

---

### Kaydederken Yenileme (Refreshing on Save)

Uygulamanız Blade ile **geleneksel sunucu tarafı render (SSR)** kullanarak oluşturulmuşsa, Vite tarayıcıyı dosya kaydettiğinizde otomatik olarak yenileyerek geliştirme sürecinizi hızlandırabilir.
Başlamak için tek yapmanız gereken `refresh` seçeneğini `true` olarak ayarlamaktır:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel({
            // ...
            refresh: true,
        }),
    ],
});
```

`refresh: true` ayarlandığında, aşağıdaki dizinlerdeki dosyaları kaydettiğinizde Vite tarayıcıda tam sayfa yenilemesi yapar (npm run dev çalışırken):

```
app/Livewire/**
app/View/Components/**
lang/**
resources/lang/**
resources/views/**
routes/**
```

`routes/**` dizinini izlemek, Ziggy kullanarak frontend tarafında rota bağlantıları üreten uygulamalarda oldukça faydalıdır.

Eğer bu varsayılan yollar ihtiyaçlarınıza uymuyorsa, kendi izleme yollarınızı belirtebilirsiniz:

```js
export default defineConfig({
    plugins: [
        laravel({
            // ...
            refresh: ['resources/views/**'],
        }),
    ],
});
```

Arka planda Laravel Vite eklentisi, bu özelliği yönetmek için **vite-plugin-full-reload** paketini kullanır.
Bu paket, yenileme davranışını ince ayarlarla özelleştirmenize olanak tanır.
Daha fazla özelleştirme gerekirse bir yapılandırma tanımı sağlayabilirsiniz:

```js
export default defineConfig({
    plugins: [
        laravel({
            // ...
            refresh: [{
                paths: ['path/to/watch/**'],
                config: { delay: 300 }
            }],
        }),
    ],
});
```

---

### Alias’lar (Takma Adlar)

JavaScript uygulamalarında sık kullanılan dizinlere alias (takma ad) oluşturmak yaygındır.
Aynı şekilde, **Blade** içinde kullanılacak alias’lar da tanımlayabilirsiniz.
Bunu `Illuminate\Support\Facades\Vite` sınıfı üzerinde **macro** metodu aracılığıyla yapabilirsiniz.
Genellikle macro’lar bir servis sağlayıcının (`ServiceProvider`) `boot` metodunda tanımlanır:

```php
/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Vite::macro('image', fn (string $asset) => $this->asset("resources/images/{$asset}"));
}
```

Bir macro tanımlandıktan sonra, şablonlarda doğrudan çağrılabilir.
Örneğin, yukarıda tanımlanan `image` macro’sunu kullanarak `resources/images/logo.png` varlığına şu şekilde erişebilirsiniz:

```blade
<img src="{{ Vite::image('logo.png') }}" alt="Laravel Logo">
```

---

### Asset Prefetching (Varlık Önyükleme)

Vite’in **code splitting (kod bölme)** özelliğini kullanan SPA’larda, gerekli varlıklar her sayfa geçişinde yeniden indirilir.
Bu durum, UI’nın geç yüklenmesine neden olabilir.
Eğer bu durum kullandığınız frontend framework’ü için bir sorun oluşturuyorsa, Laravel uygulamanızın JavaScript ve CSS varlıklarını **sayfa ilk yüklendiğinde önden (eagerly) indirme** olanağı sağlar.

Varlıkların önyüklemesini etkinleştirmek için, bir servis sağlayıcının `boot` metodunda `Vite::prefetch` metodunu çağırabilirsiniz:

```php
<?php
 
namespace App\Providers;
 
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
 
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
```

Yukarıdaki örnekte, her sayfa yüklenişinde aynı anda en fazla **3 varlık** indirilecektir.
İsterseniz `concurrency` değerini uygulamanızın ihtiyaçlarına göre artırabilir veya tüm varlıkların aynı anda indirilmesini sağlayabilirsiniz:

```php
public function boot(): void
{
    Vite::prefetch();
}
```

Varsayılan olarak, önyükleme **sayfa yüklenme olayı (load event)** gerçekleştiğinde başlar.
Eğer önyüklemenin ne zaman başlayacağını özelleştirmek isterseniz, Vite’ın dinleyeceği bir olay belirtebilirsiniz:

```php
public function boot(): void
{
    Vite::prefetch(event: 'vite:prefetch');
}
```

Bu durumda önyükleme, manuel olarak `vite:prefetch` olayını tetiklediğinizde başlar.
Örneğin, sayfa yüklendikten üç saniye sonra başlatabilirsiniz:

```html
<script>
    addEventListener('load', () => setTimeout(() => {
        dispatchEvent(new Event('vite:prefetch'))
    }, 3000))
</script>
```

---

### Özel Temel URL’ler (Custom Base URLs)

Eğer Vite tarafından derlenmiş varlıklarınız, uygulamanızdan ayrı bir alanda (örneğin bir **CDN**) barındırılıyorsa, `.env` dosyanızda **ASSET_URL** ortam değişkenini belirtmelisiniz:

```
ASSET_URL=https://cdn.example.com
```

Bu yapılandırmadan sonra, Vite tarafından yeniden yazılan tüm varlık URL’leri bu adresle öneklenir (prefixed):

```
https://cdn.example.com/build/assets/app.9dce8d17.js
```

Unutmayın: **Mutlak URL’ler** Vite tarafından yeniden yazılmaz, bu nedenle ön eklenmezler.

### Ortam Değişkenleri (Environment Variables)

JavaScript içinde ortam değişkenleri kullanmak için, uygulamanızın `.env` dosyasında değişkenlerinize `VITE_` öneki eklemeniz gerekir:

```
VITE_SENTRY_DSN_PUBLIC=http://example.com
```

Bu şekilde tanımlanmış değişkenlere JavaScript içinde `import.meta.env` nesnesi aracılığıyla erişebilirsiniz:

```js
import.meta.env.VITE_SENTRY_DSN_PUBLIC
```

---

### Testlerde Vite’i Devre Dışı Bırakma

Laravel’in Vite entegrasyonu, testler çalıştırılırken varlıklarınızı çözümlemeye çalışır.
Bu, ya Vite geliştirme sunucusunu çalıştırmanızı ya da varlıklarınızı build etmenizi gerektirir.

Eğer testler sırasında Vite’i taklit (mock) etmek isterseniz, `TestCase` sınıfını genişleten tüm testlerde kullanılabilen `withoutVite` metodunu çağırabilirsiniz:

#### Pest

```php
test('without vite example', function () {
    $this->withoutVite();
 
    // ...
});
```

#### PHPUnit

Tüm testlerde Vite’i devre dışı bırakmak isterseniz, `TestCase` sınıfınızın `setUp` metodunda `withoutVite` metodunu çağırabilirsiniz:

```php
<?php
 
namespace Tests;
 
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
 
abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
 
        $this->withoutVite();
    }
}
```

---

### Sunucu Tarafı Render (SSR)

Laravel Vite eklentisi, **Server-Side Rendering (SSR)** kurulumunu oldukça kolaylaştırır.
Başlamak için `resources/js/ssr.js` dosyasında bir SSR giriş noktası oluşturun ve bunu Vite yapılandırmasında belirtin:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
        }),
    ],
});
```

SSR giriş noktasını yeniden oluşturmayı unutmamak için, uygulamanızın `package.json` dosyasındaki `build` script’ini şu şekilde güncellemeniz önerilir:

```json
"scripts": {
    "dev": "vite",
    "build": "vite build && vite build --ssr"
}
```

Daha sonra SSR sunucusunu oluşturmak ve başlatmak için şu komutları çalıştırabilirsiniz:

```bash
npm run build
node bootstrap/ssr/ssr.js
```

Eğer SSR’yi **Inertia** ile birlikte kullanıyorsanız, SSR sunucusunu başlatmak için şu Artisan komutunu çalıştırabilirsiniz:

```bash
php artisan inertia:start-ssr
```

Laravel’in starter kit’leri, **Laravel + Inertia SSR + Vite** için gerekli tüm yapılandırmaları zaten içerir.
Bu kitler, bu teknolojilerle başlamanın en hızlı yoludur.

---

### Script ve Style Etiketleri Özellikleri

#### İçerik Güvenlik Politikası (CSP) Nonce

Eğer **Content Security Policy (CSP)** kapsamında script ve style etiketlerinize `nonce` niteliği eklemek istiyorsanız, özel bir middleware içinde `useCspNonce` metodunu kullanarak bir nonce oluşturabilir veya belirleyebilirsiniz:

```php
<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;
 
class AddContentSecurityPolicyHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();
 
        return $next($request)->withHeaders([
            'Content-Security-Policy' => "script-src 'nonce-".Vite::cspNonce()."'",
        ]);
    }
}
```

`useCspNonce` metodu çağrıldıktan sonra, Laravel tüm oluşturulan **script** ve **style** etiketlerine otomatik olarak `nonce` özelliğini ekler.

Eğer bu nonce değerini başka bir yerde (örneğin Laravel’in starter kit’lerinde yer alan Ziggy’nin `@routes` direktifinde) kullanmak istiyorsanız, `cspNonce` metodunu çağırabilirsiniz:

```blade
@routes(nonce: Vite::cspNonce())
```

Eğer halihazırda bir `nonce` değeriniz varsa ve Laravel’in bu değeri kullanmasını istiyorsanız, `useCspNonce` metoduna bu değeri parametre olarak geçebilirsiniz:

```php
Vite::useCspNonce($nonce);
```

---

### Alt Kaynak Bütünlüğü (Subresource Integrity - SRI)

Eğer Vite manifest’iniz varlıklarınız için **integrity hash** değerlerini içeriyorsa, Laravel oluşturulan tüm **script** ve **style** etiketlerine `integrity` özelliğini otomatik olarak ekler.
Bu, **Subresource Integrity (SRI)** korumasını sağlar.

Varsayılan olarak, Vite manifest dosyasına bu hash değerlerini eklemez.
Ancak bunu etkinleştirmek için şu NPM eklentisini kurabilirsiniz:

```bash
npm install --save-dev vite-plugin-manifest-sri
```

Daha sonra bu eklentiyi `vite.config.js` dosyanıza dahil edin:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import manifestSRI from 'vite-plugin-manifest-sri';
 
export default defineConfig({
    plugins: [
        laravel({
            // ...
        }),
        manifestSRI(),
    ],
});
```

Gerekirse, **integrity hash** değerinin manifest içinde bulunduğu anahtar adını özelleştirebilirsiniz:

```php
use Illuminate\Support\Facades\Vite;
 
Vite::useIntegrityKey('custom-integrity-key');
```

Bu otomatik algılamayı tamamen devre dışı bırakmak isterseniz, `useIntegrityKey` metoduna `false` parametresini geçebilirsiniz:

```php
Vite::useIntegrityKey(false);
```
### Keyfi (Arbitrary) Özellikler

Script ve style etiketlerinize ek özellikler (örneğin `data-turbo-track`) eklemeniz gerekiyorsa, bunları `useScriptTagAttributes` ve `useStyleTagAttributes` metodlarıyla belirtebilirsiniz.
Genellikle bu metodlar bir **service provider** içinde çağrılmalıdır:

```php
use Illuminate\Support\Facades\Vite;
 
Vite::useScriptTagAttributes([
    'data-turbo-track' => 'reload', // Özellik için bir değer belirtin...
    'async' => true, // Değersiz bir özellik belirtin...
    'integrity' => false, // Normalde eklenmesi gereken bir özelliği hariç tutun...
]);
 
Vite::useStyleTagAttributes([
    'data-turbo-track' => 'reload',
]);
```

Eğer koşullu olarak özellik eklemek istiyorsanız, Vite tarafından size iletilen asset yolu, URL’si, manifest chunk bilgisi ve tüm manifest’i parametre olarak alan bir callback fonksiyonu geçebilirsiniz:

```php
use Illuminate\Support\Facades\Vite;
 
Vite::useScriptTagAttributes(fn (string $src, string $url, array|null $chunk, array|null $manifest) => [
    'data-turbo-track' => $src === 'resources/js/app.js' ? 'reload' : false,
]);
 
Vite::useStyleTagAttributes(fn (string $src, string $url, array|null $chunk, array|null $manifest) => [
    'data-turbo-track' => $chunk && $chunk['isEntry'] ? 'reload' : false,
]);
```

> Not: `$chunk` ve `$manifest` parametreleri, **Vite geliştirme sunucusu çalışırken** `null` olacaktır.

---

### Gelişmiş Özelleştirme (Advanced Customization)

Laravel’in Vite eklentisi, çoğu uygulama için uygun olan mantıklı varsayılan ayarlarla birlikte gelir.
Ancak bazen Vite davranışını özelleştirmeniz gerekebilir.
Bunun için, `@vite` Blade direktifi yerine aşağıdaki metodları ve seçenekleri kullanabilirsiniz:

```blade
<!doctype html>
<head>
    {{-- ... --}}
 
    {{
        Vite::useHotFile(storage_path('vite.hot')) // "hot" dosyasını özelleştirin...
            ->useBuildDirectory('bundle') // Build dizinini özelleştirin...
            ->useManifestFilename('assets.json') // Manifest dosyasının adını özelleştirin...
            ->withEntryPoints(['resources/js/app.js']) // Entry point dosyalarını belirtin...
            ->createAssetPathsUsing(function (string $path, ?bool $secure) { // Derlenmiş varlıklar için URL oluşturmayı özelleştirin...
                return "https://cdn.example.com/{$path}";
            })
    }}
</head>
```

Buna karşılık, `vite.config.js` dosyanızda aynı yapılandırmaları belirtmelisiniz:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel({
            hotFile: 'storage/vite.hot', // "hot" dosyasını özelleştirin...
            buildDirectory: 'bundle', // Build dizinini özelleştirin...
            input: ['resources/js/app.js'], // Entry point dosyalarını belirtin...
        }),
    ],
    build: {
        manifest: 'assets.json', // Manifest dosyasını özelleştirin...
    },
});
```

---

### Geliştirme Sunucusu CORS (Cross-Origin Resource Sharing)

Eğer tarayıcıda Vite geliştirme sunucusundan varlıklar yüklenirken **CORS hataları** yaşıyorsanız, geliştirme sunucusuna özel bir origin erişimi vermeniz gerekebilir.

Laravel Vite eklentisi varsayılan olarak aşağıdaki origin’lere izin verir:

```
::1
127.0.0.1
localhost
*.test
*.localhost
APP_URL (projedeki .env dosyasında belirtilen)
```

Projede özel bir origin kullanmak istiyorsanız, yapmanız gereken en kolay şey `.env` dosyasındaki `APP_URL` değişkeninin tarayıcıda ziyaret ettiğiniz adresle eşleştiğinden emin olmaktır.

Örneğin, tarayıcıda `https://my-app.laravel` adresini ziyaret ediyorsanız `.env` dosyanızda şu şekilde olmalıdır:

```
APP_URL=https://my-app.laravel
```

Birden fazla origin’e izin vermek veya daha ayrıntılı kontrol istiyorsanız, Vite’ın yerleşik **CORS yapılandırmasını** kullanabilirsiniz.
Bunun için `vite.config.js` dosyasına aşağıdaki gibi bir yapı ekleyebilirsiniz:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
    ],
    server: {  
        cors: {  
            origin: [  
                'https://backend.laravel',  
                'http://admin.laravel:8566',  
            ],  
        },  
    },  
});
```

Ayrıca, belirli bir üst düzey alan adındaki (örneğin `*.laravel`) tüm origin’lere izin vermek için **regex desenleri** de kullanabilirsiniz:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
    ],
    server: {  
        cors: {  
            origin: [ 
                // Şu kalıba uyan tüm alan adlarını destekler: SCHEME://DOMAIN.laravel[:PORT] 
                /^https?:\/\/.*\.laravel(:\d+)?$/, 
            ], 
        }, 
    }, 
});
```

---

### Geliştirme Sunucusu URL’lerini Düzeltme

Vite ekosistemindeki bazı eklentiler, `/` (eğik çizgi) ile başlayan URL’lerin her zaman **Vite geliştirme sunucusuna** işaret edeceğini varsayar.
Ancak Laravel entegrasyonu nedeniyle bu her zaman doğru değildir.

Örneğin, `vite-imagetools` eklentisi, Vite varlıkları sunarken şu tür URL’ler oluşturur:

```html
<img src="/@imagetools/f0b2f404b13f052c604e632f2fb60381bf61a520">
```

`vite-imagetools` eklentisi, `/@imagetools` ile başlayan URL’lerin Vite tarafından yakalanmasını bekler.
Eğer bu davranışı bekleyen eklentiler kullanıyorsanız, URL’leri manuel olarak düzeltmeniz gerekir.
Bunu `vite.config.js` içinde `transformOnServe` seçeneğiyle yapabilirsiniz.

Aşağıdaki örnekte, oluşturulan tüm `/@imagetools` URL’lerine geliştirme sunucusu adresi öneklenecektir:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { imagetools } from 'vite-imagetools';
 
export default defineConfig({
    plugins: [
        laravel({
            // ...
            transformOnServe: (code, devServerUrl) => 
                code.replaceAll('/@imagetools', devServerUrl + '/@imagetools'),
        }),
        imagetools(),
    ],
});
```

Artık Vite varlıkları sunarken, oluşturulan URL’ler geliştirme sunucusunu işaret edecektir:

```diff
- <img src="/@imagetools/f0b2f404b13f052c604e632f2fb60381bf61a520">
+ <img src="http://[::1]:5173/@imagetools/f0b2f404b13f052c604e632f2fb60381bf61a520">
```
