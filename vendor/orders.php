<?php
require_once '../includes/db.php';
check_login('vendor');

// Mağaza bilgisini al
$stmt = $db->prepare("SELECT id FROM stores WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$store_id = $stmt->fetchColumn();

// Siparişleri Çek (Bu mağazaya ait ürünleri içeren siparişler)
$stmt = $db->prepare("SELECT o.*, oi.quantity, oi.price as item_price, p.product_name 
                      FROM orders o 
                      JOIN order_items oi ON o.id = oi.order_id 
                      JOIN products p ON oi.product_id = p.id 
                      WHERE p.store_id = ? 
                      ORDER BY o.created_at DESC");
$stmt->execute([$store_id]);
$orders = $stmt->fetchAll();

// Durum etiketleri
$status_map = [
    'pending'   => ['label' => 'Ödeme Bekliyor', 'class' => 'badge-warning'],
    'paid'      => ['label' => 'Yeni Sipariş', 'class' => 'badge-success'],
    'shipped'   => ['label' => 'Kargolandı', 'class' => 'badge-info'],
    'completed' => ['label' => 'Tamamlandı', 'class' => 'badge-primary'],
    'cancelled' => ['label' => 'İptal', 'class' => 'badge-danger']
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .badge { padding: 4px 10px; border-radius: 100px; font-size: 0.8rem; font-weight: 700; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-info { background: #e0f2fe; color: #0284c7; }
        .badge-primary { background: #e0e7ff; color: #4338ca; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body style="background: #f8fafc;">
    <div class="panel-grid">
        <?php include 'sidebar.php'; ?>
        <main class="panel-main">
            <header style="margin-bottom: 48px;">
                <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">Gelen Siparişler</h1>
                <p style="color: var(--text-muted);">Mağazanızdan yapılan tüm satın alımları buradan yönetin.</p>
            </header>

            <div class="card" style="padding: 32px;">
                <table class="cart-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Sipariş No</th>
                            <th>Müşteri / Ürün</th>
                            <th>Tutar</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                            <th style="text-align: right;">Detay</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($orders)): ?>
                            <tr><td colspan="6" style="text-align:center; padding:60px; color:var(--text-muted);">Henüz bir sipariş almamışsınız. 💰</td></tr>
                        <?php else: ?>
                            <?php foreach($orders as $o): 
                                $status = $status_map[$o['status']] ?? $status_map['pending'];
                            ?>
                            <tr>
                                <td><b style="color: var(--primary);">#<?php echo $o['id']; ?></b></td>
                                <td>
                                    <div style="font-weight: 600; color: var(--secondary);"><?php echo $o['full_name']; ?></div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted);"><?php echo $o['product_name']; ?> (x<?php echo $o['quantity']; ?>)</div>
                                </td>
                                <td><b><?php echo format_money($o['item_price'] * $o['quantity']); ?></b></td>
                                <td style="font-size: 0.9rem; color: var(--text-muted);"><?php echo date('d.m.Y H:i', strtotime($o['created_at'])); ?></td>
                                <td><span class="badge <?php echo $status['class']; ?>"><?php echo $status['label']; ?></span></td>
                                <td style="text-align: right;">
                                    <a href="#" class="btn btn-outline" style="padding: 6px 14px; font-size: 0.8rem;">📦 Kargola</a>
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
