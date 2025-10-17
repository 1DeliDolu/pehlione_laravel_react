
<br>






## Giriş

E-posta göndermek karmaşık olmak zorunda değildir. Laravel, popüler **Symfony Mailer** bileşeni tarafından desteklenen temiz ve basit bir e-posta API’si sağlar.  
Laravel ve Symfony Mailer, **SMTP**, **Mailgun**, **Postmark**, **Resend**, **Amazon SES** ve **sendmail** üzerinden e-posta göndermeye olanak tanıyan sürücüler (drivers) sunar.  
Bu sayede ister yerel ister bulut tabanlı bir servis üzerinden kolayca e-posta göndermeye başlayabilirsin.

<br>




## Yapılandırma (Configuration)

Laravel’in e-posta servisleri, uygulamanın `config/mail.php` dosyası üzerinden yapılandırılır.  
Bu dosyada tanımlanan her **mailer**, kendi benzersiz yapılandırmasına ve hatta kendi “transport” (iletişim yöntemi) türüne sahip olabilir.  
Böylece uygulaman, farklı e-posta servislerini farklı türde e-postalar için kullanabilir.  
Örneğin, uygulaman **Postmark** ile işlem e-postaları gönderirken, toplu e-postalar için **Amazon SES** kullanabilir.

`config/mail.php` dosyasında, **mailers** adlı bir yapılandırma dizisi bulunur.  
Bu dizi, Laravel tarafından desteklenen başlıca sürücülere ait örnek yapılandırmaları içerir.  
`default` anahtarı ise uygulamanın varsayılan olarak hangi mailer’ı kullanacağını belirler.

<br>




## Sürücü / Transport Ön Gereksinimleri (Driver / Transport Prerequisites)

**Mailgun**, **Postmark** ve **Resend** gibi API tabanlı sürücüler genellikle SMTP sunucularına göre daha basit ve daha hızlıdır.  
Bu nedenle, mümkün olduğunda bu sürücülerden birini kullanman önerilir.

<br>




### Mailgun Driver

Mailgun sürücüsünü kullanmak için aşağıdaki bağımlılıkları Composer aracılığıyla yüklemelisin:

```bash
composer require symfony/mailgun-mailer symfony/http-client
````

Ardından `config/mail.php` dosyasında iki değişiklik yapmalısın.

1. Varsayılan mailer’ı `mailgun` olarak ayarla:

```php
'default' => env('MAIL_MAILER', 'mailgun'),
```

2. Aşağıdaki yapılandırmayı `mailers` dizisine ekle:

```php
'mailgun' => [
    'transport' => 'mailgun',
    // 'client' => [
    //     'timeout' => 5,
    // ],
],
```

Sonrasında `config/services.php` dosyasına şu seçenekleri ekle:

```php
'mailgun' => [
    'domain' => env('MAILGUN_DOMAIN'),
    'secret' => env('MAILGUN_SECRET'),
    'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    'scheme' => 'https',
],
```

Eğer **ABD dışındaki Mailgun bölgesini** kullanıyorsan, uygun endpoint değerini belirtebilirsin:

```php
'mailgun' => [
    'domain' => env('MAILGUN_DOMAIN'),
    'secret' => env('MAILGUN_SECRET'),
    'endpoint' => env('MAILGUN_ENDPOINT', 'api.eu.mailgun.net'),
    'scheme' => 'https',
],
```

<br>




### Postmark Driver

Postmark sürücüsünü kullanmak için aşağıdaki bağımlılıkları yükle:

```bash
composer require symfony/postmark-mailer symfony/http-client
```

`config/mail.php` dosyasında varsayılan mailer’ı `postmark` olarak ayarla.
Sonrasında `config/services.php` dosyasında aşağıdaki yapılandırmanın bulunduğundan emin ol:

```php
'postmark' => [
    'token' => env('POSTMARK_TOKEN'),
],
```

Bir mailer için kullanılacak **message stream** değerini belirtmek istersen, `message_stream_id` seçeneğini tanımlayabilirsin:

```php
'postmark' => [
    'transport' => 'postmark',
    'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
    // 'client' => [
    //     'timeout' => 5,
    // ],
],
```

Bu sayede farklı message stream’lerle birden fazla Postmark mailer oluşturabilirsin.

<br>




### Resend Driver

Resend sürücüsünü kullanmak için Resend’in PHP SDK’sını yükle:

```bash
composer require resend/resend-php
```

Varsayılan mailer’ı `resend` olarak ayarla. Ardından `config/services.php` dosyasında aşağıdaki yapılandırmayı ekle:

```php
'resend' => [
    'key' => env('RESEND_KEY'),
],
```

<br>




### SES Driver (Amazon Simple Email Service)

Amazon SES sürücüsünü kullanmak için öncelikle Amazon AWS SDK for PHP kütüphanesini yüklemelisin:

```bash
composer require aws/aws-sdk-php
```

`config/mail.php` dosyasında varsayılan mailer’ı `ses` olarak ayarla.
Sonrasında `config/services.php` dosyasında aşağıdaki yapılandırmanın bulunduğundan emin ol:

```php
'ses' => [
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
],
```

AWS’nin geçici kimlik bilgilerini (temporary credentials) kullanmak istersen `token` anahtarını ekleyebilirsin:

```php
'ses' => [
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'token' => env('AWS_SESSION_TOKEN'),
],
```

**SES abonelik yönetimi (subscription management)** özellikleriyle etkileşim kurmak için, bir e-posta mesajının `headers` metodunda `X-Ses-List-Management-Options` başlığını döndürebilirsin:

```php
/**
 * Get the message headers.
 */
