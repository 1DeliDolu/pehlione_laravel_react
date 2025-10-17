
<br>



## Introduction

Laravel, Symfony Process bileşeninin etrafında ifade gücü yüksek, minimal bir API sağlar ve bu sayede Laravel uygulamanızdan harici işlemleri kolayca çağırmanıza olanak tanır. Laravel'in process özellikleri, en yaygın kullanım senaryolarına ve mükemmel bir geliştirici deneyimine odaklanmıştır.

<br>


## Invoking Processes

Bir işlemi çağırmak için, `Process` facade tarafından sunulan `run` ve `start` metodlarını kullanabilirsiniz. `run` metodu bir işlemi çağırır ve tamamlanmasını beklerken, `start` metodu asenkron işlem yürütme için kullanılır. Her iki yaklaşımı da bu dokümantasyonda inceleyeceğiz. Öncelikle, basit bir senkron işlemin nasıl çağrılacağını ve sonucunun nasıl inceleneceğini görelim:

```php
use Illuminate\Support\Facades\Process;

$result = Process::run('ls -la');

return $result->output();
````

Elbette, `run` metodunun döndürdüğü `Illuminate\Contracts\Process\ProcessResult` örneği, işlem sonucunu incelemek için kullanılabilecek çeşitli yardımcı metotlar sunar:

```php
$result = Process::run('ls -la');

$result->command();
$result->successful();
$result->failed();
$result->output();
$result->errorOutput();
$result->exitCode();
```

<br>


## Throwing Exceptions

Bir işlem sonucu elde ettiyseniz ve çıkış kodu sıfırdan büyükse (yani hata oluştuysa) `Illuminate\Process\Exceptions\ProcessFailedException` örneğini fırlatmak istiyorsanız, `throw` ve `throwIf` metotlarını kullanabilirsiniz. İşlem başarısız olmadıysa, `ProcessResult` örneği döndürülür:

```php
$result = Process::run('ls -la')->throw();

$result = Process::run('ls -la')->throwIf($condition);
```

<br>


## Process Options

Elbette, bir işlemi çağırmadan önce davranışını özelleştirmeniz gerekebilir. Laravel, çalışma dizini, zaman aşımı ve ortam değişkenleri gibi çeşitli işlem özelliklerini ayarlamanıza izin verir.

<br>


### Working Directory Path

Bir işlemin çalışma dizinini belirtmek için `path` metodunu kullanabilirsiniz. Bu metot çağrılmazsa, işlem şu anda çalışan PHP betiğinin çalışma dizinini devralır:

```php
$result = Process::path(__DIR__)->run('ls -la');
```

<br>


### Input

Bir işlemin “standart girdisi” üzerinden giriş sağlamak için `input` metodunu kullanabilirsiniz:

```php
$result = Process::input('Hello World')->run('cat');
```

<br>


### Timeouts

Varsayılan olarak, işlemler 60 saniyeden uzun sürdüğünde `Illuminate\Process\Exceptions\ProcessTimedOutException` fırlatır. Ancak bu davranışı `timeout` metodu ile özelleştirebilirsiniz:

```php
$result = Process::timeout(120)->run('bash import.sh');
```

Zaman aşımını tamamen devre dışı bırakmak isterseniz, `forever` metodunu çağırabilirsiniz:

```php
$result = Process::forever()->run('bash import.sh');
```

`idleTimeout` metodu, işlem herhangi bir çıktı döndürmeden önce çalışabileceği maksimum saniye sayısını belirtmek için kullanılabilir:

```php
$result = Process::timeout(60)->idleTimeout(30)->run('bash import.sh');
```

<br>


### Environment Variables

Ortam değişkenleri, `env` metodu aracılığıyla işleme sağlanabilir. Çağrılan işlem ayrıca sisteminizde tanımlanan tüm ortam değişkenlerini devralır:

```php
$result = Process::forever()
    ->env(['IMPORT_PATH' => __DIR__])
    ->run('bash import.sh');
```

Bir ortam değişkenini miras alınan değişkenler listesinden kaldırmak isterseniz, o değişkene `false` değeri atayabilirsiniz:

```php
$result = Process::forever()
    ->env(['LOAD_PATH' => false])
    ->run('bash import.sh');
