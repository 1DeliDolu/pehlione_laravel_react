# Başlangıç Kitleri

## Giriş

Yeni Laravel uygulamanızı oluştururken size bir adım önde başlama fırsatı sunmak için uygulama başlangıç kitleri sunmaktan memnuniyet duyuyoruz. Bu başlangıç kitleri, bir sonraki Laravel uygulamanızı oluştururken size avantaj sağlar ve uygulamanızın kullanıcılarını kaydetmek ve kimlik doğrulamak için gerekli olan route’ları, controller’ları ve view’ları içerir.

Bu başlangıç kitlerini kullanmakta özgürsünüz, ancak zorunlu değiller. İsterseniz sıfırdan, yalnızca Laravel’in yeni bir kopyasını yükleyerek kendi uygulamanızı oluşturabilirsiniz. Her iki durumda da, harika bir şey inşa edeceğinize eminiz!

## Bir Başlangıç Kiti Kullanarak Uygulama Oluşturma

Bir başlangıç kiti kullanarak yeni bir Laravel uygulaması oluşturmak için önce PHP ve Laravel CLI aracını yüklemelisiniz. Eğer zaten PHP ve Composer yüklüyse, Laravel yükleyici CLI aracını Composer üzerinden yükleyebilirsiniz:

```bash
composer global require laravel/installer
```

Daha sonra, Laravel yükleyici CLI kullanarak yeni bir Laravel uygulaması oluşturun. Laravel yükleyici, tercih ettiğiniz başlangıç kitini seçmeniz için sizi yönlendirecektir:

```bash
laravel new my-app
```

Laravel uygulamanızı oluşturduktan sonra, yalnızca frontend bağımlılıklarını NPM aracılığıyla yüklemeniz ve Laravel geliştirme sunucusunu başlatmanız gerekir:

```bash
cd my-app
npm install && npm run build
composer run dev
```

