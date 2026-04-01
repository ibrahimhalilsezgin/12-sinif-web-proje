<?php
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') redirect('admin/dashboard.php');
    elseif ($_SESSION['user_role'] == 'vendor') redirect('vendor/dashboard.php');
    else redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['user_role'];
        $_SESSION['user_email'] = $user['email'];

        if ($user['user_role'] == 'admin') redirect('admin/dashboard.php');
        elseif ($user['user_role'] == 'vendor') redirect('vendor/dashboard.php');
        else redirect('index.php');
    } else {
        $error = "E-posta veya şifre hatalı.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); display:flex; align-items:center; justify-content:center; min-height:100vh; padding: 20px;">
    <div style="background: white; padding: 48px; border-radius: 32px; box-shadow: var(--shadow-xl); width: 100%; max-width: 440px; border: 1px solid var(--border);">
        <div style="text-align: center; margin-bottom: 40px;">
            <a href="index.php" class="logo" style="justify-content: center; font-size: 2.2rem;">STORE<span>.PHP</span></a>
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-top: 12px;">Hesabına giriş yaparak keşfetmeye başla.</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div style="background: #fee2e2; color: #ef4444; padding: 14px; border-radius: 12px; margin-bottom: 24px; font-size: 0.95rem; text-align: center; font-weight: 600;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 8px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label>E-posta Adresi</label>
                <input type="email" name="email" class="input-field" placeholder="adiniz@example.com" required>
            </div>
            <div style="margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label style="margin-bottom: 0;">Şifre</label>
                    <a href="#" style="font-size: 0.85rem; color: var(--primary); font-weight: 600;">Şifremi Unuttum</a>
                </div>
                <input type="password" name="password" class="input-field" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px; font-size: 1rem; border-radius: 14px;">Giriş Yap</button>
            <div style="margin-top: 32px; text-align: center; font-size: 0.95rem;">
                Henüz üye değil misin? <a href="register.php" style="color: var(--primary); font-weight: 700;">Hemen Kayıt Ol</a>
            </div>
        </form>
    </div>
</body>
</html>
