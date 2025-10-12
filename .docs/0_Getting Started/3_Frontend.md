Frontend
Giriş
Laravel, modern web uygulamaları oluşturmak için ihtiyaç duyduğunuz tüm özellikleri sağlayan bir backend framework’tür; routing, validation, caching, queues, file storage ve daha fazlasını içerir. Ancak, geliştiricilere güçlü bir full-stack deneyimi sunmanın önemli olduğuna inanıyoruz ve bu da uygulamanızın frontend kısmını oluşturmak için güçlü yaklaşımlar içerir.

Laravel ile bir uygulama oluştururken frontend geliştirmeyi ele almanın iki ana yolu vardır ve hangi yaklaşımı seçeceğiniz, frontend’i PHP’den mi yoksa Vue ve React gibi JavaScript framework’lerinden yararlanarak mı oluşturmak istediğinize bağlıdır. Aşağıda bu iki seçeneği tartışacağız, böylece uygulamanız için frontend geliştirmede en uygun yaklaşımı belirleyebilirsiniz.

### PHP Kullanımı

#### PHP ve Blade

Geçmişte, çoğu PHP uygulaması, bir istek sırasında veritabanından alınan verileri render eden PHP echo ifadeleriyle karışık basit HTML şablonlarını kullanarak HTML çıktısını tarayıcıya gönderirdi:

```php
<div>
    <?php foreach ($users as $user): ?>
        Hello, <?php echo $user->name; ?> <br />
    <?php endforeach; ?>
</div>
```

Laravel’de, HTML’i render etmenin bu yaklaşımı hâlâ views ve Blade kullanılarak gerçekleştirilebilir. Blade, verileri görüntülemek, veriler üzerinde döngü oluşturmak ve daha fazlası için kullanışlı, kısa sözdizimi sağlayan son derece hafif bir template dilidir:

```php
<div>
    @foreach ($users as $user)
        Hello, {{ $user->name }} <br />
    @endforeach
</div>
```

Bu şekilde uygulamalar oluştururken, form gönderimleri ve diğer sayfa etkileşimleri genellikle sunucudan tamamen yeni bir HTML belgesi alır ve tüm sayfa tarayıcı tarafından yeniden render edilir. Günümüzde bile birçok uygulama, basit Blade template’leri kullanılarak frontend’lerinin bu şekilde oluşturulmasına gayet uygundur.

### Artan Beklentiler

Ancak, kullanıcı beklentileri web uygulamaları açısından geliştikçe, birçok geliştirici daha modern ve dinamik hissettiren frontend’ler oluşturma ihtiyacı duymuştur. Bu doğrultuda, bazı geliştiriciler uygulamalarının frontend’ini Vue ve React gibi JavaScript framework’lerini kullanarak inşa etmeye başlamıştır.

Diğerleri ise, rahat oldukları backend dilinde kalmayı tercih ederek modern web uygulama arayüzlerini oluşturmayı mümkün kılan çözümler geliştirmiştir. Örneğin, Rails ekosisteminde bu durum Turbo Hotwire ve Stimulus gibi kütüphanelerin oluşturulmasına yol açmıştır.

Laravel ekosisteminde ise, PHP’yi ağırlıklı olarak kullanarak modern ve dinamik frontend’ler oluşturma ihtiyacı, **Laravel Livewire** ve **Alpine.js**’in ortaya çıkmasına neden olmuştur.

### Livewire

Laravel Livewire, Vue ve React gibi modern JavaScript framework’leriyle oluşturulan frontend’ler kadar dinamik, modern ve canlı hissedilen Laravel tabanlı frontend’ler inşa etmek için kullanılan bir framework’tür.

Livewire kullanırken, kullanıcı arayüzünüzün belirli bir bölümünü render eden ve frontend tarafından etkileşime girilebilen veri ve metotlar sunan Livewire “component”leri oluşturursunuz. Örneğin, basit bir “Counter” component’i şu şekilde olabilir:

```php
<?php
 
namespace App\Http\Livewire;
 
use Livewire\Component;
 
class Counter extends Component
{
    public $count = 0;
 
    public function increment()
    {
        $this->count++;
    }
 
    public function render()
    {
        return view('livewire.counter');
    }
}
```

Ve, counter için karşılık gelen template şu şekilde yazılabilir:

```html
<div>
    <button wire:click="increment">+</button>
    <h1>{{ $count }}</h1>
</div>
```

Gördüğünüz gibi, Livewire, Laravel uygulamanızın frontend’i ile backend’ini birbirine bağlayan **wire:click** gibi yeni HTML attribute’leri yazmanıza olanak tanır. Ayrıca, component’in mevcut durumunu basit Blade ifadeleriyle render edebilirsiniz.

Birçok kişi için Livewire, Laravel ile frontend geliştirmede devrim yaratmıştır; böylece Laravel’in rahatlığında kalarak modern, dinamik web uygulamaları oluşturabilirler. Genellikle Livewire kullanan geliştiriciler, frontend’e yalnızca gerektiğinde (örneğin bir diyalog penceresi render etmek için) biraz JavaScript eklemek amacıyla **Alpine.js**’i de kullanırlar.

Laravel’e yeniyseniz, öncelikle views ve Blade’in temel kullanımına aşina olmanızı öneririz. Ardından, etkileşimli Livewire component’leriyle uygulamanızı bir üst seviyeye taşımayı öğrenmek için resmi Laravel Livewire dokümantasyonuna başvurun.

### Starter Kits

Eğer frontend’inizi PHP ve Livewire kullanarak oluşturmak istiyorsanız, uygulamanızın geliştirilmesini hızlı başlatmak için Livewire starter kit’inden yararlanabilirsiniz.

---

### React veya Vue Kullanımı

Laravel ve Livewire kullanarak modern frontend’ler oluşturmak mümkün olsa da, birçok geliştirici hâlâ React veya Vue gibi bir JavaScript framework’ünün gücünden yararlanmayı tercih eder. Bu, geliştiricilere NPM aracılığıyla mevcut olan zengin JavaScript paketleri ve araçları ekosisteminden faydalanma olanağı sağlar.

Ancak, ek araçlar olmadan Laravel’i React veya Vue ile eşleştirmek, **client-side routing**, **data hydration** ve **authentication** gibi çeşitli karmaşık problemleri çözmemizi gerektirir. Client-side routing genellikle **Next** ve **Nuxt** gibi belirli görüşleri benimsemiş React / Vue framework’leri kullanılarak kolaylaştırılır; ancak data hydration ve authentication, Laravel gibi bir backend framework’ü bu frontend framework’leriyle eşleştirildiğinde hâlâ karmaşık ve zahmetli problemler olmaya devam eder.

Ayrıca, geliştiriciler iki ayrı code repository’sini yönetmek zorunda kalır, genellikle bakım, sürüm ve deployment işlemlerini her iki repository arasında koordine etmeleri gerekir. Bu problemler aşılabilir olsa da, bunun üretken veya keyifli bir uygulama geliştirme yöntemi olduğuna inanmıyoruz.

---

### Inertia

Neyse ki, Laravel her iki dünyanın da en iyisini sunar. **Inertia**, Laravel uygulamanız ile modern React veya Vue frontend’iniz arasındaki boşluğu doldurur ve Laravel routes ve controllers’ı kullanarak routing, data hydration ve authentication işlemlerini tek bir code repository içinde yönetirken, React veya Vue kullanarak tam teşekküllü modern frontend’ler oluşturmanıza olanak tanır. Bu yaklaşımla, Laravel ve React / Vue’nun tüm gücünden yararlanabilir, hiçbir aracın yeteneklerini kısıtlamadan ikisini birleştirebilirsiniz.

