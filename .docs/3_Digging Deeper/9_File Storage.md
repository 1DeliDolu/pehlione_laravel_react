# File Storage

<br>




## Introduction

Laravel, Frank de Jonge tarafından geliştirilen mükemmel Flysystem PHP paketine teşekkürler, güçlü bir dosya sistemi soyutlaması sağlar. Laravel'in Flysystem entegrasyonu, yerel dosya sistemleri, SFTP ve Amazon S3 ile çalışmak için basit sürücüler sunar. Daha da iyisi, API her sistemde aynı kaldığı için bu depolama seçenekleri arasında yerel geliştirme makineniz ile üretim sunucunuz arasında geçiş yapmak son derece kolaydır.

<br>




## Configuration

Laravel’in dosya sistemi yapılandırma dosyası **config/filesystems.php** konumundadır. Bu dosyada, tüm dosya sistemi "disklerinizi" yapılandırabilirsiniz. Her disk, belirli bir depolama sürücüsünü ve depolama konumunu temsil eder. Desteklenen her sürücü için örnek yapılandırmalar bu dosyada yer alır, böylece bunları kendi depolama tercihlerinize ve kimlik bilgilerinize göre düzenleyebilirsiniz.

Yerel sürücü (`local`), Laravel uygulamasını çalıştıran sunucuda depolanan dosyalarla etkileşime girerken, `sftp` depolama sürücüsü SSH anahtar tabanlı FTP için kullanılır. `s3` sürücüsü ise Amazon’un S3 bulut depolama hizmetine yazmak için kullanılır.

İstediğiniz kadar disk yapılandırabilir ve aynı sürücüyü kullanan birden fazla diske sahip olabilirsiniz.

<br>




## The Local Driver

Yerel sürücüyü kullanırken, tüm dosya işlemleri **filesystems** yapılandırma dosyasında tanımlanan kök dizine göre gerçekleştirilir. Varsayılan olarak bu değer **storage/app/private** dizinidir. Dolayısıyla, aşağıdaki yöntem **storage/app/private/example.txt** dosyasına yazar:

```php
use Illuminate\Support\Facades\Storage;

Storage::disk('local')->put('example.txt', 'Contents');
````

<br>




## The Public Disk

Uygulamanızın **filesystems** yapılandırma dosyasında bulunan `public` diski, genel olarak erişilebilir olacak dosyalar için tasarlanmıştır. Varsayılan olarak, `public` diski `local` sürücüsünü kullanır ve dosyaları **storage/app/public** dizininde saklar.

Eğer `public` diskiniz `local` sürücüsünü kullanıyorsa ve bu dosyaların web üzerinden erişilebilir olmasını istiyorsanız, **storage/app/public** kaynak dizininden **public/storage** hedef dizinine sembolik bir bağlantı oluşturmanız gerekir:

Bu sembolik bağlantıyı oluşturmak için aşağıdaki Artisan komutunu kullanabilirsiniz:

```bash
php artisan storage:link
```

Bir dosya depolandıktan ve sembolik bağlantı oluşturulduktan sonra, dosyalara **asset** helper’ı ile URL oluşturabilirsiniz:

```php
echo asset('storage/file.txt');
```

Ek sembolik bağlantıları **filesystems** yapılandırma dosyanıza ekleyebilirsiniz. Yapılandırılmış bağlantıların tümü `storage:link` komutunu çalıştırdığınızda oluşturulacaktır:

```php
'links' => [
    public_path('storage') => storage_path('app/public'),
    public_path('images') => storage_path('app/images'),
],
```

Tanımlı sembolik bağlantıları kaldırmak için şu komutu kullanabilirsiniz:

```bash
php artisan storage:unlink
```

<br>




## Driver Prerequisites

### S3 Driver Configuration

S3 sürücüsünü kullanmadan önce, Composer paket yöneticisi aracılığıyla **Flysystem S3** paketini yüklemeniz gerekir:

```bash
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

Bir S3 disk yapılandırma dizisi **config/filesystems.php** dosyasında bulunur. Genellikle, S3 bilgilerinizi ve kimlik bilgilerinizi aşağıdaki ortam değişkenlerini kullanarak yapılandırmalısınız:

