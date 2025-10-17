
<br>




## Broadcasting

<br>



### Introduction

Birçok modern web uygulamasında, **WebSocket**’ler gerçek zamanlı, canlı güncellenen kullanıcı arayüzleri oluşturmak için kullanılır. Sunucuda bazı veriler güncellendiğinde, bu genellikle bir **WebSocket** bağlantısı üzerinden istemciye gönderilen bir mesajla gerçekleştirilir. WebSocket’ler, kullanıcı arayüzünüzde yansıtılması gereken veri değişikliklerini sürekli olarak uygulama sunucunuzdan sorgulamaya göre daha verimli bir alternatiftir.

Örneğin, uygulamanızın bir kullanıcının verilerini bir **CSV** dosyasına aktarıp kendisine e-posta ile gönderebildiğini düşünün. Ancak bu CSV dosyasının oluşturulması birkaç dakika sürebilir, bu nedenle CSV’yi bir **queued job** içinde oluşturup göndermeyi tercih edersiniz. CSV oluşturulup kullanıcıya e-posta ile gönderildiğinde, **App\Events\UserDataExported** olayını yayınlamak (**broadcast**) için olay yayınlamayı kullanabiliriz. Uygulamanızın JavaScript’i bu olayı aldığında, kullanıcıya CSV’sinin e-posta ile gönderildiğini bildiren bir mesaj gösterebiliriz — sayfayı yenilemeye gerek kalmadan.

Laravel, bu tür özellikleri oluşturmanıza yardımcı olmak için **Laravel event**’lerinizi bir **WebSocket** bağlantısı üzerinden “yayınlamayı (broadcast)” kolaylaştırır. Laravel event’lerinizi yayınlamak, sunucu tarafı Laravel uygulamanız ile istemci tarafı JavaScript uygulamanız arasında aynı event adlarını ve verileri paylaşmanıza olanak tanır.

Yayınlamanın temel kavramları basittir: istemciler ön tarafta isimlendirilmiş kanallara bağlanır, Laravel uygulamanız ise arka tarafta bu kanallara olaylar yayınlar. Bu olaylar, ön uca sunmak istediğiniz ek verileri içerebilir.

<br>



### Supported Drivers

Varsayılan olarak, Laravel üç sunucu tarafı yayınlama sürücüsünü destekler: **Laravel Reverb**, **Pusher Channels** ve **Ably**.

Olay yayınlamaya dalmadan önce, Laravel’in **events and listeners** belgelerini okuduğunuzdan emin olun.

<br>



### Quickstart

Varsayılan olarak, yeni Laravel uygulamalarında yayınlama etkin değildir. Yayınlamayı etkinleştirmek için şu Artisan komutunu kullanabilirsiniz:

```bash
php artisan install:broadcasting
````

`install:broadcasting` komutu, hangi yayın servisini kullanmak istediğinizi sorar. Ayrıca, `config/broadcasting.php` yapılandırma dosyasını ve `routes/channels.php` dosyasını oluşturur. Bu dosyalarda uygulamanızın yayın yetkilendirme rotalarını ve geri çağrımlarını kaydedebilirsiniz.

Laravel, kutudan çıktığı haliyle birkaç yayın sürücüsünü destekler: **Laravel Reverb**, **Pusher Channels**, **Ably** ve yerel geliştirme ile hata ayıklama için bir **log driver**. Ayrıca test sırasında yayınlamayı devre dışı bırakmanızı sağlayan bir **null driver** da dahildir. Her sürücü için bir yapılandırma örneği `config/broadcasting.php` dosyasında bulunur.

Tüm yayınlama yapılandırmanız `config/broadcasting.php` dosyasında saklanır. Bu dosya mevcut değilse endişelenmeyin; `install:broadcasting` Artisan komutunu çalıştırdığınızda oluşturulacaktır.

<br>



### Next Steps

Olay yayınlamayı etkinleştirdikten sonra, yayınlanacak olayların nasıl tanımlanacağını ve olayların nasıl dinleneceğini öğrenmeye hazırsınız. Laravel’in **React** veya **Vue** starter kit’lerini kullanıyorsanız, olayları **Echo**’nun `useEcho` hook’u ile dinleyebilirsiniz.

Herhangi bir olayı yayınlamadan önce, bir **queue worker** yapılandırıp çalıştırmalısınız. Tüm olay yayınlamaları, uygulamanızın yanıt süresinin olumsuz etkilenmemesi için **queued job**’lar aracılığıyla gerçekleştirilir.

<br>



### Server Side Installation

Laravel’in olay yayınlama özelliğini kullanmaya başlamak için, Laravel uygulamanızda birkaç yapılandırma yapmanız ve bazı paketleri yüklemeniz gerekir.

Olay yayınlama, Laravel **Echo** (bir JavaScript kütüphanesi) tarafından tarayıcı istemcisinde alınabilecek şekilde, Laravel event’lerinizi yayınlayan bir **server-side broadcasting driver** tarafından gerçekleştirilir. Endişelenmeyin — kurulum sürecinin her adımını tek tek inceleyeceğiz.

<br>



### Reverb

Laravel’in yayınlama özelliklerini **Reverb** kullanarak hızlıca etkinleştirmek için şu komutu çalıştırın:

```bash
php artisan install:broadcasting --reverb
```

Bu komut, Reverb için gerekli **Composer** ve **NPM** paketlerini yükler ve `.env` dosyanızı uygun değişkenlerle günceller.

<br>



#### Manual Installation

`install:broadcasting` komutunu çalıştırdığınızda sizden Reverb yüklemesi istenecektir. Elbette, Reverb’i manuel olarak da yükleyebilirsiniz:

```bash
composer require laravel/reverb
```

Paket yüklendikten sonra, Reverb’in yapılandırmasını yayınlamak, gerekli ortam değişkenlerini eklemek ve olay yayınlamayı etkinleştirmek için şu komutu çalıştırın:

```bash
php artisan reverb:install
```

Ayrıntılı Reverb kurulum ve kullanım talimatlarını **Reverb documentation**’ında bulabilirsiniz.

<br>



### Pusher Channels

Laravel’in yayınlama özelliklerini **Pusher** kullanarak etkinleştirmek için şu komutu çalıştırın:

```bash
php artisan install:broadcasting --pusher
```

Bu komut sizden Pusher kimlik bilgilerinizi ister, gerekli **PHP** ve **JavaScript SDK**’larını yükler ve `.env` dosyanızı günceller.

<br>



#### Manual Installation

Pusher desteğini manuel olarak yüklemek için şu komutu çalıştırın:

```bash
composer require pusher/pusher-php-server
```

Ardından, `config/broadcasting.php` dosyasındaki Pusher yapılandırmasını düzenleyin. Örnek yapılandırma zaten bu dosyada bulunur. Kimlik bilgilerinizi `.env` dosyanıza ekleyebilirsiniz:

```env
PUSHER_APP_ID="your-pusher-app-id"
PUSHER_APP_KEY="your-pusher-key"
PUSHER_APP_SECRET="your-pusher-secret"
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME="https"
PUSHER_APP_CLUSTER="mt1"
```

`BROADCAST_CONNECTION` ortam değişkenini `pusher` olarak ayarlayın:

```env
BROADCAST_CONNECTION=pusher
```

Son olarak, istemci tarafında yayınlanan olayları almak için **Laravel Echo**’yu yükleyip yapılandırabilirsiniz.

<br>



### Ably

Aşağıdaki belgeler, **Ably**’nin “Pusher uyumluluk” modunda nasıl kullanılacağını açıklar. Ancak **Ably** ekibi, Ably’nin özel yeteneklerinden yararlanabilen kendi yayıncı ve **Echo client** sürücülerini önermekte ve bakımını yapmaktadır. Daha fazla bilgi için **Ably’s Laravel broadcaster documentation**’a bakın.

Laravel’in yayınlama özelliklerini **Ably** kullanarak etkinleştirmek için şu komutu çalıştırın:

```bash
php artisan install:broadcasting --ably
```

Bu komut sizden Ably kimlik bilgilerinizi ister, gerekli **PHP** ve **JavaScript SDK**’larını yükler ve `.env` dosyanızı günceller.

Devam etmeden önce, Ably uygulama ayarlarında **Pusher protocol support**’u etkinleştirmelisiniz. Bu ayar, Ably kontrol panelinizdeki **“Protocol Adapter Settings”** kısmında bulunur.

<br>



#### Manual Installation

Ably desteğini manuel olarak yüklemek için şu komutu çalıştırın:

```bash
composer require ably/ably-php
```

Ardından, `config/broadcasting.php` dosyasındaki Ably yapılandırmasını düzenleyin ve `.env` dosyanıza anahtarınızı ekleyin:

```env
ABLY_KEY=your-ably-key
```

`BROADCAST_CONNECTION` ortam değişkenini `ably` olarak ayarlayın:

```env
BROADCAST_CONNECTION=ably
```

Son olarak, istemci tarafında olayları almak için **Laravel Echo**’yu yükleyip yapılandırabilirsiniz.



<br>



## Client Side Installation

<br>



### Reverb

**Laravel Echo**, sunucu tarafı yayın sürücünüz tarafından yayınlanan olaylara abone olmayı ve bu olayları dinlemeyi kolaylaştıran bir **JavaScript** kütüphanesidir.

`install:broadcasting` Artisan komutu aracılığıyla **Laravel Reverb** kurulumunu yaptığınızda, **Reverb** ve **Echo**’nun yapılandırması ve dosya iskeleti otomatik olarak uygulamanıza eklenir. Ancak Laravel Echo’yu manuel olarak yapılandırmak isterseniz, aşağıdaki adımları izleyebilirsiniz.

<br>



#### Manual Installation

Uygulamanızın frontend tarafında Laravel Echo’yu manuel olarak yapılandırmak için, öncelikle **pusher-js** paketini yükleyin. Çünkü Reverb, **WebSocket** abonelikleri, kanalları ve mesajları için **Pusher protokolünü** kullanır:

```bash
npm install --save-dev laravel-echo pusher-js
````

