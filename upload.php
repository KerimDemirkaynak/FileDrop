<?php
// GÜVENLİK KONTROLÜ
session_start();
require_once 'config.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Oturum açık, devam et
} else if (isset($_COOKIE['remember_me'])) {
    $expected_cookie_value = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . COOKIE_SECRET_KEY);
    if (hash_equals($expected_cookie_value, $_COOKIE['remember_me'])) {
        $_SESSION['loggedin'] = true;
    } else {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Yetkisiz erişim.']);
        exit();
    }
} else {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Yetkisiz erişim.']);
    exit();
}
// GÜVENLİK KONTROLÜ SONU

$upload_dir = 'uploads/';
$response = ['success' => false, 'error' => 'Bilinmeyen bir hata oluştu.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['file']['name']);
        $target_path = $upload_dir . $filename;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            $_SESSION['last_uploaded_file'] = $filename;
            $_SESSION['flash_message'] = htmlspecialchars($filename) . ' adlı dosya başarıyla yüklendi.';
            
            $response['success'] = true;
            unset($response['error']);
        } else {
            $response['error'] = 'Dosya sunucuya taşınırken bir hata oluştu.';
        }
    } else {
        $php_upload_errors = [
            1 => 'Yüklenen dosya sunucu limitini aşıyor.',
            2 => 'Yüklenen dosya form limitini aşıyor.',
            3 => 'Dosya kısmen yüklendi.',
            4 => 'Hiç dosya yüklenmedi.',
            6 => 'Geçici klasör bulunamadı.',
            7 => 'Dosya diske yazılamadı.',
            8 => 'Bir eklenti yüklemeyi durdurdu.',
        ];
        $error_code = $_FILES['file']['error'];
        $response['error'] = $php_upload_errors[$error_code] ?? 'Bilinmeyen bir yükleme hatası oluştu.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit();