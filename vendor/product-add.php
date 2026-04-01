<?php
require_once '../includes/db.php';
check_login('vendor');

// Get categories
$categories = $db->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll();

// Get store id
$stmt = $db->prepare("SELECT id FROM stores WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$store_id = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['product_name'];
    $slug = slugify($name);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = $_POST['category_id'] ?: null;
    $description = $_POST['description'];
    $image = $_POST['image_url'] ?? null;

    // Validation: Stok miktarını mantıklı bir sınırda tutalım
    if ($stock > 1000000) $stock = 1000000;
    if ($stock < 0) $stock = 0;

    try {
        $stmt = $db->prepare("INSERT INTO products (store_id, category_id, product_name, slug, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$store_id, $category_id, $name, $slug, $description, $price, $stock, $image]);
        redirect('vendor/products.php');
    } catch (PDOException $e) {
        $error = "Ürün ekleme hatası: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Ürün | <?php echo APP_NAME; ?></title>
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
                <a href="products.php" class="menu-link active">📦 Ürünlerim</a>
                <a href="orders.php" class="menu-link">📃 Siparişler</a>
                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <a href="../logout.php" class="menu-link" style="color: #f87171;">Çıkış Yap</a>
                </div>
            </nav>
        </aside>

        <main class="panel-main">
            <header style="margin-bottom: 48px;">
                <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">Yeni Ürün Ekle</h1>
                <p style="color: var(--text-muted);">Envanterinize yeni bir parça ekleyin.</p>
            </header>

            <form method="POST" class="card" style="padding: 48px; max-width: 900px;">
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; margin-bottom: 32px;">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div>
                            <label>Ürün Adı</label>
                            <input type="text" name="product_name" class="input-field" placeholder="Örn: Kablosuz Kulaklık" required>
                        </div>
                        <div>
                            <label>Ürün Açıklaması</label>
                            <textarea name="description" class="input-field" rows="8" placeholder="Ürün özellikleri, detaylar..." style="resize:none"></textarea>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div>
                            <label>Kategori</label>
                            <select name="category_id" class="input-field" style="appearance: none;">
                                <option value="">Kategori Seçin</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['category_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Fiyat (TL)</label>
                            <input type="number" step="0.01" name="price" class="input-field" placeholder="0.00" required>
                        </div>
                        <div>
                            <label>Stok Miktarı</label>
                            <input type="number" name="stock" class="input-field" placeholder="0" required>
                        </div>
                        <div>
                            <label>Görsel Linki (Opsiyonel)</label>
                            <input type="text" name="image_url" class="input-field" placeholder="Ürün resmi URL'si">
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 16px; border-top: 1px solid var(--border); padding-top: 32px;">
                    <button type="submit" class="btn btn-primary" style="padding: 16px 40px; font-size: 1rem; border-radius: 14px;">Ürünü Yayınla</button>
                    <a href="dashboard.php" class="btn btn-outline" style="padding: 16px 40px; font-size: 1rem; border-radius: 14px;">Vazgeç</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
