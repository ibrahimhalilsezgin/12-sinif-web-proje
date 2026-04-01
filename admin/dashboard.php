<?php
require_once '../includes/db.php';
check_login('admin');

// Simple stats
$total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_stores = $db->query("SELECT COUNT(*) FROM stores")->fetchColumn();
$total_products = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Pending stores
$pending_stores = $db->query("SELECT s.*, u.name as vendor_name FROM stores s JOIN users u ON s.user_id = u.id WHERE s.status = 'pending'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Masası | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .a-stat-card { background: white; padding: 25px; border-radius: 20px; border: 1px solid var(--border); }
        .a-stat-val { font-size: 2rem; font-weight: 800; color: var(--secondary); display: block; }
        .a-stat-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; }
    </style>
</head>
<body style="background: #f8fafc;">
    <div class="panel-grid">
        <?php include 'sidebar.php'; ?>
        <main class="panel-main">
            <header style="margin-bottom: 48px;">
                <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">Yönetim Paneli</h1>
                <p style="color: var(--text-muted);">Sistem genelindeki tüm faaliyetleri buradan izleyin.</p>
            </header>

            <div class="grid-4" style="margin-bottom: 48px;">
                <div class="a-stat-card">
                    <span class="a-stat-label">Kullanıcılar</span>
                    <span class="a-stat-val"><?php echo $total_users; ?></span>
                </div>
                <div class="a-stat-card">
                    <span class="a-stat-label">Mağazalar</span>
                    <span class="a-stat-val"><?php echo $total_stores; ?></span>
                </div>
                <div class="a-stat-card">
                    <span class="a-stat-label">Ürünler</span>
                    <span class="a-stat-val"><?php echo $total_products; ?></span>
                </div>
                <div class="a-stat-card" style="border-color: var(--primary-light);">
                    <span class="a-stat-label">Siparişler</span>
                    <span class="a-stat-val"><?php echo $total_orders; ?></span>
                </div>
            </div>

            <section class="card" style="padding: 32px;">
                <h2 style="margin-bottom: 24px; font-size: 1.5rem; font-weight: 700;">Onay Bekleyen Mağazalar</h2>
                <table class="cart-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Mağaza Adı</th>
                            <th>Sahibi</th>
                            <th>Kayıt Tarihi</th>
                            <th style="text-align: right;">Aksiyon</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($pending_stores)): ?>
                            <tr><td colspan="4" style="text-align:center; padding:50px; color:var(--text-muted)">Onay bekleyen mağaza bulunmuyor. ✅</td></tr>
                        <?php else: ?>
                            <?php foreach($pending_stores as $store): ?>
                            <tr>
                                <td style="font-weight: 600;"><?php echo $store['store_name']; ?></td>
                                <td><?php echo $store['vendor_name']; ?></td>
                                <td style="color: var(--text-muted);"><?php echo date('d.m.Y', strtotime($store['created_at'])); ?></td>
                                <td style="text-align: right;">
                                    <form action="actions.php" method="POST" style="display:inline-flex; gap:8px">
                                        <input type="hidden" name="store_id" value="<?php echo $store['id']; ?>">
                                        <button name="action" value="approve_store" class="btn btn-primary" style="padding:8px 16px; font-size:0.8rem">Onayla</button>
                                        <button name="action" value="reject_store" class="btn btn-outline" style="padding:8px 16px; font-size:0.8rem; color:var(--danger);">Reddet</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