Echo yüklendikten sonra, uygulamanızın **JavaScript** dosyasında yeni bir **Echo** örneği oluşturabilirsiniz. Bunu yapmak için en uygun yer, Laravel framework’ü ile birlikte gelen `resources/js/bootstrap.js` dosyasının sonudur:

```javascript
import Echo from 'laravel-echo';
 
import Pusher from 'pusher-js';
window.Pusher = Pusher;
 
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

Ardından, uygulamanızın varlıklarını derlemelisiniz:

```bash
npm run build
```

Laravel Echo’nun **Reverb broadcaster** desteği için `laravel-echo` sürümünün **v1.16.0+** olması gerekir.

<br>



### Pusher Channels

**Laravel Echo**, sunucu tarafı yayın sürücünüz tarafından yayınlanan olaylara abone olmayı ve dinlemeyi kolaylaştıran bir **JavaScript** kütüphanesidir.

`install:broadcasting --pusher` Artisan komutunu kullanarak yayın desteğini etkinleştirdiğinizde, **Pusher** ve **Echo**’nun yapılandırması ve dosya iskeleti otomatik olarak uygulamanıza eklenir. Ancak Laravel Echo’yu manuel olarak yapılandırmak isterseniz, aşağıdaki adımları izleyebilirsiniz.

<br>



#### Manual Installation

Uygulamanızın frontend tarafında Laravel Echo’yu manuel olarak yapılandırmak için, öncelikle **laravel-echo** ve **pusher-js** paketlerini yükleyin:

```bash
npm install --save-dev laravel-echo pusher-js
```

Echo yüklendikten sonra, uygulamanızın `resources/js/bootstrap.js` dosyasında yeni bir **Echo** örneği oluşturabilirsiniz:

```javascript
import Echo from 'laravel-echo';
 
import Pusher from 'pusher-js';
window.Pusher = Pusher;
 
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

Ardından `.env` dosyanızda gerekli Pusher ortam değişkenlerini tanımlayın (mevcut değillerse ekleyin):

```env
PUSHER_APP_ID="your-pusher-app-id"
PUSHER_APP_KEY="your-pusher-key"
PUSHER_APP_SECRET="your-pusher-secret"
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME="https"
PUSHER_APP_CLUSTER="mt1"
 
VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

Yapılandırmayı ihtiyaçlarınıza göre düzenledikten sonra, varlıkları derleyin:

```bash
npm run build
```

Uygulamanızın **JavaScript** varlıklarının derlenmesi hakkında daha fazla bilgi için **Vite** belgelerine bakabilirsiniz.

<br>



#### Using an Existing Client Instance

Halihazırda önceden yapılandırılmış bir **Pusher Channels client instance**’ınız varsa, bunu **Echo**’ya `client` yapılandırma seçeneğiyle geçebilirsiniz:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
 
const options = {
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY
}
 
window.Echo = new Echo({
    ...options,
    client: new Pusher(options.key, options)
});
```

<br>



### Ably

Aşağıdaki belgeler, **Ably**’nin “Pusher uyumluluk” modunda nasıl kullanılacağını açıklar. Ancak **Ably** ekibi, Ably’nin sunduğu özel yeteneklerden yararlanabilen kendi **broadcaster** ve **Echo client** sürücülerini önermekte ve bakımını yapmaktadır. Daha fazla bilgi için **Ably’s Laravel broadcaster documentation**’a başvurabilirsiniz.

