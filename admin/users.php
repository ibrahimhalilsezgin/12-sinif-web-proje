<?php
require_once '../includes/db.php';
check_login('admin');

// Kullanıcı Silme
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    // Kendini silmeyi engelle
    if ($del_id != $_SESSION['user_id']) {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$del_id]);
    }
    redirect('admin/users.php');
}

// Tüm kullanıcıları çek
$stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();

// Rol etiketleri
$role_map = [
    'admin'     => ['label' => 'Yönetici', 'class' => 'badge-primary'],
    'vendor'    => ['label' => 'Satıcı', 'class' => 'badge-info'],
    'customer'  => ['label' => 'Müşteri', 'class' => 'badge-success']
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcılar | Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .badge { padding: 4px 10px; border-radius: 100px; font-size: 0.8rem; font-weight: 700; }
        .badge-primary { background: #e0e7ff; color: #4338ca; }
        .badge-info { background: #e0f2fe; color: #0284c7; }
        .badge-success { background: #dcfce7; color: #16a34a; }
    </style>
</head>
<body style="background: #f8fafc;">
    <div class="panel-grid">
        <?php include 'sidebar.php'; ?>
        <main class="panel-main">
            <header style="margin-bottom: 48px;">
                <h1 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -1.5px; color: var(--secondary);">Kullanıcı Yönetimi</h1>
                <p style="color: var(--text-muted);">Tüm kullanıcı rollerini ve üyelikleri buradan yönetin.</p>
            </header>

            <div class="card" style="padding: 32px;">
                <table class="cart-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Kullanıcı Bilgisi</th>
                            <th>E-posta</th>
                            <th>Rol</th>
                            <th>Kayıt Tarihi</th>
                            <th style="text-align: right;">Aksiyon</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): 
                            $role = $role_map[$u['user_role']] ?? $role_map['customer'];
                        ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--secondary); font-size: 1.1rem;"><?php echo $u['name']; ?></td>
                            <td><?php echo $u['email']; ?></td>
                            <td><span class="badge <?php echo $role['class']; ?>"><?php echo $role['label']; ?></span></td>
                            <td><?php echo date('d.m.Y', strtotime($u['created_at'])); ?></td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="user-edit.php?id=<?php echo $u['id']; ?>" class="btn btn-outline" style="padding: 6px 14px; font-size: 0.8rem; color: var(--primary);">Düzenle</a>
                                    <?php if($u['id'] != $_SESSION['user_id']): ?>
                                        <a href="?delete=<?php echo $u['id']; ?>" onclick="return confirm('Kullanıcıyı silmek istediğinize emin misiniz?')" class="btn btn-outline" style="padding: 6px 14px; font-size: 0.8rem; color: var(--danger);">Sil</a>
                                    <?php endif; ?>
                                </div>
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