public function headers(): Headers
{
    return new Headers(
        text: [
            'X-Ses-List-Management-Options' => 'contactListName=MyContactList;topicName=MyTopic',
        ],
    );
}
```

Ek olarak, Laravel’in AWS SDK’nın `SendEmail` metoduna geçeceği ek parametreleri tanımlamak için `options` dizisini kullanabilirsin:

```php
'ses' => [
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'options' => [
        'ConfigurationSetName' => 'MyConfigurationSet',
        'EmailTags' => [
            ['Name' => 'foo', 'Value' => 'bar'],
        ],
    ],
],
```

<br>




## Failover Yapılandırması (Failover Configuration)

Bazen uygulamanın e-posta göndermek için kullandığı dış servis geçici olarak kapalı olabilir.
Bu durumda, birincil sürücü başarısız olduğunda devreye girecek yedek mailer’lar tanımlayabilirsin.

Bunu yapmak için, `failover` transport’unu kullanan bir mailer oluşturmalısın:

```php
'mailers' => [
    'failover' => [
        'transport' => 'failover',
        'mailers' => [
            'postmark',
            'mailgun',
            'sendmail',
        ],
        'retry_after' => 60,
    ],
],
```

Sonrasında `config/mail.php` dosyasında `default` mailer’ı `failover` olarak ayarlamalısın:

```php
'default' => env('MAIL_MAILER', 'failover'),
```

<br>




## Round Robin Yapılandırması (Round Robin Configuration)

`roundrobin` transport, e-posta yükünü birden fazla mailer arasında dağıtmanı sağlar.
Bunu yapmak için `roundrobin` transport’unu kullanan bir mailer tanımlamalısın:

```php
'mailers' => [
    'roundrobin' => [
        'transport' => 'roundrobin',
        'mailers' => [
            'ses',
            'postmark',
        ],
        'retry_after' => 60,
    ],
],
```

Sonrasında varsayılan mailer’ı `roundrobin` olarak ayarlayabilirsin:

```php
'default' => env('MAIL_MAILER', 'roundrobin'),
```

`roundrobin` transport, listedeki mailer’lardan rastgele birini seçer ve sonraki e-postalar için sıradaki mailer’a geçer.
`failover` transport, yüksek erişilebilirlik sağlarken; `roundrobin` load balancing (yük dengeleme) sağlar.

<br>




## Mailable Sınıfları Oluşturma (Generating Mailables)

Laravel uygulamalarında gönderilen her e-posta türü, bir **mailable** sınıfı tarafından temsil edilir.
Bu sınıflar `app/Mail` dizininde saklanır. Eğer bu dizin henüz mevcut değilse, ilk `mailable` sınıfını oluşturduğunda Laravel bunu senin için oluşturur:

```bash
php artisan make:mail OrderShipped
```

<br>




## Mailable Yazma (Writing Mailables)

Bir mailable oluşturduktan sonra, sınıfı açarak içeriğini inceleyebilirsin.
Mailable yapılandırması şu metotlar üzerinden yapılır:

* `envelope` → Konu (subject) ve alıcı bilgilerini tanımlar.
* `content` → Kullanılacak Blade şablonunu belirtir.
* `attachments` → E-posta eklerini (attachments) belirtir.

<br>




## Göndereni Belirleme (Configuring the Sender)

### Envelope Kullanarak

Bir e-postanın kimden gönderileceğini belirtmek için iki yöntem vardır.
İlk olarak, mesajın `envelope` metodunda “from” adresini tanımlayabilirsin:

```php
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
 