**Laravel Echo**, sunucu tarafı yayın sürücünüz tarafından yayınlanan olaylara abone olmayı ve dinlemeyi kolaylaştıran bir **JavaScript** kütüphanesidir.

`install:broadcasting --ably` Artisan komutu ile yayın desteğini kurduğunuzda, **Ably** ve **Echo** yapılandırmaları otomatik olarak uygulamanıza eklenir. Ancak manuel kurulum yapmak isterseniz, aşağıdaki adımları izleyebilirsiniz.

<br>



#### Manual Installation

Uygulamanızın frontend tarafında Laravel Echo’yu manuel olarak yapılandırmak için, önce **laravel-echo** ve **pusher-js** paketlerini yükleyin:

```bash
npm install --save-dev laravel-echo pusher-js
```

Devam etmeden önce, Ably uygulama ayarlarında **Pusher protocol support**’u etkinleştirin. Bu seçenek, Ably kontrol panelinizdeki **“Protocol Adapter Settings”** kısmında bulunur.

Echo yüklendikten sonra, uygulamanızın `resources/js/bootstrap.js` dosyasında yeni bir **Echo** örneği oluşturabilirsiniz:

```javascript
import Echo from 'laravel-echo';
 
import Pusher from 'pusher-js';
window.Pusher = Pusher;
 
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_ABLY_PUBLIC_KEY,
    wsHost: 'realtime-pusher.ably.io',
    wsPort: 443,
    disableStats: true,
    encrypted: true,
});
```

Buradaki yapılandırmada **VITE_ABLY_PUBLIC_KEY** değişkenine dikkat edin. Bu değişkenin değeri, Ably **public key**’iniz olmalıdır. Bu anahtar, Ably key’inizdeki `:` karakterinden önceki kısımdır.

Echo yapılandırmasını tamamladıktan sonra, varlıklarınızı derleyin:

```bash
npm run dev
```

Uygulamanızın **JavaScript** varlıklarının derlenmesi hakkında daha fazla bilgi için **Vite** belgelerine bakabilirsiniz.


<br>



## Concept Overview

<br>



**Laravel’in event broadcasting** özelliği, **WebSocket** tabanlı bir yaklaşım kullanarak sunucu tarafındaki Laravel event’lerinizi istemci tarafındaki JavaScript uygulamanıza yayınlamanızı sağlar. Şu anda Laravel, **Laravel Reverb**, **Pusher Channels** ve **Ably** sürücüleriyle birlikte gelir. Bu event’ler, istemci tarafında **Laravel Echo** JavaScript paketi aracılığıyla kolayca dinlenebilir.

Event’ler “kanallar” (**channels**) üzerinden yayınlanır. Bu kanallar **public** (herkese açık) veya **private** (özel) olarak tanımlanabilir. Uygulamanızın herhangi bir ziyaretçisi bir public kanala kimlik doğrulaması olmadan abone olabilir; ancak private bir kanala abone olabilmek için kullanıcının kimliği doğrulanmış ve yetkilendirilmiş olması gerekir.

<br>



### Using an Example Application

Event broadcasting’in her bileşenine girmeden önce, bir örnek üzerinden genel bir bakış yapalım. 

Bir **e-ticaret** uygulamanız olduğunu ve kullanıcıların siparişlerinin gönderim durumunu görüntüleyebildiği bir sayfa olduğunu varsayalım. Uygulama, gönderim durumu güncellendiğinde bir **OrderShipmentStatusUpdated** event’i tetikliyor olsun:

```php
use App\Events\OrderShipmentStatusUpdated;
 
OrderShipmentStatusUpdated::dispatch($order);
````

<br>



### The ShouldBroadcast Interface

Bir kullanıcı siparişini görüntülerken sayfayı yenilemeden güncellemeleri görebilmesini istiyoruz. Bunun için, event’in **ShouldBroadcast** interface’iyle işaretlenmesi gerekir. Bu, event tetiklendiğinde Laravel’e onu yayınlaması gerektiğini belirtir:

```php
<?php
 
namespace App\Events;
 
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
 
class OrderShipmentStatusUpdated implements ShouldBroadcast
{
    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $order;
}
```

**ShouldBroadcast** interface’i, event’in **broadcastOn** metodunu tanımlamasını gerektirir. Bu metod, event’in hangi kanallar üzerinden yayınlanacağını belirler. Bu metodun boş bir hali, oluşturulan event sınıflarında zaten bulunur; sadece detaylarını doldurmanız gerekir.

Yalnızca siparişin sahibinin güncellemeleri görebilmesini istediğimiz için, event’i siparişe bağlı özel bir kanalda yayınlayacağız:

```php
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
 
/**
 * Get the channel the event should broadcast on.
 */
public function broadcastOn(): Channel
{
    return new PrivateChannel('orders.'.$this->order->id);
}
```

Bir event’in birden fazla kanalda yayınlanmasını isterseniz, bir dizi döndürebilirsiniz:

```php
use Illuminate\Broadcasting\PrivateChannel;
 
/**
 * Get the channels the event should broadcast on.
 *
 * @return array<int, \Illuminate\Broadcasting\Channel>
 */
public function broadcastOn(): array
{
    return [
        new PrivateChannel('orders.'.$this->order->id),
        // ...
    ];
}
```

<br>



### Authorizing Channels

Kullanıcıların private kanallarda dinleme yapabilmesi için yetkilendirilmiş olması gerekir. Kanal yetkilendirme kurallarını `routes/channels.php` dosyasında tanımlayabilirsiniz. Örneğin, `private orders.1` kanalında dinleme yapmak isteyen kullanıcının gerçekten o siparişi oluşturduğunu doğrulamamız gerekir:

```php
use App\Models\Order;
use App\Models\User;
 
Broadcast::channel('orders.{orderId}', function (User $user, int $orderId) {
    return $user->id === Order::findOrNew($orderId)->user_id;
});
```

`channel` metodu iki parametre alır: kanalın adı ve kullanıcının bu kanalda dinleme yetkisine sahip olup olmadığını belirten `true` veya `false` döndüren bir callback.

Tüm yetkilendirme callback’leri, ilk argüman olarak kimliği doğrulanmış kullanıcıyı, sonraki argümanlar olarak ise wildcard parametreleri alır. Yukarıdaki örnekte, `{orderId}` ifadesi kanal adının ID kısmının bir wildcard olduğunu belirtir.

<br>



### Listening for Event Broadcasts

Şimdi geriye sadece event’i JavaScript tarafında dinlemek kalıyor. Bunu **Laravel Echo** kullanarak kolayca yapabiliriz. Laravel Echo’nun **React** ve **Vue** için yerleşik **hooks**’ları vardır ve varsayılan olarak event’in tüm public özellikleri broadcast edilir:

```javascript
import { useEcho } from "@laravel/echo-react";
 
