<?php
require_once '../includes/db.php';
check_login('admin');

// Tüm siparişleri çek (Global görünüm)
$stmt = $db->query("SELECT o.*, u.name as customer_name 
                    FROM orders o 
                    JOIN users u ON o.user_id = u.id 
                    ORDER BY o.id DESC");
$orders = $stmt->fetchAll();

// Durum etiketleri
$status_map = [
    'pending'   => ['label' => 'Beklemede', 'class' => 'badge-warning'],
    'paid'      => ['label' => 'Ödeme Yapıldı', 'class' => 'badge-success'],
    'shipped'   => ['label' => 'Kargolda', 'class' => 'badge-info'],
    'completed' => ['label' => 'Tamamlandı', 'class' => 'badge-primary'],
    'cancelled' => ['label' => 'İptal', 'class' => 'badge-danger']
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler | Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .badge { padding: 4px 10px; border-radius: 100px; font-size: 0.8rem; font-weight: 700; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-primary { background: #e0e7ff; color: #4338ca; }
        .badge-info { background: #e0f2fe; color: #0284c7; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body style="background: #f8fafc;">
    <div class="panel-grid">
        <aside class="panel-sidebar">
            <div style="margin-bottom: 40px; padding: 0 20px;">
                <a href="../index.php" class="logo" style="color: white; font-size: 1.5rem;">STORE<span>.PHP</span></a>
                <p style="font-size: 0.75rem; color: #64748b; margin-top: 5px;">ADMINISTRATION</p>
            </div>
            <nav>
                <a href="dashboard.php" class="menu-link">🏠 Dashboard</a>
                <a href="stores.php" class="menu-link">🏬 Mağazalar</a>
                <a href="categories.php" class="menu-link">📂 Kategoriler</a>
                <a href="users.php" class="menu-link">👥 Kullanıcılar</a>
                <a href="orders.php" class="menu-link active">📦 Siparişler</a>
                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <a href="../logout.php" class="menu-link" style="color: #f87171;">Çıkış Yap</a>
                </div>
            </nav>
        </aside>

        <main class="panel-main">
            <header style="margin-bottom: 48px;">
                <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">Tüm Siparişler</h1>
                <p style="color: var(--text-muted);">Platform genelindeki tüm satın alma işlemlerini buradan izleyin.</p>
            </header>

            <div class="card" style="padding: 32px;">
                <table class="cart-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Sipariş No</th>
                            <th>Müşteri</th>
                            <th>Tutar</th>
                            <th>Tarih</th>
                            <th>Durum</th>
                            <th style="text-align: right;">Aksiyon</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($orders)): ?>
                            <tr><td colspan="6" style="text-align:center; padding:60px; color:var(--text-muted)">Henüz herhangi bir sipariş alınmadı. 📦</td></tr>
                        <?php else: ?>
                            <?php foreach($orders as $o): 
                                $status = $status_map[$o['status']] ?? $status_map['pending'];
                            ?>
                            <tr>
                                <td><b style="color: var(--primary);">#<?php echo $o['id']; ?></b></td>
                                <td><b><?php echo $o['customer_name']; ?></b></td>
                                <td style="font-size: 1.1rem; font-weight: 800;"><?php echo format_money($o['total_amount']); ?></td>
                                <td style="color: var(--text-muted);"><?php echo date('d.m.Y H:i', strtotime($o['created_at'])); ?></td>
                                <td><span class="badge <?php echo $status['class']; ?>"><?php echo $status['label']; ?></span></td>
                                <td style="text-align: right;">
                                    <a href="#" class="btn btn-outline" style="padding: 6px 14px; font-size: 0.8rem;">Görüntüle</a>
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
