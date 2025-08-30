# Basit ve Güvenli PHP Dosya Yükleme Sistemi (FileDrop)

Bu proje, saf PHP ile yazılmış, modern ve güvenli bir dosya yükleme ve yönetim uygulamasıdır. Tek kullanıcılı sistemler veya kişisel kullanım için tasarlanmıştır. Kullanıcı girişi, "Beni Hatırla" özelliği, sürükle-bırak ile dosya yükleme, ilerleme çubuğu ve yüklenen dosyaları yönetme gibi özellikler sunar.

<img width="1272" height="820" alt="image" src="https://github.com/user-attachments/assets/d1e9ea47-8d1c-4601-8a96-a87d3a88d8bc" />

## ✨ Özellikler

- **Güvenli Giriş Sistemi**: Dosya yükleme ve yönetim sayfaları kullanıcı adı ve şifre ile korunmaktadır.
- **Oturum ve Çerez Yönetimi**: "Beni Hatırla" seçeneği ile güvenli çerez (cookie) tabanlı kalıcı oturum sağlar.
- **Modern Arayüz**: Temiz, şık ve mobil uyumlu bir tasarıma sahiptir.
- **Sürükle ve Bırak**: Kullanıcıların dosyaları kolayca sürükleyip bırakarak yüklemesine olanak tanır.
- **AJAX ile Yükleme**: Sayfa yenilenmeden, arka planda dosya yükleme ve anlık ilerleme takibi.
- **Yükleme İlerleme Çubuğu**: Dosya yüklenirken kullanıcılara görsel geri bildirim sunar.
- **Dosya Yönetimi**: Yüklenen tüm dosyaları listeleyin, indirin ve güvenli bir şekilde silin.
- **Görsel Önizleme**: Yüklenen resim dosyaları için sayfa içinde açılan bir lightbox (ışık kutusu) ile önizleme imkanı.
- **Dinamik Dosya İkonları**: Dosya uzantısına göre otomatik olarak ilgili ikonu gösterir.
- **Kolay Kurulum ve Yapılandırma**: Harici bir kütüphane veya veritabanı gerektirmez. Tüm ayarlar tek bir dosyadan yönetilir.

## 🚀 Kurulum

Bu projeyi kendi sunucunuzda çalıştırmak için aşağıdaki adımları izleyin:

1.  **Dosyaları İndirin**:
    Bu repoyu klonlayın veya dosyaları ZIP olarak indirip sunucunuza yükleyin.
    ```bash
    git clone https://github.com/KerimDemirkaynak/FileDrop.git
    ```

2.  **Yazma İzinlerini Ayarlayın**:
    Web sunucunuzun `uploads` klasörüne dosya yazma izni olduğundan emin olun. Genellikle bu komut işe yarayacaktır (sunucu yapılandırmanıza göre değişiklik gösterebilir):
    ```bash
    # Debian/Ubuntu tabanlı sistemler için
    sudo chown www-data:www-data uploads
    sudo chmod 755 uploads
    ```

3.  **Yapılandırmayı Düzenleyin**:
    `config.php` dosyasını açarak kendi bilgilerinize göre düzenleyin.

## ⚙️ Yapılandırma

Tüm ayarlar `config.php` dosyası içerisinden yönetilmektedir.

```php
<?php
// config.php

// 1. Giriş için kullanılacak kimlik bilgileri
define('SITE_USERNAME', 'Kullıcı Adınız'); // Kendi kullanıcı adınızı belirleyin
define('SITE_PASSWORD', 'Şifreniz'); // Güçlü bir şifre belirleyin

// 2. "Beni Hatırla" çerezinin güvenliği için rastgele bir anahtar.
// BU DEĞERİ MUTLAKA DEĞİŞTİRİN!
define('COOKIE_SECRET_KEY', 'cok_uzun_ve_rastgele_bir_guvenlik_anahtari_!@#$');

// 3. Çerezin geçerlilik süresi (saniye cinsinden). Varsayılan: 30 gün.
define('COOKIE_LIFETIME', 30 * 24 * 60 * 60);
```

> **⚠️ Önemli Güvenlik Notu:** `COOKIE_SECRET_KEY` değerini, kimsenin tahmin edemeyeceği uzun ve rastgele bir karakter dizisiyle değiştirdiğinizden emin olun. Bu, "Beni Hatırla" özelliğinin güvenliği için kritik öneme sahiptir.

## 📁 Dosya Yapısı

-   `index.php`: Ana sayfa. Dosya yükleme arayüzünü ve yüklenmiş tüm dosyaların listesini içerir.
-   `upload.php`: AJAX ile gelen dosya yükleme isteklerini işleyen arka plan betiği.
-   `last.php`: Bir dosya yüklendikten sonra yönlendirilen ve en son yüklenen dosyayı gösteren sayfa.
-   `login.php`: Kullanıcı giriş formu ve kimlik doğrulama mantığı.
-   `logout.php`: Kullanıcı oturumunu sonlandıran ve çıkış işlemini yapan betik.
-   `config.php`: Kullanıcı adı, şifre ve güvenlik anahtarı gibi tüm yapılandırma ayarlarını içerir.
-   `uploads/`: Yüklenen tüm dosyaların saklandığı klasör.

## 📄 Lisans

Bu proje MIT Lisansı altında lisanslanmıştır. Detaylar için `LICENSE` dosyasına bakabilirsiniz.
