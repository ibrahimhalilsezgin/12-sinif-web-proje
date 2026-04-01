<?php
require_once 'includes/db.php';
check_login(); // Sadece giriş yapmış kullanıcılar görebilir

$user_id = $_SESSION['user_id'];

// Siparişleri çek (En yeni en üstte)
try {
    $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $orders = [];
}

// Durum etiketleri için stil ve yazı eşleştirme
$status_map = [
    'pending'   => ['label' => 'Ödeme Bekliyor', 'class' => 'badge-warning'],
    'paid'      => ['label' => 'Ödendi / Hazırlanıyor', 'class' => 'badge-success'],
    'shipped'   => ['label' => 'Kargoya Verildi', 'class' => 'badge-info'],
    'completed' => ['label' => 'Tamamlandı', 'class' => 'badge-primary'],
    'cancelled' => ['label' => 'İptal Edildi', 'class' => 'badge-danger']
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .badge {
            padding: 6px 12px;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-block;
        }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-info { background: #e0f2fe; color: #0284c7; }
        .badge-primary { background: #e0e7ff; color: #4338ca; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        
        .profile-header {
            background: var(--secondary);
            color: white;
            padding: 60px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body style="background: #f8fafc;">
    <header>
        <div class="container nav-wrapper">
            <a href="index.php" class="logo">STORE<span>.PHP</span></a>
            <div class="nav-links">
                <a href="index.php" class="nav-item">Mağazaya Dön</a>
                <a href="cart.php" class="btn btn-outline" style="border-radius: 100px;">🛒 Sepet</a>
                <a href="logout.php" style="color:var(--danger); font-weight: 700;">Çıkış Yap</a>
            </div>
        </div>
    </header>

    <div class="profile-header">
        <div class="container" style="display: flex; align-items: center; gap: 24px;">
            <div style="width: 80px; height: 80px; background: var(--primary); border-radius: 24px; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; color: white;">
                <?php echo mb_substr($_SESSION['user_name'], 0, 1); ?>
            </div>
            <div>
                <h1 style="margin-bottom: 4px;"><?php echo $_SESSION['user_name']; ?></h1>
                <p style="opacity: 0.7;"><?php echo $_SESSION['user_email']; ?> • Üye Profili</p>
            </div>
        </div>
    </div>

    <main class="container" style="padding: 60px 0;">
        <div style="display: grid; grid-template-columns: 280px 1fr; gap: 40px;">
            <!-- Yan Menü -->
            <aside>
                <div class="card" style="padding: 20px;">
                    <a href="profile.php" class="btn btn-primary" style="width: 100%; justify-content: flex-start; border-radius: 12px; margin-bottom: 8px;">📦 Siparişlerim</a>
                    <a href="#" class="btn btn-outline" style="width: 100%; justify-content: flex-start; border-radius: 12px; border: none; color: var(--text-muted);">👤 Hesap Ayarları</a>
                    <a href="#" class="btn btn-outline" style="width: 100%; justify-content: flex-start; border-radius: 12px; border: none; color: var(--text-muted);">📍 Adreslerim</a>
                </div>
            </aside>

            <!-- İçerik -->
            <section>
                <div class="card">
                    <h2 style="margin-bottom: 24px; letter-spacing: -1px;">Sipariş Geçmişi</h2>

                    <?php if(empty($orders)): ?>
                        <div style="text-align: center; padding: 40px;">
                            <div style="font-size: 3rem; margin-bottom: 16px;">🔍</div>
                            <h3>Henüz siparişiniz yok.</h3>
                            <p style="color: var(--text-muted); margin-bottom: 24px;">Harika ürünleri keşfetmek için mağazaya göz atın.</p>
                            <a href="index.php" class="btn btn-primary">Alışverişe Başla</a>
                        </div>
                    <?php else: ?>
                        <table class="cart-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sipariş No</th>
                                    <th>Tarih</th>
                                    <th>Toplam</th>
                                    <th>Durum</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): 
                                    $status = $status_map[$order['status']] ?? $status_map['pending'];
                                ?>
                                <tr>
                                    <td><b style="color: var(--primary);">#<?php echo $order['id']; ?></b></td>
                                    <td style="color: var(--text-muted); font-size: 0.9rem;">
                                        <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td><b><?php echo format_money($order['total_amount']); ?></b></td>
                                    <td>
                                        <span class="badge <?php echo $status['class']; ?>">
                                            <?php echo $status['label']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.8rem;">Detaylar</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <footer style="background: var(--surface); color: var(--text-main); border-top: 1px solid var(--border); padding: 40px 0;">
        <div class="container" style="text-align: center; color: var(--text-muted); font-size: 0.9rem;">
            &copy; 2026 Store PHP. Tüm Hakları Saklıdır.
        </div>
    </footer>
</body>
</html>
