<?php
require_once 'includes/db.php';

$order_id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if (!$order_id) redirect('index.php');

$stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    die("Sipariş Bulunamadı.");
}

// In real scenario, here we verify the payment with Iyzico API response
if ($status == 'success') {
    // 1. Durumu Güncelle
    $stmt = $db->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
    $stmt->execute([$order_id]);
    
    // 2. STOKTAN DÜŞ (GERÇEKÇİ SATIŞ)
    // Sipariş edilen tüm ürünleri çek
    $stmt = $db->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll();
    
    foreach($items as $item) {
        $stmt_update = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt_update->execute([$item['quantity'], $item['product_id']]);
    }
    
    // 3. Sepeti Temizle
    unset($_SESSION['cart']);
    
    $success = true;
} else {
    $success = false;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Sonucu | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .result-container {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
            padding: 60px;
            border-radius: 40px;
            background: white;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border);
        }
        .result-icon {
            font-size: 6rem;
            margin-bottom: 24px;
        }
    </style>
</head>
<body style="background: #f8fafc;">
    <div class="result-container">
        <?php if($success): ?>
            <div class="result-icon">🎉</div>
            <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -2px; margin-bottom: 12px; color: var(--secondary);">Harika! Ödeme Tamamlandı.</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6; margin-bottom: 32px;">
                Sipariş numaranız: <b style="color: var(--primary);">#<?php echo $order_id; ?></b><br>
                E-posta adresinize detaylı bir bilgilendirme gönderildi.
            </p>
            <div style="display: flex; gap: 16px; justify-content: center;">
                <a href="profile.php" class="btn btn-primary" style="padding: 16px 32px; border-radius: 12px;">📈 Siparişi Takip Et</a>
                <a href="index.php" class="btn btn-outline" style="padding: 16px 32px; border-radius: 12px;">🏠 Anasayfaya Dön</a>
            </div>
        <?php else: ?>
            <div class="result-icon">⚠️</div>
            <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -2px; margin-bottom: 12px; color: var(--danger);">Ödeme Başarısız Oldu.</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6; margin-bottom: 32px;">
                İşleminiz bankanız tarafından reddedildi veya bir hata oluştu. Lütfen tekrar denemekten çekinmeyin.
            </p>
            <div style="display: flex; gap: 16px; justify-content: center;">
                <a href="checkout.php" class="btn btn-primary" style="padding: 16px 32px; border-radius: 12px; background: var(--danger); border: none;">🔄 Tekrar Dene</a>
                <a href="index.php" class="btn btn-outline" style="padding: 16px 32px; border-radius: 12px;">🏠 Anasayfaya Dön</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