```

<br>


### TTY Mode

`tty` metodu, işleminiz için TTY modunu etkinleştirmek için kullanılabilir. TTY modu, işlemin giriş ve çıkışını programınızın giriş ve çıkışına bağlar ve işleminiz `Vim` veya `Nano` gibi bir editörü bir işlem olarak açabilmesini sağlar:

```php
Process::forever()->tty()->run('vim');
```

TTY modu Windows'ta desteklenmez.

<br>


## Process Output

Daha önce bahsedildiği gibi, işlem çıktısına (stdout) ve hata çıktısına (stderr) `output` ve `errorOutput` metotlarıyla erişilebilir:

```php
use Illuminate\Support\Facades\Process;

$result = Process::run('ls -la');

echo $result->output();
echo $result->errorOutput();
```

Ancak çıktı, `run` metoduna ikinci bir argüman olarak bir closure (kapanış) geçirilerek gerçek zamanlı olarak da toplanabilir. Bu closure iki argüman alır: çıktı türü (`stdout` veya `stderr`) ve çıktı dizgesi:

```php
$result = Process::run('ls -la', function (string $type, string $output) {
    echo $output;
});
```

Laravel ayrıca, işlem çıktısında belirli bir dizgenin bulunup bulunmadığını belirlemenin kolay bir yolunu sunan `seeInOutput` ve `seeInErrorOutput` metotlarını da sağlar:

```php
if (Process::run('ls -la')->seeInOutput('laravel')) {
    // ...
}
```

<br>


## Disabling Process Output

Eğer işleminiz ilgilenmediğiniz büyük miktarda çıktı üretiyorsa, çıktı alımını tamamen devre dışı bırakarak belleği koruyabilirsiniz. Bunu yapmak için işlemi oluştururken `quietly` metodunu çağırın:

```php
use Illuminate\Support\Facades\Process;

$result = Process::quietly()->run('bash import.sh');
```

<br>


## Pipelines

Bazen bir işlemin çıktısını başka bir işlemin girdisi yapmak isteyebilirsiniz. Bu, genellikle bir işlemin çıktısını diğerine “pipe” (borulama) olarak aktarmak olarak adlandırılır. `Process` facade tarafından sağlanan `pipe` metodu bunu kolayca gerçekleştirmenizi sağlar. `pipe` metodu, bağlı işlemleri senkron olarak yürütür ve borudaki son işlemin sonucunu döndürür:

```php
use Illuminate\Process\Pipe;
use Illuminate\Support\Facades\Process;

$result = Process::pipe(function (Pipe $pipe) {
    $pipe->command('cat example.txt');
    $pipe->command('grep -i "laravel"');
});

if ($result->successful()) {
    // ...
}
```

Eğer boru hattındaki bireysel işlemleri özelleştirmeniz gerekmiyorsa, `pipe` metoduna yalnızca komut dizelerini içeren bir dizi geçirebilirsiniz:

```php
$result = Process::pipe([
    'cat example.txt',
    'grep -i "laravel"',
]);
```

İşlem çıktısı, `pipe` metoduna ikinci argüman olarak bir closure geçirilerek gerçek zamanlı olarak toplanabilir. Bu closure iki argüman alır: çıktı türü (`stdout` veya `stderr`) ve çıktı dizgesi:

```php
$result = Process::pipe(function (Pipe $pipe) {
    $pipe->command('cat example.txt');
    $pipe->command('grep -i "laravel"');
}, function (string $type, string $output) {
    echo $output;
});
```

Laravel ayrıca, `as` metodu aracılığıyla bir boru hattındaki her işleme string anahtarlar atamanıza izin verir. Bu anahtar, `pipe` metoduna geçirilen output closure’a da aktarılır ve böylece çıktının hangi işleme ait olduğunu belirlemenizi sağlar:

```php
$result = Process::pipe(function (Pipe $pipe) {
    $pipe->as('first')->command('cat example.txt');
    $pipe->as('second')->command('grep -i "laravel"');
}, function (string $type, string $output, string $key) {
    // ...
});
```

<br>


## Asynchronous Processes

`run` metodu işlemleri senkron olarak çağırırken, `start` metodu işlemi asenkron olarak çağırmak için kullanılabilir. Bu sayede uygulamanız işlem arka planda çalışırken diğer görevleri yürütmeye devam edebilir. İşlem çağrıldıktan sonra, işlemin hâlâ çalışıp çalışmadığını belirlemek için `running` metodunu kullanabilirsiniz:

```php
$process = Process::timeout(120)->start('bash import.sh');

while ($process->running()) {
    // ...
}

$result = $process->wait();
```

Gördüğünüz gibi, işlemin bitmesini beklemek ve `ProcessResult` örneğini almak için `wait` metodunu çağırabilirsiniz:

```php
$process = Process::timeout(120)->start('bash import.sh');

// ...