```
AWS_ACCESS_KEY_ID=<your-key-id>
AWS_SECRET_ACCESS_KEY=<your-secret-access-key>
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=<your-bucket-name>
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Kolaylık sağlamak için bu ortam değişkenleri AWS CLI tarafından kullanılan adlandırma biçimine uygundur.

<br>




### FTP Driver Configuration

FTP sürücüsünü kullanmadan önce, Composer aracılığıyla **Flysystem FTP** paketini yüklemeniz gerekir:

```bash
composer require league/flysystem-ftp "^3.0"
```

Laravel’in Flysystem entegrasyonları FTP ile mükemmel çalışır; ancak varsayılan **config/filesystems.php** dosyasında örnek bir yapılandırma bulunmaz. FTP dosya sistemi yapılandırmanız gerekiyorsa, aşağıdaki örneği kullanabilirsiniz:

```php
'ftp' => [
    'driver' => 'ftp',
    'host' => env('FTP_HOST'),
    'username' => env('FTP_USERNAME'),
    'password' => env('FTP_PASSWORD'),

    // Optional FTP Settings...
    // 'port' => env('FTP_PORT', 21),
    // 'root' => env('FTP_ROOT'),
    // 'passive' => true,
    // 'ssl' => true,
    // 'timeout' => 30,
],
```

<br>




### SFTP Driver Configuration

SFTP sürücüsünü kullanmadan önce, Composer aracılığıyla **Flysystem SFTP** paketini yüklemeniz gerekir:

```bash
composer require league/flysystem-sftp-v3 "^3.0"
```

Laravel’in Flysystem entegrasyonları SFTP ile mükemmel çalışır; ancak varsayılan yapılandırma dosyasında örnek bir yapılandırma bulunmaz. SFTP dosya sistemi yapılandırmanız gerekiyorsa, aşağıdaki örneği kullanabilirsiniz:

```php
'sftp' => [
    'driver' => 'sftp',
    'host' => env('SFTP_HOST'),

    // Settings for basic authentication...
    'username' => env('SFTP_USERNAME'),
    'password' => env('SFTP_PASSWORD'),

    // Settings for SSH key-based authentication with encryption password...
    'privateKey' => env('SFTP_PRIVATE_KEY'),
    'passphrase' => env('SFTP_PASSPHRASE'),

    // Settings for file / directory permissions...
    'visibility' => 'private',
    'directory_visibility' => 'private',

    // Optional SFTP Settings...
    // 'hostFingerprint' => env('SFTP_HOST_FINGERPRINT'),
    // 'maxTries' => 4,
    // 'port' => env('SFTP_PORT', 22),
    // 'root' => env('SFTP_ROOT', ''),
    // 'timeout' => 30,
    // 'useAgent' => true,
],
```

<br>




## Scoped and Read-Only Filesystems

Scoped disk’ler, tüm yolların otomatik olarak belirli bir yol önekiyle (path prefix) başlatıldığı dosya sistemleri tanımlamanıza olanak tanır. Scoped bir disk oluşturmak için ek bir Flysystem paketini yüklemeniz gerekir:

```bash
composer require league/flysystem-path-prefixing "^3.0"
```

Mevcut herhangi bir dosya sistemi diskinin **scoped** sürümünü tanımlayarak path tabanlı bir disk oluşturabilirsiniz. Örneğin, mevcut `s3` diskinizi belirli bir yol önekine sınırlamak için şu şekilde yapılandırabilirsiniz:

```php
's3-videos' => [
    'driver' => 'scoped',
    'disk' => 's3',
    'prefix' => 'path/to/videos',
],
```

"Read-only" diskler, yazma işlemlerine izin vermeyen dosya sistemi diskleri oluşturmanıza olanak tanır. Bu özelliği kullanmadan önce ek bir Flysystem paketini yüklemeniz gerekir:

```bash
composer require league/flysystem-read-only "^3.0"
```

Daha sonra `read-only` seçeneğini disk yapılandırmanıza ekleyebilirsiniz:

```php
's3-videos' => [
    'driver' => 's3',
    // ...
    'read-only' => true,
],
```

<br>




## Amazon S3 Compatible Filesystems

Varsayılan olarak, uygulamanızın **filesystems** yapılandırma dosyasında bir `s3` diski yapılandırması bulunur. Bu diski sadece Amazon S3 ile değil, aynı zamanda **MinIO**, **DigitalOcean Spaces**, **Vultr Object Storage**, **Cloudflare R2** veya **Hetzner Cloud Storage** gibi S3 uyumlu dosya depolama hizmetleriyle de kullanabilirsiniz.

Genellikle, bu hizmetlerden birini kullanırken kimlik bilgilerini güncelledikten sonra yalnızca **endpoint** yapılandırma seçeneğinin değerini değiştirmeniz gerekir. Bu değer genellikle **AWS_ENDPOINT** ortam değişkeniyle tanımlanır:

```php
'endpoint' => env('AWS_ENDPOINT', 'https://minio:9000'),
```

### MinIO

Laravel’in Flysystem entegrasyonunun MinIO kullanırken doğru URL’leri oluşturabilmesi için, **AWS_URL** ortam değişkenini uygulamanızın yerel URL’sine uygun şekilde tanımlamalısınız ve URL yoluna bucket adını dahil etmelisiniz:

```
AWS_URL=http://localhost:9000/local
```

`temporaryUrl` metodu kullanılarak geçici depolama URL’leri oluşturmak, eğer endpoint istemci tarafından erişilemiyorsa MinIO ile çalışmayabilir.

<br>




## Obtaining Disk Instances

`Storage` facade’ı, yapılandırılmış tüm disklerle etkileşim kurmak için kullanılabilir. Örneğin, `put` metodunu kullanarak varsayılan diske bir avatar kaydedebilirsiniz. Eğer `Storage` facade’ında `disk` metodu çağrılmadan bir metod çağrılırsa, bu metod otomatik olarak varsayılan diske yönlendirilir:

```php
use Illuminate\Support\Facades\Storage;

Storage::put('avatars/1', $content);
```

Uygulamanız birden fazla disk ile etkileşime giriyorsa, belirli bir diskteki dosyalarla çalışmak için `disk` metodunu kullanabilirsiniz:

```php
Storage::disk('s3')->put('avatars/1', $content);
```

<br>




## On-Demand Disks

Bazen, yapılandırma dosyanızda tanımlı olmayan bir disk yapılandırmasını çalışma zamanında oluşturmak isteyebilirsiniz. Bunu yapmak için, `Storage` facade’ının `build` metoduna bir yapılandırma dizisi geçebilirsiniz:

```php
use Illuminate\Support\Facades\Storage;

$disk = Storage::build([
    'driver' => 'local',
    'root' => '/path/to/root',
]);

$disk->put('image.jpg', $content);
```


<br>


## Retrieving Files

`get` metodu, bir dosyanın içeriğini almak için kullanılabilir. Bu metod, dosyanın ham string içeriğini döndürür. Tüm dosya yollarının diskin "root" konumuna göre belirtilmesi gerektiğini unutmayın:

```php
$contents = Storage::get('file.jpg');
````

Eğer alınan dosya JSON içeriyorsa, içeriği almak ve çözümlemek için `json` metodunu kullanabilirsiniz:

```php
$orders = Storage::json('orders.json');
```

`exists` metodu, bir dosyanın diskte mevcut olup olmadığını belirlemek için kullanılabilir:

```php
if (Storage::disk('s3')->exists('file.jpg')) {
    // ...
}
```

`missing` metodu, bir dosyanın diskte eksik olup olmadığını belirlemek için kullanılabilir:

```php
if (Storage::disk('s3')->missing('file.jpg')) {
    // ...
}
```

<br>


## Downloading Files

`download` metodu, kullanıcının tarayıcısının belirtilen dosyayı indirmesini sağlayan bir yanıt oluşturmak için kullanılabilir. Bu metodun ikinci argümanı, kullanıcının göreceği dosya adını belirler. Üçüncü argüman olarak HTTP başlıkları (headers) dizisi de geçebilirsiniz:

```php
return Storage::download('file.jpg');

return Storage::download('file.jpg', $name, $headers);
```

<br>


## File URLs

Belirli bir dosya için URL almak amacıyla `url` metodunu kullanabilirsiniz. `local` sürücüsünü kullanıyorsanız, bu metod genellikle verilen yolun başına `/storage` ekleyerek göreli bir URL döndürür. `s3` sürücüsünü kullanıyorsanız, tam nitelikli (fully qualified) uzak URL döndürülür:

```php
use Illuminate\Support\Facades\Storage;

$url = Storage::url('file.jpg');
```

`local` sürücüsü kullanılırken, genel olarak erişilebilir olması gereken tüm dosyalar **storage/app/public** dizinine yerleştirilmelidir. Ayrıca, **public/storage** konumunda **storage/app/public** dizinine işaret eden bir sembolik bağlantı oluşturmanız gerekir.

`local` sürücüsü kullanıldığında, `url` metodunun döndürdüğü değer URL encode edilmez. Bu nedenle, her zaman geçerli URL’ler oluşturacak dosya adları kullanmanız önerilir.

<br>


## URL Host Customization

`Storage` facade’ı tarafından oluşturulan URL’lerin ana bilgisayar (host) kısmını değiştirmek isterseniz, diskin yapılandırma dizisine `url` seçeneğini ekleyebilir veya değiştirebilirsiniz:

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
    'throw' => false,
],
```

<br>


## Temporary URLs

`temporaryUrl` metodunu kullanarak, `local` ve `s3` sürücüleriyle depolanan dosyalar için geçici URL’ler oluşturabilirsiniz. Bu metod bir yol (path) ve URL’nin ne zaman sona ereceğini belirten bir `DateTime` örneği alır:

```php
use Illuminate\Support\Facades\Storage;

$url = Storage::temporaryUrl(
    'file.jpg', now()->addMinutes(5)
);
```

<br>


### Enabling Local Temporary URLs

Eğer uygulamanızı `local` sürücü için geçici URL desteği eklenmeden önce geliştirmeye başladıysanız, yerel geçici URL’leri etkinleştirmeniz gerekebilir. Bunu yapmak için, **config/filesystems.php** dosyanızdaki `local` disk yapılandırmasına `serve` seçeneğini ekleyin:

```php
'local' => [
    'driver' => 'local',
    'root' => storage_path('app/private'),
    'serve' => true, 
    'throw' => false,
],
```

<br>


### S3 Request Parameters

Ek S3 istek parametreleri belirtmeniz gerekirse, `temporaryUrl` metoduna üçüncü argüman olarak bir parametre dizisi geçebilirsiniz:

```php
$url = Storage::temporaryUrl(
    'file.jpg',
    now()->addMinutes(5),
    [
        'ResponseContentType' => 'application/octet-stream',
        'ResponseContentDisposition' => 'attachment; filename=file2.jpg',
    ]
);
```

<br>


### Customizing Temporary URLs

Belirli bir disk için geçici URL’lerin nasıl oluşturulduğunu özelleştirmeniz gerekiyorsa, `buildTemporaryUrlsUsing` metodunu kullanabilirsiniz. Bu, genellikle geçici URL desteği bulunmayan bir disk üzerinden dosya indirilmesine izin vermek için kullanışlıdır. Bu metod genellikle bir servis sağlayıcının `boot` metodunda çağrılır:

```php
<?php

namespace App\Providers;

use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::disk('local')->buildTemporaryUrlsUsing(
            function (string $path, DateTime $expiration, array $options) {
                return URL::temporarySignedRoute(
                    'files.download',
                    $expiration,
                    array_merge($options, ['path' => $path])
                );
            }
        );
    }
}
```

<br>


## Temporary Upload URLs

Geçici yükleme (upload) URL’leri oluşturma yeteneği yalnızca `s3` sürücüsü tarafından desteklenir.

İstemci tarafı uygulamanızdan doğrudan dosya yüklemek için kullanılabilecek geçici bir URL oluşturmanız gerekiyorsa, `temporaryUploadUrl` metodunu kullanabilirsiniz. Bu metod bir yol ve URL’nin ne zaman sona ereceğini belirten bir `DateTime` örneği alır. Bu metod, yükleme URL’si ve yükleme isteğine dahil edilmesi gereken başlıkları içeren bir dizi döndürür:

```php
use Illuminate\Support\Facades\Storage;

