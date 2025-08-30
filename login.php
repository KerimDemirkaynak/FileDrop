<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Hem kullanıcı adını hem de şifreyi kontrol et
    if (isset($_POST['username']) && $_POST['username'] === SITE_USERNAME &&
        isset($_POST['password']) && $_POST['password'] === SITE_PASSWORD) {
        
        $_SESSION['loggedin'] = true;

        if (isset($_POST['remember_me'])) {
            $cookie_value = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . COOKIE_SECRET_KEY);
            setcookie('remember_me', $cookie_value, time() + COOKIE_LIFETIME, "/");
        }

        header('Location: index.php');
        exit();
    } else {
        $error = 'Hatalı kullanıcı adı veya şifre!';
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Giriş Yap - FileDrop</title>
    <link rel="icon" type="image/png" href="https://paylas.ct.ws/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <style>
        :root {
            --background: #05080F; --surface: #111827; --primary: #4A80FF; --text-primary: #FFFFFF;
            --text-secondary: #A0AEC0; --border: #374151; --danger: #E53E3E; --shadow: 0 10px 30px -10px rgba(0,0,0, .4);
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--background); color: var(--text-primary); margin: 0; padding: 40px 20px; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: var(--surface); padding: 40px; border-radius: 16px; box-shadow: var(--shadow); max-width: 400px; width: 100%; text-align: center; }
        h1 { margin: 0 0 10px 0; }
        p { color: var(--text-secondary); margin-bottom: 2rem; }
        .error { color: var(--danger); background-color: rgba(229, 62, 62, 0.15); border: 1px solid var(--danger); padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .input-group { margin-bottom: 1rem; text-align: left; }
        .input-group input { box-sizing: border-box; width: 100%; background: var(--background); border: 1px solid var(--border); color: var(--text-primary); padding: 14px; border-radius: 8px; font-size: 1rem; }
        .input-group input:focus { outline: none; border-color: var(--primary); }
        .remember-group { display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 1.5rem; color: var(--text-secondary); }
        .submit-btn { background: linear-gradient(90deg, #4A80FF, #2962FF); color: white; padding: 16px; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; font-weight: 600; width: 100%; }
    </style>
</head>
<body>
    <div class="card">
        <h1><i class="fa-solid fa-lock" style="color: var(--primary);"></i></h1>
        <h1>Giriş Gerekli</h1>
        <p>Devam etmek için lütfen giriş yapın.</p>

        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="input-group">
                <input type="text" name="username" placeholder="Kullanıcı Adı" required autofocus>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Şifre" required>
            </div>
            <div class="remember-group">
                <input type="checkbox" name="remember_me" id="remember_me" checked>
                <label for="remember_me">Beni Hatırla</label>
            </div>
            <button type="submit" class="submit-btn">Giriş Yap</button>
        </form>
    </div>
</body>
</html>