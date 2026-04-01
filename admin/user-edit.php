<?php
require_once '../includes/db.php';
check_login('admin');

$id = intval($_GET['id'] ?? 0);

// Get user details
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("Kullanıcı bulunamadı.");
}

// Update Action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    try {
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, user_role = ? WHERE id = ?");
        $stmt->execute([$name, $email, $role, $id]);
        
        // Eğer kullanıcıyı satıcı yapıyorsak ve mağazası yoksa otomatik oluştur
        if ($role == 'vendor') {
            $checkStore = $db->prepare("SELECT id FROM stores WHERE user_id = ?");
            $checkStore->execute([$id]);
            if (!$checkStore->fetch()) {
                $store_name = $name . " Mağazası";
                $slug = slugify($store_name);
                $stmt_store = $db->prepare("INSERT INTO stores (user_id, store_name, slug, status) VALUES (?, ?, ?, 'active')");
                $stmt_store->execute([$id, $store_name, $slug]);
            }
        }
        
        redirect('admin/users.php');
    } catch (PDOException $e) {
        $error = "Güncelleme sırasında bir hata oluştu veya e-posta zaten kullanımda.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcıyı Düzenle | Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background: #f8fafc;">
    <div class="panel-grid">
        <aside class="panel-sidebar">
            <nav>
                <div style="margin-bottom: 40px; padding: 0 20px;">
                    <a href="../index.php" class="logo" style="color: white; font-size: 1.5rem;">STORE<span>.PHP</span></a>
                </div>
                <a href="dashboard.php" class="menu-link">🏠 Dashboard</a>
                <a href="users.php" class="menu-link active">👥 Kullanıcılar</a>
                <a href="../logout.php" class="menu-link" style="color: #f87171; margin-top:20px;">Çıkış Yap</a>
            </nav>
        </aside>

        <main class="panel-main">
            <header style="margin-bottom: 48px;">
                <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">Kullanıcı Düzenle</h1>
                <p style="color: var(--text-muted);"><?php echo $user['name']; ?> profilini yönetin.</p>
            </header>

            <div style="max-width: 600px;">
                <?php if(isset($error)): ?>
                    <div style="background: #fee2e2; color: #ef4444; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 600;">⚠️ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="card" style="padding: 48px;">
                    <div style="margin-bottom: 24px;">
                        <label>Ad Soyad</label>
                        <input type="text" name="name" class="input-field" value="<?php echo $user['name']; ?>" required>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label>E-posta Adresi</label>
                        <input type="email" name="email" class="input-field" value="<?php echo $user['email']; ?>" required>
                    </div>

                    <div style="margin-bottom: 32px;">
                        <label>Kullanıcı Rolü / Yetkisi</label>
                        <select name="role" class="input-field" style="appearance: none;">
                            <option value="customer" <?php echo $user['user_role'] == 'customer' ? 'selected' : ''; ?>>Müşteri</option>
                            <option value="vendor" <?php echo $user['user_role'] == 'vendor' ? 'selected' : ''; ?>>Satıcı (Mağaza Sahibi)</option>
                            <option value="admin" <?php echo $user['user_role'] == 'admin' ? 'selected' : ''; ?>>Yönetici (Admin)</option>
                        </select>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">* Satıcı rolüne geçirilen kullanıcılar için otomatik mağaza oluşturulur.</p>
                    </div>

                    <div style="border-top: 1px solid var(--border); padding-top: 32px; display: flex; gap: 16px;">
                        <button type="submit" class="btn btn-primary" style="padding: 16px 40px; border-radius: 14px;">Bilgileri Güncelle</button>
                        <a href="users.php" class="btn btn-outline" style="padding: 16px 40px; border-radius: 14px;">Vazgeç</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
