<?php
require_once '../includes/db.php';
check_login('vendor');

// Mağaza bilgisini al
$stmt = $db->prepare("SELECT * FROM stores WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$store = $stmt->fetch();

if (!$store) {
    die("Mağaza bulunamadı.");
}

// 1. GERÇEK İSTATİSTİKLERİ HESAPLA
// Toplam Ürün Sayısı
$stmt = $db->prepare("SELECT COUNT(*) FROM products WHERE store_id = ?");
$stmt->execute([$store['id']]);
$total_products = $stmt->fetchColumn();

// Toplam Kazanç (Sadece 'paid' durumundaki siparişler)
$stmt = $db->prepare("SELECT SUM(oi.price * oi.quantity) FROM order_items oi 
                      JOIN products p ON oi.product_id = p.id 
                      JOIN orders o ON oi.order_id = o.id
                      WHERE p.store_id = ? AND o.status = 'paid'");
$stmt->execute([$store['id']]);
$total_earnings = $stmt->fetchColumn() ?: 0;

// Toplam Sipariş Sayısı
$stmt = $db->prepare("SELECT COUNT(DISTINCT oi.order_id) FROM order_items oi 
                      JOIN products p ON oi.product_id = p.id 
                      JOIN orders o ON oi.order_id = o.id
                      WHERE p.store_id = ? AND o.status = 'paid'");
$stmt->execute([$store['id']]);
$total_orders = $stmt->fetchColumn() ?: 0;

// Son Ürünleri Çek
$stmt = $db->prepare("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.store_id = ? ORDER BY p.id DESC LIMIT 5");
$stmt->execute([$store['id']]);
$recent_products = $stmt->fetchAll();

$status_badge = [
    'pending' => ['label' => 'Onay Bekliyor', 'style' => 'background:#fef3c7; color:#d97706;'],
    'active' => ['label' => 'Mağaza Aktif', 'style' => 'background:#dcfce7; color:#16a34a;'],
    'suspended' => ['label' => 'Askıya Alındı', 'style' => 'background:#fee2e2; color:#ef4444;']
];
$current_status = $status_badge[$store['status']] ?? $status_badge['pending'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mağaza Paneli | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .v-stat-card { background: white; padding: 30px; border-radius: 20px; border: 1px solid var(--border); }
        .v-stat-val { font-size: 2.2rem; font-weight: 800; color: var(--secondary); letter-spacing: -1px; }
        .v-stat-label { font-size: 0.9rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }
    </style>
</head>
<body style="background: #f8fafc;">
    <div class="panel-grid">
        <!-- Sidebar - MERKEZİ DOSYADAN ÇEKİLİYOR -->
        <?php include 'sidebar.php'; ?>

        <main class="panel-main">
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 48px;">
                <div>
                    <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">
                        <?php echo $store['store_name']; ?>
                        <span style="font-size: 0.8rem; vertical-align: middle; padding: 6px 14px; border-radius: 100px; margin-left: 10px; <?php echo $current_status['style']; ?>">
                            <?php echo $current_status['label']; ?>
                        </span>
                    </h1>
                </div>
                <a href="product-add.php" class="btn btn-primary">+ Yeni Ürün Ekle</a>
            </header>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; margin-bottom: 48px;">
                <div class="v-stat-card">
                    <span class="v-stat-label">Envanter</span>
                    <span class="v-stat-val"><?php echo $total_products; ?></span>
                </div>
                <div class="v-stat-card">
                    <span class="v-stat-label">Toplam Kazanç</span>
                    <span class="v-stat-val"><?php echo format_money($total_earnings); ?></span>
                </div>
                <div class="v-stat-card" style="border-color: var(--primary-light);">
                    <span class="v-stat-label">Başarılı Satışlar</span>
                    <span class="v-stat-val"><?php echo $total_orders; ?></span>
                </div>
            </div>

            <div class="card" style="padding: 32px;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--secondary); margin-bottom: 24px;">Son Ürünler</h2>
                <table class="cart-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Ürün Detayı</th>
                            <th>Fiyat</th>
                            <th>Stok</th>
                            <th style="text-align: right;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_products as $p): ?>
                        <tr>
                            <td><b><?php echo $p['product_name']; ?></b></td>
                            <td><b><?php echo format_money($p['price']); ?></b></td>
                            <td><span style="font-weight: 600;"><?php echo $p['stock']; ?> Adet</span></td>
                            <td style="text-align: right;"><a href="product-edit.php?id=<?php echo $p['id']; ?>" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.8rem;">Düzenle</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