public function envelope(): Envelope
{
    return new Envelope(
        from: new Address('jeffrey@example.com', 'Jeffrey Way'),
        subject: 'Order Shipped',
    );
}
```

Ayrıca bir **replyTo** adresi de belirtebilirsin:

```php
return new Envelope(
    from: new Address('jeffrey@example.com', 'Jeffrey Way'),
    replyTo: [
        new Address('taylor@example.com', 'Taylor Otwell'),
    ],
    subject: 'Order Shipped',
);
```
```

<br>




## Global Bir "from" Adresi Kullanma (Using a Global from Address)

Uygulaman tüm e-postalarında aynı "from" (gönderen) adresini kullanıyorsa, her `mailable` sınıfına bu adresi tek tek eklemek zahmetli olabilir.  
Bunun yerine, `config/mail.php` yapılandırma dosyasında global bir "from" adresi tanımlayabilirsin.  
Eğer `mailable` sınıfı içinde farklı bir "from" adresi belirtilmemişse, bu global adres kullanılacaktır:

```php
'from' => [
    'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
    'name' => env('MAIL_FROM_NAME', 'Example'),
],
````

Ayrıca, aynı dosyada global bir **"reply_to"** adresi de tanımlayabilirsin:

```php
'reply_to' => [
    'address' => 'example@example.com',
    'name' => 'App Name',
],
```

<br>




## Görünümü Yapılandırma (Configuring the View)

Bir `mailable` sınıfının `content` metodu içinde, e-postanın içeriğini oluşturmak için kullanılacak görünümü (view) belirleyebilirsin.
Her e-posta genellikle içeriğini oluşturmak için bir **Blade** şablonu kullandığından, Laravel’in güçlü Blade motorunun tüm olanaklarından yararlanabilirsin:

```php
/**
 * Get the message content definition.
 */
public function content(): Content
{
    return new Content(
        view: 'mail.orders.shipped',
    );
}
```

Tüm e-posta şablonlarını saklamak için `resources/views/mail` dizinini oluşturman önerilir; ancak istersen, şablonlarını `resources/views` dizini altında istediğin yere yerleştirebilirsin.

<br>




## Düz Metin E-postalar (Plain Text Emails)

E-postanın düz metin (plain-text) bir sürümünü tanımlamak istersen, `Content` tanımında `text` parametresini belirtebilirsin.
HTML ve düz metin sürümlerini birlikte tanımlamak mümkündür:

```php
public function content(): Content
{
    return new Content(
        view: 'mail.orders.shipped',
        text: 'mail.orders.shipped-text'
    );
}
```

Açıklık açısından, `html` parametresi `view` parametresinin bir takma adıdır:

```php
return new Content(
    html: 'mail.orders.shipped',
    text: 'mail.orders.shipped-text'
);
```

<br>




## Görünüme Veri Aktarma (View Data)

### Public Özellikler Üzerinden (Via Public Properties)

Genellikle, e-posta şablonuna HTML içeriğini oluştururken kullanabileceğin bazı verileri göndermek istersin.
Veriyi görünüme aktarmanın iki yolu vardır.
İlk olarak, `mailable` sınıfında tanımlanmış **public** özellikler otomatik olarak görünüme aktarılır.
Bu nedenle, veriyi sınıfın yapıcısına (constructor) aktarabilir ve public özelliklere atayabilirsin:

```php
<?php
 
namespace App\Mail;
 
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
 
