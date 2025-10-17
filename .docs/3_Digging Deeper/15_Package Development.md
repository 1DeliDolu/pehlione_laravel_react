# Package Development


<br>


## Introduction

Packages are the primary way of adding functionality to Laravel. Packages might be anything from a great way to work with dates like Carbon or a package that allows you to associate files with Eloquent models like Spatie's Laravel Media Library.

Paketler, Laravel'e işlevsellik eklemenin birincil yoludur. Paketler, tarihlerle çalışmanın harika bir yolu olan Carbon'dan, Eloquent modellerine dosyalar iliştirmenizi sağlayan Spatie'nin Laravel Media Library'sine kadar herhangi bir şey olabilir.

<br>


## Types of Packages

There are different types of packages. Some packages are stand-alone, meaning they work with any PHP framework. Carbon and Pest are examples of stand-alone packages. Any of these packages may be used with Laravel by requiring them in your composer.json file.

Farklı türde paketler vardır. Bazı paketler bağımsızdır, yani herhangi bir PHP framework'ü ile çalışabilirler. Carbon ve Pest, bağımsız paketlere örnektir. Bu paketlerin herhangi biri composer.json dosyanıza dahil edilerek Laravel ile kullanılabilir.

On the other hand, other packages are specifically intended for use with Laravel. These packages may have routes, controllers, views, and configuration specifically intended to enhance a Laravel application. This guide primarily covers the development of those packages that are Laravel specific.

Diğer yandan, bazı paketler özellikle Laravel ile kullanılmak üzere tasarlanmıştır. Bu paketler, bir Laravel uygulamasını geliştirmeye yönelik routes, controllers, views ve configuration içerebilir. Bu rehber, özellikle Laravel'e özgü paketlerin geliştirilmesini ele alır.

<br>


## A Note on Facades

When writing a Laravel application, it generally does not matter if you use contracts or facades since both provide essentially equal levels of testability. However, when writing packages, your package will not typically have access to all of Laravel's testing helpers. If you would like to be able to write your package tests as if the package were installed inside a typical Laravel application, you may use the Orchestral Testbench package.

Bir Laravel uygulaması yazarken, genellikle contracts veya facades kullanmanız fark etmez çünkü her ikisi de temelde aynı düzeyde test edilebilirlik sunar. Ancak paket geliştirirken, paketiniz genellikle Laravel'in test yardımcılarına erişemez. Paket testlerinizi sanki paket tipik bir Laravel uygulamasına kurulmuş gibi yazmak istiyorsanız, Orchestral Testbench paketini kullanabilirsiniz.

<br>


## Package Discovery

A Laravel application's bootstrap/providers.php file contains the list of service providers that should be loaded by Laravel. However, instead of requiring users to manually add your service provider to the list, you may define the provider in the extra section of your package's composer.json file so that it is automatically loaded by Laravel. In addition to service providers, you may also list any facades you would like to be registered:

Bir Laravel uygulamasının bootstrap/providers.php dosyası, Laravel tarafından yüklenmesi gereken service provider listesini içerir. Ancak kullanıcıların service provider’ınızı bu listeye manuel olarak eklemesini istemek yerine, provider’ınızı paketinizin composer.json dosyasının extra bölümünde tanımlayabilirsiniz. Böylece Laravel, paketi yüklediğinde provider’ı otomatik olarak kaydeder. Service provider’lara ek olarak kaydedilmesini istediğiniz facades’leri de listeleyebilirsiniz:

