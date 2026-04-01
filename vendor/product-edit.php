<?php
require_once '../includes/db.php';
check_login('vendor');

$id = intval($_GET['id'] ?? 0);

// Get store id
$stmt = $db->prepare("SELECT id FROM stores WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$store_id = $stmt->fetchColumn();

// Get product details
$stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND store_id = ?");
$stmt->execute([$id, $store_id]);
$product = $stmt->fetch();

if (!$product) {
    die("Ürün bulunamadı veya yetkiniz yok.");
}

// Get categories
$categories = $db->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll();

// Update Action
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['product_name'];
    $slug = slugify($name);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'] ?: null;
    $description = $_POST['description'];
    $image = $_POST['image_url'] ?? null;

    $stmt = $db->prepare("UPDATE products SET category_id = ?, product_name = ?, slug = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ? AND store_id = ?");
    $stmt->execute([$category_id, $name, $slug, $description, $price, $stock, $image, $id, $store_id]);
    
    redirect('vendor/products.php');
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürünü Düzenle | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background: #f8fafc;">
    <div class="panel-grid">
        <?php include 'sidebar.php'; ?>
        <main class="panel-main">
            <header style="margin-bottom: 48px;">
                <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">Ürünü Düzenle</h1>
                <p style="color: var(--text-muted);"><?php echo $product['product_name']; ?> detaylarını güncelleyin.</p>
            </header>

            <form method="POST" class="card" style="padding: 48px; max-width: 900px;">
                <div class="responsive-grid" style="margin-bottom: 32px;">
                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div>
                            <label>Ürün Adı</label>
                            <input type="text" name="product_name" class="input-field" value="<?php echo $product['product_name']; ?>" required>
                        </div>
                        <div>
                            <label>Ürün Açıklaması</label>
                            <textarea name="description" class="input-field" rows="8" style="resize:none"><?php echo $product['description']; ?></textarea>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <div>
                            <label>Kategori</label>
                            <select name="category_id" class="input-field" style="appearance: none;">
                                <option value="">Kategori Seçin</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo $cat['category_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Fiyat (TL)</label>
                            <input type="number" step="0.01" name="price" class="input-field" value="<?php echo $product['price']; ?>" required>
                        </div>
                        <div>
                            <label>Stok Miktarı</label>
                            <input type="number" name="stock" class="input-field" value="<?php echo $product['stock']; ?>" required>
                        </div>
                        <div>
                            <label>Görsel Linki</label>
                            <input type="text" name="image_url" class="input-field" value="<?php echo $product['image']; ?>">
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 16px; border-top: 1px solid var(--border); padding-top: 32px;">
                    <button type="submit" class="btn btn-primary" style="padding: 16px 40px; font-size: 1rem; border-radius: 14px;">Değişiklikleri Kaydet</button>
                    <a href="products.php" class="btn btn-outline" style="padding: 16px 40px; font-size: 1rem; border-radius: 14px;">Vazgeç</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