class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;
 
    public function __construct(
        public Order $order,
    ) {}
 
    public function content(): Content
    {
        return new Content(
            view: 'mail.orders.shipped',
        );
    }
}
```

Artık `$order` değişkeni Blade görünümünde otomatik olarak kullanılabilir:

```blade
<div>
    Price: {{ $order->price }}
</div>
```

<br>




### with Parametresi Üzerinden (Via the with Parameter)

Eğer veriyi görünüme göndermeden önce biçimlendirmek veya dönüştürmek istiyorsan, `Content` tanımındaki `with` parametresini kullanabilirsin.
Bu durumda veriyi `protected` veya `private` özelliklerde saklayabilir ve sadece biçimlendirilmiş haliyle görünüme gönderebilirsin:

```php
<?php
 
namespace App\Mail;
 
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
 
class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;
 
    public function __construct(
        protected Order $order,
    ) {}
 
    public function content(): Content
    {
        return new Content(
            view: 'mail.orders.shipped',
            with: [
                'orderName' => $this->order->name,
                'orderPrice' => $this->order->price,
            ],
        );
    }
}
```

Artık `with` parametresiyle gönderilen veriler Blade şablonunda kullanılabilir:

```blade
<div>
    Price: {{ $orderPrice }}
</div>
```

<br>




## Ekler (Attachments)

Bir e-postaya dosya eklemek için `attachments` metodundan dönen diziye eklemeler yaparsın.
Öncelikle, `Attachment` sınıfının `fromPath` metodunu kullanarak bir dosya ekleyebilirsin:

```php
use Illuminate\Mail\Mailables\Attachment;
 
public function attachments(): array
{
    return [
        Attachment::fromPath('/path/to/file'),
    ];
}
```

Dosya eklerken, dosya adını ve MIME türünü belirtmek için `as` ve `withMime` metotlarını kullanabilirsin:

```php
public function attachments(): array
{
    return [
        Attachment::fromPath('/path/to/file')
            ->as('name.pdf')
            ->withMime('application/pdf'),
    ];
}
```

<br>




### Diskten Dosya Eklemek (Attaching Files From Disk)

Bir dosyayı dosya sistemi disklerinden birine kaydettiysen, bu dosyayı `fromStorage` metodu ile e-postaya ekleyebilirsin:

```php
public function attachments(): array
{
    return [
        Attachment::fromStorage('/path/to/file'),
    ];
}
```

Ek olarak, dosya adını ve MIME türünü de belirtebilirsin:

```php
public function attachments(): array
{
    return [
        Attachment::fromStorage('/path/to/file')
            ->as('name.pdf')
            ->withMime('application/pdf'),
    ];
}
```

Varsayılan disk dışında bir diskten dosya eklemek istersen, `fromStorageDisk` metodunu kullanabilirsin:

```php
public function attachments(): array
{
    return [
        Attachment::fromStorageDisk('s3', '/path/to/file')
            ->as('name.pdf')
            ->withMime('application/pdf'),
    ];
}
```

<br>




### Ham Veri Ekleri (Raw Data Attachments)

Eğer bellekte oluşturulmuş bir dosyayı (örneğin PDF) diske kaydetmeden e-postaya eklemek istersen, `fromData` metodunu kullanabilirsin.
Bu metod, ham veri baytlarını döndüren bir closure ve eklenecek dosya adını kabul eder:

```php
public function attachments(): array
{
    return [
        Attachment::fromData(fn () => $this->pdf, 'Report.pdf')
            ->withMime('application/pdf'),
    ];
}
```

<br>




## Inline Ekler (Inline Attachments)

E-postalara gömülü (inline) resimler eklemek genellikle zordur, ancak Laravel bunu oldukça kolay hale getirir.
E-posta şablonunda `$message->embed()` metodunu kullanarak görselleri inline olarak ekleyebilirsin.
Laravel, `$message` değişkenini otomatik olarak tüm e-posta şablonlarına sağlar:

```html
<body>
    Here is an image:
    <img src="{{ $message->embed($pathToImage) }}">
</body>
```

> Not: `$message` değişkeni **plain-text** şablonlarında mevcut değildir çünkü bu şablonlar inline ekleri desteklemez.

<br>




### Ham Veri Gömme (Embedding Raw Data Attachments)

Eğer ham görsel verilerini doğrudan gömmek istersen, `$message->embedData()` metodunu kullanabilirsin.
Bu metod, görsel verisini ve atanacak dosya adını parametre olarak alır:

```html
<body>
    Here is an image from raw data:
    <img src="{{ $message->embedData($data, 'example-image.jpg') }}">