Laravel geliştirme sunucusunu başlattıktan sonra, uygulamanız web tarayıcınızda **[http://localhost:8000](http://localhost:8000)** adresinde erişilebilir olacaktır.

## Mevcut Başlangıç Kitleri

### React

React başlangıç kitimiz, Inertia kullanarak React frontend’li Laravel uygulamaları oluşturmak için güçlü ve modern bir başlangıç noktası sağlar.

Inertia, klasik server-side routing ve controller’ları kullanarak modern, tek sayfa React uygulamaları oluşturmanıza olanak tanır. Bu, React’in frontend gücünü, Laravel’in olağanüstü backend verimliliği ve son derece hızlı Vite derlemesi ile birleştirmenizi sağlar.

React başlangıç kiti; **React 19**, **TypeScript**, **Tailwind** ve **shadcn/ui** bileşen kütüphanesini kullanır.

### Vue

Vue başlangıç kitimiz, Inertia kullanarak Vue frontend’li Laravel uygulamaları oluşturmak için harika bir başlangıç noktası sağlar.

Inertia, klasik server-side routing ve controller’ları kullanarak modern, tek sayfa Vue uygulamaları oluşturmanıza olanak tanır. Bu, Vue’nun frontend gücünü, Laravel’in olağanüstü backend verimliliği ve son derece hızlı Vite derlemesi ile birleştirmenizi sağlar.

Vue başlangıç kiti; **Vue Composition API**, **TypeScript**, **Tailwind** ve **shadcn-vue** bileşen kütüphanesini kullanır.

### Livewire

Livewire başlangıç kitimiz, Laravel Livewire frontend’li Laravel uygulamaları oluşturmak için mükemmel bir başlangıç noktası sağlar.

Livewire, yalnızca PHP kullanarak dinamik, reaktif frontend arayüzleri oluşturmanın güçlü bir yoludur. Blade şablonlarını ağırlıklı olarak kullanan ve React veya Vue gibi JavaScript tabanlı SPA framework’lerine daha basit bir alternatif arayan ekipler için mükemmel bir seçimdir.

Livewire başlangıç kiti; **Livewire**, **Tailwind** ve **Flux UI** bileşen kütüphanesini kullanır.

# Başlangıç Kiti Özelleştirme

## React

React başlangıç kitimiz **Inertia 2**, **React 19**, **Tailwind 4** ve **shadcn/ui** ile oluşturulmuştur. Tüm başlangıç kitlerimizde olduğu gibi, backend ve frontend kodlarının tamamı uygulamanızın içinde yer alır, böylece tam özelleştirme yapabilirsiniz.

Frontend kodlarının çoğu `resources/js` dizininde bulunur. Uygulamanızın görünümünü ve davranışını özelleştirmek için bu kodların herhangi birini değiştirmekte özgürsünüz:

```
resources/js/
├── components/    # Yeniden kullanılabilir React bileşenleri
├── hooks/         # React hook'ları
├── layouts/       # Uygulama layout'ları
├── lib/           # Yardımcı fonksiyonlar ve yapılandırma
├── pages/         # Sayfa bileşenleri
└── types/         # TypeScript tanımları
```

Ek shadcn bileşenleri yayımlamak için önce yayımlamak istediğiniz bileşeni bulun. Ardından, bileşeni `npx` kullanarak yayımlayın:

```bash
npx shadcn@latest add switch
```

Bu örnekte, komut **Switch** bileşenini `resources/js/components/ui/switch.tsx` dosyasına yayımlar. Bileşen yayımlandıktan sonra, herhangi bir sayfanızda kullanabilirsiniz:

```tsx
import { Switch } from "@/components/ui/switch"
 
const MyPage = () => {
  return (
    <div>
      <Switch />
    </div>
  );
};
 
export default MyPage;
```

### Mevcut Layout’lar

React başlangıç kiti, seçebileceğiniz iki ana layout ile gelir: “**sidebar**” layout ve “**header**” layout. Varsayılan olarak sidebar layout kullanılır, ancak `resources/js/layouts/app-layout.tsx` dosyasının en üstünde içe aktarılan layout’u değiştirerek header layout’a geçebilirsiniz:

```tsx
import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout'; 
import AppLayoutTemplate from '@/layouts/app/app-header-layout'; 
```

### Sidebar Varyantları

Sidebar layout, üç farklı varyant içerir: varsayılan sidebar, “**inset**” ve “**floating**” varyantları. Tercih ettiğiniz varyantı seçmek için `resources/js/components/app-sidebar.tsx` bileşenini düzenleyebilirsiniz:

```tsx
<Sidebar collapsible="icon" variant="sidebar"> 
<Sidebar collapsible="icon" variant="inset"> 
```

### Kimlik Doğrulama Sayfası Layout Varyantları

React başlangıç kitiyle birlikte gelen kimlik doğrulama sayfaları (ör. giriş ve kayıt sayfaları), üç farklı layout varyantı sunar: “**simple**”, “**card**” ve “**split**”.

Kimlik doğrulama layout’unuzu değiştirmek için `resources/js/layouts/auth-layout.tsx` dosyasının en üstünde içe aktarılan layout’u değiştirin:

```tsx
import AuthLayoutTemplate from '@/layouts/auth/auth-simple-layout'; 
import AuthLayoutTemplate from '@/layouts/auth/auth-split-layout'; 
```

---

## Vue

Vue başlangıç kitimiz **Inertia 2**, **Vue 3 Composition API**, **Tailwind** ve **shadcn-vue** ile oluşturulmuştur. Tüm başlangıç kitlerimizde olduğu gibi, backend ve frontend kodlarının tamamı uygulamanızın içinde yer alır, böylece tam özelleştirme yapabilirsiniz.

Frontend kodlarının çoğu `resources/js` dizininde bulunur. Uygulamanızın görünümünü ve davranışını özelleştirmek için bu kodların herhangi birini değiştirmekte özgürsünüz:

```
resources/js/
├── components/    # Yeniden kullanılabilir Vue bileşenleri
├── composables/   # Vue composable'ları / hook'lar
├── layouts/       # Uygulama layout'ları
├── lib/           # Yardımcı fonksiyonlar ve yapılandırma
├── pages/         # Sayfa bileşenleri
└── types/         # TypeScript tanımları
```

Ek shadcn-vue bileşenleri yayımlamak için önce yayımlamak istediğiniz bileşeni bulun. Ardından, bileşeni `npx` kullanarak yayımlayın:

```bash
npx shadcn-vue@latest add switch
```

Bu örnekte, komut **Switch** bileşenini `resources/js/components/ui/Switch.vue` dosyasına yayımlar. Bileşen yayımlandıktan sonra, herhangi bir sayfanızda kullanabilirsiniz:

```vue
<script setup lang="ts">
import { Switch } from '@/Components/ui/switch'
</script>
 
<template>
    <div>
        <Switch />
    </div>
</template>
```

### Mevcut Layout’lar

Vue başlangıç kiti, seçebileceğiniz iki ana layout ile gelir: “**sidebar**” layout ve “**header**” layout. Varsayılan olarak sidebar layout kullanılır, ancak `resources/js/layouts/AppLayout.vue` dosyasının en üstünde içe aktarılan layout’u değiştirerek header layout’a geçebilirsiniz:

```vue
import AppLayout from '@/layouts/app/AppSidebarLayout.vue'; 
import AppLayout from '@/layouts/app/AppHeaderLayout.vue'; 
```

### Sidebar Varyantları

Sidebar layout, üç farklı varyant içerir: varsayılan sidebar, “**inset**” ve “**floating**” varyantları. Tercih ettiğiniz varyantı seçmek için `resources/js/components/AppSidebar.vue` bileşenini düzenleyebilirsiniz:

```vue
<Sidebar collapsible="icon" variant="sidebar"> 
<Sidebar collapsible="icon" variant="inset"> 
```

### Kimlik Doğrulama Sayfası Layout Varyantları

Vue başlangıç kitiyle birlikte gelen kimlik doğrulama sayfaları (ör. giriş ve kayıt sayfaları), üç farklı layout varyantı sunar: “**simple**”, “**card**” ve “**split**”.

Kimlik doğrulama layout’unuzu değiştirmek için `resources/js/layouts/AuthLayout.vue` dosyasının en üstünde içe aktarılan layout’u değiştirin:

```vue
import AuthLayout from '@/layouts/auth/AuthSimpleLayout.vue'; 
import AuthLayout from '@/layouts/auth/AuthSplitLayout.vue'; 
```
# Livewire

Livewire başlangıç kitimiz **Livewire 3**, **Tailwind** ve **Flux UI** ile oluşturulmuştur. Tüm başlangıç kitlerimizde olduğu gibi, backend ve frontend kodlarının tamamı uygulamanızın içinde yer alır, böylece tam özelleştirme yapabilirsiniz.

---

## Livewire ve Volt

Frontend kodlarının çoğu `resources/views` dizininde bulunur. Uygulamanızın görünümünü ve davranışını özelleştirmek için bu kodların herhangi birini değiştirmekte özgürsünüz:

```
resources/views
├── components            # Yeniden kullanılabilir Livewire bileşenleri
├── flux                  # Özelleştirilmiş Flux bileşenleri
├── livewire              # Livewire sayfaları
├── partials              # Yeniden kullanılabilir Blade partial’ları
├── dashboard.blade.php   # Kimliği doğrulanmış kullanıcı kontrol paneli
├── welcome.blade.php     # Ziyaretçi karşılama sayfası
```

### Geleneksel Livewire Bileşenleri

Frontend kodu `resources/views` dizininde bulunurken, **app/Livewire** dizini Livewire bileşenleri için ilgili backend mantığını içerir.

---

## Mevcut Layout’lar

Livewire başlangıç kiti, seçebileceğiniz iki ana layout ile gelir: “**sidebar**” layout ve “**header**” layout. Varsayılan olarak sidebar layout kullanılır, ancak uygulamanızın `resources/views/components/layouts/app.blade.php` dosyasında kullanılan layout’u değiştirerek header layout’a geçebilirsiniz. Ayrıca, ana Flux bileşenine **container** niteliğini eklemelisiniz:

```blade
<x-layouts.app.header>
    <flux:main container>
        {{ $slot }}
    </flux:main>
</x-layouts.app.header>
```

---

## Kimlik Doğrulama Sayfası Layout Varyantları

Livewire başlangıç kitiyle birlikte gelen kimlik doğrulama sayfaları (ör. giriş ve kayıt sayfaları), üç farklı layout varyantı sunar: **simple**, **card** ve **split**.

Kimlik doğrulama layout’unuzu değiştirmek için uygulamanızın `resources/views/components/layouts/auth.blade.php` dosyasında kullanılan layout’u değiştirin:

```blade
<x-layouts.auth.split>
    {{ $slot }}
</x-layouts.auth.split>
```

---

## İki Faktörlü Kimlik Doğrulama

Tüm başlangıç kitleri, kullanıcı hesaplarına ek güvenlik katmanı ekleyen **Laravel Fortify** tarafından desteklenen yerleşik iki faktörlü kimlik doğrulama (2FA) içerir.
Kullanıcılar, **TOTP (Time-based One-Time Password)** destekleyen herhangi bir kimlik doğrulama uygulamasını kullanarak hesaplarını koruyabilirler.

İki faktörlü kimlik doğrulama varsayılan olarak etkinleştirilmiştir ve Fortify tarafından sağlanan tüm seçenekleri destekler:

```php
Features::twoFactorAuthentication([
    'confirm' => true,
    'confirmPassword' => true,
]);
```

---

## WorkOS AuthKit Kimlik Doğrulaması

Varsayılan olarak, React, Vue ve Livewire başlangıç kitleri, Laravel’in yerleşik kimlik doğrulama sistemini kullanır ve giriş, kayıt, parola sıfırlama, e-posta doğrulama gibi işlemleri destekler.
Ayrıca, her başlangıç kitinin **WorkOS AuthKit** destekli bir varyantı da mevcuttur. Bu varyant aşağıdaki özellikleri sunar:

* Sosyal kimlik doğrulama (**Google**, **Microsoft**, **GitHub**, **Apple**)
* **Passkey** kimlik doğrulaması
* E-posta tabanlı **"Magic Auth"**
* **SSO** (Single Sign-On)

WorkOS kimlik sağlayıcısını kullanmak için bir **WorkOS hesabı** gereklidir. WorkOS, **ayda 1 milyon aktif kullanıcıya kadar ücretsiz kimlik doğrulama** sunar.

Yeni bir WorkOS destekli başlangıç kiti uygulaması oluştururken, `laravel new` komutu aracılığıyla **WorkOS seçeneğini** belirleyin.

---

## WorkOS Başlangıç Kitinizi Yapılandırma

WorkOS destekli bir başlangıç kiti kullanarak yeni bir uygulama oluşturduktan sonra, `.env` dosyanıza aşağıdaki ortam değişkenlerini eklemelisiniz. Bu değerler, WorkOS kontrol panelinde size verilen bilgilere uygun olmalıdır:

```
WORKOS_CLIENT_ID=your-client-id
WORKOS_API_KEY=your-api-key
WORKOS_REDIRECT_URL="${APP_URL}/authenticate"
```

Ayrıca, **WorkOS kontrol panelinde** uygulama ana sayfa URL’sini yapılandırmalısınız. Bu URL, kullanıcıların uygulamanızdan çıkış yaptıktan sonra yönlendirileceği sayfadır.

---

## AuthKit Kimlik Doğrulama Yöntemlerini Yapılandırma

WorkOS destekli bir başlangıç kiti kullanırken, WorkOS AuthKit yapılandırma ayarlarında **"Email + Password"** kimlik doğrulamasını devre dışı bırakmanız önerilir.
Bu, kullanıcıların yalnızca sosyal kimlik sağlayıcıları, passkey, “Magic Auth” ve SSO aracılığıyla kimlik doğrulaması yapmasını sağlar ve uygulamanızın kullanıcı parolalarını yönetme ihtiyacını ortadan kaldırır.

---

## AuthKit Oturum Zaman Aşımı Yapılandırması

Ayrıca, WorkOS AuthKit oturum **hareketsizlik zaman aşımını**, Laravel uygulamanızdaki oturum zaman aşımı eşiğiyle (genellikle **iki saat**) eşleştirmenizi öneririz.

---

## Inertia SSR

React ve Vue başlangıç kitleri, **Inertia’nın server-side rendering (SSR)** özellikleriyle uyumludur.
Uygulamanız için SSR uyumlu bir paket oluşturmak için şu komutu çalıştırın:

```bash
npm run build:ssr
```

Kolaylık olması açısından, bir **composer dev:ssr** komutu da mevcuttur. Bu komut, SSR uyumlu paketi oluşturduktan sonra Laravel geliştirme sunucusunu ve Inertia SSR sunucusunu başlatır, böylece uygulamanızı yerel olarak Inertia’nın SSR motoruyla test edebilirsiniz:

```bash
composer dev:ssr
```

---

## Topluluk Tarafından Bakımı Yapılan Başlangıç Kitleri

Laravel yükleyicisini kullanarak yeni bir Laravel uygulaması oluştururken, **Packagist** üzerindeki herhangi bir topluluk tarafından sağlanan başlangıç kitini `--using` parametresiyle belirtebilirsiniz:

```bash
laravel new my-app --using=example/starter-kit
```

---

## Başlangıç Kitleri Oluşturma

Başlangıç kitinizin diğer kullanıcılar tarafından erişilebilir olmasını istiyorsanız, onu **Packagist**’e yayınlamanız gerekir.
Başlangıç kitiniz, gerekli ortam değişkenlerini `.env.example` dosyasında tanımlamalı ve tüm gerekli kurulum sonrası komutları `composer.json` dosyanızdaki **post-create-project-cmd** dizisine eklemelidir.

---

## Sıkça Sorulan Sorular

### Nasıl yükseltirim?

Her başlangıç kiti, bir sonraki uygulamanız için sağlam bir başlangıç noktası sunar. Koda tamamen sahip olduğunuz için, uygulamanızı istediğiniz gibi değiştirebilir ve özelleştirebilirsiniz. Ancak başlangıç kitini güncellemenize gerek yoktur.

### E-posta doğrulamasını nasıl etkinleştiririm?

E-posta doğrulamasını etkinleştirmek için `App/Models/User.php` modelinizdeki **MustVerifyEmail** import’unu yorumdan çıkarın ve modelin **MustVerifyEmail** arayüzünü uyguladığından emin olun:

```php
<?php
 
namespace App\Models;
 
use Illuminate\Contracts\Auth\MustVerifyEmail;
// ...
 
class User extends Authenticatable implements MustVerifyEmail
{
    // ...
}
```

Kayıttan sonra, kullanıcılar bir doğrulama e-postası alacaklardır.
Kullanıcının e-posta adresi doğrulanana kadar belirli route’lara erişimi kısıtlamak için **verified** middleware’ini ekleyin:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});
```

WorkOS varyantı kullanılırken e-posta doğrulaması gerekli değildir.

---

### Varsayılan e-posta şablonunu nasıl değiştiririm?

Varsayılan e-posta şablonunu uygulamanızın markasına daha uygun hale getirmek isteyebilirsiniz.
Bu şablonu özelleştirmek için aşağıdaki komutu çalıştırarak e-posta görünümlerini uygulamanıza yayınlayın:

```bash
php artisan vendor:publish --tag=laravel-mail
```

Bu işlem `resources/views/vendor/mail` dizininde birkaç dosya oluşturur.
Bu dosyaları ve `resources/views/vendor/mail/themes/default.css` dosyasını düzenleyerek varsayılan e-posta şablonunun görünümünü ve tasarımını değiştirebilirsiniz.