useEcho(
    `orders.${orderId}`,
    "OrderShipmentStatusUpdated",
    (e) => {
        console.log(e.order);
    },
);
```

<br>



### Defining Broadcast Events

Bir event’in yayınlanacağını Laravel’e bildirmek için, event sınıfında **Illuminate\Contracts\Broadcasting\ShouldBroadcast** interface’ini uygulamanız gerekir. Bu interface, framework tarafından oluşturulan tüm event sınıflarına zaten import edilmiştir, bu nedenle kolayca ekleyebilirsiniz.

**ShouldBroadcast** interface’i yalnızca bir metot gerektirir: **broadcastOn**. Bu metod, event’in hangi kanallar üzerinden yayınlanacağını döndürmelidir. Kanallar **Channel**, **PrivateChannel** veya **PresenceChannel** örnekleri olabilir.

```php
<?php
 
namespace App\Events;
 
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
 
class ServerCreated implements ShouldBroadcast
{
    use SerializesModels;
 
    public function __construct(
        public User $user,
    ) {}
 
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->user->id),
        ];
    }
}
```

Bu interface’i uyguladıktan sonra, event’i normal şekilde tetiklemeniz yeterlidir. Tetiklendiğinde, kuyruğa alınan bir iş (**queued job**) otomatik olarak event’i belirttiğiniz broadcast sürücüsünü kullanarak yayınlar.

<br>



### Broadcast Name

Varsayılan olarak, Laravel event’i sınıf adını kullanarak yayınlar. Ancak yayın adını özelleştirmek isterseniz **broadcastAs** metodunu tanımlayabilirsiniz:

```php
/**
 * The event's broadcast name.
 */
public function broadcastAs(): string
{
    return 'server.created';
}
```

Yayın adını **broadcastAs** ile özelleştirirseniz, **Echo** dinleyicinizde başına bir nokta (`.`) koyarak tanımlamalısınız. Bu, Echo’ya uygulamanın namespace’ini event adına eklememesi gerektiğini söyler:

```javascript
.listen('.server.created', function (e) {
    // ...
});
```

<br>



### Broadcast Data

Bir event yayınlandığında, tüm public özellikleri otomatik olarak serileştirilir ve event yükü (**payload**) olarak gönderilir. Bu sayede, JavaScript tarafında bu verilere erişebilirsiniz.

Örneğin, event’inizde bir `public $user` özelliği varsa ve bu özellik bir Eloquent model içeriyorsa, yayınlanan payload şu şekilde olur:

```json
{
    "user": {
        "id": 1,
        "name": "Patrick Stewart"
    }
}
```

Daha özel bir payload tanımlamak isterseniz, **broadcastWith** metodunu ekleyebilirsiniz:

```php
/**
 * Get the data to broadcast.
 *
 * @return array<string, mixed>
 */
public function broadcastWith(): array
{
    return ['id' => $this->user->id];
}
```

<br>



### Broadcast Queue

Varsayılan olarak, her broadcast event, `queue.php` yapılandırma dosyasındaki varsayılan **queue connection** ve **default queue** üzerinde çalıştırılır. Yayınlayıcı tarafından kullanılacak bağlantı veya kuyruğu özelleştirmek isterseniz, event sınıfınıza aşağıdaki özellikleri ekleyebilirsiniz:

```php
public $connection = 'redis';
public $queue = 'default';
```

Alternatif olarak, kuyruğun adını **broadcastQueue** metoduyla belirtebilirsiniz:

```php
public function broadcastQueue(): string
{
    return 'default';
}
```

Event’inizi varsayılan queue driver yerine **sync queue** ile yayınlamak isterseniz, **ShouldBroadcastNow** interface’ini kullanabilirsiniz:

```php
<?php
 
namespace App\Events;
 
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
 
class OrderShipmentStatusUpdated implements ShouldBroadcastNow
{
    // ...
}
```

<br>



### Broadcast Conditions

Bazen event’in yalnızca belirli bir koşul doğru olduğunda yayınlanmasını isteyebilirsiniz. Bunun için **broadcastWhen** metodunu kullanabilirsiniz:

```php
public function broadcastWhen(): bool
{
    return $this->order->value > 100;
}
```

<br>



### Broadcasting and Database Transactions

Bir event, veritabanı işlemi (**database transaction**) içinde tetiklenirse, işlem tamamlanmadan önce kuyruğa alınmış iş çalışabilir. Bu durumda, model veya kayıtlar henüz veritabanına işlenmemiş olabilir ve event beklenmedik hatalara yol açabilir.

Eğer `queue` bağlantınızın `after_commit` seçeneği `false` ise, event’in tüm işlemler tamamlandıktan sonra yayınlanmasını sağlamak için **ShouldDispatchAfterCommit** interface’ini uygulayabilirsiniz:

```php
<?php
 
namespace App\Events;
 
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Queue\SerializesModels;
 
class ServerCreated implements ShouldBroadcast, ShouldDispatchAfterCommit
{
    use SerializesModels;
}
```

Bu konudaki olası sorunları çözmek için, **queued jobs** ve **database transactions** belgelerini inceleyebilirsiniz.



<br>



## Authorizing Channels

<br>



### Private Channels

**Private channels**, kimliği doğrulanmış kullanıcının gerçekten o kanalı dinleyip dinleyemeyeceğini kontrol etmenizi gerektirir. Bu işlem, kanal adını içeren bir **HTTP isteği** gönderilerek gerçekleştirilir ve uygulamanız, kullanıcının bu kanalı dinleme yetkisine sahip olup olmadığını belirler. 

**Laravel Echo** kullanırken, private kanallara abone olma isteğinin yetkilendirme isteği otomatik olarak yapılır.

Yayınlama etkinleştirildiğinde, Laravel otomatik olarak yetkilendirme isteklerini işlemek için `/broadcasting/auth` rotasını kaydeder. Bu rota, **web middleware group** içerisinde yer alır.

<br>



### Defining Authorization Callbacks

Bir kullanıcının belirli bir kanalı dinleyip dinleyemeyeceğini belirleyen mantığı tanımlamamız gerekir. Bu mantık, `install:broadcasting` Artisan komutu tarafından oluşturulan `routes/channels.php` dosyasında tanımlanır. Bu dosyada, **Broadcast::channel** metodunu kullanarak kanal yetkilendirme callback’lerini kaydedebilirsiniz:

```php
use App\Models\User;
 