```json
"extra": {
    "laravel": {
        "providers": [
            "Barryvdh\\Debugbar\\ServiceProvider"
        ],
        "aliases": {
            "Debugbar": "Barryvdh\\Debugbar\\Facade"
        }
    }
},
````

Once your package has been configured for discovery, Laravel will automatically register its service providers and facades when it is installed, creating a convenient installation experience for your package's users.

Paketiniz keşif (discovery) için yapılandırıldıktan sonra, Laravel paketi yüklediğinde service provider ve facades’leri otomatik olarak kaydeder, böylece kullanıcılar için kolay bir kurulum deneyimi sunar.

<br>


## Opting Out of Package Discovery

If you are the consumer of a package and would like to disable package discovery for a package, you may list the package name in the extra section of your application's composer.json file:

Bir paketi kullanan tarafsanız ve o paket için package discovery özelliğini devre dışı bırakmak istiyorsanız, uygulamanızın composer.json dosyasının extra bölümünde paket adını listeleyebilirsiniz:

```json
"extra": {
    "laravel": {
        "dont-discover": [
            "barryvdh/laravel-debugbar"
        ]
    }
},
```

You may disable package discovery for all packages using the * character inside of your application's dont-discover directive:

Tüm paketler için package discovery özelliğini devre dışı bırakmak istiyorsanız, uygulamanızın dont-discover yönergesinde * karakterini kullanabilirsiniz:

```json
"extra": {
    "laravel": {
        "dont-discover": [
            "*"
        ]
    }
},
```

<br>


## Service Providers

Service providers are the connection point between your package and Laravel. A service provider is responsible for binding things into Laravel's service container and informing Laravel where to load package resources such as views, configuration, and language files.

Service provider’lar, paketiniz ile Laravel arasındaki bağlantı noktasıdır. Bir service provider, Laravel’in service container’ına nesneleri bağlamaktan ve Laravel’e paket kaynaklarının (örneğin views, configuration, language files) nereden yükleneceğini bildirmekten sorumludur.

A service provider extends the Illuminate\Support\ServiceProvider class and contains two methods: register and boot. The base ServiceProvider class is located in the illuminate/support Composer package, which you should add to your own package's dependencies. To learn more about the structure and purpose of service providers, check out their documentation.

Bir service provider, Illuminate\Support\ServiceProvider sınıfını genişletir ve iki metot içerir: register ve boot. Temel ServiceProvider sınıfı illuminate/support Composer paketinde bulunur; bu paketi kendi paketinizin bağımlılıklarına eklemeniz gerekir. Service provider’ların yapısı ve amacı hakkında daha fazla bilgi için ilgili dokümantasyona bakabilirsiniz.

<br>


## Resources

<br>


### Configuration

Typically, you will need to publish your package's configuration file to the application's config directory. This will allow users of your package to easily override your default configuration options. To allow your configuration files to be published, call the publishes method from the boot method of your service provider:

Genellikle, paketinizin configuration dosyasını uygulamanın config dizinine yayınlamanız gerekir. Bu, paket kullanıcılarının varsayılan yapılandırma seçeneklerinizi kolayca değiştirmesini sağlar. Configuration dosyalarınızın yayınlanmasına izin vermek için, service provider’ınızın boot metodunda publishes metodunu çağırın:

```php
/**
 * Bootstrap any package services.
 */
public function boot(): void
{
    $this->publishes([
        __DIR__.'/../config/courier.php' => config_path('courier.php'),
    ]);
}
```

Now, when users of your package execute Laravel's vendor:publish command, your file will be copied to the specified publish location. Once your configuration has been published, its values may be accessed like any other configuration file:

Artık paket kullanıcıları Laravel’in `vendor:publish` komutunu çalıştırdığında, dosyanız belirtilen konuma kopyalanacaktır. Configuration dosyası yayınlandıktan sonra, değerlerine diğer yapılandırma dosyaları gibi erişilebilir:

```php
$value = config('courier.option');
```

You should not define closures in your configuration files. They cannot be serialized correctly when users execute the config:cache Artisan command.

Configuration dosyalarınızda closure tanımlamamalısınız. Kullanıcılar `config:cache` Artisan komutunu çalıştırdığında bu closure’lar doğru şekilde serileştirilemez.

<br>


### Default Package Configuration

You may also merge your own package configuration file with the application's published copy. This will allow your users to define only the options they actually want to override in the published copy of the configuration file. To merge the configuration file values, use the mergeConfigFrom method within your service provider's register method.

Ayrıca, paketinizin configuration dosyasını uygulamanın yayınlanmış kopyasıyla birleştirebilirsiniz. Bu, kullanıcıların configuration dosyasının yalnızca değiştirmek istedikleri seçenekleri tanımlamalarına olanak tanır. Configuration dosyası değerlerini birleştirmek için service provider’ınızın register metodunda `mergeConfigFrom` metodunu kullanın:

```php
/**
 * Register any package services.
 */