Laravel uygulamanıza Inertia’yı kurduktan sonra, route’ları ve controller’ları her zamanki gibi yazarsınız. Ancak, controller’dan bir Blade template döndürmek yerine, bir Inertia sayfası döndürürsünüz:

```php
<?php
 
namespace App\Http\Controllers;
 
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
 
class UserController extends Controller
{
    /**
     * Show the profile for a given user.
     */
    public function show(string $id): Response
    {
        return Inertia::render('users/show', [
            'user' => User::findOrFail($id)
        ]);
    }
}
```

Bir Inertia sayfası, genellikle uygulamanızın `resources/js/pages` dizininde bulunan bir React veya Vue component’ine karşılık gelir. **Inertia::render** metodu aracılığıyla sayfaya verilen veriler, component’in **props**’larını hydrate etmek için kullanılır:

```jsx
import Layout from '@/layouts/authenticated';
import { Head } from '@inertiajs/react';
 
export default function Show({ user }) {
    return (
        <Layout>
            <Head title="Welcome" />
            <h1>Welcome</h1>
            <p>Hello {user.name}, welcome to Inertia.</p>
        </Layout>
    )
}
```

Görüldüğü gibi, Inertia frontend oluştururken React veya Vue’nun tüm gücünden yararlanmanızı sağlar ve Laravel destekli backend ile JavaScript destekli frontend arasında hafif bir köprü görevi görür.

---

### Server-Side Rendering

Uygulamanızın **server-side rendering (SSR)** gerektirdiği için Inertia kullanmaktan endişe ediyorsanız, merak etmeyin. Inertia, server-side rendering desteği sunar. Ayrıca, uygulamanızı **Laravel Cloud** veya **Laravel Forge** aracılığıyla deploy ederken, Inertia’nın SSR sürecinin her zaman çalışır durumda olmasını sağlamak oldukça kolaydır.

---

### Starter Kits

Frontend’inizi **Inertia ve Vue / React** kullanarak oluşturmak istiyorsanız, React veya Vue uygulama starter kit’lerinden yararlanarak uygulamanızın geliştirilmesini hızlı başlatabilirsiniz. Bu starter kit’ler, **Inertia**, **Vue / React**, **Tailwind** ve **Vite** kullanarak uygulamanızın backend ve frontend authentication akışını oluşturur, böylece bir sonraki büyük fikrinizi geliştirmeye hemen başlayabilirsiniz.

---

### Bundling Assets

Frontend’inizi **Blade ve Livewire** veya **Vue / React ve Inertia** kullanarak geliştiriyor olsanız da, uygulamanızın **CSS** dosyalarını production’a hazır asset’lere dönüştürmeniz gerekecektir. Ayrıca, uygulamanızın frontend’ini Vue veya React ile oluşturmayı seçerseniz, component’lerinizi tarayıcıda çalışacak JavaScript asset’lerine de bundle etmeniz gerekir.

Varsayılan olarak Laravel, asset’lerinizi bundle etmek için **Vite** kullanır. Vite, son derece hızlı build süreleri ve yerel geliştirme sırasında neredeyse anlık **Hot Module Replacement (HMR)** sağlar. Tüm yeni Laravel uygulamalarında — starter kit’ler de dahil olmak üzere — **vite.config.js** dosyası bulunur ve bu dosya, Vite’i Laravel uygulamalarıyla kullanmayı keyifli hale getiren hafif Laravel Vite eklentisini yükler.

Laravel ve Vite ile başlamanın en hızlı yolu, uygulamanızın geliştirilmesini **application starter kit**’lerimizden biriyle başlatmaktır; bu kit’ler frontend ve backend authentication yapılandırmasını sağlayarak uygulamanıza hızlı bir başlangıç yapmanızı sağlar.

Laravel ile Vite kullanımına dair daha ayrıntılı dokümantasyon için, **asset’lerinizi derleme ve paketleme** konusundaki özel dokümantasyonumuza bakın.