['url' => $url, 'headers' => $headers] = Storage::temporaryUploadUrl(
    'file.jpg', now()->addMinutes(5)
);
```

Bu metod, özellikle Amazon S3 gibi bulut depolama sistemlerine doğrudan dosya yüklemeleri gerektiren **serverless** ortamlarda faydalıdır.

<br>


## File Metadata

Dosyaları okuma ve yazma işlemlerine ek olarak, Laravel dosyaların kendileri hakkında bilgi de sağlayabilir. Örneğin, `size` metodu dosyanın bayt cinsinden boyutunu almak için kullanılabilir:

```php
use Illuminate\Support\Facades\Storage;

$size = Storage::size('file.jpg');
```

`lastModified` metodu, dosyanın en son ne zaman değiştirildiğine dair UNIX zaman damgasını döndürür:

```php
$time = Storage::lastModified('file.jpg');
```

Belirli bir dosyanın MIME türünü almak için `mimeType` metodunu kullanabilirsiniz:

```php
$mime = Storage::mimeType('file.jpg');
```

<br>


## File Paths

Belirli bir dosyanın yolunu almak için `path` metodunu kullanabilirsiniz. `local` sürücüsünü kullanıyorsanız, bu metod dosyanın mutlak yolunu döndürür. `s3` sürücüsünü kullanıyorsanız, dosyanın S3 bucket içindeki göreli yolunu döndürür:

```php
use Illuminate\Support\Facades\Storage;

$path = Storage::path('file.jpg');
```

<br>


## Storing Files

`put` metodu, bir diske dosya içeriği kaydetmek için kullanılabilir. Ayrıca, bu metoda bir PHP resource geçebilirsiniz; bu durumda Flysystem’ın stream desteği kullanılır. Tüm dosya yollarının diskin "root" konumuna göre belirtilmesi gerektiğini unutmayın:

```php
use Illuminate\Support\Facades\Storage;

Storage::put('file.jpg', $contents);

Storage::put('file.jpg', $resource);
```

<br>


### Failed Writes

Eğer `put` metodu (veya diğer yazma işlemleri) dosyayı diske yazamazsa, `false` döner:

```php
if (! Storage::put('file.jpg', $contents)) {
    // Dosya diske yazılamadı...
}
```

Eğer isterseniz, dosya sistemi yapılandırmanızda `throw` seçeneğini `true` olarak belirleyebilirsiniz. Bu durumda `put` gibi "write" metotları, yazma işlemi başarısız olduğunda `League\Flysystem\UnableToWriteFile` hatası fırlatır:

```php
'public' => [
    'driver' => 'local',
    // ...
    'throw' => true,
],
```

<br>


## Prepending and Appending To Files

`prepend` ve `append` metotları, bir dosyanın başına veya sonuna metin eklemenizi sağlar:

```php
Storage::prepend('file.log', 'Prepended Text');

Storage::append('file.log', 'Appended Text');
```

<br>


## Copying and Moving Files

`copy` metodu mevcut bir dosyayı yeni bir konuma kopyalamak için, `move` metodu ise mevcut bir dosyayı taşımak veya yeniden adlandırmak için kullanılır:

```php
Storage::copy('old/file.jpg', 'new/file.jpg');

Storage::move('old/file.jpg', 'new/file.jpg');
```

<br>


## Automatic Streaming

Dosyaları depolamaya stream etmek, bellek kullanımını önemli ölçüde azaltır. Laravel’in bir dosyayı otomatik olarak hedef depolama konumuna stream etmesini isterseniz, `putFile` veya `putFileAs` metodlarını kullanabilirsiniz. Bu metodlar `Illuminate\Http\File` veya `Illuminate\Http\UploadedFile` örneğini kabul eder:

```php
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

