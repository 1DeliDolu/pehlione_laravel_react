PehliONE - Online Alışveriş Sitesi Özellikleri
=============================================

Genel açıklama:
- Bu proje bir online alışveriş sitesi olacak. Frontend React (Inertia) + Tailwind kullanacak.
- Backend Laravel API/Fortify ile oturum yönetimi ve yetkilendirme sağlayacak.

Navbar ve yönlendirme:
- En üstte bir navbar olacak. Sol tarafta marka adı: "PehliONE".
- Sağ tarafa dayalı linkler: Home, About, Connection, Products, Login, Register.
- Kayıt olan kullanıcıyı önce Login sayfasına yönlendir; login olunca Dashboard'a yönlendir.

Kullanıcı roller ve paneller:
- Roller: admin, calisan (employee), musteri (customer).
- Admin paneli olacak: ürünleri, kategorileri, kullanıcılari, mesajları yönetebilecek.
- Çalışanlar için özel panel/erişim olacak.
- Müşteri, çalışan ile doğrudan iletişim kurabilecek (mesaj/form).

Ürünler sayfası ve görünüm:
- Ürünler listelenirken solda (veya slide/side bar) kategoriler gösterilecek.
- Ürünlerin üstünde arama bölümü, filtreleme (kategori, fiyat aralığı vb.), sıralama olacak.
- Ürünler 3 kart halinde yan yana gelir (responsive). Alt alta 2 sıra olacak => sayfa başına 6 öğe (pagination).
- Kart bileşeni: görsel, başlık, kısa açıklama, fiyat, sepete ekle butonu.

İletişim ve e-postalar:
- Müşteri ile şirket içi iletişim için e-postalar aşağıdaki adrese yönlendirilebilir (ve/veya bildirim):
  - service@pehlione.com
  - kunden@pehlione.com
  - marketing@pehlione.com
  - lager@pehlione.com
  - vertrieb@pehlione.com
  - admin@pehlione.com
- Bu e-postaları `config/pehlione.php` veya `.env` içinde tanımla: örn. PEHLIONE_SERVICE_EMAIL.

Detaylı iş listesi ve dosya yerleri (önceliklendirilmiş):
1) UI: Navbar + sayfalar (Home, About, Connection, Products, Auth, Dashboard)
	- files: `resources/js/layouts/*`, `resources/js/pages/*`, `routes/web.php`
2) Auth: Laravel Fortify -> Register/Login/Logout, redirect kuralları
	- files: `config/fortify.php`, `app/Providers/FortifyServiceProvider.php`, `resources/js/pages/Auth/*`
3) Ürün modeli, kontroller ve API
	- files: `app/Models/Product.php`, `app/Http/Controllers/ProductController.php`, `routes/api.php`
4) Kategori sidebar ve filtreleme
	- files: `resources/js/components/SidebarCategories.tsx`, `resources/js/pages/Products.tsx`
5) Arama, filtre ve sıralama endpoint'leri
	- files: `app/Http/Controllers/Api/ProductSearchController.php`, `resources/js/lib/api.ts`
6) Grid görünümü ve pagination (6 öğe / sayfa)
	- files: `resources/js/components/ProductCard.tsx`, pagination helperlar
7) Admin panel + rol kontrolü
	- files: `app/Http/Middleware/AdminMiddleware.php`, `resources/js/pages/Admin/*`, `database/seeders/RoleSeeder.php`
8) İletişim sistemi: müşteri -> çalışan mesajlaşma
	- files: `app/Models/Message.php`, `app/Http/Controllers/MessageController.php`, `resources/js/pages/Contact.tsx`
9) E-posta ayarları ve mail şablonları
	- files: `config/pehlione.php`, `resources/views/emails/*`, `.env` örnek güncelleme

Notlar ve varsayımlar:
- Projede Inertia + React (TSX) ve Tailwind mevcut; frontend sayfalarını `resources/js/pages` altına ekleyeceğiz.
- Auth için Laravel Fortify kullanılıyor (repo içinde hizmet sağlayıcı bulunuyor).
- Mailer `.env` içinde `MAIL_MAILER` ayarlı; gerçek SMTP veya Mailhog ayarları gerektiğinde `.env` güncellenecek.

İlerleme ve bir sonraki adım:
- Bu dosya temel gereksinimleri içerir. Sonraki adımda isterseniz bir görev seçip ben uygulamaya başlayayım:
  - (A) Navbar ve temel sayfa iskeleti oluşturmak
  - (B) Auth yönlendirmelerini (register/login -> redirect) ayarlamak
  - (C) Ürün model + basit ürün listeleme API'si oluşturmak

