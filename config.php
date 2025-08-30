<?php
// config.php

// 1. Giriş için kullanılacak kimlik bilgileri
define('SITE_USERNAME', 'Kullanıcı Adınız');
define('SITE_PASSWORD', 'Şifreniz');

// 2. "Beni Hatırla" çerezinin güvenliği için rastgele bir anahtar.
define('COOKIE_SECRET_KEY', 'a_very_long_and_random_secret_key_for_security_!@#$');

// 3. Çerezin geçerlilik süresi (saniye cinsinden). 30 gün.
define('COOKIE_LIFETIME', 30 * 24 * 60 * 60); 