</body>
```

<br>




## Eklenebilir Nesneler (Attachable Objects)

Dosya yolları üzerinden ekleme genellikle yeterlidir, ancak bazı durumlarda eklenecek varlıklar (örneğin fotoğraflar) uygulamada sınıflarla temsil edilir.
Bu durumda, bu sınıfları doğrudan `attachments` metoduna gönderebilmek için **Attachable** arayüzünü (interface) kullanabilirsin.

Sınıfın `Illuminate\Contracts\Mail\Attachable` arayüzünü uygulaması ve `toMailAttachment` metodunu tanımlaması gerekir:

```php
<?php
 
namespace App\Models;
 
use Illuminate\Contracts\Mail\Attachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Attachment;
 
class Photo extends Model implements Attachable
{
    public function toMailAttachment(): Attachment
    {
        return Attachment::fromPath('/path/to/file');
    }
}
```

Bu nesneyi `attachments` metodunda doğrudan döndürebilirsin:

```php
public function attachments(): array
{
    return [$this->photo];
}
```

Dosya, uzak bir disk (örneğin Amazon S3) üzerinde saklanıyorsa, `fromStorage` veya `fromStorageDisk` metotlarını kullanabilirsin:

```php
return Attachment::fromStorage($this->path);
return Attachment::fromStorageDisk('backblaze', $this->path);
```

Bellekteki verilerden ek oluşturmak istersen, `fromData` metodunu kullanabilirsin:

```php
return Attachment::fromData(fn () => $this->content, 'Photo Name');
```

Ekleri özelleştirmek için `as` ve `withMime` metotlarını da kullanabilirsin:

```php
return Attachment::fromPath('/path/to/file')
    ->as('Photo Name')
    ->withMime('image/jpeg');
```

<br>




## Başlıklar (Headers)

Bazı durumlarda, e-postaya özel başlıklar (örneğin `Message-Id`) eklemen gerekebilir.
Bunu yapmak için, `mailable` sınıfında bir `headers` metodu tanımlamalısın.
Bu metod bir `Illuminate\Mail\Mailables\Headers` örneği döndürmelidir.

```php
use Illuminate\Mail\Mailables\Headers;
 
public function headers(): Headers
{
    return new Headers(
        messageId: 'custom-message-id@example.com',
        references: ['previous-message@example.com'],
        text: [
            'X-Custom-Header' => 'Custom Value',
        ],
    );
}
```


<br>




## Etiketler ve Metadata (Tags and Metadata)

Bazı üçüncü taraf e-posta sağlayıcıları (örneğin **Mailgun** ve **Postmark**) mesajlara “etiket” (tag) ve “metadata” eklenmesini destekler.  
Bu özellikler, uygulaman tarafından gönderilen e-postaları gruplamak ve izlemek için kullanılabilir.  
Bir e-posta mesajına etiket ve metadata eklemek için `Envelope` tanımında `tags` ve `metadata` parametrelerini kullanabilirsin:

```php
use Illuminate\Mail\Mailables\Envelope;

/**
 * Get the message envelope.
 */
public function envelope(): Envelope
{
    return new Envelope(
        subject: 'Order Shipped',
        tags: ['shipment'],
        metadata: [
            'order_id' => $this->order->id,
        ],
    );
}
````

Eğer uygulaman **Mailgun** veya **Postmark** sürücüsünü kullanıyorsa, bu servislerin etiket ve metadata desteğiyle ilgili ayrıntılar için kendi belgelerine başvurabilirsin.
Uygulaman **Amazon SES** kullanıyorsa, “tags” eklemek için `metadata` metodunu kullanmalısın.

<br>




## Symfony Mesajını Özelleştirme (Customizing the Symfony Message)

Laravel’in e-posta altyapısı **Symfony Mailer** tarafından desteklenmektedir.
Laravel, e-posta gönderilmeden önce doğrudan Symfony’nin **Message** örneği üzerinde işlem yapmanı sağlar.
Bunu gerçekleştirmek için `Envelope` tanımında `using` parametresini kullanabilirsin:

```php
use Illuminate\Mail\Mailables\Envelope;
use Symfony\Component\Mime\Email;

public function envelope(): Envelope
{
    return new Envelope(
        subject: 'Order Shipped',
        using: [
            function (Email $message) {
                // Symfony Message üzerinde özel işlemler
            },
        ]
    );
}
```

<br>




## Markdown Mailables

**Markdown mailables**, Laravel’in önceden hazırlanmış e-posta şablonlarını ve bileşenlerini kullanarak estetik, duyarlı (responsive) HTML e-postalar oluşturmanı sağlar.
Ayrıca, Laravel aynı anda otomatik olarak düz metin (plain-text) sürümünü de üretir.

<br>




### Markdown Mailables Oluşturma (Generating Markdown Mailables)

Markdown şablonu ile birlikte bir `mailable` oluşturmak için `--markdown` seçeneğini kullanabilirsin:

```bash
php artisan make:mail OrderShipped --markdown=mail.orders.shipped
```

Sonrasında `content` metodunda `view` yerine `markdown` parametresini kullan:

```php
use Illuminate\Mail\Mailables\Content;

public function content(): Content
{
    return new Content(
        markdown: 'mail.orders.shipped',
        with: [
            'url' => $this->orderUrl,
        ],
    );
}
```

<br>




### Markdown Mesajları Yazma (Writing Markdown Messages)

Markdown mailables, Blade bileşenleri ile Markdown sözdizimini birleştirir.
Bu sayede Laravel’in hazır e-posta bileşenlerini kullanarak kolayca e-posta tasarlayabilirsin:

```blade
<x-mail::message>
# Order Shipped

Your order has been shipped!

<x-mail::button :url="$url">
View Order
</x-mail::button>

Thanks,<br>




{{ config('app.name') }}
</x-mail::message>
```

> Not: Markdown e-postalarında gereksiz girintiler kullanma. Markdown standardına göre girintili satırlar **code block** olarak yorumlanır.

<br>




### Button Bileşeni (Button Component)

Button bileşeni ortalanmış bir buton bağlantısı oluşturur.
İki parametre alır: `url` ve isteğe bağlı `color`.
Desteklenen renkler: `primary`, `success`, `error`.

```blade
<x-mail::button :url="$url" color="success">
View Order
</x-mail::button>
```

<br>




### Panel Bileşeni (Panel Component)

Panel bileşeni, arka planı biraz farklı bir kutu içinde metin bloğu oluşturur:

```blade
<x-mail::panel>
This is the panel content.
</x-mail::panel>
```

<br>




### Table Bileşeni (Table Component)

Table bileşeni, Markdown tablosunu HTML tablosuna dönüştürür.
Sütun hizalaması, standart Markdown sözdizimi ile desteklenir:

```blade
<x-mail::table>
| Laravel       | Table         | Example       |
| ------------- | :-----------: | ------------: |
| Col 2 is      | Centered      | $10           |
| Col 3 is      | Right-Aligned | $20           |
</x-mail::table>
```

<br>




## Bileşenleri Özelleştirme (Customizing the Components)

Tüm Markdown e-posta bileşenlerini özelleştirmek için kendi uygulamana kopyalayabilirsin.
Bunu yapmak için şu komutu çalıştır:

```bash
php artisan vendor:publish --tag=laravel-mail
```

Bu işlem, bileşenleri `resources/views/vendor/mail` dizinine kopyalar.
Burada `html` ve `text` dizinleri bulunur — her biri bileşenlerin farklı sürümlerini içerir.

<br>




### CSS Özelleştirme (Customizing the CSS)

Yukarıdaki komutun ardından `resources/views/vendor/mail/html/themes` dizininde bir `default.css` dosyası bulunur.
Bu dosyayı düzenleyerek stil değişikliklerini yapabilirsin.
Laravel, bu stilleri otomatik olarak **inline CSS** olarak e-postaya uygular.

Yeni bir tema oluşturmak istersen, `html/themes` dizinine yeni bir CSS dosyası ekle ve `config/mail.php` dosyasındaki `theme` seçeneğini yeni tema adıyla eşleştir.

