<?php
require_once '../includes/db.php';
check_login('admin');

// Kategori Silme
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$del_id]);
    redirect('admin/categories.php');
}

$categories = $db->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategoriler | Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background: #f8fafc;">
    <div class="panel-grid">
        <aside class="panel-sidebar">
            <div style="margin-bottom: 40px; padding: 0 20px;">
                <a href="../index.php" class="logo" style="color: white; font-size: 1.5rem;">STORE<span>.PHP</span></a>
                <p style="font-size: 0.75rem; color: #64748b; margin-top: 5px;">ADMİNİSTRATION</p>
            </div>
            <nav>
                <a href="dashboard.php" class="menu-link">🏠 Dashboard</a>
                <a href="stores.php" class="menu-link">🏬 Mağazalar</a>
                <a href="categories.php" class="menu-link active">📂 Kategoriler</a>
                <a href="users.php" class="menu-link">👥 Kullanıcılar</a>
                <a href="orders.php" class="menu-link">📦 Siparişler</a>
                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <a href="../logout.php" class="menu-link" style="color: #f87171;">Çıkış Yap</a>
                </div>
            </nav>
        </aside>

        <main class="panel-main">
            <header style="margin-bottom: 48px;">
                <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">Kategori Yönetimi</h1>
                <p style="color: var(--text-muted);">Sisteme yeni kategoriler ekleyin ve mevcut olanları yönetin.</p>
            </header>

            <div style="display:grid; grid-template-columns: 320px 1fr; gap:40px">
                <section class="card" style="padding: 32px; height: fit-content;">
                    <h2 style="margin-bottom: 24px; font-size: 1.25rem; font-weight: 700;">Yeni Kategori Ekle</h2>
                    <form action="actions.php" method="POST">
                        <div style="margin-bottom: 20px;">
                            <label>Kategori İsmi</label>
                            <input type="text" name="category_name" class="input-field" placeholder="Örn: Elektronik" required>
                        </div>
                        <input type="hidden" name="action" value="add_category">
                        <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 12px; padding: 14px;">+ Kategori Oluştur</button>
                    </form>
                </section>

                <section class="card" style="padding: 32px;">
                    <h2 style="margin-bottom: 24px; font-size: 1.5rem; font-weight: 700;">Aktif Kategoriler</h2>
                    <table class="cart-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Kategori Adı</th>
                                <th>Slug</th>
                                <th style="text-align: right;">Aksiyonlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($categories)): ?>
                                <tr><td colspan="3" style="text-align:center; padding:50px; color:var(--text-muted)">Henüz kategori eklenmedi. 📦</td></tr>
                            <?php else: ?>
                                <?php foreach($categories as $cat): ?>
                                <tr>
                                    <td style="font-weight: 600; color: var(--secondary); font-size: 1.1rem;"><?php echo $cat['category_name']; ?></td>
                                    <td style="color: var(--text-muted); font-size: 0.9rem;"><?php echo $cat['slug']; ?></td>
                                    <td style="text-align: right;">
                                        <a href="?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.8rem; color: var(--danger);">Sil</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
