<?php
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') redirect('admin/dashboard.php');
    elseif ($_SESSION['user_role'] == 'vendor') redirect('vendor/dashboard.php');
    else redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = "Bu e-posta adresi zaten kullanılıyor.";
    } else {
        $stmt = $db->prepare("INSERT INTO users (name, email, password, user_role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);
        $user_id = $db->lastInsertId();

        if ($role == 'vendor') {
            $store_name = $_POST['store_name'] ?: $name . " Mağazası";
            $slug = slugify($store_name);
            $stmt = $db->prepare("INSERT INTO stores (user_id, store_name, slug, status) VALUES (?, ?, ?, 'active')");
            $stmt->execute([$user_id, $store_name, $slug]);
        }

        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_email'] = $email;

        if ($role == 'vendor') redirect('vendor/dashboard.php');
        else redirect('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        function toggleStoreName(role) {
            const field = document.getElementById('store-field');
            if (role === 'vendor') {
                field.style.display = 'block';
                field.style.animation = 'fadeIn 0.3s ease';
            } else {
                field.style.display = 'none';
            }
        }
    </script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); display:flex; align-items:center; justify-content:center; min-height:100vh; padding: 40px 20px;">
    <div style="background: white; padding: 48px; border-radius: 32px; box-shadow: var(--shadow-xl); width: 100%; max-width: 500px; border: 1px solid var(--border);">
        <div style="text-align: center; margin-bottom: 40px;">
            <a href="index.php" class="logo" style="justify-content: center; font-size: 2.2rem;">STORE<span>.PHP</span></a>
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-top: 12px;">Hemen hesabını oluştur ve aramıza katıl.</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div style="background: #fee2e2; color: #ef4444; padding: 14px; border-radius: 12px; margin-bottom: 24px; font-size: 0.95rem; text-align: center; font-weight: 600;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label>Adınız Soyadınız</label>
                <input type="text" name="name" class="input-field" placeholder="Ad Soyad" required>
            </div>
            <div style="margin-bottom: 20px;">
                <label>E-posta Adresi</label>
                <input type="email" name="email" class="input-field" placeholder="adiniz@example.com" required>
            </div>
            <div style="margin-bottom: 20px;">
                <label>Şifre</label>
                <input type="password" name="password" class="input-field" placeholder="••••••••" required>
            </div>
            <div style="margin-bottom: 20px;">
                <label>Hesap Türü</label>
                <select name="role" class="input-field" onchange="toggleStoreName(this.value)" style="appearance: none;">
                    <option value="customer">Alışveriş Yapacağım (Müşteri)</option>
                    <option value="vendor">Satış Yapacağım (Mağaza)</option>
                </select>
            </div>
            
            <div id="store-field" style="margin-bottom: 20px; display: none;">
                <label>Mağaza Adı</label>
                <input type="text" name="store_name" class="input-field" placeholder="Örn: Butik Store">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px; font-size: 1rem; border-radius: 14px; margin-top: 10px;">Kaydı Tamamla</button>
            
            <div style="margin-top: 32px; text-align: center; font-size: 0.95rem;">
                Zaten bir hesabın var mı? <a href="login.php" style="color: var(--primary); font-weight: 700;">Giriş Yap</a>
            </div>
        </form>
    </div>
</body>
</html>