Belirli bir `mailable` için özel tema belirtmek istersen, `mailable` sınıfında `$theme` özelliğini tanımlayabilirsin.

<br>




## E-Posta Gönderme (Sending Mail)

E-posta göndermek için `Mail` facade’ının `to` metodunu kullan.
Bu metod bir e-posta adresi, bir kullanıcı nesnesi veya kullanıcı koleksiyonu alabilir.
Nesne kullanıldığında, `email` ve `name` özellikleri otomatik olarak alınır.

```php
use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;

Mail::to($request->user())->send(new OrderShipped($order));
```

Birden fazla alıcı tanımlamak için `to`, `cc` ve `bcc` metotlarını zincirleme şekilde kullanabilirsin:

```php
Mail::to($request->user())
    ->cc($moreUsers)
    ->bcc($evenMoreUsers)
    ->send(new OrderShipped($order));
```

<br>




### Alıcılar Üzerinde Döngü (Looping Over Recipients)

Bir liste üzerindeki alıcılara döngüyle e-posta göndermen gerekirse, her iterasyonda **yeni bir mailable örneği** oluşturmalısın:

```php
foreach (['taylor@example.com', 'dries@example.com'] as $recipient) {
    Mail::to($recipient)->send(new OrderShipped($order));
}
```

<br>




### Belirli Bir Mailer ile Gönderim (Sending Mail via a Specific Mailer)

Varsayılan mailer dışında belirli bir yapılandırmayı kullanmak istersen, `mailer` metodunu kullanabilirsin:

```php
Mail::mailer('postmark')
    ->to($request->user())
    ->send(new OrderShipped($order));
```

<br>




## Kuyruğa Alma (Queueing Mail)

E-posta gönderimi uygulama yanıt süresini uzatabileceğinden, çoğu geliştirici e-postaları **kuyrukta** arka planda göndermeyi tercih eder.

### Kuyruklama (Queueing a Mail Message)

```php
Mail::to($request->user())
    ->cc($moreUsers)
    ->bcc($evenMoreUsers)
    ->queue(new OrderShipped($order));
```

### Gecikmeli Kuyruklama (Delayed Message Queueing)

Belirli bir zamanda gönderilmesi gereken e-postalar için `later` metodunu kullan:

```php
Mail::to($request->user())
    ->later(now()->addMinutes(10), new OrderShipped($order));
```

### Belirli Kuyruklara Gönderim (Pushing to Specific Queues)

```php
$message = (new OrderShipped($order))
    ->onConnection('sqs')
    ->onQueue('emails');

Mail::to($request->user())->queue($message);
```

### Varsayılan Olarak Kuyruklama (Queueing by Default)

Bir `mailable` sınıfını her zaman kuyruğa almak istiyorsan, `ShouldQueue` arayüzünü (contract) uygulamalısın:

```php
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderShipped extends Mailable implements ShouldQueue
{
    // ...
}
```

### Transaction Sonrası Gönderim (afterCommit)

Veritabanı işlemleri sırasında e-posta gönderimini geciktirmek için `afterCommit` metodunu kullan:

```php
Mail::to($request->user())->send(
    (new OrderShipped($order))->afterCommit()
);
```

<br>




## Hatalı Kuyruklu E-postalar (Queued Email Failures)

Bir kuyruklu e-posta başarısız olduğunda, `failed` metodu çağrılır:

```php
use Throwable;

public function failed(Throwable $exception): void
{
    // Hata yönetimi
}
```

<br>




## Mailable Görünümünü Render Etme (Rendering Mailables)

Bir `mailable`’ın HTML çıktısını göndermeden görmek istersen:

```php
return (new InvoicePaid($invoice))->render();
```

<br>




## Tarayıcıda Önizleme (Previewing Mailables in the Browser)

Tasarladığın e-postayı tarayıcıda önizlemek için bir rota döndürebilirsin:

```php
Route::get('/mailable', function () {
    $invoice = App\Models\Invoice::find(1);
    return new App\Mail\InvoicePaid($invoice);
});
```

<br>




## Yerelleştirilmiş E-postalar (Localizing Mailables)

Belirli bir dili kullanarak e-posta göndermek için `locale` metodunu kullan:

```php
Mail::to($request->user())->locale('es')->send(new OrderShipped($order));
```

