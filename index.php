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
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
// GÜVENLİK KONTROLÜ SONU

$upload_dir = 'uploads/';
$success_message = '';
$error_message = '';

if (isset($_SESSION['flash_message'])) {
    $success_message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
if (isset($_GET['delete'])) {
    $file_to_delete = basename($_GET['delete']);
    $file_path = $upload_dir . $file_to_delete;
    if (file_exists($file_path) && is_file($file_path)) {
        if (unlink($file_path)) {
            $success_message = htmlspecialchars($file_to_delete) . ' adlı dosya başarıyla silindi.';
        } else { $error_message = 'Dosya silinirken bir hata oluştu.'; }
    } else { $error_message = 'Silinmek istenen dosya bulunamadı.'; }
}
function get_fa_icon_class($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    switch ($extension) {
        case 'jpg': case 'jpeg': case 'png': case 'gif': case 'webp': case 'svg': case 'bmp': return 'fa-solid fa-file-image';
        case 'zip': case 'rar': case '7z': case 'tar': case 'gz': return 'fa-solid fa-file-zipper';
        case 'pdf': return 'fa-solid fa-file-pdf';
        case 'doc': case 'docx': return 'fa-solid fa-file-word';
        case 'xls': case 'xlsx': return 'fa-solid fa-file-excel';
        case 'mp3': case 'wav': return 'fa-solid fa-file-audio';
        case 'mp4': case 'mov': case 'avi': return 'fa-solid fa-file-video';
        case 'php': case 'js': case 'css': case 'html': return 'fa-solid fa-file-code';
        default: return 'fa-solid fa-file';
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>FileDrop - Dosya Yükle</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <style>
        :root {
            --background: #05080F; --surface: #111827; --surface-card: #1F2937; --primary: #4A80FF;
            --primary-hover: #6F9DFF; --text-primary: #FFFFFF; --text-secondary: #A0AEC0; --border: #374151;
            --danger: #E53E3E; --danger-hover: #FC8181; --success: #38A169; --shadow: 0 10px 30px -10px rgba(0,0,0, .4);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--background); color: var(--text-primary); margin: 0; padding: 40px 20px; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .container { max-width: 720px; width: 100%; }
        .card { background: var(--surface); padding: 40px; border-radius: 16px; box-shadow: var(--shadow); position: relative; }
        .logout-btn { position: absolute; top: 20px; right: 20px; color: var(--text-secondary); text-decoration: none; font-size: 1.5rem; transition: var(--transition); }
        .logout-btn:hover { color: var(--danger); transform: scale(1.1); }
        .header { text-align: center; }
        .header h1 { margin: 0 0 10px 0; font-size: 2.5rem; font-weight: 700; letter-spacing: -1px; }
        .header p { margin: 0 0 40px 0; color: var(--text-secondary); font-size: 1.125rem; }
        .message { display: flex; align-items: center; gap: 12px; padding: 1rem; margin-bottom: 24px; border-radius: 8px; border: 1px solid transparent; }
        .message.success { color: var(--text-primary); background-color: rgba(56, 161, 105, 0.15); border-color: var(--success); }
        .message.error { color: var(--text-primary); background-color: rgba(229, 62, 62, 0.15); border-color: var(--danger); }
        .message i { font-size: 1.25rem; }
        .upload-area { display: flex; flex-direction: column; justify-content: center; align-items: center; border: 2px dashed var(--border); border-radius: 12px; padding: 3rem; cursor: pointer; text-align: center; transition: var(--transition); }
        .upload-area:hover, .upload-area.dragover { border-color: var(--primary); background-color: var(--surface-card); transform: scale(1.02); }
        .upload-area .icon { font-size: 3.5rem; color: var(--primary); margin-bottom: 1.25rem; }
        .upload-area p { margin: 0; font-size: 1.25rem; font-weight: 600; }
        .upload-area span { margin-top: 0.5rem; color: var(--text-secondary); }
        #file-name { margin-top: 1rem; font-weight: 500; color: var(--success); }
        input[type="file"] { display: none; }
        .submit-btn { background: linear-gradient(90deg, #4A80FF, #2962FF); color: white; padding: 16px 28px; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; font-weight: 600; width: 100%; margin-top: 24px; transition: var(--transition); display: flex; justify-content: center; align-items: center; gap: 10px; box-shadow: 0 4px 15px -5px rgba(74, 128, 255, 0.4); }
        .submit-btn:hover:not(:disabled) { transform: translateY(-3px); box-shadow: 0 8px 20px -5px rgba(74, 128, 255, 0.5); }
        .submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .progress-container { display: none; width: 100%; background-color: var(--surface-card); border-radius: 8px; overflow: hidden; margin-top: 24px; }
        .progress-bar { width: 0%; height: 32px; background: linear-gradient(90deg, #2962FF, #4A80FF); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; transition: width 0.3s ease; }
        .file-list { margin-top: 40px; }
        .file-list ul { list-style: none; padding: 0; }
        .file-list li { display: flex; align-items: center; padding: 1rem; border-radius: 10px; margin-bottom: 12px; transition: var(--transition); background: var(--surface-card); border: 1px solid var(--border); }
        .file-list li:hover { border-color: var(--primary); transform: translateX(5px); }
        .file-icon { font-size: 1.5rem; color: var(--text-secondary); width: 40px; text-align: center; margin-right: 15px; }
        .file-info { flex-grow: 1; overflow: hidden; }
        .file-info a, .file-info span { text-decoration: none; color: var(--text-primary); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .file-info span.image-preview-trigger { color: var(--primary); cursor: pointer; }
        .file-info span.image-preview-trigger:hover { text-decoration: underline; }
        .file-actions { display: flex; align-items: center; }
        .action-btn { background-color: transparent; color: var(--text-secondary); border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; transition: var(--transition); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-left: 0.5rem; text-decoration: none; }
        .action-btn.download:hover { background-color: var(--primary); color: white; }
        .action-btn.delete:hover { background-color: var(--danger); color: white; }
        .lightbox-overlay { position: fixed; inset: 0; background: rgba(5, 8, 15, 0.9); backdrop-filter: blur(5px); display: none; justify-content: center; align-items: center; z-index: 1000; cursor: pointer; }
        .lightbox-content { max-width: 90vw; max-height: 90vh; position: relative; }
        .lightbox-content img { width: 100%; height: 100%; object-fit: contain; border-radius: 8px; opacity: 0; transition: opacity 0.4s ease; }
        .lightbox-close { position: absolute; top: 20px; right: 30px; color: #fff; font-size: 40px; font-weight: bold; cursor: pointer; }
        .spinner { border: 4px solid rgba(255, 255, 255, 0.2); border-left-color: var(--primary); border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; position: absolute; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <a href="logout.php" class="logout-btn" title="Güvenli Çıkış"><i class="fa-solid fa-right-from-bracket"></i></a>
        <div class="header">
            <h1>FileDrop</h1>
            <p>Dosyalarınızı güvenle yükleyin, dilediğiniz zaman yönetin.</p>
        </div>
        
        <?php if (!empty($success_message)): ?>
            <div class="message success"><i class="fa-solid fa-check-circle"></i> <?= $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="message error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form id="upload-form">
            <label for="file-input" class="upload-area" id="upload-area">
                <div class="icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                <p>Dosyaları buraya sürükleyin</p>
                <span>veya dosya seçmek için tıklayın</span>
                <div id="file-name"></div>
            </label>
            <input type="file" name="file" id="file-input" required>
            <button type="submit" class="submit-btn" id="submit-btn"><i class="fa-solid fa-upload"></i> Dosyayı Yükle</button>
            <div class="progress-container" id="progress-container">
                <div class="progress-bar" id="progress-bar">0%</div>
            </div>
        </form>
    </div>

    <?php if (is_dir($upload_dir) && count(array_diff(scandir($upload_dir), array('.', '..'))) > 0): ?>
    <div class="file-list card" id="file-list" style="margin-top: 40px;">
        <ul>
            <?php
                $files = array_diff(scandir($upload_dir), array('.', '..'));
                usort($files, function($a, $b) use ($upload_dir) {
                    return filemtime($upload_dir . $b) <=> filemtime($upload_dir . $a);
                });
                foreach ($files as $file) {
                    $file_path_link = $upload_dir . rawurlencode($file);
                    $file_path_delete = urlencode($file);
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $icon_class = get_fa_icon_class($file);
                    $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
                    $preview_exts = ['pdf', 'txt', 'html', 'css', 'js', 'md'];

                    echo '<li>';
                    echo "<div class='file-icon'><i class='{$icon_class}'></i></div>";
                    echo '<div class="file-info">';
                    if (in_array($extension, $image_exts)) {
                        echo "<span class='image-preview-trigger' data-fullimage='{$file_path_link}'>" . htmlspecialchars($file) . "</span>";
                    } else {
                        echo "<a href='{$file_path_link}' " . (in_array($extension, $preview_exts) ? "target='_blank'" : "download") . ">" . htmlspecialchars($file) . "</a>";
                    }
                    echo '</div>';
                    echo '<div class="file-actions">';
                    if (!in_array($extension, $image_exts) && !in_array($extension, $preview_exts)) {
                        echo "<a href='{$file_path_link}' class='action-btn download' download title='İndir'><i class='fa-solid fa-download'></i></a>";
                    }
                    echo "<a href='index.php?delete={$file_path_delete}' class='action-btn delete' onclick=\"return confirm('`" . htmlspecialchars($file) . "` dosyasını silmek istediğinizden emin misiniz?');\" title='Sil'><i class='fa-solid fa-trash-can'></i></a>";
                    echo '</div>';
                    echo '</li>';
                }
            ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<div class="lightbox-overlay" id="lightbox">
    <span class="lightbox-close" id="lightbox-close">&times;</span>
    <div class="lightbox-content">
        <div class="spinner" id="lightbox-spinner"></div>
        <img src="" id="lightbox-image">
    </div>
</div>

<script>
    const uploadForm = document.getElementById('upload-form');
    const fileInput = document.getElementById('file-input');
    const submitBtn = document.getElementById('submit-btn');
    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const fileNameDisplay = document.getElementById('file-name');
    const uploadArea = document.getElementById('upload-area');

    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (fileInput.files.length === 0) {
            alert('Lütfen bir dosya seçin.');
            return;
        }
        const formData = new FormData(this);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php', true);
        
        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                const percentComplete = Math.round((event.loaded / event.total) * 100);
                progressBar.style.width = percentComplete + '%';
                progressBar.textContent = percentComplete + '%';
            }
        };
        xhr.onloadstart = function() {
            submitBtn.style.display = 'none';
            progressContainer.style.display = 'block';
            submitBtn.disabled = true;
        };
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        progressBar.textContent = 'Yönlendiriliyor...';
                        window.location.href = 'last.php';
                    } else {
                        alert('Hata: ' + response.error);
                        resetUploadUI();
                    }
                } catch (e) {
                    alert('Sunucudan geçersiz bir yanıt alındı.');
                    resetUploadUI();
                }
            } else {
                alert('Sunucu hatası: ' + xhr.status);
                resetUploadUI();
            }
        };
        xhr.onerror = function() {
            alert('Ağ hatası. Bağlantınızı kontrol edin.');
            resetUploadUI();
        };
        xhr.send(formData);
    });
    
    function resetUploadUI() {
        submitBtn.style.display = 'flex';
        progressContainer.style.display = 'none';
        progressBar.style.width = '0%';
        progressBar.textContent = '0%';
        submitBtn.disabled = false;
        uploadForm.reset();
        fileNameDisplay.textContent = '';
    }

    ['dragenter','dragover','dragleave','drop'].forEach(eName=>uploadArea.addEventListener(eName,e=>{e.preventDefault();e.stopPropagation();}));
    ['dragenter','dragover'].forEach(eName=>uploadArea.addEventListener(eName,()=>uploadArea.classList.add('dragover')));
    ['dragleave','drop'].forEach(eName=>uploadArea.addEventListener(eName,()=>uploadArea.classList.remove('dragover')));
    uploadArea.addEventListener('drop',(e)=>{const files=e.dataTransfer.files;if(files.length>0){fileInput.files=files;fileNameDisplay.textContent=`Seçilen dosya: ${files[0].name}`;}});
    fileInput.addEventListener('change',()=>{if(fileInput.files.length>0){fileNameDisplay.textContent=`Seçilen dosya: ${fileInput.files[0].name}`;}});

    const lightbox = document.getElementById('lightbox');
    if(lightbox){
        const lightboxImage = document.getElementById('lightbox-image');
        const spinner = document.getElementById('lightbox-spinner');
        document.querySelector('.container')?.addEventListener('click', function(e) {
            const trigger = e.target.closest('.image-preview-trigger');
            if (trigger) {
                e.preventDefault();
                const imageUrl = trigger.getAttribute('data-fullimage');
                lightbox.style.display = 'flex';
                spinner.style.display = 'block';
                lightboxImage.style.opacity = '0';
                const tempImg = new Image();
                tempImg.src = imageUrl;
                tempImg.onload = function() {
                    lightboxImage.src = this.src;
                    spinner.style.display = 'none';
                    lightboxImage.style.opacity = '1';
                };
                tempImg.onerror = function() {
                    alert('Resim yüklenirken bir hata oluştu.');
                    closeLightbox();
                }
            }
        });
        const closeLightbox = () => { lightbox.style.display = 'none'; lightboxImage.src = ''; lightboxImage.style.opacity = '0'; };
        document.getElementById('lightbox-close').addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', (e) => { if (e.target === lightbox) { closeLightbox(); } });
    }
</script>

</body>
</html>