Broadcast::channel('orders.{orderId}', function (User $user, int $orderId) {
    return $user->id === Order::findOrNew($orderId)->user_id;
});
````

`channel` metodu iki argüman alır: kanalın adı ve kullanıcının o kanalda dinleme yetkisine sahip olup olmadığını belirleyen `true` veya `false` döndüren bir callback.

Tüm yetkilendirme callback’leri, ilk argüman olarak **kimliği doğrulanmış kullanıcıyı**, ardından wildcard parametreleri alır. Yukarıdaki örnekte, `{orderId}` ifadesi kanal adındaki “ID” kısmının bir wildcard olduğunu belirtir.

Uygulamanızdaki tüm yayın kanalı yetkilendirme callback’lerini görmek için şu komutu çalıştırabilirsiniz:

```bash
php artisan channel:list
```

<br>



### Authorization Callback Model Binding

Tıpkı HTTP rotalarında olduğu gibi, kanal rotaları da **route model binding** kullanabilir. Örneğin, bir **order ID** yerine doğrudan bir **Order** modeli isteyebilirsiniz:

```php
use App\Models\Order;
use App\Models\User;
 
Broadcast::channel('orders.{order}', function (User $user, Order $order) {
    return $user->id === $order->user_id;
});
```

HTTP route model binding’den farklı olarak, kanal model binding **scoping**’i otomatik olarak desteklemez. Ancak çoğu durumda bu bir sorun değildir çünkü kanallar genellikle tek bir modelin benzersiz anahtarına göre tanımlanır.

<br>



### Authorization Callback Authentication

Private ve **presence** broadcast kanalları, varsayılan kimlik doğrulama guard’ınızı kullanarak kullanıcıyı doğrular. Kullanıcı kimliği doğrulanmamışsa, kanal yetkilendirmesi otomatik olarak reddedilir ve callback hiç çalıştırılmaz.

Gerekirse birden fazla guard belirtebilirsiniz:

```php
Broadcast::channel('channel', function () {
    // ...
}, ['guards' => ['web', 'admin']]);
```

<br>



### Defining Channel Classes

Uygulamanız birçok farklı kanal kullanıyorsa, `routes/channels.php` dosyanız kalabalıklaşabilir. Bu durumda, **closure**’lar yerine **channel classes** kullanabilirsiniz.

Yeni bir kanal sınıfı oluşturmak için:

```bash
php artisan make:channel OrderChannel
```

Bu komut, yeni sınıfı `App/Broadcasting` dizinine yerleştirir. Ardından, kanalınızı `routes/channels.php` dosyasında kaydedin:

```php
use App\Broadcasting\OrderChannel;
 
Broadcast::channel('orders.{order}', OrderChannel::class);
```

Kanal sınıfınızın yetkilendirme mantığını **join** metodunda tanımlayabilirsiniz. Bu metod, closure içinde tanımlayacağınız aynı mantığı içerir. Ayrıca model binding’i de kullanabilirsiniz:

```php
<?php
 
namespace App\Broadcasting;
 
use App\Models\Order;
use App\Models\User;
 
class OrderChannel
{
    public function __construct() {}
 
    public function join(User $user, Order $order): array|bool
    {
        return $user->id === $order->user_id;
    }
}
```

Tıpkı diğer Laravel sınıfları gibi, channel class’lar da **service container** tarafından otomatik olarak çözümlenir. Bu nedenle, constructor’ınızda gerekli bağımlılıkları type-hint olarak belirtebilirsiniz.

<br>



## Broadcasting Events

Bir event tanımladıktan ve onu **ShouldBroadcast** interface’i ile işaretledikten sonra, sadece **dispatch** metodu ile tetiklemeniz yeterlidir. Event dispatcher, event’in yayınlanabilir olduğunu algılar ve kuyruğa alarak yayınlar:

```php
use App\Events\OrderShipmentStatusUpdated;
 
OrderShipmentStatusUpdated::dispatch($order);
```

<br>



### Only to Others

Bazı durumlarda, bir event’in mevcut kullanıcı dışında tüm abonelere yayınlanmasını isteyebilirsiniz. Bunu **broadcast** helper’ı ve **toOthers** metodu ile yapabilirsiniz:

```php
use App\Events\OrderShipmentStatusUpdated;
 
broadcast(new OrderShipmentStatusUpdated($update))->toOthers();
```

Bu yöntem, örneğin bir **task list** uygulamasında yinelenen görevlerin önlenmesine yardımcı olur. Kullanıcı bir görev eklediğinde, uygulama bu görevi hem API yanıtından hem de broadcast event’inden alabilir. **toOthers**, mevcut kullanıcıya yeniden yayınlanmasını engeller.

Bu yöntemi kullanabilmek için event’in **InteractsWithSockets** trait’ini kullanması gerekir.

<br>



### Configuration

Bir **Laravel Echo** örneği başlatıldığında, bağlantıya bir **socket ID** atanır. Eğer **Axios**’u global olarak kullanıyorsanız, bu socket ID otomatik olarak tüm HTTP isteklerine `X-Socket-ID` başlığıyla eklenir.

Laravel, **toOthers** çağrıldığında bu ID’yi başlıktan alır ve bu kimliğe sahip bağlantılara yayın yapılmamasını sağlar.

Global bir Axios örneği kullanmıyorsanız, `X-Socket-ID` başlığını manuel olarak tüm isteklerinize eklemelisiniz:

```javascript
var socketId = Echo.socketId();
```

<br>



### Customizing the Connection

Uygulamanız birden fazla broadcast bağlantısı (**connection**) kullanıyorsa ve event’i varsayılan bağlantı dışında bir yayıncıyla (**broadcaster**) göndermek istiyorsanız, **via** metodunu kullanabilirsiniz:

```php
use App\Events\OrderShipmentStatusUpdated;
 
broadcast(new OrderShipmentStatusUpdated($update))->via('pusher');
```

Alternatif olarak, event’in constructor’ında **broadcastVia** metodunu çağırabilirsiniz. Bunu yapmadan önce, sınıfın **InteractsWithBroadcasting** trait’ini kullandığından emin olun:

```php
<?php
 
namespace App\Events;
 
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
 
class OrderShipmentStatusUpdated implements ShouldBroadcast
{
    use InteractsWithBroadcasting;
 
    public function __construct()
    {
        $this->broadcastVia('pusher');
    }
}
```

<br>



## Anonymous Events

Bazen, özel bir event sınıfı oluşturmadan basit bir olayı frontend’e yayınlamak isteyebilirsiniz. Bu durumda, **Broadcast facade** “anonymous events” yayınlamanıza izin verir:

```php
Broadcast::on('orders.'.$order->id)->send();
```

Bu örnek, aşağıdaki event’i yayınlar:

```json
{
    "event": "AnonymousEvent",
    "data": "[]",
    "channel": "orders.1"
}
```

Event adını ve verilerini özelleştirmek için **as** ve **with** metodlarını kullanabilirsiniz:

```php
Broadcast::on('orders.'.$order->id)
    ->as('OrderPlaced')
    ->with($order)
    ->send();
```

Bu, şu şekilde bir event yayınlar:

```json
{
    "event": "OrderPlaced",
    "data": "{ id: 1, total: 100 }",
    "channel": "orders.1"
}
```

Private veya presence kanalına anonim bir event yayınlamak için:

```php
Broadcast::private('orders.'.$order->id)->send();
Broadcast::presence('channels.'.$channel->id)->send();
```

`send` metodu, event’i uygulamanızın kuyruğuna gönderir. Eğer event’in hemen yayınlanmasını istiyorsanız, `sendNow` metodunu kullanabilirsiniz:

```php
Broadcast::on('orders.'.$order->id)->sendNow();
```

Mevcut kullanıcı dışında tüm abonelere yayın yapmak için **toOthers** kullanılabilir:

```php
Broadcast::on('orders.'.$order->id)
    ->toOthers()
    ->send();
```

<br>



### Rescuing Broadcasts

Kuyruk sunucusu devre dışıysa veya Laravel bir yayın sırasında hata alırsa, genellikle bir istisna (**exception**) fırlatılır ve bu durum kullanıcıya hata olarak yansıyabilir. Ancak yayınlama işlemleri genellikle uygulamanızın temel işleviyle ilgili olmadığı için, bu hataları bastırmak isteyebilirsiniz. Bunun için **ShouldRescue** interface’ini kullanabilirsiniz.

**ShouldRescue** interface’ini uygulayan event’ler, Laravel’in **rescue** helper’ını kullanır. Bu helper, istisnaları yakalar, hata günlüğüne gönderir ve uygulamanın çalışmaya devam etmesini sağlar:

```php
<?php
 
namespace App\Events;
 
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
 
class ServerCreated implements ShouldBroadcast, ShouldRescue
{
    // ...
}
```



<br>



## Receiving Broadcasts

<br>



### Listening for Events

**Laravel Echo** kurulumunu tamamladıktan ve başlattıktan sonra, Laravel uygulamanız tarafından yayınlanan event’leri dinlemeye başlayabilirsiniz.  
İlk olarak, bir kanal örneği almak için **channel** metodunu kullanın, ardından belirli bir event’i dinlemek için **listen** metodunu çağırın:

```javascript
Echo.channel(`orders.${this.order.id}`)
    .listen('OrderShipmentStatusUpdated', (e) => {
        console.log(e.order.name);
    });
````

Bir **private channel** üzerinde event dinlemek istiyorsanız, **private** metodunu kullanabilirsiniz.
Ayrıca, tek bir kanalda birden fazla event’i dinlemek için **listen** metodunu zincirleyebilirsiniz:

```javascript
Echo.private(`orders.${this.order.id}`)
    .listen(/* ... */)
    .listen(/* ... */)
    .listen(/* ... */);
```

<br>



### Stop Listening for Events

Belirli bir event’i dinlemeyi bırakmak (kanaldan ayrılmadan) istiyorsanız, **stopListening** metodunu kullanabilirsiniz:

```javascript
Echo.private(`orders.${this.order.id}`)
    .stopListening('OrderShipmentStatusUpdated');
```

<br>



### Leaving a Channel

Bir kanaldan tamamen ayrılmak için **leaveChannel** metodunu kullanın:

```javascript
Echo.leaveChannel(`orders.${this.order.id}`);
```

Hem kanal hem de ilgili **private** ve **presence** kanallarından ayrılmak istiyorsanız, **leave** metodunu çağırabilirsiniz:

```javascript
Echo.leave(`orders.${this.order.id}`);
```

<br>



### Namespaces

Yukarıdaki örneklerde **App\Events** ad alanını (namespace) belirtmediğimizi fark etmiş olabilirsiniz. Bunun nedeni, **Echo**’nun event’lerin varsayılan olarak **App\Events** içinde bulunduğunu varsaymasıdır.
Ancak, **Echo**’yu başlatırken farklı bir namespace belirtmek isterseniz, yapılandırmaya **namespace** seçeneğini ekleyebilirsiniz:

```javascript
window.Echo = new Echo({
    broadcaster: 'pusher',
    // ...
    namespace: 'App.Other.Namespace'
});
```

Alternatif olarak, **Echo** ile abone olurken event sınıfının başına bir nokta (`.`) ekleyerek tam sınıf adını belirtebilirsiniz:

```javascript
Echo.channel('orders')
    .listen('.Namespace\\Event\\Class', (e) => {
        // ...
    });
```

<br>



## Using React or Vue

**Laravel Echo**, **React** ve **Vue** için event dinlemeyi kolaylaştıran özel **hooks** içerir.
Event dinlemeye başlamak için **useEcho** hook’unu kullanabilirsiniz.
Bu hook, component unmount olduğunda kanaldan otomatik olarak ayrılır.

```javascript
import { useEcho } from "@laravel/echo-react";
 
useEcho(
    `orders.${orderId}`,
    "OrderShipmentStatusUpdated",
    (e) => {
        console.log(e.order);
    },
);
```

Birden fazla event dinlemek istiyorsanız, **useEcho**’ya bir dizi event adı verebilirsiniz:

```javascript
useEcho(
    `orders.${orderId}`,
    ["OrderShipmentStatusUpdated", "OrderShipped"],
    (e) => {
        console.log(e.order);
    },
);
```

Yayınlanan event’in payload verisinin şeklini tanımlayarak **type safety** elde edebilirsiniz:

```typescript
type OrderData = {
    order: {
        id: number;
        user: {
            id: number;
            name: string;
        };
        created_at: string;
    };
};
 
useEcho<OrderData>(`orders.${orderId}`, "OrderShipmentStatusUpdated", (e) => {
    console.log(e.order.id);
    console.log(e.order.user.id);
});
```

<br>



**useEcho** hook’u, component kaldırıldığında (unmount edildiğinde) kanaldan otomatik olarak ayrılır.  
Ancak, gerektiğinde dinlemeyi programatik olarak başlatmak veya durdurmak için döndürülen yardımcı fonksiyonları kullanabilirsiniz:

```javascript
import { useEcho } from "@laravel/echo-react";
 
const { leaveChannel, leave, stopListening, listen } = useEcho(
    `orders.${orderId}`,
    "OrderShipmentStatusUpdated",
    (e) => {
        console.log(e.order);
    },
);
 
