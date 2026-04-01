<?php
require_once '../includes/db.php';
check_login('admin');

// Tüm mağazaları ve sahiplerini çek
$stmt = $db->query("SELECT s.*, u.name as owner_name, u.email as owner_email 
                    FROM stores s 
                    JOIN users u ON s.user_id = u.id 
                    ORDER BY s.id DESC");
$stores = $stmt->fetchAll();

// Durum etiketleri
$status_map = [
    'pending'   => ['label' => 'Onay Bekliyor', 'class' => 'badge-warning'],
    'active'    => ['label' => 'Aktif', 'class' => 'badge-success'],
    'suspended' => ['label' => 'Askıda', 'class' => 'badge-danger']
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mağazalar | Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .badge { padding: 4px 10px; border-radius: 100px; font-size: 0.8rem; font-weight: 700; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-success { background: #dcfce7; color: #16a34a; }
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
                <a href="stores.php" class="menu-link active">🏬 Mağazalar</a>
                <a href="categories.php" class="menu-link">📂 Kategoriler</a>
                <a href="users.php" class="menu-link">👥 Kullanıcılar</a>
                <a href="orders.php" class="menu-link">📦 Siparişler</a>
                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <a href="../logout.php" class="menu-link" style="color: #f87171;">Çıkış Yap</a>
                </div>
            </nav>
        </aside>

        <main class="panel-main">
            <header style="margin-bottom: 48px;">
                <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">Mağaza Yönetimi</h1>
                <p style="color: var(--text-muted);">Sisteme kayıtlı tüm satıcıları ve mağaza durumlarını kontrol edin.</p>
            </header>

            <div class="card" style="padding: 32px;">
                <table class="cart-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Mağaza Adı</th>
                            <th>Sahibi</th>
                            <th>Durum</th>
                            <th>Kayıt Tarihi</th>
                            <th style="text-align: right;">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($stores as $s): 
                            $status = $status_map[$s['status']] ?? $status_map['pending'];
                        ?>
                        <tr>
                            <td><b style="color: var(--secondary);"><?php echo $s['store_name']; ?></b></td>
                            <td>
                                <div style="font-weight: 600;"><?php echo $s['owner_name']; ?></div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo $s['owner_email']; ?></div>
                            </td>
                            <td><span class="badge <?php echo $status['class']; ?>"><?php echo $status['label']; ?></span></td>
                            <td><?php echo date('d.m.Y', strtotime($s['created_at'])); ?></td>
                            <td style="text-align: right;">
                                <form action="actions.php" method="POST" style="display: inline-flex; gap: 8px;">
                                    <input type="hidden" name="store_id" value="<?php echo $s['id']; ?>">
                                    <?php if($s['status'] != 'active'): ?>
                                        <button name="action" value="approve_store" class="btn btn-outline" style="padding: 6px 14px; font-size: 0.8rem; color: var(--success);">Aktif Yap</button>
                                    <?php else: ?>
                                        <button name="action" value="reject_store" class="btn btn-outline" style="padding: 6px 14px; font-size: 0.8rem; color: var(--danger);">Askıya Al</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