// Dosya adı için otomatik benzersiz ID oluşturur...
$path = Storage::putFile('photos', new File('/path/to/photo'));

// Dosya adını manuel belirtin...
$path = Storage::putFileAs('photos', new File('/path/to/photo'), 'photo.jpg');
```

`putFile` metodu, yalnızca bir dizin adı belirtildiğinde, dosya adını otomatik olarak benzersiz bir ID olarak oluşturur. Dosya uzantısı, MIME türüne göre belirlenir. Bu metodun döndürdüğü yol, oluşturulan dosya adını da içerir; böylece bu değeri veritabanınızda saklayabilirsiniz.

Ayrıca, `putFile` ve `putFileAs` metotlarına depolanan dosyanın "visibility" (görünürlük) ayarını belirten bir argüman geçebilirsiniz. Bu, özellikle dosyayı Amazon S3 gibi bir bulut diskine yüklediğinizde ve dosyanın genel olarak erişilebilir olmasını istediğinizde faydalıdır:

```php
Storage::putFile('photos', new File('/path/to/photo'), 'public');
```

<br>


## File Uploads

Web uygulamalarında en yaygın dosya saklama senaryosu, kullanıcıların yüklediği fotoğraf veya belgeleri depolamaktır. Laravel, `store` metodunu kullanarak yüklenen dosyaları kolayca saklamanızı sağlar. Bu metod, dosyayı saklamak istediğiniz yolu parametre olarak alır:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserAvatarController extends Controller
{
    /**
     * Update the avatar for the user.
     */
    public function update(Request $request): string
    {
        $path = $request->file('avatar')->store('avatars');

        return $path;
    }
}
```

Bu örnekle ilgili dikkat edilmesi gereken birkaç nokta vardır. Yalnızca bir dizin adı belirttik, dosya adı belirtmedik. Varsayılan olarak, `store` metodu dosya adı olarak benzersiz bir ID oluşturur. Dosyanın uzantısı MIME türüne göre belirlenir. Metodun döndürdüğü değer, oluşturulan dosya adını da içeren dosya yoludur.

Aynı işlemi `Storage` facade’ının `putFile` metodunu kullanarak da yapabilirsiniz:

```php
$path = Storage::putFile('avatars', $request->file('avatar'));
```

<br>


### Specifying a File Name

Eğer dosya adının otomatik atanmasını istemiyorsanız, `storeAs` metodunu kullanabilirsiniz. Bu metod, yol, dosya adı ve (isteğe bağlı) disk adı parametrelerini alır:

```php
$path = $request->file('avatar')->storeAs(
    'avatars', $request->user()->id
);
```

Aynı işlemi `Storage` facade’ının `putFileAs` metodu ile de yapabilirsiniz:

```php
$path = Storage::putFileAs(
    'avatars', $request->file('avatar'), $request->user()->id
);
```

Yazdırılamayan veya geçersiz Unicode karakterleri otomatik olarak dosya yollarından kaldırılır. Bu nedenle, dosya yollarınızı Laravel’in dosya depolama metotlarına geçirmeden önce temizlemeniz (sanitize etmeniz) önerilir. Dosya yolları `League\Flysystem\WhitespacePathNormalizer::normalizePath` metodu kullanılarak normalize edilir.

<br>


### Specifying a Disk

Varsayılan olarak, `store` metodu yüklenen dosyayı varsayılan diske kaydeder. Başka bir disk belirtmek isterseniz, diskin adını ikinci argüman olarak geçebilirsiniz:

```php
$path = $request->file('avatar')->store(
    'avatars/'.$request->user()->id, 's3'
);
```

Eğer `storeAs` metodunu kullanıyorsanız, disk adını üçüncü argüman olarak geçebilirsiniz:

```php
$path = $request->file('avatar')->storeAs(
    'avatars',
    $request->user()->id,
    's3'
);
```


<br>


## Other Uploaded File Information

Yüklenen dosyanın orijinal adını ve uzantısını almak isterseniz, `getClientOriginalName` ve `getClientOriginalExtension` metotlarını kullanabilirsiniz:

```php
$file = $request->file('avatar');

$name = $file->getClientOriginalName();
$extension = $file->getClientOriginalExtension();
````

Ancak, `getClientOriginalName` ve `getClientOriginalExtension` metotlarının güvenli olmadığını unutmayın, çünkü kötü niyetli bir kullanıcı dosya adını veya uzantısını değiştirebilir. Bu nedenle, genellikle `hashName` ve `extension` metotlarını kullanarak güvenli bir şekilde dosya adı ve uzantı elde etmeniz önerilir:

```php
$file = $request->file('avatar');

$name = $file->hashName(); // Benzersiz, rastgele bir ad oluşturur...
$extension = $file->extension(); // Dosyanın uzantısını MIME türüne göre belirler...
```

<br>


## File Visibility

Laravel’in Flysystem entegrasyonunda “visibility” (görünürlük), birden fazla platformda dosya izinlerini soyutlayan bir kavramdır. Dosyalar “public” (herkese açık) veya “private” (özel) olarak tanımlanabilir. Bir dosya “public” olarak tanımlandığında, genel olarak erişilebilir olacağını belirtmiş olursunuz. Örneğin, `s3` sürücüsünü kullanırken, herkese açık dosyalar için URL’ler alabilirsiniz.

Dosyayı yazarken görünürlüğü `put` metodu ile ayarlayabilirsiniz:

```php
use Illuminate\Support\Facades\Storage;

Storage::put('file.jpg', $contents, 'public');
```

Eğer dosya zaten depolanmışsa, görünürlüğünü `getVisibility` ve `setVisibility` metotları ile alabilir veya ayarlayabilirsiniz:

```php
$visibility = Storage::getVisibility('file.jpg');

Storage::setVisibility('file.jpg', 'public');
```

Yüklenen dosyalarla etkileşime girerken, dosyayı genel erişime açık şekilde depolamak için `storePublicly` ve `storePubliclyAs` metotlarını kullanabilirsiniz:

```php
$path = $request->file('avatar')->storePublicly('avatars', 's3');

$path = $request->file('avatar')->storePubliclyAs(
    'avatars',
    $request->user()->id,
    's3'
);
```

<br>


### Local Files and Visibility

`local` sürücüsü kullanıldığında, `public` görünürlük dizinler için 0755, dosyalar için 0644 izinlerine karşılık gelir. Uygulamanızın **filesystems** yapılandırma dosyasında bu izin eşlemelerini değiştirebilirsiniz:

```php
'local' => [
    'driver' => 'local',
    'root' => storage_path('app'),
    'permissions' => [
        'file' => [
            'public' => 0644,
            'private' => 0600,
        ],
        'dir' => [
            'public' => 0755,
            'private' => 0700,
        ],
    ],
    'throw' => false,
],
```

<br>


## Deleting Files

`delete` metodu, silinecek tek bir dosya adını veya dosya dizisini kabul eder:

```php
use Illuminate\Support\Facades\Storage;

Storage::delete('file.jpg');

Storage::delete(['file.jpg', 'file2.jpg']);
```

Gerekirse, dosyanın silineceği diski de belirtebilirsiniz:

```php
use Illuminate\Support\Facades\Storage;

Storage::disk('s3')->delete('path/file.jpg');
```

<br>


## Directories

### Get All Files Within a Directory

`files` metodu, belirli bir dizin içindeki tüm dosyaların bir dizisini döndürür. Alt dizinlerdeki dosyaları da dahil etmek isterseniz, `allFiles` metodunu kullanabilirsiniz:

```php
use Illuminate\Support\Facades\Storage;

$files = Storage::files($directory);

$files = Storage::allFiles($directory);
```

<br>


### Get All Directories Within a Directory

`directories` metodu, belirli bir dizin içindeki tüm dizinlerin bir dizisini döndürür. Alt dizinleri de dahil etmek isterseniz, `allDirectories` metodunu kullanabilirsiniz:

```php
$directories = Storage::directories($directory);