// Dinlemeyi bırak (kanaldan ayrılmadan)
stopListening();
 
// Tekrar dinlemeye başla
listen();
 
// Kanaldan ayrıl
leaveChannel();
 
// Kanaldan ve ilişkili private/presence kanallarından ayrıl
leave();
```

<br>



### Connecting to Public Channels

Bir **public channel**’a bağlanmak için **useEchoPublic** hook’unu kullanabilirsiniz:

```javascript
import { useEchoPublic } from "@laravel/echo-react";
 
useEchoPublic("posts", "PostPublished", (e) => {
    console.log(e.post);
});
```

<br>



### Connecting to Presence Channels

Bir **presence channel**’a bağlanmak için **useEchoPresence** hook’unu kullanabilirsiniz:

```javascript
import { useEchoPresence } from "@laravel/echo-react";
 
useEchoPresence("posts", "PostPublished", (e) => {
    console.log(e.post);
});
```

<br>



## Presence Channels

**Presence channels**, private kanalların güvenliğini korurken, aynı zamanda kanala hangi kullanıcıların abone olduğunu da bilmenizi sağlar.
Bu özellik, aynı sayfayı görüntüleyen kullanıcıları listelemek veya bir sohbet odasındaki kullanıcıları göstermek gibi **gerçek zamanlı işbirliği** özelliklerini kolayca oluşturmanıza olanak tanır.

<br>



### Authorizing Presence Channels

Tüm presence kanalları aynı zamanda private kanallardır; dolayısıyla kullanıcıların bu kanallara erişimi yetkilendirilmelidir.
Ancak presence kanallarında, yetkilendirme callback’i kullanıcıya `true` döndürmek yerine kullanıcı hakkında bilgi içeren bir **array** döndürmelidir.

Bu callback’in döndürdüğü veriler, JavaScript tarafındaki presence event dinleyicilerine iletilir.
Eğer kullanıcı yetkili değilse, **false** veya **null** döndürülmelidir:

```php
use App\Models\User;
 
Broadcast::channel('chat.{roomId}', function (User $user, int $roomId) {
    if ($user->canJoinRoom($roomId)) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});
```

<br>



### Joining Presence Channels

Bir presence kanalına katılmak için **Echo**’nun **join** metodunu kullanın.
Bu metod, **PresenceChannel** örneği döndürür ve aşağıdaki event’leri dinlemenize izin verir:

* **here:** Kanala bağlandığınız anda, mevcut kullanıcı listesini alır.
* **joining:** Yeni bir kullanıcı kanala katıldığında tetiklenir.
* **leaving:** Bir kullanıcı kanaldan ayrıldığında tetiklenir.
* **error:** Doğrulama hataları veya JSON ayrıştırma sorunları durumunda tetiklenir.

```javascript
Echo.join(`chat.${roomId}`)
    .here((users) => {
        // Kanalda mevcut kullanıcılar
    })
    .joining((user) => {
        console.log(user.name);
    })
    .leaving((user) => {
        console.log(user.name);
    })
    .error((error) => {
        console.error(error);
    });
```

<br>



### Broadcasting to Presence Channels

Presence kanalları, public veya private kanallar gibi event alabilir.
Örneğin, bir sohbet odasında **NewMessage** event’ini presence kanalına yayınlayabiliriz:

```php
public function broadcastOn(): array
{
    return [
        new PresenceChannel('chat.'.$this->message->room_id),
    ];
}
```

Diğer event’lerde olduğu gibi, mevcut kullanıcıyı hariç tutmak için **toOthers** metodunu kullanabilirsiniz:

```php
broadcast(new NewMessage($message));
 
broadcast(new NewMessage($message))->toOthers();
```

Presence kanalına gönderilen event’leri **Echo**’nun **listen** metoduyla dinleyebilirsiniz:

```javascript
Echo.join(`chat.${roomId}`)
    .here(/* ... */)
    .joining(/* ... */)
    .leaving(/* ... */)
    .listen('NewMessage', (e) => {
        // ...
    });
```

<br>



## Model Broadcasting

<br>



### Introduction

Aşağıdaki **model broadcasting** belgelerini incelemeden önce, Laravel’in genel **event broadcasting** kavramlarını ve yayınlanan event’lerin nasıl oluşturulup dinlendiğini anlamanız önerilir.

Uygulamanızda **Eloquent** modelleri oluşturulduğunda, güncellendiğinde veya silindiğinde event yayınlamak yaygın bir durumdur. Elbette, bu olaylar için özel event sınıfları oluşturup **ShouldBroadcast** interface’i ile işaretleyerek manuel olarak yapabilirsiniz.

Ancak, bu event’leri yalnızca yayınlama amacıyla kullanıyorsanız, her model durumu değişikliğinde özel event sınıfı oluşturmak zahmetli olabilir.  
Bu durumu kolaylaştırmak için Laravel, bir **Eloquent modelinin** durum değişikliklerini **otomatik olarak broadcast** etmesini sağlar.

<br>



### Broadcasting a Model Automatically

Başlamak için, modelinizin **Illuminate\Database\Eloquent\BroadcastsEvents** trait’ini kullanması gerekir.  
Ayrıca, modelin hangi kanallarda yayın yapılacağını belirten bir **broadcastOn** metodunu tanımlamalısınız:

```php
<?php
 
namespace App\Models;
 
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class Post extends Model
{
    use BroadcastsEvents, HasFactory;
 
    /**
     * Get the user that the post belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    /**
     * Get the channels that model events should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel|\Illuminate\Database\Eloquent\Model>
     */
    public function broadcastOn(string $event): array
    {
        return [$this, $this->user];
    }
}
````

Bu trait ve metodu ekledikten sonra model, şu olaylarda otomatik olarak broadcast yapmaya başlar:

* **created** (oluşturulduğunda)
* **updated** (güncellendiğinde)
* **deleted** (silindiğinde)
* **trashed** (soft delete uygulandığında)
* **restored** (geri yüklendiğinde)

`broadcastOn` metoduna iletilen `$event` argümanı, modelde meydana gelen olay türünü içerir.
Bu parametreyi kullanarak, modelin hangi olaylarda hangi kanallarda yayın yapacağını koşullu olarak belirleyebilirsiniz:

```php
public function broadcastOn(string $event): array
{
    return match ($event) {
        'deleted' => [],
        default => [$this, $this->user],
    };
}
```

<br>



### Customizing Model Broadcasting Event Creation

Bazen Laravel’in model broadcasting event’ini nasıl oluşturduğunu özelleştirmek isteyebilirsiniz.
Bunu, modelinizde **newBroadcastableEvent** metodunu tanımlayarak yapabilirsiniz.
Bu metod, bir **Illuminate\Database\Eloquent\BroadcastableModelEventOccurred** örneği döndürmelidir:

