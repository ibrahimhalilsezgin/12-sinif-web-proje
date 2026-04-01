<?php
require_once '../includes/db.php';
check_login('vendor');

// Mağaza bilgisini al
$stmt = $db->prepare("SELECT id FROM stores WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$store_id = $stmt->fetchColumn();

// Ürün Silme İşlemi
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM products WHERE id = ? AND store_id = ?");
    $stmt->execute([$del_id, $store_id]);
    redirect('vendor/products.php');
}

// Tüm ürünleri çek
$stmt = $db->prepare("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.store_id = ? ORDER BY p.id DESC");
$stmt->execute([$store_id]);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürünlerim | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background: #f8fafc;">
    <div class="panel-grid">
        <?php include 'sidebar.php'; ?>
        <main class="panel-main">
            <header style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 48px;">
                <div>
                    <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">Tüm Ürünlerim</h1>
                    <p style="color: var(--text-muted);">Envanterinizdeki tüm ürünleri buradan yönetebilirsiniz.</p>
                </div>
                <a href="product-add.php" class="btn btn-primary" style="padding: 14px 28px; border-radius: 14px;">+ Yeni Ürün Ekle</a>
            </header>

            <div class="card" style="padding: 32px;">
                <table class="cart-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Görsel</th>
                            <th>Ürün Adı</th>
                            <th>Fiyat</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th style="text-align: right;">Aksiyonlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($products)): ?>
                            <tr><td colspan="6" style="text-align:center; padding:60px; color:var(--text-muted);">Henüz ürün eklememişsiniz. 📦</td></tr>
                        <?php else: ?>
                            <?php foreach($products as $p): ?>
                            <tr>
                                <td>
                                    <div style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 12px; overflow: hidden;">
                                        <img src="<?php echo get_product_image($p['image'], $p['product_name']); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </td>
                                <td><b style="color: var(--secondary); font-size: 1.05rem;"><?php echo $p['product_name']; ?></b></td>
                                <td><b><?php echo format_money($p['price']); ?></b></td>
                                <td>
                                    <span style="font-weight: 700; color: <?php echo $p['stock'] < 5 ? 'var(--danger)' : 'var(--success)'; ?>;">
                                        <?php echo $p['stock']; ?> Adet
                                    </span>
                                </td>
                                <td><span style="background: #f1f5f9; padding: 4px 12px; border-radius: 8px; font-size: 0.85rem;"><?php echo $p['category_name'] ?: 'Genel'; ?></span></td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <a href="product-edit.php?id=<?php echo $p['id']; ?>" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.8rem;">Düzenle</a>
                                        <a href="?delete=<?php echo $p['id']; ?>" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?')" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.8rem; color: var(--danger);">Sil</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
