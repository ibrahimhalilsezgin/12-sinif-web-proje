<?php
require_once '../includes/db.php';
check_login('vendor');

// Mağaza bilgisini al
$stmt = $db->prepare("SELECT * FROM stores WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$store = $stmt->fetch();

if (!$store) {
    die("Mağaza bilgisi çekilemedi.");
}

// Ayarları Güncelleme İşlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['store_name'])) {
    $name = $_POST['store_name'];
    $desc = $_POST['description'];
    $slug = slugify($name); // Slugify config.php'den geliyor

    try {
        $stmt = $db->prepare("UPDATE stores SET store_name = ?, description = ?, slug = ? WHERE id = ?");
        $stmt->execute([$name, $desc, $slug, $store['id']]);
        $success = "Mağaza bilgileriniz başarıyla güncellendi.";
        
        // Yeniden çekelim
        $stmt = $db->prepare("SELECT * FROM stores WHERE id = ?");
        $stmt->execute([$store['id']]);
        $store = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Güncelleme sırasında bir hata oluştu.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mağaza Ayarları | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background: #f8fafc;">
    <div class="panel-grid">
        <aside class="panel-sidebar">
            <div style="margin-bottom: 40px; padding: 0 20px;">
                <a href="../index.php" class="logo" style="color: white; font-size: 1.5rem;">STORE<span>.PHP</span></a>
                <p style="font-size: 0.75rem; color: #64748b; margin-top: 5px;">MAĞAZA YÖNETİMİ</p>
            </div>
            <nav>
                <a href="dashboard.php" class="menu-link">🏠 Dashboard</a>
                <a href="products.php" class="menu-link">📦 Ürünlerim</a>
                <a href="settings.php" class="menu-link active">⚙️ Ayarlar</a>
                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <a href="../logout.php" class="menu-link" style="color: #f87171;">Çıkış Yap</a>
                </div>
            </nav>
        </aside>

        <main class="panel-main">
            <header style="margin-bottom: 48px;">
                <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">Mağaza Ayarları</h1>
                <p style="color: var(--text-muted);">Müşterilerinizin göreceği profil bilgilerini buradan özelleştirin.</p>
            </header>

            <div style="max-width: 800px;">
                <?php if(isset($success)): ?>
                    <div style="background: #dcfce7; color: #16a34a; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 600;">✅ <?php echo $success; ?></div>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <div style="background: #fee2e2; color: #ef4444; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 600;">⚠️ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="card" style="padding: 48px;">
                    <div style="margin-bottom: 24px;">
                        <label>Mağaza Adı</label>
                        <input type="text" name="store_name" class="input-field" value="<?php echo $store['store_name']; ?>" placeholder="Örn: Master Butik" required>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">URL Adresiniz: <?php echo BASE_URL . 'store/' . $store['slug']; ?></p>
                    </div>

                    <div style="margin-bottom: 32px;">
                        <label>Mağaza Açıklaması</label>
                        <textarea name="description" class="input-field" rows="6" placeholder="Kısa biyografi, kargo politikası vb. detaylar..." style="resize:none"><?php echo $store['description']; ?></textarea>
                    </div>

                    <div style="border-top: 1px solid var(--border); padding-top: 32px; display: flex; gap: 16px;">
                        <button type="submit" class="btn btn-primary" style="padding: 16px 40px; border-radius: 14px;">Değişiklikleri Kaydet</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