```php
use Illuminate\Database\Eloquent\BroadcastableModelEventOccurred;
 
protected function newBroadcastableEvent(string $event): BroadcastableModelEventOccurred
{
    return (new BroadcastableModelEventOccurred(
        $this, $event
    ))->dontBroadcastToCurrentUser();
}
```

<br>



## Model Broadcasting Conventions

<br>



### Channel Conventions

Yukarıdaki örnekteki **broadcastOn** metodunun doğrudan `Channel` örnekleri döndürmediğini fark etmiş olabilirsiniz.
Bunun yerine **Eloquent model** örnekleri döndürülmüştür.
Laravel, model örneklerini otomatik olarak **PrivateChannel**’lara dönüştürür.

Kanal adı şu şekilde oluşturulur:

```
App.Models.{ModelName}.{id}
```

Örneğin, `App\Models\User` modelinde `id = 1` olan bir kullanıcı için kanal adı:

```
App.Models.User.1
```

Bu durumda Laravel, otomatik olarak şu kanalı oluşturur:

```php
new Illuminate\Broadcasting\PrivateChannel('App.Models.User.1')
```

Elbette, tam kontrol sahibi olmak isterseniz doğrudan bir `Channel` örneği döndürebilirsiniz:

```php
use Illuminate\Broadcasting\PrivateChannel;
 
public function broadcastOn(string $event): array
{
    return [
        new PrivateChannel('user.'.$this->id)
    ];
}
```

Bir `Channel` örneğine model örneğini geçerseniz, Laravel yukarıdaki isimlendirme kuralını kullanarak kanal adını oluşturur:

```php
return [new Channel($this->user)];
```

Bir modelin kanal adını manuel olarak öğrenmek için şu metodu kullanabilirsiniz:

```php
$user->broadcastChannel(); // App.Models.User.1
```

<br>



### Event Conventions

Model broadcast event’leri, uygulamanızın `App\Events` dizininde tanımlı “gerçek” event sınıflarına ait değildir.
Bu nedenle, Laravel bu event’ler için **otomatik isimlendirme** ve **payload yapısı** kuralları kullanır.

Bir model event’i şu kuralla adlandırılır:

```
{ModelAdı}{EventTürü}
```

Örneğin:

* `App\Models\Post` modelinde bir güncelleme olayı → **PostUpdated**
* `App\Models\User` silindiğinde → **UserDeleted**

**PostUpdated** event’i için payload örneği:

```json
{
    "model": {
        "id": 1,
        "title": "My first post"
    },
    "socket": "someSocketId"
}
```

Event adını veya payload verisini özelleştirmek isterseniz, modelinize **broadcastAs** ve **broadcastWith** metodlarını ekleyebilirsiniz:

```php
public function broadcastAs(string $event): string|null
{
    return match ($event) {
        'created' => 'post.created',
        default => null,
    };
}
 
public function broadcastWith(string $event): array
{
    return match ($event) {
        'created' => ['title' => $this->title],
        default => ['model' => $this],
    };
}
```

<br>



## Listening for Model Broadcasts

Modelinize **BroadcastsEvents** trait’i ekledikten ve **broadcastOn** metodunu tanımladıktan sonra, artık istemci tarafında model broadcast event’lerini dinleyebilirsiniz.

Kanal adı Laravel’in model broadcast kurallarına uygun olmalıdır.
Örneğin, bir `App\Models\User` modeli için:

```javascript
Echo.private(`App.Models.User.${this.user.id}`)
    .listen('.UserUpdated', (e) => {
        console.log(e.model);
    });
```

Event adının başına bir **nokta (.)** eklenmesi gerekir, çünkü bu event belirli bir namespace’e ait değildir.

<br>



### Using React or Vue

**React** veya **Vue** kullanıyorsanız, **Laravel Echo**’nun sunduğu `useEchoModel` hook’unu kullanarak model broadcast’lerini kolayca dinleyebilirsiniz:

```javascript
import { useEchoModel } from "@laravel/echo-react";
 
useEchoModel("App.Models.User", userId, ["UserUpdated"], (e) => {
    console.log(e.model);
});
```

Tip güvenliği sağlamak için modelin payload yapısını da tanımlayabilirsiniz:

```typescript
type User = {
    id: number;
    name: string;
    email: string;
};
 
useEchoModel<User, "App.Models.User">("App.Models.User", userId, ["UserUpdated"], (e) => {
    console.log(e.model.id);
    console.log(e.model.name);
});
```

<br>



## Client Events

**Pusher Channels** kullanıyorsanız, “Client Events” özelliğini uygulamanızın ayarlarından etkinleştirmeniz gerekir.
Bu özellik, Laravel sunucusuna istek göndermeden diğer istemcilere event yayınlamanıza olanak tanır — örneğin “kullanıcı yazıyor” bildirimleri.

Client event göndermek için **whisper** metodunu kullanabilirsiniz:

```javascript
Echo.private(`chat.${roomId}`)
    .whisper('typing', {
        name: this.user.name
    });
```

Client event’leri dinlemek için **listenForWhisper** metodunu kullanın:

```javascript
Echo.private(`chat.${roomId}`)
    .listenForWhisper('typing', (e) => {
        console.log(e.name);
    });
```

<br>



## Notifications

Event broadcasting’i **Laravel Notifications** sistemiyle birleştirerek, kullanıcılar sayfayı yenilemeden bildirimleri gerçek zamanlı olarak alabilirler.
Bu özelliği kullanmadan önce **broadcast notification channel** belgelerini inceleyin.

Bir bildirimi **broadcast channel** üzerinden gönderecek şekilde yapılandırdıktan sonra, **Echo**’nun **notification** metodunu kullanarak dinleyebilirsiniz:

```javascript
Echo.private(`App.Models.User.${userId}`)
    .notification((notification) => {
        console.log(notification.type);
    });
```

Bu örnekte, `App\Models\User` modeline gönderilen tüm broadcast bildirimleri bu callback tarafından alınır.
`routes/channels.php` dosyasında, `App.Models.User.{id}` kanalı için gerekli yetkilendirme callback’i zaten tanımlıdır.

<br>



### Stop Listening for Notifications

Bildirimleri dinlemeyi bırakmak (kanaldan ayrılmadan) istiyorsanız, **stopListeningForNotification** metodunu kullanabilirsiniz:

```javascript
const callback = (notification) => {
    console.log(notification.type);
}
 
// Dinlemeye başla...
Echo.private(`App.Models.User.${userId}`)
    .notification(callback);
 
// Dinlemeyi bırak...
Echo.private(`App.Models.User.${userId}`)
    .stopListeningForNotification(callback);
```