public function register(): void
{
    $this->mergeConfigFrom(
        __DIR__.'/../config/courier.php', 'courier'
    );
}
```

This method only merges the first level of the configuration array. If your users partially define a multi-dimensional configuration array, the missing options will not be merged.

Bu metot yalnızca configuration dizisinin ilk seviyesini birleştirir. Kullanıcılar çok boyutlu bir configuration dizisinin yalnızca bir kısmını tanımlarsa, eksik seçenekler birleştirilmez.

<br>


### Routes

If your package contains routes, you may load them using the loadRoutesFrom method. This method will automatically determine if the application's routes are cached and will not load your routes file if the routes have already been cached:

Paketiniz route’lar içeriyorsa, bunları `loadRoutesFrom` metodu ile yükleyebilirsiniz. Bu metot, uygulamanın route’larının önbelleğe alınıp alınmadığını otomatik olarak kontrol eder ve eğer önbelleğe alınmışsa routes dosyasını yeniden yüklemez:

```php
/**
 * Bootstrap any package services.
 */
public function boot(): void
{
    $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
}
```

<br>


### Migrations

If your package contains database migrations, you may use the publishesMigrations method to inform Laravel that the given directory or file contains migrations. When Laravel publishes the migrations, it will automatically update the timestamp within their filename to reflect the current date and time:

Paketiniz database migration’ları içeriyorsa, Laravel’e belirtilen dizinin veya dosyanın migration içerdiğini bildirmek için `publishesMigrations` metodunu kullanabilirsiniz. Laravel migration’ları yayınladığında, dosya adlarındaki timestamp değerini geçerli tarih ve saatle otomatik olarak günceller:

```php
/**
 * Bootstrap any package services.
 */
public function boot(): void
{
    $this->publishesMigrations([
        __DIR__.'/../database/migrations' => database_path('migrations'),
    ]);
}
```

<br>


### Language Files

If your package contains language files, you may use the loadTranslationsFrom method to inform Laravel how to load them. For example, if your package is named courier, you should add the following to your service provider's boot method:

Paketiniz language dosyaları içeriyorsa, Laravel’e bu dosyaların nasıl yükleneceğini bildirmek için `loadTranslationsFrom` metodunu kullanabilirsiniz. Örneğin, paketinizin adı *courier* ise, service provider’ınızın boot metoduna aşağıdaki satırı eklemelisiniz:

```php
/**
 * Bootstrap any package services.
 */
public function boot(): void
{
    $this->loadTranslationsFrom(__DIR__.'/../lang', 'courier');
}
```

Package translation lines are referenced using the `package::file.line` syntax convention. So, you may load the courier package's welcome line from the messages file like so:

Paket çeviri satırlarına `package::file.line` sözdizimiyle erişilir. Dolayısıyla courier paketindeki *messages* dosyasındaki `welcome` satırına aşağıdaki şekilde erişebilirsiniz:

```php
echo trans('courier::messages.welcome');
```


<br>


### Publishing Views

If you would like to make your views available for publishing to the application's resources/views/vendor directory, you may use the service provider's publishes method. The publishes method accepts an array of package view paths and their desired publish locations:

View’larınızı uygulamanın `resources/views/vendor` dizinine yayınlanabilir hale getirmek istiyorsanız, service provider’ın `publishes` metodunu kullanabilirsiniz. `publishes` metodu, paket view yollarını ve bu view’ların yayınlanacağı hedef konumları içeren bir dizi kabul eder:

```php
/**
 * Bootstrap the package services.
 */
public function boot(): void
{
    $this->loadViewsFrom(__DIR__.'/../resources/views', 'courier');
 
    $this->publishes([
        __DIR__.'/../resources/views' => resource_path('views/vendor/courier'),
    ]);
}
````

Now, when users of your package execute Laravel's `vendor:publish` Artisan command, your package's views will be copied to the specified publish location.

Artık kullanıcılar Laravel’in `vendor:publish` Artisan komutunu çalıştırdığında, paketinizin view’ları belirtilen yayın konumuna kopyalanacaktır.

<br>


### View Components

If you are building a package that utilizes Blade components or placing components in non-conventional directories, you will need to manually register your component class and its HTML tag alias so that Laravel knows where to find the component. You should typically register your components in the boot method of your package's service provider:

Blade component’larını kullanan veya component’larını geleneksel olmayan dizinlerde bulunduran bir paket geliştiriyorsanız, Laravel’in component’i nerede bulacağını bilmesi için component sınıfınızı ve HTML etiket takma adını manuel olarak kaydetmeniz gerekir. Genellikle, component’larınızı paketinizin service provider’ının `boot` metodunda kaydetmelisiniz:

```php
use Illuminate\Support\Facades\Blade;
use VendorPackage\View\Components\AlertComponent;
 
/**
 * Bootstrap your package's services.
 */
public function boot(): void
{
    Blade::component('package-alert', AlertComponent::class);
}
```

Once your component has been registered, it may be rendered using its tag alias:

Component kaydedildikten sonra, HTML etiket takma adı kullanılarak render edilebilir:

```blade
<x-package-alert/>
```

<br>


### Autoloading Package Components

Alternatively, you may use the `componentNamespace` method to autoload component classes by convention. For example, a Nightshade package might have Calendar and ColorPicker components that reside within the `Nightshade\Views\Components` namespace:

Alternatif olarak, `componentNamespace` metodunu kullanarak component sınıflarını belirli bir isimlendirme standardına göre otomatik olarak yükleyebilirsiniz. Örneğin, bir *Nightshade* paketi `Nightshade\Views\Components` namespace’i altında bulunan `Calendar` ve `ColorPicker` component’larına sahip olabilir:

```php
use Illuminate\Support\Facades\Blade;
 
/**
 * Bootstrap your package's services.
 */
public function boot(): void
{
    Blade::componentNamespace('Nightshade\\Views\\Components', 'nightshade');
}
```

This will allow the usage of package components by their vendor namespace using the `package-name::` syntax:

Bu işlem, paket component’larının `package-name::` sözdizimiyle, vendor namespace kullanılarak çağrılmasını sağlar:

```blade
<x-nightshade::calendar />
<x-nightshade::color-picker />
```

Blade will automatically detect the class that's linked to this component by pascal-casing the component name. Subdirectories are also supported using "dot" notation.

Blade, component adını PascalCase biçimine dönüştürerek, bu component’e karşılık gelen sınıfı otomatik olarak algılar. Alt dizinler de "dot" (nokta) gösterimiyle desteklenir.

<br>


### Anonymous Components

If your package contains anonymous components, they must be placed within a `components` directory of your package's "views" directory (as specified by the `loadViewsFrom` method). Then, you may render them by prefixing the component name with the package's view namespace:

Paketiniz anonim (anonymous) component’lar içeriyorsa, bu component’lar `loadViewsFrom` metodunda belirtilen “views” dizininizin altındaki `components` klasörüne yerleştirilmelidir. Daha sonra bu component’lar, component adının önüne paket view namespace’i eklenerek render edilebilir:

```blade
<x-courier::alert />
```

<br>


### "About" Artisan Command

Laravel's built-in `about` Artisan command provides a synopsis of the application's environment and configuration. Packages may push additional information to this command's output via the `AboutCommand` class. Typically, this information may be added from your package service provider's `boot` method:

Laravel’in yerleşik `about` Artisan komutu, uygulamanın ortamı (environment) ve yapılandırması hakkında genel bir özet sağlar. Paketler, `AboutCommand` sınıfı aracılığıyla bu komutun çıktısına ek bilgiler ekleyebilir. Bu bilgi genellikle paketinizin service provider’ının `boot` metodundan eklenir:

```php
use Illuminate\Foundation\Console\AboutCommand;
 
/**
 * Bootstrap any package services.
 */
public function boot(): void
{
    AboutCommand::add('My Package', fn () => ['Version' => '1.0.0']);
}
```

<br>


### Commands

To register your package's Artisan commands with Laravel, you may use the `commands` method. This method expects an array of command class names. Once the commands have been registered, you may execute them using the Artisan CLI:

Paketinizin Artisan komutlarını Laravel’e kaydetmek için `commands` metodunu kullanabilirsiniz. Bu metot, komut sınıfı adlarının bir dizisini kabul eder. Komutlar kaydedildikten sonra, bunlar Artisan CLI aracılığıyla çalıştırılabilir:

```php
use Courier\Console\Commands\InstallCommand;
use Courier\Console\Commands\NetworkCommand;
 
/**
 * Bootstrap any package services.
 */
public function boot(): void
{
    if ($this->app->runningInConsole()) {
        $this->commands([
            InstallCommand::class,
            NetworkCommand::class,
        ]);
    }
}
```