$result = $process->wait();
```

<br>


## Process IDs and Signals

`id` metodu, çalışan işlemin işletim sistemi tarafından atanan işlem kimliğini almak için kullanılabilir:

```php
$process = Process::start('bash import.sh');

return $process->id();
```

Çalışan işleme bir “signal” (sinyal) göndermek için `signal` metodunu kullanabilirsiniz. Önceden tanımlanmış sinyal sabitlerinin listesi PHP dokümantasyonunda bulunabilir:

```php
$process->signal(SIGUSR2);
```

<br>


## Asynchronous Process Output

Bir asenkron işlem çalışırken, `output` ve `errorOutput` metotlarını kullanarak mevcut tüm çıktısına erişebilirsiniz; ancak, `latestOutput` ve `latestErrorOutput` metotlarını kullanarak, son çıktıyı en son alımdan bu yana gerçekleşen kısmıyla birlikte elde edebilirsiniz:

```php
$process = Process::timeout(120)->start('bash import.sh');

while ($process->running()) {
    echo $process->latestOutput();
    echo $process->latestErrorOutput();

    sleep(1);
}
````

`run` metodunda olduğu gibi, asenkron işlemlerden de gerçek zamanlı çıktı toplanabilir. Bunu yapmak için, `start` metoduna ikinci argüman olarak bir closure (kapanış) geçirirsiniz. Bu closure iki argüman alır: çıktının türü (`stdout` veya `stderr`) ve çıktı dizgesi:

```php
$process = Process::start('bash import.sh', function (string $type, string $output) {
    echo $output;
});

$result = $process->wait();
```

İşlemin tamamen bitmesini beklemek yerine, `waitUntil` metodunu kullanarak belirli bir çıktıya göre beklemeyi durdurabilirsiniz. `waitUntil` metoduna verilen closure `true` döndürdüğünde, Laravel işlemin bitmesini beklemeyi bırakacaktır:

```php
$process = Process::start('bash import.sh');

$process->waitUntil(function (string $type, string $output) {
    return $output === 'Ready...';
});
```

<br>


## Asynchronous Process Timeouts

Bir asenkron işlem çalışırken, işlemin zaman aşımına uğramadığını doğrulamak için `ensureNotTimedOut` metodunu kullanabilirsiniz. Bu metot, işlem zaman aşımına uğradıysa bir timeout exception fırlatır:

```php
$process = Process::timeout(120)->start('bash import.sh');

while ($process->running()) {
    $process->ensureNotTimedOut();

    // ...

    sleep(1);
}
```

<br>


## Concurrent Processes

Laravel, eşzamanlı (concurrent) asenkron işlem havuzlarını yönetmeyi son derece kolay hale getirir ve böylece birçok görevi aynı anda yürütmenize olanak tanır. Başlamak için, `Illuminate\Process\Pool` örneğini alan bir closure kabul eden `pool` metodunu çağırabilirsiniz.

Bu closure içinde, havuza ait işlemleri tanımlayabilirsiniz. Havuza ait işlemler `start` metodu ile başlatıldıktan sonra, çalışan işlemlerin koleksiyonuna `running` metodu aracılığıyla erişebilirsiniz:

```php
use Illuminate\Process\Pool;
use Illuminate\Support\Facades\Process;

$pool = Process::pool(function (Pool $pool) {
    $pool->path(__DIR__)->command('bash import-1.sh');
    $pool->path(__DIR__)->command('bash import-2.sh');
    $pool->path(__DIR__)->command('bash import-3.sh');
})->start(function (string $type, string $output, int $key) {
    // ...
});

while ($pool->running()->isNotEmpty()) {
    // ...
}

$results = $pool->wait();
```

Gördüğünüz gibi, `wait` metodu ile havuzdaki tüm işlemlerin tamamlanmasını bekleyebilir ve sonuçlarını çözebilirsiniz. `wait` metodu, her bir işlemin `ProcessResult` örneğine anahtarıyla erişmenizi sağlayan dizi benzeri bir nesne döndürür:

```php
$results = $pool->wait();

echo $results[0]->output();
```

Daha pratik bir kullanım için, `concurrently` metodu ile asenkron işlem havuzunu başlatabilir ve sonuçlarını hemen bekleyebilirsiniz. Bu yöntem, PHP’nin dizi ayrıştırma (array destructuring) özelliğiyle birleştiğinde oldukça ifade gücü yüksek bir sözdizimi sağlar:

```php
[$first, $second, $third] = Process::concurrently(function (Pool $pool) {
    $pool->path(__DIR__)->command('ls -la');
    $pool->path(app_path())->command('ls -la');
    $pool->path(storage_path())->command('ls -la');
});

echo $first->output();
```

<br>


## Naming Pool Processes

Süreç havuzu sonuçlarına sayısal anahtarlarla erişmek çok açıklayıcı değildir; bu nedenle Laravel, `as` metodu aracılığıyla her işleme string anahtarlar atamanıza izin verir. Bu anahtar, `start` metoduna sağlanan closure’a da iletilir ve böylece çıktının hangi işleme ait olduğunu belirleyebilirsiniz:

```php
$pool = Process::pool(function (Pool $pool) {
    $pool->as('first')->command('bash import-1.sh');
    $pool->as('second')->command('bash import-2.sh');
    $pool->as('third')->command('bash import-3.sh');
})->start(function (string $type, string $output, string $key) {
    // ...
});

$results = $pool->wait();

return $results['first']->output();
```

<br>


## Pool Process IDs and Signals

Bir işlem havuzunun `running` metodu, havuzdaki tüm çağrılmış işlemlerin koleksiyonunu sağladığından, temel işlem kimliklerine kolayca erişebilirsiniz:

```php
$processIds = $pool->running()->each->id();
```

Ayrıca, kolaylık sağlamak için `signal` metodunu çağırarak havuzdaki tüm işlemlere bir sinyal gönderebilirsiniz:

```php
$pool->signal(SIGUSR2);
```

<br>


## Testing

Birçok Laravel servisi, testleri kolay ve anlamlı bir şekilde yazmanıza yardımcı olacak işlevsellikler sunar; `process` servisi de bir istisna değildir. `Process` facade’ının `fake` metodu, işlemler çağrıldığında Laravel’in sahte (stub/dummy) sonuçlar döndürmesini sağlar.

<br>


### Faking Processes

Laravel’in işlemleri nasıl taklit edebileceğini incelemek için bir işlem çağıran bir route hayal edelim:

```php
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Route;

Route::get('/import', function () {
    Process::run('bash import.sh');

    return 'Import complete!';
});
```

Bu route’u test ederken, `Process` facade’ında `fake` metodunu argümansız çağırarak Laravel’e tüm işlemler için sahte ve başarılı bir sonuç döndürmesini söyleyebiliriz. Ayrıca belirli bir işlemin gerçekten “çalıştırıldığını” da doğrulayabiliriz:

**Pest / PHPUnit**

```php
<?php

use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;

test('process is invoked', function () {
    Process::fake();

    $response = $this->get('/import');

    // Basit işlem doğrulaması...
    Process::assertRan('bash import.sh');

    // Ya da işlem yapılandırmasını inceleyerek...
    Process::assertRan(function (PendingProcess $process, ProcessResult $result) {
        return $process->command === 'bash import.sh' &&
               $process->timeout === 60;
    });
});
```

`fake` metodunu çağırmak, Laravel’e her zaman başarılı bir işlem sonucu döndürmesini söyler. Ancak, sahte işlemler için çıktı ve çıkış kodunu `Process` facade’ının `result` metodu ile kolayca belirtebilirsiniz:

```php
Process::fake([
    '*' => Process::result(
        output: 'Test output',
        errorOutput: 'Test error output',
        exitCode: 1,
    ),
]);
```

<br>


### Faking Specific Processes

Daha önceki örnekte gördüğünüz gibi, `Process` facade, farklı işlemler için farklı sahte sonuçlar belirtmenize izin verir. Bunun için `fake` metoduna bir dizi geçirirsiniz.

Dizinin anahtarları, sahte yapmak istediğiniz komut desenlerini temsil eder ve * karakteri joker karakter olarak kullanılabilir. Sahte olmayan işlemler gerçekten çalıştırılacaktır. Bu komutlar için sahte sonuçları `Process::result` metodu ile oluşturabilirsiniz:

```php
Process::fake([
    'cat *' => Process::result(
        output: 'Test "cat" output',
    ),
    'ls *' => Process::result(
        output: 'Test "ls" output',
    ),
]);
```

Çıkış kodu veya hata çıktısını özelleştirmeniz gerekmiyorsa, sahte işlem sonuçlarını basit dizgeler olarak belirtmek daha kolaydır:

```php
Process::fake([
    'cat *' => 'Test "cat" output',
    'ls *' => 'Test "ls" output',
]);
```

<br>


### Faking Process Sequences

Test ettiğiniz kod, aynı komutla birden fazla işlem çağırıyorsa, her bir çağrı için farklı sahte sonuçlar atamak isteyebilirsiniz. Bunu `Process::sequence` metodu ile yapabilirsiniz:

```php
Process::fake([
    'ls *' => Process::sequence()
        ->push(Process::result('First invocation'))
        ->push(Process::result('Second invocation')),
]);
```

<br>


### Faking Asynchronous Process Lifecycles

Şimdiye kadar, genellikle `run` metodu ile çağrılan senkron işlemleri sahtelemeyi tartıştık. Ancak, `start` metodu ile çağrılan asenkron işlemlerle etkileşime giren kodları test ederken, daha gelişmiş bir sahteleme yaklaşımına ihtiyaç duyabilirsiniz.

Örneğin, aşağıdaki route’u hayal edin:

```php
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/import', function () {
    $process = Process::start('bash import.sh');

    while ($process->running()) {
        Log::info($process->latestOutput());
        Log::info($process->latestErrorOutput());
    }

    return 'Done';
});
```

Bu işlemi doğru bir şekilde sahtelemek için, `running` metodunun kaç kez `true` döndürmesi gerektiğini belirlememiz gerekir. Ayrıca, sıralı olarak döndürülecek birden fazla çıktı satırını da belirtebiliriz. Bunu `Process::describe` metodu ile yapabiliriz:

```php
Process::fake([
    'bash import.sh' => Process::describe()
        ->output('First line of standard output')
        ->errorOutput('First line of error output')
        ->output('Second line of standard output')
        ->exitCode(0)
        ->iterations(3),
]);
```

Yukarıdaki örneği inceleyelim. `output` ve `errorOutput` metotları ile sıralı olarak döndürülecek birden fazla çıktı satırı belirtebiliriz. `exitCode` metodu sahte işlemin nihai çıkış kodunu belirtmek için kullanılır. Son olarak, `iterations` metodu `running` metodunun kaç kez `true` döndüreceğini belirlemek için kullanılır.

<br>


## Available Assertions

Laravel, feature testlerinizde kullanılmak üzere çeşitli işlem doğrulama (assertion) metotları sağlar.

### assertRan

Belirli bir işlemin çağrıldığını doğrular:

```php
use Illuminate\Support\Facades\Process;

Process::assertRan('ls -la');
```

`assertRan` metodu, ayrıca bir closure kabul eder. Bu closure, işlem örneği ve işlem sonucunu alır. Closure `true` döndürürse, doğrulama başarılı olur:

```php
Process::assertRan(fn ($process, $result) =>
    $process->command === 'ls -la' &&
    $process->path === __DIR__ &&
    $process->timeout === 60
);
```

`$process`, `Illuminate\Process\PendingProcess` örneğidir; `$result` ise `Illuminate\Contracts\Process\ProcessResult` örneğidir.

### assertDidntRun

Belirli bir işlemin **çalıştırılmadığını** doğrular:

```php
use Illuminate\Support\Facades\Process;

Process::assertDidntRun('ls -la');
```

`tassertDidntRun` metodu da bir closure kabul eder. Bu closure, işlem ve sonucunu alır. Closure `true` döndürürse, doğrulama **başarısız** olur:

```php
Process::assertDidntRun(fn (PendingProcess $process, ProcessResult $result) =>
    $process->command === 'ls -la'
);
```

### assertRanTimes

Belirli bir işlemin belirtilen sayıda çalıştırıldığını doğrular:

```php
use Illuminate\Support\Facades\Process;

Process::assertRanTimes('ls -la', times: 3);
```

Bu metot da bir closure kabul eder ve işlem yapılandırması doğruysa doğrulama başarılı olur:

```php
Process::assertRanTimes(function (PendingProcess $process, ProcessResult $result) {
    return $process->command === 'ls -la';
}, times: 3);
```

<br>


## Preventing Stray Processes

Bireysel bir testte veya tüm test süitinde çağrılan tüm işlemlerin sahte olduğundan emin olmak isterseniz, `preventStrayProcesses` metodunu çağırabilirsiniz. Bu metod çağrıldıktan sonra, sahte sonucu olmayan herhangi bir işlem gerçek anlamda başlatılmak yerine bir exception fırlatır:

```php
use Illuminate\Support\Facades\Process;

Process::preventStrayProcesses();

Process::fake([
    'ls *' => 'Test output...',
]);

// Sahte yanıt döndürülür...
Process::run('ls -la');

// Exception fırlatılır...
Process::run('bash import.sh');
```

Laravel, yazılım oluşturmanın, dağıtmanın ve izlemenin en verimli yoludur.