$directories = Storage::allDirectories($directory);
```

<br>


### Create a Directory

`makeDirectory` metodu, belirtilen dizini ve gerekli alt dizinleri oluşturur:

```php
Storage::makeDirectory($directory);
```

<br>


### Delete a Directory

Son olarak, `deleteDirectory` metodu bir dizini ve içindeki tüm dosyaları silmek için kullanılabilir:

```php
Storage::deleteDirectory($directory);
```

<br>


## Testing

`Storage` facade’ının `fake` metodu, testlerde dosya yükleme işlemlerini basitleştiren sahte bir disk oluşturmanıza olanak tanır. Bu metod, `Illuminate\Http\UploadedFile` sınıfının dosya oluşturma yardımcılarıyla birlikte çalışarak testleri kolaylaştırır. Örneğin:

### Pest / PHPUnit

```php
<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('albums can be uploaded', function () {
    Storage::fake('photos');

    $response = $this->json('POST', '/photos', [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg')
    ]);

    // Bir veya daha fazla dosyanın kaydedildiğini doğrula...
    Storage::disk('photos')->assertExists('photo1.jpg');
    Storage::disk('photos')->assertExists(['photo1.jpg', 'photo2.jpg']);

    // Bir veya daha fazla dosyanın kaydedilmediğini doğrula...
    Storage::disk('photos')->assertMissing('missing.jpg');
    Storage::disk('photos')->assertMissing(['missing.jpg', 'non-existing.jpg']);

    // Belirli bir dizindeki dosya sayısının beklenenle eşleştiğini doğrula...
    Storage::disk('photos')->assertCount('/wallpapers', 2);

    // Belirli bir dizinin boş olduğunu doğrula...
    Storage::disk('photos')->assertDirectoryEmpty('/wallpapers');
});
```

Varsayılan olarak, `fake` metodu geçici dizinindeki tüm dosyaları siler. Eğer bu dosyaları korumak isterseniz, bunun yerine `persistentFake` metodunu kullanabilirsiniz. Dosya yüklemelerini test etme hakkında daha fazla bilgi için HTTP test dokümantasyonundaki dosya yükleme bölümüne bakabilirsiniz.

> **Not:** `image` metodu, PHP’nin **GD** eklentisini gerektirir.

<br>


## Custom Filesystems

Laravel’in Flysystem entegrasyonu varsayılan olarak birkaç sürücüyü destekler; ancak Flysystem bundan ibaret değildir ve birçok farklı depolama sistemi için adaptörlere sahiptir. Laravel uygulamanızda bu ek adaptörlerden birini kullanmak isterseniz, özel bir sürücü (custom driver) oluşturabilirsiniz.

Özel bir dosya sistemi tanımlamak için bir Flysystem adaptörüne ihtiyacınız vardır. Örneğin, topluluk tarafından bakımı yapılan Dropbox adaptörünü projemize ekleyelim:

```bash
composer require spatie/flysystem-dropbox
```

Ardından, uygulamanızın servis sağlayıcılarından birinin `boot` metodunda bu sürücüyü kaydedebilirsiniz. Bunu yapmak için `Storage` facade’ının `extend` metodunu kullanın:

```php
<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ...
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('dropbox', function (Application $app, array $config) {
            $adapter = new DropboxAdapter(new DropboxClient(
                $config['authorization_token']
            ));

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}
```

`extend` metodunun ilk argümanı sürücünün adıdır, ikinci argüman ise `$app` ve `$config` değişkenlerini alan bir **closure**’dır. Bu closure, bir `Illuminate\Filesystem\FilesystemAdapter` örneği döndürmelidir. `$config` değişkeni, belirtilen disk için **config/filesystems.php** dosyasında tanımlanan değerleri içerir.

Eklentinin servis sağlayıcısını oluşturup kaydettikten sonra, `config/filesystems.php` yapılandırma dosyanızda `dropbox` sürücüsünü kullanabilirsiniz.

---

**Laravel**, yazılım geliştirme, dağıtım ve izleme süreçlerini en verimli şekilde gerçekleştirmek için tasarlanmış bir framework’tür.