<br>


### Optimize Commands

Laravel's `optimize` command caches the application's configuration, events, routes, and views. Using the `optimizes` method, you may register your package's own Artisan commands that should be invoked when the `optimize` and `optimize:clear` commands are executed:

Laravel’in `optimize` komutu, uygulamanın configuration, event, route ve view’larını önbelleğe alır. `optimizes` metodunu kullanarak, `optimize` ve `optimize:clear` komutları çalıştırıldığında çağrılması gereken kendi Artisan komutlarınızı kaydedebilirsiniz:

```php
/**
 * Bootstrap any package services.
 */
public function boot(): void
{
    if ($this->app->runningInConsole()) {
        $this->optimizes(
            optimize: 'package:optimize',
            clear: 'package:clear-optimizations',
        );
    }
}
```

<br>


### Public Assets

Your package may have assets such as JavaScript, CSS, and images. To publish these assets to the application's `public` directory, use the service provider's `publishes` method. In this example, we will also add a public asset group tag, which may be used to easily publish groups of related assets:

Paketiniz JavaScript, CSS veya image gibi asset’ler içerebilir. Bu asset’leri uygulamanın `public` dizinine yayınlamak için service provider’ın `publishes` metodunu kullanabilirsiniz. Bu örnekte, ilgili asset gruplarını kolayca yayınlamak için bir “public” asset etiketi (tag) de eklenmiştir:

```php
/**
 * Bootstrap any package services.
 */
public function boot(): void
{
    $this->publishes([
        __DIR__.'/../public' => public_path('vendor/courier'),
    ], 'public');
}
```

Now, when your package's users execute the `vendor:publish` command, your assets will be copied to the specified publish location. Since users will typically need to overwrite the assets every time the package is updated, you may use the `--force` flag:

Artık kullanıcılar `vendor:publish` komutunu çalıştırdığında, asset’ler belirtilen konuma kopyalanacaktır. Kullanıcılar genellikle paketi güncellediklerinde asset’leri yeniden yazmak isteyeceğinden, `--force` bayrağını kullanabilirler:

```bash
php artisan vendor:publish --tag=public --force
```

<br>


### Publishing File Groups

You may want to publish groups of package assets and resources separately. For instance, you might want to allow your users to publish your package's configuration files without being forced to publish your package's assets. You may do this by "tagging" them when calling the `publishes` method from a package's service provider. For example, let's use tags to define two publish groups for the courier package (`courier-config` and `courier-migrations`) in the `boot` method of the package's service provider:

Paketinizdeki asset ve resource gruplarını ayrı ayrı yayınlamak isteyebilirsiniz. Örneğin, kullanıcılarınıza paketinizin configuration dosyalarını yayınlama imkânı sunarken asset’leri zorunlu kılmak istemeyebilirsiniz. Bunu, service provider’daki `publishes` metodunu çağırırken “tag” (etiket) belirterek yapabilirsiniz. Örneğin, courier paketi için iki yayın grubu (`courier-config` ve `courier-migrations`) tanımlayalım:

```php
/**
 * Bootstrap any package services.
 */
public function boot(): void
{
    $this->publishes([
        __DIR__.'/../config/package.php' => config_path('package.php')
    ], 'courier-config');
 
    $this->publishesMigrations([
        __DIR__.'/../database/migrations/' => database_path('migrations')
    ], 'courier-migrations');
}
```

Now your users may publish these groups separately by referencing their tag when executing the `vendor:publish` command:

Artık kullanıcılar, `vendor:publish` komutunu çalıştırırken bu grupları etiketlerini (tag) belirterek ayrı ayrı yayınlayabilirler:

```bash
php artisan vendor:publish --tag=courier-config
```

Your users can also publish all publishable files defined by your package's service provider using the `--provider` flag:

Kullanıcılar ayrıca paketinizin service provider’ında tanımlanmış tüm yayınlanabilir dosyaları `--provider` bayrağını kullanarak da yayınlayabilirler:

```bash
php artisan vendor:publish --provider="Your\Package\ServiceProvider"
```

<br>



Laravel is the most productive way to build, deploy, and monitor software.

Laravel, yazılım geliştirmek, dağıtmak ve izlemek için en verimli yoldur.