### Kullanıcıya Özel Diller (User Preferred Locales)

`HasLocalePreference` arayüzünü modeline uygularsan, Laravel kullanıcıya özel dili otomatik olarak kullanır:

```php
use Illuminate\Contracts\Translation\HasLocalePreference;

class User extends Model implements HasLocalePreference
{
    public function preferredLocale(): string
    {
        return $this->locale;
    }
}
```

<br>




## Test Etme (Testing)

Laravel, `mailable` içeriğini ve gönderim durumlarını test etmek için çeşitli yardımcı metotlar sunar.

```php
$mailable->assertFrom('jeffrey@example.com');
$mailable->assertTo('taylor@example.com');
$mailable->assertHasSubject('Invoice Paid');
$mailable->assertSeeInHtml('Thanks');
$mailable->assertHasAttachment('/path/to/file');
```

Mail gönderimlerini test ederken:

```php
Mail::fake();

Mail::assertSent(OrderShipped::class);
Mail::assertNotQueued(AnotherMailable::class);
Mail::assertSentTimes(OrderShipped::class, 2);
```

Belirli koşullar altında gönderim doğrulamak için closure kullanabilirsin:

```php
Mail::assertSent(function (OrderShipped $mail) use ($order) {
    return $mail->order->id === $order->id;
});
```

<br>




## Geliştirme Ortamında E-posta (Mail and Local Development)

Geliştirme aşamasında gerçek e-posta göndermek istemezsin.
Bunun yerine:

* **Log Driver:** E-postaları log dosyalarına kaydeder.
* **HELO / Mailtrap / Mailpit:** Test ortamında gerçek e-postayı simüle eder.

**Laravel Sail** kullanıyorsan, Mailpit arayüzüne şu adresten erişebilirsin:
👉 [http://localhost:8025](http://localhost:8025)

<br>




## Global "To" Adresi Kullanımı (Using a Global to Address)

Tüm e-postalar için tek bir hedef adres belirlemek istersen, `Mail::alwaysTo()` metodunu kullanabilirsin.
Genellikle bu metod, bir **Service Provider**’ın `boot` metodunda çağrılır:

```php
use Illuminate\Support\Facades\Mail;

public function boot(): void
{
    if ($this->app->environment('local')) {
        Mail::alwaysTo('taylor@example.com');
    }
}
```

Bu metod, ek "cc" veya "bcc" adreslerini devre dışı bırakır.

<br>




## Olaylar (Events)

Laravel, e-posta gönderim sürecinde iki olay (event) yayınlar:

* `MessageSending`: E-posta gönderilmeden önce tetiklenir.
* `MessageSent`: E-posta başarıyla gönderildikten sonra tetiklenir.

```php
use Illuminate\Mail\Events\MessageSending;

class LogMessage
{
    public function handle(MessageSending $event): void
    {
        // $event->message üzerinde işlem yapılabilir
    }
}
```

<br>




## Özel Transportlar (Custom Transports)

Laravel, kendi transport sistemini genişletmene izin verir.
Yeni bir transport oluşturmak için `Symfony\Component\Mailer\Transport\AbstractTransport` sınıfını genişlet:

```php
class MailchimpTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        // Mailchimp API gönderimi
    }

    public function __toString(): string
    {
        return 'mailchimp';
    }
}
```

Ardından `Mail::extend()` metoduyla kaydet:

```php
Mail::extend('mailchimp', function (array $config = []) {
    $client = new ApiClient;
    $client->setApiKey($config['key']);
    return new MailchimpTransport($client);
});
```

<br>




## Symfony Transportlarını Genişletme (Additional Symfony Transports)

Örneğin, **Brevo (Sendinblue)** desteği eklemek için:

```bash
composer require symfony/brevo-mailer symfony/http-client
```

Daha sonra `Mail::extend()` ile kaydet:

```php
Mail::extend('brevo', function () {
    return (new BrevoTransportFactory)->create(
        new Dsn('brevo+api', 'default', config('services.brevo.key'))
    );
});
```

Ardından `config/mail.php` dosyasında yeni transport’u tanımla:

```php
'brevo' => [
    'transport' => 'brevo',
],
```

---

**Laravel**, yazılım oluşturmanın, dağıtmanın ve izlemenin en üretken yoludur.


